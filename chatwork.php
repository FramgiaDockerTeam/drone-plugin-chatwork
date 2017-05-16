<?php

$arguments = isset($argv[2]) ? $argv[2] : '[]';
$arguments = json_decode($arguments, true);

$vargs = $arguments['vargs'];

if (isset($vargs['room_id']) && isset($vargs['token'])) {
    $defaultFormat = "[info][title]{repo.owner}/{repo.name}#{build.commit} {build.status}[/title]Branch: {build.branch}\r\nAuthor: {build.author}\r\nMessage: {build.message}\r\n{system.link_url}/{repo.full_name}/{build.number}[/info]";

    $format = isset($vargs['format']) ? $vargs['format'] : $defaultFormat;

    $body = formatBody($format, $arguments);

    $header = [];
    $header[] = 'X-ChatWorkToken: ' . $vargs['token'];
    $params = ['body' => $body];

    $rooms = [];
    $rooms = is_array($vargs['room_id']) ? $vargs['room_id'] : [$vargs['room_id']];

    foreach ($rooms as $room) {
        echo 'Room ' . $room . ': ';

        $url = 'https://api.chatwork.com/v2/rooms/' . $room . '/messages';
        if ($result = callCurl($url, $params, $header)) {
            $res = json_decode($result, true);
            if (isset($res['errors'])) {
                echo 'ERROR: ' . implode($res['errors'], '|');
            } else {
                echo 'Sent successfully!';
            }
        } else {
            echo 'Something error!' . $result;
        }
        echo "\r\n";
    }
} else {
    echo 'room_id & token is required.';
}

function callCurl($url, $params, $header = [])
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}
function formatBody($format, $arguments)
{
    return preg_replace_callback(
        '/{(.*?)}/',
        function ($match) use ($arguments) {
            $key = explode(".", trim($match[1]));
            if (count($key) == 2) {
                return ($arguments[$key[0]] && $arguments[$key[0]][$key[1]]) ? (($key[1] == 'status') ? strtoupper($arguments[$key[0]][$key[1]]) : $arguments[$key[0]][$key[1]]) : '';
            }
            return '';
        },
        $format
    );
}

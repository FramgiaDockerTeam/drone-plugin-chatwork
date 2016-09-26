<?php

$arguments = isset($argv[2]) ? $argv[2] : '[]';
$arguments = json_decode($arguments, true);

$vargs = $arguments['vargs'];

if (isset($vargs['room_id']) && isset($vargs['token'])) {
    $url = 'https://api.chatwork.com/v1/rooms/' . $vargs['room_id'] . '/messages';

    $defaultFormat = "[info][title]{repo.owner}/{repo.name}#{build.commit} {build.status}[/title]Branch: {build.branch}\r\nAuthor: {build.author}\r\nMessage: {build.message}\r\n{system.link_url}/{repo.full_name}/{build.number}[/info]";

    $format = isset($vargs['format']) ? $vargs['format'] : $defaultFormat;

    $body = formatBody($format, $arguments);

    $header = [];
    $header[] = 'X-ChatWorkToken: ' . $vargs['token'];
    $params = ['body' => $body];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    if ($result) {
        echo 'Sent successfully!';
    } else {
        echo 'Something error!' . result;
    }
} else {
    echo 'room_id & token is required.';
}

die();

function formatBody($format, $arguments)
{
    return preg_replace_callback(
        '/{(.*?)}/',
        function ($match) use ($arguments) {
            $key = explode(".", trim($match[1]));
            if (count($key) == 2) {
                return ($arguments[$key[0]] && $arguments[$key[0]][$key[1]]) ? $arguments[$key[0]][$key[1]] : '';
            }
            return '';
        },
        $format
    );
}

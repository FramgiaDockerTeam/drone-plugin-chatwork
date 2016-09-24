FROM php:7
MAINTAINER mr.nttung@gmail.com

COPY chatwork.php /scripts/chatwork.php


ENTRYPOINT ["php", "/scripts/chatwork.php"]
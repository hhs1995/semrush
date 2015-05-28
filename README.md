Тестовое задание
================

Установка:

 git clone https://github.com/shcherbanich/semrush.git

 cd semrush

 composer install --optimize-autoloader

 конфиг бд расположен: /path/to/config/db.php

 конфиг приложения расположен: /path/to/config/params.php

 дамп бд: /path/to/config/schema.sql

Для работы требуется:
 Субд mysql, php 5.4+ , APC, Memcached ( можно легко заменить на Redis, что в будущем предпочтительнее ), beanstalkd


Класс UserAPI расположен: /path/to/app/Classes/UserAPI.php


Работа с консолью:

php console.php команда параметры

Запуск воркера на отправку сообщений в некую апишку ( можно запускать сколько угодно воркеров ):

php console.php notifications:worker:push

Запуск воркера для обработки групп сообщений

php console.php notifications:worker:combine 3

3 (сек) - это промежуток времени, за который проверять сообщения


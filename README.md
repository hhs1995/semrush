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

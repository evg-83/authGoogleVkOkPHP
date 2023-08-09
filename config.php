<?php

$host     = 'YOUR_HOST';
$dbname   = 'YOUR_DB_NAME';
$username = 'YOUR_DB_USER_NAME';
$password = 'YOUR_PASS';

try {
    // Создание нового объекта PDO для подключения к базе данных.
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

    // Установка режима обработки ошибок в режим исключений (exceptions).
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {

    // Вывод сообщения об ошибке подключения к БД и завершение скрипта.
    echo "Ошибка подключения к базе данных: " . $e->getMessage();
    exit;
}

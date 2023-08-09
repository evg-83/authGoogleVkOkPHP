<?php

session_start();

// ошибки
error_reporting(E_ALL);
ini_set('display_errors', 'On');

// Удаление всех данных сессии
session_unset();

// Уничтожение сессии
session_destroy();

// Перенаправление на страницу входа
header("Location: index.php");
exit;

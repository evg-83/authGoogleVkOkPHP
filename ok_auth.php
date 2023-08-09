<?php

session_start();

// ошибки
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once 'curlHelper.php'; // Подключение файла с функциями для работы с cURL
require_once 'config.php';     // Подключение к БД

// Проверяем, был ли отправлен GET-запрос с параметром 'code' (от OK OAuth)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['code'])) {

    $code         = $_GET['code'];
    $clientId     = 'YOUR_CLIENT_ID';
    $clientSecret = 'YOUR_CLIENT_SECRET';
    $redirectUri  = 'YOUR_REDIRECT_URI';

    // Получение access token через oauth API Одноклассники
    $tokenUrl  = 'https://api.ok.ru/oauth/token.do';
    $tokenData = array(
        'client_id'     => $clientId,
        'client_secret' => $clientSecret,
        'redirect_uri'  => $redirectUri,
        'grant_type'    => 'authorization_code',
        'code'          => $code,
    );

    // Отправка POST-запроса для получения токена
    $tokenResponse = CurlHelper::request($tokenUrl, [], $tokenData);
    $tokenData     = json_decode($tokenResponse, true);

    // Проверяем успешность получения токена
    if (isset($tokenData['access_token'])) {
        $accessToken = $tokenData['access_token'];

        // Получить пользовательские данные с помощью токена доступа
        $apiUrl         = 'https://api.ok.ru/fb.do?method=users.getCurrentUser';
        $apiResponse    = CurlHelper::request($apiUrl, ['Authorization: Bearer ' . $accessToken]);
        $okUserResponse = json_decode($apiResponse, true);

        $okUser = null;

        if (isset($okUserResponse['response'][0])) {
            $okUser = $okUserResponse['response'][0];
        } else {
            echo "Не удалось получить данные пользователя из OK API.";
        }
    } else {
        echo "Не удалось получить токен доступа от OK API.";
    }

    //Сохранение данных в БД
    try {
        $stmt = $db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$okUser['first_name'], '', '']);

        // Установка сессионной переменной для обозначения авторизации
        $_SESSION['user_id']  = $db->lastInsertId();
        $_SESSION['username'] = $okUser['first_name'];

        // Добавляем редирект на домашнюю страницу
        header("Location: home.php");
        exit;
    } catch (PDOException $e) {
        echo 'Ошибка регистрации через OK: ' . $e->getMessage();
    }
}

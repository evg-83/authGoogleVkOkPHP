<?php

session_start();

// ошибки
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once 'curlHelper.php'; // Подключение файла с функциями для работы с cURL
require_once 'config.php';     // Подключение к БД

// Проверяем, был ли отправлен GET-запрос с параметром 'code' (от VK OAuth)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['code'])) {

    $code         = $_GET['code'];
    $clientId     = 'YOUR_CLIENT_ID';
    $clientSecret = 'YOUR_CLIENT_SECRET';
    $redirectUri  = 'YOUR_REDIRECT_URI';

    // Получение access token через VK API
    $tokenUrl  = 'https://oauth.vk.com/access_token';
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

        // Создаю URL-адрес API
        $apiUrl = "https://api.vk.com/method/users.get?user_ids={$tokenData['user_id']}&fields=first_name,last_name&access_token={$accessToken}&v=5.131";

        // Делаю запрос API
        $apiResponse    = file_get_contents($apiUrl);
        $vkUserResponse = json_decode($apiResponse, true);

        if (isset($vkUserResponse['response'][0])) {
            $vkUser = $vkUserResponse['response'][0];
        } else {
            echo "Не удалось получить данные пользователя из VK API.";
        }
    } else {
        echo "Не удалось получить токен доступа из VK API.";
    }

    //Сохранение данных в БД
    try {
        $stmt = $db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$vkUser['first_name'], '', '']);

        // Установка сессионной переменной для обозначения авторизации
        $_SESSION['user_id']  = $db->lastInsertId();
        $_SESSION['username'] = $vkUser['first_name'];

        // Добавляем редирект на домашнюю страницу
        header("Location: home.php");
        exit;
    } catch (PDOException $e) {
        echo 'Ошибка регистрации через VK: ' . $e->getMessage();
    }
}

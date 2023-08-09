<?php

session_start();

// ошибки и предупреждения
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once 'curlHelper.php'; // Вспомогательный класс для работы с cURL
require_once 'config.php';     // Подключение к БД

// Проверяем, был ли отправлен GET-запрос с параметром 'code' (от Google OAuth)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['code'])) {

    // Получаем код авторизации от Google
    $code         = $_GET['code'];
    $clientId     = 'YOUR_CLIENT_ID';
    $clientSecret = 'YOUR_CLIENT_SECRET';
    $redirectUri  = 'YOUR_REDIRECT_URI';

    // Получение токена доступа через Google API
    $tokenUrl  = 'https://www.googleapis.com/oauth2/v4/token';
    $tokenData = array(
        'client_id'     => $clientId,
        'client_secret' => $clientSecret,
        'redirect_uri'  => $redirectUri,
        'grant_type'    => 'authorization_code',
        'code'          => $code,
    );

    // Выполняем запрос к Google для получения токена
    $tokenResponse = CurlHelper::request($tokenUrl, [], $tokenData);
    $tokenData     = json_decode($tokenResponse, true);

    // Проверяем успешность получения токена
    if (isset($tokenData['access_token'])) {
        $accessToken = $tokenData['access_token'];

        // Создаем URL для запроса данных пользователя
        $apiUrl = "https://www.googleapis.com/oauth2/v2/userinfo?access_token={$accessToken}";

        // Выполняем запрос к API Google для получения данных пользователя
        $apiResponse        = file_get_contents($apiUrl);
        $googleUserResponse = json_decode($apiResponse, true);

        // Проверяем успешность получения данных пользователя
        if (isset($googleUserResponse['response'][0])) {
            $googleUser = $googleUserResponse['response'][0];
        } else {
            echo "Не удалось получить данные пользователя из Google API.";
        }
    } else {
        echo "Не удалось получить токен доступа из Google API.";
    }

    // Сохранение данных пользователя в БД
    try {
        $stmt = $db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$googleUser['name'], '', '']);

        // Установка сессионной переменной для обозначения авторизации
        $_SESSION['user_id']  = $db->lastInsertId();
        $_SESSION['username'] = $googleUser['first_name'];

        // Добавляем редирект на домашнюю страницу
        header("Location: home.php");
        exit;
    } catch (PDOException $e) {
        echo 'Ошибка регистрации через Google: ' . $e->getMessage();
    }
}

<?php

// ошибки и предупреждения
error_reporting(E_ALL);
ini_set('display_errors', 'On');

/**
 * Класс CurlHelper предоставляет методы для выполнения HTTP-запросов с использованием cURL.
 */
class CurlHelper
{
    /**
     * Выполняет HTTP-запрос с помощью cURL.
     * @param string $url URL для запроса.
     * @param array $headers Массив с заголовками запроса.
     * @param array|null $postFields Данные для POST-запроса (если необходимо).
     * @return string Ответ сервера на запрос.
     */
    public static function request($url, $headers = array(), $postFields = null)
    {
        $ch = curl_init($url);

        if (!is_null($postFields)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'cURL-ошибка: ' . curl_error($ch);
        }
        
        curl_close($ch);

        return $response;
    }
}

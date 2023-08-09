<?php

session_start();

// ошибки
error_reporting(E_ALL);
ini_set('display_errors', 'On');

/** VK data */
define('ID_VK', 'YOUR_CLIENT_ID');
define('URL_VK', 'YOUR_REDIRECT_URI');

/** OK data */
define('ID_OK', 'YOUR_CLIENT_ID');
define('URL_OK', 'YOUR_REDIRECT_URI');

/** Google data */
define('ID_GOO', 'YOUR_CLIENT_ID');
define('URL_GOO', 'YOUR_REDIRECT_URI');

require_once 'config.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Валидация имени пользователя
    if (empty($username)) {
        $errors[] = 'Введите имя пользователя.';
    }

    // Валидация пароля
    if (empty($password)) {
        $errors[] = 'Введите пароль.';
    }

    if (empty($errors)) {
        try {
            $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && $user['password'] === md5($password)) {
                // Успешная аутентификация, установка сессии
                $_SESSION['user_id']  = $user['id'];
                $_SESSION['username'] = $user['username'];
                
                // После успешного входа перенаправляем на домашнюю страницу
                header("Location: home.php");
                exit;
            } else {
                $errors[] = 'Неверные имя пользователя или пароль.';
            }
        } catch (PDOException $e) {
            echo 'Ошибка входа: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
</head>

<body class="text-center">
    <main class="form-signin w-25 m-auto mt-5">
        <form method="post">

            <?php if (!empty($errors)) : ?>
                <div class="alert alert-danger">
                    <?= implode('<br>', $errors) ?>
                </div>
            <?php endif; ?>

            <h1 class="h3 mb-3 fw-normal">Авторизация</h1>

            <div class="form-floating">
                <input name="username" type="text" class="form-control" id="floatingInput" placeholder="Имя пользователя" />
                <label for="floatingInput">Имя пользователя</label>
            </div>
            <div class="form-floating">
                <input name="password" type="password" class="form-control" id="floatingPassword" placeholder="Пароль" required />
                <label for="floatingPassword">Пароль</label>
            </div>

            <button class="w-100 btn btn-lg btn-outline-primary" type="submit">
                Войти
            </button>

            <div class="mt-3">
                <a href="https://oauth.vk.com/authorize?client_id=<?= ID_VK ?>&redirect_uri=<?= URL_VK ?>&display=page"><img src="https://ok.ru/res/i/p/socials/vk_id_32.svg"></img></a>
                <a href="https://connect.ok.ru/oauth/authorize?client_id=<?= ID_OK ?>&redirect_uri=<?= URL_OK ?>&response_type=code&scope=email%20profile&display=page"><img width="20%" src="https://ok.ru/res/i/p/toolbar/redesign2023_logo@2x.png"></img></a>
                <a href="https://accounts.google.com/o/oauth2/auth?client_id=<?= ID_GOO ?>&redirect_uri=<?= URL_GOO ?>&response_type=code&scope=email%20profile&display=page"><img src="https://ok.ru/res/i/p/socials/google_32.svg"></img></a><br>

                <a href="register.php">Регистрация</a>
            </div>

        </form>
    </main>
</body>

</html>
<?php

// ошибки
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once 'config.php'; // Подключение к БД

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    // Валидация имени пользователя
    if (empty($username)) {
        $errors[] = 'Введите имя пользователя.';
    }

    // Валидация email
    if (empty($email)) {
        $errors[] = 'Введите email.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Некорректный формат email.';
    }

    // Валидация пароля
    if (empty($password)) {
        $errors[] = 'Введите пароль.';
    }

    if (empty($errors)) {
        try {
            // Хеширование пароля перед сохранением в базе данных
            $hashedPassword = md5($password);

            $stmt = $db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$username, $email, $hashedPassword]);
            
            // После успешной регистрации перенаправляем на страницу входа
            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            echo 'Ошибка регистрации: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>

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

            <h1 class="h3 mb-3 fw-normal">Регистрация</h1>

            <div class="form-floating">
                <input name="username" type="text" class="form-control" id="floatingInput" placeholder="Имя пользователя" required />
                <label for="floatingInput">Имя пользователя</label>
            </div>
            <div class="form-floating">
                <input name="email" type="email" class="form-control" id="floatingInput" placeholder="name@example.com" required />
                <label for="floatingInput">Email address</label>
            </div>
            <div class="form-floating">
                <input name="password" type="password" class="form-control" id="floatingPassword" placeholder="Пароль" required />
                <label for="floatingPassword">Пароль</label>
            </div>

            <button class="w-100 btn btn-lg btn-outline-primary" type="submit">
                Зарегистрироваться
            </button>

            <div class="mt-3">
                <a href="index.php">Авторизация</a>
            </div>
        </form>
    </main>
</body>

</html>
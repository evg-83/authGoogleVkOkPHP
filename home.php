<?php

session_start(); // Начало сессии

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php"); // Перенаправление на страницу входа
    exit;
}

// Получение имени пользователя из базы данных или сессии
$username = $_SESSION['username'] ?? 'Гость';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Домашняя страница</title>
    <meta http-equiv="refresh" content="10;url=https://gesbes.com/">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
</head>

<body class="d-flex h-100 text-center text-bg-dark">
    <div class="cover-container d-flex w-100 h-100 p-3 mx-5 mt-3 flex-column">
        <header class="mb-5">
            <div>
                <nav class="nav nav-masthead justify-content-center float-md-end">
                    <a class="nav-link fw-bold py-1 px-0" href="logout.php">Выход</a>
                </nav>
            </div>
        </header>

        <main class="px-3">
            <h2>Добро пожаловать <?php echo ucfirst($username); ?>, вы успешно авторизовались!</h2>
        </main>
    </div>
</body>

</html>
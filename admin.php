<?php
// Устанавливаем уровень сообщений об ошибках
error_reporting(E_ALL); // Показывать все ошибки для записи в лог

// Отключаем отображение ошибок в браузере
ini_set('display_errors', 0);

// Включаем запись ошибок в лог
ini_set('log_errors', 1);

// Устанавливаем путь к файлу лога ошибок
ini_set('error_log', '/path/to/php-error.log'); // Замените на реальный путь

require_once 'App/Domain/Users/UserEntity.php'; use App\Domain\Users\UserEntity;

$user = new UserEntity();
if (!$user->isAdmin) die('Доступ закрыт');
?>
<html>
<head>
</head>
<body>
<h1>Админка</h1>

</body>
</html>
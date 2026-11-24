<?php
require_once __DIR__ . '/config.php';

// Logout só por POST com token CSRF (um link de outro site não consegue deslogar ninguém).
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_valido()) {
    header('Location: ' . (usuario_logado() ? 'admin.php' : 'index.php'));
    exit;
}

$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}
session_destroy();

header('Location: index.php');
exit;

<?php
// Cria o primeiro administrador pela linha de comando (não funciona pelo navegador).
// Uso: php criar_admin.php <usuario>
// A senha é pedida no terminal, sem aparecer na tela nem no histórico.

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/config.php';

$usuario = trim($argv[1] ?? '');
if ($usuario === '' || mb_strlen($usuario) > 50) {
    fwrite(STDERR, "Uso: php criar_admin.php <usuario>  (até 50 caracteres)\n");
    exit(1);
}

function ler_senha(string $rotulo): string
{
    fwrite(STDOUT, $rotulo);
    // Em Linux/macOS esconde a digitação; no Windows o terminal mostra a senha (use um terminal privado).
    $esconder = PHP_OS_FAMILY !== 'Windows' && function_exists('posix_isatty') && posix_isatty(STDIN);
    if ($esconder) {
        register_shutdown_function(fn () => shell_exec('stty echo'));
        shell_exec('stty -echo');
    }
    $senha = rtrim((string) fgets(STDIN), "\r\n");
    if ($esconder) {
        shell_exec('stty echo');
    }
    fwrite(STDOUT, "\n");
    return $senha;
}

$senha = ler_senha('Senha (mínimo 8 caracteres): ');
if (strlen($senha) < 8 || $senha !== ler_senha('Confirme a senha: ')) {
    fwrite(STDERR, "Senha curta ou as senhas não conferem.\n");
    exit(1);
}

$stmt = $conn->prepare('INSERT INTO funcionarios (nome_usuario, senha_hash) VALUES (?, ?)');
$hash = password_hash($senha, PASSWORD_BCRYPT, BCRYPT_OPCOES);
$stmt->bind_param('ss', $usuario, $hash);
if (!$stmt->execute()) {
    fwrite(STDERR, "Não foi possível criar o usuário (ele já existe?).\n");
    exit(1);
}
echo "Administrador '$usuario' criado. Entre por login.php.\n";

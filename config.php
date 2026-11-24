<?php
// Configuração compartilhada: conexão com o banco, sessão e funções de segurança.
// As credenciais vêm de variáveis de ambiente; os valores padrão servem para o XAMPP local.

if (session_status() === PHP_SESSION_NONE) {
    $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax', 'secure' => $https]);
    session_start();
}

if (!isset($conn)) {
    mysqli_report(MYSQLI_REPORT_OFF);
    $conn = new mysqli(
        getenv('DB_HOST') ?: '127.0.0.1',
        getenv('DB_USER') ?: 'root',
        getenv('DB_PASS') ?: '',
        getenv('DB_NAME') ?: 'loja_bolos'
    );
    if ($conn->connect_error) {
        error_log('Falha na conexão com o banco: ' . $conn->connect_error);
        http_response_code(500);
        exit('Não foi possível conectar ao banco de dados.');
    }
    $conn->set_charset('utf8mb4');
}

/** Escapa texto para exibição segura em HTML. */
function e($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

function usuario_logado(): bool
{
    return isset($_SESSION['usuario_id']);
}

/** Redireciona para o login quando não há usuário autenticado. */
function exigir_login(): void
{
    if (!usuario_logado()) {
        header('Location: login.php');
        exit;
    }
}

/** Lê um campo de texto do POST; ignora valores que não sejam texto (ex.: arrays). */
function post_texto(string $campo): string
{
    $valor = $_POST[$campo] ?? '';
    return is_string($valor) ? $valor : '';
}

// Limite de tentativas de login em janela fixa de 15 minutos.
// Cada tentativa é contada ANTES de verificar a senha, num único UPSERT atômico por chave,
// então requisições simultâneas não conseguem passar do limite.
const JANELA_LOGIN_MINUTOS = 15;
const MAX_TENTATIVAS_USUARIO = 5;   // por nome de usuário
const MAX_TENTATIVAS_IP = 30;       // por IP (mais alto: vários funcionários podem dividir a rede)
// Custo fixo do bcrypt: o hash fictício do login usa o mesmo custo dos hashes reais.
const BCRYPT_OPCOES = ['cost' => 10];

/** Conta uma tentativa para a chave e devolve o total dentro da janela atual. */
function contar_tentativa(mysqli $conn, string $chave): int
{
    $janela = JANELA_LOGIN_MINUTOS;
    $sql = "INSERT INTO tentativas_login (chave, tentativas, janela_inicio) VALUES (?, 1, NOW())
            ON DUPLICATE KEY UPDATE
                tentativas = IF(janela_inicio <= NOW() - INTERVAL $janela MINUTE, 1, tentativas + 1),
                janela_inicio = IF(janela_inicio <= NOW() - INTERVAL $janela MINUTE, NOW(), janela_inicio)";
    $stmt = $conn->prepare($sql);
    if (!$stmt || !$stmt->bind_param('s', $chave) || !$stmt->execute()) {
        return PHP_INT_MAX; // falha ao contar: bloqueia (fail closed)
    }

    $stmt = $conn->prepare('SELECT tentativas FROM tentativas_login WHERE chave = ?');
    if (!$stmt || !$stmt->bind_param('s', $chave) || !$stmt->execute()) {
        return PHP_INT_MAX;
    }
    $linha = $stmt->get_result()->fetch_row();
    return $linha ? (int) $linha[0] : PHP_INT_MAX;
}

/** Registra a tentativa de login e informa se ela ainda está dentro do limite. */
function tentativa_login_permitida(mysqli $conn, string $usuario): bool
{
    // Limpeza ocasional de registros antigos para a tabela não crescer sem fim.
    if (random_int(1, 50) === 1) {
        $conn->query('DELETE FROM tentativas_login WHERE janela_inicio < NOW() - INTERVAL 1 DAY');
    }
    // O IP é contado primeiro: se já passou do limite, nem cria registro para o usuário.
    if (contar_tentativa($conn, 'ip:' . ($_SERVER['REMOTE_ADDR'] ?? '?')) > MAX_TENTATIVAS_IP) {
        return false;
    }
    return contar_tentativa($conn, 'u:' . mb_strtolower(mb_substr($usuario, 0, 50))) <= MAX_TENTATIVAS_USUARIO;
}

/** Login correto zera só o contador do usuário (o do IP expira sozinho). */
function limpar_tentativas_usuario(mysqli $conn, string $usuario): void
{
    $chave = 'u:' . mb_strtolower(mb_substr($usuario, 0, 50));
    $stmt = $conn->prepare('DELETE FROM tentativas_login WHERE chave = ?');
    $stmt->bind_param('s', $chave);
    $stmt->execute();
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_campo(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function csrf_valido(): bool
{
    $enviado = $_POST['csrf_token'] ?? '';
    return is_string($enviado) && $enviado !== '' && hash_equals(csrf_token(), $enviado);
}

<?php
require_once __DIR__ . '/config.php';

// Variável para armazenar mensagens de erro
$erro = '';

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_valido()) {
    $erro = 'Sessão expirada. Recarregue a página e tente novamente.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_usuario = trim(post_texto('nome_usuario'));
    $senha = post_texto('senha');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $erro === '' && !tentativa_login_permitida($conn, $nome_usuario)) {
    $erro = 'Muitas tentativas. Aguarde ' . JANELA_LOGIN_MINUTOS . ' minutos e tente novamente.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $erro === '') {

    // Consulta ao banco de dados para buscar o usuário
    $sql = "SELECT id, senha_hash FROM funcionarios WHERE nome_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $nome_usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        // Verificar se a senha fornecida corresponde ao hash armazenado
        if (password_verify($senha, $usuario['senha_hash'])) {
            // Login bem-sucedido: novo ID de sessão (evita fixação) e dados na sessão
            limpar_tentativas_usuario($conn, $nome_usuario);
            session_regenerate_id(true);
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['nome_usuario'] = $nome_usuario;

            // Redirecionar para a página protegida
            header('Location: admin.php');
            exit;
        } else {
            $erro = 'Usuário ou senha inválidos.';
        }
    } else {
        // Usuário inexistente: gasta o mesmo tempo do bcrypt para não revelar quem existe.
        password_verify($senha, '$2y$10$jT2kyUlAdWA9t90lBfG4BuKuUQ4qkihYZ8u1QFITUt6vAIYM2YTAW');
        $erro = 'Usuário ou senha inválidos.';
    }
}
?>

<?php include "loginhead.php";  ?>

<body>
    <?php include "topbar.php"; ?>
    <div class="login-container">
        <div class="login-box">
            <h3 class="text-center mb-4">Login</h3>
            <?php if ($erro): ?>
                <div class="alert alert-danger"><?= e($erro) ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <?= csrf_campo() ?>
                <div class="mb-3">
                    <label for="nome_usuario" class="form-label">Usuário</label>
                    <input type="text" class="form-control" id="nome_usuario" name="nome_usuario" placeholder="Digite seu usuário" required>
                </div>
                <div class="mb-3">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" class="form-control" id="senha" name="senha" placeholder="Digite sua senha" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Entrar</button>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
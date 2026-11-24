<?php
require_once __DIR__ . '/config.php';
exigir_login();
?>
<?php include "head.php"; ?>

<body>
    <?php include("topbaradm.php"); ?>

    <div class="container-fluid about py-5">
        <div class="container">
            <div class="section-title position-relative text-center mx-auto mb-5 pb-3" style="max-width: 600px;">
                <h2 class="text-primary font-secondary">Bolos</h2>
                <h1 class="display-4 text-uppercase">Escolha um e aproveite</h1>
            </div>
            <?php include "addbolos.php"; ?>
            <?php include "bolos.php"; ?>
        </div>
    </div>
    <div class="container-fluid about py-5">
        <div class="container">
            <div class="section-title position-relative text-center mx-auto mb-5 pb-3" style="max-width: 600px;">
                <h2 class="text-primary font-secondary">Funcionarios</h2>
                <h1 class="display-4 text-uppercase">Melhor equipe</h1>
            </div>
            <?php include "exibirusuarios.php"; ?>
            <p class="text-center mt-3"><a href="register.php" class="btn btn-primary">Cadastrar funcionário</a></p>
        </div>
    </div>







    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/counterup/counterup.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>
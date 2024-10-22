<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Responsivo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<div id="loginPopup" class="popup">
    <div class="popup-content">
        <span class="close" onclick="closeLoginPopup()">&times;</span>
        <h2>Login</h2>
        <form action="testLogin.php" method="POST">

            <h4><label for="email">E-mail</label></h4>
            <input type="email" id="email" name="email" placeholder="Digite seu e-mail" required>

            <h4><label for="senha">Senha</label></h4>
            <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>

            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div class="remember-me">
                    <h5>
                        <input type="checkbox" id="rememberMe" name="rememberMe">
                        <label for="rememberMe">Manter-me conectado</label>
                    </h5>
                    <h3><a href="#" style="margin: 10px 0;">Esqueceu sua senha?</a></h3>
                </div>
            </div>

            <div class="button-container">
                <button type="submit">Fazer Login</button>
                <button type="button" id="employeeLoginIcon" class="btn-orange-light">
                    <i class="fas fa-user-shield"></i> Logar como funcionário
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function closeLoginPopup() {
        document.getElementById('loginPopup').style.display = 'none';
    }
</script>

</body>
</html>

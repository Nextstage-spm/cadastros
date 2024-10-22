<?php 
include("cadastrar_professor.php"); // Inclui a conexão com o banco de dados
$message = ''; // Variável para armazenar a mensagem de erro ou sucesso
$employeeMessage = ''; // Variável para armazenar a mensagem de erro ou sucesso do funcionário
$alertMessage = ''; // Variável para armazenar a mensagem de alerta

// Processa o login do usuário
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email']) && isset($_POST['senha'])) {
    if (!empty($_POST['email']) && !empty($_POST['senha'])) {
        $email = $_POST['email'];
        $senha = $_POST['senha'];

        $query = $conn->prepare("SELECT * FROM usuario WHERE email = :email");
        $query->bindParam(':email', $email);
        $query->execute();
        $usuario = $query->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            // Login bem-sucedido, cria sessão
            session_start();
            $_SESSION['user_id'] = $usuario['id'];
            $_SESSION['email'] = $email;

            // Verifica se a opção "Manter-me conectado" foi marcada
            if (isset($_POST['rememberMe'])) {
                setcookie('user_id', $usuario['id'], time() + (86400 * 30), "/"); // 30 dias
                setcookie('email', $email, time() + (86400 * 30), "/");
            }

            $message = "Login bem-sucedido.";
        } else {
            $message = "Usuário ou senha incorretos.";
        }
    } else {
        $message = "Por favor, preencha todos os campos.";
    }
}

// Processa o login do funcionário
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['employee_email']) && isset($_POST['employee_senha'])) {
    if (!empty($_POST['employee_email']) && !empty($_POST['employee_senha'])) {
        $employee_email = $_POST['employee_email'];
        $employee_senha = $_POST['employee_senha'];

        $query = $conn->prepare("SELECT * FROM funcionario WHERE email = :employee_email");
        $query->bindParam(':employee_email', $employee_email);
        $query->execute();
        $funcionario = $query->fetch(PDO::FETCH_ASSOC);

        if ($funcionario && password_verify($employee_senha, $funcionario['senha'])) {
            // Login do funcionário bem-sucedido, cria sessão
            session_start();
            $_SESSION['employee_id'] = $funcionario['id'];
            $_SESSION['employee_email'] = $employee_email;

            $employeeMessage = "Login do funcionário bem-sucedido.";
        } else {
            $employeeMessage = "Funcionário ou senha incorretos.";
        }
    } else {
        $employeeMessage = "Por favor, preencha todos os campos.";
    }
}

// Verifica se o usuário já está logado via cookie
session_start();
if (!isset($_SESSION['user_id']) && isset($_COOKIE['user_id'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
    $_SESSION['email'] = $_COOKIE['email'];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPM</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        /* Estilo geral para o layout */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.header-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
    background-color: #f5f5f5;
}

.logo img {
    max-width: 100%;
    height: auto;
}

/* Menu de navegação */
.menu {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

.menu a,
.menu button {
    text-decoration: none;
    padding: 10px 15px;
    border: none;
    background-color: #ddd;
    color: #333;
    cursor: pointer;
    transition: background-color 0.3s;
}

.menu a:hover,
.menu button:hover {
    background-color: #ccc;
}

/* Ajuste da responsividade do banner */
#banner {
    display: flex;
    justify-content: center;
}

#banner img {
    width: 100%;
    height: auto;
    max-width: 1536px;
}

/* Botões responsivos */
.button-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    padding: 20px;
}

.button-container button {
    flex: 1;
    margin: 5px;
    padding: 10px;
    font-size: 16px;
}

/* Media Queries para dispositivos menores */
@media (max-width: 768px) {
    .header-container {
        flex-direction: column;
        align-items: flex-start;
    }

    .menu {
        flex-direction: column;
        align-items: flex-start;
    }

    #banner img {
        height: auto;
    }

    .text-container {
        text-align: center;
    }

    .button-container {
        flex-direction: column;
        align-items: center;
    }

    .button-container button {
        width: 100%;
        margin: 10px 0;
    }
}

@media (max-width: 480px) {
    .header-container {
        padding: 5px;
    }

    .menu a,
    .menu button {
        padding: 8px;
        font-size: 14px;
    }

    .button-container button {
        font-size: 14px;
    }
}

        body {
            background-color: #ededed;
        }

        /* Estilos para o popup */
        .popup {
            display: none;
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            width: 300px; /* Largura fixa para os pop-ups */
        }

        .popup-content {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Estilos para a mensagem de erro */
        .message {
            display: none;
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
            text-align: center;
        }

        /* Exibir a mensagem de erro/sucesso */
        .message.active {
            display: block;
        }

        .close {
            cursor: pointer;
        }

        /* Estilos para o pop-up de alerta */
        .alert-popup {
            display: none;
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 1100; /* Acima do pop-up de login */
            width: 300px;
            padding: 20px;
            text-align: center;
        }

        .alert-popup .alert-icon {
            color: #856404;
            font-size: 30px;
        }

        .alert-message {
            margin-top: 10px;
            color: #856404;
        }

        .alert-buttons {
            display: flex;
            justify-content: space-around;
            margin-top: 10px;
        }
    </style>
</head>

<body>
<header>
    <div class="header-container">
        <div class="logo">
            <img src="./img/Slide S.P.M. (3).png" alt="Logotipo" height="100" width="125">
        </div>
        <nav class="menu">
            <a href="./index.php">Início</a>
            <a href="./inscricao.html">Inscrição</a>
            <a href="#consulta">Consulta</a>
            <a href="./duvidas.html">Dúvidas</a>
            <a href="./regras.html">Regras</a>
            <a href="./sobre.html">Sobre</a>
            <button id="loginBtn">Fazer Login</button>
        </nav>
    </div>

    <!-- Popup de Login -->
    <div id="loginPopup" class="popup">
        <div class="popup-content">
            <span class="close" onclick="closeLoginPopup()">&times;</span>
            <h2>Comece sua Pré-Matrícula no SPM</h2>
            <form action="" method="POST">
                <h4><label for="email">E-mail</label></h4>
                <input type="email" id="email" name="email" placeholder="Digite seu e-mail" required>
        
                <h4><label for="senha">Senha</label></h4>
                <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>

                <div class="remember-me">
                    <input type="checkbox" id="rememberMe" name="rememberMe">
                    <label for="rememberMe">Manter-me conectado</label>
                </div>

                <!-- Mensagem de erro ou sucesso como pop-up interno -->
                <div class="message <?php echo !empty($message) ? 'active' : ''; ?>">
                    <?php echo $message; ?>
                </div>

                <button type="submit">Fazer Login</button>
            </form>
            <!-- Ícone clicável para abrir o pop-up do funcionário -->
            <i class="fas fa-user-cog" id="employeeLoginIcon" title="Login do Funcionário" style="cursor:pointer; margin-top: 10px;"></i>
            <span> Login do Funcionário</span>
        </div>
    </div>

    <!-- Popup de Login do Funcionário -->
    <div id="employeeLoginPopup" class="popup">
        <div class="popup-content">
            <span class="close" onclick="closeEmployeeLoginPopup()">&times;</span>
            <h2>Login do Funcionário</h2>
            <form action="" method="POST"> <!-- A mesma página processa o login -->
                <h4><label for="employee_email">E-mail</label></h4>
                <input type="email" id="employee_email" name="employee_email" placeholder="Digite seu e-mail" required>
                
                <h4><label for="employee_senha">Senha</label></h4>
                <input type="password" id="employee_senha" name="employee_senha" placeholder="Digite sua senha" required>

                <!-- Mensagem de erro ou sucesso como pop-up interno -->
                <div class="message <?php echo !empty($employeeMessage) ? 'active' : ''; ?>">
                    <?php echo $employeeMessage; ?>
                </div>

                <button type="submit">Fazer Login</button>
            </form>
        </div>
    </div>

    <!-- Popup de Alerta -->
    <div id="alertPopup" class="alert-popup">
        <div class="alert-content">
            <i class="fas fa-exclamation-triangle alert-icon"></i>
            <div class="alert-message">Área restrita, login apenas para funcionários.</div>
            <div class="alert-buttons">
                <button onclick="closeAlertPopup()">Fechar</button>
                <button onclick="openEmployeeLoginPopup()">Prosseguir</button>
            </div>
        </div>
    </div>
</header>

<script>
    // Função para abrir o pop-up do funcionário
    document.getElementById('employeeLoginIcon').onclick = function() {
        document.getElementById('alertPopup').style.display = 'block'; // Mostra o alerta
    }

    // Função para abrir o pop-up de login do funcionário
    function openEmployeeLoginPopup() {
        closeAlertPopup(); // Fecha o pop-up de alerta
        closeLoginPopup(); // Fecha o pop-up de login do usuário
        document.getElementById('employeeLoginPopup').style.display = 'block'; // Abre o pop-up de login do funcionário
    }

    // Função para fechar o pop-up de alerta
    function closeAlertPopup() {
        document.getElementById('alertPopup').style.display = 'none';
    }

    // Função para fechar o pop-up de login
    function closeLoginPopup() {
        document.getElementById('loginPopup').style.display = 'none';
    }

    // Função para fechar o pop-up de login do funcionário
    function closeEmployeeLoginPopup() {
        document.getElementById('employeeLoginPopup').style.display = 'none';
    }

    // Função para abrir o pop-up de login do usuário
    document.getElementById('loginBtn').onclick = function() {
        document.getElementById('loginPopup').style.display = 'block';
    }
</script>
</body>
</html>

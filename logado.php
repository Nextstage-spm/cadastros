<?php 
session_start(); // Iniciar a sessão para verificar o login

// Verificar se o usuário está logado
if (!isset($_SESSION['id'])) {
    // Se não estiver logado, redirecionar para a página de login
    header('Location: inicio.php');
    exit;
}

// Conteúdo da página protegida
echo "Bem-vindo, " . $_SESSION['email'] . "!";

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Responsivo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Estilos gerais */
        body {
            font-family: "Open Sans", sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ededed;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: #ededed;
            position: relative;
            max-height: 45px;
            height: auto;
        }

        .logo img {
            max-width: 105px;
            height: auto;
            margin-top: 7px;
        }

        /* Container para o menu e botão de login */
        .menu-login-container {
            display: flex;
            align-items: center;
            margin-left: auto; /* Para alinhar à direita */
        }

        /* Menu visível por padrão */
        .menu {
            margin-top: -7px;
            display: flex;
            flex-direction: row;
            gap: 15px;
            position: static;
            background-color: transparent;
            margin-left: auto; /* Para alinhar o menu à direita */
        font-family: "Open Sans", sans-serif;
        }


        .menu a {
            text-decoration: none;
            padding: 0 15px;
            display: block;
            text-decoration: none; /* Remove sublinhado padrão */
            color: rgb(129, 121, 121); /* Cor do texto */
            text-decoration: none; /* Remove o sublinhado dos links */; 
            font-size: 17px; /* Tamanho da fonte */
            font-weight: normal;
            transition: 0.3s ease; /* Efeito suave ao passar o mouse */
            position: relative;
            margin: 0 15px;
            font-family: 'Open-sans', sans-serif;
            margin-top: 11px;
        }

        /* Ícone do menu (hambúrguer) */
        .menu-icon {
            font-size: 24px;
            background: none;
            border: none;
            cursor: pointer;
            display: none;
            margin-top: 10px;
            margin-right: 15px;
        }

        /* Estilo do botão de login */
        .loginBtn {
            font-size: 16px;
            background: none;
            border: none;
            cursor: pointer;
            text-decoration: none;
            color: white;
            border: none;
            padding: 10px 32px; /* Espaçamento interno do botão */
            background-color: #023d54; /* Cor de fundo do botão */
            padding: 10px 32px; /* Espaçamento interno do botão */
            font-size: 19px; /* Tamanho do texto no botão */
            cursor: pointer; /* Cursor de pointer para indicar que é clicável */
            border-radius: 30px; /* Bordas arredondadas */
            margin-left: 20px; /* Espaço entre o menu e o botão */
            margin-top: 2px;
            transition: background-color 0.3s ease; /* Transição suave para hover */
            box-shadow: #000000;
            font-family: "Open Sans", sans-serif;
        }

        .loginBtn:hover {
        background-color: #023d54; /* Cor de fundo quando o mouse está sobre o botão */
         }
  

 /* CSS para tornar o banner responsivo */
 #banner {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            height: 160px;
            margin-top: -10px;
        }

        #banner-img {
            width: 100%;
            height: auto;
            max-width: 1536px;
        }

        /* Estilo responsivo */
        @media (max-width: 768px) {
            .menu {
                display: none; /* Oculta o menu em telas menores */
                flex-direction: column;
                position: absolute;
                top: 60px;
                left: 0;
                width: 100%;
                background-color: #fff;
                padding: 10px;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
                margin-left: 0; /* Remove o margin-left em telas menores */
            }

            .menu-icon {
                display: block; /* Exibe o ícone do menu em telas menores */
            }

            .menu a {
                padding: 10px;
            }

            /* Alinha o botão de login à direita em telas menores */
            .loginBtn {
                margin-left: 10px; /* Adiciona espaço entre o botão de hamburguer e o botão de login */
            }
        }
        .loginBtn:hover {
            background-color: #8ca6cc;
        }

        /* Estilo responsivo */
        @media (max-width: 768px) {
            .menu {
                display: none; /* Oculta o menu em telas menores */
                flex-direction: column;
                position: absolute;
                top: 60px;
                left: 0;
                width: 100%;
                background-color: #fff;
                padding: 10px;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
                margin-left: 0; /* Remove o margin-left em telas menores */
            }

            .menu-icon {
                display: block; /* Exibe o ícone do menu em telas menores */
                color: #2f2c73;
            }

            .menu a {
                padding: 10px;
            }

            /* Alinha o botão de login à direita em telas menores */
            .loginBtn {
                margin-left: -5px; /* Adiciona espaço entre o botão de hamburguer e o botão de login */
            }
        }

        /* CSS para tornar o banner responsivo */
        #banner {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        #banner-img {
            width: 100%;
            height: auto;
            max-width: 1536px;
        }

        /* Popups */
        .popup {
            display: none;
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: white;
            justify-content: center;
            align-items: center;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
            width: 590px; /* Aumente o valor aqui para ajustar a largura */
            max-width: 100%; /* Certifique-se de que o popup não ultrapasse a largura da tela */
            height: 350px;
            border-radius: 10px;
        }

        .popup-content {
            text-align: left;
            height: 350px; /* Define a altura fixa */
            width: 590px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            position: absolute;
            top: -5px;
            right: 9px;
            cursor: pointer;
            font-size: 45px;
            font-weight: normal;
            color: #afafaf;
        }

        /* Estilos de formulários */
        h4 {
            text-align: left;
            margin-bottom: 1px;
            font-size: 13px;
            color: #000000;
            font-family: "Open Sans", sans-serif;
            margin-top: 10px;
            margin-left: 135px;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 25px;
            color: #023d54;
            font-family: "Open Sans", sans-serif;
            font-weight: normal;
            margin-top: 30px;
            letter-spacing: 0.3px; 
        }

        h3 {
            text-align: center;
            margin-bottom: 1px;
            font-size: 11px;
            color: #000000;
            font-family: "Open Sans", sans-serif;
            margin-top: 10px;
            margin-bottom: -100px;
            margin-left: 10px;
            font-weight: bold;
            text-decoration: underline;
            cursor: pointer;

        }

        h5 {
            align-items: center;
            margin-bottom: 1px;
            font-size: 11px;
            color: #000000;
            font-family: "Open Sans", sans-serif;
            margin-top: 10px;
            margin-bottom: -100px;
            margin-right: 0px;
            margin-left: 1px;
            font-weight: normal;
            text-decoration: none;
            cursor: pointer;
        }
        input[type="email"],
        input[type="password"] {
            width: 55%;
            padding: 8px;
            margin: 10px 0;
            margin-top: 0px;
            margin-left: 120px;
            border: 1px solid #ccc;
            border-radius: 15px;
            cursor: pointer;
            font-size: 12px;
           
}

        button[type="submit"]
        {
            max-width: 300px; /* Largura máxima */
            display: flex;
            flex-direction: column;
            background-color: #023d54;
            color: white;
            font-size: 16px;
            font-weight: normal;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 15px;
            margin-top: 60px;
            margin-bottom: 0px;
            width: 145px;
            height: 36px;
            margin-left: 418px;
            align-items: center;
}

        button[type="submit"]:hover {
            background-color: #536bc1; /* Verde escuro */
        
        }


        .remember-me {
            margin: 10px 0;
            display: flex;
            align-items: right; /* Para alinhar verticalmente */
            margin-left: 160px;
            margin-right: 0px;
            margin-top: 10px;
            margin-bottom: -50;
        }
        .button-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 20px;
            text-align: center;
        }

        .button-container button[type="submit"] {
       
            max-width: 300px; /* Largura máxima */
            display: flex;
            flex-direction: column;
            background-color: #023d54;
            color: white;
            font-size: 16px;
            font-weight: normal;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 15px;
            margin-top: 40px;
            margin-bottom: 0px;
            width: 145px;
            height: 36px;
            margin-left: 390px;
            align-items: center;
        }

        #employeeLoginIcon {
            background-color: rgb(255, 255, 255);
            color: rgb(0, 0, 0); 
            border: none; 
            padding: 10px 20px; 
            border-radius: 5px; 
            cursor: pointer; 
            font-size: 17px;
            margin-top: -34px;
            margin-right: 510px;
        }

        .btn-orange-light-top {
            background-color: #ff9800; /* Laranja */
        }

        .btn-purple-light-top {
            background-color: #673ab7; /* Roxo */
        }

        .btn-green-light {
            background-color: #4caf50; /* Verde */
        }

        .btn-red-light {
            background-color: #f44336; /* Vermelho */
        }

        /* Adicionando o separador */
        .separator {
            width: 80%;
            border: 1px solid #ccc;
            margin: 20px auto;
        }

        /* Estilo para a mensagem de alerta */
        .alert-message {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: rgba(255, 0, 0, 0.8);
            color: white;
            padding: 20px;
            border-radius: 8px;
            z-index: 2000;
        }

        /* Estilo para a confirmação */
        .confirm-popup {
            display: none;
            position: fixed;
            z-index: 1001;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.725);
            justify-content: center;
            align-items: center;
        }

        .confirm-popup-content {
            background-color: #fff;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 90%; /* Largura responsiva */
            max-width: 400px; /* Largura máxima */
            border-radius: 8px;
            position: relative;
            text-align: center;
        }

        /* Estilos gerais */
.content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    width: 60%; /* Ajuste conforme necessário */
    margin: 0px auto;
}

button i.icon {
    margin-left: 10px; /* Espaçamento entre o texto e o ícone */
}

.text-container p {
        font-size: 17px; /* Ajusta o tamanho da fonte do texto */
        margin-left: 130px;
        border-top: 1px;
}

.content p {
    text-align: center; /* Justifica o texto dentro do parágrafo */
    margin-top: px; /* Espaçamento entre o texto e a linha horizontal */;
}

/* Estilos individuais dos botões */
.btn-orange-light-top {
    background: linear-gradient(to bottom, #ffd3b8, #ec5a05);
    position: relative;
    flex: 0 1 calc(33% - 10px); /* Cada botão ocupa 50% da largura */
    padding: 40px;
    font-size: 18px;
    font-weight: bold;
    font-family: "Open Sans", sans-serif;
    color: rgb(255, 255, 255);
    border: none;
    border-radius: 45px; /* Arredondamento dos cantos */
    cursor: pointer;
    box-shadow: 0px 20px 15px rgba(0, 0, 0, 0.2); /* Sombra nos botões */
    text-align: left;
    display: inline-flex;
    justify-content: left;
    overflow: hidden;
}

.btn-orange-light-top::before {
    background: rgba(0, 0, 0, 0.4); /* Cor e opacidade da luz */
    box-shadow: 0px 0px 0px rgb(0, 0, 0) inset; /* Efeito de luz */
    border-radius: 30px 30px 0 0; /* Apenas a parte superior arredondada */
}

.btn-purple-light-top {
    background: linear-gradient(to bottom, #e1bee7, #6a1b9a);
    position: relative;
    flex: 0 1 calc(33% - 10px); /* Cada botão ocupa 50% da largura */
    padding: 40px;
    font-size: 18px;
    font-weight: bold;
    font-family: "Open Sans", sans-serif;
    color: rgb(255, 255, 255);
    border: none;
    border-radius: 45px; /* Arredondamento dos cantos */
    cursor: pointer;
    box-shadow: 0px 20px 15px rgba(0, 0, 0, 0.2); /* Sombra nos botões */
    text-align: left;
    display: inline-flex;
    justify-content: left;
    overflow: hidden;
}

.btn-purple-light-top::before {
    background: rgba(255, 255, 255, 0.4); /* Cor e opacidade da luz */
    box-shadow: 0 0 25px rgb(255, 255, 255) inset; /* Efeito de luz */
    border-radius: 30px 30px 0 0; /* Apenas a parte superior arredondada */
}

.btn-green-light-top {
    background: linear-gradient(to bottom, #d2f4ac, #388e3c);
    position: relative;
    flex: 0 1 calc(33% - 10px); /* Cada botão ocupa 50% da largura */
    padding: 40px;
    font-size: 18px;
    font-weight: bold;
    font-family: "Open Sans", sans-serif;
    color: rgb(255, 255, 255);
    border: none;
    border-radius: 45px; /* Arredondamento dos cantos */
    cursor: pointer;
    box-shadow: 0px 20px 15px rgba(0, 0, 0, 0.2); /* Sombra nos botões */
    text-align: left;
    display: inline-flex;
    justify-content: left;
    overflow: hidden;
}

.btn-green-light-top::before {
    background: rgba(255, 255, 255, 0.4); /* Cor e opacidade da luz */
    box-shadow: 0 0 15px rgba(255, 255, 255, 0.6) inset; /* Efeito de luz */
    border-radius: 30px 30px 0 0; /* Apenas a parte superior arredondada */
}

.btn-red-light-top {
    background: linear-gradient(to bottom, #edb4bc, #e20101);
    position: relative;
    flex: 0 1 calc(33% - 10px); /* Cada botão ocupa 50% da largura */
    padding: 40px;
    font-size: 18px;
    font-weight: bold;
    font-family: "Open Sans", sans-serif;
    color: rgb(255, 255, 255);
    border: none;
    border-radius: 45px; /* Arredondamento dos cantos */
    cursor: pointer;
    box-shadow: 0px 20px 15px rgba(0, 0, 0, 0.2); /* Sombra nos botões */
    text-align: left;
    display: inline-flex;
    justify-content: left;
    overflow: hidden;
}

.btn-red-light-top::before {
    background: rgba(255, 255, 255, 0.3); /* Cor e opacidade da luz */
    box-shadow: 0 0 15px rgba(255, 255, 255, 0.5) inset; /* Efeito de luz */
    border-radius: 30px 30px 0 0; /* Apenas a parte superior arredondada */
}

.text-container p {
    font-size: 18px; /* Ajusta o tamanho da fonte do texto */
    margin-left: 150px;
    margin-bottom: -10px;
}

.separator {
    border-top: 1px solid; /* Borda padrão */
    margin-left: 130px; /* Alinha com o texto */
    margin-bottom: 10px; /* Espaçamento abaixo do separator */
    color: #5f5c5c;
}

@media (max-width: 1200px) {
    .text-container p {
        margin-left: 100px;
        font-size: 16px;
}
    }

    .separator {
        margin-right: 150px;
        border-top: 1px solid;

    }

@media (max-width: 992px) {
    .text-container p {
        margin-left: 70px;
        font-size: 15px;
}
    }

    .separator {
        margin-left: 144px;
        border-top: 1px solid;
        width: 1140px;
        margin-top: -30;
    }


@media (max-width: 768px) {
    .text-container p {
        margin-left: 40px;
        font-size: 14px;
    }

    .separator {
        margin-left: 40px;
        border-top: 1px; /* Borda tracejada para telas menores */
    }
}

@media (max-width: 576px) {
    .text-container p {
        margin-left: 20px;
        font-size: 13px;
    }

    .separator {
        margin-left: 20px;
        border-top: 0.5px dashed; /* Borda mais fina */
    }
}




    </style>
</head>

<body>

<header class="header-container">
    <div class="logo">
        <img src="./img/Slide S.P.M. (3).png" alt="Logotipo">
    </div>

    <!-- Container para o menu e botão de usuário -->
    <div class="menu-login-container">
        <!-- Menu de navegação -->
        <nav class="menu" id="menu">
            <a href="./index.php">Início</a>
            <a href="./inscricao.html">Inscrição</a>
            <a href="#consulta">Consulta</a>
            <a href="./duvidas.html">Dúvidas</a>
            <a href="./regras.html">Regras</a>
            <a href="./funcionario.html">Sobre</a>
        </nav>
        
        <!-- Botão de Menu e Botão de Usuário -->
        <button class="menu-icon" id="menu-toggle"><i class="fas fa-bars"></i></button>
        <button class="userBtn" id="login-toggle">
            <i class="fas fa-user"></i> Usuário
        </button>
    </div>
</header>

<!-- Popup de Login -->
<div id="loginPopup" class="popup">
    <div class="popup-content">
        <span class="close" onclick="closeLoginPopup()">&times;</span>
        <h2>Comece sua Pré-Matrícula no SPM</h2>
        <form action="#" method="POST">
            <h4><label for="email">E-mail</label></h4>
            <input type="email" id="email" name="email" placeholder="Digite seu e-mail" required>

            <h4><label for="senha">Senha</label></h4>
            <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>

            <!-- Link de esqueceu sua senha e checkbox de manter-me conectado ao lado -->
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div class="remember-me">
                    <h5><input type="checkbox" id="rememberMe" name="rememberMe"></h5>
                    <h5><label for="rememberMe">Manter-me conectado</label></h5>
                    <h3><a href="#" style="margin: 10px 0;">Esqueceu sua senha?</a></h3>
                </div>
            </div>

            <div class="message <?php echo !empty($message) ? 'active' : ''; ?>">
                <?php echo $message; ?>
            </div>

            <div class="button-container">
                <button type="submit">Fazer Login</button>
                <button type="button" id="employeeLoginIcon" class="btn-orange-light">
                    <i class="fas fa-user-shield"></i>
                </button> <!-- Ícone para abrir o pop-up do funcionário -->
            </div>
        </form>
    </div>
</div>

<!-- Popup de Confirmação -->
<div id="confirmPopup" class="confirm-popup">
    <div class="confirm-popup-content">
        <p>Área restrita, login apenas para funcionários.</p>
        <button id="cancelButton" class="btn-red-light">Cancelar</button>
        <button id="proceedButton" class="btn-green-light">Prosseguir</button>
    </div>
</div>

<!-- Popup de Login do Funcionário -->
<div id="employeeLoginPopup" class="popup">
    <div class="popup-content">
        <span class="close" onclick="closeEmployeeLoginPopup()">&times;</span>
        <h2>Comece sua Pré-Matrícula no SPM</h2>
        <form action="#" method="POST">
            <h4><label for="employeeEmail">E-mail</label></h4>
            <input type="email" id="employeeEmail" name="employeeEmail" placeholder="Digite seu e-mail" required>

            <h4><label for="employeePassword">Senha</label></h4>
            <input type="password" id="employeePassword" name="employeePassword" placeholder="Digite sua senha" required>

            <!-- Link de esqueceu sua senha e checkbox de manter-me conectado ao lado -->
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div class="remember-me">
                    <h5><input type="checkbox" id="rememberMe" name="rememberMe"></h5>
                    <h5><label for="rememberMe">Manter-me conectado</label></h5>
                    <h3><a href="#" style="margin: 10px 0;">Esqueceu sua senha?</a></h3>
                </div>
            </div>

            <button type="submit">Fazer Login</button>
        </form>
    </div>
</div>

<div id="banner">
    <img id="banner-img" src="./img/banner.png" alt="Banner">
</div>

<div class="content"></div>
<div class="text-container">
    <p>
        Utilize o menu abaixo para navegar pelo site
    </p>
</div>
<hr class="separator">

<div class="button-container">
    <button class="btn-orange-light-top">Inscrição
        <i class="fas fa-user-edit icon"></i> 
    </button> 
    <button class="btn-purple-light-top">Consulte a sua inscrição
        <i class="fas fa-search icon"></i> 
    </button>
    <button class="btn-green-light-top">Tire suas dúvidas
        <i class="fas fa-question icon"></i>
    </button>
    <button class="btn-red-light-top">Regras
        <i class="fas fa-gavel icon"></i> 
    </button> 
</div>


    <header class="header-container">
        <div class="logo">
            <img src="./img/Slide S.P.M. (3).png" alt="Logotipo">
        </div>

        <!-- Container para o menu e botão de login -->
        <div class="menu-login-container">
            <!-- Menu de navegação -->
            <nav class="menu" id="menu">
                <a href="./index.php">Início</a>
                <a href="./inscricao.html">Inscrição</a>
                <a href="#consulta">Consulta</a>
                <a href="./duvidas.html">Dúvidas</a>
                <a href="./regras.html">Regras</a>
                <a href="./sobre.html">Sobre</a>
                <a href="./sobre.html">Ferramenta</a>
            </nav>
            
            <!-- Botão de Menu e Botão de Login -->
            <button class="menu-icon" id="menu-toggle"><i class="fas fa-bars"></i></button>
            <a href="#" class="loginBtn" id="login-toggle">Fazer Login</a>
        </div>
    </header>

    <!-- Popup de Login -->
    <div id="loginPopup" class="popup">
        <div class="popup-content">
            <span class="close" onclick="closeLoginPopup()">&times;</span>
            <h2>Comece sua Pré-Matrícula no SPM</h2>
            <form action="#" method="POST">

                <h4><label for="email">E-mail</label></h4>
                <input type="email" id="email" name="email" placeholder="Digite seu e-mail" required>

                <h4><label for="senha">Senha</label></h4>
                <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>

                <!-- Link de esqueceu sua senha e checkbox de manter-me conectado ao lado -->
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div class="remember-me">
                        <h5><input type="checkbox" id="rememberMe" name="rememberMe"></h5>
                        <h5><label for="rememberMe">Manter-me conectado</label></h5>

                       <h3><a href="#" style="margin: 10px 0;">Esqueceu sua senha?</a></h3>
                    </div>
                </div>

                <div class="message <?php echo !empty($message) ? 'active' : ''; ?>">
                    <?php echo $message; ?>
                </div>

                <div class="button-container">
                    <button type="submit">Fazer Login</button>
                    <button type="button" id="employeeLoginIcon" class="btn-orange-light"><i class="fas fa-user-shield"></i></button> <!-- Ícone para abrir o pop-up do funcionário -->
                </div>
            </form>
        </div>
    </div>

    <!-- Popup de Confirmação -->
    <div id="confirmPopup" class="confirm-popup">
        <div class="confirm-popup-content">
            <p>Área restrita, login apenas para funcionários.</p>
            <button id="cancelButton" class="btn-red-light">Cancelar</button>
            <button id="proceedButton" class="btn-green-light">Prosseguir</button>
        </div>
    </div>

    <!-- Popup de Login do Funcionário -->
    <div id="employeeLoginPopup" class="popup">
        <div class="popup-content">
            <span class="close" onclick="closeEmployeeLoginPopup()">&times;</span>
            <h2>Comece sua Pré-Matrícula no SPM</h2>
            <form action="#" method="POST">
                <h4><label for="employeeEmail">E-mail</label></h4>
                <input type="email" id="employeeEmail" name="employeeEmail" placeholder="Digite seu e-mail" required>

                <h4><label for="employeePassword">Senha</label></h4>
                <input type="password" id="employeePassword" name="employeePassword" placeholder="Digite sua senha" required>

                <!-- Link de esqueceu sua senha e checkbox de manter-me conectado ao lado -->
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div class="remember-me">
                        <h5><input type="checkbox" id="rememberMe" name="rememberMe"></h5>
                        <h5><label for="rememberMe">Manter-me conectado</label></h5>

                       <h3><a href="#" style="margin: 10px 0;">Esqueceu sua senha?</a></h3>
                    </div>
                </div>

                <button type="submit">Fazer Login</button>
            </form>
        </div>
    </div>

    <div id="banner">
        <img id="banner-img" src="./img/banner.png" alt="Banner">
    </div>

    <div class="content"></div>
    <div class="text-container">
        <p>
            Utilize o menu abaixo para navegar pelo site
        </p>
    </div>
    <hr class="separator">
</div>
        <div class="button-container">
        <button class="btn-orange-light-top">Inscrição
        <i class="fas fa-user-edit icon"></i> </button> 
        <button class="btn-purple-light-top">Consulte a sua inscrição
        <i class="fas fa-search icon"></i> </button>
        <button class="btn-green-light-top">Tire suas dúvidas
        <i class="fas fa-question icon"></i></button>
        <button class="btn-red-light-top">Regras
        <i class="fas fa-gavel icon"></i> </button> 
        </div>

    <script>
        
    </script>

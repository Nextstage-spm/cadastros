<?php
session_start();
include('config.php');

// Ativar exibição de erros para desenvolvimento
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Função para log detalhado de erros
function logError($message) {
    error_log(date('Y-m-d H:i:s') . " - " . $message);
}

// Log inicial
logError("Iniciando processamento de login");

// Verificar conexão com o banco de dados
if (!isset($conn)) {
    logError("Conexão com o banco de dados não está definida");
    die("Erro ao conectar ao banco de dados");
}

// Validar entrada do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');
    $cpf = trim($_POST['cpf'] ?? '');
    $rememberMe = isset($_POST['rememberMe']) ? 1 : 0;

    // Verificar campos obrigatórios
    if (empty($email) || (empty($senha) && empty($cpf))) {
        logError("Campos obrigatórios não preenchidos");
        echo "<script>alert('Preencha todos os campos obrigatórios');</script>";
        exit;
    }

    try {
        // Verificar se o usuário é um aluno
        $stmt = $conn->prepare("SELECT * FROM aluno_cadastro WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $aluno = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($aluno) {
            if (!empty($cpf) && $cpf === $aluno['cpf']) {
                // Login bem-sucedido para aluno
                $_SESSION['id'] = $aluno['id'];
                $_SESSION['nome'] = $aluno['nome'];
                $_SESSION['email'] = $aluno['email'];
                $_SESSION['tipo_usuario'] = 'aluno';

                if ($rememberMe) {
                    $token = bin2hex(random_bytes(32));
                    $update_stmt = $conn->prepare("UPDATE aluno_cadastro SET remember_token = :token WHERE id = :id");
                    $update_stmt->execute([':token' => $token, ':id' => $aluno['id']]);

                    $cookie_data = json_encode(['email' => $email, 'token' => $token, 'tipo' => 'aluno']);
                    setcookie('remember_me', $cookie_data, time() + (86400 * 30), '/');
                }

                logError("Login bem-sucedido para aluno: {$aluno['nome']}");
                header("Location: logadoaluno.php");
                exit;
            } else {
                logError("CPF incorreto para o email: $email");
                echo "<script>alert('CPF incorreto');</script>";
            }
        }

        // Verificar se o usuário é um funcionário
        $stmt = $conn->prepare("SELECT * FROM funcionarios WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $funcionario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($funcionario && password_verify($senha, $funcionario['senha'])) {
            // Login bem-sucedido para funcionário
            $_SESSION['id'] = $funcionario['id'];
            $_SESSION['nome'] = $funcionario['nome'];
            $_SESSION['email'] = $funcionario['email'];
            $_SESSION['tipo_usuario'] = 'funcionario';

            if ($rememberMe) {
                $token = bin2hex(random_bytes(32));
                $update_stmt = $conn->prepare("UPDATE funcionarios SET remember_token = :token WHERE id = :id");
                $update_stmt->execute([':token' => $token, ':id' => $funcionario['id']]);

                $cookie_data = json_encode(['email' => $email, 'token' => $token, 'tipo' => 'funcionario']);
                setcookie('remember_me', $cookie_data, time() + (86400 * 30), '/');
            }

            logError("Login bem-sucedido para funcionário: {$funcionario['nome']}");
            header("Location: 3.33.php");
            exit;
        } else {
            logError("Credenciais inválidas para o email: $email");
            echo "<script>alert('Email ou senha inválidos');</script>";
        }
    } catch (PDOException $e) {
        logError("Erro na execução da query: " . $e->getMessage());
        echo "<script>alert('Erro no servidor. Tente novamente mais tarde.');</script>";
    }
}

// Exibir informações da sessão para depuração
logError("Sessão atual: " . print_r($_SESSION, true));
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPM</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="shortcut icon" href="./img/Slide S.P.M. (55).png" type="image/x-icon">
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

        h1 {
            margin-bottom: -50px;
            margin-top: -28px;
            margin-left: 55px;
            font-size: 14px;
            color: #000000;
            font-family: "Open Sans", sans-serif;
            font-weight: normal;
            text-decoration: none;

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

.button.button-container {
    display: grid; /* Define o container como grid */
    grid-template-columns: 1fr 1fr; /* Dois botões por linha */
    grid-gap: 30px 200px; /* Espaçamento entre os botões */
    max-width: 400px; /* Largura máxima opcional para alinhar os botões */
    margin: 0 auto; /* Centraliza horizontalmente */
    justify-content: center;/* Centraliza o conteúdo horizontalmente */
    align-items: center;
    margin-top: 100px;
}
/* Estilos individuais dos botões */
.btn-orange-light-top {
    background: linear-gradient(to bottom, #ffd3b8, #ec5a05);
    position: relative;
    width: 350px;
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
    margin-top: 45px;
    margin-right: 300px;
    margin-left: -70px;
    margin-bottom: 80px;
}

.btn-orange-light-top::before {
    background: rgba(0, 0, 0, 0.4); /* Cor e opacidade da luz */
    box-shadow: 0px 0px 0px rgb(0, 0, 0) inset; /* Efeito de luz */
    border-radius: 30px 30px 0 0; /* Apenas a parte superior arredondada */
}

.btn-purple-light-top {
    background: linear-gradient(to bottom, #e1bee7, #6a1b9a);
    position: relative;
    width: 350px;
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
    width: 350px;
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
    margin-right: 300px;
    margin-left: -70px;
    margin-bottom: 20px;

}

.btn-green-light-top::before {
    background: rgba(255, 255, 255, 0.4); /* Cor e opacidade da luz */
    box-shadow: 0 0 15px rgba(255, 255, 255, 0.6) inset; /* Efeito de luz */
    border-radius: 30px 30px 0 0; /* Apenas a parte superior arredondada */
}

.btn-red-light-top {
    background: linear-gradient(to bottom, #edb4bc, #e20101);
    position: relative;
    width: 350px;
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
        width: 1175px;
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


.input-group {
    position: relative;
}

.input-group input {
    width: 50%;
    padding-right: 40px; /* Espaço para o ícone */
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

.input-group-text {
    position: absolute;
    right: 10px;
    top: 50%;
    cursor: pointer;
    margin-right: 135px;
    margin-top: -4px;
}




.modal {
    display: none; /* Inicialmente escondido */
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5); /* Fundo escuro translúcido */
    overflow: auto;
    text-align: center;
}

/* Estilo da janela do modal */
.modal-content {
    background-color: #fff;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 450px;
    border-radius: 10px;
    position: relative;
}

/* Estilo do botão de fechar */
.modal .close {
    font-size: 20px;
    position: absolute;
    top: -3px;
    right: 10px;
    font-size: 45px;
    font-weight: normal;
    color: #afafaf;
}

.modal .close:hover,
.modal .close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

.modal h2 {
    font-size: 22px;
    color: #023d54;
    font-family: "Open Sans", sans-serif;

}

#okButton {
    padding: 10px 20px;
    background-color: #023d54;
    color: white;
    border: none;
    border-radius: 7px;
    cursor: pointer;
    margin-top: 10px;
}

#okButton:hover {
    background-color: #023d54;
}





/* Estilo do dialog de erro */
.dialog {
    display: none; /* Inicialmente escondido */
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgb(0,0,0);
    background-color: rgba(0,0,0,0.4); /* Cor de fundo semi-transparente */
    padding-top: 60px;
}

.dialog-content {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 400px;
    text-align: center;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

/* Para centralizar o conteúdo */
.dialog-content h2 {
    font-size: 18px;
}

.overlay-background {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    backdrop-filter: blur(5px);
}

.painel-recuperacao {
    background: linear-gradient(to right, #ffffff, #f8f9fa);
    width: 90%;
    max-width: 400px;
    margin: 10% auto;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    position: relative;
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        transform: translateY(-20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.fechar-painel {
    position: absolute;
    right: 15px;
    top: 15px;
    font-size: 24px;
    color: #666;
    cursor: pointer;
    transition: color 0.3s ease;
    background: none;
    border: none;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    
}

.fechar-painel:hover {
    color: #333;
    background-color: #f0f0f0;
}

.titulo-recuperacao {
    color: #2c3e50;
    font-size: 1.5em;
    margin-bottom: 25px;
    text-align: center;
    font-weight: 600;
}

.campo-entrada {
    margin-bottom: 20px;
}

.campo-entrada label {
    display: block;
    color: #4a5568;
    margin-bottom: 8px;
    font-size: 0.9em;
    font-weight: 500;
}

.campo-entrada input {
    width: 100%;
    padding: 12px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 1em;
    transition: all 0.3s ease;
    background-color: #fff;
}

.campo-entrada input:focus {
    border-color: #4299e1;
    box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
    outline: none;
}

.campo-entrada input::placeholder {
    color: #a0aec0;
}

#btnRecuperar {
    width: 40%;
    padding: 12px;
    background: linear-gradient(to right, #4299e1, #3182ce);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 1em;
    font-weight: 500;
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    display: grid;
    margin-left: 250px;
    
}

.btn-recuperar:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(66, 153, 225, 0.2);
}

.btn-recuperar:active {
    transform: translateY(0);
}

/* Mensagem de erro/sucesso */
.mensagem {
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 15px;
    font-size: 0.9em;
    display: none;
}

.mensagem-erro {
    background-color: #fff5f5;
    color: #c53030;
    border: 1px solid #feb2b2;
}

.mensagem-sucesso {
    background-color: #f0fff4;
    color: #2f855a;
    border: 1px solid #9ae6b4;
}

/* Responsividade */
@media (max-width: 480px) {
    .painel-recuperacao {
        width: 95%;
        margin: 5% auto;
        padding: 20px;
    }

    .titulo-recuperacao {
        font-size: 1.3em;
    }

    .campo-entrada input {
        padding: 10px;
    }
}

.campo-entrada small.dica-campo {
    display: block;
    color: #718096;
    font-size: 0.8em;
    margin-top: 5px;
    font-style: italic;
}

.campo-entrada input[type="text"],
.campo-entrada input[type="email"] {
    width: 100%;
    padding: 12px;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    font-size: 1em;
    transition: all 0.3s ease;
    background-color: #fff;
    height: 8px;
    margin-left: -12px
}

.campo-entrada input[type="text"]:focus,
.campo-entrada input[type="email"]:focus {
    border-color: #4299e1;
    box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
    outline: none;
}


    </style>
</head>

<body>

    <header class="header-container">
        <div class="logo">
            <img src="./img/Slide S.P.M. (3).png" alt="Logotipo">
        </div>

        <!-- Container para o menu e botão de login -->
        <div class="menu-login-container">
            <!-- Menu de navegação -->
            <nav class="menu" id="menu">
                <a href="index.php">Início</a>
                <a href="#" id="inscricao">Inscrição</a>
                <a href=""id="consulta" >Consulta</a>
                <a href="./dduvida2.html">Dúvidas</a>
                <a href="./rregras.html">Regras</a>
                <a href="./sobre.html">Sobre</a>
            </nav>
            
            <!-- Botão de Menu e Botão de Login -->
            <button class="menu-icon" id="menu-toggle"><i class="fas fa-bars"></i></button>
            <a href="#" class="loginBtn" id="login-toggle">Fazer Login</a>
        </div>
    </header>

 <!-- Modal de mensagem para login -->
 <div id="loginModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeLoginModal()">&times;</span>
        <h2>É necessario fazer login para acessar.</h2>
        <button id="okButton" onclick="closeLoginModal()">OK</button>
    </div>
</div>

    <!-- Popup de Login -->
    <div id="loginPopup" class="popup">
        <div class="popup-content">
            <span class="close" onclick="closeLoginPopup()">&times;</span>
            <h2>Faça login para realizar sua pré-matrícula</h2>
            <form action="" method="POST">

                <h4><label for="email">E-mail</label></h4>
                <input type="email" id="email" name="email" placeholder="Digite seu e-mail" required>

                <div class="input-group">
                <h4><label for="cpf">Senha</label></h4>
                <input type="password" id="cpf" name="cpf" placeholder="Digite sua senha" required autocomplete="new-password">
                <span class="input-group-text" onclick="togglePassword('senha', 'toggleSenhaIcon')">
                <i id="toggleSenhaIcon" class="fa fa-eye"></i>
                </span>
              </div>

                <!-- Link de esqueceu sua senha e checkbox de manter-me conectado ao lado -->
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div class="remember-me">
                        <h5><input type="checkbox" id="rememberMe" name="rememberMe"></h5>
                        <h5><label for="rememberMe">Manter-me conectado</label></h5>
                        

                        <h3><a href="#" onclick="abrirPainelRecuperacao(); return false;">Esqueceu sua senha?</a></h3>
                    </div>
                </div>

                <div class="button-container">
                    <button type="submit">Fazer Login</button>
                    <button type="button" id="employeeLoginIcon" class="btn-orange-light"><i class="fas fa-user-shield"></i></button> <!-- Ícone para abrir o pop-up do funcionário -->
                </div>



                <div id="errorDialog" class="dialog" style="display:none;">
    <div class="dialog-content">
        <span class="close" onclick="closeErrorDialog()">&times;</span>
        <h2 id="errorMessage">Erro: Mensagem de erro</h2>
    </div>
</div>

                <h1> Logar como funcionário</h1> 
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
            <h2>Login do Funcionário</h2>
            <form action="" method="POST">
                <h4><label for="email">E-mail</label></h4>
                <input type="email" id="email" name="email" placeholder="Digite seu e-mail" required>

                <div class="input-group">
                <h4><label for="senha">Senha</label></h4>
                <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required autocomplete="new-password">
                <span class="input-group-text" onclick="togglePassword('senha', 'toggleSenhaIcon')">
                <i id="toggleSenhaIcon" class="fa fa-eye"></i>
                </span>
              </div>
            
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

   <!-- Painel de Recuperação de Senha -->
<div id="painelRecuperacao" class="overlay-background">
    <div class="painel-recuperacao">
        <button class="fechar-painel" onclick="fecharPainelRecuperacao()">&times;</button>
        
        <h2 class="titulo-recuperacao">Recuperação de Senha</h2>
        
        <div id="mensagemErro" class="mensagem mensagem-erro"></div>
        <div id="mensagemSucesso" class="mensagem mensagem-sucesso"></div>
        
        <form id="formRecuperacao" onsubmit="validarPalavraSecreta(event)">
            <div class="campo-entrada">
                <label for="email">E-mail</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       placeholder="Digite seu e-mail cadastrado" 
                       required>
            </div>

            <div class="campo-entrada">
                <label for="palavrasecreta">Palavra Secreta</label>
                <input type="text"
                       id="palavrasecreta" 
                       name="palavrasecreta" 
                       placeholder="Digite sua palavra secreta" 
                       required>
                <small class="dica-campo">Digite a palavra secreta fornecida no cadastro</small>
            </div>
            
            <button type="submit" id="btnRecuperar" class="btn-recuperar">
                Recuperar Senha
            </button>
        </form>
    </div>
</div>

<!-- Incluir o arquivo JavaScript -->
<script src="recuperacao.js"></script>


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
    <div>
    <a href="inc4.php">
        <button class="btn-orange-light-top">Inscrição
        <i class="fas fa-user-edit icon"></i> </button></a>

        <a href="conn.php">
        <button class="btn-purple-light-top">Consulte a sua inscrição
        <i class="fas fa-search icon"></i> </button> </a>
        </div>

        <div>
        <a href="duvidaa.html">
        <button class="btn-green-light-top">Tire suas dúvidas
        <i class="fas fa-question icon"></i></button></a>

        <a href="rregras1.html">
        <button class="btn-red-light-top">Regras
        <i class="fas fa-gavel icon"></i> </button> </a>
</div>
</div>


    <script>
        // Função para abrir o pop-up de login
        document.getElementById('login-toggle').onclick = function () {
            document.getElementById('loginPopup').style.display = 'flex';
        }

        // Função para fechar o pop-up de login
        function closeLoginPopup() {
            document.getElementById('loginPopup').style.display = 'none';
        }

        // Função para abrir o pop-up do funcionário com confirmação
        document.getElementById('employeeLoginIcon').onclick = function () {
            document.getElementById('confirmPopup').style.display = 'flex'; // Exibe a confirmação
        }

        // Função para cancelar a abertura do pop-up do funcionário
        document.getElementById('cancelButton').onclick = function () {
            document.getElementById('confirmPopup').style.display = 'none'; // Fecha a confirmação
        }

        // Função para prosseguir e abrir o pop-up do funcionário
        document.getElementById('proceedButton').onclick = function () {
            closeLoginPopup(); // Fecha o pop-up de login do usuário
            document.getElementById('confirmPopup').style.display = 'none'; // Fecha a confirmação
            document.getElementById('employeeLoginPopup').style.display = 'flex'; // Abre o pop-up do funcionário
        }

        // Função para fechar o pop-up do funcionário
        function closeEmployeeLoginPopup() {
            document.getElementById('employeeLoginPopup').style.display = 'none';
        }

        // Função para abrir/fechar o menu em telas menores
        document.getElementById('menu-toggle').onclick = function () {
            const menu = document.getElementById('menu');
            menu.style.display = (menu.style.display === 'flex') ? 'none' : 'flex';
        }

        // Fechar popups ao clicar fora deles
        window.onclick = function (event) {
            const loginPopup = document.getElementById('loginPopup');
            const employeeLoginPopup = document.getElementById('employeeLoginPopup');
            const confirmPopup = document.getElementById('confirmPopup');
            if (event.target === loginPopup) {
            }
        }

        function togglePassword(fieldId, iconId) {
    var passwordField = document.getElementById(fieldId);
    var icon = document.getElementById(iconId);

    if (passwordField.type === "password") {
        passwordField.type = "text"; // Mostra a senha
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash"); // Altera o ícone para olho riscado
    } else {
        passwordField.type = "password"; // Esconde a senha
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye"); // Altera o ícone para olho normal
    }
}   


document.getElementById('inscricao').addEventListener('click', function(event) {
    event.preventDefault(); // Impede o link de funcionar normalmente
    showLoginModal(); // Exibe o modal de mensagem
});

document.getElementById('consulta').addEventListener('click', function(event) {
    event.preventDefault(); // Impede o link de funcionar normalmente
    showLoginModal(); // Exibe o modal de mensagem
});

// Função para exibir o modal
function showLoginModal() {
    document.getElementById("loginModal").style.display = "block";
}

// Função para fechar o modal
function closeLoginModal() {
    document.getElementById("loginModal").style.display = "none";
}



// Função para abrir o painel de recuperação
function abrirPainelRecuperacao() {
    document.getElementById('loginPopup').style.display = 'none';
    document.getElementById('painelRecuperacao').style.display = 'block';
}

// Função para fechar o painel de recuperação
function fecharPainelRecuperacao() {
    document.getElementById('painelRecuperacao').style.display = 'none';
    document.getElementById('loginPopup').style.display = 'block';
}

// Função para mostrar mensagens
function mostrarMensagem(tipo, mensagem) {
    const mensagemErro = document.getElementById('mensagemErro');
    const mensagemSucesso = document.getElementById('mensagemSucesso');
    
    mensagemErro.style.display = 'none';
    mensagemSucesso.style.display = 'none';
    
    if (tipo === 'erro') {
        mensagemErro.textContent = mensagem;
        mensagemErro.style.display = 'block';
    } else {
        mensagemSucesso.textContent = mensagem;
        mensagemSucesso.style.display = 'block';
    }
}

// Função para validar a palavra secreta
function validarPalavraSecreta(event) {
    if (event) event.preventDefault();
    
    console.log('Iniciando validação...');

    const email = document.getElementById('email').value;
    const palavrasecreta = document.getElementById('palavrasecreta').value;
    
    console.log('Dados a serem enviados:', { email, palavrasecreta });

    const formData = new FormData();
    formData.append('email', email);
    formData.append('palavrasecreta', palavrasecreta);
    formData.append('acao', 'recuperar_senha');

    const btnRecuperar = document.getElementById('btnRecuperar');
    btnRecuperar.disabled = true;
    btnRecuperar.textContent = 'Verificando...';

    fetch('inicioo.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Status da resposta:', response.status);
        return response.text();
    })
    .then(text => {
        console.log('Resposta do servidor:', text);
        try {
            const data = JSON.parse(text);
            if (data.success) {
                mostrarMensagem('sucesso', 'Credenciais corretas! Redirecionando...');
                setTimeout(() => {
                    window.location.href = 'logadoaluno.php';
                }, 1500);
            } else {
                mostrarMensagem('erro', data.message || 'Email ou palavra secreta incorretos');
            }
        } catch (e) {
            console.error('Erro ao processar resposta:', e);
            mostrarMensagem('erro', 'Erro ao processar resposta do servidor');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        mostrarMensagem('erro', 'Erro ao verificar credenciais');
    })
    .finally(() => {
        btnRecuperar.disabled = false;
        btnRecuperar.textContent = 'Recuperar Senha';
    });
}

// Adicionar listeners quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
    const formRecuperacao = document.getElementById('formRecuperacao');
    if (formRecuperacao) {
        formRecuperacao.addEventListener('submit', validarPalavraSecreta);
    }
});
    </script>
</body>
</html>
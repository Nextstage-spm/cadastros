<?php
include("config.php"); // Inclui a conexão com o banco de dados

session_start();

if (isset($_SESSION['id'])) {
    
    echo "ID do aluno: " . $_SESSION['id'];  
    $id = $_SESSION['id'];
} else {
    echo "Sessão não iniciada ou ID não encontrado.";
    exit; 
}


// Inicializa as variáveis para mensagens
$mensagem = "";
$tipo_alerta = "";

// Atualização do perfil do aluno
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    // Verifica se todos os campos necessários foram preenchidos
    if (!empty($_POST['nome']) && !empty($_POST['email']) && !empty($_POST['telefone']) &&
        !empty($_POST['data_nascimento']) && !empty($_POST['cpf'])) {

        // Recebe os dados do formulário
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $telefone = $_POST['telefone'];
        $data_nascimento = $_POST['data_nascimento'];
        $cpf = $_POST['cpf'];

        // Vincula os parâmetros da query
        $query->bindParam(':id', $id);
        $query->bindParam(':nome', $nome);
        $query->bindParam(':email', $email);
        $query->bindParam(':telefone', $telefone);
        $query->bindParam(':data_nascimento', $data_nascimento);
        $query->bindParam(':cpf', $cpf);

        // Executa a consulta
        if ($query->execute()) {
            $mensagem = "Perfil atualizado com sucesso!";
            $tipo_alerta = "success";
        } else {
            $mensagem = "Erro ao atualizar o perfil.";
            $tipo_alerta = "error";
        }
    } else {
        $mensagem = "Por favor, preencha todos os campos.";
        $tipo_alerta = "error";
    }
}

// Consulta para buscar os dados do aluno
$id = $_SESSION['id'];
$sql = "SELECT id, nome, email, telefone, data_nascimento, cpf FROM aluno_cadastro WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $id);
$stmt->execute();
$aluno = $stmt->fetch(PDO::FETCH_ASSOC);

// Verifica se o aluno foi encontrado
if (!$aluno) {
    $mensagem = "Aluno não encontrado.";
    $tipo_alerta = "error";
}
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>

header {
        background-color: #f9f9f9; /* Cor de fundo do cabeçalho */
        padding: 0px 0;
    }
    /* Contêiner do cabeçalho */
    .header-container {
        display: flex;
        justify-content: space-between; /* Distribui o logotipo à esquerda e o menu à direita */
        align-items: right; /* Alinha os itens verticalmente ao centro */
        width: 99%;
        margin: 0px 0px; /* Centraliza o conteúdo na tela */
    }
       
    /* Estilo para o header */
    header {
        display: flex;
        justify-content: space-between; /* Espaça o logo e o banner */
        margin-top: 5px;
        margin-bottom: 3px;;
        align-items: center; /* Alinha o logo e o banner no centro verticalmente */
        padding: 0px; /* Espaçamento ao redor do header */
        height: 55px;
        background-color: #f9f9f9; /* Cor de fundo para o header (pode ser alterada conforme necessário) */
    }
    
  /* Logotipo */
.logo img {
    position: fixed;
    z-index: 10; /* Garante que o logotipo fique acima do menu */
    height: 55px; /* Ajuste conforme o tamanho do logotipo */
    top: 10px; /* Distância do topo */
    left: 10px; /* Distância da esquerda */
    margin-top: 0; /* Remova o margin-top negativo */
}

/* Menu de navegação */
.menu {
    width: 100%;
    position: fixed;
    top: 0; /* Mantém o menu fixo no topo da página */
    left: 0;
    background-color: #f9f9f9;
    padding: 18px 0; /* Padding para ajuste do conteúdo dentro do menu */
    text-align: center;
    display: flex; /* Coloca os itens do menu em linha */
    align-items: center; /* Alinha os itens verticalmente ao centro */
    justify-content: flex-end; /* Alinha os itens do menu à direita */
    box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1); /* Sombra para o menu */
    z-index: 1; /* Garante que o menu fique abaixo do logotipo */
}

/* Itens do menu */
.menu a {
    text-decoration: none; /* Remove sublinhado padrão */
    color: rgb(129, 121, 121); /* Cor do texto */
    padding: 10px 25px; /* Espaçamento interno dos links */
    font-family: 'Open-sans', sans-serif;
    font-size: 16px; /* Tamanho da fonte */
    font-weight: normal;
    transition: 0.3s ease; /* Efeito suave ao passar o mouse */
    position: relative;
    display: inline-block;
    margin: 0 15px; /* Adiciona espaçamento lateral */
}

/* Cor de fundo ao passar o mouse */
.menu a:hover {
    background-color: #f9f9f9;
}

/* Sublinhado animado */
.menu a::after {
    content: "";
    position: absolute;
    width: 0;
    height: 3.2px;
    bottom: -5px;
    left: 0;
    background-color: rgb(187, 87, 165); 
    transition: width 0.3s ease-in-out;
}

.menu a:hover::after {
    width: 100%; /* Aumenta o sublinhado ao passar o mouse */
}

/* Itens específicos do menu */
.menu a.inicio::after {
    background-color: #011772; /* Cor do sublinhado para Início */
}

.menu a.inscricao::after {
    background-color: #32cd32; /* Cor do sublinhado para Inscrição */
}

.menu a.regras::after {
    background-color: #9400d3; /* Cor do sublinhado para Regras */
}

.menu a.ajuda::after {
    background-color: #ff4500; /* Cor do sublinhado para Ajuda */
}

.menu a.sobre::after {
    background-color: #ffa500; /* Cor do sublinhado para Sobre */
}

header img {
    height: 40px;
}

.menu button {
    margin: 0 10px;
    text-decoration: none;
    font-size: 18px;
    font-weight: normal;
    padding: 0px 32px; /* Espaçamento interno do botão */
}

.menu button {
    color: white;
    border: 0px;
    padding: 10px 15px;
    cursor: pointer;
    padding: 10px 32px; /* Espaçamento interno do botão */
    background-color: #023d54; /* Cor de fundo do botão */
    padding: 10px 32px; /* Espaçamento interno do botão */
    font-size: 19px; /* Tamanho do texto no botão */
    cursor: pointer; /* Cursor de pointer para indicar que é clicável */
    border-radius: 30px; /* Bordas arredondadas */
    margin-left: 0px; /* Espaço entre o menu e o botão */
    margin-right: 0px;
    margin-top: 9px;
    transition: background-color 0.3s ease; /* Transição suave para hover */
    font-family: "Open Sans", sans-serif;
}

.menu button:hover {
    background-color: #023d54; /* Cor de fundo quando o mouse está sobre o botão */
}

.menu button:active {
    transform: scale(0.99); /* Leve redução no tamanho ao clicar */
}

.menu button:focus {
    outline: none; /* Remove o contorno padrão quando o botão está em foco */
    box-shadow: 0 0 0 0px #000000; /* Adiciona uma sombra ao redor do botão quando está em foco */
}

/* Animação ao passar o mouse */
.menu a:hover::after {
    width: 100%; /* O sublinhado se expande ao passar o mouse */
}

.menu button:hover {
    background-color: #468296cb; /* Cor mais escura ao passar o mouse */
}

        body {
            font-family: "Open Sans", sans-serif;
            background-color: #f4f4f9;
        }

        .container {
            max-width: 500px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.1);
            margin-top: 90px;
            margin-bottom: -5px;
        }

        h2 {
            background-color: #5aa2b0;
            color: white;
            text-align: center;
            padding: 15px 0;
            font-size: 24px;
            font-weight: bold;
        }

        form {
            display: grid;
            gap: 20px;
            padding: 20px;
        }

        label {
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="date"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #5aa2b0;
            border-radius: 8px;
            font-size: 14px;
        }

        button {
            padding: 10px;
            background-color: #174650;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #5aa2b0;
        }

        .alert {
            padding: 10px;
            margin-top: 10px;
            text-align: center;
            border-radius: 8px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>

<div class="header-container">
            <div class="logo">
                <img src="./img/Slide S.P.M. (13).png" alt="Logotipo" height="100" width="125" a href="html.index">
                </a>
            </div>
            <nav class="menu">
                <a href="./logadoaluno.php">Início</a>
                <a href="./inc1.php">Inscrição</a>
                <a href="#" id="consulta">Consulta</a>
                <a href="./dduvida1.html">Dúvidas</a>
                <a href="./rregras1.html">Regras</a>
                <a href="./sobre1.html">Sobre </a>
            </nav>
        </div>
    <div class="container">
        <h2>Editar Perfil</h2>
        
        <!-- Exibição da mensagem de feedback -->
        <?php if (!empty($mensagem)): ?>
            <div class="alert <?php echo $tipo_alerta; ?>"><?php echo $mensagem; ?></div>
        <?php endif; ?>

        <!-- Formulário de edição de perfil -->
        <?php if ($aluno): ?>
            <form method="POST" action="">
                <input type="hidden" name="id" value="<?php echo $aluno['id']; ?>">
                <div><label for="nome">Nome:</label><input type="text" name="nome" id="nome" value="<?php echo $aluno['nome']; ?>" required></div>
                <div><label for="email">Email:</label><input type="email" name="email" id="email" value="<?php echo $aluno['email']; ?>" required></div>
                <div><label for="telefone">Telefone:</label><input type="tel" name="telefone" id="telefone" value="<?php echo $aluno['telefone']; ?>" required></div>
                <div><label for="cpf">CPF:</label><input type="text" name="cpf" id="cpf" value="<?php echo $aluno['cpf']; ?>" required></div>
                <div><label for="data_nascimento">Data de Nascimento:</label><input type="date" name="data_nascimento" id="data_nascimento" value="<?php echo $aluno['data_nascimento']; ?>" required></div>
                <button type="submit">Atualizar Perfil</button>
              
            </form>
        <?php endif; ?>
    </div>
</body>
</html>

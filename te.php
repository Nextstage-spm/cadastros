<?php
include("config.php"); // Inclui a conexão com o banco de dados

$mensagem = "";
$tipo_alerta = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verifica se todos os campos estão preenchidos
    if (!empty($_POST['nome']) && !empty($_POST['senha']) && !empty($_POST['confirma_senha']) &&
        !empty($_POST['email']) && !empty($_POST['telefone']) && !empty($_POST['data_nascimento'])) {

        // Captura os valores do formulário
        $nome = $_POST['nome'];
        $senha = $_POST['senha'];
        $confirma_senha = $_POST['confirma_senha'];
        $email = $_POST['email'];
        $telefone = $_POST['telefone'];
        $data_nascimento = $_POST['data_nascimento'];

        if ($senha === $confirma_senha) {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT); // Hash da senha

            try {
                // Prepara a query SQL para inserir os dados
                $query = $conn->prepare("INSERT INTO aluno_cadastro (nome, senha, email, telefone, data_nascimento) 
                                         VALUES (:nome, :senha, :email, :telefone, :data_nascimento)");

                // Vincula os parâmetros da query
                $query->bindParam(':nome', $nome);
                $query->bindParam(':senha', $senha_hash);
                $query->bindParam(':email', $email);
                $query->bindParam(':telefone', $telefone);
                $query->bindParam(':data_nascimento', $data_nascimento);

                // Executa a query e verifica se foi bem-sucedida
                if ($query->execute()) {
                    $mensagem = "Cadastro realizado com sucesso!";
                    $tipo_alerta = "success";
                } else {
                    $mensagem = "Erro ao cadastrar o aluno.";
                    $tipo_alerta = "error";
                }
            } catch (PDOException $e) {
                $mensagem = "Erro ao cadastrar o aluno: " . $e->getMessage();
                $tipo_alerta = "error";
            }
        } else {
            $mensagem = "As senhas não coincidem. Tente novamente.";
            $tipo_alerta = "error";
        }
    } else {
        $mensagem = "Por favor, preencha todos os campos.";
        $tipo_alerta = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar Dobrável com Ícones</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Font Awesome para ícones -->
    <link rel="stylesheet" href="path/to/seu-estilo.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Open-sans', sans-serif;
            display: flex;
            background-color: #f4f4f9;
        }

        /* Estilo da sidebar */
        .sidebar {
            height: 100vh;
            width: 255px; /* Largura padrão */
            background: #06357b;
            color: #ffffff;
            transition: width 0.3s; /* Transição suave para largura */
            position: fixed;
            overflow-y: auto;
            margin-top: 130px;
            text-align: right;
            margin-left: -1115px;
        }

        .sidebar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background: #04315e;
            border-radius: 0px;
            margin-top: 0px;
            
        }

        /* Para navegadores WebKit (Chrome, Safari) */
.sidebar::-webkit-scrollbar {
    width: 10px; /* Largura da barra de rolagem */
    background-color: #06357b;
}

.sidebar::-webkit-scrollbar-track {
    background-color: #06357b;
    border-radius: 15px; /* Bordas arredondadas da trilha */
}

.sidebar::-webkit-scrollbar-thumb {
    background: blue; /* Cor do "polegar" da barra de rolagem */
    border-radius: 10px; /* Bordas arredondadas do polegar */
}

.sidebar::-webkit-scrollbar-thumb:hover {
    background-color: #06357b;
}

/* Para Firefox */
.sidebar {
    scrollbar-width: thin; /* Define a largura da barra de rolagem como fina */
    scrollbar-color: #888 #f1f1f1; /* Cor do polegar e da trilha */
}


        .sidebar-header h3 {
            margin: 0;
            font-size: 1.5em; /* Tamanho da fonte do cabeçalho */
            transition: opacity 0.3s; /* Transição suave para opacidade */
            overflow: hidden; /* Esconder overflow */
            white-space: nowrap; /* Impedir quebra de linha */
            text-overflow: ellipsis; /* Adicionar reticências se o texto for longo */
        }

        .toggle-btn {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
        }

        .nav {
            list-style: none;
            padding: 0;
            margin-right: 5px;
            margin-left: 0px;
        }

        .nav li {
            width: 100%;
        }

        .nav a {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: #ffffff;
            text-decoration: none;
            transition: background 0.3s;
            font-size: 15px;
            margin-top: 15px;
            transition: none;
        }

        .nav a:hover, .nav a.active {
            background: #6da3be3d;
            width: auto;
            border-radius: 20px 5px 45px 20px;
        }

        .nav .dropdown {
            display: none;
            background: #06357b;
            padding-left: 20px; /* Indentação dos itens do dropdown */
        }

        .nav .dropdown li a {
            padding: 10px 20px; /* Padding interno dos itens do dropdown */
        
        }

        /* Estilo da área de conteúdo */
        .content {
            margin-left: 255px; /* Espaço reservado para a sidebar */
            padding: 20px;
            width: 100%;
            transition: margin-left 0.3s; /* Transição suave para a margem */
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

        /* Responsividade */
        @media (max-width: 768px) {
            .sidebar {
                width: 60px; /* Largura da sidebar dobrada */
            }

            .sidebar.collapsed {
                width: 0; /* Esconder a sidebar completamente */
            }

            .content {
                margin-left: 60px; /* Ajustar margem da área de conteúdo */
            }

            .content.collapsed {
                margin-left: 0; /* Sem espaço reservado quando a sidebar é colapsada */
            }

            .nav a span {
                display: none; /* Esconder textos na sidebar dobrada */
            }

            .nav a.active span {
                display: none; /* Também esconder texto do item ativo */
            }

            .sidebar-header h3 {
                display: none; /* Esconder o título quando colapsado */
            }

}
 }

        h4{
            margin-left: 8px;
        } 
        
        #conteudo {
            display: flex; /* Usando flexbox */
            justify-content: center; /* Centraliza horizontalmente */
            align-items: center; /* Centraliza verticalmente */
            min-height: 200px; /* Define uma altura mínima, ajuste conforme necessário */
            text-align: center; /* Centraliza o texto */
            margin-left: 400px;
        }
      
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: #ffffff;
            position: relative;
            max-height: 300px;
            height:60px;
            margin-bottom: 50px;
            margin-top: 1px;
            width: 1540px;
            margin-left: -5px;
        }

        .logo img {
            max-width: 105px;
            height: 55px;
            margin-top: 7px;
            margin-right: 20px;
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
            text-decoration: none; /* Remove o sublinhado dos links */;
            text-align: left;
            margin-right: -390px;
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
        }

        /* Container para o menu e botão de login */
        .menu-login-container {
            display: flex;
            align-items: center;
            margin-left: auto; /* Para alinhar à direita */
        }

        /* Estilo responsivo */
        @media (max-width: 768px) {
            .nav menu {
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
        }

    </style>
</head>
<body>
<header class="header-container">
        <div class="logo">
            <img src="./img/Slide S.P.M. (13).png" alt="Logotipo">
        </div>

        <div class="menu-login-container">
            <!-- Menu de navegação -->
            <nav class="menu" id="menu">
                <a href="./te.php">Início</a>
                <a href="#">Lista</a>
                <a href="#">Cadastros</a>
                <a href="#">Relatórios</a>
                <a href="#">Configuração</a>
            </nav>
<div>
    <div>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h3>Admini</h3>
            <button class="toggle-btn" id="toggle-btn">&#9776;</button>
        </div>
        <ul class="nav">
            <li><a href="#" class="active"><i class="fas fa-home"></i> <h4></h4><span>Início</span></a></li>
            <li><a href="#"><i class="fas fa-clock icon"></i> <h4></h4><span>Lista de Espera</span></a></li></h4>


            <a href="javascript:void(0)" class="dropdown-toggle"><i class="fas fa-user-edit"></i> <h4></h4><span>Cadastros</span> <h4></h4><i class="fas fa-chevron-down"></i></a>
                <ul class="dropdown">
                    <li><a href="cadd.php"><i class="fas fa-pencil-alt"></i> <h4></h4>Aluno</a></li>
                    <li><a href="cad.funcionario.php"><i class="fas fa-pencil-alt"></i> <h4></h4>Funcionário</a></li>
                    <li><a href="cad.curso.php"><i class="fas fa-pencil-alt"></i> <h4></h4>Curso</a></li>
                    <li><a href="cad.turma2.php"><i class="fas fa-pencil-alt"></i> <h4></h4>Turma</a></li>
                    <li><a href="cad.funcionario.php"><i class="fas fa-pencil-alt"></i> <h4></h4>Professor</a></li>

                </ul>
                </li>

            <li><a href="#"><i class="fas fa-chart-bar icon"></i> <h4></h4><span>Relatórios Gerenciais</span></a></li>
            <li>
                <a href="javascript:void(0)" class="dropdown-toggle"><i class="fas fa-cogs"></i> <h4></h4><span>Configuração</span> <h4></h4><i class="fas fa-chevron-down"></i></a>
                <ul class="dropdown">
                    <li><a href="#"><i class="fas fa-pencil-alt"></i> <h4></h4>Editar Perfil</a></li>
                </ul>
            </li>
            <li><a href="#"><i class="fas fa-sign-out-alt"></i> <h4></h4><span>Sair</span></a></li>
        </ul>
    </div>

    <div id="conteudo">
        <!-- O conteúdo de cadd.php será carregado aqui -->
    </div>

    <script>
    // Script para alternar o menu de configurações e rotacionar a seta
    document.querySelector('.dropdown-toggle').addEventListener('click', function() {
        const dropdown = document.querySelector('.dropdown');
        const chevron = this.querySelector('.fas.fa-chevron-down');

        // Alterna a visibilidade do dropdown
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';

        // Alterna a classe de rotação
        chevron.classList.toggle('rotate');
    });

     // Script para carregar conteúdo via AJAX
     document.querySelectorAll('.carregar-conteudo').forEach(item => {
        item.addEventListener('click', function() {
            const url = this.getAttribute('data-url'); // Obtém a URL do atributo data-url
            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro ao carregar o conteúdo');
                    }
                    return response.text();
                })
                .then(data => {
                    document.getElementById('conteudo').innerHTML = data; // Insere o conteúdo carregado
                })
                .catch(error => {
                    document.getElementById('conteudo').innerHTML = '<p>' + error.message + '</p>';
                });
        });
    });

    </script>
</body>
</html>
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
    <title>Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            display: flex;
            flex-direction: row;
        }

        /* Estilo da barra lateral (sidebar) */
        .sidebar {
            width: 250px;
            background-color: #023d54;
            height: 100vh;
            padding-top: 20px;
            position: fixed;
            top: 0;
            left: 0;
            overflow-y: auto;
            transition: width 0.3s ease;
            z-index: 1000;
        }

        .sidebar a {
            text-decoration: none;
            color: white;
            padding: 15px;
            display: block;
            margin: 10px 0;
            font-size: 18px;
            border-radius: 5px;
            transition: background-color 0.2s;
        }

        .sidebar a:hover {
            background-color: #1b2336;
        }

        .sidebar .profile {
            text-align: center;
            margin-bottom: 30px;
        }

        .sidebar .profile img {
            border-radius: 50%;
            width: 90px;
            height: 80px;
            margin-bottom: 10px;
        }

        /* Estilo do conteúdo principal */
        .main-content {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
            transition: margin-left 0.3s ease, width 0.3s ease;
        }

        /* Estilo da barra de navegação superior */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #ffffff;
            padding: 15px;
            box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
            width: 100%;
            box-sizing: border-box;
        }

        .navbar .title {
            flex: 1;
        }

        .navbar .search-bar {
            flex: 2;
            display: flex;
            align-items: center;
            justify-content: flex-start; /* Alinhar para o início */
        }

        .navbar .search-bar input {
            padding: 10px;
            border-radius: 20px;
            border: 1px solid #ddd;
            margin-right: 10px; /* Ajustado para o espaçamento entre o campo de entrada e o ícone */
            width: 80%; /* Mantido dentro de um limite */
            max-width: 300px; /* Limitar a largura máxima para evitar quebra */
            min-width: 150px; /* Largura mínima para manter visibilidade */
        }

        .navbar .icons {
            flex: 1;
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

        .navbar .icons i {
            margin-left: 20px;
            font-size: 20px;
            cursor: pointer;
        }

        /* Estilos para os cartões de informação */
        .dashboard-cards {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            gap: 20px;
            flex-wrap: wrap;
        }

        .card {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
            width: 23%;
            text-align: center;
            min-width: 220px;
            margin-bottom: 20px;
        }

        .card h3 {
            font-size: 18px;
            margin-bottom: 15px;
        }

        .card h2 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .chart-section {
            margin-top: 30px;
            display: flex;
            flex-direction: column;
        }

        .chart {
            background-color: #ffffff;
            width: 100%;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .footer {
            text-align: center;
            padding: 20px;
            background-color: #fff;
            margin-top: 40px;
            border-radius: 8px;
            box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
        }

        /* Responsividade */
        @media (max-width: 1024px) {
            .main-content {
                margin-left: 250px;
                width: calc(100% - 250px);
            }

            .navbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .navbar .search-bar {
                width: 100%;
                margin-top: 10px;
            }

            .dashboard-cards {
                flex-direction: column;
                gap: 15px;
            }

            .card {
                width: 100%;
            }

            .chart-section {
                flex-direction: column;
            }
        }

        @media (max-width: 768px) {
            .navbar .icons i {
                margin-left: 10px;
            }

            .navbar .search-bar input {
                width: 100%;
            }

            .card h2 {
                font-size: 24px;
            }

            .card h3 {
                font-size: 16px;
            }

            .sidebar a {
                font-size: 16px;
            }

            .sidebar .profile img {
                width: 60px;
            }
        }

        @media (max-width: 480px) {
            .sidebar {
                width: 80px;
            }

            .main-content {
                margin-left: 80px;
                width: calc(100% - 80px);
            }

            .sidebar a {
                font-size: 14px;
                text-align: center;
            }

            .sidebar .profile img {
                width: 50px;
            }

            .navbar {
                padding: 10px;
            }

            .dashboard-cards {
                flex-direction: column;
            }

            .card h2 {
                font-size: 20px;
            }

            .card h3 {
                font-size: 14px;
            }

            .chart {
                padding: 15px;
            }
        }

        .button-container {
    display: grid; /* Define o container como grid */
    grid-template-columns: 1fr 1fr; /* Dois botões por linha */
    grid-gap: 30px 200px; /* Espaçamento entre os botões */
    max-width: 400px; /* Largura máxima opcional para alinhar os botões */
    margin: 0 auto; /* Centraliza horizontalmente */
    justify-content: center;/* Centraliza o conteúdo horizontalmente */
    align-items: center;
    margin-top: 100px;
}

/* Estilo geral dos botões */
button {
    display: flex; /* Flexbox para alinhar texto e ícone */
    justify-content: center; /* Centraliza horizontalmente */
    align-items: center; /* Centraliza verticalmente */
    border: none;
    border-radius: 5px;
    padding: 10px 15px;
    font-size: 16px;
    color: white;
    cursor: pointer;
    transition: background-color 0.3s, transform 0.2s;
    width: 100%; /* Faz o botão ocupar a largura da célula */
    margin-bottom: 100px;
    margin-top: 10px;

}

/* Estilos específicos para cada botão */
.btn-orange-light-top {
    background-color: #152b95;
    background: linear-gradient(to bottom, #c4cff3, #152b95);
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

.btn-purple-light-top {
    background-color: #800080;
    background: linear-gradient(to bottom, #ebc6ef, #a006a0);
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

.btn-green-light-top {
    background-color: #ff8800;
    background: linear-gradient(to bottom, #f4c369, #ff8800);
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
    width: 350px;
    height: 100px;
}

.btn-red-light-top {
    background-color: #29c75e;
    background: linear-gradient(to bottom, #b3ffaf, #249d4c);
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
    width: 350px;
    height: 100px;
}

/* Efeito hover */
button:hover {
    transform: translateY(-2px); /* Elevação ao passar o mouse */
}

.btn-orange-light-top:hover {
    background-color: #FF8C00;
}

.btn-purple-light-top:hover {
    background-color: #6A0DAD;
}

.btn-green-light-top:hover {
    background-color: #005500;
}

.btn-red-light-top:hover {
    background-color: #B22222;
}

/* Estilo dos ícones */
.icon {
    margin-left: 8px;
}

/* Media Queries para Responsividade */
@media (max-width: 768px) {
    .button-container {
        grid-template-columns: 1fr; /* Em telas menores, 1 botão por linha */
        max-width: 100%; /* Ocupar toda a largura da tela */
    }

    button {
        padding: 15px;
        font-size: 14px;
        border-radius: 10px;
    }

    .btn-green-light-top, .btn-red-light-top {
        width: 100%;
        height: auto;
    }
}

@media (max-width: 480px) {
    button {
        padding: 10px;
        font-size: 12px; /* Tamanho menor para telas muito pequenas */
    }
    
    .icon {
        margin-left: 4px; /* Menor margem entre o ícone e o texto */
    }
}

/* Botão Cadastros */
#cadastros-btn {
    position: relative;
    cursor: pointer;
}

.icon-arrow {
    margin-left: auto; /* Alinha a seta à direita */
    transition: transform 0.3s ease;
}

/* Menu suspenso */
.dropdown-menu {
    display: none; /* Menu oculto inicialmente */
    flex-direction: column;
    background-color: #323c57;
    border: px solid #323c57;
    border-radius: 5px;
    position: relative;
    width: 100%;
    z-index: 1;
    margin-top: -1px; /* Para eliminar o gap entre o botão e o menu */
}

.dropdown-menu a {
    display: block;
    padding: 10px;
    color: #ffffff;
    text-decoration: none;
    border-bottom: 1px solid #ddd;
}

.dropdown-menu a:hover {
    background-color: #f4f4f4;
}

/* Girar a seta quando o menu estiver visível */
.rotate {
    transform: rotate(180deg);
}

/* Exibe o menu suspenso quando a classe show for aplicada */
.show {
    display: flex;
}

/* RESPONSIVIDADE */

/* Telas pequenas (smartphones) */
@media (max-width: 480px) {
    .menu-container {
        padding: 10px;
    }

    .menu-container a {
        padding: 12px;
        font-size: 16px;
    }

    /* Reduz o tamanho dos ícones em telas menores */
    .menu-container i {
        font-size: 16px;
    }

    .dropdown-menu a {
        font-size: 14px;
    }
}

/* Telas médias (tablets) */
@media (max-width: 768px) {
    .menu-container {
        padding: 15px;
    }

    .menu-container a {
        padding: 15px;
        font-size: 17px;
    }

    /* Ícones médios */
    .menu-container i {
        font-size: 17px;
    }

    .dropdown-menu a {
        font-size: 15px;
    }
}

/* Telas grandes (desktops) */
@media (min-width: 769px) {
    .menu-container {
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
    }

    .menu-container a {
        font-size: 18px;
        width: auto; /* Restringe o tamanho do link ao conteúdo */
    }
}

/* Estilo do contêiner de conteúdo */
#content-container {
            padding: 20px;
            border: 1px solid #ddd;
            margin-top: 20px;
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .menu-container {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="profile">
            <img src="img/lee.jpg" alt="Foto de perfil">
            <h4>Funcionário</h4>
        </div>
        <a href="#"> Início</a>
        <a href="#"> Lista de Espera</a>

        <!-- Botão Cadastros com ícone de seta -->
    <a href="#" id="cadastros-btn">
        Cadastros
        <i class="fas fa-chevron-down icon-arrow"></i>
    </a>

    <!-- Menu suspenso para Cadastros -->
    <div class="dropdown-menu" id="cadastros-menu">
        <a href="aluno.php"> Aluno</a>
        <a href="funcionario2.php"> Funcionário</a>
        <a href="curso.php"> Curso</a>
        <a href="turma.php"> Turma</a>
    </div>

        <a href="#"> Relatórios Gerenciais</a>
        <a href="#"> Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Navbar -->
        <div class="navbar">
            <div class="title">
                <h2>Administração</h2>
            </div>
            <div class="search-bar">
                <input type="text" placeholder="Search...">
                <i class="fas fa-search"></i>
            </div>
            <div class="icons">
                <i class="fas fa-user-circle"></i>
            </div>
        </div>

        <div class="button-container"> 
            <button class="btn-orange-light-top">Início 
                <i class="fas fa-home icon"></i> <!-- Ícone para 'Início' -->
            </button> 
            <button class="btn-purple-light-top">Cadastros 
                <i class="fas fa-plus-circle icon"></i> <!-- Ícone para 'Cadastros' -->
            </button>
            <button class="btn-green-light-top">Lista de Espera 
                <i class="fas fa-clock icon"></i> <!-- Ícone para 'Lista de Espera' -->
            </button>
            <button class="btn-red-light-top">Relatórios Gerenciais 
                <i class="fas fa-chart-bar icon"></i> <!-- Ícone para 'Relatórios Gerenciais' -->
            </button> 
        </div>

        <!-- Div onde o conteúdo será carregado -->
    <div id="content-container">
    </div>

        <script>

document.getElementById('cadastros-btn').addEventListener('click', function (e) {
    e.preventDefault(); // Evita o comportamento padrão do link

    // Alterna a exibição do menu suspenso
    document.getElementById('cadastros-menu').classList.toggle('show');

    // Alterna a rotação da seta
    document.querySelector('.icon-arrow').classList.toggle('rotate');
});

document.addEventListener('DOMContentLoaded', function() {
            // Função para carregar uma página usando AJAX
            function loadPage(url) {
                var xhr = new XMLHttpRequest();
                xhr.open('GET', url, true);
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        document.getElementById('content-container').innerHTML = xhr.responseText;
                    }
                };
                xhr.send();
            }

            // Captura todos os links com a classe ajax-link
            var links = document.querySelectorAll('.ajax-link');
            links.forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault(); // Evita o redirecionamento padrão
                    var pageUrl = this.getAttribute('href'); // Obtém a URL da página
                    loadPage(pageUrl); // Chama a função para carregar a página
                });
            });
        });
    </script>
</body>
</html>

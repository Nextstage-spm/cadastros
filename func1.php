<?php
include("config.php"); // Inclui a conexão com o banco de dados
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página do Funcionário</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Font Awesome para ícones -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Open-sans', sans-serif;
            background-color: #e4edfa6c;
        }

        /* Header fixa */
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: #ffffff;
            height: 60px;
            border-bottom: 1px solid #ddd;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        .logo img {
            max-width: 105px;
            height: 55px;
        }

        .menu {
            display: flex;
            gap: 70px;
            margin-left: auto; /* Alinha o menu à direita */

        }

        .menu a {
            font-family:  "Open Sans", sans-serif;
            color: rgb(129, 121, 121); /* Cor do texto */
            text-decoration: none;
            font-size: 16px;
        }

        /* Botão de usuário logado e menu suspenso */
        .user-menu {
            position: relative;
        }

        .user-button {
            background-color: #06357b;
            color: #ffffff;
            padding: 8px 15px;
            border: none;
            border-radius: 40px;
            cursor: pointer;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 5px;
            margin-left: 30px;
        }

        .user-button i {
            font-size: 18px;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background-color: #ffffff;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 4px;
            min-width: 180px;
            z-index: 1000;
        }

        .dropdown-menu a {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: #333;
            font-size: 14px;
        }

        .dropdown-menu a:hover {
            background-color: #f4f4f9;
        }

        .user-menu:hover .dropdown-menu {
            display: block;
        }

        /* Sidebar fixa */
        .sidebar {
            position: fixed;
            top: 60px; /* Ajusta para abaixo do header */
            height: calc(100vh - 60px);
            width: 255px;
            background: #06357b;
            color: #ffffff;
            overflow-y: auto;
            padding-top: 20px;
        }

        .sidebar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background: #04315e;
        }

        .sidebar-header h3 {
            margin: 0;
            font-size: 1.5em;
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
            margin: 0;
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
            font-size: 15px;
        }

        .nav a:hover, .nav a.active {
            background: #6da3be3d;
            border-radius: 20px 5px 45px 20px;
        }

        .dropdown {
            display: none;
            background: #06357b;
            padding-left: 20px;
        }

        .dropdown-toggle:hover + .dropdown, .dropdown:hover {
            display: block;
        }

        /* Área de conteúdo */
        #conteudo {
            margin-left: 255px;
            margin-top: 60px;
            padding: 20px;
            width: calc(100% - 255px);
            min-height: calc(100vh - 60px);
            background-color: #ffffff;
        }

        h3{
            margin-left: auto;
            margin-right: auto;
            display: block;
        }

    </style>
</head>
<body>
    <!-- Header -->
    <header class="header-container">
        <div class="logo">
            <img src="./img/Slide S.P.M. (13).png" alt="Logotipo">
        </div>
        <div class="menu" id="menu">
            <a href="func.php">Início</a>
            <a href="#">Lista</a>
            <a href="#">Cadastros</a>
            <a href="#">Relatórios</a>
        </div>
        <div class="user-menu">
            <button class="user-button"><i class="fas fa-user"></i></button>
            <div class="dropdown-menu">
                <a href="#">Perfil</a>
                <a href="#" class="carregar-conteudo" data-url="3.php">Funcionário</a>
                <a href="#" class="carregar-conteudo" data-url="Instituicao.php">Instituição</a>
            </div>
        </div>
    </header>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h3>Menu</h3>
        </div>
        <ul class="nav">
            <li><a href="#" class="active"><i class="fas fa-home"></i> <span> Início</span></a></li>
            <li><a href="#"><i class="fas fa-clock icon"></i> <span>Lista de Espera</span></a></li>
            <li>
                <a href="javascript:void(0)" class="dropdown-toggle"><i class="fas fa-user-edit"></i> <span>Cadastros</span> <i class="fas fa-chevron-down"></i></a>
                <ul class="dropdown">
                <li><a href="#" class="carregar-conteudo" data-url="turma.php"><i class="fas fa-pencil-alt"></i> Turma</a></li>
                <li><a href="#" class="carregar-conteudo" data-url="curso1.php"><i class="fas fa-pencil-alt"></i> Curso</a></li>
                <li><a href="#" class="carregar-conteudo" data-url="prof.php"><i class="fas fa-pencil-alt"></i> Professor</a></li>
                <li><a href="#" class="carregar-conteudo" data-url="cad.dic.php"><i class="fas fa-pencil-alt"></i> Disciplina</a></li>
                    <li><a href="#" class="carregar-conteudo" data-url="3.1.php"><i class="fas fa-pencil-alt"></i> Aluno</a></li>
                </ul>
            </li>
            <li onclick="toggleDropdown()"> <!-- Chama a função de dropdown ao clicar -->
            <li><a href="#"><i class="fas fa-chart-bar icon"></i> <span>Relatórios Gerenciais</span></a></li>

            <ul class="submenu">
            <li><a href="relatorio1.html">Relatório 1</a></li>
            <li><a href="relatorio2.html">Relatório 2</a></li>
            <li><a href="relatorio3.html">Relatório 3</a></li>
        </ul>
            <li><a href="#"><i class="fas fa-sign-out-alt"></i> <span>Sair</span></a></li>
        </ul>
    </div>

    <!-- Conteúdo -->
    <div id="conteudo">
        <!-- Conteúdo das páginas carregadas dinamicamente -->
    </div>

    <script>
        // Carregar conteúdo dinamicamente com AJAX e manter funcionalidades
        document.querySelectorAll('.carregar-conteudo').forEach(item => {
            item.addEventListener('click', function(event) {
                event.preventDefault();
                const url = this.getAttribute('data-url');

                fetch(url)
                    .then(response => response.text())
                    .then(data => {
                        document.getElementById('conteudo').innerHTML = data;
                        ativarScriptsDinamicos();
                    })
                    .catch(error => {
                        document.getElementById('conteudo').innerHTML = '<p>Erro ao carregar o conteúdo.</p>';
                    });
            });
        });

        // Recarregar scripts para funcionalidade do conteúdo carregado
        function ativarScriptsDinamicos() {
            const scripts = document.getElementById('conteudo').getElementsByTagName('script');
            for (let script of scripts) {
                const novoScript = document.createElement('script');
                novoScript.textContent = script.textContent;
                document.body.appendChild(novoScript);
                document.body.removeChild(novoScript);
            }
            
            // Ativa o evento de submit do formulário carregado dinamicamente
            const formulario = document.querySelector('#conteudo form');
            if (formulario) {
                formulario.addEventListener('submit', function(event) {
                    event.preventDefault();
                    const formData = new FormData(formulario);
                    
                    fetch(formulario.action, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(data => {
                        document.getElementById('conteudo').innerHTML = data;
                        ativarScriptsDinamicos(); // Reativa os scripts
                    })
                    .catch(error => {
                        console.error('Erro ao enviar o formulário:', error);
                    });
                });
            }
        }









        document.addEventListener("DOMContentLoaded", function() {
    adicionarEventos(); // Adiciona os eventos ao carregar a página
});

function adicionarEventos() {
    document.querySelectorAll('.carregar-conteudo').forEach(item => {
        item.addEventListener('click', function(event) {
            event.preventDefault();
            const url = this.getAttribute('data-url');

            fetch(url)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('conteudo').innerHTML = data;
                    ativarScriptsDinamicos();
                })
                .catch(error => {
                    document.getElementById('conteudo').innerHTML = '<p>Erro ao carregar o conteúdo.</p>';
                });
        });
    });
}

function ativarScriptsDinamicos() {
    const scripts = document.getElementById('conteudo').getElementsByTagName('script');
    for (let script of scripts) {
        const novoScript = document.createElement('script');
        novoScript.textContent = script.textContent;
        document.body.appendChild(novoScript);
        document.body.removeChild(novoScript); // Remove o script após a execução
    }
}








        function toggleDropdown() {
        // Obtém o submenu
        var submenu = document.querySelector(".submenu");

        // Alterna a exibição do submenu
        submenu.style.display = submenu.style.display === "block" ? "none" : "block";
    }

    // Fecha o submenu ao clicar fora dele
    document.addEventListener("click", function(event) {
        var isClickInside = event.target.closest(".menu > li");
        var submenu = document.querySelector(".submenu");

        if (!isClickInside && submenu) {
            submenu.style.display = "none";
        }
    });
    </script>
</body>
</html>


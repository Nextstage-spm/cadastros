<?php
include("config.php");
session_start();

$mensagem = "";
$tipo_alerta = "";

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id']) && !empty($_POST['id'])) { // Atualização de aluno
        $id = $_POST['id'];
        $nome = $_POST['nome'] ?? '';
        $email = $_POST['email'] ?? '';
        $telefone = $_POST['telefone'] ?? '';
        $data_nascimento = $_POST['data_nascimento'] ?? '';
        $cpf = $_POST['cpf'] ?? '';

        try {
            $updateQuery = $conn->prepare("UPDATE aluno_cadastro SET nome = :nome, email = :email, telefone = :telefone, data_nascimento = :data_nascimento, cpf = :cpf WHERE id = :id");
            $updateQuery->bindParam(':nome', $nome);
            $updateQuery->bindParam(':email', $email);
            $updateQuery->bindParam(':telefone', $telefone);
            $updateQuery->bindParam(':data_nascimento', $data_nascimento);
            $updateQuery->bindParam(':cpf', $cpf);
            $updateQuery->bindParam(':id', $id, PDO::PARAM_INT);

            if ($updateQuery->execute()) {
                $mensagem = "Aluno atualizado com sucesso!";
                $tipo_alerta = "success";
            } else {
                $mensagem = "Erro ao atualizar o aluno.";
                $tipo_alerta = "error";
            }
        } catch (PDOException $e) {
            $mensagem = "Erro ao atualizar o aluno: " . $e->getMessage();
            $tipo_alerta = "error";
        }
    } elseif (!empty($_POST['nome']) && !empty($_POST['email']) && !empty($_POST['telefone']) && !empty($_POST['data_nascimento']) && !empty($_POST['cpf'])) {
        // Cadastro de novo aluno
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $telefone = $_POST['telefone'];
        $data_nascimento = $_POST['data_nascimento'];
        $cpf = $_POST['cpf'];

        try {
            $query = $conn->prepare("INSERT INTO aluno_cadastro (nome, email, telefone, data_nascimento, cpf) VALUES (:nome, :email, :telefone, :data_nascimento, :cpf)");
            $query->bindParam(':nome', $nome);
            $query->bindParam(':email', $email);
            $query->bindParam(':telefone', $telefone);
            $query->bindParam(':data_nascimento', $data_nascimento);
            $query->bindParam(':cpf', $cpf);

            if ($query->execute()) {
                $mensagem = "Cadastro realizado com sucesso!";
                $tipo_alerta = "success";
                $chave_acesso = "https://seusite.com/acesso/" . md5(uniqid($email, true));
            } else {
                $mensagem = "Erro ao cadastrar o aluno.";
                $tipo_alerta = "error";
            }
        } catch (PDOException $e) {
            $mensagem = "Erro ao cadastrar o aluno: " . $e->getMessage();
            $tipo_alerta = "error";
        }
    } elseif (isset($_POST['delete_id']) && !empty($_POST['delete_id'])) { // Exclusão de aluno
        $id = $_POST['delete_id'];
        try {
            $deleteQuery = $conn->prepare("DELETE FROM aluno_cadastro WHERE id = :id");
            $deleteQuery->bindParam(':id', $id, PDO::PARAM_INT);
            if ($deleteQuery->execute()) {
                $mensagem = "Aluno excluído com sucesso!";
                $tipo_alerta = "success";
            } else {
                $mensagem = "Erro ao excluir o aluno.";
                $tipo_alerta = "error";
            }
        } catch (PDOException $e) {
            $mensagem = "Erro ao excluir o aluno: " . $e->getMessage();
            $tipo_alerta = "error";
        }
    }
}

// Recuperação dos dados dos alunos
try {
    $sql = "SELECT id, nome, email, telefone, data_nascimento, cpf FROM aluno_cadastro";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erro ao recuperar os dados: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar Fixa com Carregamento Dinâmico</title>
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
            max-width: 110px;
            height: 60px;
            margin-top: 3px;
        }

        .menu {
            display: flex;
            gap: 70px;
            margin-left: auto;
        }

        .menu a {
            font-family:  "Open Sans", sans-serif;
            color: rgb(129, 121, 121);
            text-decoration: none;
            font-size: 16px;
        }

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
            margin-left: 40px;
            height: 45px;
            width: 45px;
        }

        .user-button i {
            font-size: 19px;
            margin-left: -1px;
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
            top: 60px;
            height: calc(100vh - 60px);
            width: 255px;
            background-color: #06357b;
            color: #ffffff;
            overflow-y: auto;
            padding-top: 20px;
        }

        .sidebar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background-color: #06357b;
        }

        .sidebar-header h3 {
            margin: 0;
            font-size: 1.5em;
            text-align: center;
            background-color: #06357b;
        }

        .nav {
            list-style: none;
            padding: 0;
            margin: 0;
            background-color: #06357b;
        }

        .nav li {
            width: 100%;
            background-color: #06357b;
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

        hr {
            font-style: normal;
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
                <a href="#" class="carregar-conteudo" data-url="int.php">Instituição</a>
                <a href="inicio.php">Sair</a>
            </div>
        </div>
    </header>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h3>Menu</h3>
        </div>
        <ul class="nav">
            <li><a href="func4.php" class="active"><i class="fas fa-home"></i> <span> Início</span></a></li>
            <li><a href="#"><i class="fas fa-clock icon"></i> <span>Lista de Espera</span></a></li>
            <li>
            <hr>
                    <a href="func.php" class="carregar-conteudo" data-url="turma.php"></i> Turma</a></li>
                    
                    <a href="" class="carregar-conteudo" data-url="curso1.php"></i> Curso</a></li>
                    <a href="prof.php" class="carregar-conteudo" data-url="prof.php"></i> Professor</a></li>
                    <a href="#" class="carregar-conteudo" data-url="cad.dic.php"></i> Disciplina</a></li>
                    <a href="3.2.php" class="carregar-conteudo" data-url="3.2.php"> Aluno</a></li>
    </li>
    <hr>
            <li><a href="#"  class="carregar-conteudo" data-url="relatorio.php" data-url="relatorio.func.php"> <i class="fas fa-chart-bar icon"></i> <span>Relatórios Gerenciais</span></a></li>
        </ul>''
    </div>

    <!-- Conteúdo -->
    <div id="conteudo">
        <!-- Conteúdo das páginas carregadas dinamicamente -->
    </div>

    <script>
        
document.addEventListener("DOMContentLoaded", function() {
    adicionarEventos(); // Adiciona eventos ao carregar a página
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
        document.body.removeChild(novoScript);
    }

    const formulario = document.querySelector('#conteudo form');
    if (formulario) {
        formulario.addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(formulario);

            fetch(formulario.action, {
                method: formulario.method,
                body: formData
            })
            .then(response => response.text())
            .then(responseData => {
                document.getElementById('conteudo').innerHTML = responseData;
                ativarScriptsDinamicos();
            })
            .catch(error => console.log('Erro ao enviar o formulário:', error));
        });
    }
}

</script>

</body> </html>






<li><a href="#"  class="carregar-conteudo" data-url="relatorio.php"> <i class="fas fa-chart-bar icon"></i> <span>Relatórios Gerenciais</span></a></li>
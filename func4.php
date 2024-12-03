<?php
include("config.php"); 

session_start();

$mensagem = "";
$tipo_alerta = "";

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id'])) { // Atualização de aluno
        $id = $_POST['id'];
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $telefone = $_POST['telefone'];
        $data_nascimento = $_POST['data_nascimento'];
        $cpf = $_POST['cpf'];

        try {
            $updateQuery = $conn->prepare("UPDATE aluno_cadastro SET nome = :nome, email = :email, telefone = :telefone, data_nascimento = :data_nascimento, cpf = :cpf WHERE id = :id");
            $updateQuery->bindParam(':nome', $nome);
            $updateQuery->bindParam(':email', $email);
            $updateQuery->bindParam(':telefone', $telefone);
            $updateQuery->bindParam(':data_nascimento', $data_nascimento);
            $updateQuery->bindParam(':cpf', $cpf);
            $updateQuery->bindParam(':id', $id);

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
    } elseif (isset($_POST['nome']) && isset($_POST['email']) && isset($_POST['telefone']) && isset($_POST['data_nascimento']) && isset($_POST['cpf'])) {
        // Cadastro de novo aluno
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $telefone = $_POST['telefone'];
        $data_nascimento = $_POST['data_nascimento'];
        $cpf = $_POST['cpf'];

        try {
            $query = $conn->prepare("INSERT INTO aluno_cadastro (nome, email, telefone, data_nascimento, cpf) 
                                     VALUES (:nome, :email, :telefone, :data_nascimento, :cpf)");
            $query->bindParam(':nome', $nome);
            $query->bindParam(':email', $email);
            $query->bindParam(':telefone', $telefone);
            $query->bindParam(':data_nascimento', $data_nascimento);
            $query->bindParam(':cpf', $cpf);

            if ($query->execute()) {
                $mensagem = "Cadastro realizado com sucesso!";
                $tipo_alerta = "success";

                // Gerar a chave de acesso automaticamente
                $chave_acesso = "https://seusite.com/acesso/" . md5(uniqid($email, true));
            } else {
                $mensagem = "Erro ao cadastrar o aluno.";
                $tipo_alerta = "error";
            }
        } catch (PDOException $e) {
            $mensagem = "Erro ao cadastrar o aluno: " . $e->getMessage();
            $tipo_alerta = "error";
        }
    } elseif (isset($_POST['delete_id'])) { // Exclusão de aluno
        $id = $_POST['delete_id'];
        try {
            $deleteQuery = $conn->prepare("DELETE FROM aluno_cadastro WHERE id = :id");
            $deleteQuery->bindParam(':id', $id);
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
$sql = "SELECT id, nome, email, telefone, data_nascimento, cpf FROM aluno_cadastro";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Cadastro e atualização de funcionário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update'])) {
        // Atualização do funcionário
        if (!empty($_POST['nome']) && !empty($_POST['email']) && !empty($_POST['telefone']) &&
            !empty($_POST['data_nascimento']) && !empty($_POST['matricula']) && !empty($_POST['cargo'])) {

            $id = $_POST['id'];
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $telefone = $_POST['telefone'];
            $data_nascimento = $_POST['data_nascimento'];
            $matricula = $_POST['matricula'];
            $cargo = $_POST['cargo'];

            if (!empty($_POST['senha']) && $_POST['senha'] === $_POST['confirma_senha']) {
                $senha_hash = password_hash($_POST['senha'], PASSWORD_DEFAULT);
                $query = $conn->prepare("UPDATE funcionarios SET nome=:nome, email=:email, telefone=:telefone, data_nascimento=:data_nascimento, cargo=:cargo, matricula=:matricula, senha=:senha WHERE id=:id");
                $query->bindParam(':senha', $senha_hash);
            } else {
                $query = $conn->prepare("UPDATE funcionarios SET nome=:nome, email=:email, telefone=:telefone, data_nascimento=:data_nascimento, cargo=:cargo, matricula=:matricula WHERE id=:id");
            }

            // Vincula os parâmetros da query
            $query->bindParam(':id', $id);
            $query->bindParam(':nome', $nome);
            $query->bindParam(':email', $email);
            $query->bindParam(':telefone', $telefone);
            $query->bindParam(':data_nascimento', $data_nascimento);
            $query->bindParam(':matricula', $matricula);
            $query->bindParam(':cargo', $cargo);

            if ($query->execute()) {
                $mensagem = "Funcionário atualizado com sucesso!";
                $tipo_alerta = "success";
            } else {
                $mensagem = "Erro ao atualizar o funcionário.";
                $tipo_alerta = "error";
            }
        } else {
            $mensagem = "Por favor, preencha todos os campos.";
            $tipo_alerta = "error";
        }
    } elseif (isset($_POST['delete_id'])) {
        // Exclusão do funcionário
        $delete_id = $_POST['delete_id'];
        $query = $conn->prepare("DELETE FROM funcionarios WHERE id = :delete_id");
        $query->bindParam(':delete_id', $delete_id);

        if ($query->execute()) {
            $mensagem = "Funcionário excluído com sucesso!";
            $tipo_alerta = "success";
        } else {
            $mensagem = "Erro ao excluir o funcionário.";
            $tipo_alerta = "error";
        }
    } else {
        // Cadastro do funcionário
        if (!empty($_POST['nome']) && !empty($_POST['senha']) && !empty($_POST['confirma_senha']) &&
            !empty($_POST['email']) && !empty($_POST['telefone']) && !empty($_POST['data_nascimento']) &&
            !empty($_POST['matricula']) && !empty($_POST['cargo'])) {

            $nome = $_POST['nome'];
            $senha = $_POST['senha'];
            $cargo = $_POST['cargo'];
            $matricula = $_POST['matricula'];
            $confirma_senha = $_POST['confirma_senha'];
            $email = $_POST['email'];
            $telefone = $_POST['telefone'];
            $data_nascimento = $_POST['data_nascimento'];

            if ($senha === $confirma_senha) {
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

                try {
                    $query = $conn->prepare("INSERT INTO funcionarios (nome, senha, cargo, matricula, email, telefone, data_nascimento) 
                                             VALUES (:nome, :senha, :cargo, :matricula, :email, :telefone, :data_nascimento)");

                    $query->bindParam(':nome', $nome);
                    $query->bindParam(':senha', $senha_hash);
                    $query->bindParam(':email', $email);
                    $query->bindParam(':telefone', $telefone);
                    $query->bindParam(':data_nascimento', $data_nascimento);
                    $query->bindParam(':matricula', $matricula);
                    $query->bindParam(':cargo', $cargo);

                    if ($query->execute()) {
                        $mensagem = "Cadastro realizado com sucesso!";
                        $tipo_alerta = "success";
                    } else {
                        $mensagem = "Erro ao cadastrar o funcionário.";
                        $tipo_alerta = "error";
                    }
                } catch (PDOException $e) {
                    $mensagem = "Erro ao cadastrar o funcionário: " . $e->getMessage();
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
}

// Consulta para buscar os registros
$sql = "SELECT id, nome, email, cargo, matricula, telefone, data_nascimento FROM funcionarios";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);


$mensagem = ""; // 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_id'])) {
        // Lógica para exclusão
        $delete_id = $_POST['delete_id'];
        $query = $conn->prepare("DELETE FROM disciplina WHERE id = :id");
        $query->bindParam(':id', $delete_id);

        if ($query->execute()) {
            $mensagem = "Disciplina excluída com sucesso";
        } else {
            $mensagem = "Erro ao excluir a disciplina";
        }
    } elseif (isset($_POST['update'])) {
        // Lógica para atualização
        $id = $_POST['id'];
        $nome = $_POST['nome'];
        $professor_codd = $_POST['professor'];

        $query = $conn->prepare("UPDATE disciplina SET nome = :nome, professor = :professor WHERE id = :id");
        $query->bindParam(':id', $id);
        $query->bindParam(':nome', $nome);
        $query->bindParam(':professor', $professor_codd);

        if ($query->execute()) {
            $mensagem = "Disciplina atualizada com sucesso";
        } else {
            $mensagem = "Erro ao atualizar a disciplina.";
        }
    } elseif (!empty($_POST['nome']) && !empty($_POST['professor'])) {
        // Lógica para cadastro
        $nome = $_POST['nome'];
        $professor_codd = $_POST['professor'];

        try {
            $query = $conn->prepare("SELECT COUNT(*) FROM disciplina WHERE nome = :nome AND professor = :professor");
            $query->bindParam(':nome', $nome);
            $query->bindParam(':professor', $professor_codd);
            $query->execute();
            $count = $query->fetchColumn();

            if ($count > 0) {
                $mensagem = "Disciplina já cadastrada";
            } else {
                $query = $conn->prepare("INSERT INTO disciplina (nome, professor) VALUES (:nome, :professor)");
                $query->bindParam(':nome', $nome);
                $query->bindParam(':professor', $professor_codd);

                if ($query->execute()) {
                    $mensagem = "Disciplina cadastrada com sucesso";
                } else {
                    $mensagem = "Erro ao cadastrar disciplina.";
                }
            }
        } catch (PDOException $e) {
            $mensagem = "Erro ao cadastrar disciplina: " . $e->getMessage();
        }
    } else {
        $mensagem = "Preencha todos os campos";
    }
}

// Consulta para buscar os registros
$sql = "SELECT disciplina.id, disciplina.nome, professor.nome AS professor FROM disciplina INNER JOIN professor ON disciplina.professor = professor.id";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);


$mensagem = "";
$tipo_alerta = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Cadastrar novo professor
    if (isset($_POST['nome']) && !empty($_POST['nome']) && isset($_POST['titulacao_id']) && !empty($_POST['titulacao_id'])
    && isset($_POST['email']) && !empty($_POST['email']) && isset($_POST['telefone']) && !empty($_POST['telefone']) && !isset($_POST['update'])) {
        $nome_professor = $_POST['nome'];
        $email_professor = $_POST['email'];
        $telefone_professor = $_POST['telefone'];
        $titulacao_id = $_POST['titulacao_id']; 

        try {
            $query = $conn->prepare("INSERT INTO professor (nome, email, telefone, titulacao_id) VALUES (:nome, :email, :telefone, :titulacao_id)"); 
            $query->bindParam(':nome', $nome_professor);
            $query->bindParam(':email', $email_professor);
            $query->bindParam(':telefone', $telefone_professor);
            $query->bindParam(':titulacao_id', $titulacao_id); 

            if ($query->execute()) {
                $mensagem = "Cadastro realizado com sucesso!";
                $tipo_alerta = "success";
            } else {
                $mensagem = "Erro ao cadastrar o professor.";
                $tipo_alerta = "error";
            }
        } catch (PDOException $e) {
            $mensagem = "Erro ao cadastrar o professor: " . $e->getMessage();
            $tipo_alerta = "error";
        }
    }

    // Atualizar professor
    if (isset($_POST['update'])) {
        $id = $_POST['id'];
        
        $nome_professor = $_POST['nome'];
        $email_professor = $_POST['email'];
        $telefone_professor = $_POST['telefone'];
        $titulacao_id = $_POST['titulacao_id'];

        try {
            $query = $conn->prepare("UPDATE professor SET nome = :nome, email = :email, telefone = :telefone, titulacao_id = :titulacao_id WHERE id = :id");
            $query->bindParam(':nome', $nome_professor);
            $query->bindParam(':email', $email_professor);
            $query->bindParam(':telefone', $telefone_professor);
            $query->bindParam(':titulacao_id', $titulacao_id);
            $query->bindParam(':id', $id);

            if ($query->execute()) {
                $mensagem = "Atualização realizada com sucesso!";
                $tipo_alerta = "success";
            } else {
                $mensagem = "Erro ao atualizar o professor.";
                $tipo_alerta = "error";
            }
        } catch (PDOException $e) {
            $mensagem = "Erro ao atualizar o professor: " . $e->getMessage();
            $tipo_alerta = "error";
        }
    }

    // Excluir professor
    if (isset($_POST['delete_id'])) {
        $id = $_POST['delete_id'];
        
        try {
            $query = $conn->prepare("DELETE FROM professor WHERE id = :id");
            $query->bindParam(':id', $id);

            if ($query->execute()) {
                $mensagem = "Professor excluído com sucesso!";
                $tipo_alerta = "success";
            } else {
                $mensagem = "Erro ao excluir o professor.";
                $tipo_alerta = "error";
            }
        } catch (PDOException $e) {
            $mensagem = "Erro ao excluir o professor: " . $e->getMessage();
            $tipo_alerta = "error";
        }
    }
}

$sql = "SELECT id, nome, email, telefone, titulacao_id FROM professor";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_id'])) {
        // Lógica para exclusão
        $delete_id = $_POST['delete_id'];
        $query = $conn->prepare("DELETE FROM cursos WHERE id = :id");
        $query->bindParam(':id', $delete_id);

        if ($query->execute()) {
            $mensagem = "Curso excluído com sucesso";
        } else {
            $mensagem = "Erro ao excluir o curso";
        }
    } elseif (isset($_POST['update'])) {
        // Lógica para atualização
        $id = $_POST['id'];
        $nome = $_POST['nome'];
        $descricao = $_POST['descricao'];

        $query = $conn->prepare("UPDATE cursos SET nome = :nome, descricao = :descricao WHERE id = :id");
        $query->bindParam(':id', $id);
        $query->bindParam(':nome', $nome);
        $query->bindParam(':descricao', $descricao);

        if ($query->execute()) {
            $mensagem = "Curso atualizado com sucesso";
        } else {
            $mensagem = "Erro ao atualizar o curso";
        }
    } elseif (!empty($_POST['nome']) && !empty($_POST['descricao'])) {
        // Lógica para cadastro
        $nome = $_POST['nome'];
        $descricao = $_POST['descricao'];

        try {
            $query = $conn->prepare("SELECT COUNT(*) FROM cursos WHERE nome = :nome AND descricao = :descricao");
            $query->bindParam(':nome', $nome);
            $query->bindParam(':descricao', $descricao);
            $query->execute();
            $count = $query->fetchColumn();

            if ($count > 0) {
                $mensagem = "Curso já cadastrado";
            } else {
                $query = $conn->prepare("INSERT INTO cursos (nome, descricao) VALUES (:nome, :descricao)");
                $query->bindParam(':nome', $nome);
                $query->bindParam(':descricao', $descricao);

                if ($query->execute()) {
                    $mensagem = "Curso cadastrado com sucesso";
                } else {
                    $mensagem = "Erro ao cadastrar curso";
                }
            }
        } catch (PDOException $e) {
            $mensagem = "Erro ao cadastrar curso";
        }
    } else {
        $mensagem = "Preencha todos os campos";
    }
}

// Consulta para buscar os registros
$sql = "SELECT id, nome, descricao FROM cursos";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página do Funcioário</title>
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


        .nav hr {
            border: 0;
            border-top: 1px solid #5aa2b0;
            margin: 10px 0;
        }

        /* Estilo do dropdown */
        .dropdown {
            position: relative;
        }
        .dropdown-toggle {
            cursor: pointer;
            display: flex;
            align-items: center;
        }
        .dropdown-content {
            display: none;
            background-color: #06357b;
            padding-left: 20px;
            margin-top: 5px;
        }
        .dropdown-content a {
            color: #fff;
            padding: 15px 0;
            display: block;
        }
        .dropdown-content a:hover {
            color: #5aa2b0;
        }
        .show-dropdown .dropdown-content {
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
            <li><a href="func4.php" class=""><i class="fas fa-home" style="margin-right: 15px;"> </i> <span> Início</span></a></li>
            <li><a href="#"><i class="fas fa-clock icon" style="margin-right: 15px;"></i> <span>Lista de Espera</span></a></li>
            <li>
            <hr>
                    <a href="func.php" class="carregar-conteudo" data-url="turma.php"></i> Turma</a></li>
                    
                    <a href="" class="carregar-conteudo" data-url="curso1.php"></i> Curso</a></li>
                    <a href="prof.php" class="carregar-conteudo" data-url="prof.php"></i> Professor</a></li>
                    <a href="#" class="carregar-conteudo" data-url="cad.dic.php"></i> Disciplina</a></li>
                    <a href="3.2.php" class="carregar-conteudo" data-url="3.2.php"> Aluno</a></li>
    </li>

    <hr>
                <a href="#" class="dropdown-toggle" onclick="toggleDropdown(event)">
                    <i class="fas fa-chart-bar icon" style="margin-right: 15px;"></i> <span>Relatórios Gerenciais</span>
                </a>
                <div class="dropdown-content">
                    <a href="#" class="carregar-conteudo" data-url="relatorio.php">Curso</a>
                    <a href="#" class="carregar-conteudo" data-url="relat2.php">Funcionário</a>
                    <a href="#" class="carregar-conteudo" data-url="relat3.php">Instituição</a>
                    <a href="#" class="carregar-conteudo" data-url="relat3.php">Aluno</a>
                </div>
            </li>

        </ul>
    </div>

    <!-- Conteúdo -->
    <div id="conteudo">
        <!-- Conteúdo das páginas carregadas dinamicamente -->
    </div>

    <script>
        function showEditModal(curso) {
            document.getElementById("editId").value = curso.id;
            document.getElementById("editNome").value = curso.nome;
            document.getElementById("editDescricao").value = curso.descricao;
            document.getElementById("editModal").style.display = "block";
        }

        function closeModal() {
            document.getElementById("editModal").style.display = "none";
        }

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
                formulario.addEventListener('submit', function(event) { event.preventDefault(); const formData = new FormData(formulario);

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




function toggleDropdown(event) {
            event.preventDefault();
            const dropdown = event.currentTarget.parentNode;
            dropdown.classList.toggle('show-dropdown');
        }
</script>

</body> </html>
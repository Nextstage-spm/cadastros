<?php
session_start();
include("config.php");

// Função auxiliar para mensagens e redirecionamentos
function setMessageAndRedirect($mensagem, $tipo_alerta, $url = null) {
    $_SESSION['mensagem'] = $mensagem;
    $_SESSION['tipo_alerta'] = $tipo_alerta;
    if ($url) {
        header("Location: " . $url);
        exit();
    }
}

// Inicialização de variáveis
$mensagem = isset($_SESSION['mensagem']) ? $_SESSION['mensagem'] : "";
$tipo_alerta = isset($_SESSION['tipo_alerta']) ? $_SESSION['tipo_alerta'] : "";

unset($_SESSION['mensagem']);
unset($_SESSION['tipo_alerta']);

// Processamento de Alunos
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Atualização de aluno
    if (isset($_POST['id'])) {
        try {
            $updateQuery = $conn->prepare("UPDATE aluno_cadastro SET nome = :nome, email = :email, telefone = :telefone, data_nascimento = :data_nascimento, cpf = :cpf WHERE id = :id");
            $updateQuery->execute([
                ':nome' => filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING),
                ':email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
                ':telefone' => filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING),
                ':data_nascimento' => filter_input(INPUT_POST, 'data_nascimento', FILTER_SANITIZE_STRING),
                ':cpf' => filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_STRING),
                ':id' => filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT)
            ]);
            setMessageAndRedirect("Aluno atualizado com sucesso!", "success", "3.3.php");
        } catch (PDOException $e) {
            setMessageAndRedirect("Erro ao atualizar o aluno: " . $e->getMessage(), "error");
        }
    }
    // Cadastro de novo aluno
    elseif (!empty($_POST['nome']) && !empty($_POST['email'])) {
        try {
            $query = $conn->prepare("INSERT INTO aluno_cadastro (nome, email, telefone, data_nascimento, cpf) VALUES (:nome, :email, :telefone, :data_nascimento, :cpf)");
            $query->execute([
                ':nome' => filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING),
                ':email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
                ':telefone' => filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING),
                ':data_nascimento' => filter_input(INPUT_POST, 'data_nascimento', FILTER_SANITIZE_STRING),
                ':cpf' => filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_STRING)
            ]);
            setMessageAndRedirect("Aluno cadastrado com sucesso!", "success", "3.3.php");
        } catch (PDOException $e) {
            setMessageAndRedirect("Erro ao cadastrar o aluno: " . $e->getMessage(), "error");
        }
    }
    // Exclusão de aluno
    elseif (isset($_POST['delete_id'])) {
        try {
            $deleteQuery = $conn->prepare("DELETE FROM aluno_cadastro WHERE id = :id");
            $deleteQuery->execute([':id' => filter_input(INPUT_POST, 'delete_id', FILTER_SANITIZE_NUMBER_INT)]);
            setMessageAndRedirect("Aluno excluído com sucesso!", "success", "3.3.php");
        } catch (PDOException $e) {
            setMessageAndRedirect("Erro ao excluir o aluno: " . $e->getMessage(), "error");
        }
    }
}

// Processamento de Funcionários
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Atualização de funcionário
    if (isset($_POST['update']) && !empty($_POST['id'])) {
        try {
            $queryString = "UPDATE funcionarios SET 
                           nome = :nome, 
                           email = :email, 
                           telefone = :telefone, 
                           data_nascimento = :data_nascimento, 
                           cargo = :cargo, 
                           matricula = :matricula";

            $params = [
                ':nome' => filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING),
                ':email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
                ':telefone' => filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING),
                ':data_nascimento' => filter_input(INPUT_POST, 'data_nascimento', FILTER_SANITIZE_STRING),
                ':cargo' => filter_input(INPUT_POST, 'cargo', FILTER_SANITIZE_STRING),
                ':matricula' => filter_input(INPUT_POST, 'matricula', FILTER_SANITIZE_STRING),
                ':id' => filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT)
            ];

            if (!empty($_POST['senha']) && $_POST['senha'] === $_POST['confirma_senha']) {
                $queryString .= ", senha = :senha";
                $params[':senha'] = password_hash($_POST['senha'], PASSWORD_DEFAULT);
            }

            $queryString .= " WHERE id = :id";
            $query = $conn->prepare($queryString);
            $query->execute($params);
            
            setMessageAndRedirect("Funcionário atualizado com sucesso!", "success", "3.3.php");
        } catch (PDOException $e) {
            setMessageAndRedirect("Erro ao atualizar funcionário: " . $e->getMessage(), "error");
        }
    }
    // Exclusão de funcionário
    elseif (isset($_POST['delete_id'])) {
        try {
            $query = $conn->prepare("DELETE FROM funcionarios WHERE id = :id");
            $query->execute([':id' => filter_input(INPUT_POST, 'delete_id', FILTER_SANITIZE_NUMBER_INT)]);
            setMessageAndRedirect("Funcionário excluído com sucesso!", "success", "3.3.php");
        } catch (PDOException $e) {
            setMessageAndRedirect("Erro ao excluir funcionário: " . $e->getMessage(), "error");
        }
    }
    // Cadastro de novo funcionário
    elseif (!empty($_POST['nome']) && !empty($_POST['senha']) && !empty($_POST['confirma_senha'])) {
        if ($_POST['senha'] === $_POST['confirma_senha']) {
            try {
                $query = $conn->prepare("INSERT INTO funcionarios (nome, senha, cargo, matricula, email, telefone, data_nascimento) 
                                       VALUES (:nome, :senha, :cargo, :matricula, :email, :telefone, :data_nascimento)");
                
                $query->execute([
                    ':nome' => filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING),
                    ':senha' => password_hash($_POST['senha'], PASSWORD_DEFAULT),
                    ':cargo' => filter_input(INPUT_POST, 'cargo', FILTER_SANITIZE_STRING),
                    ':matricula' => filter_input(INPUT_POST, 'matricula', FILTER_SANITIZE_STRING),
                    ':email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
                    ':telefone' => filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING),
                    ':data_nascimento' => filter_input(INPUT_POST, 'data_nascimento', FILTER_SANITIZE_STRING)
                ]);
                
                setMessageAndRedirect("Funcionário cadastrado com sucesso!", "success", "3.3.php");
            } catch (PDOException $e) {
                setMessageAndRedirect("Erro ao cadastrar funcionário: " . $e->getMessage(), "error");
            }
        } else {
            setMessageAndRedirect("As senhas não coincidem!", "error");
        }
    }
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestão Escolar</title>
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
            background-color: #e9f2f5;
        }

        hr {
            font-style: normal;
        }


        .nav hr {
            border: 0;
            border-top: 1px solid #5aa2b0;
            align-self: center;
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

        h3 menu{
            margin-left: 200px;
        }




        .button-container {
            display: grid;
            grid-template-areas: 
                "btn1 btn2 btn3"
                ". btn4 ."
                "btn5 btn6 btn7";
            grid-gap: 20px;
            max-width: 800px;
            margin: 0 auto;
            margin-top: 120px;
            justify-content: center;
            align-items: center;
            margin-left: 220px;
        }

        /* Estilos individuais dos botões */
        a.button {
            width: 340px;
            height: 95px;
            padding: 20px;
            font-size: 18px;
            font-weight: bold;
            font-family: "Open Sans", sans-serif;
            font-style: normal;
            color: #fff;
            border: none;
            border-radius: 45px;
            cursor: pointer;
            box-shadow: 0px 20px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            display: space-between;
        }

        .button i {
            margin-left: 10px; 
            font-size: 24px;
        }

        .btn-orange-light-top {
            background: linear-gradient(to bottom, #ed9bf4, #5c1465);
            grid-area: btn1;
            margin-top: -50px;
            margin-bottom: 100px;
        }
        
        .btn-purple-light-top {
            background: linear-gradient(to bottom, #a08ef9, #1d0966);
            grid-area: btn2;
            margin-top: -50px;
            margin-bottom: 100px;
        }

        .btn-green-light-top {
            background: linear-gradient(to bottom, #afcc8d, #16581a);
            grid-area: btn3;
            margin-top: -50px;
            margin-bottom: 100px;
        }

        .btn-red-light-top {
            background: linear-gradient(to bottom, #f15d71, #b81701);
            grid-area: btn4;
            margin-top: -50px;
            margin-bottom: 100px;
        }

        .btn-orange-light-bottom {
            background: linear-gradient(to bottom, #adadad, #615d5d);
            grid-area: btn5;
            margin-top: -50px;
            margin-bottom: 100px;
        }
        
        .btn-purple-light-bottom {
            background: linear-gradient(to bottom, #57c8d0, #0a5b4f);
            grid-area: btn6;
            margin-top: -50px;
            margin-bottom: 100px;
        }

        .btn-green-light-bottom {
            background: linear-gradient(to bottom, #fa92d5, #a91a56);
            grid-area: btn7;
            margin-top: -50px;
            margin-bottom: 100px;
        }
        .seta {
            font-size: 15px;
            margin-left: 7px;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        /* Transição para a seta para cima */
        .seta.para-cima {
            transform: rotate(180deg);
        }





        .modal-logoff {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    animation: fadeIn 0.3s ease-out;
}

.modal-content-logoff {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border-radius: 5px;
    width: 400px;
    text-align: center;
    position: relative;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.modal-content-logoff h2 {
    color: #174650;
    margin-bottom: 15px;
    font-size: 1.5em;
}

.modal-content-logoff p {
    margin-bottom: 20px;
    color: #666;
}

.modal-buttons {
    display: flex;
    justify-content: center;
    gap: 10px;
}

.btn-confirmar, .btn-cancelar {
    padding: 8px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: bold;
    transition: background-color 0.3s ease;
}

.btn-confirmar {
    background-color: #174650;
    color: white;
}

.btn-confirmar:hover {
    background-color: #5aa2b0;
}

.btn-cancelar {
    background-color: #dc3545;
    color: white;
}

.btn-cancelar:hover {
    background-color: #c82333;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

    </style>
</head>
<body>


    <?php if (!empty($mensagem)): ?>
    <div class="alert alert-<?php echo $tipo_alerta; ?>">
        <?php echo $mensagem; ?>
    </div>
    <?php endif; ?>

    <header class="header-container">
        <div class="logo">
            <img src="./img/Slide S.P.M. (13).png" alt="Logotipo">
        </div>
        <div class="menu" id="menu">
            <a href="3.3.php">Início</a>
            <a href="#">Lista</a>
            <a href="#" class="carregar-conteudo" data-url="botao.cad.php">Cadastros</a>
            <a href="#" class="carregar-conteudo" data-url="botao.relatorio.html">Relatórios</a>
        </div>
        <div class="user-menu">
            <button class="user-button"><i class="fas fa-user"></i></button>
            <div class="dropdown-menu">
                <a href="#" class="carregar-conteudo" data-url="editarfuncionario.php">Perfil</a>
                <a href="#" class="carregar-conteudo" data-url="3.php">Funcionário</a>
                <a href="#" class="carregar-conteudo" data-url="int2.php">Instituição</a>
                <a href="javascript:void(0);" onclick="confirmarLogoff()">Sair</a>
            </div>
        </div>
        </header>

 <!-- Modal de Confirmação de Logoff -->
 <div id="logoffModal" class="modal-logoff">
        <div class="modal-content-logoff">
            <h2>Confirmar Saída</h2>
            <p>Tem certeza que deseja sair do sistema?</p>
            <div class="modal-buttons">
                <button onclick="realizarLogoff()" class="btn-confirmar">Confirmar</button>
                <button onclick="fecharModalLogoff()" class="btn-cancelar">Cancelar</button>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h3>Menu</h3>
        </div>
        <ul class="nav">
            <li><a href="3.3.php" class=""><i class="fas fa-home" style="margin-right: 15px;"> </i> <span> Início</span></a></li>
            <li><a href="#"><i class="fas fa-clock icon" style="margin-right: 15px;"></i> <span>Lista de Espera</span></a></li>
            <li>
            <hr>
                    <a href="func.php" class="carregar-conteudo" data-url="turma3.php"></i> Turma</a></li>
                    
                    <a href="" class="carregar-conteudo" data-url="cu.php"></i> Curso</a></li>
                    <a href="prof.php" class="carregar-conteudo" data-url="fuc.php"></i> Professor</a></li>
                    <a href="#" class="carregar-conteudo" data-url="listar_disciplinas.php"></i> Disciplina</a></li>
                    <a href="3.2.php" class="carregar-conteudo" data-url="3.2.php"> Aluno</a></li>
    </li>

    <hr>
                <a href="#" class="dropdown-toggle" onclick="toggleDropdown(event)">
                    <i class="fas fa-chart-bar icon" style="margin-right: 15px;"></i> <span>Relatórios Gerenciais</span>
                    <i class="fas fa-chevron-down seta" id="seta"></i>
                </a>
                <div class="dropdown-content">
                <a href="#" class="carregar-conteudo" data-url="relatorio.turma2.php">Turma</a>
                    <a href="#" class="carregar-conteudo" data-url="relatorio.php">Curso</a>
                    <a href="#" class="carregar-conteudo" data-url="relatorio.prof.php">Professor</a>
                    <a href="#" class="carregar-conteudo" data-url="relatorio.dic.php">Disciplina</a>
                    <a href="#" class="carregar-conteudo" data-url="relatorio.func1.php">Funcionário</a>
                    <a href="#" class="carregar-conteudo" data-url="relatorio.int.php">Instituição</a>
                    <a href="#" class="carregar-conteudo" data-url="relatorio.aluno.php">Aluno</a>
                </div>
            </li>

        </ul>
    </div>


    <!-- Conteúdo -->
    <div id="conteudo">

    <div class="button-container">
    <a href="#" data-url="#" class="button btn-orange-light-top">Lista de Espera<i class="fas fa-clock icon"></i></a>
    <a href="#" data-url="cu.php" class="carregar-conteudo button btn-purple-light-top">Curso<i class="fas fa-book-open"></i></a>
    <a href="#" data-url="fuc.php" class="carregar-conteudo button btn-green-light-top">Professor<i class="fas fa-chalkboard-teacher"></i></a>
    <a href="#" data-url="turma3.php" class="carregar-conteudo button btn-red-light-top">Turma<i class="fas fa-users"></i></a>
    <a href="#" data-url="dic.php" class="carregar-conteudo button btn-orange-light-bottom">Disciplina<i class="fas fa-book"></i></a>
    <a href="#" data-url="3.2.php" class="carregar-conteudo button btn-purple-light-bottom">Aluno<i class="fas fa-user-graduate"></i></a>
    <a href="#" class="button btn-green-light-bottom">Relatórios Gerenciais<i class="fas fa-chart-bar icon"></i></a>
</div>
    
    </div>

    <script>
   document.getElementById('seta').addEventListener('click', function() {
            // Alterna a classe "para-cima", que irá girar a seta
            this.classList.toggle('para-cima');
        });

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







        function confirmarLogoff() {
            document.getElementById('logoffModal').style.display = 'block';
        }

        // Função para fechar o modal
        function fecharModalLogoff() {
            document.getElementById('logoffModal').style.display = 'none';
        }

        // Função para realizar o logoff
        function realizarLogoff() {
            window.location.href = 'index.php';
        }

        // Controle do menu dropdown
        document.querySelector('.user-button').addEventListener('click', function(e) {
            e.stopPropagation();
            const dropdownMenu = document.querySelector('.dropdown-menu');
            dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
        });

        // Fechar menu dropdown e modal ao clicar fora
        window.addEventListener('click', function(e) {
            const dropdownMenu = document.querySelector('.dropdown-menu');
            const modal = document.getElementById('logoffModal');
            
            if (!e.target.matches('.user-button') && !e.target.matches('.fa-user')) {
                if (dropdownMenu.style.display === 'block') {
                    dropdownMenu.style.display = 'none';
                }
            }

            if (e.target === modal) {
                fecharModalLogoff();
            }
        })
    </script>

</body>
</html>
<?php ob_end_flush(); ?>
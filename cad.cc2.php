<?php
include("config.php"); // Inclui a conexão com o banco de dados

$mensagem = ""; // Variável para armazenar a mensagem de erro ou sucesso

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verifica se os campos 'nome' e 'descricao' foram enviados e não estão vazios
    if (!empty($_POST['nome']) && !empty($_POST['descricao'])) {
        // Captura os valores do formulário
        $nome = $_POST['nome'];
        $descricao = $_POST['descricao'];

        try {
            // Verifica se o curso já está cadastrado
            $query = $conn->prepare("SELECT COUNT(*) FROM cursos WHERE nome = :nome AND descricao = :descricao");
            $query->bindParam(':nome', $nome);
            $query->bindParam(':descricao', $descricao);
            $query->execute();
            $count = $query->fetchColumn();

            if ($count > 0) {
                // Se o curso já existe, armazena uma mensagem de curso existente
                $mensagem = "curso_existente";
            } else {
                // Caso contrário, insere os dados na tabela 'cursos'
                $query = $conn->prepare("INSERT INTO cursos (nome, descricao) VALUES (:nome, :descricao)");
                $query->bindParam(':nome', $nome);
                $query->bindParam(':descricao', $descricao);

                if ($query->execute()) {
                    $mensagem = "sucesso";
                } else {
                    $mensagem = "erro";
                }
            }
        } catch (PDOException $e) {
            // Em caso de erro
            $mensagem = "erro";
        }
    } else {
        // Se os campos não foram preenchidos corretamente
        $mensagem = "campos_invalidos";
    }
}

// Consulta para buscar os registros
$sql = "SELECT id, nome, descricao FROM cursos";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Curso</title>
    <style>
        /* Estilos do formulário */
        body {
            font-family: "Open Sans", sans-serif;
            background-color: #f4f4f9;
        }

        .container { 
    max-width: 800px;
    margin: 50px auto;
    background-color: #fff;
    padding: 20px;
    border-radius: 0px;
    box-shadow: 0 0 25px rgba(0, 0, 0, 0.1);
    margin-top: 50px; /* Ajuste para começar abaixo do cabeçalho */
    margin-bottom: 50px; /* Espaçamento entre o container e a tabela */
    height: auto; /* Altura ajustável para conteúdo */
}

.container h2 {
    background-color: #5aa2b0;
    color: white;
    text-align: center;
    padding: 15px 0;
    border-radius: 0px 0px 0 0;
    box-shadow: 50 10 15px rgba(0, 0, 0, 0.1);
    margin-top: -20px;
    width: calc(100% + 40px);
    margin-left: -20px;
    height: 40px;
    margin-bottom: 30px;
    font-family: 'Arial', sans-serif;
    font-size: 35px;
    line-height: 40px;
    font-weight: bold;
    letter-spacing: 1px;
}

form {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    padding: 20px;
}

label {
    font-weight: bold;
}

input[type="text"] {
    width: 90%;
    padding: 10px;
    margin-top: 5px;
    border: 1px solid #5aa2b0;
    border-radius: 12px;
    box-sizing: border-box; /* Para garantir que o padding não aumente a largura total */
    min-height: 20px; /* Altura mínima para todos os campos */
    margin-left: 0; /* Centraliza o campo input */
    font-size: 10px; 
}

#descricao {
    margin-top: 22px; /* Ajuste para alinhamento */
}

label[for="nome"], 
label[for="descricao"] {
    font-weight: bold;
    font-size: 14px;
    color: #000000; 
}

button {
    grid-column: span 2;
    padding: 15px;
    background-color: #174650;
    color: white;
    border: none;
    border-radius: 30px;
    box-shadow: 0 10px 10px rgba(0, 0, 0, 0.1);
    font-size: 18px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    width: 100%; /* Largura total */
    max-width: 800px; /* Tamanho máximo do botão */
    margin: 20px auto; /* Centraliza o botão */
}

button:hover {
    background-color: #5aa2b0;
}

.error {
    color: red;
    margin-top: 5px;
    font-size: 14px;
}

.header-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
    background-color: #ffffff;
    position: relative;
    max-height: 45px;
    margin-bottom: 50px;
    width: 100%; /* Ajuste para preencher toda a largura */
}

.logo img {
    max-width: 105px;
    height: auto;
    margin-top: 7px;
    margin-right: 20px;
}

.menu {
    margin-top: -7px;
    display: flex;
    flex-direction: row;
    gap: 15px;
    position: static;
    background-color: transparent;
    margin-left: auto; /* Alinhamento à direita */
    font-family: "Open Sans", sans-serif;
    text-decoration: none;
}

.menu a {
    text-decoration: none;
    padding: 0 15px;
    color: rgb(129, 121, 121); 
    font-size: 17px;
    transition: 0.3s ease;
}

.menu-login-container {
    display: flex;
    align-items: center;
    margin-left: auto; /* Alinhamento à direita */
}

/* Estilo responsivo */
@media (max-width: 768px) {
    .nav menu {
        display: none;
        flex-direction: column;
        position: absolute;
        top: 60px;
        left: 0;
        width: 100%;
        background-color: #fff;
        padding: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    .menu-icon {
        display: block;
        color: #2f2c73;
    }

    .menu a {
        padding: 10px;
    }
}

h1 {
    color: #007bff;
    text-align: center;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
    margin-top: 50px; /* Ajuste para posicionar a tabela após o formulário */
    border: 2px solid #007bff;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

th, td {
    padding: 20px;
    text-align: left;
    border: 1px solid #007bff;
    transition: background-color 0.3s;
}

th {
    background-color: #007bff;
    color: white;
}

tr:nth-child(even) {
    background-color: #f2f2f2;
}

tr:hover {
    background-color: #e9ecef;
}

#confirmModal, #successModal, #errorModal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
}

.modal-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    max-width: 100px;
    width: 70%;
}

.modal-button {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    margin: 5px;
    transition: background-color 0.3s, transform 0.2s;
    font-size: 16px;
}

.modal-button:hover {
    background-color: #0056b3;
    transform: scale(1.05);
}

.modal-button.cancel {
    background-color: #6c757d;
}

.modal-button.cancel:hover {
    background-color: #5a6268;
}

.modal-text {
    font-size: 16px;
    margin: 10px 0;
}

.action-link {
    color: #007bff;
    text-decoration: none;
    font-weight: bold;
    margin: 0 5px;
}

.action-link:hover {
    text-decoration: underline;
}

.fa {
    font-size: 18px;
    margin: 0 5px;
}

.edit-form {
    display: none;
    margin: 10px 0;
    padding: 10px;
    border: 1px solid #007bff;
    border-radius: 5px;
    background-color: #f8f9fa;
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
    <div class="container">
        <h2 class="titulo-principal">Cadastrar curso</h2>
        <form id="cadastroForm" action="" method="post">
            <div>
                <label for="nome">Curso</label>
                <input type="text" id="nome" name="nome" placeholder="Curso" required>
                <div id="nomeError" class="error"></div> <!-- Mensagem de erro -->
            </div>
            <br>
            <label for="nome">Descrição</label>
                <input type="text" id="descricao" name="descricao" placeholder="Descrição" required>
                <div id="descricaoError" class="error"></div> <!-- Mensagem de erro -->
            </div> 

            <button type="submit">Cadastrar</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    
    <script>
        // Aqui está o código PHP que insere a variável "mensagem" no JavaScript
        const mensagem = "<?php echo $mensagem; ?>";

        // Exibe o popup apropriado de acordo com o valor da mensagem
        if (mensagem === "sucesso") {
            Swal.fire({
                icon: 'success',
                title: 'Cadastro realizado com sucesso!',
                showConfirmButton: false,
                timer: 1500,
                position: 'center'
            });
        } else if (mensagem === "curso_existente") {
            Swal.fire({
                icon: 'warning',
                title: 'Curso já cadastrado!',
                text: 'Este curso já existe no sistema.',
                showConfirmButton: true,
                position: 'center'
            });
        } else if (mensagem === "erro") {
            Swal.fire({
                icon: 'error',
                title: 'Erro ao cadastrar!',
                text: 'Ocorreu um erro ao cadastrar o curso.',
                showConfirmButton: true,
                position: 'center'
            });
        } else if (mensagem === "campos_invalidos") {
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: 'Por favor, preencha todos os campos.',
                showConfirmButton: true,
                position: 'center'
            });
        }

    </script>
    <h1>Lista de Cursos </h1>

    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Descrição</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($result as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['nome']) ?></td>
                    <td><?= htmlspecialchars($row['descricao']) ?></td>
                    <td>
                        <a href="#" class="action-link" onclick="editarRegistro(<?= $row['id'] ?>, '<?= addslashes($row['nome']) ?>', '<?= addslashes($row['descricao']) ?>')">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="#" class="action-link" onclick="confirmarExclusao(<?= $row['id'] ?>)">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>

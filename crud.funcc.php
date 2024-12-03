<?php  
session_start();
include("config.php");

$edit_id = null;
$edit_nome = null;
$edit_cargo = null;
$edit_email = null;
$edit_matricula = null;
$edit_telefone = null;
$edit_data_nascimento = null;
$edit_senha = null;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    $stmt = $conn->prepare("DELETE FROM funcionarios WHERE id = :id");
    $stmt->bindParam(':id', $delete_id);
    
    if ($stmt->execute()) {
        $_SESSION['mensagemSucesso'] = "";
        echo "<script>window.onload = function() { document.getElementById('successMessage').innerText = 'Funcionário excluído com sucesso!'; document.getElementById('successModal').style.display = 'block'; }</script>";
    } else {
        echo "<script>alert('Erro ao excluir o funcionário.');</script>";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_id'])) {
    $edit_id = $_POST['edit_id'];
    $edit_nome = $_POST['edit_nome'];
    $edit_cargo = $_POST['edit_cargo'];
    $edit_email = $_POST['edit_email'];
    $edit_matricula = $_POST['edit_matricula'];
    $edit_telefone = $_POST['edit_telefone'];
    $edit_data_nascimento = $_POST['edit_data_nascimento'];
    $edit_senha = $_POST['edit_senha'];

    $stmt = $conn->prepare("SELECT COUNT(*) FROM funcionarios WHERE (nome = :nome OR email = :email OR matricula = :matricula OR telefone = :telefone) AND id != :id");
    $stmt->bindParam(':nome', $edit_nome);
    $stmt->bindParam(':email', $edit_email);
    $stmt->bindParam(':matricula', $edit_matricula);
    $stmt->bindParam(':telefone', $edit_telefone);
    $stmt->bindParam(':id', $edit_id);
    $stmt->execute();
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        $_SESSION['mensagemErro'] = "Funcionário já cadastrado.";
    } else {
        $hashed_senha = password_hash($edit_senha, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE funcionarios SET nome = :nome, email = :email, cargo = :cargo, matricula = :matricula, telefone = :telefone, data_nascimento = :data_nascimento, senha = :senha WHERE id = :id");
        $stmt->bindParam(':nome', $edit_nome);
        $stmt->bindParam(':email', $edit_email);
        $stmt->bindParam(':cargo', $edit_cargo);
        $stmt->bindParam(':matricula', $edit_matricula);
        $stmt->bindParam(':telefone', $edit_telefone);
        $stmt->bindParam(':data_nascimento', $edit_data_nascimento);
        $stmt->bindParam(':senha', $hashed_senha);
        $stmt->bindParam(':id', $edit_id);
        
        if ($stmt->execute()) {
            echo "<script>window.onload = function() { document.getElementById('successMessage').innerText = 'Registro atualizado com sucesso!'; document.getElementById('successModal').style.display = 'block'; }</script>";
        } else {
            echo "<script>alert('Erro ao atualizar o registro.');</script>";
        }
    }
}

$sql = "SELECT id, nome, email, cargo, matricula, telefone, data_nascimento FROM funcionarios";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$mensagemSucesso = isset($_SESSION['mensagemSucesso']) ? $_SESSION['mensagemSucesso'] : "";
$mensagemErro = isset($_SESSION['mensagemErro']) ? $_SESSION['mensagemErro'] : "";

unset($_SESSION['mensagemSucesso']);
unset($_SESSION['mensagemErro']);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Registros</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
         body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        h1 {
            color: #007bff;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
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
        /* Estilo para os modais */
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
            max-width: 400px;
            width: 90%;
        }
        h3 {
            margin-top: 0;
        }
        /* Estilos para os botões */
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
        /* Estilo para os textos dentro do modal */
        .modal-text {
            font-size: 16px;
            margin: 10px 0;
        }
        /* Estilo para os links de ações */
        .action-link {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
            margin: 0 5px;
        }
        .action-link:hover {
            text-decoration: underline;
        }
        /* Estilo dos ícones */
        .fa {
            font-size: 18px;
            margin: 0 5px;
        }
        /* Estilo do formulário de edição */
        .edit-form {
            display: none; /* Inicialmente oculto */
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #007bff;
            border-radius: 5px;
            background-color: #f8f9fa;
        }
        /* Estilo do campo de senha */
        .password-container {
            position: relative;
        }
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #007bff;
        }
    </style>
    <script>
        function openConfirmModal(id) {
            document.getElementById('confirmModal').style.display = 'block';
            document.getElementById('delete_id').value = id;
        }
        function closeConfirmModal() {
            document.getElementById('confirmModal').style.display = 'none';
        }
        function openEditForm(id, nome, cargo, email, matricula, telefone, data_nascimento) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_nome').value = nome;
            document.getElementById('edit_cargo').value = cargo;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_matricula').value = matricula;
            document.getElementById('edit_telefone').value = telefone;
            document.getElementById('edit_data_nascimento').value = data_nascimento;
            document.querySelector('.edit-form').style.display = 'block';
        }
        function fecharSuccessModal() {
            document.getElementById('successModal').style.display = 'none';
        }
    </script>
</head>
<body>
    <h1>Lista de Funcionários</h1>
    <?php if ($mensagemSucesso): ?>
        <div class="alert alert-success"><?= $mensagemSucesso ?></div>
    <?php endif; ?>
    <?php if ($mensagemErro): ?>
        <div class="alert alert-danger"><?= $mensagemErro ?></div>
    <?php endif; ?>
    
    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Cargo</th>
                <th>Matrícula</th>
                <th>Telefone</th>
                <th>Data Nascimento</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($result as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['nome']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['cargo']) ?></td>
                    <td><?= htmlspecialchars($row['matricula']) ?></td>
                    <td><?= htmlspecialchars($row['telefone']) ?></td>
                    <td><?= htmlspecialchars($row['data_nascimento']) ?></td>
                    <td>
                        <a href="#" class="action-link" onclick="openEditForm(<?= $row['id'] ?>, '<?= addslashes(htmlspecialchars($row['nome'])) ?>', '<?= addslashes(htmlspecialchars($row['cargo'])) ?>', '<?= addslashes(htmlspecialchars($row['email'])) ?>', '<?= addslashes(htmlspecialchars($row['matricula'])) ?>', '<?= addslashes(htmlspecialchars($row['telefone'])) ?>', '<?= addslashes(htmlspecialchars($row['data_nascimento'])) ?>')">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="#" class="action-link" onclick="openConfirmModal(<?= $row['id'] ?>)">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Modal de confirmação -->
    <div id="confirmModal">
        <div class="modal-content">
            <h3>Confirmar Exclusão</h3>
            <p class="modal-text">Você tem certeza que deseja excluir este funcionário?</p>
            <form method="POST" action="">
                <input type="hidden" name="delete_id" id="delete_id">
                <button type="submit" class="modal-button">Confirmar</button>
                <button type="button" class="modal-button cancel" onclick="closeConfirmModal()">Cancelar</button>
            </form>
        </div>
    </div>

    <!-- Modal de sucesso -->
    <div id="successModal" style="display: none;">
        <div class="modal-content">
            <h3>Sucesso</h3>
            <p class="modal-text" id="successMessage"></p>
            <button class="modal-button" onclick="fecharSuccessModal()">Fechar</button>
        </div>
    </div>

    <!-- Formulário de edição -->
    <div class="edit-form">
        <h3>Editar Funcionário</h3>
        <form method="POST" action="">
            <input type="hidden" name="edit_id" id="edit_id">
            <label for="edit_nome">Nome:</label>
            <input type="text" name="edit_nome" id="edit_nome" required>
            <label for="edit_cargo">Cargo:</label>
            <input type="text" name="edit_cargo" id="edit_cargo" required>
            <label for="edit_email">Email:</label>
            <input type="email" name="edit_email" id="edit_email" required>
            <label for="edit_matricula">Matrícula:</label>
            <input type="text" name="edit_matricula" id="edit_matricula" required>
            <label for="edit_telefone">Telefone:</label>
            <input type="text" name="edit_telefone" id="edit_telefone" required>
            <label for="edit_data_nascimento">Data de Nascimento:</label>
            <input type="date" name="edit_data_nascimento" id="edit_data_nascimento" required>
            <label for="edit_senha">Senha:</label>
            <input type="password" name="edit_senha" id="edit_senha" required>
            <button type="submit" class="modal-button">Salvar</button>
            <button type="button" class="modal-button cancel" onclick="document.querySelector('.edit-form').style.display='none'">Cancelar</button>
        </form>
    </div>
</body>
</html>

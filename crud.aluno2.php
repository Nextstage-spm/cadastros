<?php  
// Iniciar a sessão
session_start();

// Incluir a conexão com o banco de dados
include("config.php");

// Variáveis de controle
$edit_id = null;
$edit_nome = null;
$edit_email = null;
$edit_telefone = null;
$edit_data_nascimento = null;

// Verificar se uma exclusão foi solicitada via POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];

    // Preparar e executar a exclusão no banco de dados
    $stmt = $conn->prepare("DELETE FROM aluno_cadastro WHERE id = :id");
    $stmt->bindParam(':id', $delete_id);
    
    if ($stmt->execute()) {
        $_SESSION['mensagemSucesso'] = "Funcionário excluído com sucesso!";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $_SESSION['mensagemErro'] = "Erro ao excluir o aluno.";
    }
}

// Verificar se uma edição foi solicitada via POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_id'])) {
    $edit_id = $_POST['edit_id'];
    $edit_nome = $_POST['edit_nome'];
    $edit_email = $_POST['edit_email'];
    $edit_telefone = $_POST['edit_telefone'];
    $edit_data_nascimento = $_POST['edit_data_nascimento'];

    // Verificar se o novo nome já existe no banco de dados
    $stmt = $conn->prepare("SELECT COUNT(*) FROM aluno_cadastro WHERE (nome = :nome OR email = :email OR telefone = :telefone OR data_nascimento = :data_nascimento) AND id != :id");
    $stmt->bindParam(':nome', $edit_nome);
    $stmt->bindParam(':email', $edit_email);
    $stmt->bindParam(':telefone', $edit_telefone);
    $stmt->bindParam(':data_nascimento', $edit_data_nascimento);
    $stmt->bindParam(':id', $edit_id);
    $stmt->execute();
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        $_SESSION['mensagemErro'] = "Aluno já cadastrado.";
    } else {
        // Preparar e executar a atualização no banco de dados
        $stmt = $conn->prepare("UPDATE aluno_cadastro SET nome = :nome, email = :email, telefone = :telefone, data_nascimento = :data_nascimento WHERE id = :id");
        $stmt->bindParam(':nome', $edit_nome);
        $stmt->bindParam(':email', $edit_email);
        $stmt->bindParam(':telefone', $edit_telefone);
        $stmt->bindParam(':data_nascimento', $edit_data_nascimento);
        $stmt->bindParam(':id', $edit_id);
        
        if ($stmt->execute()) {
            $_SESSION['mensagemSucesso'] = "Registro atualizado com sucesso!";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $_SESSION['mensagemErro'] = "Erro ao atualizar o registro.";
        }
    }
}

// Consulta para buscar os registros
$sql = "SELECT id, nome, email, telefone, data_nascimento FROM aluno_cadastro";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Capturar a mensagem de sucesso e erro da sessão
$mensagemSucesso = isset($_SESSION['mensagemSucesso']) ? $_SESSION['mensagemSucesso'] : "";
$mensagemErro = isset($_SESSION['mensagemErro']) ? $_SESSION['mensagemErro'] : "";

// Limpar as variáveis de sessão após o uso
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
            display: none; /* Inicialmente oculto */
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #007bff;
            border-radius: 5px;
            background-color: #f8f9fa;
        }
    </style>
    <script>
    function editarRegistro(id, nome, email, telefone, data_nascimento) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_nome').value = nome;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_telefone').value = telefone;
        document.getElementById('edit_data_nascimento').value = data_nascimento;
        document.getElementById('editForm').style.display = 'block';
    }

    function confirmarExclusao(id) {
        document.getElementById('delete_id').value = id;
        document.getElementById('confirmModal').style.display = 'block';
    }

    function fecharModal() {
        document.getElementById('confirmModal').style.display = 'none';
        document.getElementById('successModal').style.display = 'none';
    }

    function exibirMensagem(mensagem) {
        const successModal = document.getElementById('successModal');
        successModal.querySelector('.modal-text').innerText = mensagem;
        successModal.style.display = 'block';
    }

    window.onload = function() {
        <?php if ($mensagemSucesso): ?>
            exibirMensagem('<?= addslashes($mensagemSucesso) ?>');
        <?php endif; ?>
    }
    </script>
</head>
<body>
    <h1>Lista de Alunos</h1>
    
    <?php if ($mensagemErro): ?>
        <div class="alert alert-danger"><?= $mensagemErro ?></div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Telefone</th>
                <th>Data de Nascimento</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($result as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['nome']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['telefone']) ?></td>
                    <td><?= htmlspecialchars($row['data_nascimento']) ?></td>
                    <td>
                        <a href="#" class="action-link" onclick="editarRegistro(<?= $row['id'] ?>, '<?= addslashes($row['nome']) ?>', '<?= addslashes($row['email']) ?>', '<?= addslashes($row['telefone']) ?>', '<?= addslashes($row['data_nascimento']) ?>')">
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

    <!-- Formulário de Edição -->
    <div class="edit-form" id="editForm">
        <h3>Editar Funcionário</h3>
        <form method="POST">
            <input type="hidden" id="edit_id" name="edit_id">
            <div>
                <label for="edit_nome">Nome:</label>
                <input type="text" id="edit_nome" name="edit_nome" required>
            </div>
            <div>
                <label for="edit_email">Email:</label>
                <input type="email" id="edit_email" name="edit_email" required>
            </div>
            <div>
                <label for="edit_telefone">Telefone:</label>
                <input type="text" id="edit_telefone" name="edit_telefone" required>
            </div>
            <div>
                <label for="edit_data_nascimento">Data de Nascimento:</label>
                <input type="date" id="edit_data_nascimento" name="edit_data_nascimento" required>
            </div>
            <button type="submit" class="modal-button">Atualizar</button>
            <button type="button" class="modal-button cancel" onclick="document.getElementById('editForm').style.display='none'">Cancelar</button>
        </form>
    </div>

    <!-- Modal de confirmação de exclusão -->
    <div id="confirmModal">
        <div class="modal-content">
            <h3>Confirmar Exclusão</h3>
            <p class="modal-text">Você tem certeza que deseja excluir este funcionário?</p>
            <form method="POST">
                <input type="hidden" id="delete_id" name="delete_id">
                <button type="submit" class="modal-button">Confirmar</button>
                <button type="button" class="modal-button cancel" onclick="fecharModal()">Cancelar</button>
            </form>
        </div>
    </div>

    <!-- Modal de sucesso -->
    <div id="successModal" style="display: none;">
        <div class="modal-content">
            <h3>Sucesso</h3>
            <p class="modal-text"></p>
            <button class="modal-button" onclick="document.getElementById('successModal').style.display='none'">Fechar</button>
        </div>
    </div>

    <script>
        // Funções existentes...
    </script>
</body>
</html>

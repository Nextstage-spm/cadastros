<?php
// Iniciar a sessão
session_start();

// Incluir a conexão com o banco de dados
include("config.php");

// Variáveis de controle
$edit_id = null;
$edit_nome = null;
$edit_cargo = null;
$edit_email = null;
$edit_matricula = null;
$edit_telefone = null;
$edit_data_nascimento = null;
$edit_senha = null;
$formVisible = false; // Variável para controlar a visibilidade do formulário

// Verificar se uma exclusão foi solicitada via POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];

    // Preparar e executar a exclusão no banco de dados
    $stmt = $conn->prepare("DELETE FROM funcionarios WHERE id = :id");
    $stmt->bindParam(':id', $delete_id);
    
    if ($stmt->execute()) {
        $_SESSION['mensagemSucesso'] = "Funcionário excluído com sucesso!";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        echo "<script>alert('Erro ao excluir o funcionário.');</script>";
    }
}

// Verificar se uma edição foi solicitada via POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_id'])) {
    $edit_id = $_POST['edit_id'];
    $edit_nome = $_POST['edit_nome'];
    $edit_cargo = $_POST['edit_cargo'];
    $edit_email = $_POST['edit_email'];
    $edit_matricula = $_POST['edit_matricula'];
    $edit_telefone = $_POST['edit_telefone'];
    $edit_data_nascimento = $_POST['edit_data_nascimento'];

    // Verificar se o novo nome já existe no banco de dados
    $stmt = $conn->prepare("SELECT COUNT(*) FROM funcionarios WHERE nome = :nome OR email = :email OR matricula = :matricula OR telefone = :telefone) AND id != :id");
    $stmt->bindParam(':nome', $edit_nome);
    $stmt->bindParam(':email', $edit_email);
    $stmt->bindParam(':cargo', $edit_cargo);
    $stmt->bindParam(':matricula', $edit_matricula);
    $stmt->bindParam(':telefone', $edit_telefone);
    $stmt->bindParam(':data_nascimento', $edit_data_nascimento);
    $stmt->bindParam(':id', $edit_id);
    $stmt->execute();
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        // Nome já cadastrado
        $_SESSION['mensagemErro'] = "Funcionário já cadastrado.";
    } else {
        // Preparar e executar a atualização no banco de dados
        $stmt = $conn->prepare("UPDATE funcionarios SET nome = :nome, email = :email, cargo = :cargo, matricula = :matricula, telefone = :telefone, data_nascimento = :data_nascimento, senha = :senha WHERE id = :id");
        $stmt->bindParam(':nome', $edit_nome);
        $stmt->bindParam(':email', $edit_email);
        $stmt->bindParam(':cargo', $edit_cargo);
        $stmt->bindParam(':matricula', $edit_matricula);
        $stmt->bindParam(':telefone', $edit_telefone);
        $stmt->bindParam(':data_nascimento', $edit_data_nascimento);
        $stmt->bindParam(':id', $edit_id);
        
        if ($stmt->execute()) {
            $_SESSION['mensagemSucesso'] = "Registro atualizado com sucesso!";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            echo "<script>alert('Erro ao atualizar o registro.');</script>";
        }
    }
}

// Consulta para buscar os registros
$sql = "SELECT id, nome, email, cargo, matricula, telefone, data_nascimento FROM funcionarios";
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
            border-radius: 0;
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
    </style>
    <script>
    // Função para exibir o formulário de edição
    function editarRegistro(id, nome, cargo, matricula, telefone, email, data_nascimento, senha) {
        // Preencher os campos do formulário
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_nome').value = nome;
        document.getElementById('edit_cargo').value = cargo;
        document.getElementById('edit_matricula').value = matricula;
        document.getElementById('edit_telefone').value = telefone;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_data_nascimento').value = data_nascimento;

        // Exibir o formulário
        const form = document.getElementById('form-edit-' + id);
        form.style.display = 'block';
        // Ocultar outros formulários
        const forms = document.getElementsByClassName('edit-form');
        for (let i = 0; i < forms.length; i++) {
            if (forms[i] !== form) {
                forms[i].style.display = 'none';
            }
        }
    }

    // Função para fechar o formulário de edição
    function fecharFormularioEditar(id) {
        document.getElementById('form-edit-' + id).style.display = 'none';
    }

    // Função para fechar o modal
    function fecharModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }

    // Função para exibir o modal de sucesso
    function mostrarMensagemSucesso() {
        document.getElementById('successModal').style.display = 'block';
    }

    // Função para exibir o modal de erro
    function mostrarMensagemErro() {
        document.getElementById('errorModal').style.display = 'block';
    }

    // Fechar o modal de sucesso
    function fecharModalSucesso() {
        fecharModal('successModal');
    }

    // Fechar o modal de erro e garantir que o formulário permaneça aberto
    function fecharModalErro() {
        fecharModal('errorModal');
        // Reabrir o formulário de edição se houver um ID
        const editId = document.getElementById('edit_id').value;
        if (editId) {
            document.getElementById('form-edit-' + editId).style.display = 'block';
        }
    }

    // Função para confirmar a exclusão do registro
    function confirmarExclusao(id) {
        document.getElementById('delete_id').value = id;
        document.getElementById('confirmModal').style.display = 'block';
    }

    // Função para excluir o registro
    function excluirRegistro() {
        const id = document.getElementById('delete_id').value;
        // Criar um formulário para enviar o ID via POST
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `<input type='hidden' name='delete_id' value='${id}'>`; // Corrigido para usar crase
        document.body.appendChild(form);
        form.submit();
    }
</script>

</head>
<body>

<h1>Lista de Registros</h1>

<table>
    <tr>
        <td colspan="3" style="text-align: center; font-weight: bold; font-size: 20px;">Registro de Professores</td>
    </tr>
    <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>Cargo</th>
        <th>E-mail</th>
        <th>Matrícula</th>
        <th>Telefone</th>
        <th>Data de Nascimento</th>
        <th>Ações</th>
    </tr>
    <?php
    // Exibir cada registro
    if ($result) {
        foreach ($result as $row) {
            echo "<tr>
                    <td>" . htmlspecialchars($row["id"]) . "</td>
                    <td>" . htmlspecialchars($row["nome"]) . "</td>
                    <td>" . htmlspecialchars($row["cargo"]) . "</td>
                    <td>" . htmlspecialchars($row["email"]) . "</td>
                    <td>" . htmlspecialchars($row["matricula"]) . "</td>
                    <td>" . htmlspecialchars($row["telefone"]) . "</td>
                    <td>" . htmlspecialchars($row["data_nascimento"]) . "</td>
                    <td>
                        <a class='action-link' href='#' onclick='editarRegistro(" . htmlspecialchars($row["id"]) . ", \"" . htmlspecialchars($row["nome"]) .
                        ", \"" . htmlspecialchars($row["cargo"]) . ", \"" . htmlspecialchars($row["email"]) . ", \"" . htmlspecialchars($row["matricula"]) .
                        ", \"" . htmlspecialchars($row["telefone"]) .", \"" . htmlspecialchars($row["data_nascimento"]) . "\")'>
                            <i class='fas fa-edit' title='Editar'></i>
                        </a>
                        <a class='action-link' href='#' onclick='confirmarExclusao(" . htmlspecialchars($row["id"]) . ")'>
                            <i class='fas fa-trash' title='Excluir'></i>
                        </a>
                        <!-- Formulário de edição -->
                        <div id='form-edit-" . htmlspecialchars($row["id"]) . "' class='edit-form'>
                            <form method='POST'>
                                <input type='hidden' id='edit_id' name='edit_id' value='" . htmlspecialchars($row["id"]) . "'>
                                <input type='text' id='edit_nome' name='edit_nome' value='" . htmlspecialchars($row["nome"]) . "' required>
                                <input type='text' id='edit_cargo' name='edit_cargo' value='" . htmlspecialchars($row["cargo"]) . "' required>
                                <input type='text' id='edit_email' name='edit_email' value='" . htmlspecialchars($row["email"]) . "' required>
                                <input type='text' id='edit_matricula' name='edit_matricula' value='" . htmlspecialchars($row["matricula"]) . "' required>
                                <input type='text' id='edit_telefone' name='edit_telefone' value='" . htmlspecialchars($row["nome"]) . "' required>
                                <input type='text' id='edit_data_nascimento' name='edit_data_nascimento' value='" . htmlspecialchars($row["data_nascimento"]) . "' required>
                                <button type='submit' class='modal-button'>Salvar</button>
                                <button type='button' class='modal-button cancel' onclick='fecharFormularioEditar(" . htmlspecialchars($row["id"]) . ")'>Cancelar</button>
                            </form>
                        </div>
                    </td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='3'>Nenhum registro encontrado.</td></tr>";
    }
    ?>
</table>

<!-- Modal de confirmação -->
<div id="confirmModal">
    <div class="modal-content">
        <h3>Confirmar Exclusão</h3>
        <p class="modal-text">Tem certeza que deseja excluir este registro?</p>
        <input type="hidden" id="delete_id" value="">
        <button class="modal-button" onclick="excluirRegistro()">Sim</button>
        <button onclick="fecharModal('confirmModal')" class="modal-button cancel">Não</button>
    </div>
</div>

<!-- Modal de sucesso -->
<div id="successModal" style="display: <?php echo !empty($mensagemSucesso) ? 'block' : 'none'; ?>;">
    <div class="modal-content">
        <h3>Sucesso!</h3>
        <p class="modal-text"><?php echo htmlspecialchars($mensagemSucesso); ?></p>
        <button onclick="fecharModalSucesso()" class="modal-button">Fechar</button>
    </div>
</div>

<!-- Modal de erro -->
<div id="errorModal" style="display: <?php echo !empty($mensagemErro) ? 'block' : 'none'; ?>;">
    <div class="modal-content">
        <h3>Erro!</h3>
        <p class="modal-text"><?php echo htmlspecialchars($mensagemErro); ?></p>
        <button onclick="fecharModalErro()" class="modal-button">Fechar</button>
    </div>
</div>
</body>
</html>
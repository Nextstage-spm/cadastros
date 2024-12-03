<?php   
include("config.php"); // Inclui a conexão com o banco de dados

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
        $palavrasecreta = $_POST['palavrasecreta'];

        try {
            $updateQuery = $conn->prepare("UPDATE aluno_cadastro SET nome = :nome, email = :email, telefone = :telefone, data_nascimento = :data_nascimento, cpf = :cpf, palavrasecreta = :palavrasecreta WHERE id = :id");
            $updateQuery->bindParam(':nome', $nome);
            $updateQuery->bindParam(':email', $email);
            $updateQuery->bindParam(':telefone', $telefone);
            $updateQuery->bindParam(':data_nascimento', $data_nascimento);
            $updateQuery->bindParam(':cpf', $cpf);
            $updateQuery->bindParam(':id', $id);
            $updateQuery->bindParam(':palavrasecreta', $palavrasecreta);

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
    } elseif (isset($_POST['nome']) && isset($_POST['email']) && isset($_POST['telefone']) && isset($_POST['data_nascimento']) && isset($_POST['cpf']) && isset($_POST['palavrasecreta'])) {
        // Cadastro de novo aluno
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $telefone = $_POST['telefone'];
        $data_nascimento = $_POST['data_nascimento'];
        $cpf = $_POST['cpf'];
        $palavrasecreta = $_POST['palavrasecreta'];

        try {
            $query = $conn->prepare("INSERT INTO aluno_cadastro (nome, email, telefone, data_nascimento, cpf) 
                                     VALUES (:nome, :email, :telefone, :data_nascimento, :cpf, :palavrasecreta)");
            $query->bindParam(':nome', $nome);
            $query->bindParam(':email', $email);
            $query->bindParam(':telefone', $telefone);
            $query->bindParam(':data_nascimento', $data_nascimento);
            $query->bindParam(':cpf', $cpf);
            $query->bindParam(':palavrasecreta', $palavrasecreta);
           

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
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Aluno</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: "Open Sans", sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1000px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 0px;
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.1);
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
            height: 60px;
            margin-bottom: 30px;
            font-family: 'Arial', sans-serif;
            font-size: 35px;
            font-weight: bold;
        }

        form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            padding: 20px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="date"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #5aa2b0;
            border-radius: 12px;
            box-sizing: border-box;
            font-size: 14px;
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
            width: 100%;
            max-width: 200px;
            margin: 20px auto;
        }

        button:hover {
            background-color: #5aa2b0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 12px;
        }

        th {
            background-color: #5aa2b0;
            color: white;
        }

        .acao {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .acao a {
            text-decoration: none;
            color: #174650;
            padding: 5px;
            transition: all 0.3s ease;
        }

        .acao a:hover {
            color: #5aa2b0;
        }

        /* Estilos do modal de mensagem */
        #mensagemModal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            overflow: auto;
        }

        .modal-content-mensagem {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border-radius: 5px;
            width: 300px;
            text-align: center;
            position: relative;
        }

        /* Estilos do modal de edição */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            overflow: auto;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 5px;
            position: relative;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: black;
        }

        /* Estilos do modal de confirmação */
        .confirm-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            overflow: auto;
        }

        .confirm-modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border-radius: 5px;
            width: 300px;
            text-align: center;
            position: relative;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .confirm-modal-buttons {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .confirm-modal-buttons button {
            padding: 8px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: auto;
            margin: 0 5px;
        }

        .btn-confirmar {
            background-color: #174650;
            color: white;
        }

        .btn-cancelar {
            background-color: #dc3545;
            color: white;
        }

        .success { color: #4CAF50; }
        .error { color: #f44336; }
    </style>
</head>
<body>
    <!-- Modal de Mensagem -->
    <div id="mensagemModal">
        <div class="modal-content-mensagem">
            <p id="mensagemTexto"></p>
            <button onclick="fecharModal()" class="btn-confirmar">OK</button>
        </div>
    </div>

    <!-- Modal de Confirmação -->
    <div id="confirmModal" class="confirm-modal">
        <div class="confirm-modal-content">
            <p>Tem certeza que deseja excluir este aluno?</p>
            <div class="confirm-modal-buttons">
                <button class="btn-confirmar" onclick="confirmarExclusaoFinal()">Confirmar</button>
                <button class="btn-cancelar" onclick="fecharModalConfirmacao()">Cancelar</button>
            </div>
        </div>
    </div>

    <div class="container">
        <h2>Cadastro de Aluno</h2>

        <form method="POST" action="">
            <div>
                <label for="nome">Nome:</label>
                <input type="text" name="nome" id="nome" required>
            </div>
            <div>
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div>
                <label for="telefone">Telefone:</label>
                <input type="tel" name="telefone" id="telefone" required>
            </div>
            <div>
                <label for="cpf">CPF:</label>
                <input type="text" name="cpf" id="cpf" required>
            </div>
            <div>
                <label for="data_nascimento">Data de Nascimento:</label>
                <input type="date" name="data_nascimento" id="data_nascimento" required>
            </div>
            <div>
                <label for="palavrasecreta">Palavra Secreta:</label>
                <input type="text" name="palavrasecreta" id="palavrasecreta" required>
            </div>
            <button type="submit">Cadastrar</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Telefone</th>
                    <th>Data de Nascimento</th>
                    <th>CPF</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result as $aluno): ?>
                    <tr>
                        <td><?php echo $aluno['nome']; ?></td>
                        <td><?php echo $aluno['email']; ?></td>
                        <td><?php echo $aluno['telefone']; ?></td>
                        <td><?php echo $aluno['data_nascimento']; ?></td>
                        <td><?php echo $aluno['cpf']; ?></td>
                        <td class="acao">
                            <a href="javascript:void(0);" onclick="showEditModal(<?php echo htmlspecialchars(json_encode($aluno)); ?>)">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="javascript:void(0);" onclick="confirmarExclusao(<?php echo $aluno['id']; ?>)">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                            <form id="delete-form-<?php echo $aluno['id']; ?>" method="POST" style="display:none;">
                                <input type="hidden" name="delete_id" value="<?php echo $aluno['id']; ?>">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal de edição -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Editar Aluno</h2>
            <form method="POST" action="">
                <input type="hidden" name="id" id="editId">
                <div>
                    <label for="editNome">Nome:</label>
                    <input type="text" name="nome" id="editNome" required>
                </div>
                <div>
                    <label for="editEmail">Email:</label>
                    <input type="email" name="email" id="editEmail" required>
                </div>
                <div>
                    <label for="editTelefone">Telefone:</label>
                    <input type="tel" name="telefone" id="editTelefone" required>
                </div>
                <div>
                    <label for="editCPF">CPF:</label>
                    <input type="text" name="cpf" id="editCPF" required>
                </div>
                <div>
                    <label for="editDataNascimento">Data de Nascimento:</label>
                    <input type="date" name="data_nascimento" id="editDataNascimento" required>
                </div>
                <button type="submit" name="update">Atualizar</button>
            </form>
        </div>
    </div>

    <script>
        let alunoIdParaExcluir = null;

        function mostrarModal(mensagem, tipo) {
            const modal = document.getElementById('mensagemModal');
            const mensagemTexto = document.getElementById('mensagemTexto');
            
            mensagemTexto.textContent = mensagem;
            mensagemTexto.className = tipo;
            
            modal.style.display = 'block';

            setTimeout(function() {
                fecharModal();
            }, 2000);
        }

        function fecharModal() {
            document.getElementById('mensagemModal').style.display = 'none';
        }

        function confirmarExclusao(id) {
            alunoIdParaExcluir = id;
            document.getElementById('confirmModal').style.display = 'block';
        }

        function fecharModalConfirmacao() {
            document.getElementById('confirmModal').style.display = 'none';
            alunoIdParaExcluir = null;
        }

        function confirmarExclusaoFinal() {
            if (alunoIdParaExcluir) {
                document.getElementById('delete-form-' + alunoIdParaExcluir).submit();
            }
            fecharModalConfirmacao();
        }

        function showEditModal(aluno) {
            document.getElementById('editId').value = aluno.id;
            document.getElementById('editNome').value = aluno.nome;
            document.getElementById('editEmail').value = aluno.email;
            document.getElementById('editTelefone').value = aluno.telefone;
            document.getElementById('editCPF').value = aluno.cpf;
            document.getElementById('editDataNascimento').value = aluno.data_nascimento;
            document.getElementById('editModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        window.onclick = function(event) {
            const mensagemModal = document.getElementById('mensagemModal');
            const confirmModal = document.getElementById('confirmModal');
            const editModal = document.getElementById('editModal');
            
            if (event.target == mensagemModal) {
                fecharModal();
            }
            if (event.target == confirmModal) {
                fecharModalConfirmacao();
            }
            if (event.target == editModal) {
                closeModal();
            }
        }

        <?php if (!empty($mensagem)): ?>
            mostrarModal("<?php echo addslashes($mensagem); ?>", "<?php echo $tipo_alerta; ?>");
        <?php endif; ?>
    </script>
</body>
</html>
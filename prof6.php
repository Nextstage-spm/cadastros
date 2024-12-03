<?php
session_start();
include("config.php");

// Verifica se existe mensagem na sessão
if (isset($_SESSION['mensagem'])) {
    $mensagem = $_SESSION['mensagem'];
    $tipo_alerta = $_SESSION['tipo_alerta'];
    
    // Limpa as mensagens da sessão
    unset($_SESSION['mensagem']);
    unset($_SESSION['tipo_alerta']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_id'])) {
        // Lógica para exclusão
        $delete_id = $_POST['delete_id'];
        
        try {
            $query = $conn->prepare("DELETE FROM professor WHERE id = :id");
            $query->bindParam(':id', $delete_id);

            if ($query->execute()) {
                $_SESSION['mensagem'] = "Professor excluído com sucesso!";
                $_SESSION['tipo_alerta'] = "success";
            } else {
                $_SESSION['mensagem'] = "Erro ao excluir o professor.";
                $_SESSION['tipo_alerta'] = "error";
            }
        } catch (PDOException $e) {
            $_SESSION['mensagem'] = "Erro ao excluir o professor: " . $e->getMessage();
            $_SESSION['tipo_alerta'] = "error";
        }
        
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } elseif (isset($_POST['update'])) {
        // Lógica para atualização
        if (isset($_POST['idtitulacao']) && !empty($_POST['idtitulacao'])) {
            $id = $_POST['id'];
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $telefone = $_POST['telefone'];
            $idtitulacao = $_POST['idtitulacao'];

            try {
                // Verificar se a titulação existe
                $stmt = $conn->prepare("SELECT COUNT(*) FROM titulacao WHERE idtitulacao = :idtitulacao");
                $stmt->bindParam(':idtitulacao', $idtitulacao);
                $stmt->execute();
                $titulacaoCount = $stmt->fetchColumn();

                if ($titulacaoCount == 0) {
                    $_SESSION['mensagem'] = "A titulação selecionada não existe!";
                    $_SESSION['tipo_alerta'] = "error";
                } else {
                    $query = $conn->prepare("UPDATE professor SET nome = :nome, email = :email, telefone = :telefone, idtitulacao = :idtitulacao WHERE id = :id");
                    $query->bindParam(':id', $id);
                    $query->bindParam(':nome', $nome);
                    $query->bindParam(':email', $email);
                    $query->bindParam(':telefone', $telefone);
                    $query->bindParam(':idtitulacao', $idtitulacao);

                    if ($query->execute()) {
                        $_SESSION['mensagem'] = "Professor atualizado com sucesso!";
                        $_SESSION['tipo_alerta'] = "success";
                    } else {
                        $_SESSION['mensagem'] = "Erro ao atualizar o professor.";
                        $_SESSION['tipo_alerta'] = "error";
                    }
                }
            } catch (PDOException $e) {
                $_SESSION['mensagem'] = "Erro ao atualizar o professor: " . $e->getMessage();
                $_SESSION['tipo_alerta'] = "error";
            }
        } else {
            $_SESSION['mensagem'] = "Selecione uma titulação válida!";
            $_SESSION['tipo_alerta'] = "error";
        }
        
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } elseif (!empty($_POST['nome']) && !empty($_POST['email']) && !empty($_POST['telefone']) && !empty($_POST['idtitulacao'])) {
        // Lógica para cadastro
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $telefone = $_POST['telefone'];
        $idtitulacao = $_POST['idtitulacao'];

        try {
            // Verificar se a titulação existe
            $stmt = $conn->prepare("SELECT COUNT(*) FROM titulacao WHERE idtitulacao = :idtitulacao");
            $stmt->bindParam(':idtitulacao', $idtitulacao);
            $stmt->execute();
            $titulacaoCount = $stmt->fetchColumn();

            if ($titulacaoCount == 0) {
                $_SESSION['mensagem'] = "A titulação selecionada não existe!";
                $_SESSION['tipo_alerta'] = "error";
            } else {
                // Verificar se o professor já existe com este email
                $stmt = $conn->prepare("SELECT COUNT(*) FROM professor WHERE email = :email");
                $stmt->bindParam(':email', $email);
                $stmt->execute();
                $count = $stmt->fetchColumn();

                if ($count > 0) {
                    $_SESSION['mensagem'] = "Já existe um professor cadastrado com este e-mail!";
                    $_SESSION['tipo_alerta'] = "error";
                } else {
                    $query = $conn->prepare("INSERT INTO professor (nome, email, telefone, idtitulacao) VALUES (:nome, :email, :telefone, :idtitulacao)");
                    $query->bindParam(':nome', $nome);
                    $query->bindParam(':email', $email);
                    $query->bindParam(':telefone', $telefone);
                    $query->bindParam(':idtitulacao', $idtitulacao);

                    if ($query->execute()) {
                        $_SESSION['mensagem'] = "Professor cadastrado com sucesso!";
                        $_SESSION['tipo_alerta'] = "success";
                    } else {
                        $_SESSION['mensagem'] = "Erro ao cadastrar o professor.";
                        $_SESSION['tipo_alerta'] = "error";
                    }
                }
            }
        } catch (PDOException $e) {
            $_SESSION['mensagem'] = "Erro ao cadastrar professor: " . $e->getMessage();
            $_SESSION['tipo_alerta'] = "error";
        }
        
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $_SESSION['mensagem'] = "Preencha todos os campos!";
        $_SESSION['tipo_alerta'] = "error";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Consulta para buscar os registros com o nome da titulação
$sql = "SELECT p.id, p.nome, p.email, p.telefone, p.idtitulacao, t.nome as titulacao_nome 
        FROM professor p 
        LEFT JOIN titulacao t ON p.idtitulacao = t.idtitulacao";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar lista de titulações para o select
$query = $conn->query("SELECT idtitulacao, nome FROM titulacao ORDER BY nome ASC");
$titulacoes = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Professor</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: "Open Sans", sans-serif;
            background-color: #f4f4f9;
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
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #5aa2b0;
            border-radius: 12px;
            box-sizing: border-box;
            font-size: 12px;
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
            padding: 8px;
        }

        th {
            background-color: #5aa2b0;
            color: white;
        }

        .acao {
            display: flex;
            gap: 10px;
        }

        .acao a {
            text-decoration: none;
            color: black;
            padding: 5px;
            transition: background-color 0.3s;
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
        }

        .modal-content-mensagem {
            background-color: #fefefe;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            border-radius: 5px;
            min-width: 300px;
            text-align: center;
        }

        .modal-content-mensagem button {
            margin-top: 15px;
            padding: 8px 20px;
            background-color: #174650;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: auto;
            max-width: none;
        }

        .success { color: #4CAF50; }
        .error { color: #f44336; }

        /* Estilos do modal de edição */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 5px;
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
        }

        .confirm-modal-content {
            background-color: #fefefe;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            border-radius: 5px;
            min-width: 300px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .confirm-modal-buttons {
            margin-top: 20px;
        }

        .confirm-modal-buttons button {
            margin: 0 10px;
            padding: 8px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: auto;
            max-width: none;
        }

        .btn-confirmar {
            background-color: #174650;
            color: white;
        }

        .btn-cancelar {
            background-color: #dc3545;
            color: white;
        }
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
            <p>Tem certeza que deseja excluir este professor?</p>
            <div class="confirm-modal-buttons">
                <button class="btn-confirmar" onclick="confirmarExclusaoFinal()">Confirmar</button>
                <button class="btn-cancelar" onclick="fecharModalConfirmacao()">Cancelar</button>
            </div>
        </div>
    </div>

    <div class="container">
        <h2>Cadastro de Professor</h2>

        <form method="POST" action="">
            <div>
                <label for="nome">Nome</label>
                <input type="text" name="nome" id="nome" required>
            </div>
            <div>
                <label for="email">E-mail</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div>
                <label for="telefone">Telefone</label>
                <input type="tel" name="telefone" id="telefone" required>
            </div>
            <div>
                <label for="idtitulacao">Titulação</label>
                <select name="idtitulacao" id="idtitulacao" required>
                    <option value="">Selecione</option>
                    <?php foreach ($titulacoes as $titulacao): ?>
                        <option value="<?php echo $titulacao['idtitulacao']; ?>"><?php echo $titulacao['nome']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit">Cadastrar</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Telefone</th>
                    <th>Titulação</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result as $professor): ?>
                    <tr>
                        <td><?php echo $professor['nome']; ?></td>
                        <td><?php echo $professor['email']; ?></td>
                        <td><?php echo $professor['telefone']; ?></td>
                        <td><?php echo $professor['titulacao_nome']; ?></td>
                        <td class="acao">
                            <a href="javascript:void(0);" onclick="showEditModal(<?php echo htmlspecialchars(json_encode($professor)); ?>)">
                                <i class="fas fa-edit" style="font-size: 18px;"></i>
                            </a>
                            <a href="javascript:void(0);" onclick="confirmarExclusao(<?php echo $professor['id']; ?>)">
                                <i class="fas fa-trash-alt" style="font-size: 18px; color: #FF0000;" ></i>
                            </a>
                            <form id="delete-form-<?php echo $professor['id']; ?>" method="POST" style="display: none;">
                                <input type="hidden" name="delete_id" value="<?php echo $professor['id']; ?>">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal de Edição -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Editar Professor</h2>
            <form method="POST" action="">
                <input type="hidden" name="update" value="1">
                <input type="hidden" name="id" id="editId">
                <div>
                    <label for="editNome">Nome</label>
                    <input type="text" id="editNome" name="nome" required>
                </div>
                <div>
                    <label for="editEmail">E-mail</label>
                    <input type="email" id="editEmail" name="email" required>
                </div>
                <div>
                    <label for="editTelefone">Telefone</label>
                    <input type="tel" id="editTelefone" name="telefone" required>
                </div>
                <div>
                    <label for="editTitulacao">Titulação</label>
                    <select name="idtitulacao" id="editTitulacao" required>
                        <option value="">Selecione</option>
                        <?php foreach ($titulacoes as $titulacao): ?>
                            <option value="<?php echo $titulacao['idtitulacao']; ?>"><?php echo $titulacao['nome']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit">Atualizar</button>
            </form>
        </div>
    </div>

    <script>
        let professorIdParaExcluir = null;

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
            professorIdParaExcluir = id;
            document.getElementById('confirmModal').style.display = 'block';
        }

        function fecharModalConfirmacao() {
            document.getElementById('confirmModal').style.display = 'none';
            professorIdParaExcluir = null;
        }

        function confirmarExclusaoFinal() {
            if (professorIdParaExcluir) {
                document.getElementById('delete-form-' + professorIdParaExcluir).submit();
            }
            fecharModalConfirmacao();
        }

        function showEditModal(professor) {
            document.getElementById('editId').value = professor.id;
            document.getElementById('editNome').value = professor.nome;
            document.getElementById('editEmail').value = professor.email;
            document.getElementById('editTelefone').value = professor.telefone;
            document.getElementById('editTitulacao').value = professor.idtitulacao;
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
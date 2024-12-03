<?php
// Não precisa iniciar sessão aqui pois já foi iniciada no 3.3.php
include("config.php");

// Processamento do formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_iddisciplina'])) {
        // Lógica para exclusão
        $delete_id = $_POST['delete_iddisciplina'];
        $query = $conn->prepare("DELETE FROM disciplina WHERE iddisciplina = :iddisciplina");
        $query->bindParam(':iddisciplina', $delete_id);

        if ($query->execute()) {
            $_SESSION['mensagem'] = "Disciplina excluída com sucesso";
            $_SESSION['tipo_alerta'] = "success";
        } else {
            $_SESSION['mensagem'] = "Erro ao excluir a disciplina";
            $_SESSION['tipo_alerta'] = "error";
        }
        header("Location: 3.3.php?page=listar_disciplinas");
        exit();
    } elseif (isset($_POST['update']) && !empty($_POST['id']) && !empty($_POST['nome']) && !empty($_POST['idprofessor'])) {
        // Lógica para atualização
        $id = $_POST['id'];
        $nome = $_POST['nome'];
        $idprofessor = $_POST['idprofessor'];
        $query = $conn->prepare("UPDATE disciplina SET nome = :nome, idprofessor = :idprofessor WHERE iddisciplina = :id");
        $query->bindParam(':id', $id);
        $query->bindParam(':nome', $nome);
        $query->bindParam(':idprofessor', $idprofessor);
        
        if ($query->execute()) {
            $_SESSION['mensagem'] = "Disciplina atualizada com sucesso";
            $_SESSION['tipo_alerta'] = "success";
        } else {
            $_SESSION['mensagem'] = "Erro ao atualizar a disciplina.";
            $_SESSION['tipo_alerta'] = "error";
        }
        header("Location: 3.3.php?page=listar_disciplinas");
        exit();
    } elseif (!empty($_POST['nome']) && !empty($_POST['idprofessor'])) {
        // Lógica para cadastro
        try {
            $nome = $_POST['nome'];
            $idprofessor = $_POST['idprofessor'];
            
            // Verifica se o professor existe
            $query = $conn->prepare("SELECT COUNT(*) FROM professor WHERE id = :idprofessor");
            $query->bindParam(':idprofessor', $idprofessor);
            $query->execute();
            $professorExists = $query->fetchColumn();
            
            if ($professorExists > 0) {
                // Verifica se a disciplina já está cadastrada
                $query = $conn->prepare("SELECT COUNT(*) FROM disciplina WHERE nome = :nome AND idprofessor = :idprofessor");
                $query->bindParam(':nome', $nome);
                $query->bindParam(':idprofessor', $idprofessor);
                $query->execute();
                $count = $query->fetchColumn();

                if ($count > 0) {
                    $_SESSION['mensagem'] = "Disciplina já cadastrada";
                    $_SESSION['tipo_alerta'] = "error";
                } else {
                    // Cadastra a nova disciplina
                    $query = $conn->prepare("INSERT INTO disciplina (nome, idprofessor) VALUES (:nome, :idprofessor)");
                    $query->bindParam(':nome', $nome);
                    $query->bindParam(':idprofessor', $idprofessor);
                    
                    if ($query->execute()) {
                        $_SESSION['mensagem'] = "Disciplina cadastrada com sucesso";
                        $_SESSION['tipo_alerta'] = "success";
                    } else {
                        $_SESSION['mensagem'] = "Erro ao cadastrar disciplina.";
                        $_SESSION['tipo_alerta'] = "error";
                    }
                }
            } else {
                $_SESSION['mensagem'] = "Professor selecionado não encontrado.";
                $_SESSION['tipo_alerta'] = "error";
            }
        } catch (PDOException $e) {
            $_SESSION['mensagem'] = "Erro ao cadastrar disciplina: " . $e->getMessage();
            $_SESSION['tipo_alerta'] = "error";
        }
        header("Location: 3.3.php?page=listar_disciplinas");
        exit();
    }
}

// Consultas para carregar dados
$sql = "SELECT d.iddisciplina, d.nome, d.idprofessor, p.nome AS professor_nome 
        FROM disciplina d 
        JOIN professor p ON d.idprofessor = p.id";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar lista de professores para o select
$query = $conn->query("SELECT id, nome FROM professor ORDER BY nome ASC");
$professores = $query->fetchAll(PDO::FETCH_ASSOC);

// Verifica se existe mensagem na sessão
if (isset($_SESSION['mensagem'])) {
    $mensagem = $_SESSION['mensagem'];
    $tipo_alerta = $_SESSION['tipo_alerta'];
    
    // Limpa as mensagens da sessão
    unset($_SESSION['mensagem']);
    unset($_SESSION['tipo_alerta']);
}
?>
<!-- Resto do código HTML permanece igual -->
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
        <p>Tem certeza que deseja excluir esta disciplina?</p>
        <div class="confirm-modal-buttons">
            <button class="btn-confirmar" onclick="confirmarExclusaoFinal()">Confirmar</button>
            <button class="btn-cancelar" onclick="fecharModalConfirmacao()">Cancelar</button>
        </div>
    </div>
</div>

<!-- Modal de Cadastro -->
<div id="cadastroModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="fecharModalCadastro()">&times;</span>
        <h2>Nova Disciplina</h2>
        <form method="POST" action="">
            <div>
                <label for="nome">Disciplina</label>
                <input type="text" name="nome" id="nome" required>
            </div>
            <div>
                <label for="idprofessor">Professor</label>
                <select name="idprofessor" required>
                    <option value="">Selecione</option>
                    <?php foreach ($professores as $professor): ?>
                        <option value="<?php echo $professor['id']; ?>">
                            <?php echo $professor['nome']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit">Cadastrar</button>
        </form>
    </div>
</div>

<div class="container">
    <h2>Lista de Disciplinas</h2>
    
    <button onclick="abrirModalCadastro()" class="btn-novo">
        <i class="fas fa-plus"></i> Nova Disciplina
    </button>

    <table>
        <thead>
            <tr>
                <th>Disciplina</th>
                <th>Professor</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($result as $disciplina): ?>
                <tr>
                    <td><?php echo $disciplina['nome']; ?></td>
                    <td><?php echo $disciplina['professor_nome']; ?></td>
                    <td class="acao">
                        <a href="javascript:void(0);" onclick="confirmarExclusao(<?php echo $disciplina['iddisciplina']; ?>)">
                            <i class="fas fa-trash-alt" style="color: #174650; font-size: 17px;"></i>
                        </a>
                        <form id="delete-form-<?php echo $disciplina['iddisciplina']; ?>" method="POST" style="display: none;">
                            <input type="hidden" name="delete_iddisciplina" value="<?php echo $disciplina['iddisciplina']; ?>">
                        </form>
                        <a href="javascript:void(0);" onclick="showEditModal(<?php echo htmlspecialchars(json_encode($disciplina)); ?>)">
                            <i class="fas fa-edit" style="color: #174650; font-size: 17px;"></i>
                        </a>
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
        <h2>Editar Disciplina</h2>
        <form method="POST" action="">
            <input type="hidden" id="editId" name="id">
            <div>
                <label for="editNome">Disciplina</label>
                <input type="text" id="editNome" name="nome" required>
            </div>
            <div>
                <label for="editProfessor">Professor</label>
                <select id="editProfessor" name="idprofessor" required>
                    <option value="">Selecione</option>
                    <?php foreach ($professores as $professor): ?>
                        <option value="<?php echo $professor['id']; ?>"><?php echo $professor['nome']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="update">Atualizar</button>
        </form>
    </div>
</div>

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

    .btn-novo {
        background-color: #174650;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin-bottom: 20px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .btn-novo:hover {
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

    /* Estilos dos modais */
    .modal, .confirm-modal, #mensagemModal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-content, .confirm-modal-content, .modal-content-mensagem {
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

    .modal-content {
        width: 80%;
        max-width: 500px;
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

    /* Estilos do formulário no modal */
    .modal form {
        display: grid;
        grid-template-columns: 1fr;
        gap: 15px;
        padding: 20px 0;
    }

    .modal form div {
        margin-bottom: 15px;
    }

    .modal label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }

    .modal input[type="text"],
    .modal select {
        width: 100%;
        padding: 8px;
        border: 1px solid #5aa2b0;
        border-radius: 4px;
        box-sizing: border-box;
    }

    .modal button {
        background-color: #174650;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        width: auto;
    }

    .modal button:hover {
        background-color: #5aa2b0;
    }

    .success { color: #4CAF50; }
    .error { color: #f44336; }
</style>

<script>
    let disciplinaIdParaExcluir = null;

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

    function abrirModalCadastro() {
        document.getElementById('cadastroModal').style.display = 'block';
    }

    function fecharModalCadastro() {
        document.getElementById('cadastroModal').style.display = 'none';
    }

    function confirmarExclusao(id) {
        disciplinaIdParaExcluir = id;
        document.getElementById('confirmModal').style.display = 'block';
    }

    function fecharModalConfirmacao() {
        document.getElementById('confirmModal').style.display = 'none';
        disciplinaIdParaExcluir = null;
    }

    function confirmarExclusaoFinal() {
        if (disciplinaIdParaExcluir) {
            document.getElementById('delete-form-' + disciplinaIdParaExcluir).submit();
        }
        fecharModalConfirmacao();
    }

    function showEditModal(disciplina) {
        document.getElementById('editId').value = disciplina.iddisciplina;
        document.getElementById('editNome').value = disciplina.nome;
        document.getElementById('editProfessor').value = disciplina.idprofessor;
        document.getElementById('editModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    window.onclick = function(event) {
        const mensagemModal = document.getElementById('mensagemModal');
        const confirmModal = document.getElementById('confirmModal');
        const editModal = document.getElementById('editModal');
        const cadastroModal = document.getElementById('cadastroModal');
        
        if (event.target == mensagemModal) {
            fecharModal();
        }
        if (event.target == confirmModal) {
            fecharModalConfirmacao();
        }
        if (event.target == editModal) {
            closeModal();
        }
        if (event.target == cadastroModal) {
            fecharModalCadastro();
        }
    }

    <?php if (!empty($mensagem)): ?>
        mostrarModal("<?php echo addslashes($mensagem); ?>", "<?php echo $tipo_alerta; ?>");
    <?php endif; ?>
</script>
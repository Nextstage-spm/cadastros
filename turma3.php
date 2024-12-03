<?php 
include("config.php");

$mensagem = ""; 
$tipo_alerta = "";
$editMode = false;
$turmaData = [];

// Editar turma
if (isset($_POST['update'])) {
    if (isset($_POST['idturma'], $_POST['nome'], $_POST['idperiodo'], $_POST['idcursos'], $_POST['idturno'])) {
        $idturma = $_POST['idturma'];
        $nome = $_POST['nome'];
        $idperiodo = $_POST['idperiodo']; 
        $idturno = $_POST['idturno'];
        $idcursos = $_POST['idcursos'];  

        try {
            $query = $conn->prepare("UPDATE turma SET nome = :nome, idperiodo = :idperiodo, idcursos = :idcursos, idturno = :idturno WHERE idturma = :idturma");
            $query->bindParam(':nome', $nome);
            $query->bindParam(':idperiodo', $idperiodo);  
            $query->bindParam(':idcursos', $idcursos);  
            $query->bindParam(':idturno', $idturno);
            $query->bindParam(':idturma', $idturma);

            if ($query->execute()) {
                $mensagem = "Turma atualizada com sucesso!";
                $tipo_alerta = "success";
            } else {
                $mensagem = "Erro ao atualizar a turma!";
                $tipo_alerta = "error";
            }
        } catch (PDOException $e) {
            $mensagem = "Erro ao atualizar: " . $e->getMessage();
            $tipo_alerta = "error";
        }
    }
}

// Excluir turma
if (isset($_POST['delete_id'])) {
    $idturma = $_POST['delete_id'];

    try {
        $query = $conn->prepare("DELETE FROM turma WHERE idturma = :idturma");
        $query->bindParam(':idturma', $idturma);
        
        if ($query->execute()) {
            $mensagem = "Turma excluída com sucesso!";
            $tipo_alerta = "success";
        } else {
            $mensagem = "Erro ao excluir a turma!";
            $tipo_alerta = "error";
        }
    } catch (PDOException $e) {
        $mensagem = "Erro ao excluir: " . $e->getMessage();
        $tipo_alerta = "error";
    }
}

// Buscar dados da turma para edição
if (isset($_GET['edit_id'])) {
    $idturma = $_GET['edit_id'];
    $editMode = true;

    $query = $conn->prepare("SELECT * FROM turma WHERE idturma = :idturma");
    $query->bindParam(':idturma', $idturma);
    $query->execute();
    $turmaData = $query->fetch(PDO::FETCH_ASSOC);
}

// Cadastrar turma
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['update']) && !isset($_POST['delete_id'])) {
    if (isset($_POST['nome']) && !empty($_POST['nome']) && 
        isset($_POST['idperiodo']) && !empty($_POST['idperiodo']) && 
        isset($_POST['idcursos']) && !empty($_POST['idcursos']) &&
        isset($_POST['idturno']) && !empty($_POST['idturno'])) {
        
        $nome = $_POST['nome'];
        $idperiodo = $_POST['idperiodo']; 
        $idturno = $_POST['idturno'];
        $idcursos = $_POST['idcursos'];  

        try {
            $query = $conn->prepare("INSERT INTO turma (nome, idperiodo, idcursos, idturno) VALUES (:nome, :idperiodo, :idcursos, :idturno)");
            $query->bindParam(':nome', $nome);
            $query->bindParam(':idperiodo', $idperiodo);  
            $query->bindParam(':idcursos', $idcursos);  
            $query->bindParam(':idturno', $idturno);

            if ($query->execute()) {
                $mensagem = "Turma cadastrada com sucesso!";
                $tipo_alerta = "success";
            } else {
                $mensagem = "Erro ao cadastrar turma";
                $tipo_alerta = "error";
            }
        } catch (PDOException $e) {
            $mensagem = "Erro ao cadastrar: " . $e->getMessage();
            $tipo_alerta = "error";
        }
    } else {
        $mensagem = "Campos inválidos!";
        $tipo_alerta = "error";
    }
}

// Consulta para exibir registros com nomes
$sql = "SELECT t.idturma, t.nome, p.nome AS periodo, c.nome AS curso, tu.nome AS turno 
        FROM turma t
        JOIN periodo p ON t.idperiodo = p.id
        JOIN cursos c ON t.idcursos = c.id
        JOIN turno tu ON t.idturno = tu.id";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Turma</title>
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
        input[type=""],
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
            <button onclick="fecharModal()">OK</button>
        </div>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div id="confirmModal" class="confirm-modal">
        <div class="confirm-modal-content">
            <p>Tem certeza que deseja excluir esta turma?</p>
            <div class="confirm-modal-buttons">
                <button class="btn-confirmar" onclick="confirmarExclusaoFinal()">Confirmar</button>
                <button class="btn-cancelar" onclick="fecharModalConfirmacao()">Cancelar</button>
            </div>
        </div>
    </div>

    <div class="container">
        <h2>Cadastro de Turma</h2>

        <!-- Formulário de Cadastro -->
        <form method="POST" action="">
            <div>
                <label for="nome">Turma</label>
                <input type="text" id="nome" name="nome" placeholder="Turma" required>
            </div>
            <div>
                <label for="idturno">Turno</label>
                <select name="idturno" id="idturno" required>
                    <option value="">Selecione</option>
                    <?php
                    $query = $conn->query("SELECT id, nome FROM turno ORDER BY nome ASC");
                    $registros = $query->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($registros as $option) {
                        echo "<option value=\"{$option['id']}\">{$option['nome']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div>
                <label for="idperiodo">Período</label>
                <select name="idperiodo" id="idperiodo" required>
                    <option value="">Selecione</option>
                    <?php
                    $query = $conn->query("SELECT id, nome FROM periodo ORDER BY nome ASC");
                    $registros = $query->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($registros as $option) {
                        echo "<option value=\"{$option['id']}\">{$option['nome']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div>
                <label for="idcursos">Curso</label>
                <select name="idcursos" id="idcursos" required>
                    <option value="">Selecione</option>
                    <?php
                    $query = $conn->query("SELECT id, nome FROM cursos ORDER BY nome ASC");
                    $registros = $query->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($registros as $option) {
                        echo "<option value=\"{$option['id']}\">{$option['nome']}</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit">Cadastrar</button>
        </form>

        <!-- Listagem de Turmas -->
        <table>
            <thead>
                <tr>
                    <th>Turma</th>
                    <th>Turno</th>
                    <th>Período</th>
                    <th>Curso</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result as $turma): ?>
                    <tr>
                        <td><?php echo $turma['nome']; ?></td>
                        <td><?php echo $turma['turno']; ?></td>
                        <td><?php echo $turma['periodo']; ?></td>
                        <td><?php echo $turma['curso']; ?></td>
                        <td class="acao">
                            <a href="?edit_id=<?php echo $turma['idturma']; ?>" class="edit-btn">
                                <i class="fas fa-edit" style="color: #174650; font-size: 18px;"></i>
                            </a>
                            <a href="#" onclick="confirmarExclusao(<?php echo $turma['idturma']; ?>)">
                                <i class="fas fa-trash-alt" style="font-size: 18px; color: #FF0000;"></i>
                            </a>
                            <form id="delete-form-<?php echo $turma['idturma']; ?>" method="POST" style="display:none;">
                                <input type="hidden" name="delete_id" value="<?php echo $turma['idturma']; ?>">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Modal de Edição -->
        <?php if ($editMode && !empty($turmaData)): ?>
            <div class="modal" id="editModal" style="display: block;">
                <div class="modal-content">
                    <span class="close" onclick="document.getElementById('editModal').style.display='none'">&times;</span>
                    Editar Turma
                    <form method="POST" action="">
                        <input type="hidden" name="idturma" value="<?php echo $turmaData['idturma']; ?>">
                        <div>
                            <label>Nome</label>
                            <input type="text" name="nome" value="<?php echo $turmaData['nome']; ?>">
                        </div>
                        <div>
                            <label>Turno</label>
                            <select name="idturno" required>
                                <option value="">Selecione</option>
                                <?php
                                $query = $conn->query("SELECT id, nome FROM turno ORDER BY nome ASC");
                                $turnos = $query->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($turnos as $turno) {
                                    $selected = ($turno['id'] == $turmaData['idturno']) ? 'selected' : '';
                                    echo "<option value=\"{$turno['id']}\" $selected>{$turno['nome']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div>
                            <label>Período</label>
                            <select name="idperiodo" required>
                                <option value="">Selecione</option>
                                <?php
                                $query = $conn->query("SELECT id, nome FROM periodo ORDER BY nome ASC");
                                $periodos = $query->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($periodos as $periodo) {
                                    $selected = ($periodo['id'] == $turmaData['idperiodo']) ? 'selected' : '';
                                    echo "<option value=\"{$periodo['id']}\" $selected>{$periodo['nome']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div>
                            <label>Curso</label>
                            <select name="idcursos" required>
                                <option value="">Selecione</option>
                                <?php
                                $query = $conn->query("SELECT id, nome FROM cursos ORDER BY nome ASC");
                                $cursos = $query->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($cursos as $curso) {
                                    $selected = ($curso['id'] == $turmaData['idcursos']) ? 'selected' : '';
                                    echo "<option value=\"{$curso['id']}\" $selected>{$curso['nome']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <button type="submit" name="update">Atualizar</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        let turmaIdParaExcluir = null;

        // Função para mostrar o modal de mensagem
        function mostrarModal(mensagem, tipo) {
            const modal = document.getElementById('mensagemModal');
            const mensagemTexto = document.getElementById('mensagemTexto');
            
            mensagemTexto.textContent = mensagem;
            mensagemTexto.className = tipo;
            
            modal.style.display = 'block';
        }

        // Função para fechar o modal de mensagem
        function fecharModal() {
            document.getElementById('mensagemModal').style.display = 'none';
        }

        // Função para mostrar o modal de confirmação
        function confirmarExclusao(id) {
            turmaIdParaExcluir = id;
            document.getElementById('confirmModal').style.display = 'block';
        }

        // Função para fechar o modal de confirmação
        function fecharModalConfirmacao() {
            document.getElementById('confirmModal').style.display = 'none';
            turmaIdParaExcluir = null;
        }

        // Função para confirmar a exclusão final
        function confirmarExclusaoFinal() {
            if (turmaIdParaExcluir) {
                document.getElementById('delete-form-' + turmaIdParaExcluir).submit();
            }
            fecharModalConfirmacao();
        }

        // Fechar modais ao clicar fora
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
                editModal.style.display = 'none';
            }
        }

        // Mostrar mensagem se existir
        <?php if (!empty($mensagem)): ?>
            mostrarModal("<?php echo addslashes($mensagem); ?>", "<?php echo $tipo_alerta; ?>");
        <?php endif; ?>
    </script>
</body>
</html>
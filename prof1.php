<?php 
include("config.php");

$mensagem = "";
$tipo_alerta = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificar se a titulação existe antes de registrar ou atualizar o professor
    if (isset($_POST['idtitulacao']) && !empty($_POST['idtitulacao'])) {
        $idtitulacao = $_POST['idtitulacao'];
        
        // Verificar se a titulação existe
        $stmt = $conn->prepare("SELECT COUNT(*) FROM titulacao WHERE idtitulacao = :idtitulacao");
        $stmt->bindParam(':idtitulacao', $idtitulacao);
        $stmt->execute();
        $titulacaoCount = $stmt->fetchColumn();

        if ($titulacaoCount == 0) {
            $mensagem = "A titulação selecionada não existe!";
            $tipo_alerta = "error";
        } else {
            // Registro do professor
            if (isset($_POST['nome']) && !empty($_POST['nome']) && isset($_POST['email']) && !empty($_POST['email'])
            && isset($_POST['telefone']) && !empty($_POST['telefone']) && !isset($_POST['update'])) {
                
                // Atribuindo os valores do POST
                $nome_professor = $_POST['nome'];
                $email_professor = $_POST['email'];
                $telefone_professor = $_POST['telefone'];

                try {
                    $query = $conn->prepare("INSERT INTO professor (nome, email, telefone, idtitulacao) VALUES (:nome, :email, :telefone, :idtitulacao)");
                    $query->bindParam(':nome', $nome_professor);
                    $query->bindParam(':email', $email_professor);
                    $query->bindParam(':telefone', $telefone_professor);
                    $query->bindParam(':idtitulacao', $idtitulacao);

                    if ($query->execute()) {
                        $mensagem = "Cadastro realizado com sucesso!";
                        $tipo_alerta = "success";
                    } else {
                        $mensagem = "Erro ao cadastrar o professor.";
                        $tipo_alerta = "error";
                    }
                } catch (PDOException $e) {
                    error_log("Erro ao cadastrar o professor: " . $e->getMessage());
                    $mensagem = "Erro ao cadastrar o professor: " . $e->getMessage();
                    $tipo_alerta = "error";
                }
            }

            // Atualização do professor
            if (isset($_POST['update'])) {
                $id = $_POST['id'];
                $nome_professor = $_POST['nome'];
                $email_professor = $_POST['email'];
                $telefone_professor = $_POST['telefone'];

                try {
                    $query = $conn->prepare("UPDATE professor SET nome = :nome, email = :email, telefone = :telefone, idtitulacao = :idtitulacao WHERE id = :id");
                    $query->bindParam(':nome', $nome_professor);
                    $query->bindParam(':email', $email_professor);
                    $query->bindParam(':telefone', $telefone_professor);
                    $query->bindParam(':idtitulacao', $idtitulacao);
                    $query->bindParam(':id', $id);

                    if ($query->execute()) {
                        $mensagem = "Atualização realizada com sucesso!";
                        $tipo_alerta = "success";
                    } else {
                        $mensagem = "Erro ao atualizar o professor.";
                        $tipo_alerta = "error";
                    }
                } catch (PDOException $e) {
                    error_log("Erro ao atualizar o professor: " . $e->getMessage());
                    $mensagem = "Erro ao atualizar o professor: " . $e->getMessage();
                    $tipo_alerta = "error";
                }
            }
        }
    }
    
    // Deletar professor
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
            error_log("Erro ao excluir o professor: " . $e->getMessage());
            $mensagem = "Erro ao excluir o professor: " . $e->getMessage();
            $tipo_alerta = "error";
        }
    }
}

$sql = "SELECT id, nome, email, telefone, idtitulacao FROM professor";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            margin-top: -5px;
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
    margin-bottom: 5px; /* Espaço entre o label e o input */
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
    font-size: 10px;
    margin-bottom: 15px; /* Espaço entre os campos de input */
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
            margin-left: 400px;
            margin-top: 30px;
            margin-bottom: 35px;
        }

        button:hover {
            background-color: #5aa2b0;
        }

        .error {
            color: red;
            margin-top: 5px;
            font-size: 14px;
        }

        table {
            width: 100%;
            height: 50px;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 5px;
        }

        th {
            background-color: #5aa2b0;
            color: white;
        }

        /* Estilos do modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 60%;
            border-radius: 10px;
            text-align: center;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Cadastro de Professor</h2>
        <?php if ($mensagem): ?>
            <div class="alerta <?= $tipo_alerta ?>">
                <?= $mensagem ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div>
            <label for="nome">Nome</label>
            <input type="text" id="nome" name="nome" required>
        </div>

        <div>
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div>
            <label for="telefone">Telefone</label>
            <input type="tel" id="telefone" name="telefone" required>
        </div>

        <div>
            <label for="idtitulacao">Titulação</label>
            <select name="idtitulacao" id="idtitulacao" required>
                <option value="">Selecione</option>
                <?php
                $query = $conn->query("SELECT idtitulacao, nome FROM titulacao ORDER BY nome ASC");
                $registros = $query->fetchAll(PDO::FETCH_ASSOC);
                foreach ($registros as $registro) {
                    echo "<option value='{$registro['idtitulacao']}'>{$registro['nome']}</option>";
                }
                ?>
            </select>
            </div>
            </form>
            <button type="submit">Cadastrar</button>

        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Telefone</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result as $professor): ?>
                <tr>
                    <td><?= $professor['nome'] ?></td>
                    <td><?= $professor['email'] ?></td>
                    <td><?= $professor['telefone'] ?></td>
                    <td class="acao">
                        <a href="#" onclick="openModal(<?= $professor['id'] ?>, '<?= $professor['nome'] ?>', '<?= $professor['email'] ?>', '<?= $professor['telefone'] ?>', <?= $professor['idtitulacao'] ?>)">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="#" onclick="confirmDelete(<?= $professor['id'] ?>)">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Modal de Edição -->
        <div id="editModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h3>Editar Professor</h3>
                <form id="editForm" method="POST" action="">

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
                        <?php
                        $query = $conn->query("SELECT idtitulacao, nome FROM titulacao ORDER BY nome ASC");
                        $registros = $query->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($registros as $registro) {
                            echo "<option value='{$registro['idtitulacao']}'>{$registro['nome']}</option>";
                        }
                        ?>
                    </select>
                    </div>
                    <button type="submit">Atualizar</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Função para abrir o modal e preencher os dados
        function openModal(id, nome, email, telefone, titulacao) {
            document.getElementById('editId').value = id;
            document.getElementById('editNome').value = nome;
            document.getElementById('editEmail').value = email;
            document.getElementById('editTelefone').value = telefone;
            document.getElementById('editTitulacao').value = titulacao;
            document.getElementById('editModal').style.display = 'block';
        }

        // Função para fechar o modal
        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Função para confirmar exclusão
        function confirmDelete(id) {
            if (confirm("Tem certeza que deseja excluir este professor?")) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = ''; // Ação que chama a mesma página

                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'delete_id';
                input.value = id;

                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();  // Envia o formulário de exclusão
            }
        }
    </script>
</body>
</html>

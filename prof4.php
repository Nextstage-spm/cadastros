<?php 
include("config.php");

$mensagem = "";
$tipo_alerta = "";

// Processamento do formulário (cadastrar, atualizar, deletar)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'create') {
            // Cadastro de novo professor
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $telefone = $_POST['telefone'];
            $idtitulacao = $_POST['idtitulacao'];

            $stmt = $conn->prepare("INSERT INTO professor (nome, email, telefone, idtitulacao) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$nome, $email, $telefone, $idtitulacao])) {
                $mensagem = "Professor cadastrado com sucesso!";
                $tipo_alerta = "success";
            } else {
                $mensagem = "Erro ao cadastrar o professor.";
                $tipo_alerta = "error";
            }
        } elseif ($action === 'update') {
            // Atualização de professor
            $id = $_POST['id'];
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $telefone = $_POST['telefone'];
            $idtitulacao = $_POST['idtitulacao'];

            $stmt = $conn->prepare("UPDATE professor SET nome = ?, email = ?, telefone = ?, idtitulacao = ? WHERE id = ?");
            if ($stmt->execute([$nome, $email, $telefone, $idtitulacao, $id])) {
                $mensagem = "Professor atualizado com sucesso!";
                $tipo_alerta = "success";
            } else {
                $mensagem = "Erro ao atualizar o professor.";
                $tipo_alerta = "error";
            }
        } elseif ($action === 'delete') {
            // Exclusão de professor
            $id = $_POST['id'];
            $stmt = $conn->prepare("DELETE FROM professor WHERE id = ?");
            if ($stmt->execute([$id])) {
                $mensagem = "Professor excluído com sucesso!";
                $tipo_alerta = "success";
            } else {
                $mensagem = "Erro ao excluir o professor.";
                $tipo_alerta = "error";
            }
        }
    }
}

// Obter dados para exibição na tabela
$sql = "SELECT p.id, p.nome, p.email, p.telefone, t.nome AS titulacao FROM professor p INNER JOIN titulacao t ON p.idtitulacao = t.idtitulacao";
$stmt = $conn->prepare($sql);
$stmt->execute();
$professores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Professores</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
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

        .acao {
            display: flex;
            gap: 10px;
            height: 36px;
        }

        .acao a {
            text-decoration: none;
            padding: 5px; /* Removido fundo azul */
            transition: background-color 0.3s;
            color: #174650;

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
        .acao {
            display: flex;
            gap: 10px;
        }

        .acao a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: #5aa2b0;
            color: white;
            transition: background-color 0.3s;
        }

        .acao a:hover {
            background-color: #174650;
        }

        .acao a i {
            font-size: 18px;
        }

         /* Estilos do modal de mensagem */
         #mensagemModal {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ccc;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            text-align: center;
            width: 300px;
        }
        #mensagemModal button {
            margin-top: 10px;
            padding: 5px 10px;
            cursor: pointer;
        }

        /* Overlay para manter o fundo visível */
        #modalOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            z-index: 900;
        }

        /* Esconde o modal e o overlay inicialmente */
        #mensagemModal, #modalOverlay {
            display: none;
        }
    </style>
</head>

<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Cadastro de Professores</h2>

    <?php if ($mensagem): ?>
        <div class="alert alert-<?= $tipo_alerta === 'success' ? 'success' : 'danger'; ?>">
            <?= $mensagem ?>
        </div>
    <?php endif; ?>

    <!-- Formulário de Cadastro -->
    <form method="POST" class="mb-4">
        <input type="hidden" name="action" value="create">
        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" name="nome" id="nome" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="telefone" class="form-label">Telefone</label>
            <input type="tel" name="telefone" id="telefone" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="idtitulacao" class="form-label">Titulação</label>
            <select name="idtitulacao" id="idtitulacao" class="form-select" required>
                <option value="">Selecione</option>
                <?php
                $query = $conn->query("SELECT idtitulacao, nome FROM titulacao");
                while ($titulacao = $query->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$titulacao['idtitulacao']}'>{$titulacao['nome']}</option>";
                }
                ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Cadastrar</button>
    </form>

    <!-- Tabela de Professores -->
    <table class="table table-striped">
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
            <?php foreach ($professores as $professor): ?>
            <tr>
                <td><?= $professor['nome'] ?></td>
                <td><?= $professor['email'] ?></td>
                <td><?= $professor['telefone'] ?></td>
                <td><?= $professor['titulacao'] ?></td>
                <td>
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $professor['id'] ?>">Editar</button>
                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $professor['id'] ?>">Excluir</button>
                </td>
            </tr>

            <!-- Modal de Edição -->
            <div class="modal fade" id="editModal<?= $professor['id'] ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="id" value="<?= $professor['id'] ?>">
                            <div class="modal-header">
                                <h5 class="modal-title">Editar Professor</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="nome" class="form-label">Nome</label>
                                    <input type="text" name="nome" class="form-control" value="<?= $professor['nome'] ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="<?= $professor['email'] ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="telefone" class="form-label">Telefone</label>
                                    <input type="tel" name="telefone" class="form-control" value="<?= $professor['telefone'] ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="idtitulacao" class="form-label">Titulação</label>
                                    <select name="idtitulacao" class="form-select" required>
                                        <?php
                                        $query = $conn->query("SELECT idtitulacao, nome FROM titulacao");
                                        while ($titulacao = $query->fetch(PDO::FETCH_ASSOC)) {
                                            $selected = $professor['idtitulacao'] == $titulacao['idtitulacao'] ? 'selected' : '';
                                            echo "<option value='{$titulacao['idtitulacao']}' $selected>{$titulacao['nome']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Salvar</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal de Exclusão -->
            <div class="modal fade" id="deleteModal<?= $professor['id'] ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $professor['id'] ?>">
                            <div class="modal-header">
                                <h5 class="modal-title">Excluir Professor</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p>Tem certeza de que deseja excluir o professor <strong><?= $professor['nome'] ?></strong>?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-danger">Excluir</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
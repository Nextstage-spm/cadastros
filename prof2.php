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
            if (isset($_POST['update']) && !empty($_POST['id'])) {
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

// Atualizar consulta para incluir o nome da titulação
$sql = "SELECT professor.id, professor.nome, professor.email, professor.telefone, titulacao.nome AS titulacao_nome 
        FROM professor 
        JOIN titulacao ON professor.idtitulacao = titulacao.idtitulacao";
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
</head>
<style>
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
            margin-left: 10px;
        }
        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="date"],
        select {
            width: 90%;
            padding: 10px;
            border: 1px solid #5aa2b0;
            border-radius: 12px;
            box-sizing: border-box;
            font-size: 10px;
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
            margin-left: 260px;
            justify-content: center;
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
            width: 500px; 
            border-collapse: collapse;
            margin: 20px auto;   
            margin-left: 140px;
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
            height: 36px;
        }
        .acao a {
            text-decoration: none;
            padding: 5px;
            transition: background-color 0.3s;
            color: #174650;
        }
        /* Estilos do modal */
        .modal {
            display: none; /* Escondido por padrão */
            position: fixed;
            z-index: 1; /* Fica em cima */
            left: 0;
            top: 0;
            width: 100%; /* Largura total */
            height: 100%; /* Altura total */
            overflow: auto; /* Habilita scroll se necessário */
            background-color: rgba(0,0,0,0.4); /* Fundo preto com opacidade */
            padding-top: 60px; /* Espaço acima do modal */
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto; /* 15% da parte superior e centraliza */
            padding: 20px;
            border: 1px solid #888;
            width: 80%; /* Largura do modal */
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
<body>
    <div class="container">
        <h2>Cadastro de Professor</h2>
        
        <!-- Exibição da mensagem de feedback -->
        <?php if (!empty($mensagem)): ?>
            <div class="alert <?php echo $tipo_alerta; ?>"><?php echo $mensagem; ?></div>
        <?php endif; ?>

        <!-- Formulário de cadastro e atualização -->
        <form method="POST" action="">
            <input type="hidden" name="id" id="id"> <!-- Campo para ID ao atualizar -->
            <input type="hidden" name="update" id="update"> <!-- Identifica operação de atualização -->

            <div>
                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" placeholder="Nome do Professor" required>
            </div>

            <div>
                <label for="email">E-mail</label>
                <input type="text" id="email" name="email" placeholder="E-mail do Professor" required>
            </div>

            <div>
                <label for="telefone">Telefone</label>
                <input type="text" id="telefone" name="telefone" placeholder="Telefone" required>
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

            <button type="submit">Cadastrar</button>
        </form>

        <!-- Tabela de professores -->
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Telefone</th>
                    <th>Titulação</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($result as $row): ?>
                <tr>
                    <td><?php echo $row['nome']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['telefone']; ?></td>
                    <td><?php echo $row['titulacao_nome']; ?></td> <!-- Exibindo o nome da titulação -->
                    <td>
                        <button onclick="openEditModal(<?php echo $row['id']; ?>, '<?php echo addslashes($row['nome']); ?>', '<?php echo addslashes($row['email']); ?>', '<?php echo addslashes($row['telefone']); ?>', <?php echo $row['idtitulacao']; ?>)">Editar</button>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                            <button type="submit">Excluir</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        function openEditModal(id, nome, email, telefone, idtitulacao) {
            document.getElementById('id').value = id;
            document.getElementById('nome').value = nome;
            document.getElementById('email').value = email;
            document.getElementById('telefone').value = telefone;
            document.getElementById('idtitulacao').value = idtitulacao;
            document.getElementById('update').value = 'true';
        }
    </script>
</body>
</html>
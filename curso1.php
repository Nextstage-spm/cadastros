<?php
include("config.php"); // Inclui a conexão com o banco de dados

$mensagem = ""; // Variável para armazenar a mensagem de erro ou sucesso

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_id'])) {
        // Lógica para exclusão
        $delete_id = $_POST['delete_id'];
        $query = $conn->prepare("DELETE FROM cursos WHERE id = :id");
        $query->bindParam(':id', $delete_id);

        if ($query->execute()) {
            $mensagem = "Curso excluído com sucesso";
        } else {
            $mensagem = "Erro ao excluir o curso";
        }
    } elseif (isset($_POST['update'])) {
        // Lógica para atualização
        $id = $_POST['id'];
        $nome = $_POST['nome'];
        $descricao = $_POST['descricao'];

        $query = $conn->prepare("UPDATE cursos SET nome = :nome, descricao = :descricao WHERE id = :id");
        $query->bindParam(':id', $id);
        $query->bindParam(':nome', $nome);
        $query->bindParam(':descricao', $descricao);

        if ($query->execute()) {
            $mensagem = "Curso atualizado com sucesso";
        } else {
            $mensagem = "Erro ao atualizar o curso";
        }
    } elseif (!empty($_POST['nome']) && !empty($_POST['descricao'])) {
        // Lógica para cadastro
        $nome = $_POST['nome'];
        $descricao = $_POST['descricao'];

        try {
            $query = $conn->prepare("SELECT COUNT(*) FROM cursos WHERE nome = :nome AND descricao = :descricao");
            $query->bindParam(':nome', $nome);
            $query->bindParam(':descricao', $descricao);
            $query->execute();
            $count = $query->fetchColumn();

            if ($count > 0) {
                $mensagem = "Curso já cadastrado";
            } else {
                $query = $conn->prepare("INSERT INTO cursos (nome, descricao) VALUES (:nome, :descricao)");
                $query->bindParam(':nome', $nome);
                $query->bindParam(':descricao', $descricao);

                if ($query->execute()) {
                    $mensagem = "Curso cadastrado com sucesso";
                } else {
                    $mensagem = "Erro ao cadastrar curso";
                }
            }
        } catch (PDOException $e) {
            $mensagem = "Erro ao cadastrar curso";
        }
    } else {
        $mensagem = "Preencha todos os campos";
    }
}

// Consulta para buscar os registros
$sql = "SELECT id, nome, descricao FROM cursos";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Curso</title>
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
            margin-top: -20px;
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
        input[type="date"],
        input[type="password"] {
            width: 100%;
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
            padding: 5px; /* Removido fundo azul */
            transition: background-color 0.3s;
            color: #023d54;
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
</head>
<body>
    <div class="container">
        <h2>Cadastro de Curso</h2>

        <?php if (!empty($mensagem)): ?>
            <div class="alert"><?php echo $mensagem; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div><label for="nome">Curso</label><input type="text" name="nome" id="nome" required></div>
            <div><label for="descricao">Descrição</label><input type="text" name="descricao" id="descricao" required></div>
            <button type="submit">Cadastrar</button>
        </form>

        <table>
            <thead>
                <tr>
                    
                    <th>Curso</th>
                    <th>Descrição</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result as $curso): ?>
                    <tr>
                       
                        <td><?php echo $curso['nome']; ?></td>
                        <td><?php echo $curso['descricao']; ?></td>
                        <td class="acao">
                            <a href="javascript:void(0);" onclick="showEditModal(<?php echo htmlspecialchars(json_encode($curso)); ?>)">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="javascript:void(0);" onclick="if(confirm('Tem certeza que deseja excluir?')) { this.nextElementSibling.submit(); }">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                            <form method="POST" action="" style="display:none;">
                                <input type="hidden" name="delete_id" value="<?php echo $curso['id']; ?>">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Editar Curso </h2>
            <form method="POST" action="">
                <input type="hidden" name="id" id="editId">
                <div><label for="editNome">Curso</label><input type="text" name="nome" id="editNome" required></div>
                <div><label for="editDescricao">Descrição</label><input type="text" name="descricao" id="editDescricao" required></div>
                <button type="submit" name="update">Atualizar</button>
            </form>
        </div>
    </div>

    <script>
        function showEditModal(curso) {
            document.getElementById("editId").value = curso.id;
            document.getElementById("editNome").value = curso.nome;
            document.getElementById("editDescricao").value = curso.descricao;
            document.getElementById("editModal").style.display = "block";
        }

        function closeModal() {
            document.getElementById("editModal").style.display = "none";
        }
    </script>
</body>
</html>

<?php
include("config.php"); // Inclui a conexão com o banco de dados

// Cadastro e atualização de funcionário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update'])) {
        // Atualização do funcionário
        if (!empty($_POST['nome']) && !empty($_POST['email']) && !empty($_POST['telefone']) &&
            !empty($_POST['representante']) && !empty($_POST['cep']) && !empty($_POST['municipio']) &&
            !empty($_POST['bairro']) && !empty($_POST['rua'])) {

            $id = $_POST['id'];
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $telefone = $_POST['telefone'];
            $representante = $_POST['representante'];
            $cep = $_POST['cep'];
            $municipio = $_POST['municipio'];
            $bairro = $_POST['bairro'];
            $rua = $_POST['rua'];

            // Prepara a query para atualização
            $query = $conn->prepare("UPDATE instituicao SET nome=:nome, email=:email, telefone=:telefone, 
                                     representante=:representante, cep=:cep, municipio=:municipio, bairro=:bairro, rua=:rua WHERE id=:id");

            // Vincula os parâmetros da query
            $query->bindParam(':id', $id);
            $query->bindParam(':nome', $nome);
            $query->bindParam(':email', $email);
            $query->bindParam(':telefone', $telefone);
            $query->bindParam(':representante', $representante);
            $query->bindParam(':cep', $cep);
            $query->bindParam(':municipio', $municipio);
            $query->bindParam(':bairro', $bairro);
            $query->bindParam(':rua', $rua);

            if ($query->execute()) {
                $mensagem = "Instituição atualizada com sucesso!";
                $tipo_alerta = "success";
            } else {
                $mensagem = "Erro ao atualizar a Instituição.";
                $tipo_alerta = "error";
            }
        } else {
            $mensagem = "Por favor, preencha todos os campos.";
            $tipo_alerta = "error";
        }
    } else {
        // Cadastro do funcionário
        if (!empty($_POST['nome']) && !empty($_POST['email']) && !empty($_POST['telefone']) &&
            !empty($_POST['representante']) && !empty($_POST['cep']) && !empty($_POST['municipio']) &&
            !empty($_POST['bairro']) && !empty($_POST['rua'])) {

            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $telefone = $_POST['telefone'];
            $representante = $_POST['representante'];
            $cep = $_POST['cep'];
            $municipio = $_POST['municipio'];
            $bairro = $_POST['bairro'];
            $rua = $_POST['rua'];

            try {
                $query = $conn->prepare("INSERT INTO instituicao (nome, email, telefone, representante, cep, municipio, bairro, rua) 
                                         VALUES (:nome, :email, :telefone, :representante, :cep, :municipio, :bairro, :rua)");

                $query->bindParam(':nome', $nome);
                $query->bindParam(':email', $email);
                $query->bindParam(':telefone', $telefone);
                $query->bindParam(':representante', $representante);
                $query->bindParam(':cep', $cep);
                $query->bindParam(':municipio', $municipio);
                $query->bindParam(':bairro', $bairro);
                $query->bindParam(':rua', $rua);

                if ($query->execute()) {
                    $mensagem = "Cadastro realizado com sucesso!";
                    $tipo_alerta = "success";
                } else {
                    $mensagem = "Erro ao cadastrar a instituição.";
                    $tipo_alerta = "error";
                }
            } catch (PDOException $e) {
                $mensagem = "Erro ao cadastrar a instituição: " . $e->getMessage();
                $tipo_alerta = "error";
            }
        } else {
            $mensagem = "Por favor, preencha todos os campos.";
            $tipo_alerta = "error";
        }
    }
}

// Consulta para buscar os registros
$sql = "SELECT id, nome, email, telefone, representante, cep, municipio, bairro, rua FROM instituicao";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>



<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Instituição</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Estilos do formulário */
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
        <h2>Cadastro de Instituição</h2>

        <?php if (!empty($mensagem)): ?>
            <div class="alert <?php echo $tipo_alerta; ?>"><?php echo $mensagem; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
        <div><label for="nome">Nome</label><input type="text" name="nome" id="nome" placeholder="Nome" required></div>
            <div><label for="email">Email</label><input type="email" name="email" id="email" placeholder="E-mail" required></div>
            <div><label for="telefone">Telefone</label><input type="tel" name="telefone" id="telefone" placeholder="Telefone" required></div>
            <div><label for="representante">Representante</label><input type="text" name="representante" id="representante" placeholder="Nome do Representante" required></div>
            <div><label for="cep">CEP</label><input type="text" name="cep" id="cep" placeholder="CEP" required></div>
            <div><label for="municipio">Município</label><input type="text" name="municipio" id="municipio" placeholder="Município" required></div>
            <div><label for="bairro">Bairro</label><input type="text" name="bairro" id="bairro"  placeholder="Bairro" required></div>
            <div><label for="rua">Rua</label><input type="text" name="rua" id="rua" placeholder="Rua" required></div>
            
               
            <button type="submit">Cadastrar</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Nome</th><th>Email</th><th>Telefone</th><th>Representante</th><th>Cep</th><th>Município</th><th>Bairro</th>
                    <th>Rua</th><th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result as $instituicao): ?>
                    <tr>
                       
                        <td><?php echo $instituicao['nome']; ?></td>
                        <td><?php echo $instituicao['email']; ?></td>
                        <td><?php echo $instituicao['telefone']; ?></td>
                        <td><?php echo $instituicao['representante']; ?></td>
                        <td><?php echo $instituicao['cep']; ?></td>
                        <td><?php echo $instituicao['municipio']; ?></td>
                        <td><?php echo $instituicao['bairro']; ?></td>
                        <td><?php echo $instituicao['rua']; ?></td>
                        <td class="acao">
                            <a href="javascript:void(0);" onclick="showEditModal(<?php echo htmlspecialchars(json_encode($instituicao)); ?>)">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="" style="display:none;">
                                <input type="hidden" name="delete_id" value="<?php echo $instituicao['id']; ?>">
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
            <h2>Editar Instituição</h2>
            <form method="POST" action="">
                <input type="hidden" name="id" id="editId">
                <div><label for="editNome">Nome</label><input type="text" name="nome" id="editNome" required></div>
            <div><label for="editEmail">Email</label><input type="email" name="email" id="editEmail" required></div>
            <div><label for="editTelefone">Telefone</label><input type="tel" name="telefone" id="editTelefone" required></div>
            <div><label for="editRepresentante">Representante</label><input type="text" name="representante" id="editRepresentante" required></div>
            <div><label for="editCep">Cep</label><input type="text" name="cep" id="editCep" required></div>
            <div><label for="editMunicipio">Município</label><input type="text" name="municipio" id="editMunicipio" required></div>
            <div><label for="editBairro">Bairro</label><input type="text" name="bairro" id="editBairro"></div>
            <div><label for="editRua">Rua</label><input type="text" name="rua" id="editRua"></div>
                <button type="submit" name="update">Atualizar</button>
            </form>
        </div>
    </div>

    <script>
        function showEditModal(instituicao) {
            document.getElementById("editId").value = instituicao.id;
            document.getElementById("editNome").value = instituicao.nome;
            document.getElementById("editEmail").value = instituicao.email;
            document.getElementById("editTelefone").value = instituicao.telefone;
            document.getElementById("editRepresentante").value = instituicao.representante;
            document.getElementById("editCep").value = instituicao.cep;
            document.getElementById("editMunicipio").value = instituicao.municipio;
            document.getElementById("editBairro").value = instituicao.bairro;
            document.getElementById("editRua").value = instituicao.rua;
            document.getElementById("editModal").style.display = "block";
        }

        function closeModal() {
            document.getElementById("editModal").style.display = "none";
        }
    </script>
</body>
</html>

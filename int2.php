<?php
include("config.php"); // Inclui a conexão com o banco de dados

// Atualização de funcionário
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
    }
}


if (isset($_POST['update'])) {
    // Processar o formulário e atualizar os dados no banco de dados
    $sucesso = true; // Alterar de acordo com o sucesso ou falha da atualização
    
    // Se a atualização for bem-sucedida, defina a variável que acionará o modal
    if ($sucesso) {
        $tipo_alerta = 'sucesso'; // Variável para indicar que a operação foi bem-sucedida
        $mensagem = '';
    } else {
        $tipo_alerta = 'erro';
        $mensagem = 'Erro ao atualizar o registro.';
    }
}

// Consulta para buscar os registros
$sql = "SELECT id, nome, email, telefone, representante, cep, municipio, bairro, rua FROM instituicao LIMIT 1"; // Só pega 1 registro
$stmt = $conn->prepare($sql);
$stmt->execute();
$instituicao = $stmt->fetch(PDO::FETCH_ASSOC); // Pega o primeiro registro encontrado
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atualização de Instituição</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Estilos do formulário */
        body {
            font-family: "Open Sans", sans-serif;
            background-color: #f4f4f9;
        }

        .container {
            max-width: 900px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 0px;
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.1);
            margin-top: -20px;
            margin-bottom: -30px;
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
            margin-left: 52px;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="date"],
        input[type="password"] {
            width: 70%;
            padding: 10px;
            border: 1px solid #5aa2b0;
            border-radius: 12px;
            box-sizing: border-box;
            font-size: 10px;
            margin-bottom: 15px;
            margin-left: 45px;
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

        .modal {
            display: none; 
            position: fixed; 
            z-index: 1; 
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5); /* Cor mais suave */
            padding-top: 60px;
            animation: fadeIn 0.3s ease-out;
        }

        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 30px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            text-align: center;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            animation: modalContentAnimation 0.5s ease-out;
        }

        h2 {
            font-size: 24px;
            color: #5aa2b0;
            font-weight: bold;
        }

        .close {
            color: #5aa2b0;
            font-size: 28px;
            font-weight: bold;
            position: fixed;
            top: 10px;
            right: 10px;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .close:hover {
            color: #174650;
        }

        p {
            font-size: 18px;
            color: #333;
            margin-top: 20px;
        }


    </style>
</head>
<body>
    <div class="container">
        <h2>Atualização de Instituição</h2>

        <?php if (!empty($mensagem)): ?>
            <div class="alert <?php echo $tipo_alerta; ?>"><?php echo $mensagem; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="id" value="<?php echo $instituicao['id']; ?>">
            <div>
                <label for="editNome">Nome</label>
                <input type="text" name="nome" id="editNome" value="<?php echo $instituicao['nome']; ?>" required>
            </div>
            <div>
                <label for="editEmail">Email</label>
                <input type="email" name="email" id="editEmail" value="<?php echo $instituicao['email']; ?>" required>
            </div>
            <div>
                <label for="editTelefone">Telefone</label>
                <input type="tel" name="telefone" id="editTelefone" value="<?php echo $instituicao['telefone']; ?>" required>
            </div>
            <div>
                <label for="editRepresentante">Representante</label>
                <input type="text" name="representante" id="editRepresentante" value="<?php echo $instituicao['representante']; ?>" required>
            </div>
            <div>
                <label for="editCep">Cep</label>
                <input type="text" name="cep" id="editCep" value="<?php echo $instituicao['cep']; ?>" required>
            </div>
            <div>
                <label for="editMunicipio">Município</label>
                <input type="text" name="municipio" id="editMunicipio" value="<?php echo $instituicao['municipio']; ?>" required>
            </div>
            <div>
                <label for="editBairro">Bairro</label>
                <input type="text" name="bairro" id="editBairro" value="<?php echo $instituicao['bairro']; ?>">
            </div>
            <div>
                <label for="editRua">Rua</label>
                <input type="text" name="rua" id="editRua" value="<?php echo $instituicao['rua']; ?>">
            </div>
            <button type="submit" name="update">Atualizar</button>
        </form>
    </div>

    <!-- Modal de Sucesso -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Registro Atualizado com Sucesso!</h2>
            <p>Os dados da instituição foram atualizados corretamente.</p>
        </div>
    </div>

    <script>
       <?php if (isset($tipo_alerta) && $tipo_alerta === 'sucesso'): ?>
            window.onload = function() {
                var modal = document.getElementById('successModal');
                var span = document.getElementsByClassName('close')[0];

                modal.style.display = "block";

                span.onclick = function() {
                    modal.style.display = "none";
                }

                // Fecha o modal quando clicar fora dele
                window.onclick = function(event) {
                    if (event.target == modal) {
                        modal.style.display = "none";
                    }
                }
            };
        <?php endif; ?>
    </script>

</body>
</html>

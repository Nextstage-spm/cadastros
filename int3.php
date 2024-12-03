<?php
include("config.php"); // Inclui a conexão com o banco de dados

$mensagem = "";
$tipo_alerta = "";

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

// Consulta para buscar os registros
$sql = "SELECT id, nome, email, telefone, representante, cep, municipio, bairro, rua FROM instituicao LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->execute();
$instituicao = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atualização de Instituição</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Seus estilos existentes */
        /* ... */

        /* Estilos do modal de mensagem */
        .modal-mensagem {
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
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .btn-confirmar {
            background-color: #174650;
            color: white;
            padding: 8px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 15px;
        }

        .success { 
            color: #4CAF50; 
            font-weight: bold;
        }

        .error { 
            color: #f44336; 
            font-weight: bold;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-mensagem {
            animation: fadeIn 0.3s ease-out;
        }
    </style>
</head>
<body>
    <!-- Modal de Mensagem -->
    <div id="mensagemModal" class="modal-mensagem">
        <div class="modal-content-mensagem">
            <p id="mensagemTexto"></p>
            <button onclick="fecharModal()" class="btn-confirmar">OK</button>
        </div>
    </div>

    <div class="container">
        <h2>Atualização de Instituição</h2>

        <form method="POST" action="">
            <input type="hidden" name="id" value="<?php echo $instituicao['id']; ?>">
            <!-- Seus campos de formulário existentes -->
            <!-- ... -->
            <button type="submit" name="update">Atualizar</button>
        </form>
    </div>

    <script>
        function mostrarModal(mensagem, tipo) {
            const modal = document.getElementById('mensagemModal');
            const mensagemTexto = document.getElementById('mensagemTexto');
            
            mensagemTexto.textContent = mensagem;
            mensagemTexto.className = tipo;
            
            modal.style.display = 'block';

            // Fecha o modal automaticamente após 3 segundos
            setTimeout(function() {
                fecharModal();
            }, 3000);
        }

        function fecharModal() {
            document.getElementById('mensagemModal').style.display = 'none';
        }

        // Fechar modal ao clicar fora
        window.onclick = function(event) {
            const modal = document.getElementById('mensagemModal');
            if (event.target == modal) {
                fecharModal();
            }
        }

        // Mostrar mensagem se existir
        <?php if (!empty($mensagem)): ?>
            mostrarModal("<?php echo addslashes($mensagem); ?>", "<?php echo $tipo_alerta; ?>");
        <?php endif; ?>
    </script>
</body>
</html>
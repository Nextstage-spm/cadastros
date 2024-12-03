<?php
session_start();
include("config.php");

// Buscar lista de professores para o select
$query = $conn->query("SELECT id, nome FROM professor ORDER BY nome ASC");
$professores = $query->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['nome']) && !empty($_POST['idprofessor'])) {
        $nome = $_POST['nome'];
        $idprofessor = $_POST['idprofessor'];

        try {
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
                        header("Location: listar_disciplinas.php");
                        exit();
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
    } else {
        $_SESSION['mensagem'] = "Preencha todos os campos";
        $_SESSION['tipo_alerta'] = "error";
    }
}

// Verifica se existe mensagem na sessão
if (isset($_SESSION['mensagem'])) {
    $mensagem = $_SESSION['mensagem'];
    $tipo_alerta = $_SESSION['tipo_alerta'];
    
    // Limpa as mensagens da sessão
    unset($_SESSION['mensagem']);
    unset($_SESSION['tipo_alerta']);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Disciplina</title>
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

        .btn-voltar {
            background-color: #5aa2b0;
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

        .btn-voltar:hover {
            background-color: #174650;
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
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #5aa2b0;
            border-radius: 12px;
            box-sizing: border-box;
            font-size: 14px;
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

    <div class="container">
        <h2>Cadastro de Disciplina</h2>

        <a href="listar_disciplinas.php" class="btn-voltar">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>

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

    <script>
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

        window.onclick = function(event) {
            const mensagemModal = document.getElementById('mensagemModal');
            if (event.target == mensagemModal) {
                fecharModal();
            }
        }

        <?php if (!empty($mensagem)): ?>
            mostrarModal("<?php echo addslashes($mensagem); ?>", "<?php echo $tipo_alerta; ?>");
        <?php endif; ?>
    </script>
</body>
</html>
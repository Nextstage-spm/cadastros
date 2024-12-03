<?php   
include("config.php"); // Inclui a conexão com o banco de dados

$mensagem = "";
$tipo_alerta = "";

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id'])) { // Atualização de aluno
        $id = $_POST['id'];
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $telefone = $_POST['telefone'];
        $data_nascimento = $_POST['data_nascimento'];
        $cpf = $_POST['cpf'];

        try {
            $updateQuery = $conn->prepare("UPDATE aluno_cadastro SET nome = :nome, email = :email, telefone = :telefone, data_nascimento = :data_nascimento, cpf = :cpf WHERE id = :id");
            $updateQuery->bindParam(':nome', $nome);
            $updateQuery->bindParam(':email', $email);
            $updateQuery->bindParam(':telefone', $telefone);
            $updateQuery->bindParam(':data_nascimento', $data_nascimento);
            $updateQuery->bindParam(':cpf', $cpf);
            $updateQuery->bindParam(':id', $id);

            if ($updateQuery->execute()) {
                $mensagem = "Aluno atualizado com sucesso!";
                $tipo_alerta = "success";
            } else {
                $mensagem = "Erro ao atualizar o aluno.";
                $tipo_alerta = "error";
            }
        } catch (PDOException $e) {
            $mensagem = "Erro ao atualizar o aluno: " . $e->getMessage();
            $tipo_alerta = "error";
        }
    } elseif (isset($_POST['nome']) && isset($_POST['email']) && isset($_POST['telefone']) && isset($_POST['data_nascimento']) && isset($_POST['cpf'])&& isset($_POST['palavrasecreta'])) {
        // Cadastro de novo aluno
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $telefone = $_POST['telefone'];
        $data_nascimento = $_POST['data_nascimento'];
        $cpf = $_POST['cpf'];
        $palavrasecreta = $_POST['palavrasecreta'];

        try {
            $query = $conn->prepare("INSERT INTO aluno_cadastro (nome, email, telefone, data_nascimento, cpf) 
                                     VALUES (:nome, :email, :telefone, :data_nascimento, :cpf)");
            $query->bindParam(':nome', $nome);
            $query->bindParam(':email', $email);
            $query->bindParam(':telefone', $telefone);
            $query->bindParam(':data_nascimento', $data_nascimento);
            $query->bindParam(':cpf', $cpf);
            $query->bindParam(':palavrasecreta', $palavrasecreta);

            if ($query->execute()) {
                $mensagem = "Cadastro realizado com sucesso!";
                $tipo_alerta = "success";

                // Gerar a chave de acesso automaticamente
                $chave_acesso = "https://seusite.com/acesso/" . md5(uniqid($email, true));
            } else {
                $mensagem = "Erro ao cadastrar o aluno.";
                $tipo_alerta = "error";
            }
        } catch (PDOException $e) {
            $mensagem = "Erro ao cadastrar o aluno: " . $e->getMessage();
            $tipo_alerta = "error";
        }
    } elseif (isset($_POST['delete_id'])) { // Exclusão de aluno
        $id = $_POST['delete_id'];
        try {
            $deleteQuery = $conn->prepare("DELETE FROM aluno_cadastro WHERE id = :id");
            $deleteQuery->bindParam(':id', $id);
            if ($deleteQuery->execute()) {
                $mensagem = "Aluno excluído com sucesso!";
                $tipo_alerta = "success";
            } else {
                $mensagem = "Erro ao excluir o aluno.";
                $tipo_alerta = "error";
            }
        } catch (PDOException $e) {
            $mensagem = "Erro ao excluir o aluno: " . $e->getMessage();
            $tipo_alerta = "error";
        }
    }
}

// Recuperação dos dados dos alunos
$sql = "SELECT id, nome, email, telefone, data_nascimento, cpf FROM aluno_cadastro";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Aluno</title>
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
            margin-top: 50px;
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
        input[type="date"] {
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
            height: 45px;
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
    </style>
</head>
<body>
    <div class="container">
        <h2>Cadastro de Aluno</h2>
        
        <?php if (!empty($mensagem)): ?>
            <div class="alert <?php echo $tipo_alerta; ?>"><?php echo $mensagem; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div><label for="nome">Nome:</label><input type="text" name="nome" id="nome" required></div>
            <div><label for="email">Email:</label><input type="email" name="email" id="email" required></div>
            <div><label for="telefone">Telefone:</label><input type="tel" name="telefone" id="telefone" required></div>
            <div><label for="cpf">CPF:</label><input type="text" name="cpf" id="cpf" required></div>
            <div><label for="data_nascimento">Data de Nascimento:</label><input type="text" name="data_nascimento" id="data_nascimento" required></div>
            <div><label for="palavrasecreta">Palavra secreta:</label><input type="text" name="palavrasecreta" id="palavrasecreta" required></div>
            <button type="submit">Cadastrar</button>
        </form>

        <table>
            <thead>
                <tr><th>Nome</th><th>Email</th><th>Telefone</th><th>Data de Nascimento</th><th>CPF</th><th>Ações</th></tr>
            </thead>
            <tbody>
                <?php foreach ($result as $aluno): ?>
                    <tr>
                        <td><?php echo $aluno['nome']; ?></td>
                        <td><?php echo $aluno['email']; ?></td>
                        <td><?php echo $aluno['telefone']; ?></td>
                        <td><?php echo $aluno['data_nascimento']; ?></td>
                        <td><?php echo $aluno['cpf']; ?></td>
                        
                        <td class="acao">
                            <a href="javascript:void(0);" onclick="showEditModal(<?php echo htmlspecialchars(json_encode($aluno)); ?>)">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="javascript:void(0);" onclick="if(confirm('Tem certeza que deseja excluir?')) { this.nextElementSibling.submit(); }">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                            <form method="POST" action="" style="display:none;">
                                <input type="hidden" name="delete_id" value="<?php echo $aluno['id']; ?>">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal de edição -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Editar Aluno</h2>
            <form method="POST" action="">
                <input type="hidden" name="id" id="editId">
                <div><label for="editNome">Nome:</label><input type="text" name="nome" id="editNome" required></div>
                <div><label for="editEmail">Email:</label><input type="email" name="email" id="editEmail" required></div>
                <div><label for="editTelefone">Telefone:</label><input type="tel" name="telefone" id="editTelefone" required></div>
                <div><label for="editCPF">CPF:</label><input type="text" name="cpf" id="editCPF" required></div>
                <div><label for="editDataNascimento">Data de Nascimento:</label><input type="date" name="data_nascimento" id="editDataNascimento" required></div>
                <button type="submit" name="update">Atualizar</button>
            </form>
        </div>
    </div>

    <!-- Modal para exibir a chave de acesso -->
    <?php if (isset($chave_acesso)): ?>
        <div id="chaveAcessoModal" class="modal" style="display: block;">
            <div class="modal-content">
                <span class="close" onclick="document.getElementById('chaveAcessoModal').style.display='none'">&times;</span>
                <h2>Chave de Acesso</h2>
                <p>Abaixo está o link de acesso para o aluno recém-cadastrado:</p>
                <p><strong><?php echo $chave_acesso; ?></strong></p>
                <button onclick="enviarChave('<?php echo $chave_acesso; ?>')">Enviar chave de acesso</button>
            </div>
        </div>
    <?php endif; ?>

    <script>
        function showEditModal(aluno) {
            document.getElementById('editId').value = aluno.id;
            document.getElementById('editNome').value = aluno.nome;
            document.getElementById('editEmail').value = aluno.email;
            document.getElementById('editTelefone').value = aluno.telefone;
            document.getElementById('editCPF').value = aluno.cpf;
            document.getElementById('editDataNascimento').value = aluno.data_nascimento;
            document.getElementById('editModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        function confirmAction(action, id) {
            let message = "";
            if (action === 'edit') {
                message = "Tem certeza que deseja editar esse aluno?";
            } else if (action === 'delete') {
                message = "Tem certeza que deseja excluir esse aluno?";
            }
            if (confirm(message)) {
                if (action === 'edit') {
                    showEditModal(id);
                } else if (action === 'delete') {
                    deleteAluno(id);
                }
            }
        }

        function deleteAluno(id) {
            const formData = new FormData();
            formData.append('delete_id', id);

            fetch('seu_arquivo_php.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => alert(data));
        }

        function enviarChave(chave) {
            alert("Chave enviada: " + chave); // Lógica para enviar a chave
        }




        document.getElementById('data_nascimento').addEventListener('input', function(event) {
    var value = event.target.value.replace(/\D/g, '');  // Remove qualquer coisa que não seja número
    if (value.length <= 2) {
        event.target.value = value.replace(/(\d{2})/, '$1'); // dd
    } else if (value.length <= 4) {
        event.target.value = value.replace(/(\d{2})(\d{2})/, '$1/$2'); // dd/mm
    } else {
        event.target.value = value.replace(/(\d{2})(\d{2})(\d{4})/, '$1/$2/$3'); // dd/mm/yyyy
    }
});

// Submeter o formulário com a data no formato correto (yyyy-mm-dd)
document.querySelector('form').addEventListener('submit', function(event) {
    var dataNascimento = document.getElementById('data_nascimento').value;
    
    // Verifica se o campo de data está preenchido
    if (dataNascimento) {
        // Converte de dd/mm/yyyy para yyyy-mm-dd
        var partesData = dataNascimento.split('/');
        var dataConvertida = partesData[2] + '-' + partesData[1] + '-' + partesData[0];

        // Atualiza o valor do campo antes de enviar o formulário
        document.getElementById('data_nascimento').value = dataConvertida;
    }
});




var dataNascimentoBanco = "<?php echo $data_nascimento; ?>"; // data no formato yyyy-mm-dd

// Função para formatar a data para o formato brasileiro dd/mm/yyyy
function formatarDataBrasil(data) {
    var partes = data.split('-');
    return partes[2] + '/' + partes[1] + '/' + partes[0];
}

// Exibir a data formatada no campo de data
document.getElementById('data_nascimento').value = formatarDataBrasil(dataNascimentoBanco);
    </script>
</body>
</html>

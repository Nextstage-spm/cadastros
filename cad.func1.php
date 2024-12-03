<php
// Iniciar a sessão
session_start();

// Incluir a conexão com o banco de dados
include("config.php");

// Consulta para buscar os registros
$sql = "SELECT id, nome, email, cargo, matricula, telefone, data_nascimento FROM funcionarios";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verifica se todos os campos estão preenchidos
    if (!empty($_POST['nome']) && !empty($_POST['senha']) && !empty($_POST['confirma_senha']) &&
        !empty($_POST['email']) && !empty($_POST['telefone']) && !empty($_POST['data_nascimento']) &&
        !empty($_POST['matricula']) && !empty($_POST['cargo'])) {

        // Captura os valores do formulário
        $nome = $_POST['nome'];
        $senha = $_POST['senha'];
        $cargo = $_POST['cargo'];
        $matricula = $_POST['matricula'];
        $confirma_senha = $_POST['confirma_senha'];
        $email = $_POST['email'];
        $telefone = $_POST['telefone'];
        $data_nascimento = $_POST['data_nascimento'];

        if ($senha === $confirma_senha) {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT); // Hash da senha

            try {
                // Prepara a query SQL para inserir os dados
                $query = $conn->prepare("INSERT INTO funcionarios (nome, senha, cargo, matricula, email, telefone, data_nascimento) 
                                         VALUES (:nome, :senha, :cargo, :matricula, :email, :telefone, :data_nascimento)");

                // Vincula os parâmetros da query
                $query->bindParam(':nome', $nome);
                $query->bindParam(':senha', $senha_hash);
                $query->bindParam(':email', $email);
                $query->bindParam(':telefone', $telefone);
                $query->bindParam(':data_nascimento', $data_nascimento);
                $query->bindParam(':matricula', $matricula);
                $query->bindParam(':cargo', $cargo);

                // Executa a query e verifica se foi bem-sucedida
                if ($query->execute()) {
                    $mensagem = "Cadastro realizado com sucesso!";
                    $tipo_alerta = "success";
                } else {
                    $mensagem = "Erro ao cadastrar o funcionário.";
                    $tipo_alerta = "error";
                }
            } catch (PDOException $e) {
                $mensagem = "Erro ao cadastrar o funcionário: " . $e->getMessage();
                $tipo_alerta = "error";
            }
        } else {
            $mensagem = "As senhas não coincidem. Tente novamente.";
            $tipo_alerta = "error";
        }
    } else {
        $mensagem = "Por favor, preencha todos os campos.";
        $tipo_alerta = "error";
    }
}

<?
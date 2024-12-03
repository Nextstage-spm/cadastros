<?php  
include("config.php"); // Inclui a conexão com o banco de dados

$mensagem = "";
$tipo_alerta = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verifica se os campos 'nome' e 'titulacao_cod' foram enviados e não estão vazios
    if (isset($_POST['nome']) && !empty($_POST['nome']) && isset($_POST['titulacao_cod']) && !empty($_POST['titulacao_cod'])) {
        // Captura os valores do formulário
        $nome_professor = $_POST['nome'];
        $titulacao_cod = $_POST['titulacao_cod']; // Captura o ID da titulação

        try {
            // Prepara a query para inserir os dados na tabela 'professor'
            $query = $conn->prepare("INSERT INTO professor (nome, titulacao_cod) VALUES (:nome, :titulacao_cod)");
            
            // Faz o bind dos parâmetros
            $query->bindParam(':nome', $nome_professor);
            $query->bindParam(':titulacao_cod', $titulacao_cod); // Mantém o ID da titulação

            // Executa a query e verifica se foi bem-sucedida
            if ($query->execute()) {
                $mensagem = "Cadastro realizado com sucesso!";
                $tipo_alerta = "success";
            } else {
                $mensagem = "Erro ao cadastrar o professor.";
                $tipo_alerta = "error";
            }
        } catch (PDOException $e) {
            $mensagem = "Erro ao cadastrar o professor: " . $e->getMessage();
            $tipo_alerta = "error";
        }
    } else {
        $mensagem = "Por favor, preencha todos os campos.";
        $tipo_alerta = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de professor</title>
    <style>
        /* Estilos do formulário */
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
            width: calc(100% + 40px); /* Ajustado para largura total */
            margin-left: -20px;
            height: 40px;
            margin-bottom: 30px;

            font-family: 'Arial', sans-serif; /* Fonte personalizada */
            font-size: 35px; /* Tamanho da fonte */
            line-height: 40px; /* Alinhamento vertical do texto */
            font-weight: bold; /* Estilo em negrito */
            font-weight: 300; /* Letra mais fina */
            letter-spacing: 1px; /* Espaçamento entre letras */
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

        input[type="text"] {
            width: 90%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #5aa2b0;
            border-radius: 12px;
            box-sizing: border-box; /* Para garantir que o padding não aumente a largura total */
            min-height: 20px; /* Altura mínima para todos os campos */
            margin-left: 208px;
            font-size: 10px; 
        }

        select {
            width: 90%;
            padding: 10px;
            margin-top: 21px;
            border: 1px solid #5aa2b0;
            border-radius: 12px;
            box-sizing: border-box; /* Para garantir que o padding não aumente a largura total */
            min-height: 20px; /* Altura mínima para todos os campos */
            margin-left: -180px;
            font-size: 10px; 
        }

        label[for="nome"] {
            font-weight: bold;
            font-size: 16px;
            color: #000000; /* Exemplo de cor personalizada */
            margin-left: 220px; /* Ajuste para alinhamento */
            font-size: 14px; 
        }

        label[for="titulacao_cod"] { /* Alterado para 'titulacao_cod' */
            font-weight: bold;
            font-size: 16px;
            color: #000000; /* Exemplo de cor personalizada */
            margin-left: 220px; /* Ajuste para alinhamento */
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
            width: 100%; /* Largura total para ocupar espaço */
            max-width: 200px; /* Tamanho máximo do botão */
            margin: 20px auto; /* Centralizando o botão */
            height: 45px;
            margin-top: 70px;
            margin-bottom: -5px;
        }

        button:hover {
            background-color: #5aa2b0;
        }

        .error {
            color: red;
            margin-top: 5px;
            font-size: 14px;
        }
    </style>
</head>
<body>
   <div class="container">
      <h2 class="titulo-principal">Cadastrar professor</h2>
      <form id="cadastroForm" action="" method="post">
      <div>
            <label for="nome">Nome do Professor</label>
            <input type="text" id="nome" name="nome" placeholder="Nome do Professor" required>
            <div id="nomeError" class="error"></div> <!-- Mensagem de erro -->
        </div>
        <br>

        <!-- Campo para selecionar a titulação do professor -->
        <label for="titulacao_cod">Titulação</label> <!-- Alterado -->
        <select name="titulacao_cod" required> <!-- Alterado -->
            <option value="">Selecione</option>

            <?php
            // Consulta para obter as titulações disponíveis
            $query = $conn->query("SELECT id, nome FROM titulacao ORDER BY nome ASC");
            $registros = $query->fetchAll(PDO::FETCH_ASSOC);

            // Exibe as opções de titulação no select
            foreach ($registros as $option) {
                echo "<option value=\"{$option['id']}\">{$option['nome']}</option>";
            }
            ?>
        </select>

        <button type="submit">Cadastrar</button>
      </form>
   </div>

   <!-- Passa as variáveis PHP para o JavaScript -->
   <script>
       var mensagem = "<?php echo $mensagem; ?>";
       var tipoAlerta = "<?php echo $tipo_alerta; ?>";
   </script>

   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   <script>
       // Mostra a mensagem de sucesso ou erro quando o formulário for submetido
       document.addEventListener("DOMContentLoaded", function() {
           if (mensagem && tipoAlerta) {
               Swal.fire({
                   icon: tipoAlerta,
                   title: mensagem,
                   showConfirmButton: true
               });
           }
       });
   </script>
</body>
</html>

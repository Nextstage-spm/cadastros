<?php
include("config.php"); // Inclui a conexão com o banco de dados

// Obtém os dados dos cursos
$query = $conn->query("SELECT nome, email, telefone, data_nascimento, cpf FROM aluno_cadastro");
$aluno = $query->fetchAll(PDO::FETCH_ASSOC);

// Conta o total de cursos
$totalAluno = count($aluno);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alunos</title>
    <style>
     body {
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 900px;
            margin: auto;
            padding: 20px;
            margin-left: 160px;
        }
        h1 {
          margin-left: 200px;
            color: #023d54;
            
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 7px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #5aa2b0;
            color: white;
        }
        .total-aluno {
            margin-top: 20px;
            text-align: left;
            font-weight: bold;
            color: #023d54;
        }
        .btn-print {
            display: block;
            width: 100px;
            margin: 20px auto;
            margin-left: 400px;
            padding: 10px;
            text-align: center;
            background-color: #5aa2b0;
            color: white;
            border: none;
            cursor: pointer;
        }
        .btn-print:hover {
            background-color: #174650;
        }

        /* Estilos específicos para impressão */
        @media print {
            /* Oculta tudo exceto o relatório */
            body * {
                visibility: hidden;
            }
            .container, .container * {
                visibility: visible;
            }
            .container {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
            }
            .btn-print {
                display: none; 
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Relatório Gerencial - Alunos</h1>
        <table>
            <thead>
                <tr>
                <th>Nome</th>
                <th>Telefone</th>
                <th>Email</th>
                <th>CPF</th>
                <th>Data de Nascimento</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($aluno as $aluno): ?>
                    <tr>
                    <td><?php echo htmlspecialchars($aluno['nome']); ?></td>
                        <td><?php echo htmlspecialchars($aluno['telefone']); ?></td>
                        <td><?php echo htmlspecialchars($aluno['email']); ?></td>
                        <td><?php echo htmlspecialchars($aluno['cpf']); ?></td>
                        <td><?php echo htmlspecialchars($aluno['data_nascimento']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="total-aluno">Total de Alunos Cadastrados: <?php echo $totalAluno; ?></div>
        <button class="btn-print" onclick="window.print()">Imprimir</button>
    </div>
</body>
</html>

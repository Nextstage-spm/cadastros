<?php
include("config.php"); // Inclui a conexão com o banco de dados

// Obtém os dados dos cursos
$query = $conn->query("SELECT id, nome, cargo, email, matricula, telefone, data_nascimento FROM funcionarios");
$funcionarios = $query->fetchAll(PDO::FETCH_ASSOC);

// Conta o total de cursos
$totalFuncionarios = count($funcionarios);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório Gerencial - Funcionários</title>
    <style>
     body {
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            margin-left: 100px;
        }
        h1 {
          margin-left: 210px;
            color: #023d54;
            width: 700px;
            
        }
        table {
            width: 130%;
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
        .total-funcionarios {
            margin-top: 20px;
            text-align: left;
            font-weight: bold;
            color: #023d54;
        }
        .btn-print {
            display: block;
            width: 100px;
            margin: 20px auto;
            margin-left: 420px;
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
                display: none; /* Oculta o botão de impressão */
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Relatório Gerencial - Funcionários</h1>
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Cargo</th>
                    <th>Email</th>
                    <th>Matrícula</th>
                    <th>Telefone</th>
                    <th>Data de Nascimento</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($funcionarios as $funcionarios): ?>
                    <tr>
                    <td><?php echo htmlspecialchars($funcionarios['nome']); ?></td>
                        <td><?php echo htmlspecialchars($funcionarios['cargo']); ?></td>
                        <td><?php echo htmlspecialchars($funcionarios['email']); ?></td>
                        <td><?php echo htmlspecialchars($funcionarios['matricula']); ?></td>
                        <td><?php echo htmlspecialchars($funcionarios['telefone']); ?></td>
                        <td><?php echo htmlspecialchars(date("d/m/Y", strtotime($funcionarios['data_nascimento']))); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="total-funcionarios">Total de Funcionários Cadastrados: <?php echo $totalFuncionarios; ?></div>
        <button class="btn-print" onclick="window.print()">Imprimir</button>
    </div>
</body>
</html>

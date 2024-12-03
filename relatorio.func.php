<?php
include("config.php"); // Include database connection

// Fetch employee data
$query = $conn->query("SELECT id, nome, cargo, email, matricula, telefone, data_nascimento FROM funcionarios");
$funcionarios = $query->fetchAll(PDO::FETCH_ASSOC);
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
            max-width: 900px;
            margin: auto;
            padding: 20px;
        }
        h1 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #5aa2b0;
            color: white;
        }
        .btn-print {
            display: block;
            width: 100px;
            margin: 20px auto;
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
                <?php foreach ($funcionarios as $funcionario): ?>
                    <tr>
                        
                        <td><?php echo htmlspecialchars($funcionario['nome']); ?></td>
                        <td><?php echo htmlspecialchars($funcionario['cargo']); ?></td>
                        <td><?php echo htmlspecialchars($funcionario['email']); ?></td>
                        <td><?php echo htmlspecialchars($funcionario['matricula']); ?></td>
                        <td><?php echo htmlspecialchars($funcionario['telefone']); ?></td>
                        <td><?php echo htmlspecialchars(date("d/m/Y", strtotime($funcionario['data_nascimento']))); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button class="btn-print" onclick="window.print()">Imprimir</button>
    </div>
</body>
</html>

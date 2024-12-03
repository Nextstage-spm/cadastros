<?php
include("config.php"); // Inclui a conexão com o banco de dados

// Obtém os dados dos cursos
$query = $conn->query("SELECT id, nome, descricao FROM cursos");
$cursos = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório Gerencial - Cursos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 800px;
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
        <h1>Relatório Gerencial - Cursos</h1>
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Descrição</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cursos as $curso): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($curso['nome']); ?></td>
                        <td><?php echo htmlspecialchars($curso['descricao']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button class="btn-print" onclick="window.print()">Imprimir</button>
    </div>
</body>
</html>

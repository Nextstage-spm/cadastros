<?php
include("config.php"); // Inclui a conexão com o banco de dados

// Obtém os dados dos cursos
$query = $conn->query("SELECT iddisciplina, nome, idprofessor FROM disciplina");
$disciplina = $query->fetchAll(PDO::FETCH_ASSOC);

// Conta o total de cursos
$totalDisciplina = count($disciplina);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disciplinas </title>
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
        .total-disciplina {
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
        <h1>Relatório Gerencial - Disciplinas</h1>
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Professor</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($disciplina as $disciplina): ?>
                    <tr>
                    <td><?php echo htmlspecialchars($disciplina['nome']); ?></td>
                        <td><?php echo htmlspecialchars($disciplina['idprofessor']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="total-disciplina">Total de Disciplinas Cadastradas: <?php echo $totalDisciplina; ?></div>
        <button class="btn-print" onclick="window.print()">Imprimir</button>
    </div>
</body>
</html>

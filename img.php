<?php
include("config.php");

// Criar pasta de uploads se não existir
$pasta = "uploads/";
if (!file_exists($pasta)) {
    mkdir($pasta, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validação do arquivo
    if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
        die("Erro no upload do arquivo: " . $_FILES['foto']['error']);
    }

    // Validação do tipo de arquivo
    $allowed = array('jpg', 'jpeg', 'png', 'gif');
    $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) {
        die('Apenas arquivos de imagem são permitidos (jpg, jpeg, png, gif)');
    }

    // Nome temporário do arquivo
    $arquivo_tmp = $_FILES['foto']['tmp_name'];
    // Nome original do arquivo
    $nome_arquivo = basename($_FILES['foto']['name']);
    // Caminho final do arquivo
    $caminho_final = $pasta . uniqid() . "-" . htmlspecialchars($nome_arquivo);

    // Verifica se o upload foi bem-sucedido
    if (move_uploaded_file($arquivo_tmp, $caminho_final)) {
        try {
            // Prepara a query para salvar o caminho no banco
            $sql = "INSERT INTO fotos (caminho) VALUES (?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$caminho_final]);
            echo "Foto enviada e caminho salvo com sucesso!";
        } catch(Exception $e) {
            echo "Erro ao salvar o caminho no banco: " . $e->getMessage();
        }
    } else {
        echo "Erro ao fazer o upload da imagem.";
    }
}

// Exibição das fotos
try {
    $sql = "SELECT caminho FROM fotos";
    $stmt = $conn->query($sql);
    
    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<img src='" . htmlspecialchars($row['caminho']) . "' alt='Foto' style='width: 200px; margin: 10px;'><br>";
        }
    } else {
        echo "Nenhuma foto encontrada.";
    }
} catch(Exception $e) {
    echo "Erro ao buscar fotos: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload de Fotos</title>
</head>
<body>
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="foto">Selecione a foto:</label>
        <input type="file" name="foto" id="foto" required accept="image/*">
        <button type="submit">Enviar</button>
    </form>
</body>
</html>
<?php
include("config.php");

// Habilitar exibição de erros para debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Função para salvar arquivos
function salvarArquivo($campo, $pasta) {
    if (isset($_FILES[$campo]['error']) && $_FILES[$campo]['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = array(
            UPLOAD_ERR_INI_SIZE => "O arquivo é maior que o limite permitido pelo servidor",
            UPLOAD_ERR_FORM_SIZE => "O arquivo é maior que o limite permitido pelo formulário",
            UPLOAD_ERR_PARTIAL => "O upload foi interrompido",
            UPLOAD_ERR_NO_FILE => "Nenhum arquivo foi enviado",
            UPLOAD_ERR_NO_TMP_DIR => "Pasta temporária não encontrada",
            UPLOAD_ERR_CANT_WRITE => "Falha ao escrever o arquivo",
            UPLOAD_ERR_EXTENSION => "Upload bloqueado por extensão"
        );
        echo "Erro no upload do arquivo $campo: " . 
             (isset($errorMessages[$_FILES[$campo]['error']]) ? 
             $errorMessages[$_FILES[$campo]['error']] : 'Erro desconhecido');
        return null;
    }

    if (!empty($_FILES[$campo]['tmp_name']) && is_uploaded_file($_FILES[$campo]['tmp_name'])) {
        $allowed = array('jpg', 'jpeg', 'png');
        $ext = strtolower(pathinfo($_FILES[$campo]['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            echo "Erro: Tipo de arquivo não permitido para o campo $campo. Apenas jpg, jpeg, png são permitidos.<br>";
            return null;
        }

        if ($_FILES[$campo]['size'] > 5 * 1024 * 1024) {
            echo "Erro: O arquivo $campo é muito grande. Tamanho máximo permitido: 5MB<br>";
            return null;
        }

        $nome_original = basename($_FILES[$campo]['name']);
        $novo_nome = uniqid() . "-" . preg_replace("/[^a-zA-Z0-9.\-_]/", "_", $nome_original);
        $caminho_final = $pasta . $novo_nome;

        if (!is_writable($pasta)) {
            echo "Erro: A pasta $pasta não tem permissão de escrita<br>";
            return null;
        }

        if (move_uploaded_file($_FILES[$campo]['tmp_name'], $caminho_final)) {
            return $caminho_final;
        } else {
            echo "Erro ao mover o arquivo $campo. Erro: " . error_get_last()['message'] . "<br>";
            return null;
        }
    }
    return null;
}

$mensagem = '';
$documentos_enviados = null;

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $pasta = "uploads/documentos/";
        if (!file_exists($pasta)) {
            if (!mkdir($pasta, 0777, true)) {
                throw new Exception("Erro ao criar a pasta de uploads.");
            }
        }

        chmod($pasta, 0777);

        $identidade_frente = salvarArquivo('identidade_frente', $pasta);
        $identidade_verso = salvarArquivo('identidade_verso', $pasta);
        $cpf_frente = salvarArquivo('cpf_frente', $pasta);
        $historico = salvarArquivo('historico', $pasta);
        $foto = salvarArquivo('foto', $pasta);

        if (!$identidade_frente || !$identidade_verso || !$cpf_frente || !$historico || !$foto) {
            throw new Exception("Um ou mais arquivos não foram enviados corretamente.");
        }

        $sql = "INSERT INTO documentos (identidade_frente, identidade_verso, cpf_frente, historico, foto) 
                VALUES (:identidade_frente, :identidade_verso, :cpf_frente, :historico, :foto)";
        
        $stmt = $conn->prepare($sql);
        
        $stmt->bindParam(':identidade_frente', $identidade_frente);
        $stmt->bindParam(':identidade_verso', $identidade_verso);
        $stmt->bindParam(':cpf_frente', $cpf_frente);
        $stmt->bindParam(':historico', $historico);
        $stmt->bindParam(':foto', $foto);

        if ($stmt->execute()) {
            $mensagem = "<div class='sucesso'>Documentos enviados e salvos com sucesso!</div>";
            // Recupera os documentos recém enviados
            $documentos_enviados = [
                'identidade_frente' => $identidade_frente,
                'identidade_verso' => $identidade_verso,
                'cpf_frente' => $cpf_frente,
                'historico' => $historico,
                'foto' => $foto
            ];
        } else {
            throw new Exception("Erro ao salvar no banco de dados");
        }
    }

    // Busca todos os documentos
    $sql = "SELECT * FROM documentos ORDER BY data_upload DESC";
    $stmt = $conn->query($sql);
    $todos_documentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $mensagem = "<div class='erro'>Erro: " . $e->getMessage() . "</div>";
} finally {
    if (isset($stmt)) {
        $stmt = null;
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Upload de Documentos</title>
    <style>
   .form-container {
        display: flex; /* Mudado para flex ao invés de grid */
        flex-wrap: nowrap; /* Impede que as caixas quebrem para a próxima linha */
        gap: 15px;
        margin-bottom: 20px;
        justify-content: center;
    }

    .form-group {
        background: white;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        flex: 1; /* Distribui o espaço igualmente */
        min-width: 220px; /* Largura mínima para cada caixa */
        max-width: 250px; /* Largura máxima para cada caixa */
    }

    .file-input-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }

    .preview-container {
        width: 150px; /* Reduzido o tamanho */
        height: 150px; /* Reduzido o tamanho */
        border: 2px dashed #ccc;
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
        background-color: #f8f8f8;
        transition: border-color 0.3s, background-color 0.3s;
    }

    .preview-container:hover {
        border-color: #007bff;
        background-color: #f0f7ff;
    }

    .preview-container img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .upload-icon {
        width: 40px; /* Reduzido o tamanho */
        height: 40px; /* Reduzido o tamanho */
        fill: #666;
    }

    .preview-text {
        color: #666;
        text-align: center;
        font-size: 12px; /* Reduzido o tamanho */
        margin-top: 5px;
    }

    .custom-file-input {
        display: none;
    }

    .custom-file-label {
        display: inline-block;
        padding: 6px 12px; /* Reduzido o padding */
        background-color: #007bff;
        color: white;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s;
        text-align: center;
        width: 90%;
        font-size: 12px; /* Reduzido o tamanho da fonte */
    }

    .custom-file-label:hover {
        background-color: #0056b3;
    }

    button[type="submit"] {
        background-color: #28a745;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        transition: background-color 0.3s;
        display: block;
        margin: 20px auto;
        width: 180px;
    }

    button[type="submit"]:hover {
        background-color: #218838;
    }

    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }

    .preview-container:hover .upload-icon {
        animation: bounce 1s infinite;
        fill: #007bff;
    }

    /* Adicione media query para telas menores */
    @media (max-width: 1200px) {
        .form-container {
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .form-group {
            flex: 0 1 auto;
        }
    }

    .form-group {
        background: white;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        flex: 1;
        min-width: 220px;
        max-width: 250px;
        text-align: center; /* Centraliza o texto do título */
    }

    .document-title {
        font-size: 14px;
        color: #333;
        margin-bottom: 10px;
        font-weight: 600;
        text-align: center;
    }

    .preview-container {
        width: 150px;
        height: 150px;
        border: 2px dashed #ccc;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
        background-color: #f8f8f8;
        transition: border-color 0.3s, background-color 0.3s;
        margin: 0 auto; /* Centraliza a caixa */
    }

    .upload-icon {
        width: 50px; /* Aumentado um pouco já que agora é só o ícone */
        height: 50px;
        fill: #666;
    }

    .custom-file-label {
        margin-top: 10px;
        padding: 6px 12px;
        font-size: 12px;
    }
</style>

<!-- HTML atualizado -->
<form action="" method="POST" enctype="multipart/form-data">
    <div class="form-container">
        <div class="form-group">
            <div class="document-title">RG (Frente)</div>
            <div class="file-input-container">
                <div class="preview-container" id="preview-identidade-frente">
                    <div class="preview-content">
                        <svg class="upload-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 4L12 16M12 4L8 8M12 4L16 8M4 17L4 19C4 19.5304 4.21071 20.0391 4.58579 20.4142C4.96086 20.7893 5.46957 21 6 21L18 21C18.5304 21 19.0391 20.7893 19.4142 20.4142C19.7893 20.0391 20 19.5304 20 19L20 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
                <label for="identidade_frente" class="custom-file-label">Selecionar arquivo</label>
                <input type="file" name="identidade_frente" id="identidade_frente" class="custom-file-input" required accept="image/jpeg,image/png">
            </div>
        </div>

        <div class="form-group">
            <div class="document-title">RG (Verso)</div>
            <div class="file-input-container">
                <div class="preview-container" id="preview-identidade-verso">
                    <div class="preview-content">
                        <svg class="upload-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 4L12 16M12 4L8 8M12 4L16 8M4 17L4 19C4 19.5304 4.21071 20.0391 4.58579 20.4142C4.96086 20.7893 5.46957 21 6 21L18 21C18.5304 21 19.0391 20.7893 19.4142 20.4142C19.7893 20.0391 20 19.5304 20 19L20 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
                <label for="identidade_verso" class="custom-file-label">Selecionar arquivo</label>
                <input type="file" name="identidade_verso" id="identidade_verso" class="custom-file-input" required accept="image/jpeg,image/png">
            </div>
        </div>

        <div class="form-group">
            <div class="document-title">CPF</div>
            <div class="file-input-container">
                <div class="preview-container" id="preview-cpf-frente">
                    <div class="preview-content">
                        <svg class="upload-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 4L12 16M12 4L8 8M12 4L16 8M4 17L4 19C4 19.5304 4.21071 20.0391 4.58579 20.4142C4.96086 20.7893 5.46957 21 6 21L18 21C18.5304 21 19.0391 20.7893 19.4142 20.4142C19.7893 20.0391 20 19.5304 20 19L20 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
                <label for="cpf_frente" class="custom-file-label">Selecionar arquivo</label>
                <input type="file" name="cpf_frente" id="cpf_frente" class="custom-file-input" required accept="image/jpeg,image/png">
            </div>
        </div>

        <div class="form-group">
            <div class="document-title">Histórico</div>
            <div class="file-input-container">
                <div class="preview-container" id="preview-historico">
                    <div class="preview-content">
                        <svg class="upload-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 4L12 16M12 4L8 8M12 4L16 8M4 17L4 19C4 19.5304 4.21071 20.0391 4.58579 20.4142C4.96086 20.7893 5.46957 21 6 21L18 21C18.5304 21 19.0391 20.7893 19.4142 20.4142C19.7893 20.0391 20 19.5304 20 19L20 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
                <label for="historico" class="custom-file-label">Selecionar arquivo</label>
                <input type="file" name="historico" id="historico" class="custom-file-input" required accept="image/jpeg,image/png">
            </div>
        </div>

        <div class="form-group">
            <div class="document-title">Foto</div>
            <div class="file-input-container">
                <div class="preview-container" id="preview-foto">
                    <div class="preview-content">
                        <svg class="upload-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 4L12 16M12 4L8 8M12 4L16 8M4 17L4 19C4 19.5304 4.21071 20.0391 4.58579 20.4142C4.96086 20.7893 5.46957 21 6 21L18 21C18.5304 21 19.0391 20.7893 19.4142 20.4142C19.7893 20.0391 20 19.5304 20 19L20 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
                <label for="foto" class="custom-file-label">Selecionar arquivo</label>
                <input type="file" name="foto" id="foto" class="custom-file-input" required accept="image/jpeg,image/png">
            </div>
        </div>
    </div>

    <button type="submit">Enviar Documentos</button>
</form>

<script>
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        const file = input.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" style="max-width: 100%; max-height: 100%; object-fit: contain;">`;
            }
            reader.readAsDataURL(file);
        } else {
            preview.innerHTML = `
                <div class="preview-content">
                    <svg class="upload-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 4L12 16M12 4L8 8M12 4L16 8M4 17L4 19C4 19.5304 4.21071 20.0391 4.58579 20.4142C4.96086 20.7893 5.46957 21 6 21L18 21C18.5304 21 19.0391 20.7893 19.4142 20.4142C19.7893 20.0391 20 19.5304 20 19L20 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>`;
        }
    }

    // Função para criar preview da imagem
    function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            const file = input.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Limpa o conteúdo atual
                    preview.innerHTML = '';
                    // Cria e adiciona a imagem
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    preview.appendChild(img);
                }
                reader.readAsDataURL(file);
            } else {
                preview.innerHTML = `<span>Preview ${input.name}</span>`;
            }
        }

        // Adiciona os listeners para cada input
        document.getElementById('identidade_frente').addEventListener('change', function() {
            previewImage(this, 'preview-identidade-frente');
        });

        document.getElementById('identidade_verso').addEventListener('change', function() {
            previewImage(this, 'preview-identidade-verso');
        });

        document.getElementById('cpf_frente').addEventListener('change', function() {
            previewImage(this, 'preview-cpf-frente');
        });

        document.getElementById('historico').addEventListener('change', function() {
            previewImage(this, 'preview-historico');
        });

        document.getElementById('foto').addEventListener('change', function() {
            previewImage(this, 'preview-foto');
        });
</script>
</body>
</html>
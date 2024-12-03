<div class="container" id="form4">
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
            <div class="instruction"><center>Certifique-se que todos seus documentos estão corretos.</center></div>
            <div class="buttons">
                <button onclick="previousForm()">Anterior</button>
                <button class="cancel">Cancelar</button>
                <button type="submit" class="send">Enviar</button>
        </div>

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
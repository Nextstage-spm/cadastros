<?php
include("config.php");

// Configurações de debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Função para aprovar via AJAX
if(isset($_POST['aprovar_inscricao'])) {
    header('Content-Type: application/json');
    try {
        $id = $_POST['id'];
        
        $sql = "UPDATE aluno_cadastro SET status = 'aprovado' WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        echo json_encode(['success' => true, 'message' => 'Inscrição aprovada com sucesso!']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao aprovar inscrição: ' . $e->getMessage()]);
    }
    exit;
}

// Endpoint para marcar documento como pendente
if(isset($_POST['marcar_documento_pendente'])) {
    header('Content-Type: application/json');
    try {
        $aluno_id = $_POST['aluno_id'];
        $tipo_documento = $_POST['tipo_documento'];
        $observacao = $_POST['observacao'];
        
        $sql = "INSERT INTO documentos_pendentes (aluno_id, tipo_documento, observacao) 
                VALUES (:aluno_id, :tipo_documento, :observacao)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'aluno_id' => $aluno_id,
            'tipo_documento' => $tipo_documento,
            'observacao' => $observacao
        ]);
        
        // Atualizar status do aluno para pendente
        $sql_update = "UPDATE aluno_cadastro SET status = 'pendente' WHERE id = :id";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->execute(['id' => $aluno_id]);
        
        echo json_encode(['success' => true, 'message' => 'Documento marcado como pendente com sucesso!']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao marcar documento: ' . $e->getMessage()]);
    }
    exit;
}

// Buscar detalhes de uma inscrição específica
if(isset($_GET['get_details'])) {
    header('Content-Type: application/json');
    try {
        $id = $_GET['id'];
        
        // Buscar informações do aluno
        $sql = "SELECT 
                    a.*,
                    c.nome as nome_curso,
                    t.nome as nome_turno,
                    s.nome as nome_sexo,
                    e.nome as nome_etnia,
                    ec.nome as nome_estado_civil,
                    p.nome as nome_serie
                FROM aluno_cadastro a 
                LEFT JOIN cursos c ON a.idcursos = c.id 
                LEFT JOIN turno t ON a.idturno = t.id
                LEFT JOIN sexo s ON a.idsexo = s.id
                LEFT JOIN etnia e ON a.idetnia = e.id
                LEFT JOIN estado_civil ec ON a.idestado_civil = ec.id
                LEFT JOIN periodo p ON a.idserie = p.id
                WHERE a.id = :id";
                
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        $detalhes = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Buscar pendências
        $sql_pendencias = "SELECT * FROM documentos_pendentes 
                          WHERE aluno_id = :aluno_id 
                          ORDER BY data_solicitacao DESC";
        $stmt_pendencias = $conn->prepare($sql_pendencias);
        $stmt_pendencias->execute(['aluno_id' => $id]);
        $pendencias = $stmt_pendencias->fetchAll(PDO::FETCH_ASSOC);
        
        if($detalhes) {
            // Processar imagens
            $imagens = [
                'identidade_frente' => $detalhes['identidade_frente'],
                'identidade_verso' => $detalhes['identidade_verso'],
                'cpf_frente' => $detalhes['cpf_frente'],
                'historico' => $detalhes['historico'],
                'foto' => $detalhes['foto']
            ];
            $detalhes['imagens'] = $imagens;
            $detalhes['pendencias'] = $pendencias;
            
            echo json_encode($detalhes);
        } else {
            throw new Exception('Inscrição não encontrada');
        }
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// Consulta principal
try {
    $sql = "SELECT 
                a.*,
                c.nome as nome_curso,
                t.nome as nome_turno,
                s.nome as nome_sexo,
                e.nome as nome_etnia,
                ec.nome as nome_estado_civil,
                p.nome as nome_serie
            FROM aluno_cadastro a 
            LEFT JOIN cursos c ON a.idcursos = c.id 
            LEFT JOIN turno t ON a.idturno = t.id
            LEFT JOIN sexo s ON a.idsexo = s.id
            LEFT JOIN etnia e ON a.idetnia = e.id
            LEFT JOIN estado_civil ec ON a.idestado_civil = ec.id
            LEFT JOIN periodo p ON a.idserie = p.id
            ORDER BY a.data_matricula DESC";
            
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $inscricoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Consulta para cursos (filtro)
    $sql_cursos = "SELECT id, nome FROM cursos ORDER BY nome";
    $stmt_cursos = $conn->prepare($sql_cursos);
    $stmt_cursos->execute();
    $cursos = $stmt_cursos->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    echo "Erro: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Espera - Validação de Inscrições</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* Estilos base */
        body {
            background-color: #f8f9fa;
        }

        /* Estilos para os Filtros */
        .filters {
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            margin-bottom: 25px;
        }

        /* Estilização da Barra de Pesquisa */
        .search-box {
            position: relative;
            margin-top: 27px;
        }

        .search-input {
            padding-left: 45px;
            height: 45px;
            font-size: 0.95rem;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }

        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        /* Estilização dos Selects */
        .filter-select {
            position: relative;
        }

        .form-select {
            height: 45px;
            padding: 0 15px;
            font-size: 0.95rem;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: #fff;
        }

        .form-select:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }

        /* Labels dos filtros */
        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 0.3rem;
        }

        /* Status indicator e cards */
        .status-indicator {
            width: 15px;
            height: 15px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
            transition: background-color 0.3s ease;
        }

        .status-aprovado {
            background-color: #28a745;
        }

        .status-pendente {
            background-color: #ffc107;
        }

        .inscription-card {
            border-left: 4px solid #ccc;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .inscription-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }

        .inscription-card.aprovado {
            border-left-color: #28a745;
        }

        .inscription-card.pendente {
            border-left-color: #ffc107;
        }

        /* Pendências */
        .pendencia-item {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
        }

        .pendencia-item .data {
            font-size: 0.8em;
            color: #6c757d;
        }

        .pendencia-item .observacao {
            margin-top: 5px;
            font-style: italic;
        }

        .documentos-checklist {
            max-height: 300px;
            overflow-y: auto;
        }

        .documento-item {
            padding: 10px;
            border-bottom: 1px solid #dee2e6;
        }

        .documento-item:last-child {
            border-bottom: none;
        }

        .documento-item label {
            margin-bottom: 0;
            cursor: pointer;
        }

        .documento-item:hover {
            background-color: #f8f9fa;
        }

        #observacao-pendencia {
            width: 100%;
            margin-top: 10px;
        }

        /* Botões e ações */
        .action-buttons {
            display: flex;
            gap: 10px;
        }

        /* Documentos no modal */
        .doc-preview {
            margin-bottom: 20px;
        }

        .doc-preview .card {
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }

        .doc-preview .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .doc-preview .card-img-top {
            height: 200px;
            object-fit: cover;
            object-position: center;
        }

        .doc-preview .card-body {
            padding: 10px;
        }

        .doc-preview .card-title {
            font-size: 14px;
            margin-bottom: 8px;
        }

        /* Modal */
        .modal-body {
            max-height: 80vh;
            overflow-y: auto;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.9em;
        }

        .status-badge.aprovado {
            background-color: #28a745;
            color: white;
        }

        .status-badge.pendente {
            background-color: #ffc107;
            color: black;
        }

        /* Badge */
        .badge {
            font-size: 0.8em;
            padding: 0.5em 0.8em;
        }

        /* Botões */
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        /* Contador de resultados */
        .results-count {
            font-size: 0.95rem;
            color: #2c3e50;
            margin: 1rem 0;
            padding: 12px 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .results-count i {
            color: #6c757d;
        }

        .results-count .text-muted {
            font-size: 0.9em;
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .filters .row {
                gap: 1rem;
            }
            
            .d-flex {
                flex-direction: column;
                gap: 1rem;
            }
            
            .filter-select {
                width: 100%;
            }

            .page-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>
<div class="container mt-5">
    
    <!-- Filtros -->
    <div class="filters">
        <div class="row g-3">
            <!-- Barra de Pesquisa -->
            <div class="col-md-6">
                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="form-control search-input" id="searchInput" placeholder="Buscar por nome do aluno...">
                </div>
            </div>
            
            <!-- Filtros -->
            <div class="col-md-6">
                <div class="d-flex gap-3">
                    <div class="filter-select flex-grow-1">
                        <label class="form-label text-muted small mb-1">Visualizar</label>
                        <select class="form-select" id="statusFilter">
                            <option value="todos">Todas as Inscrições</option>
                            <option value="pendente">Pendentes de Aprovação</option>
                            <option value="aprovado">Aprovadas</option>
                        </select>
                    </div>
                    <div class="filter-select flex-grow-1">
                        <label class="form-label text-muted small mb-1">Curso</label>
                        <select class="form-select" id="cursoFilter">
                            <option value="todos">Todos os Cursos</option>
                            <?php foreach ($cursos as $curso): ?>
                                <option value="<?php echo $curso['id']; ?>"><?php echo htmlspecialchars($curso['nome']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contador de Resultados -->
    <div class="results-count">
        <i class="fas fa-list-ul me-2"></i>
        <span id="resultCount">0</span> inscrição(ões) encontrada(s)
        <span class="text-muted">
            (Total de <?php echo count($inscricoes); ?> registro(s))
        </span>
    </div>

    <!-- Lista de Inscrições -->
    <div class="inscriptions-list">
        <?php foreach($inscricoes as $inscricao): ?>
            <div class="card inscription-card <?php echo $inscricao['status'] ?? 'pendente'; ?>" data-id="<?php echo $inscricao['id']; ?>">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="card-title mb-3">
                                <span class="status-indicator status-<?php echo $inscricao['status'] ?? 'pendente'; ?>"></span>
                                <?php echo htmlspecialchars($inscricao['nome']); ?>
                                <span class="badge <?php echo ($inscricao['status'] == 'aprovado') ? 'bg-success' : 'bg-warning'; ?> ms-2">
                                    <?php echo ($inscricao['status'] == 'aprovado') ? 'Aprovado' : 'Pendente'; ?>
                                </span>
                            </h5>
                            <div class="card-text">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-2">
                                            <i class="fas fa-graduation-cap me-2 text-muted"></i>
                                            <strong>Curso:</strong> <?php echo htmlspecialchars($inscricao['nome_curso']); ?>
                                        </p>
                                        <p class="mb-2">
                                            <i class="fas fa-calendar me-2 text-muted"></i>
                                            <strong>Data de Inscrição:</strong> <?php echo date('d/m/Y', strtotime($inscricao['data_matricula'])); ?>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-2">
                                            <i class="fas fa-envelope me-2 text-muted"></i>
                                            <strong>Email:</strong> <?php echo htmlspecialchars($inscricao['email']); ?>
                                        </p>
                                        <p class="mb-2">
                                            <i class="fas fa-phone me-2 text-muted"></i>
                                            <strong>Telefone:</strong> <?php echo htmlspecialchars($inscricao['telefone']); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="action-buttons d-flex flex-column align-items-end">
                                <button class="btn btn-primary mb-2" onclick="viewDetails(<?php echo $inscricao['id']; ?>)">
                                    <i class="fas fa-eye me-2"></i>Ver Detalhes
                                </button>
                                <?php if($inscricao['status'] != 'aprovado'): ?>
                                    <button class="btn btn-success" onclick="aprovarInscricao(<?php echo $inscricao['id']; ?>)">
                                        <i class="fas fa-check me-2"></i>Aprovar Inscrição
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal de Detalhes -->
<div class="modal fade" id="detailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-graduate me-2"></i>
                    Detalhes da Inscrição
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2">
                            <i class="fas fa-user me-2"></i>
                            Informações Pessoais
                        </h6>
                        <p><strong>Nome:</strong> <span id="modal-nome"></span></p>
                        <p><strong>Email:</strong> <span id="modal-email"></span></p>
                        <p><strong>Data de Nascimento:</strong> <span id="modal-nascimento"></span></p>
                        <p><strong>Telefone:</strong> <span id="modal-telefone"></span></p>
                        <p><strong>Sexo:</strong> <span id="modal-sexo"></span></p>
                        <p><strong>Etnia:</strong> <span id="modal-etnia"></span></p>
                        <p><strong>Estado Civil:</strong> <span id="modal-estado-civil"></span></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2">
                            <i class="fas fa-graduation-cap me-2"></i>
                            Informações da Inscrição
                        </h6>
                        <p><strong>Curso:</strong> <span id="modal-curso"></span></p>
                        <p><strong>Série/Período:</strong> <span id="modal-serie"></span></p>
                        <p><strong>Turno:</strong> <span id="modal-turno"></span></p>
                        <p><strong>Data da Inscrição:</strong> <span id="modal-data-inscricao"></span></p>
                        <p><strong>Status:</strong> <span id="modal-status" class="badge"></span></p>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-12">
                        <h6 class="border-bottom pb-2">
                            <i class="fas fa-file-alt me-2"></i>
                            Documentos Enviados
                        </h6>
                        <div id="documentos-container" class="row">
                            <!-- Documentos serão inseridos aqui via JavaScript -->
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <h6 class="border-bottom pb-2">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Pendências
                        </h6>
                        <div id="pendencias-container">
                            <!-- Lista de pendências será inserida aqui -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Fechar
                </button>
                <button type="button" class="btn btn-warning" onclick="abrirModalPendencias()">
                    <i class="fas fa-exclamation-triangle me-2"></i>Marcar Pendências
                </button>
                <button type="button" class="btn btn-success" id="btn-aprovar" data-id="">
                    <i class="fas fa-check me-2"></i>Aprovar Inscrição
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Pendências -->
<div class="modal fade" id="pendenciasModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Marcar Documentos Pendentes
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formPendencias">
                    <div class="documentos-checklist">
                        <!-- Os checkboxes serão inseridos aqui via JavaScript -->
                    </div>
                    <div class="mt-3">
                        <label class="form-label">Observações Gerais:</label>
                        <textarea class="form-control" id="observacoes-gerais" rows="3" 
                                placeholder="Descreva as observações sobre os documentos pendentes..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-warning" onclick="salvarPendencias()">
                    <i class="fas fa-save me-2"></i>Salvar Pendências
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let alunoIdAtual = null; // Variável global para armazenar o ID do aluno atual

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar todos os modais
    var modals = document.querySelectorAll('.modal');
    modals.forEach(function(modal) {
        new bootstrap.Modal(modal);
    });

    // Inicializar a contagem
    updateInscriptionCount();

    // Adicionar event listeners para os filtros
    document.getElementById('statusFilter').addEventListener('change', filterInscriptions);
    document.getElementById('cursoFilter').addEventListener('change', filterInscriptions);
    document.getElementById('searchInput').addEventListener('input', filterInscriptions);
});

function updateInscriptionCount() {
    const visibleCards = document.querySelectorAll('.inscription-card:not([style*="display: none"])').length;
    document.getElementById('resultCount').textContent = visibleCards;
}

function filterInscriptions() {
    const statusFilter = document.getElementById('statusFilter').value;
    const cursoFilter = document.getElementById('cursoFilter').value;
    const searchText = document.getElementById('searchInput').value.toLowerCase();
    
    document.querySelectorAll('.inscription-card').forEach(card => {
        const status = card.classList.contains('aprovado') ? 'aprovado' : 'pendente';
        const curso = card.querySelector('.card-text').textContent;
        const nome = card.querySelector('.card-title').textContent.toLowerCase();
        
        const statusMatch = statusFilter === 'todos' || status === statusFilter;
        const cursoMatch = cursoFilter === 'todos' || curso.includes(cursoFilter);
        const searchMatch = nome.includes(searchText);
        
        card.style.display = statusMatch && cursoMatch && searchMatch ? 'block' : 'none';
    });

    updateInscriptionCount();
}

function abrirModalPendencias() {
    const detailsModal = bootstrap.Modal.getInstance(document.getElementById('detailsModal'));
    detailsModal.hide(); // Esconde o modal de detalhes

    // Lista de documentos possíveis
    const documentos = [
        { id: 'rg_frente', nome: 'RG (Frente)' },
        { id: 'rg_verso', nome: 'RG (Verso)' },
        { id: 'cpf', nome: 'CPF' },
        { id: 'historico', nome: 'Histórico' },
        { id: 'foto', nome: 'Foto' }
    ];

    // Preenche a checklist de documentos
    const checklistContainer = document.querySelector('.documentos-checklist');
    checklistContainer.innerHTML = documentos.map(doc => `
        <div class="documento-item">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" 
                       id="check-${doc.id}" name="documentos[]" 
                       value="${doc.nome}">
                <label class="form-check-label" for="check-${doc.id}">
                    ${doc.nome}
                </label>
            </div>
        </div>
    `).join('');

    // Limpa as observações
    document.getElementById('observacoes-gerais').value = '';

    // Mostra o modal de pendências
    const pendenciasModal = new bootstrap.Modal(document.getElementById('pendenciasModal'));
    pendenciasModal.show();
}

function salvarPendencias() {
    const documentosSelecionados = Array.from(document.querySelectorAll('input[name="documentos[]"]:checked'))
        .map(checkbox => checkbox.value);
    
    const observacoes = document.getElementById('observacoes-gerais').value;

    if (documentosSelecionados.length === 0) {
        Swal.fire({
            title: 'Atenção!',
            text: 'Selecione pelo menos um documento pendente.',
            icon: 'warning',
            confirmButtonColor: '#ffc107'
        });
        return;
    }

    if (!observacoes.trim()) {
        Swal.fire({
            title: 'Atenção!',
            text: 'Por favor, adicione observações sobre as pendências.',
            icon: 'warning',
            confirmButtonColor: '#ffc107'
        });
        return;
    }

    // Mostra loading
    Swal.fire({
        title: 'Processando...',
        html: 'Salvando as pendências...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Para cada documento selecionado, cria uma pendência
    const promises = documentosSelecionados.map(documento => {
        return fetch(window.location.pathname, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `marcar_documento_pendente=1&aluno_id=${alunoIdAtual}&tipo_documento=${encodeURIComponent(documento)}&observacao=${encodeURIComponent(observacoes)}`
        }).then(response => response.json());
    });

    Promise.all(promises)
        .then(results => {
            const pendenciasModal = bootstrap.Modal.getInstance(document.getElementById('pendenciasModal'));
            pendenciasModal.hide();

            Swal.fire({
                title: 'Sucesso!',
                text: 'Pendências registradas com sucesso!',
                icon: 'success',
                confirmButtonColor: '#28a745'
            }).then(() => {
                // Recarrega os detalhes do aluno
                viewDetails(alunoIdAtual);
            });
        })
        .catch(error => {
            Swal.fire({
                title: 'Erro!',
                text: 'Erro ao registrar pendências: ' + error.message,
                icon: 'error',
                confirmButtonColor: '#dc3545'
            });
        });
}

function viewDetails(id) {
    alunoIdAtual = id; // Armazena o ID do aluno atual
    console.log('Função viewDetails chamada com ID:', id);

    fetch(`${window.location.pathname}?get_details=1&id=${id}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro na resposta da rede');
            }
            return response.json();
        })
        .then(data => {
            if (!data) {
                throw new Error('Nenhum dado recebido');
            }

            // Preencher informações pessoais
            document.getElementById('modal-nome').textContent = data.nome || '';
            document.getElementById('modal-email').textContent = data.email || '';
            document.getElementById('modal-nascimento').textContent = formatDate(data.data_nascimento) || '';
            document.getElementById('modal-telefone').textContent = data.telefone || '';
            document.getElementById('modal-sexo').textContent = data.nome_sexo || '';
            document.getElementById('modal-etnia').textContent = data.nome_etnia || '';
            document.getElementById('modal-estado-civil').textContent = data.nome_estado_civil || '';
            
            // Preencher informações da inscrição
            document.getElementById('modal-curso').textContent = data.nome_curso || '';
            document.getElementById('modal-serie').textContent = data.nome_serie || '';
            document.getElementById('modal-turno').textContent = data.nome_turno || '';
            document.getElementById('modal-data-inscricao').textContent = formatDate(data.data_matricula) || '';
            
            // Status com badge
            const statusBadge = document.getElementById('modal-status');
            const isAprovado = data.status === 'aprovado';
            statusBadge.textContent = isAprovado ? 'Aprovado' : 'Pendente';
            statusBadge.className = `badge ${isAprovado ? 'bg-success' : 'bg-warning'}`;
            
            // Configurar botões
            const btnAprovar = document.getElementById('btn-aprovar');
            const btnPendencias = document.querySelector('.btn-warning[onclick="abrirModalPendencias()"]');
            
            if (isAprovado) {
                btnAprovar.style.display = 'none';
                btnPendencias.style.display = 'none';
            } else {
                btnAprovar.style.display = 'block';
                btnPendencias.style.display = 'block';
                btnAprovar.setAttribute('data-id', id);
                btnAprovar.onclick = () => aprovarInscricao(id);
            }
            
            // Limpar e preencher container de documentos
            const docsContainer = document.getElementById('documentos-container');
            docsContainer.innerHTML = '';
            
            // Array com os documentos a serem exibidos
            const documentos = [
                { tipo: 'RG (Frente)', arquivo: data.imagens?.identidade_frente },
                { tipo: 'RG (Verso)', arquivo: data.imagens?.identidade_verso },
                { tipo: 'CPF', arquivo: data.imagens?.cpf_frente },
                { tipo: 'Histórico', arquivo: data.imagens?.historico },
                { tipo: 'Foto', arquivo: data.imagens?.foto }
            ];

            // Criar elementos para cada documento
            documentos.forEach(doc => {
                if (doc.arquivo) {
                    const docDiv = document.createElement('div');
                    docDiv.className = 'col-md-4 doc-preview';
                    docDiv.innerHTML = `
                        <div class="card">
                            <img src="${doc.arquivo}" class="card-img-top" alt="${doc.tipo}">
                            <div class="card-body">
                                <h6 class="card-title">${doc.tipo}</h6>
                                <a href="${doc.arquivo}" class="btn btn-sm btn-primary w-100" target="_blank">
                                    <i class="fas fa-search-plus me-2"></i>Ver em tamanho real
                                </a>
                            </div>
                        </div>
                    `;
                    docsContainer.appendChild(docDiv);
                }
            });

            // Preencher pendências
            const pendenciasContainer = document.getElementById('pendencias-container');
            pendenciasContainer.innerHTML = '';
            
            if (data.pendencias && data.pendencias.length > 0) {
                data.pendencias.forEach(pendencia => {
                    const pendenciaDiv = document.createElement('div');
                    pendenciaDiv.className = 'pendencia-item';
                    pendenciaDiv.innerHTML = `
                        <div class="d-flex justify-content-between align-items-start">
                            <strong>${pendencia.tipo_documento}</strong>
                            <span class="data">${formatDate(pendencia.data_solicitacao)}</span>
                        </div>
                        <div class="observacao">${pendencia.observacao}</div>
                    `;
                    pendenciasContainer.appendChild(pendenciaDiv);
                });
            } else {
                pendenciasContainer.innerHTML = '<p class="text-muted">Nenhuma pendência registrada.</p>';
            }

            // Exibir modal
            const modal = new bootstrap.Modal(document.getElementById('detailsModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Erro ao carregar detalhes:', error);
            Swal.fire({
                title: 'Erro!',
                text: 'Erro ao carregar os detalhes. Por favor, tente novamente.',
                icon: 'error',
                confirmButtonColor: '#dc3545'
            });
        });
}

function aprovarInscricao(id) {
    Swal.fire({
        title: 'Confirmar aprovação',
        text: "Deseja aprovar está inscrição? Essa ação não pode ser desfeita.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#dc3545',
        confirmButtonText: 'Sim, aprovar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Processando...',
                html: 'Por favor, aguarde enquanto a inscrição é aprovada.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(`${window.location.pathname}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `aprovar_inscricao=1&id=${id}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const card = document.querySelector(`.inscription-card[data-id="${id}"]`);
                    if (card) {
                        card.classList.remove('pendente');
                        card.classList.add('aprovado');
                        
                        const statusBadge = card.querySelector('.badge');
                        if (statusBadge) {
                            statusBadge.classList.remove('bg-warning');
                            statusBadge.classList.add('bg-success');
                            statusBadge.textContent = 'Aprovado';
                        }
                        
                        const statusIndicator = card.querySelector('.status-indicator');
                        if (statusIndicator) {
                            statusIndicator.classList.remove('status-pendente');
                            statusIndicator.classList.add('status-aprovado');
                        }

                        const approveButton = card.querySelector('button.btn-success');
                        if (approveButton) {
                            approveButton.remove();
                        }
                    }

                    // Fechar o modal se estiver aberto
                    const modal = bootstrap.Modal.getInstance(document.getElementById('detailsModal'));
                    if (modal) {
                        modal.hide();
                    }

                    Swal.fire({
                        title: 'Aprovado!',
                        text: 'Inscrição aprovada com sucesso!',
                        icon: 'success',
                        confirmButtonColor: '#28a745'
                    });
                } else {
                    Swal.fire({
                        title: 'Erro!',
                        text: data.message || 'Erro ao aprovar inscrição',
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                    });
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                Swal.fire({
                    title: 'Erro!',
                    text: 'Erro ao aprovar inscrição',
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                });
            });
        }
    });
}

function formatDate(dateString) {
    if (!dateString) return '';
    const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
    return new Date(dateString).toLocaleDateString('pt-BR', options);
}
</script>

</body>
</html>
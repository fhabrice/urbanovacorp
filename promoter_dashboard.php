<?php
/**
 * Dashboard pour porteurs de projet (Version Production)
 * Avec formulaire multi-étapes intégré et authentification réelle
 */

// Définir les constantes avant d'inclure config.php
define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH . '/app');
define('PUBLIC_PATH', BASE_PATH);
define('CONFIG_PATH', BASE_PATH . '/config');

session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit;
}

require_once __DIR__ . '/app/Core/Database.php';

try {
    $config = [
        'host' => 'localhost',
        'port' => '3306',
        'name' => 'urbanova_db',
        'user' => 'root',
        'password' => '',
        'charset' => 'utf8mb4'
    ];
    $db = new App\Core\Database($config);
    $pdo = $db->getConnection();
    
    // Récupérer les projets de l'utilisateur
    $stmt = $pdo->prepare("
        SELECT p.*, 
               (SELECT COUNT(*) FROM reservations r WHERE r.project_id = p.id) as reservation_count,
               (SELECT COUNT(*) FROM visit_requests v WHERE v.project_id = p.id) as visit_count
        FROM projects p 
        WHERE p.promoter_id = ? 
        ORDER BY p.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $error = $e->getMessage();
    $projects = [];
}
?>
<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Porteur - URBANOVA SOLUTIONS</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            dark: '#0B132B',
                            navy: '#1C2541',
                            blue: '#3A506B',
                            gold: '#D4AF37',
                            goldHover: '#F3E5AB',
                            light: '#F8FAFC',
                            success: '#10B981',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: bold;
        }
        .status-draft { background: #e9ecef; color: #495057; }
        .status-submitted { background: #fff3cd; color: #856404; }
        .status-under_review { background: #cce5ff; color: #004085; }
        .status-approved { background: #d4edda; color: #155724; }
        .status-published { background: #28a745; color: white; }
        .status-rejected { background: #f8d7da; color: #721c24; }
        .status-suspended { background: #6c757d; color: white; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 flex flex-col min-h-screen">

    <!-- Navigation Bar -->
    <header class="bg-brand-dark text-white sticky top-0 z-50 shadow-md border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <div class="flex items-center space-x-3 cursor-pointer" onclick="window.location.href='index.html'">
                    <div class="bg-gradient-to-r from-brand-gold to-yellow-500 p-2 rounded-lg text-brand-dark font-extrabold text-xl">
                        <i class="fa-solid fa-city"></i>
                    </div>
                    <div>
                        <span class="text-xl font-extrabold tracking-wider bg-gradient-to-r from-brand-gold via-yellow-200 to-white bg-clip-text text-transparent">URBANOVA</span>
                        <p class="text-[9px] text-slate-400 tracking-widest uppercase -mt-1">SOLUTIONS</p>
                    </div>
                </div>

                <!-- Navigation Links -->
                <nav class="hidden lg:flex space-x-1 xl:space-x-2 text-sm font-medium">
                    <a href="index.html" class="px-3 py-2 rounded-md transition-all text-slate-300 hover:text-white">Accueil</a>
                    <a href="index.html#marketplace" class="px-3 py-2 rounded-md transition-all text-slate-300 hover:text-white">Marketplace</a>
                    <a href="#" class="px-3 py-2 rounded-md transition-all text-brand-gold border-b-2 border-brand-gold">Tableau de bord</a>
                </nav>
                
                <!-- User Info -->
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <div class="text-sm font-medium text-white"><?php echo htmlspecialchars($_SESSION['name'] ?? 'Utilisateur'); ?></div>
                        <div class="text-xs text-slate-400">Porteur de projet</div>
                    </div>
                    <a href="api.php?action=logout" class="bg-slate-700 hover:bg-slate-600 text-white text-sm font-medium py-2 px-4 rounded-lg transition-all">
                        <i class="fa-solid fa-sign-out-alt mr-2"></i>Déconnexion
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Main Content Area -->
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Left Sidebar - Stats & Projects List -->
            <div class="lg:w-1/3 space-y-6">
                <!-- Stats Grid -->
                <div class="bg-white rounded-2xl shadow-lg border border-slate-200 p-6">
                    <h2 class="text-xl font-bold text-brand-dark mb-4">📊 Statistiques</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-slate-50 border border-slate-200 p-4 rounded-xl text-center">
                            <div class="text-3xl font-bold text-brand-gold mb-1"><?php echo count($projects); ?></div>
                            <div class="text-xs text-slate-600">Projets</div>
                        </div>
                        <div class="bg-slate-50 border border-slate-200 p-4 rounded-xl text-center">
                            <div class="text-3xl font-bold text-brand-gold mb-1"><?php echo array_sum(array_column($projects, 'reservation_count')); ?></div>
                            <div class="text-xs text-slate-600">Réservations</div>
                        </div>
                        <div class="bg-slate-50 border border-slate-200 p-4 rounded-xl text-center">
                            <div class="text-3xl font-bold text-brand-gold mb-1"><?php echo array_sum(array_column($projects, 'visit_count')); ?></div>
                            <div class="text-xs text-slate-600">Visites</div>
                        </div>
                        <div class="bg-slate-50 border border-slate-200 p-4 rounded-xl text-center">
                            <div class="text-3xl font-bold text-brand-gold mb-1">
                                <?php 
                                $published = count(array_filter($projects, function($p) { return $p['validation_status'] == 'published'; }));
                                echo $published;
                                ?>
                            </div>
                            <div class="text-xs text-slate-600">Publiés</div>
                        </div>
                    </div>
                </div>

                <!-- Projects List -->
                <div class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden">
                    <div class="p-4 border-b border-slate-200 flex justify-between items-center">
                        <h2 class="text-lg font-bold text-brand-dark">Vos projets</h2>
                        <button onclick="showProjectForm()" class="bg-brand-gold hover:bg-yellow-500 text-brand-dark text-xs font-bold py-2 px-4 rounded-lg transition-all">
                            <i class="fa-solid fa-plus mr-1"></i>Nouveau
                        </button>
                    </div>
                    
                    <?php if (empty($projects)): ?>
                        <div class="text-center py-8 px-4">
                            <i class="fa-solid fa-folder-open text-3xl text-slate-400 mb-3"></i>
                            <p class="text-sm text-slate-500">Aucun projet pour le moment</p>
                        </div>
                    <?php else: ?>
                        <div class="divide-y divide-slate-100 max-h-96 overflow-y-auto">
                            <?php foreach ($projects as $project): ?>
                                <div class="p-4 hover:bg-slate-50 cursor-pointer" onclick="viewProject(<?php echo $project['id']; ?>)">
                                    <div class="font-bold text-brand-dark text-sm"><?php echo htmlspecialchars($project['title']); ?></div>
                                    <div class="text-xs text-slate-500 mb-2"><?php echo htmlspecialchars($project['city']); ?></div>
                                    <span class="status-badge status-<?php echo $project['validation_status']; ?> text-xs">
                                        <?php echo translateStatus($project['validation_status']); ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Side - Form or Project Details -->
            <div class="lg:w-2/3">
                <!-- Create Project Form (Multi-step Wizard) -->
                <div id="projectFormContainer" class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                    <!-- Steps Indicator -->
                    <div class="bg-brand-navy text-white px-6 py-6 md:px-12 flex justify-between items-center border-b border-slate-800">
                        <div class="flex items-center space-x-3">
                            <div id="stepIndicator-1" class="w-8 h-8 rounded-full bg-brand-gold text-brand-dark flex items-center justify-center font-bold text-sm">1</div>
                            <span id="stepText-1" class="hidden md:inline text-xs font-bold uppercase tracking-wider text-brand-gold">Infos générales</span>
                        </div>
                        <div class="h-0.5 w-8 bg-slate-700"></div>
                        <div class="flex items-center space-x-3">
                            <div id="stepIndicator-2" class="w-8 h-8 rounded-full bg-slate-800 text-slate-400 border border-slate-700 flex items-center justify-center font-bold text-sm">2</div>
                            <span id="stepText-2" class="hidden md:inline text-xs font-bold uppercase tracking-wider text-slate-400">Financement</span>
                        </div>
                        <div class="h-0.5 w-8 bg-slate-700"></div>
                        <div class="flex items-center space-x-3">
                            <div id="stepIndicator-3" class="w-8 h-8 rounded-full bg-slate-800 text-slate-400 border border-slate-700 flex items-center justify-center font-bold text-sm">3</div>
                            <span id="stepText-3" class="hidden md:inline text-xs font-bold uppercase tracking-wider text-slate-400">Détails</span>
                        </div>
                        <div class="h-0.5 w-8 bg-slate-700"></div>
                        <div class="flex items-center space-x-3">
                            <div id="stepIndicator-4" class="w-8 h-8 rounded-full bg-slate-800 text-slate-400 border border-slate-700 flex items-center justify-center font-bold text-sm">4</div>
                            <span id="stepText-4" class="hidden md:inline text-xs font-bold uppercase tracking-wider text-slate-400">Vérification</span>
                        </div>
                    </div>

                    <!-- Form Steps -->
                    <div class="p-8 md:p-12">
                        <!-- Step 1: General Information -->
                        <div id="formStep-1" class="form-step">
                            <h2 class="text-2xl font-bold text-brand-dark mb-6">Informations générales</h2>
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-600 mb-2">Nom du projet *</label>
                                    <input type="text" id="projName" class="w-full border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-brand-gold focus:outline-none" placeholder="Ex: Résidence Horizon">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-600 mb-2">Promoteur / Société *</label>
                                    <input type="text" id="projOwner" class="w-full border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-brand-gold focus:outline-none" placeholder="Ex: SARL Horizon">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-600 mb-2">Localisation *</label>
                                    <input type="text" id="projLoc" class="w-full border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-brand-gold focus:outline-none" placeholder="Ex: Goma, RDC">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-600 mb-2">Secteur *</label>
                                    <select id="projSector" class="w-full border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-brand-gold focus:outline-none">
                                        <option value="">Sélectionnez un secteur</option>
                                        <option value="Résidentiel">Résidentiel</option>
                                        <option value="Commercial">Commercial</option>
                                        <option value="Bureaux">Bureaux</option>
                                        <option value="Hôtel">Hôtel</option>
                                        <option value="Industriel">Industriel</option>
                                        <option value="Mixte">Mixte</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-600 mb-2">Description du projet *</label>
                                    <textarea id="projDesc" rows="4" class="w-full border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-brand-gold focus:outline-none" placeholder="Décrivez votre projet immobilier en détail..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Financial Data -->
                        <div id="formStep-2" class="form-step hidden">
                            <h2 class="text-2xl font-bold text-brand-dark mb-6">Données financières</h2>
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-600 mb-2">Coût du projet (USD) *</label>
                                    <input type="number" id="projCost" class="w-full border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-brand-gold focus:outline-none" placeholder="Ex: 500000">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-600 mb-2">Financement recherché (USD) *</label>
                                    <input type="number" id="projTarget" class="w-full border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-brand-gold focus:outline-none" placeholder="Ex: 300000">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-600 mb-2">ROI attendu (%)</label>
                                    <input type="number" id="projRoi" class="w-full border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-brand-gold focus:outline-none" placeholder="Ex: 15" value="15">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-600 mb-2">Type de projet</label>
                                    <select id="projType" class="w-full border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-brand-gold focus:outline-none">
                                        <option value="residential">Résidentiel</option>
                                        <option value="commercial">Commercial</option>
                                        <option value="mixed">Mixte</option>
                                        <option value="industrial">Industriel</option>
                                        <option value="hotel">Hôtel</option>
                                        <option value="office">Bureau</option>
                                        <option value="subdivision">Lotissement</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-600 mb-2">Type d'opération</label>
                                    <select id="projOperation" class="w-full border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-brand-gold focus:outline-none">
                                        <option value="sale">Vente</option>
                                        <option value="rental">Location</option>
                                        <option value="fundraising">Levée de fonds</option>
                                        <option value="sale_fundraising">Vente + Levée de fonds</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Additional Details -->
                        <div id="formStep-3" class="form-step hidden">
                            <h2 class="text-2xl font-bold text-brand-dark mb-6">Détails supplémentaires</h2>
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-600 mb-2">Latitude (optionnel)</label>
                                    <input type="text" id="projLat" class="w-full border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-brand-gold focus:outline-none" placeholder="Ex: -4.4419">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-600 mb-2">Longitude (optionnel)</label>
                                    <input type="text" id="projLng" class="w-full border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-brand-gold focus:outline-none" placeholder="Ex: 15.2663">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-600 mb-2">Image URL (optionnel)</label>
                                    <input type="text" id="projImage" class="w-full border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-brand-gold focus:outline-none" placeholder="URL de l'image du projet">
                                </div>
                                <div class="bg-slate-50 border border-slate-200 p-4 rounded-xl">
                                    <p class="text-sm text-slate-600 mb-2"><i class="fa-solid fa-info-circle text-brand-gold mr-2"></i>Conseil:</p>
                                    <p class="text-xs text-slate-500">Ajoutez les coordonnées GPS pour permettre aux investisseurs de localiser votre projet sur une carte.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Step 4: Verification -->
                        <div id="formStep-4" class="form-step hidden">
                            <h2 class="text-2xl font-bold text-brand-dark mb-6">Vérification</h2>
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-6 space-y-4">
                                <div class="flex justify-between items-start">
                                    <span class="text-sm font-semibold text-slate-600">Nom du projet:</span>
                                    <span id="reviewName" class="text-sm font-bold text-brand-dark">-</span>
                                </div>
                                <div class="flex justify-between items-start">
                                    <span class="text-sm font-semibold text-slate-600">Promoteur:</span>
                                    <span id="reviewOwner" class="text-sm font-bold text-brand-dark">-</span>
                                </div>
                                <div class="flex justify-between items-start">
                                    <span class="text-sm font-semibold text-slate-600">Localisation:</span>
                                    <span id="reviewLoc" class="text-sm font-bold text-brand-dark">-</span>
                                </div>
                                <div class="flex justify-between items-start">
                                    <span class="text-sm font-semibold text-slate-600">Secteur:</span>
                                    <span id="reviewSector" class="text-sm font-bold text-brand-dark">-</span>
                                </div>
                                <div class="flex justify-between items-start">
                                    <span class="text-sm font-semibold text-slate-600">Financement recherché:</span>
                                    <span id="reviewTarget" class="text-sm font-bold text-brand-dark">-</span>
                                </div>
                                <div class="flex justify-between items-start">
                                    <span class="text-sm font-semibold text-slate-600">ROI attendu:</span>
                                    <span id="reviewRoi" class="text-sm font-bold text-brand-dark">-</span>
                                </div>
                            </div>
                            <div class="mt-6 bg-amber-50 border border-amber-200 p-4 rounded-xl">
                                <p class="text-sm text-amber-800"><i class="fa-solid fa-exclamation-triangle mr-2"></i>En soumettant ce projet, vous confirmez que toutes les informations sont exactes et complètes.</p>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="flex justify-between mt-8">
                            <button id="prevStepBtn" onclick="navigateForm(-1)" class="invisible bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold px-8 py-3.5 rounded-xl text-sm transition-all">
                                Précédent
                            </button>
                            <button id="nextStepBtn" onclick="navigateForm(1)" class="bg-brand-navy hover:bg-slate-800 text-white font-bold px-8 py-3.5 rounded-xl text-sm transition-all shadow-md">
                                Suivant
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification -->
    <div id="notification" class="fixed top-4 right-4 px-6 py-4 rounded-xl shadow-lg z-50 hidden"></div>

    <script>
        let wizardStep = 1;

        function translateStatus(status) {
            const translations = {
                'draft': 'Brouillon',
                'submitted': 'Soumis',
                'under_review': 'En analyse',
                'additional_info': 'Infos complémentaires',
                'approved': 'Approuvé',
                'rejected': 'Rejeté',
                'published': 'Publié',
                'suspended': 'Suspendu',
                'sold': 'Vendu',
                'rented': 'Loué',
                'archivé': 'Archivé'
            };
            return translations[status] || status;
        }

        function showProjectForm() {
            document.getElementById('projectFormContainer').classList.remove('hidden');
            resetForm();
        }

        function resetForm() {
            wizardStep = 1;
            document.querySelectorAll('.form-step').forEach(step => step.classList.add('hidden'));
            document.getElementById('formStep-1').classList.remove('hidden');
            updateStepIndicators();
            document.getElementById('prevStepBtn').classList.add('invisible');
            document.getElementById('nextStepBtn').innerText = 'Suivant';
            document.getElementById('nextStepBtn').className = 'bg-brand-navy hover:bg-slate-800 text-white font-bold px-8 py-3.5 rounded-xl text-sm transition-all shadow-md';
        }

        function navigateForm(direction) {
            const currentForm = document.getElementById(`formStep-${wizardStep}`);
            
            // Validation
            if (direction === 1) {
                if (wizardStep === 1) {
                    const name = document.getElementById('projName').value;
                    const owner = document.getElementById('projOwner').value;
                    const loc = document.getElementById('projLoc').value;
                    const sector = document.getElementById('projSector').value;
                    const desc = document.getElementById('projDesc').value;
                    if (!name || !owner || !loc || !sector || !desc) {
                        showNotification('Champs requis', 'Veuillez remplir tous les champs obligatoires (*)', 'error');
                        return;
                    }
                }
                if (wizardStep === 2) {
                    const cost = document.getElementById('projCost').value;
                    const target = document.getElementById('projTarget').value;
                    if (!cost || !target) {
                        showNotification('Données financières', 'Veuillez renseigner le coût et le financement cible.', 'error');
                        return;
                    }
                }
                if (wizardStep === 3) {
                    // Populate review
                    document.getElementById('reviewName').textContent = document.getElementById('projName').value;
                    document.getElementById('reviewOwner').textContent = document.getElementById('projOwner').value;
                    document.getElementById('reviewLoc').textContent = document.getElementById('projLoc').value;
                    document.getElementById('reviewSector').textContent = document.getElementById('projSector').value;
                    document.getElementById('reviewTarget').textContent = parseInt(document.getElementById('projTarget').value).toLocaleString() + ' $';
                    document.getElementById('reviewRoi').textContent = document.getElementById('projRoi').value + '%';
                }
            }

            currentForm.classList.add('hidden');
            wizardStep += direction;

            if (wizardStep > 4) {
                submitProject();
                return;
            }

            if (wizardStep < 1) {
                wizardStep = 1;
            }

            const nextForm = document.getElementById(`formStep-${wizardStep}`);
            nextForm.classList.remove('hidden');

            updateStepIndicators();
        }

        function updateStepIndicators() {
            const prevBtn = document.getElementById('prevStepBtn');
            if (wizardStep > 1) {
                prevBtn.classList.remove('invisible');
            } else {
                prevBtn.classList.add('invisible');
            }

            const nextBtn = document.getElementById('nextStepBtn');
            if (wizardStep === 4) {
                nextBtn.innerText = 'Finaliser la soumission';
                nextBtn.className = 'bg-brand-success hover:bg-emerald-600 text-white font-bold px-8 py-3.5 rounded-xl text-sm transition-all shadow-md';
            } else {
                nextBtn.innerText = 'Suivant';
                nextBtn.className = 'bg-brand-navy hover:bg-slate-800 text-white font-bold px-8 py-3.5 rounded-xl text-sm transition-all shadow-md';
            }

            for (let i = 1; i <= 4; i++) {
                const indicator = document.getElementById(`stepIndicator-${i}`);
                const text = document.getElementById(`stepText-${i}`);

                if (i === wizardStep) {
                    indicator.className = 'w-8 h-8 rounded-full bg-brand-gold text-brand-dark flex items-center justify-center font-bold text-sm';
                    if (text) text.className = 'hidden md:inline text-xs font-bold uppercase tracking-wider text-brand-gold';
                } else if (i < wizardStep) {
                    indicator.className = 'w-8 h-8 rounded-full bg-brand-success text-white flex items-center justify-center font-bold text-sm';
                    if (text) text.className = 'hidden md:inline text-xs font-bold uppercase tracking-wider text-brand-success';
                } else {
                    indicator.className = 'w-8 h-8 rounded-full bg-slate-800 text-slate-400 border border-slate-700 flex items-center justify-center font-bold text-sm';
                    if (text) text.className = 'hidden md:inline text-xs font-bold uppercase tracking-wider text-slate-400';
                }
            }
        }

        async function submitProject() {
            const projectData = {
                name: document.getElementById('projName').value,
                owner: document.getElementById('projOwner').value,
                location: document.getElementById('projLoc').value,
                sector: document.getElementById('projSector').value,
                description: document.getElementById('projDesc').value,
                cost: document.getElementById('projCost').value,
                target: document.getElementById('projTarget').value,
                roi: document.getElementById('projRoi').value,
                project_type: document.getElementById('projType').value,
                operation_type: document.getElementById('projOperation').value,
                coordinates_lat: document.getElementById('projLat').value,
                coordinates_lng: document.getElementById('projLng').value,
                image: document.getElementById('projImage').value
            };

            try {
                const response = await fetch('api.php?action=submit-project', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(projectData)
                });

                const result = await response.json();

                if (result.success) {
                    showNotification('Succès', 'Projet soumis avec succès !', 'success');
                    resetForm();
                    setTimeout(() => location.reload(), 2000);
                } else {
                    showNotification('Erreur', result.message, 'error');
                }
            } catch (error) {
                showNotification('Erreur', 'Erreur de connexion', 'error');
            }
        }

        function viewProject(projectId) {
            showNotification('Info', 'Fonctionnalité en développement', 'success');
        }

        function showNotification(title, message, type) {
            const notification = document.getElementById('notification');
            notification.innerHTML = `<strong>${title}</strong><br>${message}`;
            notification.className = 'fixed top-4 right-4 px-6 py-4 rounded-xl shadow-lg z-50';
            
            if (type === 'success') {
                notification.classList.add('bg-green-500', 'text-white');
            } else {
                notification.classList.add('bg-red-500', 'text-white');
            }
            
            notification.classList.remove('hidden');
            
            setTimeout(() => {
                notification.classList.add('hidden');
            }, 3000);
        }
    </script>
</body>
</html>

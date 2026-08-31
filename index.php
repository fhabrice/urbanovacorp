<?php
/**
 * Configuration partagée (mot de passe admin, etc.)
 */
if (!defined('CONFIG_PATH')) {
    define('CONFIG_PATH', __DIR__ . '/config');
}
$siteConfig = file_exists(CONFIG_PATH . '/config.php') ? require CONFIG_PATH . '/config.php' : [];
$adminPassword = htmlspecialchars($siteConfig['security']['admin_password'] ?? 'urbanova', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URBANOVA SOLUTIONS - Plateforme Web Corporate & Investissement</title>
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

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #0B132B;
        }

        ::-webkit-scrollbar-thumb {
            background: #D4AF37;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #F3E5AB;
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
                <div class="flex items-center space-x-3 cursor-pointer" onclick="switchTab('accueil')">
                    <div
                        class="bg-gradient-to-r from-brand-gold to-yellow-500 p-2 rounded-lg text-brand-dark font-extrabold text-xl">
                        <i class="fa-solid fa-city"></i>
                    </div>
                    <div>
                        <span
                            class="text-xl font-extrabold tracking-wider bg-gradient-to-r from-brand-gold via-yellow-200 to-white bg-clip-text text-transparent">URBANOVA</span>
                        <p class="text-[9px] text-slate-400 tracking-widest uppercase -mt-1">SOLUTIONS</p>
                    </div>
                </div>

                <!-- Navigation Tabs -->
                <nav class="hidden lg:flex space-x-1 xl:space-x-2 text-sm font-medium">
                    <button onclick="switchTab('accueil')"
                        class="nav-link px-3 py-2 rounded-md transition-all text-brand-gold border-b-2 border-brand-gold"
                        id="nav-accueil">Accueil</button>
                    <button onclick="switchTab('apropos')"
                        class="nav-link px-3 py-2 rounded-md transition-all text-slate-300 hover:text-white"
                        id="nav-apropos">À propos</button>
                    <button onclick="goToMarketplace()"
                        class="nav-link px-3 py-2 rounded-md transition-all text-slate-300 hover:text-white"
                        id="nav-marketplace">Marketplace</button>
                    <button onclick="switchTab('promoteur')"
                        class="nav-link px-3 py-2 rounded-md transition-all text-slate-300 hover:text-white"
                        id="nav-promoteur">Tableau de bord</button>
                    <button onclick="switchTab('investisseur')"
                        class="nav-link px-3 py-2 rounded-md transition-all text-slate-300 hover:text-white"
                        id="nav-investisseur">Espace Investisseur <span class="text-xs ml-1 opacity-75">(réservé)</span></button>
                    <button onclick="switchTab('admin')"
                        class="nav-link px-3 py-2 rounded-md transition-all text-slate-300 hover:text-white"
                        id="nav-admin">Admin</button>
                    <button onclick="switchTab('actualites')"
                        class="nav-link px-3 py-2 rounded-md transition-all text-slate-300 hover:text-white"
                        id="nav-actualites">Actualités</button>
                    <button onclick="switchTab('contact')"
                        class="nav-link px-3 py-2 rounded-md transition-all text-slate-300 hover:text-white"
                        id="nav-contact">Contact</button>
                </nav>

                <!-- Actions -->
                <div class="hidden md:flex items-center space-x-4" id="headerActions">
                    <button onclick="openLoginModal()"
                        class="text-slate-300 hover:text-white text-sm font-medium transition-all">Se connecter</button>
                    <button onclick="openRegisterModal()"
                        class="bg-brand-gold hover:bg-yellow-500 text-brand-dark px-5 py-2.5 rounded-lg text-sm font-bold shadow-lg shadow-brand-gold/20 transition-all hover:-translate-y-0.5">S'inscrire</button>
                </div>

                <!-- Mobile menu button -->
                <div class="lg:hidden">
                    <button onclick="toggleMobileMenu()"
                        class="text-slate-300 hover:text-white focus:outline-none p-2 rounded-md"
                        aria-label="Ouvrir le menu">
                        <i class="fa-solid fa-bars text-2xl" id="menu-icon"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu (Hidden by default) -->
        <div id="mobile-menu" class="hidden lg:hidden bg-brand-navy border-t border-slate-800 px-4 pt-2 pb-4 space-y-1">
            <button onclick="switchTab('accueil'); toggleMobileMenu()"
                class="block w-full text-left px-3 py-2.5 rounded-md text-brand-gold font-medium">Accueil</button>
            <button onclick="switchTab('apropos'); toggleMobileMenu()"
                class="block w-full text-left px-3 py-2.5 rounded-md text-slate-300 hover:text-white font-medium">À propos</button>
            <button onclick="goToMarketplace(); toggleMobileMenu()"
                class="block w-full text-left px-3 py-2.5 rounded-md text-slate-300 hover:text-white font-medium">Marketplace</button>
            <button onclick="goToPromoterDashboard(); toggleMobileMenu()"
                class="block w-full text-left px-3 py-2.5 rounded-md text-slate-300 hover:text-white font-medium">Tableau de bord</button>
            <button onclick="switchTab('investisseur'); toggleMobileMenu()"
                class="block w-full text-left px-3 py-2.5 rounded-md text-slate-300 hover:text-white font-medium">Espace Investisseur <span class="text-xs opacity-75">(réservé)</span></button>
            <button onclick="switchTab('admin'); toggleMobileMenu()"
                class="block w-full text-left px-3 py-2.5 rounded-md text-slate-300 hover:text-white font-medium">Admin</button>
            <button onclick="switchTab('actualites'); toggleMobileMenu()"
                class="block w-full text-left px-3 py-2.5 rounded-md text-slate-300 hover:text-white font-medium">Actualités</button>
            <button onclick="switchTab('contact'); toggleMobileMenu()"
                class="block w-full text-left px-3 py-2.5 rounded-md text-slate-300 hover:text-white font-medium">Contact</button>
            <div class="pt-4 border-t border-slate-700 flex flex-col space-y-2" id="mobileMenuAuth">
                <button onclick="openLoginModal(); toggleMobileMenu()"
                    class="w-full text-center py-2 text-slate-300 hover:text-white font-medium">Se connecter</button>
                <button onclick="openRegisterModal(); toggleMobileMenu()"
                    class="w-full text-center py-2.5 bg-brand-gold text-brand-dark rounded-md font-bold">S'inscrire</button>
            </div>
        </div>
    </header>

    <!-- Main Container for dynamic pages -->
    <main class="flex-grow">

        <!-- ================= PAGE 1: ACCUEIL ================= -->
        <div id="page-accueil" class="tab-content">
            <!-- Hero Section with Background -->
            <section class="relative bg-brand-dark text-white py-24 md:py-36 overflow-hidden">
                <div class="absolute inset-0 bg-cover bg-center mix-blend-overlay opacity-30"
                    style="background-image: url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=1920&q=80')">
                </div>
                <!-- Radial golden light source simulation -->
                <div class="absolute -top-40 -left-40 w-96 h-96 bg-brand-gold/10 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-yellow-500/10 rounded-full blur-3xl"></div>

                <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <p class="text-brand-gold uppercase tracking-widest font-semibold text-sm mb-3">URBANOVA SOLUTIONS
                    </p>
                    <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight mb-6 max-w-4xl leading-tight">
                        Structurer les villes <br><span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-brand-gold to-yellow-400">africaines
                            de demain</span>
                    </h1>
                    <p class="text-slate-300 text-lg md:text-xl max-w-2xl mb-10 leading-relaxed">
                        Construction, Immobilier, Infrastructures et Investissements pour un développement durable en
                        République Démocratique du Congo et Afrique Centrale.
                    </p>

                    <!-- Calls to Action -->
                    <div class="flex flex-wrap gap-4 mb-16">
                        <button onclick="switchTab('contact')"
                            class="bg-brand-gold hover:bg-yellow-500 text-brand-dark font-bold px-8 py-4 rounded-xl shadow-xl shadow-brand-gold/20 transition-all hover:-translate-y-1">Demander
                            un devis</button>
                        <button onclick="goToPromoterDashboard()"
                            class="border-2 border-slate-400 hover:border-brand-gold text-white font-semibold px-8 py-4 rounded-xl hover:bg-white/5 transition-all hover:-translate-y-1">Soumettre
                            un projet</button>
                        <button onclick="goToMarketplace()"
                            class="border-2 border-slate-400 hover:border-brand-gold text-white font-semibold px-8 py-4 rounded-xl hover:bg-white/5 transition-all hover:-translate-y-1">Investir
                            avec nous</button>
                        <button onclick="switchTab('contact')"
                            class="border-2 border-slate-400 hover:border-brand-gold text-white font-semibold px-8 py-4 rounded-xl hover:bg-white/5 transition-all hover:-translate-y-1">Devenir
                            partenaire</button>
                    </div>

                    <!-- Key Stats Display -->
                    <div
                        class="grid grid-cols-2 md:grid-cols-5 gap-6 bg-brand-navy/60 backdrop-blur-md border border-slate-700/50 p-6 md:p-8 rounded-2xl">
                        <div class="text-center md:border-r border-slate-700/60 last:border-0 p-2">
                            <span class="block text-3xl md:text-4xl font-extrabold text-brand-gold mb-1">120+</span>
                            <span class="text-xs md:text-sm text-slate-300">Projets réalisés</span>
                        </div>
                        <div class="text-center md:border-r border-slate-700/60 last:border-0 p-2">
                            <span class="block text-3xl md:text-4xl font-extrabold text-brand-gold mb-1">45 M$</span>
                            <span class="text-xs md:text-sm text-slate-300">Investissements mobilisés</span>
                        </div>
                        <div class="text-center md:border-r border-slate-700/60 last:border-0 p-2">
                            <span class="block text-3xl md:text-4xl font-extrabold text-brand-gold mb-1">2 500</span>
                            <span class="text-xs md:text-sm text-slate-300">Logements développés</span>
                        </div>
                        <div class="text-center md:border-r border-slate-700/60 last:border-0 p-2">
                            <span class="block text-3xl md:text-4xl font-extrabold text-brand-gold mb-1">5 000</span>
                            <span class="text-xs md:text-sm text-slate-300">Emplois créés</span>
                        </div>
                        <div class="text-center col-span-2 md:col-span-1 p-2">
                            <span class="block text-3xl md:text-4xl font-extrabold text-brand-gold mb-1">4</span>
                            <span class="text-xs md:text-sm text-slate-300">Pays d'intervention <br><span
                                    class="text-[10px] text-slate-400">RDC • Rwanda • Ouganda • Burundi</span></span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Departments Section -->
            <section class="py-20 bg-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center max-w-3xl mx-auto mb-16">
                        <h2 class="text-3xl md:text-4xl font-extrabold text-brand-dark mb-4">Nos Départements</h2>
                        <p class="text-slate-600 text-lg">Une expertise multisectorielle au service du développement
                            urbain durable en République Démocratique du Congo.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <!-- Dept 1 -->
                        <div
                            class="border border-slate-100 hover:border-brand-gold/30 bg-slate-50 hover:bg-white p-8 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 group">
                            <div
                                class="w-14 h-14 bg-brand-gold/10 group-hover:bg-brand-gold text-brand-gold group-hover:text-brand-dark rounded-xl flex items-center justify-center text-2xl transition-all mb-6">
                                <i class="fa-solid fa-helmet-safety"></i>
                            </div>
                            <h3 class="text-xl font-bold text-brand-dark mb-3">Construction & Maintenance</h3>
                            <p class="text-slate-600">Édification et entretien d'ouvrages durables, respectueux de
                                l'environnement et des standards de sécurité modernes.</p>
                        </div>
                        <!-- Dept 2 -->
                        <div
                            class="border border-slate-100 hover:border-brand-gold/30 bg-slate-50 hover:bg-white p-8 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 group">
                            <div
                                class="w-14 h-14 bg-brand-gold/10 group-hover:bg-brand-gold text-brand-gold group-hover:text-brand-dark rounded-xl flex items-center justify-center text-2xl transition-all mb-6">
                                <i class="fa-solid fa-trash-can"></i>
                            </div>
                            <h3 class="text-xl font-bold text-brand-dark mb-3">Assainissement & Déchets</h3>
                            <p class="text-slate-600">Gestion environnementale intégrée, tri, recyclage et propreté
                                urbaine pour préserver la santé publique.</p>
                        </div>
                        <!-- Dept 3 -->
                        <div
                            class="border border-slate-100 hover:border-brand-gold/30 bg-slate-50 hover:bg-white p-8 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 group">
                            <div
                                class="w-14 h-14 bg-brand-gold/10 group-hover:bg-brand-gold text-brand-gold group-hover:text-brand-dark rounded-xl flex items-center justify-center text-2xl transition-all mb-6">
                                <i class="fa-solid fa-building-circle-check"></i>
                            </div>
                            <h3 class="text-xl font-bold text-brand-dark mb-3">Facility Management</h3>
                            <p class="text-slate-600">Exploitation, sécurisation, et maintenance d'infrastructures
                                d'envergure pour prolonger la durée de vie de vos actifs.</p>
                        </div>
                        <!-- Dept 4 -->
                        <div
                            class="border border-slate-100 hover:border-brand-gold/30 bg-slate-50 hover:bg-white p-8 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 group">
                            <div
                                class="w-14 h-14 bg-brand-gold/10 group-hover:bg-brand-gold text-brand-gold group-hover:text-brand-dark rounded-xl flex items-center justify-center text-2xl transition-all mb-6">
                                <i class="fa-solid fa-house-chimney-window"></i>
                            </div>
                            <h3 class="text-xl font-bold text-brand-dark mb-3">Immobilier & Aménagement</h3>
                            <p class="text-slate-600">Promotion immobilière et aménagement de zones urbaines intégrées
                                favorisant la mixité sociale et économique.</p>
                        </div>
                        <!-- Dept 5 -->
                        <div
                            class="border border-slate-100 hover:border-brand-gold/30 bg-slate-50 hover:bg-white p-8 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 group">
                            <div
                                class="w-14 h-14 bg-brand-gold/10 group-hover:bg-brand-gold text-brand-gold group-hover:text-brand-dark rounded-xl flex items-center justify-center text-2xl transition-all mb-6">
                                <i class="fa-solid fa-compass-drafting"></i>
                            </div>
                            <h3 class="text-xl font-bold text-brand-dark mb-3">Ingénierie</h3>
                            <p class="text-slate-600">Assistance technique de haut niveau, études de sol, architecture
                                moderne et conception modulaire de projets.</p>
                        </div>
                        <!-- Dept 6 -->
                        <div
                            class="border border-slate-100 hover:border-brand-gold/30 bg-slate-50 hover:bg-white p-8 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 group">
                            <div
                                class="w-14 h-14 bg-brand-gold/10 group-hover:bg-brand-gold text-brand-gold group-hover:text-brand-dark rounded-xl flex items-center justify-center text-2xl transition-all mb-6">
                                <i class="fa-solid fa-leaf"></i>
                            </div>
                            <h3 class="text-xl font-bold text-brand-dark mb-3">Développement durable</h3>
                            <p class="text-slate-600">Conception d'infrastructures à fort impact environnemental positif
                                (énergies renouvelables, captage d'eau).</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Featured Projects (Projets Phares) -->
            <section class="py-20 bg-slate-50 border-t border-b border-slate-100">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col md:flex-row md:items-end justify-between mb-12">
                        <div>
                            <h2 class="text-3xl font-extrabold text-brand-dark mb-3">Projets phares</h2>
                            <p class="text-slate-600 max-w-2xl">Découvrez nos réalisations majeures en cours d'exécution
                                ou livrées avec succès sur le continent.</p>
                        </div>
                        <button onclick="goToMarketplace()"
                            class="mt-4 md:mt-0 text-brand-gold font-bold hover:text-yellow-600 flex items-center space-x-2 transition-all">
                            <span>Voir tous les projets</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>

                    <!-- Projects Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <!-- Project 1 -->
                        <div
                            class="bg-white rounded-2xl overflow-hidden shadow-md border border-slate-100 hover:shadow-xl transition-all group">
                            <div class="relative h-60 overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=600&q=80"
                                    alt="Résidence Kivu Green"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-all duration-500">
                                <span
                                    class="absolute top-4 right-4 bg-brand-success/90 backdrop-blur-md text-white text-xs font-semibold px-3 py-1 rounded-full flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>En cours
                                </span>
                            </div>
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-brand-dark mb-2">Résidence Kivu Green</h3>
                                <div class="flex items-center space-x-2 text-slate-500 text-sm mb-4">
                                    <i class="fa-solid fa-location-dot text-brand-gold"></i>
                                    <span>Goma, RDC</span>
                                </div>
                                <div class="border-t border-slate-100 pt-4 flex justify-between items-center text-sm">
                                    <span class="text-slate-600 font-medium">40 logements</span>
                                    <div>
                                        <p class="text-slate-400 text-xs text-right">Budget</p>
                                        <p class="text-brand-dark font-extrabold text-base">1 200 000 $</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Project 2 -->
                        <div
                            class="bg-white rounded-2xl overflow-hidden shadow-md border border-slate-100 hover:shadow-xl transition-all group">
                            <div class="relative h-60 overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1554469384-e58fac16e23a?auto=format&fit=crop&w=600&q=80"
                                    alt="Urban Market Center"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-all duration-500">
                                <span
                                    class="absolute top-4 right-4 bg-blue-600/90 text-white text-xs font-semibold px-3 py-1 rounded-full">
                                    Finalisé
                                </span>
                            </div>
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-brand-dark mb-2">Urban Market Center</h3>
                                <div class="flex items-center space-x-2 text-slate-500 text-sm mb-4">
                                    <i class="fa-solid fa-location-dot text-brand-gold"></i>
                                    <span>Butembo, RDC</span>
                                </div>
                                <div class="border-t border-slate-100 pt-4 flex justify-between items-center text-sm">
                                    <span class="text-slate-600 font-medium">Centre commercial</span>
                                    <div>
                                        <p class="text-slate-400 text-xs text-right">Budget</p>
                                        <p class="text-brand-dark font-extrabold text-base">2 500 000 $</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Project 3 -->
                        <div
                            class="bg-white rounded-2xl overflow-hidden shadow-md border border-slate-100 hover:shadow-xl transition-all group">
                            <div class="relative h-60 overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=600&q=80"
                                    alt="Business Park Kinshasa"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-all duration-500">
                                <span
                                    class="absolute top-4 right-4 bg-brand-success/90 backdrop-blur-md text-white text-xs font-semibold px-3 py-1 rounded-full flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>En cours
                                </span>
                            </div>
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-brand-dark mb-2">Business Park Kinshasa</h3>
                                <div class="flex items-center space-x-2 text-slate-500 text-sm mb-4">
                                    <i class="fa-solid fa-location-dot text-brand-gold"></i>
                                    <span>Kinshasa, RDC</span>
                                </div>
                                <div class="border-t border-slate-100 pt-4 flex justify-between items-center text-sm">
                                    <span class="text-slate-600 font-medium">Bureaux & Commerces</span>
                                    <div>
                                        <p class="text-slate-400 text-xs text-right">Budget</p>
                                        <p class="text-brand-dark font-extrabold text-base">5 800 000 $</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Dual CTA Banners -->
            <section class="py-16 bg-brand-dark text-white relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-brand-navy to-brand-dark"></div>
                <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Left Block (Submit project) -->
                        <div
                            class="bg-slate-900/50 border border-slate-700/60 p-8 rounded-2xl shadow-xl flex flex-col justify-between">
                            <div>
                                <span
                                    class="text-brand-gold uppercase tracking-wider font-semibold text-xs block mb-2">Secteur
                                    Immobilier</span>
                                <h3 class="text-2xl font-bold mb-4">Vous avez un projet immobilier ?</h3>
                                <p class="text-slate-300 text-sm md:text-base leading-relaxed mb-6">
                                    Soumettez votre dossier technique, profitez d'une validation d'experts et accédez à 
                                    notre réseau international d'investisseurs certifiés.
                                </p>
                            </div>
                            <button onclick="goToPromoterDashboard()"
                                class="bg-brand-gold hover:bg-yellow-500 text-brand-dark font-bold py-3.5 px-6 rounded-xl transition-all self-start flex items-center space-x-2 shadow-lg shadow-brand-gold/15">
                                <span>Soumettre un projet</span>
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>

                        <!-- Right Block (Invest) -->
                        <div
                            class="bg-slate-900/50 border border-slate-700/60 p-8 rounded-2xl shadow-xl flex flex-col justify-between">
                            <div>
                                <span
                                    class="text-brand-gold uppercase tracking-wider font-semibold text-xs block mb-2">Finance
                                    & Climat</span>
                                <h3 class="text-2xl font-bold mb-4">Vous êtes investisseur ?</h3>
                                <p class="text-slate-300 text-sm md:text-base leading-relaxed mb-6">
                                    Rejoignez notre écosystème d'investisseurs, explorez des opportunités qualifiées et
                                    suivez vos rendements via notre tableau de bord de pointe.
                                </p>
                            </div>
                            <button onclick="switchTab('investisseur')"
                                class="bg-transparent border border-brand-gold hover:bg-brand-gold hover:text-brand-dark text-brand-gold font-bold py-3.5 px-6 rounded-xl transition-all self-start flex items-center space-x-2">
                                <span>Devenir investisseur</span>
                                <i class="fa-solid fa-user-tie"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </section>
        </div>


        <!-- ================= PAGE 2: À PROPOS ================= -->
        <div id="page-apropos" class="tab-content hidden">
            <!-- Header Section -->
            <section class="bg-brand-dark text-white py-12 border-b border-slate-800">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-xs text-slate-400 space-x-2 mb-3">
                        <span class="hover:text-white cursor-pointer" onclick="switchTab('accueil')">Accueil</span>
                        <span>/</span>
                        <span class="text-brand-gold font-medium">À propos</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-extrabold">À propos de nous</h1>
                </div>
            </section>

            <!-- Mission, Vision & Values Section -->
            <section class="py-16 bg-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                        <div>
                            <!-- Mission -->
                            <div class="mb-10">
                                <div class="flex items-center space-x-3 mb-4">
                                    <span class="w-8 h-1 bg-brand-gold rounded-full"></span>
                                    <h2 class="text-2xl font-extrabold text-brand-dark uppercase tracking-wide">Mission
                                    </h2>
                                </div>
                                <p class="text-slate-600 text-lg leading-relaxed">
                                    Concevoir, développer et opérer des solutions urbaines durables à fort impact pour
                                    restructurer durablement l'environnement urbain et mobiliser des capitaux qualifiés.
                                </p>
                            </div>

                            <!-- Vision -->
                            <div class="mb-10">
                                <div class="flex items-center space-x-3 mb-4">
                                    <span class="w-8 h-1 bg-brand-gold rounded-full"></span>
                                    <h2 class="text-2xl font-extrabold text-brand-dark uppercase tracking-wide">Vision
                                    </h2>
                                </div>
                                <p class="text-slate-600 text-lg leading-relaxed">
                                    Devenir la référence africaine incontournable en matière de développement urbain
                                    intégré et de facilitation financière de projets durables en Afrique Centrale.
                                </p>
                            </div>
                        </div>

                        <!-- Right Illustration Card -->
                        <div
                            class="relative bg-brand-dark rounded-3xl overflow-hidden p-8 h-[380px] shadow-2xl flex items-end">
                            <div class="absolute inset-0 bg-cover bg-center mix-blend-overlay opacity-40"
                                style="background-image: url('https://images.unsplash.com/photo-1448697138198-9fa6d0db244d?auto=format&fit=crop&w=1000&q=80')">
                            </div>
                            <div class="relative z-10 text-white">
                                <h3 class="text-2xl font-extrabold mb-2">Soutenir la croissance régionale</h3>
                                <p class="text-slate-300 text-sm">Une gouvernance forte au service de l'émergence des
                                    villes de demain en RDC.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Values Grid -->
                    <div class="mt-20">
                        <h2 class="text-2xl font-extrabold text-brand-dark text-center mb-12">Nos valeurs</h2>
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
                            <!-- Value 1 -->
                            <div
                                class="bg-slate-50 border border-slate-100 p-6 rounded-2xl text-center hover:shadow-lg transition-all">
                                <div class="text-brand-gold text-3xl mb-4"><i class="fa-solid fa-star"></i></div>
                                <h4 class="font-bold text-brand-dark mb-1">Excellence</h4>
                                <p class="text-xs text-slate-500">Rigueur dans nos processus.</p>
                            </div>
                            <!-- Value 2 -->
                            <div
                                class="bg-slate-50 border border-slate-100 p-6 rounded-2xl text-center hover:shadow-lg transition-all">
                                <div class="text-brand-gold text-3xl mb-4"><i class="fa-solid fa-shield-halved"></i>
                                </div>
                                <h4 class="font-bold text-brand-dark mb-1">Intégrité</h4>
                                <p class="text-xs text-slate-500">Transparence et confiance.</p>
                            </div>
                            <!-- Value 3 -->
                            <div
                                class="bg-slate-50 border border-slate-100 p-6 rounded-2xl text-center hover:shadow-lg transition-all">
                                <div class="text-brand-gold text-3xl mb-4"><i class="fa-solid fa-lightbulb"></i>
                            </div>
                                <h4 class="font-bold text-brand-dark mb-1">Innovation</h4>
                                <p class="text-xs text-slate-500">Technologies d'avenir.</p>
                            </div>
                            <!-- Value 4 -->
                            <div
                                class="bg-slate-50 border border-slate-100 p-6 rounded-2xl text-center hover:shadow-lg transition-all">
                                <div class="text-brand-gold text-3xl mb-4"><i class="fa-solid fa-seedling"></i>
                            </div>
                                <h4 class="font-bold text-brand-dark mb-1">Durabilité</h4>
                                <p class="text-xs text-slate-500">Vision à long terme.</p>
                            </div>
                            <!-- Value 5 -->
                            <div
                                class="bg-slate-50 border border-slate-100 p-6 rounded-2xl text-center hover:shadow-lg transition-all">
                                <div class="text-brand-gold text-3xl mb-4"><i class="fa-solid fa-chart-line"></i>
                            </div>
                                <h4 class="font-bold text-brand-dark mb-1">Impact</h4>
                                <p class="text-xs text-slate-500">Transformation durable.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Notre Gouvernance & QSE -->
            <section class="py-16 bg-slate-50 border-t border-slate-100">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                        <div>
                            <h2 class="text-3xl font-extrabold text-brand-dark mb-6">Notre gouvernance</h2>
                            <p class="text-slate-600 mb-6 leading-relaxed">
                                URBANOVA SOLUTIONS s'appuie sur un organigramme clair, un comité de direction diversifié
                                et une équipe dirigeante chevronnée pour garantir la transparence opérationnelle et le
                                respect des engagements des investisseurs.
                            </p>
                            <button
                                class="bg-brand-navy text-white hover:bg-slate-800 font-bold px-6 py-3 rounded-xl text-sm transition-all shadow-md">En
                                savoir plus</button>
                        </div>

                        <!-- Policy Card QSE -->
                        <div class="bg-white p-8 rounded-2xl shadow-md border border-slate-100">
                            <h3 class="text-xl font-bold text-brand-dark mb-6 flex items-center space-x-2">
                                <i class="fa-solid fa-award text-brand-gold"></i>
                                <span>Politique QSE RDC</span>
                            </h3>
                            <p class="text-sm text-slate-600 mb-8">
                                Nous respectons rigoureusement les normes internationales de Qualité, Sécurité, et
                                d'Environnement pour tous les chantiers conduits en République Démocratique du Congo.
                            </p>
                            <div class="grid grid-cols-3 gap-4">
                                <div
                                    class="bg-emerald-50 border border-emerald-100 text-emerald-800 text-center py-4 rounded-xl font-bold text-xs shadow-sm">
                                    <span class="block text-emerald-500 text-lg mb-1"><i
                                            class="fa-solid fa-circle-check"></i></span>
                                    ISO 9001
                                </div>
                                <div
                                    class="bg-emerald-50 border border-emerald-100 text-emerald-800 text-center py-4 rounded-xl font-bold text-xs shadow-sm">
                                    <span class="block text-emerald-500 text-lg mb-1"><i
                                            class="fa-solid fa-circle-check"></i></span>
                                    ISO 14001
                                </div>
                                <div
                                    class="bg-emerald-50 border border-emerald-100 text-emerald-800 text-center py-4 rounded-xl font-bold text-xs shadow-sm">
                                    <span class="block text-emerald-500 text-lg mb-1"><i
                                            class="fa-solid fa-circle-check"></i></span>
                                    ISO 45001
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>


        <!-- ================= PAGE 3: MARKETPLACE ================= -->
        <div id="page-marketplace" class="tab-content hidden">
            <!-- Header Section -->
            <section class="bg-brand-dark text-white py-12 border-b border-slate-800">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-xs text-slate-400 space-x-2 mb-3">
                        <span class="hover:text-white cursor-pointer" onclick="switchTab('accueil')">Accueil</span>
                        <span>/</span>
                        <span class="text-brand-gold font-medium">Marketplace</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-extrabold">Marketplace des opportunités</h1>
                    <p class="text-slate-400 mt-2">Projets immobiliers validés et sécurisés par Urbanova</p>
                </div>
            </section>

            <!-- Main Content Area with Filters & Grid -->
            <section class="py-12 bg-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                        <!-- Left Sidebar: Filters -->
                        <div class="bg-slate-50 border border-slate-200/60 p-6 rounded-2xl h-fit">
                            <h3 class="font-bold text-brand-dark text-lg mb-6 flex items-center justify-between">
                                <span>Filtres</span>
                                <i class="fa-solid fa-sliders text-slate-400"></i>
                            </h3>

                            <form id="filterForm" class="space-y-6"
                                onsubmit="event.preventDefault(); applyMarketplaceFilters();">
                                <!-- Country filter -->
                                <div>
                                    <label
                                        class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Pays</label>
                                    <select id="filterCountry"
                                        class="w-full bg-white border border-slate-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-brand-gold focus:outline-none">
                                        <option value="">Tous les pays</option>
                                        <optgroup label="Afrique Centrale">
                                            <option value="RDC">Congo (RDC)</option>
                                            <option value="Rwanda">Rwanda</option>
                                            <option value="Burundi">Burundi</option>
                                            <option value="Ouganda">Ouganda</option>
                                            <option value="Angola">Angola</option>
                                            <option value="Congo">Congo (Brazzaville)</option>
                                            <option value="Cameroun">Cameroun</option>
                                            <option value="Gabon">Gabon</option>
                                            <option value="Centrafrique">République Centrafricaine</option>
                                            <option value="Tchad">Tchad</option>
                                        </optgroup>
                                        <optgroup label="Afrique de l'Est">
                                            <option value="Kenya">Kenya</option>
                                            <option value="Tanzanie">Tanzanie</option>
                                            <option value="Éthiopie">Éthiopie</option>
                                            <option value="Soudan">Soudan</option>
                                            <option value="Somalie">Somalie</option>
                                            <option value="Djibouti">Djibouti</option>
                                            <option value="Érythrée">Érythrée</option>
                                        </optgroup>
                                        <optgroup label="Afrique de l'Ouest">
                                            <option value="Nigeria">Nigeria</option>
                                            <option value="Ghana">Ghana</option>
                                            <option value="Côte d'Ivoire">Côte d'Ivoire</option>
                                            <option value="Sénégal">Sénégal</option>
                                            <option value="Mali">Mali</option>
                                            <option value="Burkina Faso">Burkina Faso</option>
                                            <option value="Niger">Niger</option>
                                            <option value="Bénin">Bénin</option>
                                            <option value="Togo">Togo</option>
                                            <option value="Guinée">Guinée</option>
                                            <option value="Sierra Leone">Sierra Leone</option>
                                            <option value="Libéria">Libéria</option>
                                        </optgroup>
                                        <optgroup label="Afrique Australe">
                                            <option value="Afrique du Sud">Afrique du Sud</option>
                                            <option value="Namibie">Namibie</option>
                                            <option value="Botswana">Botswana</option>
                                            <option value="Zimbabwe">Zimbabwe</option>
                                            <option value="Zambie">Zambie</option>
                                            <option value="Mozambique">Mozambique</option>
                                            <option value="Malawi">Malawi</option>
                                            <option value="Angola">Angola</option>
                                            <option value="Madagascar">Madagascar</option>
                                            <option value="Maurice">Maurice</option>
                                        </optgroup>
                                        <optgroup label="Afrique du Nord">
                                            <option value="Maroc">Maroc</option>
                                            <option value="Algérie">Algérie</option>
                                            <option value="Tunisie">Tunisie</option>
                                            <option value="Libye">Libye</option>
                                            <option value="Égypte">Égypte</option>
                                        </optgroup>
                                        <optgroup label="Autres pays">
                                            <option value="France">France</option>
                                            <option value="Belgique">Belgique</option>
                                            <option value="Suisse">Suisse</option>
                                            <option value="Allemagne">Allemagne</option>
                                            <option value="Royaume-Uni">Royaume-Uni</option>
                                            <option value="États-Unis">États-Unis</option>
                                            <option value="Canada">Canada</option>
                                            <option value="Chine">Chine</option>
                                            <option value="Inde">Inde</option>
                                            <option value="Brésil">Brésil</option>
                                            <option value="Émirats Arabes Unis">Émirats Arabes Unis</option>
                                            <option value="Arabie Saoudite">Arabie Saoudite</option>
                                        </optgroup>
                                    </select>
                                </div>

                                <!-- City Filter -->
                                <div>
                                    <label
                                        class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Ville</label>
                                    <input type="text" id="filterCity"
                                        class="w-full bg-white border border-slate-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-brand-gold focus:outline-none"
                                        placeholder="Ex: Kinshasa">
                                </div>

                                <!-- Project Type Filter -->
                                <div>
                                    <label
                                        class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Type de projet</label>
                                    <select id="filterProjectType"
                                        class="w-full bg-white border border-slate-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-brand-gold focus:outline-none">
                                        <option value="">Tous les types</option>
                                        <option value="residential">Résidentiel</option>
                                        <option value="commercial">Commercial</option>
                                        <option value="mixed">Mixte</option>
                                        <option value="industrial">Industriel</option>
                                        <option value="hotel">Hôtel</option>
                                        <option value="office">Bureau</option>
                                        <option value="subdivision">Lotissement</option>
                                    </select>
                                </div>

                                <!-- Operation Type Filter -->
                                <div>
                                    <label
                                        class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Type d'opération</label>
                                    <select id="filterOperationType"
                                        class="w-full bg-white border border-slate-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-brand-gold focus:outline-none">
                                        <option value="">Toutes les opérations</option>
                                        <option value="sale">Vente</option>
                                        <option value="rental">Location</option>
                                        <option value="fundraising">Levée de fonds</option>
                                        <option value="sale_fundraising">Vente + Levée de fonds</option>
                                    </select>
                                </div>

                                <!-- Sector Filter -->
                                <div>
                                    <label
                                        class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Secteur</label>
                                    <input type="text" id="filterSector"
                                        class="w-full bg-white border border-slate-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-brand-gold focus:outline-none"
                                        placeholder="Ex: Immobilier">
                                </div>

                                <!-- ROI expected filter slider -->
                                <div>
                                    <div class="flex justify-between items-center mb-2">
                                        <label
                                            class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Rendement
                                            attendu (ROI)</label>
                                        <span id="roiVal" class="text-xs font-bold text-brand-gold">0% +</span>
                                    </div>
                                    <input type="range" id="filterROI" min="0" max="30" value="0"
                                        class="w-full accent-brand-gold"
                                        oninput="document.getElementById('roiVal').innerText = this.value + '% +'">
                                </div>

                                <!-- Action Buttons -->
                                <div class="pt-4 space-y-2">
                                    <button type="submit"
                                        class="w-full bg-brand-navy hover:bg-slate-800 text-white font-bold py-3 rounded-xl text-sm transition-all shadow-md">Appliquer
                                        les filtres</button>
                                    <button type="button" onclick="resetMarketplaceFilters()"
                                        class="w-full bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold py-2.5 rounded-xl text-sm transition-all">Réinitialiser</button>
                                </div>
                            </form>
                        </div>

                        <!-- Right Area: Projects Grid -->
                        <div class="lg:col-span-3">
                            <div class="flex justify-between items-center mb-6">
                                <span class="text-slate-600 font-medium" id="resultsCount">Chargement...</span>
                                <select id="sortOrder"
                                    class="bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-xs font-medium focus:ring-2 focus:ring-brand-gold focus:outline-none">
                                    <option value="recent">Plus récentes</option>
                                    <option value="roi_high">Rentabilité (Élevé)</option>
                                    <option value="funding_low">Financement recherché (Bas)</option>
                                </select>
                            </div>

                            <!-- Dynamic Cards Container -->
                            <div id="marketplaceGrid" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Cards will be loaded dynamically by script -->
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>


        <!-- ================= PAGE 4: LEVÉE DE FONDS (SOUMISSION) ================= -->
        <div id="page-levee" class="tab-content hidden">
            <!-- Header Section -->
            <section class="bg-brand-dark text-white py-12 border-b border-slate-800">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-xs text-slate-400 space-x-2 mb-3">
                        <span class="hover:text-white cursor-pointer" onclick="switchTab('accueil')">Accueil</span>
                        <span>/</span>
                        <span class="text-brand-gold font-medium">Levée de fonds</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-extrabold">Soumettre un projet</h1>
                </div>
            </section>

            <!-- Multi-step Form Wizard Container -->
            <section class="py-16 bg-slate-50">
                <div class="max-w-4xl mx-auto px-4 sm:px-6">
                    <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                        <!-- Steps Indicator -->
                        <div
                            class="bg-brand-navy text-white px-6 py-6 md:px-12 flex justify-between items-center border-b border-slate-800">
                            <!-- Step 1 -->
                            <div class="flex items-center space-x-3 step-indicator" data-step="1">
                                <div class="w-8 h-8 rounded-full bg-brand-gold text-brand-dark flex items-center justify-center font-bold text-sm"
                                    id="stepIndicator-1">1</div>
                                <span class="hidden md:inline text-xs font-bold uppercase tracking-wider text-slate-300"
                                    id="stepText-1">Informations générales</span>
                            </div>
                            <div class="h-[2px] flex-grow bg-slate-700 mx-4 hidden md:block"></div>
                            <!-- Step 2 -->
                            <div class="flex items-center space-x-3 step-indicator" data-step="2">
                                <div class="w-8 h-8 rounded-full bg-slate-800 text-slate-400 border border-slate-700 flex items-center justify-center font-bold text-sm"
                                    id="stepIndicator-2">2</div>
                                <span class="hidden md:inline text-xs font-bold uppercase tracking-wider text-slate-400"
                                    id="stepText-2">Données financières</span>
                            </div>
                            <div class="h-[2px] flex-grow bg-slate-700 mx-4 hidden md:block"></div>
                            <!-- Step 3 -->
                            <div class="flex items-center space-x-3 step-indicator" data-step="3">
                                <div class="w-8 h-8 rounded-full bg-slate-800 text-slate-400 border border-slate-700 flex items-center justify-center font-bold text-sm"
                                    id="stepIndicator-3">3</div>
                                <span class="hidden md:inline text-xs font-bold uppercase tracking-wider text-slate-400"
                                    id="stepText-3">Documents requis</span>
                            </div>
                            <div class="h-[2px] flex-grow bg-slate-700 mx-4 hidden md:block"></div>
                            <!-- Step 4 -->
                            <div class="flex items-center space-x-3 step-indicator" data-step="4">
                                <div class="w-8 h-8 rounded-full bg-slate-800 text-slate-400 border border-slate-700 flex items-center justify-center font-bold text-sm"
                                    id="stepIndicator-4">4</div>
                                <span class="hidden md:inline text-xs font-bold uppercase tracking-wider text-slate-400"
                                    id="stepText-4">Vérification</span>
                            </div>
                        </div>

                        <!-- Step content forms -->
                        <div class="p-8 md:p-12">
                            <form id="submissionWizard" onsubmit="event.preventDefault();">

                                <!-- STEP 1 -->
                                <div id="formStep-1" class="space-y-6">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-bold text-brand-dark mb-2">Nom du projet
                                                *</label>
                                            <input type="text" id="projName" placeholder="Ex: Résidence Horizon"
                                                class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-brand-gold focus:outline-none"
                                                required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-bold text-brand-dark mb-2">Promoteur /
                                                Société *</label>
                                            <input type="text" id="projOwner" placeholder="Ex: SARL Horizon"
                                                class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-brand-gold focus:outline-none"
                                                required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-bold text-brand-dark mb-2">Localisation
                                                *</label>
                                            <input type="text" id="projLoc" placeholder="Ex: Goma, RDC"
                                                class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-brand-gold focus:outline-none"
                                                required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-bold text-brand-dark mb-2">Secteur
                                                *</label>
                                            <select id="projSector"
                                                class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-brand-gold focus:outline-none"
                                                required>
                                                <option value="">Sélectionnez un secteur</option>
                                                <option value="Résidentiel">Résidentiel</option>
                                                <option value="Commercial">Commercial</option>
                                                <option value="Bureaux">Bureaux / Tertiaire</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-brand-dark mb-2">Description du
                                            projet *</label>
                                        <textarea id="projDesc" rows="4"
                                            placeholder="Décrivez votre projet immobilier en détail..."
                                            class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-brand-gold focus:outline-none"
                                            required></textarea>
                                    </div>
                                </div>

                                <!-- STEP 2 -->
                                <div id="formStep-2" class="space-y-6 hidden">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-bold text-brand-dark mb-2">Coût total du
                                                projet ($) *</label>
                                            <input type="number" id="projCost" placeholder="Ex: 800000"
                                                class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-brand-gold focus:outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-bold text-brand-dark mb-2">Financement
                                                recherché ($) *</label>
                                            <input type="number" id="projTarget" placeholder="Ex: 500000"
                                                class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-brand-gold focus:outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-bold text-brand-dark mb-2">Apport personnel
                                                ($)</label>
                                            <input type="number" id="projOwnEquity" placeholder="Ex: 300000"
                                                class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-brand-gold focus:outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-bold text-brand-dark mb-2">ROI attendu
                                                (%)</label>
                                            <input type="number" id="projRoi" placeholder="Ex: 24"
                                                class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-brand-gold focus:outline-none">
                                        </div>
                                    </div>
                                </div>

                                <!-- STEP 3 -->
                                <div id="formStep-3" class="space-y-6 hidden">
                                    <p class="text-sm text-slate-500 mb-4">Veuillez joindre les documents de référence
                                        pour maximiser la confiance des investisseurs qualifiés.</p>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                        <div id="businessPlanDropzone"
                                            class="border-2 border-dashed border-slate-300 hover:border-brand-gold rounded-2xl p-6 text-center cursor-pointer transition-all bg-slate-50">
                                            <i class="fa-solid fa-file-pdf text-3xl text-slate-400 mb-3"></i>
                                            <p class="text-sm font-bold text-brand-dark">Business Plan</p>
                                            <p class="text-xs text-slate-400 mt-1">Glissez un fichier ou cliquez pour
                                                uploader</p>
                                            <p id="businessPlanFileName" class="text-xs text-slate-500 mt-2">Aucun fichier sélectionné</p>
                                        </div>
                                        <div id="pitchDeckDropzone"
                                            class="border-2 border-dashed border-slate-300 hover:border-brand-gold rounded-2xl p-6 text-center cursor-pointer transition-all bg-slate-50">
                                            <i class="fa-solid fa-file-lines text-3xl text-slate-400 mb-3"></i>
                                            <p class="text-sm font-bold text-brand-dark">Pitch Deck</p>
                                            <p class="text-xs text-slate-400 mt-1">Glissez un fichier ou cliquez pour
                                                uploader</p>
                                            <p id="pitchDeckFileName" class="text-xs text-slate-500 mt-2">Aucun fichier sélectionné</p>
                                        </div>
                                        <div id="financialModelDropzone"
                                            class="border-2 border-dashed border-slate-300 hover:border-brand-gold rounded-2xl p-6 text-center cursor-pointer transition-all bg-slate-50">
                                            <i class="fa-solid fa-file-excel text-3xl text-slate-400 mb-3"></i>
                                            <p class="text-sm font-bold text-brand-dark">Modèle Financier (Excel)</p>
                                            <p class="text-xs text-slate-400 mt-1">Données prévisionnelles d'activité
                                            </p>
                                            <p id="financialModelFileName" class="text-xs text-slate-500 mt-2">Aucun fichier sélectionné</p>
                                        </div>
                                    </div>

                                    <input type="file" id="businessPlanInput" name="business_plan" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.csv,image/*" style="display:none;">
                                    <input type="file" id="pitchDeckInput" name="pitch_deck" accept=".pdf,.doc,.docx,.ppt,.pptx,image/*" style="display:none;">
                                    <input type="file" id="financialModelInput" name="financial_model" accept=".pdf,.xls,.xlsx,.csv,image/*" style="display:none;">
                                </div>

                                <!-- STEP 4 -->
                                <div id="formStep-4" class="space-y-6 hidden">
                                    <div class="bg-emerald-50 border border-emerald-100 p-6 rounded-2xl text-center">
                                        <i class="fa-solid fa-circle-check text-4xl text-brand-success mb-3"></i>
                                        <h3 class="text-lg font-bold text-emerald-900">Dossier prêt à la vérification
                                        </h3>
                                        <p class="text-sm text-emerald-700 mt-1">Votre projet va être analysé par
                                            l'équipe administrative de la plateforme URBANOVA solutions sous 48 heures
                                            ouvrables.</p>
                                    </div>
                                </div>

                                <!-- Footer / Navigation Buttons of the Wizard -->
                                <div class="mt-12 flex justify-between items-center border-t border-slate-100 pt-6">
                                    <button type="button" id="prevStepBtn" onclick="navigateWizard(-1)"
                                        class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold px-6 py-3 rounded-xl text-sm transition-all invisible">Précédent</button>
                                    <button type="button" id="nextStepBtn" onclick="navigateWizard(1)"
                                        class="bg-brand-navy hover:bg-slate-800 text-white font-bold px-8 py-3.5 rounded-xl text-sm transition-all shadow-md">Suivant</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div>


        <!-- ================= PAGE 5: ESPACE INVESTISSEUR (PORTAIL) ================= -->
        <div id="page-investisseur" class="tab-content hidden bg-slate-100">
            <!-- Header Section -->
            <section class="bg-brand-dark text-white py-12 border-b border-slate-800">
                <div
                    class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-start md:items-center">
                    <div>
                        <div class="text-xs text-slate-400 space-x-2 mb-3">
                            <span class="hover:text-white cursor-pointer" onclick="switchTab('accueil')">Accueil</span>
                            <span>/</span>
                            <span class="text-brand-gold font-medium">Espace Investisseur</span>
                        </div>
                        <h1 class="text-3xl md:text-4xl font-extrabold">Espace Investisseur Certifié</h1>
                    </div>
                    <div
                        class="mt-4 md:mt-0 bg-slate-800 border border-slate-700 px-4 py-2 rounded-xl flex items-center space-x-3">
                        <div
                            class="w-10 h-10 rounded-full bg-brand-gold text-brand-dark flex items-center justify-center font-bold text-sm">
                            JD
                        </div>
                        <div>
                            <span class="block text-sm font-bold text-white">Jean Dupont</span>
                            <span class="text-xs text-brand-success flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-brand-success"></span> Investisseur Certifié
                            </span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main Investor Dashboard Structure -->
            <section class="py-12">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

                        <!-- Left Panel Menu -->
                        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm h-fit">
                            <div class="bg-brand-navy p-4 text-white text-xs font-semibold tracking-wider uppercase">
                                Menu de pilotage</div>
                            <nav class="p-2 space-y-1">
                                <button onclick="setInvestorTab('dashboard')" id="invest-tab-dashboard"
                                    class="w-full text-left px-4 py-3 rounded-xl text-sm font-bold flex items-center space-x-3 bg-brand-gold/15 text-brand-dark">
                                    <i class="fa-solid fa-chart-pie"></i>
                                    <span>Tableau de bord</span>
                                </button>
                                <button onclick="setInvestorTab('investissements')" id="invest-tab-investissements"
                                    class="w-full text-left px-4 py-3 rounded-xl text-sm font-medium flex items-center space-x-3 text-slate-600 hover:bg-slate-50">
                                    <i class="fa-solid fa-wallet"></i>
                                    <span>Mes investissements</span>
                                </button>
                                <button onclick="setInvestorTab('dataroom')" id="invest-tab-dataroom"
                                    class="w-full text-left px-4 py-3 rounded-xl text-sm font-medium flex items-center space-x-3 text-slate-600 hover:bg-slate-50 justify-between">
                                    <span class="flex items-center space-x-3">
                                        <i class="fa-solid fa-lock text-brand-gold"></i>
                                        <span>Data Room Sécurisée</span>
                                    </span>
                                    <span
                                        class="bg-brand-gold/20 text-brand-gold text-[10px] px-2 py-0.5 rounded-full font-bold">PRO</span>
                                </button>
                                <button onclick="goToMarketplace()"
                                    class="w-full text-left px-4 py-3 rounded-xl text-sm font-medium flex items-center space-x-3 text-slate-600 hover:bg-slate-50">
                                    <i class="fa-solid fa-magnifying-glass-chart"></i>
                                    <span>Opportunités</span>
                                </button>
                                <button onclick="handleInvestorSpaceAction()"
                                    class="w-full text-left px-4 py-3 rounded-xl text-sm font-medium flex items-center space-x-3 text-red-600 hover:bg-red-50 mt-8">
                                    <i class="fa-solid fa-arrow-right-from-bracket" id="investorLogoutIcon"></i>
                                    <span id="investorLogoutText">Déconnexion</span>
                                </button>
                            </nav>
                        </div>

                        <!-- Right Panel Contents -->
                        <div class="lg:col-span-3 space-y-8">

                            <!-- TAB SUB-CONTENT: DASHBOARD -->
                            <div id="invest-sub-dashboard" class="invest-sub-content space-y-8">
                                <!-- Top Performance Stats Card Grid -->
                                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                                        <span
                                            class="block text-slate-400 text-xs font-semibold uppercase tracking-wider mb-1">Projets
                                            investis</span>
                                        <span class="text-2xl md:text-3xl font-extrabold text-brand-dark" id="investProjectCount">0</span>
                                    </div>
                                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                                        <span
                                            class="block text-slate-400 text-xs font-semibold uppercase tracking-wider mb-1">Montant
                                            total investi</span>
                                        <span class="text-2xl md:text-3xl font-extrabold text-brand-dark" id="investTotalAmount">0 $</span>
                                    </div>
                                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                                        <span
                                            class="block text-slate-400 text-xs font-semibold uppercase tracking-wider mb-1">ROI
                                            moyen</span>
                                        <span
                                            class="text-2xl md:text-3xl font-extrabold text-brand-success" id="investAvgRoi">0%</span>
                                    </div>
                                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                                        <span
                                            class="block text-slate-400 text-xs font-semibold uppercase tracking-wider mb-1">Gains
                                            réalisés</span>
                                        <span class="text-2xl md:text-3xl font-extrabold text-brand-dark" id="investTotalGains">0 $</span>
                                    </div>
                                </div>

                                <!-- Recent Investments Table -->
                                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                                    <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                                        <h3 class="font-extrabold text-brand-dark text-lg">Mes investissements récents
                                        </h3>
                                        <button onclick="switchTab('marketplace')"
                                            class="text-brand-gold text-xs font-bold hover:underline">Investir à 
                                            nouveau</button>
                                    </div>
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-left text-sm">
                                            <thead class="bg-slate-50 text-slate-500 uppercase text-xs font-semibold">
                                                <tr>
                                                    <th class="p-4">Projet</th>
                                                    <th class="p-4">Montant investi</th>
                                                    <th class="p-4">Progression</th>
                                                    <th class="p-4">ROI attendu</th>
                                                    <th class="p-4">Statut</th>
                                                    <th class="p-4">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-100" id="investorInvestmentsTable">
                                                <tr>
                                                    <td colspan="6" class="p-4 text-center text-slate-500">Chargement des investissements...</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <!-- Quick Actions -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <button onclick="switchTab('marketplace')" class="bg-brand-navy hover:bg-slate-800 text-white p-6 rounded-2xl shadow-sm border border-slate-200 text-left transition-all">
                                        <i class="fa-solid fa-plus-circle text-brand-gold text-2xl mb-2"></i>
                                        <h4 class="font-bold text-lg">Nouvel investissement</h4>
                                        <p class="text-sm text-slate-300">Explorer les opportunités</p>
                                    </button>
                                    <button onclick="setInvestorTab('investissements')" class="bg-white hover:bg-slate-50 p-6 rounded-2xl shadow-sm border border-slate-200 text-left transition-all">
                                        <i class="fa-solid fa-chart-line text-brand-navy text-2xl mb-2"></i>
                                        <h4 class="font-bold text-lg text-brand-dark">Voir mes rapports</h4>
                                        <p class="text-sm text-slate-500">Analyse détaillée</p>
                                    </button>
                                    <button onclick="setInvestorTab('dataroom')" class="bg-white hover:bg-slate-50 p-6 rounded-2xl shadow-sm border border-slate-200 text-left transition-all">
                                        <i class="fa-solid fa-folder-open text-brand-gold text-2xl mb-2"></i>
                                        <h4 class="font-bold text-lg text-brand-dark">Data Room</h4>
                                        <p class="text-sm text-slate-500">Documents confidentiels</p>
                                    </button>
                                </div>
                            </div>

                            <!-- TAB SUB-CONTENT: INVESTISSEMENTS DETAIL -->
                            <div id="invest-sub-investissements" class="invest-sub-content hidden space-y-8">
                                <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
                                    <h3 class="font-extrabold text-brand-dark text-lg mb-4">Détails de mon portefeuille
                                    </h3>
                                    <p class="text-slate-600 mb-6">Suivez l'état technique et les rapports financiers
                                        trimestriels audités de l'ensemble de vos actifs immobiliers.</p>
                                    <div
                                        class="bg-slate-50 p-6 rounded-xl border border-slate-200 flex items-center justify-between">
                                        <div class="flex items-center space-x-4">
                                            <i class="fa-solid fa-file-invoice-dollar text-brand-gold text-4xl"></i>
                                            <div>
                                                <h4 class="font-bold text-brand-dark">Rapport d'activité Q2 2026</h4>
                                                <p class="text-xs text-slate-500">Mis à jour le 15 Juin 2026</p>
                                            </div>
                                        </div>
                                        <button
                                            class="bg-brand-navy text-white hover:bg-slate-800 px-4 py-2 rounded-lg text-xs font-bold flex items-center space-x-2">
                                            <i class="fa-solid fa-download"></i>
                                            <span>Télécharger</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB SUB-CONTENT: SECURED DATA ROOM -->
                            <div id="invest-sub-dataroom" class="invest-sub-content hidden space-y-8">
                                <!-- KYC Validation Screen simulator (Locks/Unlocks confidential data) -->
                                <div id="dataroom-locked-state"
                                    class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200 text-center space-y-6">
                                    <div
                                        class="w-16 h-16 bg-brand-gold/15 text-brand-gold rounded-full flex items-center justify-center text-3xl mx-auto">
                                        <i class="fa-solid fa-lock"></i>
                                    </div>
                                    <div class="max-w-md mx-auto">
                                        <h3 class="text-2xl font-extrabold text-brand-dark mb-2">Accéder à la Data Room
                                            Sécurisée</h3>
                                        <p class="text-sm text-slate-600 leading-relaxed">
                                            Conformément aux réglementations financières, l'accès aux audits de due
                                            diligence, aux business plans et aux plans cadastraux confidentiels est
                                            strictement protégé.
                                        </p>
                                    </div>

                                    <!-- Interactive Form to unlock -->
                                    <div
                                        class="border border-slate-200 bg-slate-50 p-6 rounded-2xl max-w-lg mx-auto text-left space-y-4">
                                        <h4 class="font-bold text-brand-dark text-sm border-b border-slate-200 pb-2">
                                            Compléter la vérification</h4>
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-500 mb-1">Type
                                                d'investisseur</label>
                                            <select
                                                class="w-full bg-white border border-slate-300 rounded-lg p-2 text-xs focus:ring-1 focus:ring-brand-gold focus:outline-none">
                                                <option>Personne Physique (Business Angel / Diaspora)</option>
                                                <option>Personne Morale (Fonds d'investissement / Family Office)
                                                </option>
                                            </select>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <input type="checkbox" id="ndaConsent" class="rounded accent-brand-gold">
                                            <label for="ndaConsent" class="text-xs text-slate-600">Je consens à la
                                                signature électronique d'un accord de non-divulgation (NDA)</label>
                                        </div>
                                        <button onclick="unlockDataRoom()"
                                            class="w-full bg-brand-navy hover:bg-slate-800 text-white font-bold py-3 rounded-xl text-xs transition-all flex items-center justify-center space-x-2">
                                            <i class="fa-solid fa-signature"></i>
                                            <span>Signer l'accord & Déverrouiller</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Unlocked State (Confidential file space) -->
                                <div id="dataroom-unlocked-state"
                                    class="hidden bg-white p-8 rounded-2xl shadow-sm border border-slate-200 space-y-6">
                                    <div class="flex justify-between items-center border-b border-slate-100 pb-4">
                                        <div>
                                            <span
                                                class="bg-brand-success/10 text-brand-success text-xs font-bold px-2.5 py-1 rounded-full">Accès
                                                Autorisé</span>
                                            <h3 class="text-2xl font-extrabold text-brand-dark mt-2">Dossiers d'audit
                                                confidentiels</h3>
                                        </div>
                                        <i class="fa-solid fa-folder-open text-brand-gold text-3xl"></i>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div
                                            class="p-4 border border-slate-200 rounded-xl hover:border-brand-gold transition-all flex justify-between items-center">
                                            <div class="flex items-center space-x-3">
                                                <i class="fa-solid fa-file-pdf text-red-500 text-2xl"></i>
                                                <div>
                                                    <span class="block text-sm font-bold text-brand-dark">Business Plan
                                                        Horizon</span>
                                                    <span class="text-xs text-slate-400">PDF • 14.2 MB</span>
                                                </div>
                                            </div>
                                            <button class="text-slate-400 hover:text-brand-gold"><i
                                                    class="fa-solid fa-download"></i></button>
                                        </div>
                                        <div
                                            class="p-4 border border-slate-200 rounded-xl hover:border-brand-gold transition-all flex justify-between items-center">
                                            <div class="flex items-center space-x-3">
                                                <i class="fa-solid fa-file-excel text-emerald-500 text-2xl"></i>
                                                <div>
                                                    <span class="block text-sm font-bold text-brand-dark">Modèle
                                                        prévisionnel 10 ans</span>
                                                    <span class="text-xs text-slate-400">XLSX • 4.8 MB</span>
                                                </div>
                                            </div>
                                            <button class="text-slate-400 hover:text-brand-gold"><i
                                                    class="fa-solid fa-download"></i></button>
                                        </div>
                                        <div
                                            class="p-4 border border-slate-200 rounded-xl hover:border-brand-gold transition-all flex justify-between items-center">
                                            <div class="flex items-center space-x-3">
                                                <i class="fa-solid fa-file-shield text-blue-500 text-2xl"></i>
                                                <div>
                                                    <span class="block text-sm font-bold text-brand-dark">Rapport de Due
                                                        Diligence</span>
                                                    <span class="text-xs text-slate-400">PDF • 8.1 MB</span>
                                                </div>
                                            </div>
                                            <button class="text-slate-400 hover:text-brand-gold"><i
                                                    class="fa-solid fa-download"></i></button>
                                        </div>
                                        <div
                                            class="p-4 border border-slate-200 rounded-xl hover:border-brand-gold transition-all flex justify-between items-center">
                                            <div class="flex items-center space-x-3">
                                                <i class="fa-solid fa-file-signature text-amber-500 text-2xl"></i>
                                                <div>
                                                    <span class="block text-sm font-bold text-brand-dark">Titres
                                                        fonciers & Permis</span>
                                                    <span class="text-xs text-slate-400">PDF • 18.5 MB</span>
                                                </div>
                                            </div>
                                            <button class="text-slate-400 hover:text-brand-gold"><i
                                                    class="fa-solid fa-download"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>


        <!-- ================= PAGE 6: ESPACE ADMIN ================= -->
        <div id="page-admin" class="tab-content hidden bg-slate-100">
            <!-- Header Section -->
            <section class="bg-brand-dark text-white py-12 border-b border-slate-800">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-xs text-slate-400 space-x-2 mb-3">
                        <span class="hover:text-white cursor-pointer" onclick="switchTab('accueil')">Accueil</span>
                        <span>/</span>
                        <span class="text-brand-gold font-medium">Admin</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-extrabold">Espace Administration</h1>
                    <p class="text-slate-400 mt-2">Gérer les projets, utilisateurs et approbations</p>
                </div>
            </section>

            <!-- Admin Dashboard -->
            <section class="py-8">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <!-- Admin Login if not authenticated -->
                    <div id="adminLogin" class="max-w-md mx-auto">
                        <div class="bg-white rounded-2xl shadow-lg p-8">
                            <div class="text-center mb-6">
                                <div class="w-16 h-16 bg-brand-navy rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fa-solid fa-shield-halved text-2xl text-white"></i>
                                </div>
                                <h2 class="text-2xl font-extrabold text-brand-dark">Accès Admin</h2>
                                <p class="text-slate-600 text-sm mt-1">Entrez le mot de passe administrateur</p>
                            </div>
                            
                            <form onsubmit="event.preventDefault(); handleAdminLogin();">
                                <div class="mb-4">
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Mot de passe</label>
                                    <input type="password" id="adminCode" placeholder="Mot de passe"
                                        class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-brand-gold focus:outline-none"
                                        required>
                                </div>
                                <button type="submit"
                                    class="w-full bg-brand-navy hover:bg-slate-800 text-white font-bold py-3 rounded-xl text-sm transition-all shadow-md">
                                    Accéder à l'administration
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Admin Dashboard (hidden by default) -->
                    <div id="adminDashboard" class="hidden">
                        <!-- Stats Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-slate-500 text-sm font-medium">Projets en attente</p>
                                        <p class="text-3xl font-extrabold text-brand-navy mt-1" id="adminPendingProjects">0</p>
                                    </div>
                                    <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                                        <i class="fa-solid fa-clock text-yellow-600"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-slate-500 text-sm font-medium">Projets approuvés</p>
                                        <p class="text-3xl font-extrabold text-brand-success mt-1" id="adminApprovedProjects">0</p>
                                    </div>
                                    <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                                        <i class="fa-solid fa-check-circle text-emerald-600"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-slate-500 text-sm font-medium">Total utilisateurs</p>
                                        <p class="text-3xl font-extrabold text-brand-gold mt-1" id="adminTotalUsers">0</p>
                                    </div>
                                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                                        <i class="fa-solid fa-users text-amber-600"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-slate-500 text-sm font-medium">Investissements</p>
                                        <p class="text-3xl font-extrabold text-brand-navy mt-1" id="adminTotalInvestments">0</p>
                                    </div>
                                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                        <i class="fa-solid fa-hand-holding-dollar text-blue-600"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabs for admin management -->
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-100">
                            <div class="border-b border-slate-100">
                                <div class="flex">
                                    <button onclick="switchAdminTab('projects')" class="admin-tab px-6 py-4 text-sm font-bold text-brand-navy border-b-2 border-brand-navy">Projets</button>
                                    <button onclick="switchAdminTab('users')" class="admin-tab px-6 py-4 text-sm font-medium text-slate-500 hover:text-slate-700">Utilisateurs</button>
                                    <button onclick="switchAdminTab('investments')" class="admin-tab px-6 py-4 text-sm font-medium text-slate-500 hover:text-slate-700">Investissements</button>
                                </div>
                            </div>

                            <!-- Projects Management -->
                            <div id="adminProjectsTab" class="p-6">
                                <h3 class="text-lg font-bold text-brand-dark mb-4">Gestion des Projets</h3>
                                <div id="adminProjectsList" class="space-y-4">
                                    <p class="text-slate-500 text-center py-8">Chargement des projets...</p>
                                </div>
                            </div>

                            <!-- Users Management -->
                            <div id="adminUsersTab" class="p-6 hidden">
                                <h3 class="text-lg font-bold text-brand-dark mb-4">Gestion des Utilisateurs</h3>
                                <div id="adminUsersList" class="space-y-4">
                                    <p class="text-slate-500 text-center py-8">Chargement des utilisateurs...</p>
                                </div>
                            </div>

                            <!-- Investments Management -->
                            <div id="adminInvestmentsTab" class="p-6 hidden">
                                <h3 class="text-lg font-bold text-brand-dark mb-4">Gestion des Investissements</h3>
                                <div id="adminInvestmentsList" class="space-y-4">
                                    <p class="text-slate-500 text-center py-8">Chargement des investissements...</p>
                                </div>
                            </div>
                        </div>

                        <!-- Logout Button -->
                        <div class="mt-6 text-center">
                            <button onclick="handleAdminLogout()" class="bg-red-500 hover:bg-red-600 text-white font-bold px-6 py-3 rounded-xl text-sm transition-all">
                                <i class="fa-solid fa-arrow-right-from-bracket mr-2"></i>
                                Déconnexion Admin
                            </button>
                        </div>
                    </div>
                </div>
            </section>
        </div>


        <!-- ================= PAGE 7: ACTUALITÉS ================= -->
        <div id="page-actualites" class="tab-content hidden">
            <!-- Header Section -->
            <section class="bg-brand-dark text-white py-12 border-b border-slate-800">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-xs text-slate-400 space-x-2 mb-3">
                        <span class="hover:text-white cursor-pointer" onclick="switchTab('accueil')">Accueil</span>
                        <span>/</span>
                        <span class="text-brand-gold font-medium">Actualités</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-extrabold">Espace Actualités</h1>
                </div>
            </section>

            <!-- News Grid with Categorized Filter buttons -->
            <section class="py-16 bg-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <!-- Category Tabs -->
                    <div class="flex flex-wrap gap-2 justify-center mb-12">
                        <button
                            class="px-5 py-2 rounded-full text-sm font-bold bg-brand-navy text-white">Toutes</button>
                        <button
                            class="px-5 py-2 rounded-full text-sm font-semibold bg-slate-100 hover:bg-slate-200 text-slate-600">Entreprise</button>
                        <button
                            class="px-5 py-2 rounded-full text-sm font-semibold bg-slate-100 hover:bg-slate-200 text-slate-600">Projets</button>
                        <button
                            class="px-5 py-2 rounded-full text-sm font-semibold bg-slate-100 hover:bg-slate-200 text-slate-600">Marché</button>
                        <button
                            class="px-5 py-2 rounded-full text-sm font-semibold bg-slate-100 hover:bg-slate-200 text-slate-600">Partenariats</button>
                    </div>

                    <!-- News Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <?php if (!empty($newsItems)): ?>
                            <?php foreach ($newsItems as $item): ?>
                                <div class="bg-slate-50 border border-slate-100 rounded-2xl overflow-hidden hover:shadow-xl transition-all flex flex-col justify-between">
                                    <div>
                                        <?php if (!empty($item['image'])): ?>
                                            <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="h-48 w-full object-cover">
                                        <?php else: ?>
                                            <div class="h-48 w-full bg-slate-200"></div>
                                        <?php endif; ?>
                                        <div class="p-6">
                                            <span class="text-xs font-bold text-brand-gold block mb-2"><?php echo date('d F Y', strtotime($item['published_at'])); ?></span>
                                            <h3 class="font-bold text-brand-dark text-lg mb-3"><?php echo htmlspecialchars($item['title']); ?></h3>
                                            <p class="text-sm text-slate-600"><?php echo htmlspecialchars($item['excerpt']); ?></p>
                                        </div>
                                    </div>
                                    <div class="p-6 pt-0">
                                        <a href="?route=news/show-<?php echo urlencode($item['slug']); ?>" class="text-brand-navy font-bold text-sm hover:text-brand-gold flex items-center space-x-1">
                                            <span>Lire la suite</span>
                                            <i class="fa-solid fa-arrow-right text-xs"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-span-1 md:col-span-3 bg-slate-50 border border-slate-100 rounded-2xl p-8 text-center">
                                <p class="text-slate-600">Aucune actualité publiée pour le moment.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </div>


        <!-- ================= PAGE 7: CONTACT ================= -->
        <div id="page-contact" class="tab-content hidden">
            <!-- Header Section -->
            <section class="bg-brand-dark text-white py-12 border-b border-slate-800">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-xs text-slate-400 space-x-2 mb-3">
                        <span class="hover:text-white cursor-pointer" onclick="switchTab('accueil')">Accueil</span>
                        <span>/</span>
                        <span class="text-brand-gold font-medium">Contact</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-extrabold">Contactez-nous</h1>
                </div>
            </section>

            <!-- Main grid containing coordinates and form -->
            <section class="py-16 bg-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                        <!-- Left Coordinate Information -->
                        <div class="space-y-8">
                            <h2 class="text-2xl font-extrabold text-brand-dark">Coordonnées de l'entreprise</h2>
                            <p class="text-slate-600 leading-relaxed">
                                Nos équipes d'ingénierie et de relation investisseurs sont à votre entière écoute pour
                                structurer et catalyser la réussite de vos projets urbains durables en RD Congo.
                            </p>

                            <div class="space-y-6">
                                <!-- Address -->
                                <div class="flex items-start space-x-4">
                                    <div class="bg-brand-gold/10 text-brand-gold p-3 rounded-xl"><i
                                            class="fa-solid fa-map-location-dot"></i></div>
                                    <div>
                                        <span class="block font-bold text-slate-700">Adresse</span>
                                        <span class="text-slate-500 text-sm">Avenue de l'Unité, Quartier Himbi, Goma,
                                            Nord-Kivu, RD Congo</span>
                                    </div>
                                </div>
                                <!-- Tel -->
                                <div class="flex items-start space-x-4">
                                    <div class="bg-brand-gold/10 text-brand-gold p-3 rounded-xl"><i
                                            class="fa-solid fa-phone"></i></div>
                                    <div>
                                        <span class="block font-bold text-slate-700">Téléphone</span>
                                        <span class="text-slate-500 text-sm block">+243 900 000 000</span>
                                        <span class="text-slate-500 text-sm block">+243 800 000 000</span>
                                    </div>
                                </div>
                                <!-- Email -->
                                <div class="flex items-start space-x-4">
                                    <div class="bg-brand-gold/10 text-brand-gold p-3 rounded-xl"><i
                                            class="fa-solid fa-envelope"></i></div>
                                    <div>
                                        <span class="block font-bold text-slate-700">Email</span>
                                        <span class="text-slate-500 text-sm">contact@urbanova.cd</span>
                                    </div>
                                </div>
                                <!-- Horaires -->
                                <div class="flex items-start space-x-4">
                                    <div class="bg-brand-gold/10 text-brand-gold p-3 rounded-xl"><i
                                            class="fa-solid fa-clock"></i></div>
                                    <div>
                                        <span class="block font-bold text-slate-700">Horaires d'ouverture</span>
                                        <span class="text-slate-500 text-sm block">Lun - Ven : 8h00 - 17h00</span>
                                        <span class="text-slate-500 text-sm block">Sam : 9h00 - 13h00</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Form -->
                        <div class="bg-slate-50 p-8 rounded-3xl border border-slate-200">
                            <h3 class="font-bold text-brand-dark text-xl mb-6">Formulaire de contact</h3>

                            <form id="contactForm" class="space-y-4"
                                onsubmit="event.preventDefault(); submitContactForm();">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nom
                                            complet</label>
                                        <input type="text" id="contactName" placeholder="Votre nom"
                                            class="w-full bg-white border border-slate-300 rounded-xl p-3 text-sm focus:ring-1 focus:ring-brand-gold focus:outline-none"
                                            required>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-slate-500 uppercase mb-1">Email</label>
                                        <input type="email" id="contactEmail" placeholder="Votre email"
                                            class="w-full bg-white border border-slate-300 rounded-xl p-3 text-sm focus:ring-1 focus:ring-brand-gold focus:outline-none"
                                            required>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-slate-500 uppercase mb-1">Téléphone</label>
                                        <input type="text" id="contactPhone" placeholder="Votre téléphone"
                                            class="w-full bg-white border border-slate-300 rounded-xl p-3 text-sm focus:ring-1 focus:ring-brand-gold focus:outline-none">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-slate-500 uppercase mb-1">Sujet</label>
                                        <select id="contactSubject"
                                            class="w-full bg-white border border-slate-300 rounded-xl p-3 text-sm focus:ring-1 focus:ring-brand-gold focus:outline-none">
                                            <option>Demande d'informations</option>
                                            <option>Investissement Climat / Immobilier</option>
                                            <option>Demande de devis</option>
                                            <option>Postuler (Carrières)</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Message</label>
                                    <textarea id="contactMessage" rows="5" placeholder="Votre message..."
                                        class="w-full bg-white border border-slate-300 rounded-xl p-3 text-sm focus:ring-1 focus:ring-brand-gold focus:outline-none"
                                        required></textarea>
                                </div>

                                <button type="submit"
                                    class="w-full bg-brand-navy hover:bg-slate-800 text-white font-bold py-4 rounded-xl text-sm transition-all shadow-md">Envoyer
                                    le message</button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div>


        <!-- ================= PAGE 8: TABLEAU DE BORD PORTEUR ================= -->
        <div id="page-promoteur" class="tab-content hidden">
            <!-- Header Section -->
            <section class="bg-brand-dark text-white py-12 border-b border-slate-800">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-xs text-slate-400 space-x-2 mb-3">
                        <span class="hover:text-white cursor-pointer" onclick="switchTab('accueil')">Accueil</span>
                        <span>/</span>
                        <span class="text-brand-gold font-medium">Tableau de bord porteur</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-extrabold">Tableau de bord porteur</h1>
                    <p class="text-slate-400 mt-2">Gérez vos projets immobiliers et soumettez de nouvelles opportunités</p>
                </div>
            </section>

            <!-- Main Content Area -->
            <section class="py-12 bg-slate-50">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col lg:flex-row gap-8">
                        <!-- Left Sidebar - Stats & Projects List -->
                        <div class="lg:w-1/3 space-y-6">
                            <!-- Stats Grid -->
                            <div class="bg-white rounded-2xl shadow-lg border border-slate-200 p-6">
                                <h2 class="text-xl font-bold text-brand-dark mb-4">📊 Statistiques</h2>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-slate-50 border border-slate-200 p-4 rounded-xl text-center">
                                        <div class="text-3xl font-bold text-brand-gold mb-1" id="promoteurTotalProjects">0</div>
                                        <div class="text-xs text-slate-600">Projets</div>
                                    </div>
                                    <div class="bg-slate-50 border border-slate-200 p-4 rounded-xl text-center">
                                        <div class="text-3xl font-bold text-brand-gold mb-1" id="promoteurTotalReservations">0</div>
                                        <div class="text-xs text-slate-600">Réservations</div>
                                    </div>
                                    <div class="bg-slate-50 border border-slate-200 p-4 rounded-xl text-center">
                                        <div class="text-3xl font-bold text-brand-gold mb-1" id="promoteurTotalVisits">0</div>
                                        <div class="text-xs text-slate-600">Visites</div>
                                    </div>
                                    <div class="bg-slate-50 border border-slate-200 p-4 rounded-xl text-center">
                                        <div class="text-3xl font-bold text-brand-gold mb-1" id="promoteurPublishedProjects">0</div>
                                        <div class="text-xs text-slate-600">Publiés</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Projects List -->
                            <div class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden">
                                <div class="p-4 border-b border-slate-200 flex justify-between items-center">
                                    <h2 class="text-lg font-bold text-brand-dark">Vos projets</h2>
                                    <button onclick="showPromoteurForm()" class="bg-brand-gold hover:bg-yellow-500 text-brand-dark text-xs font-bold py-2 px-4 rounded-lg transition-all">
                                        <i class="fa-solid fa-plus mr-1"></i>Nouveau
                                    </button>
                                </div>
                                
                                <div id="promoteurProjectsList" class="divide-y divide-slate-100 max-h-96 overflow-y-auto">
                                    <div class="text-center py-8 px-4">
                                        <i class="fa-solid fa-folder-open text-3xl text-slate-400 mb-3"></i>
                                        <p class="text-sm text-slate-500">Chargement des projets...</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Side - Form -->
                        <div class="lg:w-2/3">
                            <!-- Create Project Form (Multi-step Wizard) -->
                            <div id="promoteurFormContainer" class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden hidden">
                                <!-- Steps Indicator -->
                                <div class="bg-brand-navy text-white px-6 py-6 md:px-12 flex justify-between items-center border-b border-slate-800">
                                    <div class="flex items-center space-x-3">
                                        <div id="promoteurStepIndicator-1" class="w-8 h-8 rounded-full bg-brand-gold text-brand-dark flex items-center justify-center font-bold text-sm">1</div>
                                        <span id="promoteurStepText-1" class="hidden md:inline text-xs font-bold uppercase tracking-wider text-brand-gold">Infos générales</span>
                                    </div>
                                    <div class="h-0.5 w-8 bg-slate-700"></div>
                                    <div class="flex items-center space-x-3">
                                        <div id="promoteurStepIndicator-2" class="w-8 h-8 rounded-full bg-slate-800 text-slate-400 border border-slate-700 flex items-center justify-center font-bold text-sm">2</div>
                                        <span id="promoteurStepText-2" class="hidden md:inline text-xs font-bold uppercase tracking-wider text-slate-400">Financement</span>
                                    </div>
                                    <div class="h-0.5 w-8 bg-slate-700"></div>
                                    <div class="flex items-center space-x-3">
                                        <div id="promoteurStepIndicator-3" class="w-8 h-8 rounded-full bg-slate-800 text-slate-400 border border-slate-700 flex items-center justify-center font-bold text-sm">3</div>
                                        <span id="promoteurStepText-3" class="hidden md:inline text-xs font-bold uppercase tracking-wider text-slate-400">Détails</span>
                                    </div>
                                    <div class="h-0.5 w-8 bg-slate-700"></div>
                                    <div class="flex items-center space-x-3">
                                        <div id="promoteurStepIndicator-4" class="w-8 h-8 rounded-full bg-slate-800 text-slate-400 border border-slate-700 flex items-center justify-center font-bold text-sm">4</div>
                                        <span id="promoteurStepText-4" class="hidden md:inline text-xs font-bold uppercase tracking-wider text-slate-400">Vérification</span>
                                    </div>
                                </div>

                                <!-- Form Steps -->
                                <div class="p-8 md:p-12">
                                    <!-- Step 1: General Information -->
                                    <div id="promoteurFormStep-1" class="promoteur-form-step">
                                        <h2 class="text-2xl font-bold text-brand-dark mb-6">Informations générales</h2>
                                        <div class="space-y-6">
                                            <div>
                                                <label class="block text-sm font-semibold text-slate-600 mb-2">Nom du projet *</label>
                                                <input type="text" id="promoteurProjName" class="w-full border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-brand-gold focus:outline-none" placeholder="Ex: Résidence Horizon">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-slate-600 mb-2">Promoteur / Société *</label>
                                                <input type="text" id="promoteurProjOwner" class="w-full border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-brand-gold focus:outline-none" placeholder="Ex: SARL Horizon">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-slate-600 mb-2">Localisation *</label>
                                                <input type="text" id="promoteurProjLoc" class="w-full border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-brand-gold focus:outline-none" placeholder="Ex: Goma, RDC">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-slate-600 mb-2">Secteur *</label>
                                                <select id="promoteurProjSector" class="w-full border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-brand-gold focus:outline-none">
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
                                                <textarea id="promoteurProjDesc" rows="4" class="w-full border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-brand-gold focus:outline-none" placeholder="Décrivez votre projet immobilier en détail..."></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Step 2: Financial Data -->
                                    <div id="promoteurFormStep-2" class="promoteur-form-step hidden">
                                        <h2 class="text-2xl font-bold text-brand-dark mb-6">Données financières</h2>
                                        <div class="space-y-6">
                                            <div>
                                                <label class="block text-sm font-semibold text-slate-600 mb-2">Coût du projet (USD) *</label>
                                                <input type="number" id="promoteurProjCost" class="w-full border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-brand-gold focus:outline-none" placeholder="Ex: 500000">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-slate-600 mb-2">Financement recherché (USD) *</label>
                                                <input type="number" id="promoteurProjTarget" class="w-full border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-brand-gold focus:outline-none" placeholder="Ex: 300000">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-slate-600 mb-2">ROI attendu (%)</label>
                                                <input type="number" id="promoteurProjRoi" class="w-full border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-brand-gold focus:outline-none" placeholder="Ex: 15" value="15">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-slate-600 mb-2">Type de projet</label>
                                                <select id="promoteurProjType" class="w-full border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-brand-gold focus:outline-none">
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
                                                <select id="promoteurProjOperation" class="w-full border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-brand-gold focus:outline-none">
                                                    <option value="sale">Vente</option>
                                                    <option value="rental">Location</option>
                                                    <option value="fundraising">Levée de fonds</option>
                                                    <option value="sale_fundraising">Vente + Levée de fonds</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Step 3: Additional Details -->
                                    <div id="promoteurFormStep-3" class="promoteur-form-step hidden">
                                        <h2 class="text-2xl font-bold text-brand-dark mb-6">Détails supplémentaires</h2>
                                        <div class="space-y-6">
                                            <div>
                                                <label class="block text-sm font-semibold text-slate-600 mb-2">Latitude (optionnel)</label>
                                                <input type="text" id="promoteurProjLat" class="w-full border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-brand-gold focus:outline-none" placeholder="Ex: -4.4419">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-slate-600 mb-2">Longitude (optionnel)</label>
                                                <input type="text" id="promoteurProjLng" class="w-full border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-brand-gold focus:outline-none" placeholder="Ex: 15.2663">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-slate-600 mb-2">Image URL (optionnel)</label>
                                                <input type="text" id="promoteurProjImage" class="w-full border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-brand-gold focus:outline-none" placeholder="URL de l'image du projet">
                                            </div>
                                            <div class="bg-slate-50 border border-slate-200 p-4 rounded-xl">
                                                <p class="text-sm text-slate-600 mb-2"><i class="fa-solid fa-info-circle text-brand-gold mr-2"></i>Conseil:</p>
                                                <p class="text-xs text-slate-500">Ajoutez les coordonnées GPS pour permettre aux investisseurs de localiser votre projet sur une carte.</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Step 4: Verification -->
                                    <div id="promoteurFormStep-4" class="promoteur-form-step hidden">
                                        <h2 class="text-2xl font-bold text-brand-dark mb-6">Vérification</h2>
                                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-6 space-y-4">
                                            <div class="flex justify-between items-start">
                                                <span class="text-sm font-semibold text-slate-600">Nom du projet:</span>
                                                <span id="promoteurReviewName" class="text-sm font-bold text-brand-dark">-</span>
                                            </div>
                                            <div class="flex justify-between items-start">
                                                <span class="text-sm font-semibold text-slate-600">Promoteur:</span>
                                                <span id="promoteurReviewOwner" class="text-sm font-bold text-brand-dark">-</span>
                                            </div>
                                            <div class="flex justify-between items-start">
                                                <span class="text-sm font-semibold text-slate-600">Localisation:</span>
                                                <span id="promoteurReviewLoc" class="text-sm font-bold text-brand-dark">-</span>
                                            </div>
                                            <div class="flex justify-between items-start">
                                                <span class="text-sm font-semibold text-slate-600">Secteur:</span>
                                                <span id="promoteurReviewSector" class="text-sm font-bold text-brand-dark">-</span>
                                            </div>
                                            <div class="flex justify-between items-start">
                                                <span class="text-sm font-semibold text-slate-600">Financement recherché:</span>
                                                <span id="promoteurReviewTarget" class="text-sm font-bold text-brand-dark">-</span>
                                            </div>
                                            <div class="flex justify-between items-start">
                                                <span class="text-sm font-semibold text-slate-600">ROI attendu:</span>
                                                <span id="promoteurReviewRoi" class="text-sm font-bold text-brand-dark">-</span>
                                            </div>
                                        </div>
                                        <div class="mt-6 bg-amber-50 border border-amber-200 p-4 rounded-xl">
                                            <p class="text-sm text-amber-800"><i class="fa-solid fa-exclamation-triangle mr-2"></i>En soumettant ce projet, vous confirmez que toutes les informations sont exactes et complètes.</p>
                                        </div>
                                    </div>

                                    <!-- Navigation Buttons -->
                                    <div class="flex justify-between mt-8">
                                        <button id="promoteurPrevStepBtn" onclick="navigatePromoteurForm(-1)" class="invisible bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold px-8 py-3.5 rounded-xl text-sm transition-all">
                                            Précédent
                                        </button>
                                        <button id="promoteurNextStepBtn" onclick="navigatePromoteurForm(1)" class="bg-brand-navy hover:bg-slate-800 text-white font-bold px-8 py-3.5 rounded-xl text-sm transition-all shadow-md">
                                            Suivant
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <!-- Footer Section -->
    <footer class="bg-brand-dark text-white border-t border-slate-800 pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
                <!-- Branding info -->
                <div class="space-y-4">
                    <div class="flex items-center space-x-3 cursor-pointer" onclick="switchTab('accueil')">
                        <div class="bg-brand-gold p-2 rounded-lg text-brand-dark font-extrabold text-lg">
                            <i class="fa-solid fa-city"></i>
                        </div>
                        <div>
                            <span
                                class="text-lg font-extrabold tracking-wider bg-gradient-to-r from-brand-gold via-yellow-200 to-white bg-clip-text text-transparent">URBANOVA</span>
                            <p class="text-[8px] text-slate-400 tracking-widest uppercase -mt-1">SOLUTIONS</p>
                        </div>
                    </div>
                    <p class="text-sm text-slate-400 leading-relaxed">
                        Construisons aujourd'hui les villes durables de demain. Écosystème d'investissements et
                        d'ingénierie moderne en Afrique Centrale.
                    </p>
                    <div class="flex space-x-3 text-brand-gold text-lg">
                        <a href="#" class="hover:text-white"><i class="fa-brands fa-facebook"></i></a>
                        <a href="#" class="hover:text-white"><i class="fa-brands fa-linkedin"></i></a>
                        <a href="#" class="hover:text-white"><i class="fa-brands fa-x-twitter"></i></a>
                    </div>
                </div>

                <!-- Navigation Quick links -->
                <div>
                    <h4 class="font-bold text-slate-200 mb-4 text-sm uppercase tracking-wider">Navigation</h4>
                    <ul class="space-y-2.5 text-sm text-slate-400">
                        <li><button onclick="switchTab('accueil')"
                                class="hover:text-brand-gold transition-all">Accueil</button></li>
                        <li><button onclick="switchTab('apropos')" class="hover:text-brand-gold transition-all">À
                                propos</button></li>
                        <li><button onclick="switchTab('marketplace')"
                                class="hover:text-brand-gold transition-all">Marketplace</button></li>
                        <li><button onclick="goToPromoterDashboard()" class="hover:text-brand-gold transition-all">Tableau de
                                bord</button></li>
                    </ul>
                </div>

                <!-- Resources links -->
                <div>
                    <h4 class="font-bold text-slate-200 mb-4 text-sm uppercase tracking-wider">Ressources</h4>
                    <ul class="space-y-2.5 text-sm text-slate-400">
                        <li><button onclick="setInvestorTab('dataroom'); switchTab('investisseur');"
                                class="hover:text-brand-gold transition-all">Data Room Sécurisée</button></li>
                        <li><button onclick="switchTab('actualites')"
                                class="hover:text-brand-gold transition-all">Actualités</button></li>
                        <li><button onclick="switchTab('contact')"
                                class="hover:text-brand-gold transition-all">Carrières</button></li>
                        <li><button onclick="switchTab('contact')" class="hover:text-brand-gold transition-all">Devenir
                                partenaire</button></li>
                    </ul>
                </div>

                <!-- Fast Coordinates -->
                <div>
                    <h4 class="font-bold text-slate-200 mb-4 text-sm uppercase tracking-wider">Contact</h4>
                    <p class="text-sm text-slate-400 mb-2">Goma, RD Congo</p>
                    <p class="text-sm text-slate-400 mb-2">+243 900 000 000</p>
                    <p class="text-sm text-slate-400">contact@urbanova.cd</p>
                </div>
            </div>

            <div
                class="border-t border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center text-xs text-slate-500">
                <p>&copy; 2026 URBANOVA SOLUTIONS. Tous droits réservés.</p>
                <div class="flex space-x-4 mt-4 md:mt-0">
                    <a href="#" class="hover:text-white">Mentions légales</a>
                    <a href="#" class="hover:text-white">Politique de confidentialité</a>
                    <a href="#" class="hover:text-white">CGU</a>
                </div>
            </div>
        </div>
    </footer>


    <!-- Global UI Dialog / Notification Toast -->
    <div id="toastMessage"
        class="fixed bottom-5 right-5 z-50 bg-brand-navy border border-brand-gold text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center space-x-3 transform translate-y-20 opacity-0 transition-all duration-300 pointer-events-none">
        <i class="fa-solid fa-circle-check text-brand-gold text-xl" id="toastIcon"></i>
        <div>
            <span class="block font-bold text-sm" id="toastTitle">Succès</span>
            <span class="text-xs text-slate-300" id="toastBody">Action réalisée avec succès.</span>
        </div>
    </div>

    <!-- Login Modal -->
    <div id="loginModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeLoginModal()"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl shadow-2xl w-full max-w-md p-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-extrabold text-brand-dark">Connexion</h2>
                <button onclick="closeLoginModal()" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            
            <form id="loginForm" class="space-y-4" onsubmit="event.preventDefault(); handleLogin();">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Email</label>
                    <input type="email" id="loginEmail" placeholder="votre@email.com"
                        class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-brand-gold focus:outline-none"
                        required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Mot de passe</label>
                    <input type="password" id="loginPassword" placeholder="••••••••"
                        class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-brand-gold focus:outline-none"
                        required>
                </div>
                <button type="submit"
                    class="w-full bg-brand-navy hover:bg-slate-800 text-white font-bold py-3 rounded-xl text-sm transition-all shadow-md">
                    Se connecter
                </button>
            </form>
            
            <p class="text-center text-sm text-slate-600 mt-4">
                Pas encore de compte ? 
                <button onclick="closeLoginModal(); openRegisterModal()" class="text-brand-gold font-bold hover:underline">
                    S'inscrire
                </button>
            </p>
        </div>
    </div>

    <!-- Register Modal -->
    <div id="registerModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeRegisterModal()"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-3xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center p-6 border-b border-slate-100">
                <div>
                    <h2 class="text-2xl font-extrabold text-brand-dark">Inscription Investisseur</h2>
                    <p class="text-sm text-brand-gold font-medium">Créer votre compte et soumettre votre projet</p>
                </div>
                <button onclick="closeRegisterModal()" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <!-- Steps Indicator -->
            <div class="bg-brand-navy text-white px-6 py-4 flex justify-between items-center border-b border-slate-800">
                <!-- Step 1 -->
                <div class="flex items-center space-x-2 reg-step-indicator" data-step="1">
                    <div class="w-7 h-7 rounded-full bg-brand-gold text-brand-dark flex items-center justify-center font-bold text-xs" id="regStepIndicator-1">1</div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-300" id="regStepText-1">Compte</span>
                </div>
                <div class="h-[2px] flex-grow bg-slate-700 mx-2"></div>
                <!-- Step 2 -->
                <div class="flex items-center space-x-2 reg-step-indicator" data-step="2">
                    <div class="w-7 h-7 rounded-full bg-slate-800 text-slate-400 border border-slate-700 flex items-center justify-center font-bold text-xs" id="regStepIndicator-2">2</div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400" id="regStepText-2">Projet</span>
                </div>
                <div class="h-[2px] flex-grow bg-slate-700 mx-2"></div>
                <!-- Step 3 -->
                <div class="flex items-center space-x-2 reg-step-indicator" data-step="3">
                    <div class="w-7 h-7 rounded-full bg-slate-800 text-slate-400 border border-slate-700 flex items-center justify-center font-bold text-xs" id="regStepIndicator-3">3</div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400" id="regStepText-3">Vérification</span>
                </div>
            </div>

            <div class="p-8">
                <form id="registerForm" onsubmit="event.preventDefault();">
                    <!-- STEP 1: Compte -->
                    <div id="regStep-1" class="space-y-4">
                        <h3 class="text-lg font-bold text-brand-dark mb-4">Informations de votre compte</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Nom complet *</label>
                                <input type="text" id="registerName" placeholder="Votre nom"
                                    class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-brand-gold focus:outline-none"
                                    required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Email *</label>
                                <input type="email" id="registerEmail" placeholder="votre@email.com"
                                    class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-brand-gold focus:outline-none"
                                    required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Mot de passe * (min. 8 caractères)</label>
                                <input type="password" id="registerPassword" placeholder="••••••••"
                                    class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-brand-gold focus:outline-none"
                                    required minlength="8">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Téléphone</label>
                                <input type="tel" id="registerPhone" placeholder="+243 XXX XXX XXX"
                                    class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-brand-gold focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Pays</label>
                                <input type="text" id="registerCountry" placeholder="RDC"
                                    class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-brand-gold focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Ville</label>
                                <input type="text" id="registerCity" placeholder="Goma"
                                    class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-brand-gold focus:outline-none">
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2: Projet -->
                    <div id="regStep-2" class="space-y-4 hidden">
                        <h3 class="text-lg font-bold text-brand-dark mb-4">Informations du projet</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Nom du projet *</label>
                                <input type="text" id="regProjName" placeholder="Ex: Résidence Horizon"
                                    class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-brand-gold focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Promoteur / Société *</label>
                                <input type="text" id="regProjOwner" placeholder="Ex: SARL Horizon"
                                    class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-brand-gold focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Localisation *</label>
                                <input type="text" id="regProjLoc" placeholder="Ex: Goma, RDC"
                                    class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-brand-gold focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Secteur *</label>
                                <select id="regProjSector"
                                    class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-brand-gold focus:outline-none">
                                    <option value="">Sélectionnez un secteur</option>
                                    <option value="Résidentiel">Résidentiel</option>
                                    <option value="Commercial">Commercial</option>
                                    <option value="Bureaux">Bureaux / Tertiaire</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Description du projet *</label>
                            <textarea id="regProjDesc" rows="4"
                                placeholder="Décrivez votre projet immobilier en détail..."
                                class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-brand-gold focus:outline-none"></textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Coût total du projet ($)</label>
                                <input type="number" id="regProjCost" placeholder="Ex: 800000"
                                    class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-brand-gold focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Financement recherché ($)</label>
                                <input type="number" id="regProjTarget" placeholder="Ex: 500000"
                                    class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-brand-gold focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">ROI attendu (%)</label>
                                <input type="number" id="regProjRoi" placeholder="Ex: 24"
                                    class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-brand-gold focus:outline-none">
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <input type="checkbox" id="regSkipProject" class="rounded accent-brand-gold">
                            <label for="regSkipProject" class="text-sm text-slate-600">Je veux soumettre un projet plus tard</label>
                        </div>
                    </div>

                    <!-- STEP 3: Vérification -->
                    <div id="regStep-3" class="space-y-4 hidden">
                        <div class="bg-emerald-50 border border-emerald-100 p-6 rounded-2xl text-center">
                            <i class="fa-solid fa-circle-check text-4xl text-brand-success mb-3"></i>
                            <h3 class="text-lg font-bold text-emerald-900">Prêt à finaliser</h3>
                            <p class="text-sm text-emerald-700 mt-1">Vos informations seront enregistrées et votre projet soumis pour validation administrative.</p>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="mt-6 flex justify-between items-center border-t border-slate-100 pt-4">
                        <button type="button" id="regPrevStepBtn" onclick="navigateRegisterWizard(-1)"
                            class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold px-6 py-3 rounded-xl text-sm transition-all invisible">Précédent</button>
                        <button type="button" id="regNextStepBtn" onclick="navigateRegisterWizard(1)"
                            class="bg-brand-navy hover:bg-slate-800 text-white font-bold px-8 py-3.5 rounded-xl text-sm transition-all shadow-md">Suivant</button>
                    </div>
                </form>
            </div>

            <div class="p-6 border-t border-slate-100 text-center">
                <p class="text-sm text-slate-600">
                    Déjà inscrit ? 
                    <button onclick="closeRegisterModal(); openLoginModal()" class="text-brand-navy font-bold hover:underline">
                        Se connecter
                    </button>
                </p>
            </div>
        </div>
    </div>


    <script>
        // Core data representing Marketplace investment projects matching specifications & image layout
        const projectDatabase = [
            {
                id: 'proj-1',
                title: 'Résidence Horizon',
                location: 'Goma, RDC',
                country: 'RDC',
                city: 'Goma',
                sector: 'Résidentiel',
                target: 800000,
                raised: 250000,
                roi: 24,
                progress: 31,
                status: 'En cours',
                image: 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=600&q=80'
            },
            {
                id: 'proj-2',
                title: 'Urban Business Park',
                location: 'Kinshasa, RDC',
                country: 'RDC',
                city: 'Kinshasa',
                sector: 'Bureaux',
                target: 6000000,
                raised: 2100000,
                roi: 28,
                progress: 35,
                status: 'En cours',
                image: 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=600&q=80'
            },
            {
                id: 'proj-3',
                title: 'Eco City Villas',
                location: 'Kigali, Rwanda',
                country: 'Rwanda',
                city: 'Kigali',
                sector: 'Résidentiel',
                target: 1500000,
                raised: 330000,
                roi: 22,
                progress: 22,
                status: 'Planifié',
                image: 'https://images.unsplash.com/photo-151291777408-0-9991f1c4c750?auto=format&fit=crop&w=600&q=80'
            },
            {
                id: 'proj-4',
                title: 'Commercial Hub Goma',
                location: 'Goma, RDC',
                country: 'RDC',
                city: 'Goma',
                sector: 'Commercial',
                target: 2000000,
                raised: 500000,
                roi: 25,
                progress: 25,
                status: 'En cours',
                image: 'https://images.unsplash.com/photo-1554469384-e58fac16e23a?auto=format&fit=crop&w=600&q=80'
            }
        ];

        // Active state managers
        let activeTab = 'accueil';
        let wizardStep = 1;
        let isDataRoomUnlocked = false;
        let currentUser = null;
        let registerWizardStep = 1;
        let isAdminAuthenticated = false;
        const ADMIN_PASSWORD = <?php echo json_encode($adminPassword); ?>;

        // On Load initialization
        window.onload = function () {
            // Check authentication status
            checkAuthStatus();
            initializeProjectWizardFileInputs();
        }

        function initializeProjectWizardFileInputs() {
            const businessPlanDropzone = document.getElementById('businessPlanDropzone');
            const businessPlanInput = document.getElementById('businessPlanInput');
            const businessPlanFileName = document.getElementById('businessPlanFileName');
            const financialModelDropzone = document.getElementById('financialModelDropzone');
            const financialModelInput = document.getElementById('financialModelInput');
            const financialModelFileName = document.getElementById('financialModelFileName');

            const assignFilesToInput = (input, file) => {
                if (!input || !file) return;
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                input.files = dataTransfer.files;
            };

            if (businessPlanDropzone && businessPlanInput) {
                businessPlanDropzone.addEventListener('click', () => businessPlanInput.click());
                businessPlanDropzone.addEventListener('dragover', (event) => {
                    event.preventDefault();
                    businessPlanDropzone.classList.add('border-brand-gold', 'bg-slate-100');
                });
                businessPlanDropzone.addEventListener('dragleave', () => {
                    businessPlanDropzone.classList.remove('border-brand-gold', 'bg-slate-100');
                });
                businessPlanDropzone.addEventListener('drop', (event) => {
                    event.preventDefault();
                    businessPlanDropzone.classList.remove('border-brand-gold', 'bg-slate-100');
                    if (event.dataTransfer.files.length > 0) {
                        assignFilesToInput(businessPlanInput, event.dataTransfer.files[0]);
                        const file = event.dataTransfer.files[0];
                        if (businessPlanFileName) {
                            businessPlanFileName.textContent = file.name;
                        }
                    }
                });

                businessPlanInput.addEventListener('change', () => {
                    const file = businessPlanInput.files[0];
                    if (file && businessPlanFileName) {
                        businessPlanFileName.textContent = file.name;
                    }
                });
            }

            const pitchDeckDropzone = document.getElementById('pitchDeckDropzone');
            const pitchDeckInput = document.getElementById('pitchDeckInput');
            const pitchDeckFileName = document.getElementById('pitchDeckFileName');

            if (financialModelDropzone && financialModelInput) {
                financialModelDropzone.addEventListener('click', () => financialModelInput.click());
                financialModelDropzone.addEventListener('dragover', (event) => {
                    event.preventDefault();
                    financialModelDropzone.classList.add('border-brand-gold', 'bg-slate-100');
                });
                financialModelDropzone.addEventListener('dragleave', () => {
                    financialModelDropzone.classList.remove('border-brand-gold', 'bg-slate-100');
                });
                financialModelDropzone.addEventListener('drop', (event) => {
                    event.preventDefault();
                    financialModelDropzone.classList.remove('border-brand-gold', 'bg-slate-100');
                    if (event.dataTransfer.files.length > 0) {
                        assignFilesToInput(financialModelInput, event.dataTransfer.files[0]);
                        const file = event.dataTransfer.files[0];
                        if (financialModelFileName) {
                            financialModelFileName.textContent = file.name;
                        }
                    }
                });

                financialModelInput.addEventListener('change', () => {
                    const file = financialModelInput.files[0];
                    if (file && financialModelFileName) {
                        financialModelFileName.textContent = file.name;
                    }
                });
            }

            if (pitchDeckDropzone && pitchDeckInput) {
                pitchDeckDropzone.addEventListener('click', () => pitchDeckInput.click());
                pitchDeckDropzone.addEventListener('dragover', (event) => {
                    event.preventDefault();
                    pitchDeckDropzone.classList.add('border-brand-gold', 'bg-slate-100');
                });
                pitchDeckDropzone.addEventListener('dragleave', () => {
                    pitchDeckDropzone.classList.remove('border-brand-gold', 'bg-slate-100');
                });
                pitchDeckDropzone.addEventListener('drop', (event) => {
                    event.preventDefault();
                    pitchDeckDropzone.classList.remove('border-brand-gold', 'bg-slate-100');
                    if (event.dataTransfer.files.length > 0) {
                        assignFilesToInput(pitchDeckInput, event.dataTransfer.files[0]);
                        const file = event.dataTransfer.files[0];
                        if (pitchDeckFileName) {
                            pitchDeckFileName.textContent = file.name;
                        }
                    }
                });

                pitchDeckInput.addEventListener('change', () => {
                    const file = pitchDeckInput.files[0];
                    if (file && pitchDeckFileName) {
                        pitchDeckFileName.textContent = file.name;
                    }
                });
            }
        }

        // Check authentication status on load
        async function checkAuthStatus() {
            try {
                const response = await fetch('api.php?action=check-auth');
                const result = await response.json();
                
                if (result.authenticated && result.user) {
                    currentUser = result.user;
                    updateUIForLoggedInUser();
                }
                
                // Always update investor space button based on auth status
                updateInvestorSpaceButton();
            } catch (error) {
                console.error('Auth check failed:', error);
                updateInvestorSpaceButton();
            }
        }

        // Update investor space button based on auth status
        function updateInvestorSpaceButton() {
            const logoutIcon = document.getElementById('investorLogoutIcon');
            const logoutText = document.getElementById('investorLogoutText');
            if (logoutIcon && logoutText) {
                if (currentUser && currentUser.role === 'investor') {
                    logoutIcon.className = 'fa-solid fa-arrow-right-from-bracket';
                    logoutText.textContent = 'Déconnexion';
                } else {
                    logoutIcon.className = 'fa-solid fa-user-plus';
                    logoutText.textContent = 'S\'inscrire';
                }
            }
        }

        // Update UI when user is logged in
        function updateUIForLoggedInUser() {
            // Update header buttons
            const actionsDiv = document.querySelector('.hidden.md\\:flex.items-center.space-x-4');
            if (actionsDiv && currentUser) {
                const roleLabel = currentUser.role === 'investor' ? 'Investisseur' : 'Utilisateur';
                actionsDiv.innerHTML = `
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-full bg-brand-gold text-brand-dark flex items-center justify-center font-bold text-sm">
                            ${currentUser.initials || currentUser.name.substring(0, 2).toUpperCase()}
                        </div>
                        <div class="flex flex-col">
                            <span class="text-slate-300 text-sm font-medium">${currentUser.name}</span>
                            <span class="text-xs text-brand-gold">${roleLabel}</span>
                        </div>
                    </div>
                    <button onclick="handleLogout()" class="text-slate-300 hover:text-white text-sm font-medium transition-all">
                        Déconnexion
                    </button>
                `;
            }

            // Update mobile menu
            const mobileMenuAuth = document.querySelector('#mobileMenuAuth');
            if (mobileMenuAuth && currentUser) {
                const roleLabel = currentUser.role === 'investor' ? 'Investisseur' : 'Utilisateur';
                mobileMenuAuth.innerHTML = `
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="w-8 h-8 rounded-full bg-brand-gold text-brand-dark flex items-center justify-center font-bold text-sm">
                            ${currentUser.initials || currentUser.name.substring(0, 2).toUpperCase()}
                        </div>
                        <div class="flex flex-col">
                            <span class="text-slate-300 text-sm font-medium">${currentUser.name}</span>
                            <span class="text-xs text-brand-gold">${roleLabel}</span>
                        </div>
                    </div>
                    <button onclick="handleLogout(); toggleMobileMenu()" class="w-full text-center py-2 text-slate-300 hover:text-white font-medium">Déconnexion</button>
                `;
            }
        }

        // Modal functions
        function openLoginModal() {
            document.getElementById('loginModal').classList.remove('hidden');
        }

        function closeLoginModal() {
            document.getElementById('loginModal').classList.add('hidden');
        }

        function openRegisterModal() {
            document.getElementById('registerModal').classList.remove('hidden');
            registerWizardStep = 1;
            updateRegisterStepIndicators();
            document.getElementById('regStep-1').classList.remove('hidden');
            document.getElementById('regStep-2').classList.add('hidden');
            document.getElementById('regStep-3').classList.add('hidden');
        }

        function closeRegisterModal() {
            document.getElementById('registerModal').classList.add('hidden');
            document.getElementById('registerForm').reset();
            registerWizardStep = 1;
            updateRegisterStepIndicators();
        }

        // Handle login
        async function handleLogin() {
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;

            console.log('Attempting login with:', { email });
            
            try {
                const response = await fetch('api.php?action=login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ email, password })
                });

                console.log('Login response status:', response.status);
                const result = await response.json();
                console.log('Login result:', result);

                if (result.success) {
                    showToast("Connexion réussie", "Bienvenue " + result.user.name + " !");
                    currentUser = result.user;
                    closeLoginModal();
                    updateUIForLoggedInUser();
                    updateInvestorSpaceButton();
                    document.getElementById('loginForm').reset();
                } else {
                    console.error('Login failed:', result.message);
                    showToast("Erreur", result.message, "warning");
                }
            } catch (error) {
                console.error('Login error:', error);
                showToast("Erreur", "Erreur de connexion au serveur", "warning");
            }
        }

        // Handle register
        async function handleRegister() {
            // Step 1: Account validation
            if (registerWizardStep === 1) {
                const name = document.getElementById('registerName').value;
                const email = document.getElementById('registerEmail').value;
                const password = document.getElementById('registerPassword').value;

                if (!name || !email || !password) {
                    showToast("Champs requis", "Veuillez remplir tous les champs obligatoires (*)", "warning");
                    return;
                }

                if (password.length < 8) {
                    showToast("Mot de passe", "Le mot de passe doit contenir au moins 8 caractères", "warning");
                    return;
                }

                navigateRegisterWizard(1);
                return;
            }

            // Step 2: Project validation
            if (registerWizardStep === 2) {
                const skipProject = document.getElementById('regSkipProject').checked;
                
                if (!skipProject) {
                    const projName = document.getElementById('regProjName').value;
                    const projOwner = document.getElementById('regProjOwner').value;
                    const projLoc = document.getElementById('regProjLoc').value;
                    const projSector = document.getElementById('regProjSector').value;

                    if (!projName || !projOwner || !projLoc || !projSector) {
                        showToast("Champs requis", "Veuillez remplir tous les champs obligatoires (*) du projet", "warning");
                        return;
                    }
                }

                navigateRegisterWizard(1);
                return;
            }

            // Step 3: Submit everything
            const name = document.getElementById('registerName').value;
            const email = document.getElementById('registerEmail').value;
            const password = document.getElementById('registerPassword').value;
            const phone = document.getElementById('registerPhone').value;
            const country = document.getElementById('registerCountry').value;
            const city = document.getElementById('registerCity').value;

            const skipProject = document.getElementById('regSkipProject').checked;
            let projectData = null;

            if (!skipProject) {
                projectData = {
                    name: document.getElementById('regProjName').value,
                    owner: document.getElementById('regProjOwner').value,
                    location: document.getElementById('regProjLoc').value,
                    sector: document.getElementById('regProjSector').value,
                    description: document.getElementById('regProjDesc').value,
                    cost: document.getElementById('regProjCost').value,
                    target: document.getElementById('regProjTarget').value,
                    roi: document.getElementById('regProjRoi').value
                };
            }

            try {
                console.log('Attempting registration with:', { name, email, phone, country, city });
                
                // Register user
                const registerResponse = await fetch('api.php?action=register', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ name, email, password, phone, country, city })
                });

                console.log('Register response status:', registerResponse.status);
                const registerResult = await registerResponse.json();
                console.log('Register result:', registerResult);

                if (!registerResult.success) {
                    showToast("Erreur", registerResult.message, "warning");
                    return;
                }

                currentUser = registerResult.user;

                // Submit project if provided
                if (projectData) {
                    const projectResponse = await fetch('api.php?action=submit-project', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(projectData)
                    });

                    const projectResult = await projectResponse.json();

                    if (projectResult.success) {
                        showToast("Inscription réussie", "Compte créé et projet soumis avec succès !");
                        await loadProjectsFromAPI();
                        closeRegisterModal();
                        updateUIForLoggedInUser();
                        document.getElementById('registerForm').reset();
                        registerWizardStep = 1;
                        updateRegisterStepIndicators();
                    } else {
                        showToast("Compte créé", "Votre compte a été créé mais le projet n'a pas pu être soumis: " + projectResult.message, "warning");
                        closeRegisterModal();
                        updateUIForLoggedInUser();
                        document.getElementById('registerForm').reset();
                        registerWizardStep = 1;
                        updateRegisterStepIndicators();
                    }
                } else {
                    showToast("Inscription réussie", "Bienvenue " + registerResult.user.name + " !");
                    closeRegisterModal();
                    updateUIForLoggedInUser();
                    document.getElementById('registerForm').reset();
                    registerWizardStep = 1;
                    updateRegisterStepIndicators();
                }
            } catch (error) {
                console.error('Register error:', error);
                showToast("Erreur", "Erreur de connexion au serveur", "warning");
            }
        }

        // Register wizard navigation
        function navigateRegisterWizard(direction) {
            const currentForm = document.getElementById(`regStep-${registerWizardStep}`);
            currentForm.classList.add('hidden');
            registerWizardStep += direction;

            if (registerWizardStep > 3) {
                // Final submission
                handleRegister();
                return;
            }

            if (registerWizardStep < 1) {
                registerWizardStep = 1;
            }

            const nextForm = document.getElementById(`regStep-${registerWizardStep}`);
            nextForm.classList.remove('hidden');

            updateRegisterStepIndicators();
        }

        function updateRegisterStepIndicators() {
            // Manage Previous button visibility
            const prevBtn = document.getElementById('regPrevStepBtn');
            if (registerWizardStep > 1) {
                prevBtn.classList.remove('invisible');
            } else {
                prevBtn.classList.add('invisible');
            }

            // Manage Next button text
            const nextBtn = document.getElementById('regNextStepBtn');
            if (registerWizardStep === 3) {
                nextBtn.innerText = "Finaliser l'inscription";
                nextBtn.className = "bg-brand-success hover:bg-emerald-600 text-white font-bold px-8 py-3.5 rounded-xl text-sm transition-all shadow-md";
            } else {
                nextBtn.innerText = "Suivant";
                nextBtn.className = "bg-brand-navy hover:bg-slate-800 text-white font-bold px-8 py-3.5 rounded-xl text-sm transition-all shadow-md";
            }

            // Render Circle markers color
            for (let i = 1; i <= 3; i++) {
                const indicator = document.getElementById(`regStepIndicator-${i}`);
                const text = document.getElementById(`regStepText-${i}`);

                if (i === registerWizardStep) {
                    indicator.className = "w-7 h-7 rounded-full bg-brand-gold text-brand-dark flex items-center justify-center font-bold text-xs";
                    if (text) text.className = "text-xs font-bold uppercase tracking-wider text-brand-gold";
                } else if (i < registerWizardStep) {
                    indicator.className = "w-7 h-7 rounded-full bg-brand-success text-white flex items-center justify-center font-bold text-xs";
                    if (text) text.className = "text-xs font-bold uppercase tracking-wider text-brand-success";
                } else {
                    indicator.className = "w-7 h-7 rounded-full bg-slate-800 text-slate-400 border border-slate-700 flex items-center justify-center font-bold text-xs";
                    if (text) text.className = "text-xs font-bold uppercase tracking-wider text-slate-400";
                }
            }
        }

        // Handle investor space action (logout or register)
        function handleInvestorSpaceAction() {
            if (currentUser && currentUser.role === 'investor') {
                // User is logged in as investor - logout
                handleLogout();
            } else {
                // User is not logged in or not investor - show register modal
                closeRegisterModal();
                closeLoginModal();
                openRegisterModal();
            }
        }

        // Handle logout
        async function handleLogout() {
            try {
                const response = await fetch('api.php?action=logout');
                const result = await response.json();

                if (result.success) {
                    showToast("Déconnexion", "Vous avez été déconnecté avec succès");
                    currentUser = null;
                    updateInvestorSpaceButton();
                    location.reload();
                }
            } catch (error) {
                console.error('Logout error:', error);
            }
        }

        // Load projects from PHP API
        async function loadProjectsFromAPI() {
            try {
                const response = await fetch('api.php?action=projects');
                const result = await response.json();
                
                if (result.success) {
                    projectDatabase = result.data;
                    renderMarketplace(projectDatabase);
                } else {
                    console.error('API Error:', result.message);
                    showToast("Erreur", result.message || "Erreur lors du chargement des projets", "warning");
                    renderMarketplace([]);
                }
            } catch (error) {
                console.error('Error loading projects:', error);
                showToast("Erreur de connexion", "Impossible de se connecter au serveur", "warning");
                renderMarketplace([]);
            }
        }

        // Go to Promoter Dashboard
        function goToPromoterDashboard() {
            if (!currentUser) {
                showToast("Authentification requise", "Veuillez vous connecter pour accéder au tableau de bord porteur", "warning");
                openLoginModal();
                return;
            }
            switchTab('promoteur');
        }

        function goToMarketplace() {
            if (!currentUser) {
                showToast("Authentification requise", "Veuillez vous connecter pour accéder à la Marketplace", "warning");
                openLoginModal();
                return;
            }
            switchTab('marketplace');
        }

        // Promoter Dashboard Functions
        let promoteurWizardStep = 1;

        function showPromoteurForm() {
            document.getElementById('promoteurFormContainer').classList.remove('hidden');
            resetPromoteurForm();
        }

        function resetPromoteurForm() {
            promoteurWizardStep = 1;
            document.querySelectorAll('.promoteur-form-step').forEach(step => step.classList.add('hidden'));
            document.getElementById('promoteurFormStep-1').classList.remove('hidden');
            updatePromoteurStepIndicators();
            document.getElementById('promoteurPrevStepBtn').classList.add('invisible');
            document.getElementById('promoteurNextStepBtn').innerText = 'Suivant';
            document.getElementById('promoteurNextStepBtn').className = 'bg-brand-navy hover:bg-slate-800 text-white font-bold px-8 py-3.5 rounded-xl text-sm transition-all shadow-md';
        }

        function navigatePromoteurForm(direction) {
            const currentForm = document.getElementById(`promoteurFormStep-${promoteurWizardStep}`);
            
            // Validation
            if (direction === 1) {
                if (promoteurWizardStep === 1) {
                    const name = document.getElementById('promoteurProjName').value;
                    const owner = document.getElementById('promoteurProjOwner').value;
                    const loc = document.getElementById('promoteurProjLoc').value;
                    const sector = document.getElementById('promoteurProjSector').value;
                    const desc = document.getElementById('promoteurProjDesc').value;
                    if (!name || !owner || !loc || !sector || !desc) {
                        showToast('Champs requis', 'Veuillez remplir tous les champs obligatoires (*)', 'warning');
                        return;
                    }
                }
                if (promoteurWizardStep === 2) {
                    const cost = document.getElementById('promoteurProjCost').value;
                    const target = document.getElementById('promoteurProjTarget').value;
                    if (!cost || !target) {
                        showToast('Données financières', 'Veuillez renseigner le coût et le financement cible.', 'warning');
                        return;
                    }
                }
                if (promoteurWizardStep === 3) {
                    // Populate review
                    document.getElementById('promoteurReviewName').textContent = document.getElementById('promoteurProjName').value;
                    document.getElementById('promoteurReviewOwner').textContent = document.getElementById('promoteurProjOwner').value;
                    document.getElementById('promoteurReviewLoc').textContent = document.getElementById('promoteurProjLoc').value;
                    document.getElementById('promoteurReviewSector').textContent = document.getElementById('promoteurProjSector').value;
                    document.getElementById('promoteurReviewTarget').textContent = parseInt(document.getElementById('promoteurProjTarget').value).toLocaleString() + ' $';
                    document.getElementById('promoteurReviewRoi').textContent = document.getElementById('promoteurProjRoi').value + '%';
                }
            }

            currentForm.classList.add('hidden');
            promoteurWizardStep += direction;

            if (promoteurWizardStep > 4) {
                submitPromoteurProject();
                return;
            }

            if (promoteurWizardStep < 1) {
                promoteurWizardStep = 1;
            }

            const nextForm = document.getElementById(`promoteurFormStep-${promoteurWizardStep}`);
            nextForm.classList.remove('hidden');

            updatePromoteurStepIndicators();
        }

        function updatePromoteurStepIndicators() {
            const prevBtn = document.getElementById('promoteurPrevStepBtn');
            if (promoteurWizardStep > 1) {
                prevBtn.classList.remove('invisible');
            } else {
                prevBtn.classList.add('invisible');
            }

            const nextBtn = document.getElementById('promoteurNextStepBtn');
            if (promoteurWizardStep === 4) {
                nextBtn.innerText = 'Finaliser la soumission';
                nextBtn.className = 'bg-brand-success hover:bg-emerald-600 text-white font-bold px-8 py-3.5 rounded-xl text-sm transition-all shadow-md';
            } else {
                nextBtn.innerText = 'Suivant';
                nextBtn.className = 'bg-brand-navy hover:bg-slate-800 text-white font-bold px-8 py-3.5 rounded-xl text-sm transition-all shadow-md';
            }

            for (let i = 1; i <= 4; i++) {
                const indicator = document.getElementById(`promoteurStepIndicator-${i}`);
                const text = document.getElementById(`promoteurStepText-${i}`);

                if (i === promoteurWizardStep) {
                    indicator.className = 'w-8 h-8 rounded-full bg-brand-gold text-brand-dark flex items-center justify-center font-bold text-sm';
                    if (text) text.className = 'hidden md:inline text-xs font-bold uppercase tracking-wider text-brand-gold';
                } else if (i < promoteurWizardStep) {
                    indicator.className = 'w-8 h-8 rounded-full bg-brand-success text-white flex items-center justify-center font-bold text-sm';
                    if (text) text.className = 'hidden md:inline text-xs font-bold uppercase tracking-wider text-brand-success';
                } else {
                    indicator.className = 'w-8 h-8 rounded-full bg-slate-800 text-slate-400 border border-slate-700 flex items-center justify-center font-bold text-sm';
                    if (text) text.className = 'hidden md:inline text-xs font-bold uppercase tracking-wider text-slate-400';
                }
            }
        }

        async function submitPromoteurProject() {
            const projectData = {
                name: document.getElementById('promoteurProjName').value,
                owner: document.getElementById('promoteurProjOwner').value,
                location: document.getElementById('promoteurProjLoc').value,
                sector: document.getElementById('promoteurProjSector').value,
                description: document.getElementById('promoteurProjDesc').value,
                cost: document.getElementById('promoteurProjCost').value,
                target: document.getElementById('promoteurProjTarget').value,
                roi: document.getElementById('promoteurProjRoi').value,
                project_type: document.getElementById('promoteurProjType').value,
                operation_type: document.getElementById('promoteurProjOperation').value,
                coordinates_lat: document.getElementById('promoteurProjLat').value,
                coordinates_lng: document.getElementById('promoteurProjLng').value,
                image: document.getElementById('promoteurProjImage').value
            };

            try {
                const response = await fetch('api.php?action=submit-project', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(projectData)
                });

                const result = await response.json();

                if (result.success) {
                    showToast('Succès', 'Projet soumis avec succès !', 'success');
                    resetPromoteurForm();
                    loadPromoteurProjects();
                } else {
                    showToast('Erreur', result.message, 'warning');
                }
            } catch (error) {
                showToast('Erreur', 'Erreur de connexion', 'warning');
            }
        }

        function loadPromoteurProjects() {
            const container = document.getElementById('promoteurProjectsList');
            
            if (container) {
                container.innerHTML = '<div class="text-center py-8 px-4"><i class="fa-solid fa-spinner fa-spin text-2xl text-brand-gold"></i><p class="text-slate-500 mt-2">Chargement des projets...</p></div>';
            }

            fetch('api.php?action=get-promoter-projects')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        renderPromoteurProjects(data.data);
                        updatePromoteurStats(data.data);
                    } else {
                        if (container) {
                            container.innerHTML = '<div class="text-center py-8 px-4 text-red-500">Erreur lors du chargement des projets</div>';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading promoter projects:', error);
                    if (container) {
                        container.innerHTML = '<div class="text-center py-8 px-4 text-red-500">Erreur de connexion</div>';
                    }
                });
        }

        function renderPromoteurProjects(projects) {
            const container = document.getElementById('promoteurProjectsList');
            
            if (!container) return;
            container.innerHTML = '';

            if (projects.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8 px-4">
                        <i class="fa-solid fa-folder-open text-3xl text-slate-400 mb-3"></i>
                        <p class="text-sm text-slate-500">Aucun projet pour le moment</p>
                    </div>
                `;
                return;
            }

            projects.forEach(p => {
                const statusTranslations = {
                    'draft': 'Brouillon',
                    'submitted': 'Soumis',
                    'under_review': 'En analyse',
                    'approved': 'Approuvé',
                    'published': 'Publié',
                    'rejected': 'Rejeté'
                };
                const translatedStatus = statusTranslations[p.validation_status] || p.validation_status;

                const item = document.createElement('div');
                item.className = 'p-4 hover:bg-slate-50 cursor-pointer';
                item.innerHTML = `
                    <div class="font-bold text-brand-dark text-sm">${p.title}</div>
                    <div class="text-xs text-slate-500 mb-2">${p.city}</div>
                    <span class="status-badge status-${p.validation_status} text-xs">
                        ${translatedStatus}
                    </span>
                `;
                container.appendChild(item);
            });
        }

        function updatePromoteurStats(projects) {
            document.getElementById('promoteurTotalProjects').textContent = projects.length;
            document.getElementById('promoteurTotalReservations').textContent = projects.reduce((sum, p) => sum + (p.reservation_count || 0), 0);
            document.getElementById('promoteurTotalVisits').textContent = projects.reduce((sum, p) => sum + (p.visit_count || 0), 0);
            document.getElementById('promoteurPublishedProjects').textContent = projects.filter(p => p.validation_status === 'published').length;
        }

        // Tab Switcher between Pages
        async function switchTab(tabId) {
            // Hide all tab contents first
            const contents = document.querySelectorAll('.tab-content');
            contents.forEach(el => el.classList.add('hidden'));

            // Check auth for promoter tab
            if (tabId === 'promoteur') {
                if (!currentUser) {
                    showToast("Authentification requise", "Veuillez vous connecter pour accéder au tableau de bord porteur", "warning");
                    openLoginModal();
                    return;
                }
                loadPromoteurProjects();
            }

            // Check auth for marketplace tab
            if (tabId === 'marketplace') {
                if (!currentUser) {
                    showToast("Authentification requise", "Veuillez vous connecter pour accéder à la Marketplace", "warning");
                    openLoginModal();
                    return;
                }
                loadMarketplaceProjects();
            }

            // Load marketplace projects when switching to marketplace tab
            if (tabId === 'investisseur') {
                try {
                    console.log('Checking auth for investor space...');
                    
                    // Check if user is logged in via JavaScript first
                    if (!currentUser) {
                        console.log('No current user in JavaScript');
                        showToast("Authentification requise", "Veuillez vous connecter pour accéder à l'espace investisseur", "warning");
                        openLoginModal();
                        return;
                    }
                    
                    if (currentUser.role !== 'investor') {
                        console.log('User is not an investor:', currentUser.role);
                        showToast("Accès réservé", "L'espace investisseur est réservé aux comptes investisseurs", "warning");
                        return;
                    }
                    
                    // Load investor data
                    await loadInvestorData();
                    
                    // Show active tab after successful auth and data load
                    const targetEl = document.getElementById('page-' + tabId);
                    if (targetEl) {
                        targetEl.classList.remove('hidden');
                    }
                } catch (error) {
                    console.error('Auth check failed:', error);
                    showToast("Erreur", "Erreur lors de l'accès à l'espace investisseur: " + error.message, "warning");
                    return;
                }
            } else {
                // Show active tab for non-investor tabs
                const targetEl = document.getElementById('page-' + tabId);
                if (targetEl) {
                    targetEl.classList.remove('hidden');
                }
            }

            // Update Nav link classes
            const links = document.querySelectorAll('.nav-link');
            links.forEach(link => {
                link.classList.remove('text-brand-gold', 'border-b-2', 'border-brand-gold');
                link.classList.add('text-slate-300');
            });

            const activeLink = document.getElementById('nav-' + tabId);
            if (activeLink) {
                activeLink.classList.remove('text-slate-300');
                activeLink.classList.add('text-brand-gold', 'border-b-2', 'border-brand-gold');
            }

            activeTab = tabId;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Load investor data from API
        async function loadInvestorData() {
            try {
                const response = await fetch('api.php?action=investor-data');
                const result = await response.json();
                
                if (result.success) {
                    console.log('Investor data loaded:', result.data);
                    
                    // Update dashboard stats
                    const data = result.data;
                    document.getElementById('investProjectCount').textContent = data.project_count || 0;
                    document.getElementById('investTotalAmount').textContent = (data.total_invested || 0).toLocaleString() + ' $';
                    document.getElementById('investAvgRoi').textContent = (data.avg_roi || 0) + '%';
                    document.getElementById('investTotalGains').textContent = (data.total_gains || 0).toLocaleString() + ' $';
                    
                    // Render investments table
                    renderInvestorInvestments(data.investments || []);
                } else {
                    console.error('API Error:', result.message);
                    showToast("Erreur", result.message || "Erreur lors du chargement des données", "warning");
                }
            } catch (error) {
                console.error('Error loading investor data:', error);
                showToast("Erreur", "Impossible de charger les données de l'investisseur", "warning");
            }
        }

        // Render investor investments table
        function renderInvestorInvestments(investments) {
            const tableBody = document.getElementById('investorInvestmentsTable');
            
            if (!investments || investments.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="p-4 text-center text-slate-500">
                            Aucun investissement pour le moment. 
                            <button onclick="switchTab('marketplace')" class="text-brand-gold font-bold hover:underline">Explorer les opportunités</button>
                        </td>
                    </tr>
                `;
                return;
            }
            
            tableBody.innerHTML = investments.map(inv => `
                <tr>
                    <td class="p-4 font-bold text-brand-dark">${inv.project_name || 'Projet #' + inv.project_id}</td>
                    <td class="p-4">${(inv.amount || 0).toLocaleString()} $</td>
                    <td class="p-4">
                        <div class="flex items-center space-x-2">
                            <div class="w-24 bg-slate-200 h-2 rounded-full overflow-hidden">
                                <div class="bg-brand-success h-full" style="width: ${inv.progress || 0}%"></div>
                            </div>
                            <span class="text-xs font-semibold">${inv.progress || 0}%</span>
                        </div>
                    </td>
                    <td class="p-4 font-semibold">${inv.roi || 0}%</td>
                    <td class="p-4">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold ${
                            inv.status === 'active' ? 'bg-emerald-50 text-emerald-800' :
                            inv.status === 'completed' ? 'bg-blue-50 text-blue-800' :
                            'bg-slate-100 text-slate-700'
                        }">${inv.status === 'active' ? 'En cours' : inv.status === 'completed' ? 'Terminé' : inv.status}</span>
                    </td>
                    <td class="p-4">
                        <button onclick="viewInvestmentDetails(${inv.id})" class="text-brand-gold hover:text-brand-navy text-xs font-bold">
                            Détails
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        // View investment details
        function viewInvestmentDetails(investmentId) {
            showToast("Info", "Détails de l'investissement #" + investmentId + " - Fonctionnalité à venir");
        }

        // Admin Login
        function handleAdminLogin() {
            const code = document.getElementById('adminCode').value;
            
            if (code === ADMIN_PASSWORD) {
                isAdminAuthenticated = true;
                document.getElementById('adminLogin').classList.add('hidden');
                document.getElementById('adminDashboard').classList.remove('hidden');
                showToast("Accès autorisé", "Bienvenue dans l'espace administration");
                loadAdminData();
            } else {
                showToast("Mot de passe incorrect", "Le mot de passe administrateur est incorrect", "warning");
                document.getElementById('adminCode').value = '';
            }
        }

        // Admin Logout
        function handleAdminLogout() {
            isAdminAuthenticated = false;
            document.getElementById('adminLogin').classList.remove('hidden');
            document.getElementById('adminDashboard').classList.add('hidden');
            document.getElementById('adminCode').value = '';
            showToast("Déconnexion", "Vous avez été déconnecté de l'administration");
        }

        // Switch Admin Tabs
        function switchAdminTab(tab) {
            // Hide all tabs
            document.getElementById('adminProjectsTab').classList.add('hidden');
            document.getElementById('adminUsersTab').classList.add('hidden');
            document.getElementById('adminInvestmentsTab').classList.add('hidden');
            
            // Show selected tab
            document.getElementById('admin' + tab.charAt(0).toUpperCase() + tab.slice(1) + 'Tab').classList.remove('hidden');
            
            // Update tab styles
            document.querySelectorAll('.admin-tab').forEach(btn => {
                btn.classList.remove('text-brand-navy', 'border-b-2', 'border-brand-navy');
                btn.classList.add('text-slate-500');
            });
            
            event.target.classList.remove('text-slate-500');
            event.target.classList.add('text-brand-navy', 'border-b-2', 'border-brand-navy');
        }

        // Load Admin Data
        async function loadAdminData() {
            try {
                // Load projects
                const projectsResponse = await fetch('api.php?action=projects');
                const projectsResult = await projectsResponse.json();
                
                if (projectsResult.success) {
                    const projects = projectsResult.data;
                    const pending = projects.filter(p => (p.raw_status || p.status) === 'pending' || (p.raw_status || p.status) === 'submitted' || p.status === 'En attente' || p.status === 'Soumis').length;
                    const approved = projects.filter(p => (p.raw_status || p.status) === 'approved' || p.status === 'Approuvé').length;
                    
                    document.getElementById('adminPendingProjects').textContent = pending;
                    document.getElementById('adminApprovedProjects').textContent = approved;
                    
                    // Render projects list
                    renderAdminProjects(projects);
                }
                
                // Load users
                try {
                    const usersResponse = await fetch('api.php?action=get-users', {
                        headers: { 'X-Admin-Password': ADMIN_PASSWORD }
                    });
                    const usersResult = await usersResponse.json();
                    if (usersResult.success) {
                        document.getElementById('adminTotalUsers').textContent = usersResult.data.length;
                        renderAdminUsers(usersResult.data);
                    }
                } catch (e) {
                    console.error('Error loading users:', e);
                }
                
                // Load investments
                try {
                    const invResponse = await fetch('api.php?action=get-investments', {
                        headers: { 'X-Admin-Password': ADMIN_PASSWORD }
                    });
                    const invResult = await invResponse.json();
                    if (invResult.success) {
                        const totalFormatted = (invResult.total_amount || 0).toLocaleString('fr-FR');
                        document.getElementById('adminTotalInvestments').textContent = totalFormatted + ' $';
                        renderAdminInvestments(invResult.data);
                    }
                } catch (e) {
                    console.error('Error loading investments:', e);
                }
                
            } catch (error) {
                console.error('Error loading admin data:', error);
            }
        }

        // Render Admin Users List
        function renderAdminUsers(users) {
            const container = document.getElementById('adminUsersList');
            if (!container) return;
            // Cache for viewUserDetailsAdmin
            window._adminUsersCache = users || [];

            if (!users || users.length === 0) {
                container.innerHTML = '<p class="text-slate-500 text-center py-8">Aucun utilisateur inscrit</p>';
                return;
            }

            container.innerHTML = users.map(user => `
                <div class="bg-slate-50 rounded-xl p-4 border border-slate-200 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <div class="flex items-center space-x-3">
                            <span class="font-bold text-brand-dark text-base">${user.name}</span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold ${
                                user.role === 'admin' ? 'bg-purple-100 text-purple-700' :
                                user.role === 'promoter' ? 'bg-blue-100 text-blue-700' :
                                'bg-emerald-100 text-emerald-700'
                            }">
                                ${user.role === 'admin' ? 'Administrateur' : user.role === 'promoter' ? 'Porteur de projet' : 'Investisseur'}
                            </span>
                            <span class="px-2 py-0.5 rounded-full text-xs ${user.status === 'blocked' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'}">
                                ${user.status === 'blocked' ? 'Bloqué' : 'Actif'}
                            </span>
                        </div>
                        <div class="flex items-center space-x-4 mt-1 text-sm text-slate-500">
                            <span><i class="fa-regular fa-envelope mr-1"></i>${user.email}</span>
                            ${user.phone ? `<span><i class="fa-solid fa-phone mr-1"></i>${user.phone}</span>` : ''}
                            <span><i class="fa-regular fa-calendar mr-1"></i>Inscrit le ${new Date(user.created_at).toLocaleDateString('fr-FR')}</span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 w-full md:w-auto justify-end flex-wrap gap-1">
                        ${user.investor_status === 'pending' ? `
                            <button onclick="approveUserInvestor(${user.id})" class="px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-emerald-500 hover:bg-emerald-600 transition-all">
                                <i class="fa-solid fa-check mr-1"></i>Approuver
                            </button>
                            <button onclick="rejectUserInvestor(${user.id})" class="px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-orange-500 hover:bg-orange-600 transition-all">
                                <i class="fa-solid fa-xmark mr-1"></i>Rejeter
                            </button>
                        ` : ''}
                        <button onclick="viewUserDetailsAdmin(${user.id}, '${user.role}')" class="px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-blue-500 hover:bg-blue-600 transition-all">
                            <i class="fa-solid fa-eye mr-1"></i>Voir
                        </button>
                        <button onclick="toggleUserStatus(${user.id}, '${user.status === 'blocked' ? 'active' : 'blocked'}')" 
                                class="px-3 py-1.5 rounded-lg text-xs font-bold text-white transition-all ${user.status === 'blocked' ? 'bg-emerald-500 hover:bg-emerald-600' : 'bg-amber-500 hover:bg-amber-600'}">
                            ${user.status === 'blocked' ? '<i class="fa-solid fa-check mr-1"></i>Débloquer' : '<i class="fa-solid fa-ban mr-1"></i>Bloquer'}
                        </button>
                        <button onclick="deleteUser(${user.id})" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-all">
                            <i class="fa-solid fa-trash mr-1"></i>Supprimer
                        </button>
                    </div>
                </div>
            `).join('');
        }

        // Toggle user active / blocked status
        async function toggleUserStatus(userId, newStatus) {
            try {
                const response = await fetch('api.php?action=update-user-status', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Admin-Password': ADMIN_PASSWORD },
                    body: JSON.stringify({ user_id: userId, status: newStatus })
                });
                const res = await response.json();
                if (res.success) {
                    showToast("Succès", res.message || "Statut mis à jour");
                    loadAdminData();
                } else {
                    showToast("Erreur", res.message, "warning");
                }
            } catch (e) {
                showToast("Erreur", "Erreur lors de la mise à jour", "warning");
            }
        }

        // Delete User
        async function deleteUser(userId) {
            if (!confirm("Voulez-vous vraiment supprimer cet utilisateur ? Cette action est irréversible.")) return;
            try {
                const response = await fetch('api.php?action=delete-user', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Admin-Password': ADMIN_PASSWORD },
                    body: JSON.stringify({ user_id: userId })
                });
                const res = await response.json();
                if (res.success) {
                    showToast("Succès", res.message || "Utilisateur supprimé");
                    loadAdminData();
                } else {
                    showToast("Erreur", res.message, "warning");
                }
            } catch (e) {
                showToast("Erreur", "Erreur de connexion", "warning");
            }
        }

        // Approve Investor (user with investor_status = pending)
        async function approveUserInvestor(userId) {
            if (!confirm("Approuver cet investisseur ?")) return;
            try {
                const response = await fetch('api.php?action=approve-investor', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Admin-Password': ADMIN_PASSWORD },
                    body: JSON.stringify({ user_id: userId })
                });
                const res = await response.json();
                if (res.success) {
                    showToast("Succès", res.message || "Investisseur approuvé");
                    loadAdminData();
                } else {
                    showToast("Erreur", res.message || "Erreur lors de l'approbation", "warning");
                }
            } catch (e) {
                showToast("Erreur", "Erreur de connexion", "warning");
            }
        }

        // Reject Investor (user with investor_status = pending)
        async function rejectUserInvestor(userId) {
            if (!confirm("Rejeter cet investisseur ? Il sera informé du refus.")) return;
            try {
                const response = await fetch('api.php?action=reject-investor', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Admin-Password': ADMIN_PASSWORD },
                    body: JSON.stringify({ user_id: userId })
                });
                const res = await response.json();
                if (res.success) {
                    showToast("Succès", res.message || "Investisseur rejeté");
                    loadAdminData();
                } else {
                    showToast("Erreur", res.message || "Erreur lors du rejet", "warning");
                }
            } catch (e) {
                showToast("Erreur", "Erreur de connexion", "warning");
            }
        }

        // View User Details for Admin
        function viewUserDetailsAdmin(userId, role) {
            // Find the user in current admin data
            const allUsers = window._adminUsersCache || [];
            const user = allUsers.find(u => u.id === userId || u.id === String(userId));
            if (!user) {
                showToast("Information", "Utilisateur #" + userId);
                return;
            }
            const roleLabel = user.role === 'admin' ? 'Administrateur' : user.role === 'promoter' ? 'Porteur de projet' : 'Investisseur';
            const message = `👤 DÉTAILS DE L'UTILISATEUR #${user.id}\n` +
                `----------------------------------------\n` +
                `• Nom : ${user.name}\n` +
                `• Email : ${user.email}\n` +
                `• Rôle : ${roleLabel}\n` +
                `• Statut : ${user.status === 'blocked' ? 'Bloqué' : 'Actif'}\n` +
                `• Statut KYC : ${user.investor_status || 'N/A'}\n` +
                `• Téléphone : ${user.phone || 'Non renseigné'}\n` +
                `• Inscrit le : ${new Date(user.created_at).toLocaleDateString('fr-FR')}\n`;
            alert(message);
        }

        // Render Admin Investments List
        function renderAdminInvestments(investments) {
            const container = document.getElementById('adminInvestmentsList');
            if (!container) return;

            if (!investments || investments.length === 0) {
                container.innerHTML = '<p class="text-slate-500 text-center py-8">Aucun investissement enregistré</p>';
                return;
            }

            container.innerHTML = investments.map(inv => `
                <div class="bg-slate-50 rounded-xl p-4 border border-slate-200 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <div class="flex items-center space-x-3">
                            <span class="font-extrabold text-brand-gold text-lg">${Number(inv.amount).toLocaleString('fr-FR')} $</span>
                            <span class="font-bold text-brand-dark">${inv.project_title || 'Projet #' + inv.project_id}</span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold ${
                                inv.status === 'confirmed' || inv.status === 'completed' ? 'bg-emerald-100 text-emerald-700' :
                                inv.status === 'cancelled' ? 'bg-red-100 text-red-700' :
                                'bg-amber-100 text-amber-700'
                            }">
                                ${inv.status === 'confirmed' || inv.status === 'completed' ? 'Confirmé' : inv.status === 'cancelled' ? 'Annulé' : 'En attente'}
                            </span>
                        </div>
                        <div class="flex items-center space-x-4 mt-1 text-sm text-slate-500">
                            <span><i class="fa-solid fa-user mr-1"></i>Investisseur : <strong>${inv.investor_name || inv.investor_email || 'Inconnu'}</strong></span>
                            <span><i class="fa-regular fa-calendar mr-1"></i>${new Date(inv.created_at).toLocaleDateString('fr-FR')}</span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 w-full md:w-auto justify-end">
                        ${inv.status !== 'confirmed' ? `
                            <button onclick="updateInvestmentStatus(${inv.id}, 'confirmed')" class="bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-all">
                                <i class="fa-solid fa-check mr-1"></i>Confirmer
                            </button>
                        ` : `
                            <button onclick="updateInvestmentStatus(${inv.id}, 'pending')" class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-all">
                                <i class="fa-solid fa-clock mr-1"></i>En attente
                            </button>
                        `}
                        <button onclick="deleteInvestmentAdmin(${inv.id})" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-all">
                            <i class="fa-solid fa-trash mr-1"></i>Supprimer
                        </button>
                    </div>
                </div>
            `).join('');
        }

        // Update Investment status in Admin
        async function updateInvestmentStatus(invId, newStatus) {
            try {
                const response = await fetch('api.php?action=update-investment-status', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Admin-Password': ADMIN_PASSWORD },
                    body: JSON.stringify({ investment_id: invId, status: newStatus })
                });
                const res = await response.json();
                if (res.success) {
                    showToast("Succès", res.message || "Statut de l'investissement mis à jour");
                    loadAdminData();
                } else {
                    showToast("Erreur", res.message, "warning");
                }
            } catch (e) {
                showToast("Erreur", "Erreur lors de la mise à jour", "warning");
            }
        }

        // Delete Investment in Admin
        async function deleteInvestmentAdmin(invId) {
            if (!confirm("Voulez-vous vraiment supprimer cet investissement ?")) return;
            try {
                const response = await fetch('api.php?action=delete-investment', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Admin-Password': ADMIN_PASSWORD },
                    body: JSON.stringify({ investment_id: invId })
                });
                const res = await response.json();
                if (res.success) {
                    showToast("Succès", res.message || "Investissement supprimé");
                    loadAdminData();
                } else {
                    showToast("Erreur", res.message, "warning");
                }
            } catch (e) {
                showToast("Erreur", "Erreur de connexion", "warning");
            }
        }

        // Render Admin Projects
        function renderAdminProjects(projects) {
            const container = document.getElementById('adminProjectsList');
            
            if (projects.length === 0) {
                container.innerHTML = '<p class="text-slate-500 text-center py-8">Aucun projet pour le moment</p>';
                return;
            }
            
            container.innerHTML = projects.map(project => `
                <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h4 class="font-bold text-brand-dark">${project.title}</h4>
                            <p class="text-sm text-slate-600 mt-1">${project.promoter} • ${project.city}, ${project.country}</p>
                            <div class="flex items-center space-x-4 mt-2 text-sm">
                                <span class="text-slate-500">Secteur: ${project.sector}</span>
                                <span class="text-slate-500">Cible: ${project.funding_sought.toLocaleString()} $</span>
                                <span class="text-slate-500">ROI: ${project.expected_roi}%</span>
                            </div>
                            <div class="mt-2 text-xs text-slate-500">
                                <span class="font-semibold">Description:</span> ${project.description ? project.description.substring(0, 100) + '...' : 'Aucune description'}
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="px-3 py-1 rounded-full text-xs font-bold ${
                                (project.raw_status || project.status) === 'approved' || project.status === 'Approuvé' ? 'bg-emerald-100 text-emerald-700' :
                                (project.raw_status || project.status) === 'pending' || (project.raw_status || project.status) === 'submitted' || project.status === 'En attente' || project.status === 'Soumis' ? 'bg-yellow-100 text-yellow-700' :
                                'bg-slate-100 text-slate-700'
                            }">${project.status}</span>

                            ${(project.raw_status || project.status) !== 'approved' && project.status !== 'Approuvé' ? `
                                <button onclick="approveProject(${project.numeric_id || project.id})" class="bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center">
                                    <i class="fa-solid fa-check mr-1"></i>Approuver
                                </button>
                                <button onclick="rejectProject(${project.numeric_id || project.id})" class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center">
                                    <i class="fa-solid fa-xmark mr-1"></i>Rejeter
                                </button>
                            ` : ''}

                            <button onclick="viewProjectDetailsAdmin('${project.id}', ${project.numeric_id || project.id})" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center">
                                <i class="fa-solid fa-eye mr-1"></i>Voir
                            </button>

                            <button onclick="deleteProjectAdmin(${project.numeric_id || project.id})" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center">
                                <i class="fa-solid fa-trash mr-1"></i>Supprimer
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        // View project details for Admin
        function viewProjectDetailsAdmin(projStringId, numericId) {
            const project = projectDatabase.find(p => p.id === projStringId || p.numeric_id === numericId || p.id === numericId);
            if (!project) {
                showToast("Information", "ID Projet #" + (numericId || projStringId));
                return;
            }
            
            const message = `📋 DÉTAILS DU PROJET #${project.numeric_id || project.id}\n` +
                `----------------------------------------\n` +
                `• Titre : ${project.title}\n` +
                `• Promoteur : ${project.promoter}\n` +
                `• Localisation : ${project.city}, ${project.country}\n` +
                `• Secteur : ${project.sector}\n` +
                `• Montant recherché : ${Number(project.funding_sought || project.target || 0).toLocaleString()} $\n` +
                `• Montant collecté : ${Number(project.funding_raised || project.raised || 0).toLocaleString()} $\n` +
                `• ROI Attendu : ${project.expected_roi || project.roi}%\n` +
                `• Statut : ${project.status}\n` +
                `----------------------------------------\n` +
                `Description :\n${project.description || 'Aucune description'}`;
            
            alert(message);
        }

        // Delete Project for Admin
        async function deleteProjectAdmin(projectId) {
            if (!confirm("Voulez-vous vraiment supprimer ce projet ? Cette action est définitive.")) return;
            try {
                const response = await fetch('api.php?action=delete-project', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Admin-Password': ADMIN_PASSWORD },
                    body: JSON.stringify({ id: projectId })
                });
                const result = await response.json();
                if (result.success) {
                    showToast("Succès", "Projet supprimé avec succès");
                    loadAdminData();
                    if (typeof loadProjectsFromAPI === 'function') loadProjectsFromAPI();
                } else {
                    showToast("Erreur", result.message, "warning");
                }
            } catch (error) {
                console.error('Error deleting project:', error);
                showToast("Erreur", "Erreur lors de la suppression du projet", "warning");
            }
        }

        // Approve Project
        async function approveProject(projectId) {
            try {
                const response = await fetch('api.php?action=approve-project', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Admin-Password': ADMIN_PASSWORD },
                    body: JSON.stringify({ id: projectId, action: 'approve' })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showToast("Succès", "Projet approuvé avec succès");
                    loadAdminData();
                } else {
                    showToast("Erreur", result.message, "warning");
                }
            } catch (error) {
                console.error('Error approving project:', error);
                showToast("Erreur", "Erreur lors de l'approbation", "warning");
            }
        }

        // Reject Project
        async function rejectProject(projectId) {
            try {
                const response = await fetch('api.php?action=approve-project', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Admin-Password': ADMIN_PASSWORD },
                    body: JSON.stringify({ id: projectId, action: 'reject' })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showToast("Succès", "Projet rejeté");
                    loadAdminData();
                } else {
                    showToast("Erreur", result.message, "warning");
                }
            } catch (error) {
                console.error('Error rejecting project:', error);
                showToast("Erreur", "Erreur lors du rejet", "warning");
            }
        }

        // Mobile Menu Toggler
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            const icon = document.getElementById('menu-icon');
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                icon.className = "fa-solid fa-xmark text-2xl";
            } else {
                menu.classList.add('hidden');
                icon.className = "fa-solid fa-bars text-2xl";
            }
        }

        // Toast Messages
        function showToast(title, body, type = 'success') {
            const toast = document.getElementById('toastMessage');
            const toastTitle = document.getElementById('toastTitle');
            const toastBody = document.getElementById('toastBody');
            const toastIcon = document.getElementById('toastIcon');

            toastTitle.innerText = title;
            toastBody.innerText = body;

            if (type === 'success') {
                toastIcon.className = "fa-solid fa-circle-check text-brand-success text-xl";
            } else {
                toastIcon.className = "fa-solid fa-triangle-exclamation text-brand-gold text-xl";
            }

            toast.classList.remove('translate-y-20', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');

            setTimeout(() => {
                toast.classList.add('translate-y-20', 'opacity-0');
                toast.classList.remove('translate-y-0', 'opacity-100');
            }, 4000);
        }

        // Marketplace Render Logic
        function renderMarketplace(projects) {
            const container = document.getElementById('marketplaceGrid');
            const counter = document.getElementById('resultsCount');

            if (!container) return;
            container.innerHTML = '';

            counter.innerText = `${projects.length} opportunité(s) disponible(s)`;

            if (projects.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-16 bg-slate-50 rounded-2xl border border-dashed border-slate-300 col-span-2">
                        <i class="fa-solid fa-plus-circle text-4xl text-brand-gold mb-3"></i>
                        <h4 class="font-bold text-brand-dark">Soyez le premier à publier un projet</h4>
                        <p class="text-xs text-slate-400 mt-1">Soumettez votre projet immobilier pour le faire découvrir par notre communauté d'investisseurs</p>
                        <button onclick="goToPromoterDashboard()" class="mt-4 bg-brand-gold hover:bg-yellow-500 text-brand-dark font-bold py-2 px-6 rounded-xl text-sm transition-all">
                            <i class="fa-solid fa-rocket mr-2"></i>Soumettre un projet
                        </button>
                    </div>
                `;
                return;
            }

            projects.forEach(p => {
                const card = document.createElement('div');
                card.className = "bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all flex flex-col";

                // Translate status
                const statusTranslations = {
                    'published': 'Publié',
                    'approved': 'Approuvé',
                    'pending': 'En attente',
                    'rejected': 'Rejeté',
                    'draft': 'Brouillon'
                };
                const translatedStatus = statusTranslations[p.status] || p.status;
                
                // Translate project type
                const typeTranslations = {
                    'residential': 'Résidentiel',
                    'commercial': 'Commercial',
                    'mixed': 'Mixte',
                    'industrial': 'Industriel',
                    'hotel': 'Hôtel',
                    'office': 'Bureau',
                    'subdivision': 'Lotissement'
                };
                const translatedType = typeTranslations[p.project_type] || p.project_type || 'N/A';
                
                // Translate operation type
                const operationTranslations = {
                    'sale': 'Vente',
                    'rental': 'Location',
                    'fundraising': 'Levée de fonds',
                    'sale_fundraising': 'Vente + Levée de fonds'
                };
                const translatedOperation = operationTranslations[p.operation_type] || p.operation_type || 'N/A';

                card.innerHTML = `
                    <!-- Image -->
                    <div class="relative h-48 overflow-hidden">
                        <img src="${p.image}" alt="${p.title}" class="w-full h-full object-cover">
                        <div class="absolute top-4 left-4 flex space-x-2">
                            <span class="bg-brand-navy/90 backdrop-blur-md text-brand-gold text-xs font-bold px-3 py-1 rounded-full">
                                ${translatedType}
                            </span>
                            <span class="bg-brand-gold/90 backdrop-blur-md text-brand-dark text-xs font-bold px-3 py-1 rounded-full">
                                ${translatedOperation}
                            </span>
                        </div>
                        <span class="absolute top-4 right-4 bg-emerald-50 text-emerald-800 text-xs font-bold px-2.5 py-1 rounded-full">
                            ${translatedStatus}
                        </span>
                    </div>

                    <!-- Details -->
                    <div class="p-6 flex-grow flex flex-col">
                        <div>
                            <h3 class="text-lg font-extrabold text-brand-dark mb-2">${p.title}</h3>
                            <p class="text-sm text-slate-500 mb-4"><i class="fa-solid fa-location-dot text-brand-gold mr-1.5"></i>${p.location}</p>
                            
                            ${p.coordinates && p.coordinates.lat ? `
                                <p class="text-xs text-slate-400 mb-4"><i class="fa-solid fa-map-pin text-brand-gold mr-1"></i>GPS: ${p.coordinates.lat.toFixed(4)}, ${p.coordinates.lng.toFixed(4)}</p>
                            ` : ''}
                            
                            <!-- Financial Progress info -->
                            <div class="mt-4 space-y-2">
                                <div class="flex justify-between text-xs font-bold text-slate-600">
                                    <span>Recherché: ${p.target.toLocaleString()} $</span>
                                    <span>Mobilisé: ${p.raised.toLocaleString()} $</span>
                                </div>
                                <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                                    <div class="bg-brand-gold h-full" style="width: ${p.progress}%"></div>
                                </div>
                                <div class="flex justify-between text-[11px] text-slate-400">
                                    <span>Progression: ${p.progress}%</span>
                                    <span>ROI attendu: <strong class="text-brand-success font-bold">${p.roi}%</strong></span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-6 grid grid-cols-2 gap-2">
                            <button onclick="openReservationModal('${p.id}', '${p.title.replace(/'/g, "\\'")}')" class="bg-brand-navy hover:bg-slate-800 text-white font-bold py-2.5 px-4 rounded-xl text-xs transition-all shadow-sm">
                                <i class="fa-solid fa-phone mr-1"></i> Réserver
                            </button>
                            <button onclick="openVisitModal('${p.id}', '${p.title.replace(/'/g, "\\'")}')" class="bg-brand-gold hover:bg-yellow-500 text-brand-dark font-bold py-2.5 px-4 rounded-xl text-xs transition-all shadow-sm">
                                <i class="fa-solid fa-calendar mr-1"></i> Visiter
                            </button>
                        </div>
                    </div>
                `;
                container.appendChild(card);
            });
        }

        // Load projects from API
        function loadMarketplaceProjects() {
            const container = document.getElementById('marketplaceGrid');
            const counter = document.getElementById('resultsCount');
            
            if (container) {
                container.innerHTML = '<div class="col-span-2 text-center py-8"><i class="fa-solid fa-spinner fa-spin text-2xl text-brand-gold"></i><p class="text-slate-500 mt-2">Chargement des projets...</p></div>';
            }

            fetch('api.php?action=get-projects')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        projectDatabase = data.data; // Store for filtering
                        renderMarketplace(data.data);
                    } else {
                        if (container) {
                            container.innerHTML = '<div class="col-span-2 text-center py-8 text-red-500">Erreur lors du chargement des projets</div>';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading projects:', error);
                    if (container) {
                        container.innerHTML = `
                            <div class="text-center py-16 bg-slate-50 rounded-2xl border border-dashed border-slate-300 col-span-2">
                                <i class="fa-solid fa-plus-circle text-4xl text-brand-gold mb-3"></i>
                                <h4 class="font-bold text-brand-dark">Soyez le premier à publier un projet</h4>
                                <p class="text-xs text-slate-400 mt-1">Soumettez votre projet immobilier pour le faire découvrir par notre communauté d'investisseurs</p>
                                <button onclick="switchTab('levee')" class="mt-4 bg-brand-gold hover:bg-yellow-500 text-brand-dark font-bold py-2 px-6 rounded-xl text-sm transition-all">
                                    <i class="fa-solid fa-rocket mr-2"></i>Soumettre un projet
                                </button>
                            </div>
                        `;
                    }
                });
        }

        // Apply filters
        function applyMarketplaceFilters() {
            const country = document.getElementById('filterCountry').value;
            const city = document.getElementById('filterCity').value.toLowerCase();
            const projectType = document.getElementById('filterProjectType').value;
            const operationType = document.getElementById('filterOperationType').value;
            const sector = document.getElementById('filterSector').value.toLowerCase();
            const roi = parseInt(document.getElementById('filterROI').value);

            const filtered = projectDatabase.filter(p => {
                if (country && p.country !== country) return false;
                if (city && !p.city.toLowerCase().includes(city)) return false;
                if (projectType && p.project_type !== projectType) return false;
                if (operationType && p.operation_type !== operationType) return false;
                if (sector && !p.sector.toLowerCase().includes(sector)) return false;
                if (p.roi < roi) return false;
                return true;
            });

            renderMarketplace(filtered);
            showToast("Filtres appliqués", `${filtered.length} opportunité(s) filtrée(s).`);
        }

        // Reset filters
        function resetMarketplaceFilters() {
            document.getElementById('filterCountry').value = '';
            document.getElementById('filterCity').value = '';
            document.getElementById('filterProjectType').value = '';
            document.getElementById('filterOperationType').value = '';
            document.getElementById('filterSector').value = '';
            document.getElementById('filterROI').value = 0;
            document.getElementById('roiVal').innerText = '0% +';
            
            renderMarketplace(projectDatabase);
            showToast("Filtres réinitialisés", "Tous les projets sont affichés.");
        }

        // Open reservation modal
        function openReservationModal(projectId, projectTitle) {
            const modalHtml = `
                <div id="reservationModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                    <div class="bg-white rounded-2xl max-w-md w-full p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-bold text-brand-dark">📞 Réservation</h3>
                            <button onclick="document.getElementById('reservationModal').remove()" class="text-slate-400 hover:text-slate-600">
                                <i class="fa-solid fa-times text-xl"></i>
                            </button>
                        </div>
                        <p class="text-sm text-slate-600 mb-4">Projet: <strong>${projectTitle}</strong></p>
                        <form onsubmit="submitReservation(event, '${projectId}')">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Type de propriété</label>
                                    <select id="resPropertyType" class="w-full border border-slate-300 rounded-lg p-2" required>
                                        <option value="purchase">Achat</option>
                                        <option value="rent">Location</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Nom complet *</label>
                                    <input type="text" id="resName" class="w-full border border-slate-300 rounded-lg p-2" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Email *</label>
                                    <input type="email" id="resEmail" class="w-full border border-slate-300 rounded-lg p-2" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Téléphone *</label>
                                    <input type="tel" id="resPhone" class="w-full border border-slate-300 rounded-lg p-2" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Budget (USD)</label>
                                    <input type="number" id="resBudget" class="w-full border border-slate-300 rounded-lg p-2" placeholder="Ex: 50000">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Message</label>
                                    <textarea id="resMessage" class="w-full border border-slate-300 rounded-lg p-2" rows="2"></textarea>
                                </div>
                                <button type="submit" class="w-full bg-brand-navy hover:bg-slate-800 text-white font-bold py-3 rounded-xl">Envoyer la réservation</button>
                            </div>
                        </form>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', modalHtml);
        }

        // Submit reservation
        function submitReservation(event, projectId) {
            event.preventDefault();
            
            const formData = {
                project_id: projectId,
                name: document.getElementById('resName').value,
                email: document.getElementById('resEmail').value,
                phone: document.getElementById('resPhone').value,
                property_type: document.getElementById('resPropertyType').value,
                budget: document.getElementById('resBudget').value,
                message: document.getElementById('resMessage').value
            };

            fetch('api.php?action=make-reservation', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast("Succès", "Réservation envoyée avec succès !");
                    document.getElementById('reservationModal').remove();
                } else {
                    showToast("Erreur", data.message);
                }
            })
            .catch(error => {
                showToast("Erreur", "Erreur de connexion");
            });
        }

        // Open visit modal
        function openVisitModal(projectId, projectTitle) {
            const modalHtml = `
                <div id="visitModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                    <div class="bg-white rounded-2xl max-w-md w-full p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-bold text-brand-dark">🗓 Demande de visite</h3>
                            <button onclick="document.getElementById('visitModal').remove()" class="text-slate-400 hover:text-slate-600">
                                <i class="fa-solid fa-times text-xl"></i>
                            </button>
                        </div>
                        <p class="text-sm text-slate-600 mb-4">Projet: <strong>${projectTitle}</strong></p>
                        <form onsubmit="submitVisit(event, '${projectId}')">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Nom complet *</label>
                                    <input type="text" id="visitName" class="w-full border border-slate-300 rounded-lg p-2" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Email *</label>
                                    <input type="email" id="visitEmail" class="w-full border border-slate-300 rounded-lg p-2" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Téléphone *</label>
                                    <input type="tel" id="visitPhone" class="w-full border border-slate-300 rounded-lg p-2" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Date préférée *</label>
                                    <input type="date" id="visitDate" class="w-full border border-slate-300 rounded-lg p-2" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Heure préférée</label>
                                    <input type="time" id="visitTime" class="w-full border border-slate-300 rounded-lg p-2">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Message</label>
                                    <textarea id="visitMessage" class="w-full border border-slate-300 rounded-lg p-2" rows="2"></textarea>
                                </div>
                                <button type="submit" class="w-full bg-brand-gold hover:bg-yellow-500 text-brand-dark font-bold py-3 rounded-xl">Envoyer la demande</button>
                            </div>
                        </form>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', modalHtml);
        }

        // Submit visit request
        function submitVisit(event, projectId) {
            event.preventDefault();
            
            const formData = {
                project_id: projectId,
                name: document.getElementById('visitName').value,
                email: document.getElementById('visitEmail').value,
                phone: document.getElementById('visitPhone').value,
                preferred_date: document.getElementById('visitDate').value,
                preferred_time: document.getElementById('visitTime').value,
                message: document.getElementById('visitMessage').value
            };

            fetch('api.php?action=request-visit', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast("Succès", "Demande de visite envoyée avec succès !");
                    document.getElementById('visitModal').remove();
                } else {
                    showToast("Erreur", data.message);
                }
            })
            .catch(error => {
                showToast("Erreur", "Erreur de connexion");
            });
        }

        // Reset Filters
        function resetMarketplaceFilters() {
            document.getElementById('filterForm').reset();
            document.getElementById('roiVal').innerText = "0% +";
            renderMarketplace(projectDatabase);
            showToast("Réinitialisation", "Tous les filtres de recherche ont été remis à zéro.");
        }

        // Click to Investigate project
        function investigateProject(id) {
            // Directing the user to the safe space Data Room inside Espace Investisseur
            showToast("Accès au dossier", "Veuillez vous identifier et signer l'accord pour accéder à la Data Room.");
            setInvestorTab('dataroom');
            switchTab('investisseur');
        }

        // Wizard Form Step Navigation
        function navigateWizard(direction) {
            const currentForm = document.getElementById(`formStep-${wizardStep}`);

            // Basic validation check
            if (direction === 1) {
                if (wizardStep === 1) {
                    const name = document.getElementById('projName').value;
                    const owner = document.getElementById('projOwner').value;
                    const loc = document.getElementById('projLoc').value;
                    if (!name || !owner || !loc) {
                        showToast("Champs requis", "Veuillez remplir tous les champs obligatoires (*)", "warning");
                        return;
                    }
                }
                if (wizardStep === 2) {
                    const cost = document.getElementById('projCost').value;
                    const target = document.getElementById('projTarget').value;
                    if (!cost || !target) {
                        showToast("Données financières", "Veuillez renseigner le coût et le financement cible.", "warning");
                        return;
                    }
                }
            }

            // Update steps
            currentForm.classList.add('hidden');
            wizardStep += direction;

            // Cap limits
            if (wizardStep > 4) {
                // Final submit triggers simulated action
                submitFundraisingProject();
                return;
            }

            const nextForm = document.getElementById(`formStep-${wizardStep}`);
            nextForm.classList.remove('hidden');

            // Update indicators and headers visually
            updateStepIndicators();
        }

        function updateStepIndicators() {
            // Manage Previous button visibility
            const prevBtn = document.getElementById('prevStepBtn');
            if (wizardStep > 1) {
                prevBtn.classList.remove('invisible');
            } else {
                prevBtn.classList.add('invisible');
            }

            // Manage Next button text
            const nextBtn = document.getElementById('nextStepBtn');
            if (wizardStep === 4) {
                nextBtn.innerText = "Finaliser la soumission";
                nextBtn.className = "bg-brand-success hover:bg-emerald-600 text-white font-bold px-8 py-3.5 rounded-xl text-sm transition-all shadow-md";
            } else {
                nextBtn.innerText = "Suivant";
                nextBtn.className = "bg-brand-navy hover:bg-slate-800 text-white font-bold px-8 py-3.5 rounded-xl text-sm transition-all shadow-md";
            }

            // Render Circle markers color
            for (let i = 1; i <= 4; i++) {
                const indicator = document.getElementById(`stepIndicator-${i}`);
                const text = document.getElementById(`stepText-${i}`);

                if (i === wizardStep) {
                    indicator.className = "w-8 h-8 rounded-full bg-brand-gold text-brand-dark flex items-center justify-center font-bold text-sm";
                    if (text) text.className = "hidden md:inline text-xs font-bold uppercase tracking-wider text-brand-gold";
                } else if (i < wizardStep) {
                    indicator.className = "w-8 h-8 rounded-full bg-brand-success text-white flex items-center justify-center font-bold text-sm";
                    if (text) text.className = "hidden md:inline text-xs font-bold uppercase tracking-wider text-brand-success";
                } else {
                    indicator.className = "w-8 h-8 rounded-full bg-slate-800 text-slate-400 border border-slate-700 flex items-center justify-center font-bold text-sm";
                    if (text) text.className = "hidden md:inline text-xs font-bold uppercase tracking-wider text-slate-400";
                }
            }
        }

        // Submits fundraising form and pushes to database dynamically!
        async function submitFundraisingProject() {
            const name = document.getElementById('projName').value;
            const owner = document.getElementById('projOwner').value;
            const loc = document.getElementById('projLoc').value;
            const sector = document.getElementById('projSector').value;
            const target = parseInt(document.getElementById('projTarget').value);
            const roi = parseInt(document.getElementById('projRoi').value) || 20;

            const formData = new FormData();
            formData.append('name', name);
            formData.append('owner', owner);
            formData.append('location', loc);
            formData.append('sector', sector);
            formData.append('target', target);
            formData.append('roi', roi);

            const businessPlanInput = document.getElementById('businessPlanInput');
            const pitchDeckInput = document.getElementById('pitchDeckInput');
            const financialModelInput = document.getElementById('financialModelInput');
            const description = document.getElementById('projDesc').value;

            formData.append('description', description);

            if (businessPlanInput && businessPlanInput.files.length > 0) {
                formData.append('business_plan', businessPlanInput.files[0]);
            }
            if (pitchDeckInput && pitchDeckInput.files.length > 0) {
                formData.append('pitch_deck', pitchDeckInput.files[0]);
            }
            if (financialModelInput && financialModelInput.files.length > 0) {
                formData.append('financial_model', financialModelInput.files[0]);
            }

            try {
                const response = await fetch('api.php?action=submit-project', {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    showToast("Projet Soumis", "Félicitations! Votre projet a bien été enregistré pour validation administrative.");
                    
                    // Reload projects from API
                    await loadProjectsFromAPI();

                    // Reset wizard
                    document.getElementById('submissionWizard').reset();
                    wizardStep = 1;
                    document.getElementById('formStep-4').classList.add('hidden');
                    document.getElementById('formStep-1').classList.remove('hidden');
                    updateStepIndicators();

                    // Redirect to marketplace to let user preview
                    switchTab('marketplace');
                } else {
                    showToast("Erreur", result.message || "Erreur lors de la soumission du projet", "warning");
                }
            } catch (error) {
                console.error('Error submitting project:', error);
                showToast("Erreur", "Erreur de connexion au serveur", "warning");
            }
        }

        // Investor Dashboard tab switcher
        function setInvestorTab(tab) {
            // Hide sub tabs
            const subs = document.querySelectorAll('.invest-sub-content');
            subs.forEach(el => el.classList.add('hidden'));

            // Show target
            document.getElementById(`invest-sub-${tab}`).classList.remove('hidden');

            // Manage nav buttons styling inside Investor Panel
            const buttons = [
                { id: 'dashboard', el: document.getElementById('invest-tab-dashboard') },
                { id: 'investissements', el: document.getElementById('invest-tab-investissements') },
                { id: 'dataroom', el: document.getElementById('invest-tab-dataroom') }
            ];

            buttons.forEach(btn => {
                if (btn.id === tab) {
                    btn.el.className = "w-full text-left px-4 py-3 rounded-xl text-sm font-bold flex items-center space-x-3 bg-brand-gold/15 text-brand-dark";
                } else {
                    btn.el.className = "w-full text-left px-4 py-3 rounded-xl text-sm font-medium flex items-center space-x-3 text-slate-600 hover:bg-slate-50";
                }
            });
        }

        // Unlock Data Room secure system
        function unlockDataRoom() {
            const consent = document.getElementById('ndaConsent').checked;
            if (!consent) {
                showToast("NDA Obligatoire", "Vous devez approuver l'accord de non-divulgation (NDA) pour continuer.", "warning");
                return;
            }

            isDataRoomUnlocked = true;
            document.getElementById('dataroom-locked-state').classList.add('hidden');
            document.getElementById('dataroom-unlocked-state').classList.remove('hidden');
            showToast("Accès déverrouillé", "Data Room débloquée. Les documents confidentiels sont désormais accessibles.");
        }

        // Submit Contact Form
        async function submitContactForm() {
            const name = document.getElementById('contactName').value;
            const email = document.getElementById('contactEmail').value;
            const phone = document.getElementById('contactPhone').value;
            const subject = document.getElementById('contactSubject').value;
            const message = document.getElementById('contactMessage').value;

            const contactData = {
                name: name,
                email: email,
                phone: phone,
                subject: subject,
                message: message
            };

            try {
                const response = await fetch('api.php?action=submit-contact', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(contactData)
                });

                const result = await response.json();

                if (result.success) {
                    showToast("Message Envoyé !", `Merci ${name}, notre service de support étudie votre demande concernant "${subject}".`);
                    document.getElementById('contactForm').reset();
                } else {
                    showToast("Erreur", result.message || "Erreur lors de l'envoi du message", "warning");
                }
            } catch (error) {
                console.error('Error submitting contact:', error);
                showToast("Erreur", "Erreur de connexion au serveur", "warning");
            }
        }

        // Open investment modal
        function openInvestModal(projectId, projectTitle, target, raised) {
            if (!currentUser || currentUser.role !== 'investor') {
                showToast("Accès réservé", "Vous devez être connecté en tant qu'investisseur pour investir", "warning");
                openLoginModal();
                return;
            }

            const remaining = target - raised;
            const modalHtml = `
                <div id="investModal" class="fixed inset-0 z-50">
                    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeInvestModal()"></div>
                    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl shadow-2xl w-full max-w-md p-8">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-extrabold text-brand-dark">Investir dans ${projectTitle}</h2>
                            <button onclick="closeInvestModal()" class="text-slate-400 hover:text-slate-600">
                                <i class="fa-solid fa-xmark text-xl"></i>
                            </button>
                        </div>
                        
                        <div class="mb-6 p-4 bg-slate-50 rounded-xl">
                            <p class="text-sm text-slate-600 mb-2">Financement recherché: ${target.toLocaleString()} $</p>
                            <p class="text-sm text-slate-600 mb-2">Déjà mobilisé: ${raised.toLocaleString()} $</p>
                            <p class="text-sm font-bold text-brand-gold">Reste à mobiliser: ${remaining.toLocaleString()} $</p>
                        </div>
                        
                        <form id="investForm" class="space-y-4" onsubmit="event.preventDefault(); handleInvestment(${projectId});">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Montant à investir ($)</label>
                                <input type="number" id="investAmount" min="1" max="${remaining}" placeholder="Montant"
                                    class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-brand-gold focus:outline-none"
                                    required>
                            </div>
                            <button type="submit"
                                class="w-full bg-brand-gold hover:bg-yellow-500 text-brand-dark font-bold py-3 rounded-xl text-sm transition-all shadow-md">
                                Confirmer l'investissement
                            </button>
                        </form>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', modalHtml);
        }

        // Close investment modal
        function closeInvestModal() {
            const modal = document.getElementById('investModal');
            if (modal) {
                modal.remove();
            }
        }

        // Handle investment
        async function handleInvestment(projectId) {
            const amount = document.getElementById('investAmount').value;
            
            if (!amount || amount <= 0) {
                showToast("Erreur", "Veuillez entrer un montant valide", "warning");
                return;
            }

            try {
                const response = await fetch('api.php?action=invest-project', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ project_id: projectId, amount: amount })
                });

                const result = await response.json();

                if (result.success) {
                    showToast("Investissement réussi !", "Votre investissement a été enregistré avec succès");
                    closeInvestModal();
                    await loadProjectsFromAPI();
                    if (currentUser && currentUser.role === 'investor') {
                        await loadInvestorData();
                    }
                } else {
                    showToast("Erreur", result.message, "warning");
                }
            } catch (error) {
                console.error('Investment error:', error);
                showToast("Erreur", "Erreur de connexion au serveur", "warning");
            }
        }

        // Modals d'authentification (Connexion & Inscription)
        function openLoginModal() {
            closeAuthModal();
            const modalHtml = `
                <div id="authModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeAuthModal()"></div>
                    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 z-10">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-extrabold text-brand-dark">Connexion</h2>
                            <button onclick="closeAuthModal()" class="text-slate-400 hover:text-slate-600">
                                <i class="fa-solid fa-xmark text-xl"></i>
                            </button>
                        </div>
                        <form onsubmit="handleLoginSubmit(event)" class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Adresse Email</label>
                                <input type="email" id="loginEmail" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-brand-gold focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Mot de passe</label>
                                <input type="password" id="loginPassword" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-brand-gold focus:outline-none">
                            </div>
                            <button type="submit" class="w-full bg-brand-gold hover:bg-yellow-500 text-brand-dark font-bold py-3.5 rounded-xl text-sm transition-all shadow-md">
                                Se connecter
                            </button>
                            <p class="text-center text-xs text-slate-500 mt-4">
                                Pas encore de compte ? <a href="javascript:void(0)" onclick="openRegisterModal()" class="text-brand-gold font-bold hover:underline">S'inscrire</a>
                            </p>
                        </form>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', modalHtml);
        }

        function openRegisterModal() {
            closeAuthModal();
            const modalHtml = `
                <div id="authModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeAuthModal()"></div>
                    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg p-8 z-10 max-h-[90vh] overflow-y-auto">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-extrabold text-brand-dark">Créer un compte</h2>
                            <button onclick="closeAuthModal()" class="text-slate-400 hover:text-slate-600">
                                <i class="fa-solid fa-xmark text-xl"></i>
                            </button>
                        </div>
                        <form onsubmit="handleRegisterSubmit(event)" class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Prénom *</label>
                                    <input type="text" id="regFirstName" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm focus:ring-2 focus:ring-brand-gold focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Nom *</label>
                                    <input type="text" id="regLastName" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm focus:ring-2 focus:ring-brand-gold focus:outline-none">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Email *</label>
                                <input type="email" id="regEmail" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm focus:ring-2 focus:ring-brand-gold focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Mot de passe (min. 8 caractères) *</label>
                                <input type="password" id="regPassword" required minlength="8" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm focus:ring-2 focus:ring-brand-gold focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Type de profil *</label>
                                <select id="regRole" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm focus:ring-2 focus:ring-brand-gold focus:outline-none">
                                    <option value="investor">Investisseur</option>
                                    <option value="promoter">Porteur de projet</option>
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Pays</label>
                                    <input type="text" id="regCountry" placeholder="RDC" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm focus:ring-2 focus:ring-brand-gold focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Ville</label>
                                    <input type="text" id="regCity" placeholder="Kinshasa" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm focus:ring-2 focus:ring-brand-gold focus:outline-none">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Téléphone</label>
                                <input type="tel" id="regPhone" placeholder="+243..." class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm focus:ring-2 focus:ring-brand-gold focus:outline-none">
                            </div>
                            <button type="submit" class="w-full bg-brand-gold hover:bg-yellow-500 text-brand-dark font-bold py-3.5 rounded-xl text-sm transition-all shadow-md mt-2">
                                Valider l'inscription
                            </button>
                            <p class="text-center text-xs text-slate-500 mt-4">
                                Déjà inscrit ? <a href="javascript:void(0)" onclick="openLoginModal()" class="text-brand-gold font-bold hover:underline">Se connecter</a>
                            </p>
                        </form>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', modalHtml);
        }

        function closeAuthModal() {
            const modal = document.getElementById('authModal');
            if (modal) modal.remove();
        }

        async function handleLoginSubmit(e) {
            e.preventDefault();
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;

            try {
                const res = await fetch('api.php?action=login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, password })
                });
                const data = await res.json();
                if (data.success) {
                    closeAuthModal();
                    alert("✅ CONNEXION RÉUSSIE !\n\nBienvenue, " + (data.user.name || data.user.email) + " !");
                    showToast("Connexion réussie", "Bienvenue " + (data.user.name || data.user.email));
                    setTimeout(() => location.reload(), 800);
                } else {
                    alert("❌ ÉCHEC DE CONNEXION\n\n" + (data.message || "Email ou mot de passe incorrect."));
                    showToast("Erreur", data.message || "Identifiants incorrects", "warning");
                }
            } catch (err) {
                alert("⚠️ ERREUR DE CONNEXION AU SERVEUR");
                showToast("Erreur", "Erreur de connexion au serveur", "warning");
            }
        }

        async function handleRegisterSubmit(e) {
            e.preventDefault();
            const firstName = document.getElementById('regFirstName').value;
            const lastName = document.getElementById('regLastName').value;
            const email = document.getElementById('regEmail').value;
            const password = document.getElementById('regPassword').value;
            const role = document.getElementById('regRole').value;
            const country = document.getElementById('regCountry').value;
            const city = document.getElementById('regCity').value;
            const phone = document.getElementById('regPhone').value;

            try {
                const res = await fetch('api.php?action=register', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        name: `${firstName} ${lastName}`.trim(),
                        first_name: firstName,
                        last_name: lastName,
                        email: email,
                        password: password,
                        role: role,
                        country: country,
                        city: city,
                        phone: phone
                    })
                });
                const data = await res.json();
                if (data.success) {
                    closeAuthModal();
                    alert("🎉 INSCRIPTION RÉUSSIE !\n\nVotre compte a été créé et activé avec succès.\nVous êtes désormais connecté !");
                    showToast("Inscription réussie !", "Votre compte a été créé avec succès.");
                    setTimeout(() => location.reload(), 1000);
                } else {
                    alert("⚠️ ÉCHEC DE L'INSCRIPTION\n\n" + (data.message || "Impossible de créer le compte."));
                    showToast("Erreur d'inscription", data.message || "Impossible de vous inscrire", "warning");
                }
            } catch (err) {
                alert("⚠️ ERREUR DE CONNEXION AU SERVEUR");
                showToast("Erreur", "Erreur de connexion au serveur", "warning");
            }
        }
    </script>
</body>

</html>
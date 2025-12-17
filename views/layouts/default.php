<!DOCTYPE html>
<html lang="pt-BR" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> - <?= $title ?? 'Sistema' ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f8fafc;
            min-height: 100vh;
        }

        .wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Modern Header */
        .main-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.15);
            position: sticky;
            top: 0;
            z-index: 1000;
            backdrop-filter: blur(10px);
        }

        .main-header .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 32px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }

        .logo a {
            color: white !important;
            text-decoration: none !important;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 0;
            transition: opacity 0.3s;
        }

        .logo a:hover {
            opacity: 0.9;
        }

        .logo-icon {
            width: 32px;
            height: 32px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
        }

        /* Navigation */
        .main-nav {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .main-nav a {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
            white-space: nowrap;
            position: relative;
        }

        .main-nav a:hover {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            transform: translateY(-1px);
        }

        .main-nav a.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        /* User Info */
        .user-section {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 16px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            margin-left: 8px;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 14px;
        }

        .user-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .user-name {
            color: white;
            font-size: 14px;
            font-weight: 600;
            line-height: 1;
        }

        .user-role {
            color: rgba(255, 255, 255, 0.7);
            font-size: 12px;
            line-height: 1;
        }

        /* Dropdown Menu */
        .user-menu {
            position: relative;
        }

        .user-menu-toggle {
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
            display: flex;
            align-items: center;
        }

        .user-menu-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            padding: 8px;
            min-width: 200px;
            display: none;
            animation: slideDown 0.2s ease;
        }

        .user-menu:hover .user-menu-dropdown {
            display: block;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .user-menu-dropdown a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            color: #334155;
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.2s;
        }

        .user-menu-dropdown a:hover {
            background: #f1f5f9;
            color: #667eea;
            transform: none;
        }

        .user-menu-dropdown .divider {
            height: 1px;
            background: #e2e8f0;
            margin: 8px 0;
        }

        .menu-icon {
            width: 18px;
            height: 18px;
            stroke-width: 2;
        }

        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 8px;
            border-radius: 8px;
            cursor: pointer;
            color: white;
        }

        @media (max-width: 1024px) {
            .main-nav {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                flex-direction: column;
                padding: 16px;
                gap: 8px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            }

            .main-nav.active {
                display: flex;
            }

            .main-nav a {
                width: 100%;
                padding: 12px 16px;
            }

            .mobile-menu-toggle {
                display: block;
            }

            .user-section {
                margin-left: 0;
            }
        }

        @media (max-width: 768px) {
            .main-header .container {
                padding: 0 16px;
            }

            .logo h1 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Modern Header -->
        <header class="main-header">
            <div class="container">
                <div class="logo">
                    <a href="<?= BASE_URL ?>/relatorios/index">
                        <div class="logo-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                        </div>
                        <h1><?= APP_NAME ?></h1>
                    </a>
                </div>

                <?php if (Session::isValid()): ?>
                    <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </button>

                    <nav class="main-nav" id="mainNav">
                        <a href="<?= BASE_URL ?>/relatorios/index">
                            <svg class="menu-icon" style="display:inline-block; vertical-align:middle; margin-right:4px; width:16px; height:16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="7" height="7"></rect>
                                <rect x="14" y="3" width="7" height="7"></rect>
                                <rect x="14" y="14" width="7" height="7"></rect>
                                <rect x="3" y="14" width="7" height="7"></rect>
                            </svg>
                            Dashboard
                        </a>
                        <a href="<?= BASE_URL ?>/clientes/index">
                            <svg class="menu-icon" style="display:inline-block; vertical-align:middle; margin-right:4px; width:16px; height:16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            Clientes
                        </a>
                        <a href="<?= BASE_URL ?>/questionarios/index">
                            <svg class="menu-icon" style="display:inline-block; vertical-align:middle; margin-right:4px; width:16px; height:16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <polyline points="10 9 9 9 8 9"></polyline>
                            </svg>
                            Questionários
                        </a>
                        <a href="<?= BASE_URL ?>/questionarios/proximosAtendimentos">
                            <svg class="menu-icon" style="display:inline-block; vertical-align:middle; margin-right:4px; width:16px; height:16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            Atendimentos
                        </a>
                        <a href="<?= BASE_URL ?>/relatorios/atendimentos">
                            <svg class="menu-icon" style="display:inline-block; vertical-align:middle; margin-right:4px; width:16px; height:16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="20" x2="18" y2="10"></line>
                                <line x1="12" y1="20" x2="12" y2="4"></line>
                                <line x1="6" y1="20" x2="6" y2="14"></line>
                            </svg>
                            Relatórios
                        </a>
                        <a href="<?= BASE_URL ?>/xml/index">
                            <svg class="menu-icon" style="display:inline-block; vertical-align:middle; margin-right:4px; width:16px; height:16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                            </svg>
                            Correção XML
                        </a>
                        <a href="<?= BASE_URL ?>/usuarios/visualizar">
                            <svg class="menu-icon" style="display:inline-block; vertical-align:middle; margin-right:4px; width:16px; height:16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="8.5" cy="7" r="4"></circle>
                                <line x1="20" y1="8" x2="20" y2="14"></line>
                                <line x1="23" y1="11" x2="17" y2="11"></line>
                            </svg>
                            Usuários
                        </a>

                        <div class="user-menu">
                            <button class="user-menu-toggle">
                                <div class="user-section">
                                    <div class="user-avatar">
                                        <?= strtoupper(substr(Session::read('Questionarios.nm_usu'), 0, 1)) ?>
                                    </div>
                                    <div class="user-info">
                                        <span class="user-name"><?= Session::read('Questionarios.nm_usu') ?></span>
                                        <span class="user-role">Administrador</span>
                                    </div>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </div>
                            </button>
                            <div class="user-menu-dropdown">
                                <a href="<?= BASE_URL ?>/usuarios/changePassword">
                                    <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                    </svg>
                                    Trocar Senha
                                </a>
                                <?php if (Session::read('Questionarios.cd_usu') == 1): ?>
                                <a href="<?= BASE_URL ?>/usuarios/adiciona_database">
                                    <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                                        <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                                        <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                                    </svg>
                                    Adicionar Database
                                </a>
                                <?php endif; ?>
                                <div class="divider"></div>
                                <a href="<?= BASE_URL ?>/usuarios/logout">
                                    <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                        <polyline points="16 17 21 12 16 7"></polyline>
                                        <line x1="21" y1="12" x2="9" y2="12"></line>
                                    </svg>
                                    Sair
                                </a>
                            </div>
                        </div>
                    </nav>
                <?php endif; ?>
            </div>
        </header>

        <script>
            function toggleMobileMenu() {
                document.getElementById('mainNav').classList.toggle('active');
            }
        </script>
        
        <!-- Flash Messages -->
        <?php $flash = Session::flash(); ?>
        <?php if ($flash): ?>
            <div class="flash-message flash-<?= $flash['type'] ?>">
                <?= $flash['message'] ?>
            </div>
        <?php endif; ?>
        
        <!-- Content -->
        <main class="main-content">
            <div class="container">
                <?= $content ?>
            </div>
        </main>
        
        <!-- Footer -->
        <footer class="main-footer">
            <div class="container">
                <p>&copy; <?= date('Y') ?> <?= APP_NAME ?> - Versão <?= APP_VERSION ?></p>
            </div>
        </footer>
    </div>
    
    <script src="<?= BASE_URL ?>/public/js/main.js"></script>
</body>
</html>

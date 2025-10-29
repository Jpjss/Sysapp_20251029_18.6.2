<!DOCTYPE html>
<html lang="pt-BR" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> - <?= $title ?? 'Sistema' ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .theme-toggle {
            background: none;
            border: 1px solid #334155;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            color: #e2e8f0;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .theme-toggle:hover {
            background: rgba(99, 102, 241, 0.15);
            border-color: #6366f1;
        }
        .theme-icon {
            width: 18px;
            height: 18px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Header -->
        <header class="main-header">
            <div class="container">
                <div class="logo">
                    <h1><a href="<?= BASE_URL ?>/relatorios/index" style="color: white; text-decoration: none;"><?= APP_NAME ?></a></h1>
                </div>
                <nav class="main-nav">
                    <?php if (Session::isValid()): ?>
                        <a href="<?= BASE_URL ?>/relatorios/index">Dashboard</a>
                        <a href="<?= BASE_URL ?>/clientes/index">Clientes</a>
                        <a href="<?= BASE_URL ?>/questionarios/index">Questionários</a>
                        <a href="<?= BASE_URL ?>/questionarios/proximosAtendimentos">Atendimentos</a>
                        <a href="<?= BASE_URL ?>/relatorios/atendimentos">Relatórios</a>
                        <button class="theme-toggle" id="themeToggle" title="Alternar tema">
                            <svg class="theme-icon" id="themeIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                            </svg>
                        </button>
                        <span class="user-info">
                            Olá, <?= Session::read('Questionarios.nm_usu') ?>
                        </span>
                        <a href="<?= BASE_URL ?>/usuarios/changePassword">Trocar Senha</a>
                        <a href="<?= BASE_URL ?>/usuarios/logout">Sair</a>
                    <?php endif; ?>
                </nav>
            </div>
        </header>
        
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

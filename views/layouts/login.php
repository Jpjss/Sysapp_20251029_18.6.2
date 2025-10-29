<!DOCTYPE html>
<html lang="pt-BR" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> - Login</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/login.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .theme-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(42, 42, 58, 0.8);
            border: 1px solid #444;
            padding: 10px;
            border-radius: 50%;
            cursor: pointer;
            color: #e2e8f0;
            transition: all 0.3s;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
        }
        .theme-toggle:hover {
            background: rgba(99, 102, 241, 0.3);
            border-color: #6366f1;
        }
        .theme-icon {
            width: 20px;
            height: 20px;
        }
    </style>
</head>
<body>
    <button class="theme-toggle" id="themeToggle" title="Alternar tema">
        <svg class="theme-icon" id="themeIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
        </svg>
    </button>
    <?= $content ?>
    <script>
        // Theme toggle para a página de login
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
        const html = document.documentElement;
        
        // Carregar tema salvo
        const savedTheme = localStorage.getItem('theme') || 'dark';
        html.setAttribute('data-theme', savedTheme);
        updateThemeIcon(savedTheme);
        
        themeToggle.addEventListener('click', () => {
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateThemeIcon(newTheme);
        });
        
        function updateThemeIcon(theme) {
            if (theme === 'dark') {
                themeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>';
            } else {
                themeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>';
            }
        }
    </script>
</body>
</html>

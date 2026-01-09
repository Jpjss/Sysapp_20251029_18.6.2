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
    <?= $content ?>
</body>
</html>

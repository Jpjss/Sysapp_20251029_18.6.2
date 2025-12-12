<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Teste de Login</h1>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h2>✅ POST Recebido!</h2>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
} else {
    echo "<h2>Formulário de Teste</h2>";
    ?>
    <form method="POST" action="">
        <label>Usuário:</label><br>
        <input type="text" name="email" value="admin" required><br><br>
        
        <label>Senha:</label><br>
        <input type="password" name="senha" value="123456" required><br><br>
        
        <button type="submit">Testar Login</button>
    </form>
    <?php
}
?>

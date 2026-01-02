<?php
// mapping_review.php
// Página simples para revisar/editar mapping_diaazze.json via browser.

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json = $_POST['mapping_json'] ?? '';
    if ($json) {
        file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'mapping_diaazze.json', $json);
        $msg = 'Mapping salvo.';
    }
}

$mapPath = __DIR__ . DIRECTORY_SEPARATOR . 'mapping_diaazze.json';
$content = file_exists($mapPath) ? file_get_contents($mapPath) : '{}';
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Revisão de Mapping Diaazze</title></head><body>
<h2>Revisão de Mapping Diaazze</h2>
<?php if (!empty($msg)) echo "<p style='color:green'>{$msg}</p>"; ?>
<form method="post">
    <textarea name="mapping_json" style="width:100%;height:70vh"><?=htmlspecialchars($content)?></textarea>
    <br>
    <button type="submit">Salvar</button>
    <button type="button" onclick="location.reload()">Recarregar</button>
</form>
<p>Salve para aplicar mudanças. Arquivo salvo em <b>mapping_diaazze.json</b>.</p>
</body></html>

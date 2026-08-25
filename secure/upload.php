<?php
/* misma app, pero validando antes de guardar. tres cosas:
- solo dejo pasar extensiones de imagen (lista blanca)
- reviso el contenido real del archivo, no solo el nombre
- el nombre final lo genero yo, no uso el que manda el usuario
*/

$extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif'];
$mimesPermitidos       = ['image/jpeg', 'image/png', 'image/gif'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo'])) {
    $nombreOriginal = $_FILES['archivo']['name'];
    $tmp            = $_FILES['archivo']['tmp_name'];
    $extension      = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

    // extensión contra la lista blanca
    if (!in_array($extension, $extensionesPermitidas, true)) {
        http_response_code(400);
        die("Rechazado: extensión '$extension' no permitida.");
    }

    // ahora el contenido real (por si renombraron el archivo para engañar la extensión)
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeReal = finfo_file($finfo, $tmp);
    finfo_close($finfo);

    if (!in_array($mimeReal, $mimesPermitidos, true)) {
        http_response_code(400);
        die("Rechazado: el contenido del archivo es '$mimeReal', no coincide con una imagen real.");
    }

    // nombre nuevo, generado acá, no el que vino en el request
    $nuevoNombre = bin2hex(random_bytes(8)) . '.' . $extension;
    $destino     = __DIR__ . '/uploads/' . $nuevoNombre;

    if (move_uploaded_file($tmp, $destino)) {
        echo "Archivo subido correctamente.";
        echo "Guardado como: uploads/$nuevoNombre";
    } else {
        echo "Error al subir el archivo.";
    }
} else {
    echo "No se recibió ningún archivo.";
}

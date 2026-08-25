# Instructivo — Demo CWE-434

Armamos dos versiones de la misma funcionalidad, "subir foto de perfil": una vulnerable y otra con las mitigaciones aplicadas. La idea es mostrar el ataque funcionando en la primera y después mostrar que la segunda lo rechaza.

Todo se prueba desde el navegador. Lo único que hace falta escribir en una terminal es el comando para levantar el servidor (uno por cada versión), porque PHP necesita eso para arrancar. El resto — subir el archivo, ejecutar comandos, ver que se rechaza — se hace todo con el mouse en el navegador.

Requisito: tener PHP instalado (lo probé con PHP 8.4, pero cualquier versión 7.4 en adelante anda igual). No usé Docker ni nada especial.

## Parte 1 — explotando la versión vulnerable

Abro una terminal solo para levantar el servidor:

```bash
cd vulnerable
php -S 127.0.0.1:8001
```

Eso se deja corriendo. Ahora todo es en el navegador:

1. Entro a `http://127.0.0.1:8001`. Aparece el formulario para subir una foto.
2. Con el botón "Seleccionar archivo" elijo `payload/shell.php` (el "webshell") como si fuera cualquier imagen, y le doy a Subir.
3. La app lo acepta sin preguntar nada y me dice dónde quedó guardado: `uploads/shell.php`.
4. Entro a `http://127.0.0.1:8001/uploads/shell.php`. Ahí aparece un cuadro de texto con un botón "Ejecutar" — eso es el webshell.
5. Escribo `id` en el cuadro y hago clic en Ejecutar. Aparece el resultado del comando corriendo en el servidor (`uid=0(root)...`).

Ese último paso es la consecuencia principal de la vulnerabilidad: subir un archivo alcanzó para terminar corriendo comandos en el servidor, como si tuviera una terminal ahí adentro.

Corto el servidor con Ctrl+C en la terminal cuando termino.

## Parte 2 — la versión que sí valida

Levanto el otro servidor (otra vez, un solo comando):

```bash
cd ../secure
php -S 127.0.0.1:8002
```

Y de nuevo, todo lo demás en el navegador:

1. Entro a `http://127.0.0.1:8002`.
2. Intento subir `payload/shell.php` tal cual, igual que antes. Esta vez la app lo rechaza: "extensión 'php' no permitida".
3. Ahora la parte más interesante: le cambio el nombre al archivo (en el explorador de archivos, lo copio y lo renombro a `foto.jpg`) e intento subir ese archivo renombrado. Tampoco lo deja pasar: "el contenido del archivo no coincide con una imagen real". Acá no alcanza con cambiarle el nombre, porque la app revisa el contenido de verdad, no solo cómo se llama.
4. Para cerrar, subo una imagen real (cualquier .jpg o .png que tenga a mano). Esta vez sí la acepta, y la guarda con un nombre nuevo generado por el servidor, no el que yo mandé.

## Qué mitigación evita cada cosa

- La lista blanca de extensiones evita subir directamente algo como `.php`, `.phtml`, `.asp`.
- Revisar el contenido real del archivo evita el truco de cambiarle el nombre a `.jpg` para pasar el primer filtro.
- Generar el nombre del lado del servidor evita que el atacante controle cómo termina llamándose el archivo.
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Contrato</title>
    <style>
        /* Estilo general del cuerpo */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        /* Contenedor del contenido principal para evitar desplazamiento */
        .content {
            margin: 20px auto;
            padding: 20px;
            max-width: 800px; /* Ajusta el ancho máximo del contenido */
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        /* Estilo del encabezado */
        h1 {
            color: #343a40;
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
        }

        /* Estilo del formulario */
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        /* Estilo del input file */
        input[type="file"] {
            border: 1px solid #ced4da;
            border-radius: 5px;
            padding: 10px;
            background-color: #f8f9fa;
            cursor: pointer;
        }

        /* Estilo del botón de envío */
        button {
            background-color: #007bff;
            color: #ffffff;
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        /* Estilo del mensaje */
        p {
            color: #dc3545;
            text-align: center;
            font-size: 14px;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<!-- Contenedor del contenido principal -->
<div class="content">
    <h1>Subir Contrato</h1>

    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="idCompra" value="<?php echo htmlspecialchars($_GET['id']); ?>">
        <input type="file" name="contrato" accept="application/pdf">
        <button type="submit">Subir Contrato</button>
    </form>

    <!-- Mostrar mensaje si hay uno -->
    <?php if (isset($_GET['mensaje'])): ?>
        <p><?php echo htmlspecialchars($_GET['mensaje']); ?></p>
    <?php endif; ?>
</div>

</body>
</html>

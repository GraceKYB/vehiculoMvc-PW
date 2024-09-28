<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú de Navegación</title>
    <style>
        /* Estilos CSS para el menú de navegación */
        nav {
            background-color: #333;
            padding: 10px 0;
        }

        nav ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            text-align: center;
        }

        nav ul li {
            display: inline;
            margin-right: 20px;
        }

        nav ul li a {
            color: #fff;
            text-decoration: none;
            padding: 10px 15px;
            transition: background-color 0.3s ease;
        }

        nav ul li a:hover {
            background-color: #555;
            border-radius: 5px;
        }

        /* Estilos CSS para el contenedor de la imagen */
        .image-container {
            text-align: center;
            margin-top: 20px;
        }

        .image-container img {
            width: 100%;
            height: auto;
            max-height: 400px; /* Limita la altura máxima de la imagen */
            object-fit: cover; /* Asegura que la imagen cubra el contenedor */
        }
    </style>
</head>
<body>

<nav>
    <ul>
        <li><a href="index.php?controller=Cliente&action=index">Lista usuario</a></li>
        <li><a href="index.php?controller=Cliente&action=search">Busqueda por ID</a></li>
        <li><a href="index.php?controller=Cliente&action=create">Registrar usuario</a></li>
        <li><a href="index.php?controller=Cliente&action=searchByPlaca">Busqueda por Placa</a></li>
        <li><a href="index.php?controller=Vehiculo&action=index">Lista Vehículos</a></li>
        <li><a href="index.php?controller=Vehiculo&action=create">Registrar Vehiculo</a></li>
        <li><a href="index.php?controller=Compra&action=verDetalleCompra">Ver Detalle</a></li>
    </ul>
</nav>

<?php if (isset($isHomePage) && $isHomePage): ?>
<div class="image-container">
    <img src="portada.jpg" alt="Descripción de la imagen">
</div>
<?php endif; ?>
</body>
</html>

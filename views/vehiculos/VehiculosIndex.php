<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Vehículos</title>
</head>
<body>
    <h1>Lista de Vehículos</h1>

    <?php if (!empty($vehiculos)): ?>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Modelo</th>
                <th>Marca</th>
                <th>Color</th>
                <th>Año</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Imágenes</th>
                <th>Acciones</th>
            </tr>
            <?php foreach ($vehiculos as $vehiculo): ?>
                <tr>
                    <td><?php echo $vehiculo->id_vehiculo; ?></td>
                    <td><?php echo $vehiculo->nom_vehiculo; ?></td>
                    <td><?php echo $vehiculo->mod_vehiculo; ?></td>
                    <td><?php echo $vehiculo->mar_vehiculo; ?></td>
                    <td><?php echo $vehiculo->col_vehiculo; ?></td>
                    <td><?php echo $vehiculo->anio_vehiculo; ?></td>
                    <td><?php echo $vehiculo->pre_vehiculo; ?></td>
                    <td><?php echo $vehiculo->stock; ?></td>
                    <td>
                        <?php if (!empty($vehiculo->rutas_imagenes)): ?>
                            <?php foreach ($vehiculo->rutas_imagenes as $ruta): ?>
                                <img src="<?php echo $ruta; ?>" alt="Imagen del vehículo" width="100">
                            <?php endforeach; ?>
                        <?php else: ?>
                            No hay imágenes.
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="index.php?controller=Vehiculo&action=edit&id=<?php echo $vehiculo->id_vehiculo; ?>">Editar</a>
                        <a href="index.php?controller=Vehiculo&action=delete&id=<?php echo $vehiculo->id_vehiculo; ?>">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No hay vehículos disponibles.</p>
    <?php endif; ?>
</body>
</html>

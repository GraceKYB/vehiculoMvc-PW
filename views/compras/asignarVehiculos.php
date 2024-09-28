<!DOCTYPE html>
<html>
<head>
    <title>Asignar Vehículos</title>
    <link rel="stylesheet" type="text/css" href="public/css/asignarVeh.css">
    <script>
        function validateInput(input) {
            if (input.value < 0) {
                input.value = 0;
            }
        }
    </script>
</head>
<body>
    <header>
        <h1>Asignar Vehículos</h1>
    </header>
    <div class="container">
        <form action="index.php?controller=Compra&action=asignarVehiculos&cliente_id=<?= $clienteId ?>" method="POST">
            <table class="assign-table">
                <thead>
                    <tr>
                        <th>Seleccionar</th>
                        <th>Nombre Vehículo</th>
                        <th>Modelo</th>
                        <th>Color</th>
                        <th>Marca</th>
                        <th>Año</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Imágenes</th>
                        <th>Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vehiculos as $vehiculo): ?>
                        <tr>
                            <td><input type="checkbox" name="vehiculos[<?= $vehiculo->id_vehiculo ?>][id]" value="<?= $vehiculo->id_vehiculo ?>"></td>
                            <td><?= htmlspecialchars($vehiculo->nom_vehiculo) ?></td>
                            <td><?= htmlspecialchars($vehiculo->mod_vehiculo) ?></td>
                            <td><?= htmlspecialchars($vehiculo->col_vehiculo) ?></td>
                            <td><?= htmlspecialchars($vehiculo->mar_vehiculo) ?></td>
                            <td><?= htmlspecialchars($vehiculo->anio_vehiculo) ?></td>
                            <td><?= htmlspecialchars($vehiculo->pre_vehiculo) ?></td>
                            <td><?= htmlspecialchars($vehiculo->stock) ?></td>
                            <td>
                                <?php if (!empty($vehiculo->rutas_imagenes)): ?>
                                    <?php foreach ($vehiculo->rutas_imagenes as $imagen): ?>
                                        <img src="<?= htmlspecialchars($imagen) ?>" alt="Imagen del vehículo <?= htmlspecialchars($vehiculo->nom_vehiculo) ?>" class="vehicle-image">
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    No hay imágenes disponibles
                                <?php endif; ?>
                            </td>
                            <td><input type="number" name="vehiculos[<?= $vehiculo->id_vehiculo ?>][cantidad]" min="0" max="<?= htmlspecialchars($vehiculo->stock) ?>" value="0"></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button type="submit">Continuar</button>
        </form>
    </div>
</body>
</html>

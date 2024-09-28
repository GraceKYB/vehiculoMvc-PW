<!DOCTYPE html>
<html>
<head>
    <title>Asignar Placas</title>
    <link rel="stylesheet" type="text/css" href="public/css/asignarPlacas.css">
</head>
<body>
    <header>
        <h1>Asignar Placas</h1>
    </header>
    <div class="container">
        <form action="index.php?controller=Compra&action=guardarPlacas" method="POST">
            <table class="assign-table">
                <thead>
                    <tr>
                        <th>Nombre Vehículo</th>
                        <th>Modelo</th>
                        <th>Color</th>
                        <th>Marca</th>
                        <th>Stock</th>
                        <th>Placas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vehiculos as $vehiculo): ?>
                        <?php if ($_POST['vehiculos'][$vehiculo->id_vehiculo]['cantidad'] > 0): ?>
                            <tr>
                                <td><?= htmlspecialchars($vehiculo->nom_vehiculo) ?></td>
                                <td><?= htmlspecialchars($vehiculo->mod_vehiculo) ?></td>
                                <td><?= htmlspecialchars($vehiculo->col_vehiculo) ?></td>
                                <td><?= htmlspecialchars($vehiculo->mar_vehiculo) ?></td>
                                <td><?= htmlspecialchars($vehiculo->stock) ?></td>
                                
                                <td>
                                    <?php for ($i = 0; $i < $_POST['vehiculos'][$vehiculo->id_vehiculo]['cantidad']; $i++): ?>
                                        <input type="text" name="placas[<?= $vehiculo->id_vehiculo ?>][]" placeholder="Ingrese la placa" class="input-placa">
                                    <?php endfor; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <input type="hidden" name="cliente_id" value="<?= $clienteId ?>">
            <button type="submit">Guardar</button>
        </form>
    </div>
</body>
</html>

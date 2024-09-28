<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Vehículos Asignados</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #333;
            color: white;
        }
        td {
            background-color: #f2f2f2;
        }
        img {
            max-width: 100px; /* Ajusta el tamaño máximo de la imagen */
            height: auto;
        }
    </style>
</head>
<body>

<?php
// Eliminar duplicados basados en la placa del vehículo
$uniqueVehiculos = [];
foreach ($vehiculos as $vehiculo) {
    if (!isset($uniqueVehiculos[$vehiculo->placa])) {
        $uniqueVehiculos[$vehiculo->placa] = $vehiculo;
    }
}
?>

<?php if (!empty($uniqueVehiculos)): ?>
    <h2>Vehículos Asignados para la Cédula: <?php echo htmlspecialchars($cedula); ?></h2>
    <table>
        <tr>
            <th>Placa</th>
            <th>Marca</th>
            <th>Modelo</th>
            <th>Color</th>
            <th>Año</th>
            <th>Nombre</th>
            <th>Foto</th>
        </tr>
        <?php foreach ($uniqueVehiculos as $vehiculo): ?>
            <tr>
                <td><?php echo htmlspecialchars($vehiculo->placa); ?></td>
                <td><?php echo htmlspecialchars($vehiculo->mar_vehiculo); ?></td>
                <td><?php echo htmlspecialchars($vehiculo->mod_vehiculo); ?></td>
                <td><?php echo htmlspecialchars($vehiculo->col_vehiculo); ?></td>
                <td><?php echo htmlspecialchars($vehiculo->anio_vehiculo); ?></td>
                <td><?php echo htmlspecialchars($vehiculo->nom_vehiculo); ?></td>
                <td><img src="<?php echo htmlspecialchars($vehiculo->ruta_img_veh); ?>" alt="Imagen del vehículo"></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>No se encontraron vehículos asignados para la cédula <?php echo htmlspecialchars($cedula); ?></p>
<?php endif; ?>

</body>
</html>

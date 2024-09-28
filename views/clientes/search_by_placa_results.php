<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resultados de Búsqueda por Placa</title>
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
            max-width: 100px;
            height: auto;
        }
    </style>
</head>
<body>

<?php if (!empty($data)): ?>
    <h2>Resultados para la Placa: <?php echo htmlspecialchars($placa); ?></h2>
    <h3>Datos del Cliente</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Cédula</th>
            <th>Correo</th>
            <th>Edad</th>
            <th>Dirección</th>
            <th>Estado</th>
        </tr>
        <tr>
            <td><?php echo htmlspecialchars($data['cliente']['id_cliente']); ?></td>
            <td><?php echo htmlspecialchars($data['cliente']['nombre']); ?></td>
            <td><?php echo htmlspecialchars($data['cliente']['apellido']); ?></td>
            <td><?php echo htmlspecialchars($data['cliente']['cedula']); ?></td>
            <td><?php echo htmlspecialchars($data['cliente']['correo']); ?></td>
            <td><?php echo htmlspecialchars($data['cliente']['edad']); ?></td>
            <td><?php echo htmlspecialchars($data['cliente']['direccion']); ?></td>
            <td><?php echo htmlspecialchars($data['cliente']['estado']); ?></td>
        </tr>
    </table>

    <h3>Datos del Vehículo</h3>
    <table>
        <tr>
            <th>ID Vehículo</th>
            <th>Nombre</th>
            <th>Modelo</th>
            <th>Marca</th>
            <th>Color</th>
            <th>Año</th>
            <th>Precio</th>
            <th>Stock</th>
            <th>Placa</th>
            <th>Imagen</th>
        </tr>
        <tr>
            <td><?php echo htmlspecialchars($data['vehiculo']['id_vehiculo']); ?></td>
            <td><?php echo htmlspecialchars($data['vehiculo']['nom_vehiculo']); ?></td>
            <td><?php echo htmlspecialchars($data['vehiculo']['mod_vehiculo']); ?></td>
            <td><?php echo htmlspecialchars($data['vehiculo']['mar_vehiculo']); ?></td>
            <td><?php echo htmlspecialchars($data['vehiculo']['col_vehiculo']); ?></td>
            <td><?php echo htmlspecialchars($data['vehiculo']['anio_vehiculo']); ?></td>
            <td><?php echo htmlspecialchars($data['vehiculo']['pre_vehiculo']); ?></td>
            <td><?php echo htmlspecialchars($data['vehiculo']['stock']); ?></td>
            <td><?php echo htmlspecialchars($data['vehiculo']['placa']); ?></td>
            <td><img src="<?php echo htmlspecialchars($data['vehiculo']['ruta_img_veh']); ?>" alt="Imagen del vehículo"></td>
        </tr>
    </table>
<?php else: ?>
    <p>No se encontraron datos para la placa <?php echo htmlspecialchars($placa); ?></p>
<?php endif; ?>

</body>
</html>

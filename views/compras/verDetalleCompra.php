<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facturas de Compra</title>
    <link rel="stylesheet" href="public/css/verDetalleCom.css">
</head>
<body>

<h1>Facturas de Compra</h1>

<?php if (isset($data) && !empty($data)): ?>
    <?php foreach ($data as $factura): ?>
        <h2>Factura ID: <?php echo htmlspecialchars($factura['idCompra']); ?></h2>

        <table>
            <thead>
                <tr>
                    <th>Fecha Compra</th>
                    <th>Nombre Cliente</th>
                    <th>Apellido Cliente</th>
                    <th>Cédula Cliente</th>
                    <th>Nombre Vehículo</th>
                    <th>Modelo Vehículo</th>
                    <th>Marca Vehículo</th>
                    <th>Precio Vehículo</th>
                    <th>Placa Vehículo</th>
                    <th>Imagen Vehículo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($factura['vehiculos'] as $vehiculo): ?>
                    <?php foreach ($vehiculo['placas'] as $detalle): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($factura['fechaCompra']); ?></td>
                            <td><?php echo htmlspecialchars($factura['nombreCliente']); ?></td>
                            <td><?php echo htmlspecialchars($factura['apellidoCliente']); ?></td>
                            <td><?php echo htmlspecialchars($factura['cedulaCliente']); ?></td>
                            <td><?php echo htmlspecialchars($vehiculo['nombreVehiculo']); ?></td>
                            <td><?php echo htmlspecialchars($vehiculo['modeloVehiculo']); ?></td>
                            <td><?php echo htmlspecialchars($vehiculo['marcaVehiculo']); ?></td>
                            <td><?php echo htmlspecialchars($vehiculo['precioVehiculo']); ?></td>
                            <td><?php echo htmlspecialchars($detalle['placa']); ?></td>
                            <td><img src="<?php echo htmlspecialchars($vehiculo['imagenVehiculo']); ?>" alt="Imagen Vehículo"></td>
                            <td>
                                <a href="edit.php?id=<?php echo htmlspecialchars($factura['idCompra']); ?>&placa=<?php echo htmlspecialchars($detalle['placa']); ?>" class="btn-edit">Editar</a>
                                <a href="delete.php?id=<?php echo htmlspecialchars($factura['idCompra']); ?>&detalle=<?php echo htmlspecialchars($detalle['idCompDet']); ?>" class="btn-delete">Eliminar</a>
                                <a href="index.php?controller=Compra&action=verContrato&id=<?= $factura['idCompra'] ?>" class=" btn-view">Ver Contrato</a>
                                <a href="index.php?controller=Contrato&action=subirContrato&id=<?= $factura['idCompra'] ?>" class=" btn-upload">Subir Contrato</a> |
                                <a href="index.php?controller=Contrato&action=mostrarContrato&id=<?= $factura['idCompra'] ?>" class=" btn-show">Mostrar Contrato</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endforeach; ?>
<?php else: ?>
    <p>No se encontraron facturas.</p>
<?php endif; ?>

</body>
</html>

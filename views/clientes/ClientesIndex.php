<!DOCTYPE html>
<html>
<head>
    <title>Lista de Clientes</title>
    <link rel="stylesheet" type="text/css" href="public/css/listaCliente.css">
</head>
<body>
    <header>
        <h1>Lista de Clientes</h1>
    </header>
    <div class="container">
        <table class="client-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Cédula</th>
                    <th>Correo</th>
                    <th>Edad</th>
                    <th>Dirección</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientes as $cliente): ?>
                <tr>
                    <td><?= $cliente->nombre ?></td>
                    <td><?= $cliente->apellido ?></td>
                    <td><?= $cliente->cedula ?></td>
                    <td><?= $cliente->correo ?></td>
                    <td><?= $cliente->edad ?></td>
                    <td><?= $cliente->direccion ?></td>
                    <td><?= $cliente->estado ?></td>
                    <td>
                        <a href="index.php?controller=Cliente&action=edit&id=<?= $cliente->id_cliente ?>">Editar</a>

                        <a href="index.php?controller=Cliente&action=delete&id_cliente=<?= $cliente->id_cliente ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar este cliente?')">Eliminar</a>
                        
                        <a href="index.php?controller=Compra&action=asignarVehiculos&cliente_id=<?= $cliente->id_cliente ?>">Asignar Vehículos</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div>
            <a href="index.php" class="menu-link">Regresar</a>
        </div>
    </div>
</body>
</html>

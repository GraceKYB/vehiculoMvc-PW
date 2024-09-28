<!DOCTYPE html>
<html>
<head>
    <title>Editar Cliente</title>
    <link rel="stylesheet" type="text/css" href="public/css/editaCliente.css">
</head>
<body>
    <header>
        <h1>Editar Cliente</h1>
    </header>
    <div class="form-container">
        <form action="index.php?controller=Cliente&action=edit&id=<?= $cliente->id_cliente ?>" method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" value="<?= $cliente->nombre ?>" required>
                </div>
                <div class="form-group">
                    <label for="apellido">Apellido:</label>
                    <input type="text" id="apellido" name="apellido" value="<?= $cliente->apellido ?>" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="cedula">Cédula:</label>
                    <input type="text" id="cedula" name="cedula" value="<?= $cliente->cedula ?>" required>
                </div>
                <div class="form-group">
                    <label for="correo">Correo:</label>
                    <input type="email" id="correo" name="correo" value="<?= $cliente->correo ?>" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="edad">Edad:</label>
                    <input type="number" id="edad" name="edad" value="<?= $cliente->edad ?>" required>
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección:</label>
                    <input type="text" id="direccion" name="direccion" value="<?= $cliente->direccion ?>" required>
                </div>
            </div>
            <button type="submit">Actualizar Cliente</button>
        </form>
    </div>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <title>Registrar Cliente</title>
    <link rel="stylesheet" type="text/css" href="public/css/registroCliente.css">
</head>
<body>
    <header>
        <h1>Registrar Cliente</h1>
    </header>
    <div class="form-container">
        <form action="index.php?controller=Cliente&action=create" method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                <div class="form-group">
                    <label for="apellido">Apellido:</label>
                    <input type="text" id="apellido" name="apellido" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="cedula">Cédula:</label>
                    <input type="text" id="cedula" name="cedula" required>
                </div>
                <div class="form-group">
                    <label for="correo">Correo:</label>
                    <input type="email" id="correo" name="correo" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="edad">Edad:</label>
                    <input type="number" id="edad" name="edad" required>
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección:</label>
                    <input type="text" id="direccion" name="direccion" required>
                </div>
            </div>
            <button type="submit">Registrar</button>
        </form>
        <div>
            <a href="index.php" class="menu-link">Regresar</a>
        </div>
    </div>
</body>
</html>

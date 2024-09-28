<!DOCTYPE html>
<html>
<head>
    <title>Registrar Vehículo</title>
    <link rel="stylesheet" type="text/css" href="public/css/registroVeh.css">
   
</head>
<body>
    <header>
        <h1>Registrar Vehículo</h1>
    </header>
    <div class="form-container">
        <form action="index.php?controller=Vehiculo&action=create" method="POST" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group">
                    <label for="nom_vehiculo">Nombre:</label>
                    <input type="text" id="nom_vehiculo" name="nom_vehiculo" required>
                </div>
                <div class="form-group">
                    <label for="mod_vehiculo">Modelo:</label>
                    <input type="text" id="mod_vehiculo" name="mod_vehiculo" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="mar_vehiculo">Marca:</label>
                    <input type="text" id="mar_vehiculo" name="mar_vehiculo" required>
                </div>
                <div class="form-group">
                    <label for="col_vehiculo">Color:</label>
                    <input type="text" id="col_vehiculo" name="col_vehiculo" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="anio_vehiculo">Año:</label>
                    <input type="number" id="anio_vehiculo" name="anio_vehiculo" required>
                </div>
                <div class="form-group">
                    <label for="pre_vehiculo">Precio:</label>
                    <input type="number" id="pre_vehiculo" name="pre_vehiculo" step="0.01" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="stock">Stock:</label>
                    <input type="number" id="stock" name="stock" required>
                </div>
                <div class="form-group">
                    <label for="imagenes">Imágenes:</label>
                    <input type="file" id="imagenes" name="imagenes[]" accept="image/*" multiple required>
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

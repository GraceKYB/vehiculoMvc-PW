<!DOCTYPE html>
<html>
<head>
    <title>Editar Vehículo</title>
    <link rel="stylesheet" type="text/css" href="public/css/editVehiculo.css">
</head>
<body>
    <header>
        <h1>Editar Vehículo</h1>
    </header>
    <div class="form-container">
        <form action="index.php?controller=Vehiculo&action=update" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_vehiculo" value="<?= htmlspecialchars($vehiculo->id_vehiculo) ?>">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="nom_vehiculo">Nombre:</label>
                    <input type="text" id="nom_vehiculo" name="nom_vehiculo" value="<?= htmlspecialchars($vehiculo->nom_vehiculo) ?>" required>
                </div>
                <div class="form-group">
                    <label for="mod_vehiculo">Modelo:</label>
                    <input type="text" id="mod_vehiculo" name="mod_vehiculo" value="<?= htmlspecialchars($vehiculo->mod_vehiculo) ?>" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="mar_vehiculo">Marca:</label>
                    <input type="text" id="mar_vehiculo" name="mar_vehiculo" value="<?= htmlspecialchars($vehiculo->mar_vehiculo) ?>" required>
                </div>
                <div class="form-group">
                    <label for="col_vehiculo">Color:</label>
                    <input type="text" id="col_vehiculo" name="col_vehiculo" value="<?= htmlspecialchars($vehiculo->col_vehiculo) ?>" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="anio_vehiculo">Año:</label>
                    <input type="number" id="anio_vehiculo" name="anio_vehiculo" value="<?= htmlspecialchars($vehiculo->anio_vehiculo) ?>" required>
                </div>
                <div class="form-group">
                    <label for="pre_vehiculo">Precio:</label>
                    <input type="number" id="pre_vehiculo" name="pre_vehiculo" value="<?= htmlspecialchars($vehiculo->pre_vehiculo) ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="stock">Stock:</label>
                    <input type="number" id="stock" name="stock" value="<?= htmlspecialchars($vehiculo->stock) ?>" required>
                </div>
            </div>

            <label for="imagenes">Imágenes actuales:</label>
            <div class="current-images">
                <?php if (!empty($rutas_imagenes)): ?>
                    <?php foreach ($rutas_imagenes as $ruta_imagen): ?>
                        <div class="image-container">
                            <img src="<?= htmlspecialchars($ruta_imagen) ?>" alt="Imagen del vehículo">
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No hay imágenes disponibles</p>
                <?php endif; ?>
            </div>
            
            <label for="imagenes">Añadir nuevas imágenes:</label>
            <input type="file" id="imagenes" name="imagenes[]" multiple>
            
            <button type="submit">Actualizar Vehículo</button>
        </form>
        <a href="index.php" class="menu-link">Regresar</a>
    </div>
</body>
</html>

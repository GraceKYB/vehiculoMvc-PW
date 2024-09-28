<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Buscar por Placa</title>
    <link rel="stylesheet" type="text/css" href="public/css/busxplaca.css">
</head>

<body>
    <header>
        <h1>Buscar Cliente por Placa</h1>
    </header>
    <div class="container">
        <form action="index.php" method="GET">
            <input type="hidden" name="controller" value="Cliente">
            <input type="hidden" name="action" value="searchByPlaca">
            <label for="placa">Ingrese la Placa:</label>
            <input type="text" id="placa" name="placa">
            <input type="submit" value="Buscar">
        </form>
        <div>
            <a href="index.php" class="menu-link">Regresar</a>
        </div>
    </div>
</body>
</html>

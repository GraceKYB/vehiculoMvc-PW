<?php
// delete.php
include 'config/config.php';
require_once 'controllers/FacturasController.php';

// Crear una instancia del controlador
$controller = new FacturasController();

// Obtener el ID del detalle a eliminar
$idDetalle = isset($_GET['detalle']) ? (int)$_GET['detalle'] : 0;

// Llamar al método de eliminación y obtener el mensaje
$message = '';
if ($idDetalle > 0) {
    $message = $controller->deleteDetalle($idDetalle);
} else {
    $message = 'ID de detalle no válido';
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Eliminación</title>
    <script type="text/javascript">
        function showAlertAndRedirect(message, redirectUrl) {
            alert(message);
            window.location.href = redirectUrl;
        }
    </script>
</head>
<body onload="showAlertAndRedirect('<?php echo htmlspecialchars($message); ?>', 'index.php?controller=Compra&action=verDetalleCompra')">
</body>
</html>

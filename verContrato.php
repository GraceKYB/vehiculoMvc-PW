<?php
require_once 'config/config.php'; // Incluir el archivo de configuración
require_once 'controllers/CompraController.php'; // Incluir el archivo del controlador
require_once 'models/Compra.php'; // Incluir el archivo del modelo

// Instanciar el modelo y el controlador
$model = new Compra();
$controller = new CompraController($model);

// Verificar que se ha pasado el ID de la compra
if (isset($_GET['id'])) {
    $idCompra = intval($_GET['id']); // Asegurarse de que el ID sea un número entero
    $controller->verContrato($idCompra); // Pasar el ID de compra al método del controlador
} else {
    echo "ID de compra no proporcionado.";
}
?>

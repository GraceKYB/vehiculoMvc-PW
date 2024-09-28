<?php
session_start();
require_once 'config/config.php';

// Incluir encabezado de la página principal
require_once 'views/header.php';

// Determinar si es la página principal
$isHomePage = !isset($_GET['controller']) && !isset($_GET['action']);


// Mostrar menú
require_once 'views/menu.php';

// Determinar controlador y acción por defecto si no se proporcionan en la URL
$controller = isset($_GET['controller']) ? $_GET['controller'] : 'Cliente';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Incluir controlador basado en los parámetros recibidos
require_once 'controllers/' . ucfirst($controller) . 'Controller.php';

$controllerName = ucfirst($controller) . 'Controller';
$controllerObj = new $controllerName();

// Verificar si la acción existe en el controlador
if (method_exists($controllerObj, $action)) {
    // Ejecutar la acción del controlador
    $controllerObj->$action();
} else {
    // Mostrar un mensaje de error o redirigir a una página de error 404
    echo "Página no encontrada";
}

// Incluir pie de página
require_once 'views/footer.php';
?>

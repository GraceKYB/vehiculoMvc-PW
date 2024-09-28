<?php
require_once 'config/config.php';
require_once 'models/FacturaModel.php';

class FacturasController
{
    private $model;

    public function __construct()
    {
        // Inicializar el modelo usando el método loadModel
        $this->model = $this->loadModel('FacturaModel');
    }
  

    // Método para cargar modelos
    private function loadModel($modelName)
    {
        // Asegúrate de que el nombre del modelo es correcto y la ruta es adecuada
        require_once 'models/' . $modelName . '.php';
        return new $modelName();
    }

    public function edit()
    {
        if (isset($_GET['id']) && isset($_GET['placa'])) {
            $idCompra = $_GET['id'];
            $placa = $_GET['placa'];

            $detalle = $this->model->getDetallePorPlaca($idCompra, $placa);

            if ($detalle) {
                require_once 'views/facturas/edit.php';
            } else {
                echo "No se encontraron detalles para la placa especificada.";
            }
        } else {
            echo "ID de compra o placa no especificada.";
        }
    }

    public function update()
    {
        if (isset($_POST['idCompDet']) && isset($_POST['placa'])) {
            $idCompDet = $_POST['idCompDet'];
            $placa = $_POST['placa'];

            $this->model->updatePlaca($idCompDet, $placa);

            header('Location: index.php?controller=facturas&action=index');
        } else {
            echo "Datos incompletos para actualizar la placa.";
        }
    }

    public function deleteDetalle()
    {
        $idDetalle = isset($_GET['detalle']) ? (int)$_GET['detalle'] : 0;

        if ($idDetalle > 0) {
            // Llamar al método de eliminación lógica
            $result = $this->model->deleteDetalleYCompra($idDetalle);

            if ($result) {
                return 'Detalle y compra eliminados correctamente';
            } else {
                return 'Error al eliminar el detalle o la compra';
            }
        } else {
            return 'ID de detalle no válido';
        }
    }
}
?>

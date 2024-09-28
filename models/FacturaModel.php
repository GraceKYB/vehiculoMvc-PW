<?php
require_once 'config/config.php';
class FacturaModel
{
    private $conn;

    public function __construct()
    {
        $this->conn = connect();
    }
    


    public function getDetallePorPlaca($idCompra, $placa)
    {
        $sql = "SELECT * FROM compra_detalle WHERE idCompra = ? AND placa = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('is', $idCompra, $placa);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function updatePlaca($idCompDet, $placa)
    {
        $sql = "UPDATE compra_detalle SET placa = ? WHERE idCompDet = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('si', $placa, $idCompDet);
        $stmt->execute();
    }

    public function deleteDetalleYCompra($idDetalle)
    {
        $this->conn->begin_transaction();

        try {
            // Obtener id_comp asociado al detalle
            $sql = "SELECT id_comp FROM compra_detalle WHERE id_comp_det = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('i', $idDetalle);
            $stmt->execute();
            $stmt->bind_result($idCompra);
            $stmt->fetch();
            $stmt->close();

            // Actualizar estado del detalle
            $sql = "UPDATE compra_detalle SET estado = 'I' WHERE id_comp_det = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('i', $idDetalle);
            $stmt->execute();
            $stmt->close();

            // Actualizar estado de la compra
            $sql = "UPDATE compra SET estado = 'I' WHERE id_comp = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('i', $idCompra);
            $stmt->execute();
            $stmt->close();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }
}
?>



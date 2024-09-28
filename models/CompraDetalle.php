
<?php
require_once 'config/config.php';

class CompraDetalle {
    public static function getByCompraId($idCompra) {
        $db = connect();
        $stmt = $db->prepare("SELECT * FROM compra_detalle WHERE id_comp = ?");
        $stmt->bind_param('i', $idCompra);
        $stmt->execute();
        $result = $stmt->get_result();

        $compraDetalle = [];
        while ($row = $result->fetch_assoc()) {
            $compraDetalle[] = $row;
        }

        $stmt->close();
        return $compraDetalle;
    }
}

?>

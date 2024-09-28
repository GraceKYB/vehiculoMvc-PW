<?php
require_once 'config/config.php';

class ContratoModel
{
    private $db;

    public function __construct()
    {
        $this->db = connect();  // Usando la conexión proporcionada
    }

    public function guardarContrato($idCompra, $contratoBase64)
    {
        $stmt = $this->db->prepare("UPDATE compra SET contrato = ? WHERE id_comp = ?");
        $stmt->bind_param('si', $contratoBase64, $idCompra);
        return $stmt->execute();
    }

    public function obtenerContrato($idCompra)
    {
        $stmt = $this->db->prepare("SELECT contrato FROM compra WHERE id_comp = ?");
        $stmt->bind_param('i', $idCompra);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}
?>

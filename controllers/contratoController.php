<?php
require_once 'config/config.php';
require_once 'models/ContratoModel.php';

class ContratoController
{
    private $model;

    public function __construct()
    {
        $this->model = new ContratoModel();
    }

    public function subirContrato()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idCompra']) && isset($_FILES['contrato'])) {
            $idCompra = $_POST['idCompra'];
            $contrato = $_FILES['contrato'];

            if ($contrato['error'] == UPLOAD_ERR_OK && $contrato['type'] == 'application/pdf') {
                // Leer el contenido del archivo PDF
                $contenidoPDF = file_get_contents($contrato['tmp_name']);
                // Convertir a Base64
                $contratoBase64 = base64_encode($contenidoPDF);
                // Guardar en la base de datos
                if ($this->model->guardarContrato($idCompra, $contratoBase64)) {
                    $mensaje = "Contrato subido exitosamente.";
                } else {
                    $mensaje = "Error al subir el contrato.";
                }
            } else {
                $mensaje = "Error en la carga del archivo. Asegúrese de que es un PDF.";
            }

            // Redirigir con mensaje
            header("Location: index.php?controller=Contrato&action=subirContrato&id=$idCompra&mensaje=" . urlencode($mensaje));
            exit();
        } else {
            $idCompra = $_GET['id'] ?? null;
            $mensaje = $_GET['mensaje'] ?? null;
            require 'subirContrato.php';  // Asegúrate de que la vista es correcta
        }
    }

    public function mostrarContrato()
    {
        if (isset($_GET['id'])) {
            $idCompra = $_GET['id'];
            $contrato = $this->model->obtenerContrato($idCompra);

            if ($contrato && !empty($contrato['contrato'])) {
                $pdfContent = base64_decode($contrato['contrato']);

                if ($pdfContent === false) {
                    echo "Error al decodificar el PDF.";
                    return;
                }

                // Limpiar el buffer de salida
                if (ob_get_contents()) {
                    ob_end_clean();
                }

                // Forzar la visualización del PDF en el navegador
                header('Content-Type: application/pdf');
                header('Content-Disposition: inline; filename="contrato.pdf"');
                echo $pdfContent;
                exit();
            } else {
                echo "Contrato no encontrado.";
            }
        } else {
            echo "ID de compra no especificado.";
        }
    }
}
?>
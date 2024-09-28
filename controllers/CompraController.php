<?php
require_once 'models/Vehiculo.php';
require_once 'models/Compra.php';
require_once 'models/Cliente.php';
require_once 'models/CompraDetalle.php';
require_once 'config/config.php'; 
require_once 'lib/fpdf186/fpdf.php';

class CompraController
{
    private $vehiculoModel;
    private $clienteModel;
    private $conn;

    public function __construct() {
        $this->vehiculoModel = new Vehiculo();
        $this->clienteModel = new Cliente();
        $this->model = new Compra(); 
        $this->conn = connect();
        
        // Iniciar la sesión si no está iniciada
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function calcularCompraTotal($vehiculos) {
        $total = 0;
    
        foreach ($vehiculos as $vehiculo) {
            // Verificar si 'v_unitario' está definido y no es null
            $precio = isset($vehiculo['v_unitario']) ? $vehiculo['v_unitario'] : 0;
            $cantidad = isset($vehiculo['cantidad']) ? $vehiculo['cantidad'] : 0;
    
            $total += $precio * $cantidad;
        }
    
        return $total;
    }
    
    public function guardarPlacas() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_SESSION['compra'])) {
                echo "Error: No se encontraron datos de compra en la sesión.";
                return;
            }
    
            $clienteId = isset($_POST['cliente_id']) ? $_POST['cliente_id'] : null;
            $compraTotal = $this->calcularCompraTotal($_SESSION['compra']['vehiculos']);
    
            if ($clienteId && $compraTotal !== false) {
                $idComp = $this->crearCompra($clienteId, $compraTotal);
    
                if ($idComp) {
                    foreach ($_SESSION['compra']['vehiculos'] as $idVehiculo => $vehiculo) {
                        if (is_array($vehiculo)) {
                            $cantidad = isset($vehiculo['cantidad']) ? $vehiculo['cantidad'] : 0;
                            $precio = isset($vehiculo['v_unitario']) ? $vehiculo['v_unitario'] : 0;
                            $placas = isset($_POST['placas'][$idVehiculo]) ? $_POST['placas'][$idVehiculo] : [];
    
                            foreach ($placas as $placa) {
                                $vTotal = $precio * $cantidad;
                                $guardado = $this->guardarCompraDetalle($idComp, $idVehiculo, $placa, $precio, $cantidad, $vTotal);
    
                                if (!$guardado) {
                                    echo "Error al guardar la placa para el vehículo con ID: $idVehiculo";
                                    return;
                                }
                            }
                        } else {
                            echo "Error: Se esperaba un array para el vehículo con ID: $idVehiculo.";
                            return;
                        }
                    }
    
                    // Generar PDF
                    $pdfContent = $this->generarPDF($idComp);
    
                    // Codificar PDF en base64
                    $pdfBase64 = base64_encode($pdfContent);
    
                    // Actualizar contrato en base64
                    $this->actualizarContrato($idComp, $pdfBase64);
    
                    echo "Placas guardadas correctamente. Contrato creado.";
                    header('Location: index.php?controller=Cliente&action=index&success=true');
                    exit;
                } else {
                    echo "Error al crear la compra.";
                }
            } else {
                echo "Error: Cliente no especificado o total de compra no válido.";
            }
        } else {
            echo "No se recibió el formulario correctamente.";
        }
    }
    
    private function guardarContrato($idCompra, $pdfBase64) {
        $db = $this->model->conn;
        $query = "UPDATE compra SET contrato = ? WHERE id_comp = ?";
        $stmt = $db->prepare($query);

        if ($stmt === false) {
            die('Error al preparar la consulta: ' . $db->error);
        }

        $stmt->bind_param('si', $pdfBase64, $idCompra);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "Contrato actualizado correctamente.";
        } else {
            echo "Error al actualizar el contrato.";
        }

        $stmt->close();
    }
    private function actualizarContrato($idCompra, $pdfBase64) {
        $db = $this->conn;
        $query = "UPDATE compra SET contrato = ? WHERE id_comp = ?";
        $stmt = $db->prepare($query);
        
        if ($stmt === false) {
            die('Error al preparar la consulta de actualización: ' . $db->error);
        }
    
        $stmt->bind_param('si', $pdfBase64, $idCompra);
        $stmt->execute();
        
        if ($stmt->error) {
            echo 'Error al actualizar el contrato: ' . $stmt->error;
        }
    
        $stmt->close();
    }
    private function crearCompra($clienteId, $compraTotal) {
        $db = $this->conn;
        $query = "INSERT INTO compra (id_cliente, compra_total, fecha_comp, estado, contrato) VALUES (?, ?, NOW(), 'A', '')";
        $stmt = $db->prepare($query);
    
        // Verificar si la consulta se preparó correctamente
        if ($stmt === false) {
            die('Error al preparar la consulta: ' . $db->error);
        }
    
        // Vincular los parámetros y ejecutar la consulta
        $stmt->bind_param('id', $clienteId, $compraTotal);
        $stmt->execute();
    
        // Verificar si la inserción fue exitosa
        if ($stmt->affected_rows > 0) {
            $idComp = $stmt->insert_id;
        } else {
            // Manejar errores en la inserción
            $idComp = null;
            echo "Error al crear la compra: " . $stmt->error;
        }
    
        // Cerrar la declaración
        $stmt->close();
        return $idComp;
    }

    private function guardarCompraDetalle($idCompra, $idVehiculo, $placa, $precio, $cantidad, $vTotal) {
        $db = $this->conn;
        $query = "INSERT INTO compra_detalle (id_comp, id_vehiculo, placa, v_unitario, cantidad, v_total, estado) VALUES (?, ?, ?, ?, ?, ?, 'A')";
        $stmt = $db->prepare($query);

        if ($stmt === false) {
            die('Error al preparar la consulta: ' . $db->error);
        }

        $stmt->bind_param('iisdid', $idCompra, $idVehiculo, $placa, $precio, $cantidad, $vTotal);
        $stmt->execute();

        $guardado = $stmt->affected_rows > 0;
        $stmt->close();
        return $guardado;
    }

    public function asignarVehiculos()
    {
        // Verificar si se proporcionó un ID de cliente en la URL
        if (!isset($_GET['cliente_id'])) {
            echo "Debe proporcionar un ID de cliente.";
            return;
        }

        // Obtener el ID del cliente desde la URL
        $clienteId = $_GET['cliente_id'];

        // Obtener información del cliente
        $cliente = $this->obtenerInfoCliente($clienteId);

        // Obtener vehículos con imágenes utilizando el método del modelo Compra (o Vehiculo)
        $vehiculos = Vehiculo::getAllVehiculos();
        //$vehiculos = Compra::getVehiculosConImagenes(); // Asegúrate de tener este método en tu modelo

        // Procesar la solicitud POST (si se envió el formulario)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar si se seleccionaron vehículos
            if (!isset($_POST['vehiculos']) || empty($_POST['vehiculos'])) {
                echo "Debe seleccionar al menos un vehículo.";
                return;
            }

            // Obtener los vehículos seleccionados y la cantidad asignada
            $vehiculosSeleccionados = $_POST['vehiculos'];
            $vehiculosFiltrados = [];

            foreach ($vehiculosSeleccionados as $id => $vehiculo) {
                if (isset($vehiculo['cantidad']) && $vehiculo['cantidad'] > 0) {
                    // Buscar el vehículo por su ID en la lista de vehículos disponibles
                    foreach ($vehiculos as $v) {
                        if ($v->id_vehiculo == $id) {
                            // Agregar detalles adicionales del vehículo seleccionado
                            $vehiculo['v_unitario'] = $v->pre_vehiculo;
                            $vehiculo['modelo'] = $v->mod_vehiculo;
                            $vehiculosFiltrados[$id] = $vehiculo;

                            // Reducir el stock del vehículo seleccionado
                            $v->stock -= $vehiculo['cantidad'];
                            $v->save(); // Guardar el cambio en la base de datos

                            break;
                        }
                    }
                }
            }

            // Guardar la información de la compra en la sesión
            $_SESSION['compra'] = [
                'cliente_id' => $clienteId,
                'vehiculos' => $vehiculosFiltrados
            ];

            // Redirigir a la página de asignar placa u otra página necesaria
            require_once 'views/compras/asignarPlaca.php';
            return;
        }

        // Cargar la vista de asignar vehículos con los vehículos obtenidos
        require_once 'views/compras/asignarVehiculos.php';
    }

    public function obtenerImagenesDeVehiculo($idVehiculo) {
        // Utiliza el modelo Vehiculo para obtener las imágenes del vehículo
        $imagenes = Vehiculo::getImagesByVehiculoId($idVehiculo);
        
        // Si no se encontraron imágenes, podrías manejarlo devolviendo un array vacío o null según tu lógica de negocio
        if (empty($imagenes)) {
            return [];
        }
        
        return $imagenes;
    }
    
    private function obtenerInfoVehiculo($id) {
        return $this->vehiculoModel->getVehiculoById($id);
    }

    private function obtenerInfoCliente($id) {
        return $this->clienteModel->getById($id);
    }

    public function asignarPlaca() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verificar datos de compra en sesión
            if (!isset($_SESSION['compra'])) {
                echo "Error: No se encontraron datos de compra en la sesión.";
                exit;
            }
    
            $vehiculos = $_SESSION['compra']['vehiculos'];
            $placas = $_POST['placas'] ?? [];
    
            $vehiculosData = [];
            foreach ($vehiculos as $vehiculoId => $data) {
                // Verificar si 'v_unitario' está definido y no es null
                $vUnitario = isset($data['v_unitario']) ? $data['v_unitario'] : 0;
                $cantidad = isset($data['cantidad']) ? $data['cantidad'] : 0;
    
                // Asegurar que 'placas' esté definido y sea un array
                $vehiculoPlacas = isset($placas[$vehiculoId]) && is_array($placas[$vehiculoId]) ? $placas[$vehiculoId] : [];
    
                $vehiculosData[] = [
                    'id' => $vehiculoId,
                    'nom_vehiculo' => $data['nom_vehiculo'],
                    'v_unitario' => $vUnitario,
                    'cantidad' => $cantidad,
                    'placas' => $vehiculoPlacas
                ];
            }
    
            try {
                // Llamar al método estático de Compra para asignar vehículos con placas
                Compra::asignarVehiculos($_SESSION['compra']['cliente_id'], $vehiculosData);
                unset($_SESSION['compra']);
                header('Location: index.php?controller=Cliente&action=index');
                exit;
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
            }
        } else {
            // Mostrar formulario cuando no es POST
            if (!isset($_SESSION['compra'])) {
                echo "Error: No se encontraron datos de compra en la sesión.";
                exit;
            }
    
            // Obtener datos del cliente y vehículos desde la sesión
            $cliente = $_SESSION['compra']['cliente'];
            $vehiculos = $_SESSION['compra']['vehiculos'];
    
            // Cargar la vista para mostrar el formulario
            require 'views/compras/asignarPlaca.php';
        }
    }

    public function verDetalleCompra() {
        // Crear una instancia del modelo Compra
        $compraModel = new Compra();
        
        // Obtener todas las compras con detalles
        $data = $compraModel->getAllComprasConDetalles();
    
        // Incluir la vista y pasar los datos
        require_once 'views/compras/verDetalleCompra.php';
    }
    
    private function guardarContratoBase64($idCompra, $pdfBase64)
    {
        $db = $this->conn;
        $query = "UPDATE compra SET contrato = ? WHERE id_comp = ?";
        $stmt = $db->prepare($query);

        if ($stmt === false) {
            die('Error al preparar la consulta: ' . $db->error);
        }

        $stmt->bind_param('si', $pdfBase64, $idCompra);
        $stmt->execute();
        $stmt->close();
    }

    public function verContrato()
    {
        $idCompra = isset($_GET['id']) ? intval($_GET['id']) : null;

        if (!$idCompra) {
            echo "ID de compra no proporcionado.";
            return;
        }

        $compra = $this->model->getCompraById($idCompra);
        if ($compra) {
            // Generar el PDF
            $pdfContent = $this->generarPDF($compra);

            if (ob_get_contents()) {
                ob_end_clean();
            }

            // Enviar encabezados para la visualización y descarga del PDF en el navegador
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="contrato.pdf"');
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . strlen($pdfContent));
            echo $pdfContent;
        } else {
            echo "No se encontró la compra.";
        }
    }
    private function obtenerCompraPorId($idCompra)
    {
        $db = $this->conn;
        $query = "SELECT * FROM compra WHERE id_comp = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param('i', $idCompra);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return null;
    }

    private function obtenerDetalleCompraPorId($idCompra)
    {
        $db = $this->conn;
        $query = "SELECT cd.*, v.nom_vehiculo, v.mod_vehiculo FROM compra_detalle cd JOIN vehiculo v ON cd.id_vehiculo = v.id_vehiculo WHERE cd.id_comp = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param('i', $idCompra);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }

        return [];
    }
    private function getCompraById($idCompra) {
        // Verifica que $this->model esté inicializado
        if (!$this->model) {
            die('El modelo no está inicializado.');
        }

        // Usa el modelo para obtener la compra
        $compra = $this->model->getCompraById($idCompra);
        return $compra;
    }

        public function descargarContrato()
    {
        if (isset($_GET['id'])) {
            $idCompra = intval($_GET['id']); // Asegúrate de convertir el ID a un entero
            $compra = $this->model->getCompraById($idCompra);

            if ($compra) {
                // Generar el PDF
                $pdfContent = $this->generarPDF($compra);

                // Enviar encabezados para la descarga del PDF
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="contrato.pdf"');
                echo $pdfContent;
                exit; // Detener la ejecución del script después de enviar el PDF
            } else {
                echo "Compra no encontrada.";
            }
        } else {
            echo "ID de compra no especificado.";
        }
    }
    
        private function generarPDF($compra)
    {
        require_once 'lib/fpdf186/fpdf.php';

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'CONTRATO DE COMPRAVENTA DE VEHICULO AUTOMOTOR', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Ln(10);

        // Información del cliente
        $pdf->Cell(0, 10, 'Cliente: ' . $compra['nombreCliente'] . ' ' . $compra['apellidoCliente'], 0, 1);
        $pdf->Cell(0, 10, 'Cedula: ' . $compra['cedulaCliente'], 0, 1);
        $pdf->Ln(10);

        // Información del vehículo
        $pdf->Cell(0, 10, 'Vehiculo:', 0, 1);
        $pdf->Cell(0, 10, 'Nombre: ' . $compra['nombreVehiculo'], 0, 1);
        $pdf->Cell(0, 10, 'Marca: ' . $compra['marcaVehiculo'], 0, 1);
        $pdf->Cell(0, 10, 'Modelo: ' . $compra['modeloVehiculo'], 0, 1);
        $pdf->Cell(0, 10, 'Precio: $' . number_format($compra['precioVehiculo'], 2), 0, 1);
        $pdf->Cell(0, 10, 'Placa: ' . $compra['placa'], 0, 1);
        $pdf->Ln(15);

        // Espacio para la firma del comprador
        $pdf->Cell(0, 10, 'Firma del Comprador:', 0, 1);
        $pdf->Cell(0, 10, '___________________________________', 0, 1);
        $pdf->Ln(10);
        $pdf->Cell(0, 10, '(Nombre del Comprador)', 0, 1);
        
        // Retornar el PDF como una cadena
        return $pdf->Output('S');
    }
}
?>

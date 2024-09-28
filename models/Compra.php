<?php
require_once 'config/config.php';
require_once 'models/Vehiculo.php'; // Asegúrate de incluir el modelo Vehiculo

class Compra
{
    private $db;

    public function __construct()
    {
        $this->db = connect(); // Inicializa la conexión a la base de datos
    }
    public static function asignarVehiculos($clienteId, $vehiculos)
    {
        $db = connect();
        $db->begin_transaction();
        try {
            // Crear la compra con estado 'A' (activo) y compra_total inicial en 0
            $stmt = $db->prepare("INSERT INTO compra (id_cliente, compra_total, fecha_comp, estado) VALUES (?, 0, CURDATE(), 'A')");
            $stmt->bind_param('i', $clienteId);
            $stmt->execute();
            $compraId = $stmt->insert_id;

            $total = 0;
            foreach ($vehiculos as $vehiculo) {
                $vehiculoId = $vehiculo['id'];
                $cantidad = $vehiculo['cantidad'];
                $precio = $vehiculo['v_unitario'];
                $placas = $vehiculo['placas'];

                // Insertar cada detalle de compra por cada placa asignada al vehículo
                $stmtDetalle = $db->prepare("INSERT INTO compra_detalle (id_comp, id_vehiculo, placa, v_unitario, cantidad, v_total, estado) VALUES (?, ?, ?, ?, ?, ?, 'A')");
                foreach ($placas as $placa) {
                    $vTotal = $precio * $cantidad;
                    $stmtDetalle->bind_param('iisdid', $compraId, $vehiculoId, $placa, $precio, $cantidad, $vTotal);
                    $stmtDetalle->execute();
                }

                // Actualizar el stock del vehículo
                Vehiculo::updateStock($vehiculoId, $cantidad);

                // Calcular el total de la compra
                $total += $precio * $cantidad;
            }

            // Actualizar el total de la compra
            $stmtTotal = $db->prepare("UPDATE compra SET compra_total = ? WHERE id_comp = ?");
            $stmtTotal->bind_param('di', $total, $compraId);
            $stmtTotal->execute();

            // Confirmar la transacción
            $db->commit();

        } catch (Exception $e) {
            // Revertir la transacción en caso de error
            $db->rollback();
            throw $e;
        }
    }

    public static function getCompraDetalle($idCompra) {
        $db = connect();

        // Consulta para obtener los datos de la compra y los detalles de los vehículos
        $query = "SELECT cd.id_comp_det, cd.id_comp, cd.id_vehiculo, cd.v_unitario, cd.cantidad, cd.v_total, cd.placa,
                         c.id_comp, c.id_cliente, c.compra_total, c.fecha_comp,
                         u.cedula
                  FROM compra_detalle cd
                  JOIN compra c ON cd.id_comp = c.id_comp
                  JOIN cliente cl ON c.id_cliente = cl.id_cliente
                  JOIN usuario u ON cl.id_usuario = u.id_usuario
                  WHERE cd.id_comp = ?";
                  
        $stmt = $db->prepare($query);
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

    public static function getById($idCompra) {
        $db = connect();
        $stmt = $db->prepare("SELECT * FROM compra WHERE id_comp = ?");
        $stmt->bind_param('i', $idCompra);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return null;
        }

        $compra = $result->fetch_assoc();

        $stmt->close();
        return $compra;
    }

    public static function getUltimaCompra() {
        $db = connect(); // Utiliza tu método de conexión a la base de datos

        $query = "SELECT * FROM compra ORDER BY fecha_comp DESC LIMIT 1";
        $result = $db->query($query);

        if ($result) {
            $compra = $result->fetch_assoc();
            $result->free();
            return $compra;
        } else {
            return null;
        }
    }
    public static function getVehiculosConImagenes()
    {
        $db = connect();
        $query = "
            SELECT v.id_vehiculo, v.nom_vehiculo, v.mod_vehiculo, v.mar_vehiculo, v.col_vehiculo, 
                v.anio_vehiculo, v.pre_vehiculo, v.stock, iv.ruta_img_veh
            FROM vehiculo v
            LEFT JOIN (
                SELECT id_vehiculo, GROUP_CONCAT(ruta_img_veh SEPARATOR ';') AS ruta_img_veh
                FROM imagen_vehiculo
                GROUP BY id_vehiculo
            ) iv ON v.id_vehiculo = iv.id_vehiculo
        ";
        $result = $db->query($query);

        $vehiculos = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $vehiculo = new Vehiculo();
                $vehiculo->id_vehiculo = $row['id_vehiculo'];
                $vehiculo->nom_vehiculo = $row['nom_vehiculo'];
                $vehiculo->mod_vehiculo = $row['mod_vehiculo'];
                $vehiculo->mar_vehiculo = $row['mar_vehiculo'];
                $vehiculo->col_vehiculo = $row['col_vehiculo'];
                $vehiculo->anio_vehiculo = $row['anio_vehiculo'];
                $vehiculo->pre_vehiculo = $row['pre_vehiculo'];
                $vehiculo->stock = $row['stock'];

                // Separar las rutas de las imágenes en un array
                if ($row['ruta_img_veh']) {
                    $vehiculo->rutas_imagenes = explode(';', $row['ruta_img_veh']);
                } else {
                    $vehiculo->rutas_imagenes = [];
                }

                $vehiculos[] = $vehiculo;
            }
        }

        $db->close();

        return $vehiculos;
    }
    public static function getAll() {
        $db = connect(); // Utiliza tu función de conexión a la base de datos aquí

        $query = "SELECT * FROM compra";
        $result = $db->query($query);

        if (!$result) {
            die('Error al obtener las compras: ' . $db->error);
        }

        $compras = [];
        while ($row = $result->fetch_assoc()) {
            $compras[] = $row;
        }

        $result->close();
        $db->close();

        return $compras;
    }
    
    public function getAllComprasConDetalles() {
        $db = connect(); // Asegúrate de que la conexión a la base de datos esté disponible
    
        // Consulta SQL para obtener todas las compras con detalles, incluyendo el ID del detalle de compra
        $sql = "SELECT c.id_comp AS idCompra, c.fecha_comp AS fechaCompra, 
               cl.nombre AS nombreCliente, cl.apellido AS apellidoCliente, cl.cedula AS cedulaCliente,
               v.id_vehiculo AS idVehiculo, v.nom_vehiculo AS nombreVehiculo, 
               v.mod_vehiculo AS modeloVehiculo, v.mar_vehiculo AS marcaVehiculo, 
               v.pre_vehiculo AS precioVehiculo, cd.placa AS placaVehiculo, cd.id_comp_det AS idCompDet,
               iv.ruta_img_veh AS imagenVehiculo
        FROM compra c
        INNER JOIN usuario cl ON c.id_cliente = cl.id_cliente
        INNER JOIN compra_detalle cd ON c.id_comp = cd.id_comp
        INNER JOIN vehiculo v ON cd.id_vehiculo = v.id_vehiculo
        LEFT JOIN imagen_vehiculo iv ON v.id_vehiculo = iv.id_vehiculo
        WHERE cd.estado = 'A' AND c.estado = 'A'
        ORDER BY c.id_comp, v.id_vehiculo, cd.placa";
    
        $result = $db->query($sql);
    
        if (!$result) {
            die('Error en la consulta SQL: ' . $db->error);
        }
    
        $compras = [];
        while ($row = $result->fetch_assoc()) {
            $idCompra = $row['idCompra'];
    
            // Si la compra no está en el array, inicialízala
            if (!isset($compras[$idCompra])) {
                $compras[$idCompra] = [
                    'idCompra' => $row['idCompra'],
                    'fechaCompra' => $row['fechaCompra'],
                    'nombreCliente' => $row['nombreCliente'],
                    'apellidoCliente' => $row['apellidoCliente'],
                    'cedulaCliente' => $row['cedulaCliente'],
                    'vehiculos' => []
                ];
            }
    
            $vehiculoId = $row['idVehiculo'];
            $placa = isset($row['placaVehiculo']) ? $row['placaVehiculo'] : 'No disponible'; // Maneja posibles valores nulos
    
            // Verificar si el vehículo ya está agregado para esta compra
            if (!isset($compras[$idCompra]['vehiculos'][$vehiculoId])) {
                $compras[$idCompra]['vehiculos'][$vehiculoId] = [
                    'idVehiculo' => $row['idVehiculo'],
                    'nombreVehiculo' => $row['nombreVehiculo'],
                    'modeloVehiculo' => $row['modeloVehiculo'],
                    'marcaVehiculo' => $row['marcaVehiculo'],
                    'precioVehiculo' => $row['precioVehiculo'],
                    'imagenVehiculo' => $row['imagenVehiculo'],
                    'placas' => []
                ];
            }
    
            // Agregar el detalle del vehículo, incluyendo el ID del detalle
            $compras[$idCompra]['vehiculos'][$vehiculoId]['placas'][] = [
                'placa' => $placa,
                'idCompDet' => $row['idCompDet'], // Incluye el ID del detalle de compra
            ];
        }
    
        // Reindexar los vehículos para que sea un array numerado
        foreach ($compras as $idCompra => $compra) {
            $compras[$idCompra]['vehiculos'] = array_values($compras[$idCompra]['vehiculos']);
        }
    
        return $compras;
    }
    
    public static function getCompraDetalleById($idCompra, $placa) {
        $db = connect();
        $stmt = $db->prepare("SELECT * FROM compra_detalle WHERE id_comp = ? AND placa = ?");
        $stmt->bind_param('is', $idCompra, $placa);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows === 0) {
            return null;
        }
    
        $detalle = $result->fetch_assoc();
    
        $stmt->close();
        return $detalle;
    }
    
    public function getFacturas() {
        $query = "SELECT * FROM compra WHERE estado = 'A'";
        $stmt = $this->connection->prepare($query);

        if ($stmt === false) {
            die('Error al preparar la consulta: ' . $this->connection->error);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $id_comp = $row['id_comp'];

            $detailsQuery = "SELECT * FROM compra_detalle WHERE id_comp = ? AND estado = 'A'";
            $detailsStmt = $this->connection->prepare($detailsQuery);

            if ($detailsStmt === false) {
                die('Error al preparar la consulta de detalles: ' . $this->connection->error);
            }

            $detailsStmt->bind_param('i', $id_comp);
            $detailsStmt->execute();
            $detailsResult = $detailsStmt->get_result();

            $vehiculos = [];
            while ($detail = $detailsResult->fetch_assoc()) {
                $vehiculos[] = $detail;
            }

            $data[] = [
                'idCompra' => $row['id_comp'],
                'fechaCompra' => $row['fecha_comp'],
                'nombreCliente' => $row['nombre_cliente'],
                'apellidoCliente' => $row['apellido_cliente'],
                'cedulaCliente' => $row['cedula_cliente'],
                'vehiculos' => $vehiculos,
            ];
        }

        return $data;
    }
    public function verContrato() {
        $idCompra = $_GET['id'];
    
        // Recuperar el contrato desde la base de datos
        $sql = "SELECT contrato FROM compra WHERE idCompra = :idCompra";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':idCompra' => $idCompra]);
    
        $compra = $stmt->fetch();
    
        if ($compra && !empty($compra['contrato'])) {
            // Decodificar el contrato desde base64
            $pdfContent = base64_decode($compra['contrato']);
            
            // Establecer las cabeceras para mostrar el PDF
            header('Content-Type: application/pdf');
            echo $pdfContent;
        } else {
            echo "Contrato no encontrado.";
        }
    }
    public function getCompraById($idCompra)
    {
        if (!$this->db) {
            die('No se pudo conectar a la base de datos.');
        }

        $query = "SELECT c.id_comp, u.nombre AS nombreCliente, u.apellido AS apellidoCliente, u.cedula AS cedulaCliente,
                  c.compra_total, c.fecha_comp,
                  v.nom_vehiculo AS nombreVehiculo, v.mod_vehiculo AS modeloVehiculo, v.mar_vehiculo AS marcaVehiculo,
                  cd.v_unitario AS precioVehiculo, cd.placa
                  FROM compra c
                  JOIN usuario u ON c.id_cliente = u.id_cliente
                  JOIN compra_detalle cd ON c.id_comp = cd.id_comp
                  JOIN vehiculo v ON cd.id_vehiculo = v.id_vehiculo
                  WHERE c.id_comp = ?";

        $stmt = $this->db->prepare($query);
        if ($stmt === false) {
            die('Error al preparar la consulta: ' . $this->db->error);
        }

        $stmt->bind_param('i', $idCompra);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }
}

?>
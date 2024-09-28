<?php

require_once 'config/config.php';

class Vehiculo
{
    public $id_vehiculo;
    public $nom_vehiculo;
    public $mod_vehiculo;
    public $mar_vehiculo;
    public $col_vehiculo;
    public $anio_vehiculo;
    public $pre_vehiculo;
    public $stock;
    public $estado; 
    public $rutas_imagenes = []; // Agrega esta propiedad para las imágenes

    public static function getAll()
{
    $db = connect();
    $result = $db->query("SELECT v.id_vehiculo, v.nom_vehiculo, v.mod_vehiculo, v.mar_vehiculo, v.col_vehiculo, v.anio_vehiculo, v.pre_vehiculo, v.stock, i.ruta_img_veh FROM vehiculo v LEFT JOIN imagen_vehiculo i ON v.id_vehiculo = i.id_vehiculo");

    if (!$result) {
        die('Error en la consulta SQL: ' . $db->error);
    }

    $vehiculos = [];
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
        $vehiculo->ruta_img_veh = $row['ruta_img_veh'];
        $vehiculos[] = $vehiculo;
    }
    return $vehiculos;
}

public static function getAllVehiculos()
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
        WHERE v.estado = 'A'";  // Filtrar solo los vehículos activos

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



    public function getVehiculoById($id) {
        $db = connect();
        $stmt = $db->prepare("SELECT * FROM vehiculo WHERE id_vehiculo = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public static function create($nom_vehiculo, $mod_vehiculo, $mar_vehiculo, $col_vehiculo, $anio_vehiculo, $pre_vehiculo, $stock, $rutas_imagenes)
{
    $db = connect();

    // Iniciar transacción para asegurar integridad de datos
    $db->begin_transaction();

    try {
        // Insertar datos del vehículo
        $estado = 'A'; 
        $stmt = $db->prepare("INSERT INTO vehiculo (nom_vehiculo, mod_vehiculo, mar_vehiculo, col_vehiculo, anio_vehiculo, pre_vehiculo, stock, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        // Corrección de los tipos de parámetros
        $stmt->bind_param('ssssdiss', $nom_vehiculo, $mod_vehiculo, $mar_vehiculo, $col_vehiculo, $anio_vehiculo, $pre_vehiculo, $stock, $estado);
        $stmt->execute();
        $id_vehiculo = $stmt->insert_id;
        $stmt->close();

        // Verificar si se insertó correctamente el vehículo
        if (!$id_vehiculo) {
            throw new Exception("Error al insertar el vehículo.");
        }

        // Insertar imágenes del vehículo
        foreach ($rutas_imagenes as $ruta_imagen) {
            $stmt_img = $db->prepare("INSERT INTO imagen_vehiculo (id_vehiculo, ruta_img_veh) VALUES (?, ?)");
            $stmt_img->bind_param('is', $id_vehiculo, $ruta_imagen);
            $stmt_img->execute();
            $stmt_img->close();
        }

        // Commit para confirmar transacción
        $db->commit();

        return $id_vehiculo;

    } catch (Exception $e) {
        // Rollback en caso de error
        $db->rollback();
        return false;
    }
}

    
    

    public static function updateStock($id, $cantidad)
    {
        $db = connect();
        $stmt = $db->prepare("UPDATE vehiculo SET stock = stock - ? WHERE id_vehiculo = ?");
        $stmt->bind_param('ii', $cantidad, $id);
        return $stmt->execute();
    }

    public static function update($id_vehiculo, $nom_vehiculo, $mod_vehiculo, $mar_vehiculo, $col_vehiculo, $anio_vehiculo, $pre_vehiculo, $stock, $rutas_imagenes = [])
{
    $db = connect();

    // Iniciar transacción para asegurar integridad de datos
    $db->begin_transaction();

    try {
        // Actualizar información del vehículo
        $stmt = $db->prepare("UPDATE vehiculo SET nom_vehiculo = ?, mod_vehiculo = ?, mar_vehiculo = ?, col_vehiculo = ?, anio_vehiculo = ?, pre_vehiculo = ?, stock = ? WHERE id_vehiculo = ?");
        $stmt->bind_param('ssssssii', $nom_vehiculo, $mod_vehiculo, $mar_vehiculo, $col_vehiculo, $anio_vehiculo, $pre_vehiculo, $stock, $id_vehiculo);
        $stmt->execute();
        $stmt->close();

        // Eliminar imágenes existentes para el vehículo
        $stmt = $db->prepare("DELETE FROM imagen_vehiculo WHERE id_vehiculo = ?");
        $stmt->bind_param('i', $id_vehiculo);
        $stmt->execute();
        $stmt->close();

        // Insertar nuevas imágenes
        foreach ($rutas_imagenes as $ruta_imagen) {
            $stmt_img = $db->prepare("INSERT INTO imagen_vehiculo (id_vehiculo, ruta_img_veh) VALUES (?, ?)");
            $stmt_img->bind_param('is', $id_vehiculo, $ruta_imagen);
            $stmt_img->execute();
            $stmt_img->close();
        }

        // Commit para confirmar transacción
        $db->commit();

        return true;

    } catch (Exception $e) {
        // Rollback en caso de error
        $db->rollback();
        return false;
    }
}


    public static function getImagesByVehiculoId($id_vehiculo)
    {
        $db = connect();
        $stmt = $db->prepare("SELECT ruta_img_veh FROM imagen_vehiculo WHERE id_vehiculo = ? LIMIT 1");
        $stmt->bind_param('i', $id_vehiculo);
        $stmt->execute();
        $stmt->bind_result($ruta_img_veh);
        
        $imagenes = [];
        
        while ($stmt->fetch()) {
            $imagenes[] = ['ruta_img_veh' => $ruta_img_veh];
        }
    
        $stmt->close();
        
        return $imagenes;
    }
    

    public function save()
    {
        $db = connect();
        $stmt = $db->prepare("UPDATE vehiculo SET stock = ? WHERE id_vehiculo = ?");
        $stmt->bind_param('ii', $this->stock, $this->id_vehiculo);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
        
    }

    public static function getById($id_vehiculo)
    {
        $db = connect();
        $stmt = $db->prepare("SELECT id_vehiculo, nom_vehiculo, mod_vehiculo, mar_vehiculo, col_vehiculo, anio_vehiculo, pre_vehiculo, stock FROM vehiculo WHERE id_vehiculo = ?");
        $stmt->bind_param('i', $id_vehiculo);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return null;
        }

        $row = $result->fetch_assoc();
        $vehiculo = new Vehiculo();
        $vehiculo->id_vehiculo = $row['id_vehiculo'];
        $vehiculo->nom_vehiculo = $row['nom_vehiculo'];
        $vehiculo->mod_vehiculo = $row['mod_vehiculo'];
        $vehiculo->mar_vehiculo = $row['mar_vehiculo'];
        $vehiculo->col_vehiculo = $row['col_vehiculo'];
        $vehiculo->anio_vehiculo = $row['anio_vehiculo'];
        $vehiculo->pre_vehiculo = $row['pre_vehiculo'];
        $vehiculo->stock = $row['stock'];

        $stmt->close();
        return $vehiculo;
    }
    public static function deleteLogically($id_vehiculo)
    {
        $db = connect();
        $estado = 'I';  // Estado inactivo
        $stmt = $db->prepare("UPDATE vehiculo SET estado = ? WHERE id_vehiculo = ?");
        $stmt->bind_param('si', $estado, $id_vehiculo);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
    
}

?>

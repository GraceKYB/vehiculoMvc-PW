<?php

require_once 'config/config.php';

class Cliente
{
    public $id_cliente;
    public $nombre;
    public $apellido;
    public $cedula;
    public $correo;
    public $edad;
    public $direccion;
    public $estado;

    public static function getAllActive()
    {
        $db = connect();
        $result = $db->query("SELECT id_cliente, nombre, apellido, cedula, correo, edad, direccion, estado FROM usuario WHERE estado = 'A'");
        $clientes = [];
        while ($row = $result->fetch_assoc()) {
            $cliente = new Cliente();
            $cliente->id_cliente = $row['id_cliente'];
            $cliente->nombre = $row['nombre'];
            $cliente->apellido = $row['apellido'];
            $cliente->cedula = $row['cedula'];
            $cliente->correo = $row['correo'];
            $cliente->edad = $row['edad'];
            $cliente->direccion = $row['direccion'];
            $cliente->estado = $row['estado'];
            $clientes[] = $cliente;
        }
        return $clientes;
    }
    
    public function getVehiculosAsignados($cedula)
    {
        $db = connect();
        $stmt = $db->prepare("SELECT cd.placa, v.mar_vehiculo, v.mod_vehiculo, v.col_vehiculo, v.anio_vehiculo, v.nom_vehiculo, iv.ruta_img_veh 
                              FROM compra_detalle cd
                              INNER JOIN vehiculo v ON cd.id_vehiculo = v.id_vehiculo
                              INNER JOIN compra c ON cd.id_comp = c.id_comp
                              LEFT JOIN imagen_vehiculo iv ON v.id_vehiculo = iv.id_vehiculo
                              WHERE c.id_cliente = (SELECT id_cliente FROM usuario WHERE cedula = ?)");
        $stmt->bind_param('s', $cedula);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $vehiculos = [];
        while ($row = $result->fetch_assoc()) {
            $vehiculo = new stdClass();
            $vehiculo->placa = $row['placa'];
            $vehiculo->mar_vehiculo = $row['mar_vehiculo'];
            $vehiculo->mod_vehiculo = $row['mod_vehiculo'];
            $vehiculo->col_vehiculo = $row['col_vehiculo'];
            $vehiculo->anio_vehiculo = $row['anio_vehiculo'];
            $vehiculo->nom_vehiculo = $row['nom_vehiculo'];
            $vehiculo->ruta_img_veh = $row['ruta_img_veh']; // Añadir la ruta de la imagen
            $vehiculos[] = $vehiculo;
        }
    
        $stmt->close();
        return $vehiculos;
    }
    

    public function getClienteYVehiculoPorPlaca($placa)
    {
        $db = connect();
        $stmt = $db->prepare("SELECT u.id_cliente, u.nombre, u.apellido, u.cedula, u.correo, u.edad, u.direccion, u.estado,
                                     v.id_vehiculo, v.nom_vehiculo, v.mod_vehiculo, v.mar_vehiculo, v.col_vehiculo, v.anio_vehiculo, v.pre_vehiculo, v.stock,
                                     iv.ruta_img_veh, cd.placa
                              FROM compra_detalle cd
                              INNER JOIN compra c ON cd.id_comp = c.id_comp
                              INNER JOIN usuario u ON c.id_cliente = u.id_cliente
                              INNER JOIN vehiculo v ON cd.id_vehiculo = v.id_vehiculo
                              LEFT JOIN imagen_vehiculo iv ON v.id_vehiculo = iv.id_vehiculo
                              WHERE cd.placa = ?");
        $stmt->bind_param('s', $placa);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $data = [];
        if ($row = $result->fetch_assoc()) {
            $data['cliente'] = [
                'id_cliente' => $row['id_cliente'],
                'nombre' => $row['nombre'],
                'apellido' => $row['apellido'],
                'cedula' => $row['cedula'],
                'correo' => $row['correo'],
                'edad' => $row['edad'],
                'direccion' => $row['direccion'],
                'estado' => $row['estado']
            ];
            $data['vehiculo'] = [
                'id_vehiculo' => $row['id_vehiculo'],
                'nom_vehiculo' => $row['nom_vehiculo'],
                'mod_vehiculo' => $row['mod_vehiculo'],
                'mar_vehiculo' => $row['mar_vehiculo'],
                'col_vehiculo' => $row['col_vehiculo'],
                'anio_vehiculo' => $row['anio_vehiculo'],
                'pre_vehiculo' => $row['pre_vehiculo'],
                'stock' => $row['stock'],
                'ruta_img_veh' => $row['ruta_img_veh'],
                'placa' => $row['placa']
            ];
        }
    
        $stmt->close();
        return $data;
    }

    public static function getCedulaByClienteId($idCliente) {
        $db = connect();
        $stmt = $db->prepare("SELECT cedula FROM usuario WHERE id_cliente = ?");
        $stmt->bind_param('i', $idCliente);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return null;
        }

        $usuario = $result->fetch_assoc();

        $stmt->close();
        return $usuario;
    }
    

    


    public static function getById($id_cliente)
    {
        $db = connect();
        $stmt = $db->prepare("SELECT * FROM usuario WHERE id_cliente = ?");
        $stmt->bind_param('i', $id_cliente);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return null;
        }

        $row = $result->fetch_assoc();
        $cliente = new Cliente();
        $cliente->id_cliente = $row['id_cliente'];
        $cliente->nombre = $row['nombre'];
        $cliente->apellido = $row['apellido'];
        $cliente->cedula = $row['cedula'];
        $cliente->correo = $row['correo'];
        $cliente->edad = $row['edad'];
        $cliente->direccion = $row['direccion'];
        $cliente->estado = $row['estado'];

        $stmt->close();
        return $cliente;
    }


    public static function find($cliente_id)
    {
        $db = connect();
        $stmt = $db->prepare("SELECT id_cliente, nombre, apellido, cedula, correo, edad, direccion, estado FROM usuario WHERE id_cliente = ?");
        $stmt->bind_param('i', $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $cliente = $result->fetch_object('Cliente');
        $stmt->close();
        return $cliente;
    }

    public static function update($cliente_id, $nombre, $apellido, $cedula, $correo, $edad, $direccion)
    {
        $db = connect();
        $stmt = $db->prepare("UPDATE usuario SET nombre = ?, apellido = ?, cedula = ?, correo = ?, edad = ?, direccion = ? WHERE id_cliente = ?");
        $stmt->bind_param('ssssisi', $nombre, $apellido, $cedula, $correo, $edad, $direccion, $cliente_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public static function create($nombre, $apellido, $cedula, $correo, $edad, $direccion)
    {
        $db = connect();
        $stmt = $db->prepare("INSERT INTO usuario (nombre, apellido, cedula, correo, edad, direccion, estado) VALUES (?, ?, ?, ?, ?, ?, 'A')");
        $stmt->bind_param('ssssis', $nombre, $apellido, $cedula, $correo, $edad, $direccion);
        return $stmt->execute();
    }
    public static function delete($id_cliente) {
        $db = connect();
        $sql = "UPDATE usuario SET estado = 'I' WHERE id_cliente = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('i', $id_cliente);
        return $stmt->execute();
    }
}
?>

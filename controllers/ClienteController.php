<?php
require_once 'models/Cliente.php';
require_once 'config/config.php';

class ClienteController
{

    private $clienteModel; // Declara una propiedad para el modelo Cliente

    public function __construct()
    {
        // Inicializa el modelo Cliente
        $this->clienteModel = new Cliente();
    }

    public function index() {
        // Verificar si se accede desde el enlace "Listar Clientes"
        if (!isset($_GET['action']) || $_GET['action'] !== 'index') {
            
            return;
        }

        $clientes = Cliente::getAllActive(); 

        require_once 'views/clientes/ClientesIndex.php';
    }

    

    // ClienteController.php

public function search()
    {
        // Verificar si se ha enviado una cédula
        if (isset($_GET['cedula']) && !empty($_GET['cedula'])) {
            // Obtener la cédula desde el formulario
            $cedula = $_GET['cedula'];

            // Llamar al método en el modelo para obtener los vehículos asignados
            $vehiculos = $this->clienteModel->getVehiculosAsignados($cedula);

            // Incluir la vista para mostrar los resultados
            require_once 'views/clientes/search_results.php';
        } else {
            // Mostrar el formulario de búsqueda por cédula
            require_once 'views/clientes/search.php';
        }
    }

    
    public function searchByPlaca()
    {
        if (isset($_GET['placa']) && !empty($_GET['placa'])) {
            $placa = $_GET['placa'];
    
            $data = $this->clienteModel->getClienteYVehiculoPorPlaca($placa);
    
            require_once 'views/clientes/search_by_placa_results.php';
        } else {
            require_once 'views/clientes/search_by_placa.php';
        }
    }
    

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'];
            $apellido = $_POST['apellido'];
            $cedula = $_POST['cedula'];
            $correo = $_POST['correo'];
            $edad = $_POST['edad'];
            $direccion = $_POST['direccion'];
    
            // Intenta crear el cliente
            if (Cliente::create($nombre, $apellido, $cedula, $correo, $edad, $direccion)) {
                // Cliente creado correctamente
                header('Location: index.php?controller=Cliente&action=index&success=true');
                exit;
            } else {
                // Error al crear el cliente
                echo "Error al registrar el cliente.";
            }
        } else {
            require 'views/clientes/create.php';
        }
        
    }

    public function edit()
    {
        if (!isset($_GET['id'])) {
            echo "ID de cliente no proporcionado.";
            return;
        }

        $cliente_id = $_GET['id'];
        $cliente = Cliente::find($cliente_id);

        if (!$cliente) {
            echo "Cliente no encontrado.";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'];
            $apellido = $_POST['apellido'];
            $cedula = $_POST['cedula'];
            $correo = $_POST['correo'];
            $edad = $_POST['edad'];
            $direccion = $_POST['direccion'];
    
            if (Cliente::update($cliente_id, $nombre, $apellido, $cedula, $correo, $edad, $direccion)) {
                header('Location: index.php?controller=Cliente&action=index&success=true');
                exit;
            } else {
                echo "Error al actualizar el cliente.";
            }
        } else {
            require 'views/clientes/edit.php';
        }
    }
    public function delete() {
        if (!isset($_GET['id_cliente'])) {
            echo "ID de cliente no proporcionado.";
            return;
        }
    
        $cliente_id = $_GET['id_cliente'];
    
        if (Cliente::delete($cliente_id)) {
            header('Location: index.php?controller=Cliente&action=index&success=true');
            exit;
        } else {
            echo "Error al eliminar el cliente.";
        }
    }
}
?>

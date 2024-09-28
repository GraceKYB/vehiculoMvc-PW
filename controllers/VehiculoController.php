<?php
require_once 'models/Vehiculo.php';
require_once 'config/config.php';

class VehiculoController
{
    public function index()
    {
        // Obtener todos los vehículos
        $vehiculos = Vehiculo::getAllVehiculos();
        
        // Cargar la vista de la lista de vehículos
        require 'views/vehiculos/VehiculosIndex.php';
    }


    public function create()
    {
        // Verificar si es una solicitud POST para procesar el formulario
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Obtener los datos del formulario
            $nom_vehiculo = $_POST['nom_vehiculo'];
            $mod_vehiculo = $_POST['mod_vehiculo'];
            $mar_vehiculo = $_POST['mar_vehiculo'];
            $col_vehiculo = $_POST['col_vehiculo'];
            $anio_vehiculo = $_POST['anio_vehiculo'];
            $pre_vehiculo = $_POST['pre_vehiculo'];
            $stock = $_POST['stock'];
    
            // Verificar si se subieron archivos de imágenes
            if (isset($_FILES['imagenes'])) {
                $uploaded_images = [];
    
                foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
                    $file_name = $_FILES['imagenes']['name'][$key];
                    $file_tmp = $_FILES['imagenes']['tmp_name'][$key];
                    $ruta_imagen = 'public/imagenes/' . $file_name;
    
                    if (move_uploaded_file($file_tmp, $ruta_imagen)) {
                        $uploaded_images[] = $ruta_imagen;
                    } else {
                        echo "Error al subir la imagen $file_name.";
                        return; // Detener la ejecución si hay un error en la carga de imágenes
                    }
                }
    
                // Llamar al método create del modelo Vehiculo con las imágenes subidas
                $id_vehiculo = Vehiculo::create($nom_vehiculo, $mod_vehiculo, $mar_vehiculo, $col_vehiculo, $anio_vehiculo, $pre_vehiculo, $stock, $uploaded_images);
    
                if ($id_vehiculo) {
                    // Éxito en la creación del vehículo
                    header('Location: index.php?controller=Vehiculo&action=index&success=true');
                    exit;
                } else {
                    // Error al registrar el vehículo
                    echo "Hubo un error al registrar el vehículo.";
                }
            } else {
                // No se subió ninguna imagen válida
                echo "Por favor seleccione al menos una imagen válida.";
            }
        } else {
            // Si no es una solicitud POST, mostrar el formulario de registro de vehículos
            require 'views/vehiculos/create.php';
        }
    }
    
    public function edit()
{
    $id_vehiculo = isset($_GET['id']) ? $_GET['id'] : null;

    if ($id_vehiculo) {
        $vehiculo = Vehiculo::getById($id_vehiculo);

        
        if ($vehiculo) {
            // Obtener imágenes existentes
            $imagenes = Vehiculo::getImagesByVehiculoId($id_vehiculo);
            // Convertir las rutas de imágenes a un formato adecuado para la vista
            $rutas_imagenes = array_map(function($imagen) {
                return $imagen['ruta_img_veh'];
            }, $imagenes);
            require 'views/vehiculos/edit.php';
        } else {
            echo "Vehículo no encontrado.";
        }
    } else {
        echo "ID de vehículo no especificado.";
    }
}

public function update()
{
    $id_vehiculo = isset($_POST['id_vehiculo']) ? $_POST['id_vehiculo'] : null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id_vehiculo) {
        $nom_vehiculo = $_POST['nom_vehiculo'];
        $mod_vehiculo = $_POST['mod_vehiculo'];
        $mar_vehiculo = $_POST['mar_vehiculo'];
        $col_vehiculo = $_POST['col_vehiculo'];
        $anio_vehiculo = $_POST['anio_vehiculo'];
        $pre_vehiculo = $_POST['pre_vehiculo'];
        $stock = $_POST['stock'];

        // Manejar imágenes
        $uploaded_images = [];
        if (isset($_FILES['imagenes'])) {
            foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
                $file_name = $_FILES['imagenes']['name'][$key];
                $file_tmp = $_FILES['imagenes']['tmp_name'][$key];
                $ruta_imagen = 'public/imagenes/' . $file_name;

                if (move_uploaded_file($file_tmp, $ruta_imagen)) {
                    $uploaded_images[] = $ruta_imagen;
                } else {
                    echo "Error al subir la imagen $file_name.";
                    return;
                }
            }
        }

        // Llamar al método update del modelo Vehiculo
        if (Vehiculo::update($id_vehiculo, $nom_vehiculo, $mod_vehiculo, $mar_vehiculo, $col_vehiculo, $anio_vehiculo, $pre_vehiculo, $stock, $uploaded_images)) {
            header('Location: index.php?controller=Vehiculo&action=index&updated=true');
            exit;
        } else {
            echo "Error al actualizar el vehículo.";
        }
    } else {
        echo "ID de vehículo no especificado.";
    }
}

public function delete()
{
    if (isset($_GET['id'])) {
        $id_vehiculo = $_GET['id'];
        $result = Vehiculo::deleteLogically($id_vehiculo);

        if ($result) {
            // Redirigir después de eliminar lógicamente
            header('Location: index.php?controller=Vehiculo&action=index&deleted=true');
            exit;
        } else {
            echo "Hubo un error al eliminar el vehículo.";
        }
    } else {
        echo "ID de vehículo no especificado.";
    }
}



}
?>

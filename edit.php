<?php
include 'config/config.php';

// Obtener la conexión
$connection = connect();

// Verificar la conexión
if (!$connection) {
    die('Error en la conexión: ' . mysqli_connect_error());
}

// Obtener los parámetros de la solicitud
$id_comp = $_GET['id'] ?? '';
$placa = $_GET['placa'] ?? '';

// Consultar los detalles de la placa
$query = "SELECT * FROM compra_detalle WHERE id_comp = ? AND placa = ?";
$stmt = $connection->prepare($query);

if ($stmt === false) {
    die('Error al preparar la consulta: ' . $connection->error);
}

$stmt->bind_param('is', $id_comp, $placa);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $detalle = $result->fetch_assoc();
} else {
    die('No se encontraron detalles.');
}

// Procesar la actualización si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevaPlaca = $_POST['placa'];

    $updateQuery = "UPDATE compra_detalle SET placa = ? WHERE id_comp = ? AND placa = ?";
    $updateStmt = $connection->prepare($updateQuery);

    if ($updateStmt === false) {
        die('Error al preparar la consulta de actualización: ' . $connection->error);
    }

    $updateStmt->bind_param('sis', $nuevaPlaca, $id_comp, $placa);
    $updateStmt->execute();

    if ($updateStmt->affected_rows > 0) {
        echo 'Placa actualizada correctamente.';
    } else {
        echo 'No se realizaron cambios.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Placa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #007bff;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        label {
            font-weight: bold;
        }
        input[type="text"] {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        button {
            padding: 10px;
            border: none;
            border-radius: 4px;
            background-color: #007bff;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message {
            text-align: center;
            font-size: 18px;
            margin: 10px 0;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Editar Placa</h1>

        <?php if (isset($message)): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <form action="edit.php?id=<?php echo htmlspecialchars($id_comp); ?>&placa=<?php echo htmlspecialchars($placa); ?>" method="post">
            <label for="placa">Placa:</label>
            <input type="text" id="placa" name="placa" value="<?php echo htmlspecialchars($detalle['placa']); ?>" required>
            <button type="submit">Guardar</button>
        </form>

        <!-- Enlace para regresar a ver detalles de la compra -->
        <a href="http://localhost/vehiculoMvc/index.php?controller=Compra&action=verDetalleCompra&id=<?php echo htmlspecialchars($id_comp); ?>">Volver a Detalle de Compra</a>
    </div>
</body>
</html>
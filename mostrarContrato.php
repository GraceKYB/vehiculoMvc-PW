<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mostrar Contrato</title>
</head>
<body>

<h1>Contrato</h1>

<iframe src="index.php?controller=Contrato&action=mostrarContrato&id=<?php echo htmlspecialchars($_GET['id']); ?>" width="100%" height="600px">
    This browser does not support PDFs. Please download the PDF to view it: <a href="index.php?controller=Contrato&action=mostrarContrato&id=<?php echo htmlspecialchars($_GET['id']); ?>">Download PDF</a>.
</iframe>

</body>
</html>

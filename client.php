<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validación de campos
    $fields = ['name', 'paternal_surname', 'maternal_surname', 'id_number', 'birth_date', 'gender', 'birth_place', 'marital_status', 'profession', 'address'];
    foreach ($fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            die("Error: Todos los campos son obligatorios.");
        }
    }

    $nombre = htmlspecialchars($_POST['name']);
    $apellido_paterno = htmlspecialchars($_POST['paternal_surname']);
    $apellido_materno = htmlspecialchars($_POST['maternal_surname']);
    $numero_carnet = htmlspecialchars($_POST['id_number']);
    $fecha_nacimiento = htmlspecialchars($_POST['birth_date']);
    $sexo = htmlspecialchars($_POST['gender']);
    $lugar_nacimiento = htmlspecialchars($_POST['birth_place']);
    $estado_civil = htmlspecialchars($_POST['marital_status']);
    $profesion = htmlspecialchars($_POST['profession']);
    $domicilio = htmlspecialchars($_POST['address']);

    // Generar un token único para la transacción
    $token = md5($nombre . $numero_carnet . time());

    try {
        // Crear un cliente SOAP para enviar los datos al servidor
        $client = new SoapClient(null, [
            'location' => "http://192.168.1.4:80/soap/server.php", // URL del servidor SOAP en PC2
            'uri' => "urn:PersonService",
            'trace' => 1
        ]);

        // Enviar los datos de la persona y el token al servidor
        $response = $client->registerPerson(
            $nombre, 
            $apellido_paterno, 
            $apellido_materno, 
            $numero_carnet, 
            $fecha_nacimiento, 
            $sexo, 
            $lugar_nacimiento, 
            $estado_civil, 
            $profesion, 
            $domicilio, 
            $token
        );
        echo $response;
    } catch (SoapFault $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

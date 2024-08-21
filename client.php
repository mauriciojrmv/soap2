<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['name']) && !empty($_POST['name'])
        && isset($_POST['paternal_surname']) && !empty($_POST['paternal_surname'])
        && isset($_POST['maternal_surname']) && !empty($_POST['maternal_surname'])
        && isset($_POST['id_number']) && !empty($_POST['id_number'])
        && isset($_POST['birth_date']) && !empty($_POST['birth_date'])
        && isset($_POST['gender']) && !empty($_POST['gender'])
        && isset($_POST['birth_place']) && !empty($_POST['birth_place'])
        && isset($_POST['marital_status']) && !empty($_POST['marital_status'])
        && isset($_POST['profession']) && !empty($_POST['profession'])
        && isset($_POST['address']) && !empty($_POST['address'])
    ) {
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
        
        // Generar un token simple para evitar duplicación
        $token = md5($nombre . $numero_carnet . time());

        try {
            $client = new SoapClient(null, [
                'location' => "http://localhost:8000/soap/server.php",
                'uri' => "http://localhost:8000/soap/server.php",
                'trace' => 1
            ]);

            // Enviar los datos de la persona y el token al servidor
            $response = $client->registerPerson($nombre, $apellido_paterno, $apellido_materno, $numero_carnet, $fecha_nacimiento, $sexo, $lugar_nacimiento, $estado_civil, $profesion, $domicilio, $token);
            echo $response;
        } catch (SoapFault $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Por favor, complete todos los campos.";
    }
}
?>

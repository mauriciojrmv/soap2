<?php
class PersonService {
    private $dbServerUrl;

    public function __construct() {
        // URL del servidor de base de datos en PC3
        $this->dbServerUrl = "http://localhost:8000/soap/bd.php";
    }

    public function registerPerson($nombre, $apellido_paterno, $apellido_materno, $numero_carnet, $fecha_nacimiento, $sexo, $lugar_nacimiento, $estado_civil, $profesion, $domicilio, $token) {
        try {
            // Verificar si la persona ya está registrada
            $exists = $this->remoteCall('checkIfPersonExists', [$numero_carnet]);

            if ($exists) {
                // Obtener la información de la persona si ya está registrada
                $personInfo = $this->remoteCall('getPersonInfo', [$numero_carnet]);

                // Verificar si el token ya fue utilizado
                if ($personInfo['token'] === $token) {
                    return "Error: Este token ya fue utilizado para registrar a esta persona.";
                }

                // Seleccionar solo la información crucial
                $infoCrucial = [
                    'Nombre Completo' => $personInfo['nombre'] . ' ' . $personInfo['apellido_paterno'] . ' ' . $personInfo['apellido_materno'],
                    'Número de Carnet' => $personInfo['numero_carnet'],
                    'Profesión' => $personInfo['profesion'],
                    'Domicilio' => $personInfo['domicilio']
                ];

                $output = "La persona ya está registrada.<br>\n";
                foreach ($infoCrucial as $key => $value) {
                    $output .= "$key: $value\n";
                }

                return $output;
            }

            // Insertar la persona en la base de datos junto con el token
            $result = $this->remoteCall('insertPerson', [
                $nombre, $apellido_paterno, $apellido_materno, $numero_carnet, $fecha_nacimiento, 
                $sexo, $lugar_nacimiento, $estado_civil, $profesion, $domicilio, $token
            ]);

            return $result ? "Registro exitoso de: $nombre $apellido_paterno $apellido_materno - $numero_carnet"
                           : "Error al registrar la persona.";
        } catch (Exception $e) {
            return "Error en el servidor: " . $e->getMessage();
        }
    }

    private function remoteCall($method, $params) {
        // Crear un cliente SOAP para comunicarse con bd.php en la PC3
        $client = new SoapClient(null, [
            'location' => $this->dbServerUrl,
            'uri' => "urn:DatabaseService"
        ]);
        return $client->__soapCall($method, $params);
    }
}

// Configuración del servidor SOAP en la PC2
$server = new SoapServer(null, ['uri' => "urn:PersonService"]);
$server->setClass('PersonService');
$server->handle();
?>
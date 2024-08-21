<?php
class PersonService {
    private $db;
    
    public function __construct() {
        // Conectar a la base de datos
        $this->db = new mysqli('localhost', 'root', '', 'person_db'); // Cambiar según la configuración de tu base de datos
        if ($this->db->connect_error) {
            throw new Exception("Error de conexión a la base de datos: " . $this->db->connect_error);
        }
    }

    public function registerPerson($nombre, $apellido_paterno, $apellido_materno, $numero_carnet, $fecha_nacimiento, $sexo, $lugar_nacimiento, $estado_civil, $profesion, $domicilio, $token) {
        // Verificar si el número de carnet ya está registrado
        $stmt = $this->db->prepare("SELECT COUNT(*), nombre, apellido_paterno, apellido_materno, fecha_nacimiento, sexo, lugar_nacimiento, estado_civil, profesion, domicilio FROM personas WHERE numero_carnet = ?");
        $stmt->bind_param("s", $numero_carnet);
        $stmt->execute();
        $stmt->bind_result($count, $db_nombre, $db_apellido_paterno, $db_apellido_materno, $db_fecha_nacimiento, $db_sexo, $db_lugar_nacimiento, $db_estado_civil, $db_profesion, $db_domicilio);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            // Mostrar datos de la persona ya registrada
            return "Error: La persona con este número de carnet ya está registrada.<br>" .
                   "Datos Registrados:<br>" .
                   "Nombre: $db_nombre<br>" .
                   "Apellido Paterno: $db_apellido_paterno<br>" .
                   "Apellido Materno: $db_apellido_materno<br>" .
                   "Fecha de Nacimiento: $db_fecha_nacimiento<br>" .
                   "Sexo: $db_sexo<br>" .
                   "Lugar de Nacimiento: $db_lugar_nacimiento<br>" .
                   "Estado Civil: $db_estado_civil<br>" .
                   "Profesión: $db_profesion<br>" .
                   "Domicilio: $db_domicilio";
        }

        // Insertar los datos en la base de datos
        $stmt = $this->db->prepare("INSERT INTO personas (nombre, apellido_paterno, apellido_materno, numero_carnet, fecha_nacimiento, sexo, lugar_nacimiento, estado_civil, profesion, domicilio, token) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssssss", $nombre, $apellido_paterno, $apellido_materno, $numero_carnet, $fecha_nacimiento, $sexo, $lugar_nacimiento, $estado_civil, $profesion, $domicilio, $token);
        if ($stmt->execute()) {
            return "Registro exitoso de: $nombre $apellido_paterno $apellido_materno - $numero_carnet";
        } else {
            throw new Exception("Error al registrar la persona: " . $stmt->error);
        }
    }
}

// Configuración del servidor SOAP
$server = new SoapServer(null, ['uri' => "http://192.168.56.1:8000/soap/server.php"]);
$server->setClass('PersonService');
$server->handle();
?>

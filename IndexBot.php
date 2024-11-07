<?php
// Configura las variables de conexión usando los valores de entorno de Railway
$host = getenv('MYSQL_HOST');
$dbname = getenv('MYSQL_DATABASE');
$user = getenv('MYSQL_USER');
$password = getenv('MYSQL_PASSWORD');

// Conexión a la base de datos
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("Error en la conexión a la base de datos: " . $e->getMessage());
    die("Error en la conexión a la base de datos");
}

// Función para procesar solicitudes del bot
function processMessage($message) {
    global $pdo;

    // Token de tu bot (asegúrate de no exponerlo públicamente)
    $token = 'TU_TOKEN_DEL_BOT_DE_TELEGRAM';
    $chat_id = $message['message']['chat']['id'];
    $text = $message['message']['text'];

    // Verificar el comando recibido
    if (strpos($text, '/consulta_vuelos') === 0) {
        $query = "SELECT * FROM vuelos"; // Ejemplo de consulta; ajústala según tu tabla
        $stmt = $pdo->query($query);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($results) {
            $response = "Vuelos disponibles:\n";
            foreach ($results as $vuelo) {
                $response .= "Vuelo: " . $vuelo['nombre_vuelo'] . " - Destino: " . $vuelo['destino'] . "\n";
            }
        } else {
            $response = "No hay vuelos disponibles en este momento.";
        }
    } else {
        $response = "Comando no reconocido. Usa /consulta_vuelos para obtener información.";
    }

    // Enviar la respuesta al usuario
    file_get_contents("https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=" . urlencode($response));
}

// Leer el mensaje de entrada desde Telegram
$content = file_get_contents("php://input");
$update = json_decode($content, true);

if (!$update) {
    // Si no hay datos, salir
    exit;
}

// Procesar el mensaje
processMessage($update);
?>

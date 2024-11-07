<?php
// Tu token de Telegram
$telegram_token = "7878187891:AAG2aW4PK1p8UyaWGnVh52kBHxi6fEJdGT4";

// URL del bot para interactuar con la API de Telegram
$telegram_api_url = "https://api.telegram.org/bot$telegram_token";

// Detalles de la base de datos en Railway
$mysql_host = "mysql.railway.internal"; // El host interno de la base de datos
$mysql_user = "root"; // El usuario de la base de datos
$mysql_password = "jTkQUOKnFggBChnTHtPNEtTmuaJisuBx"; // La contraseña de la base de datos
$mysql_database = "railway"; // El nombre de la base de datos
$mysql_port = 3306; // El puerto de la base de datos (puerto predeterminado de MySQL)

// Conexión a la base de datos en Railway
$conn = new mysqli($mysql_host, $mysql_user, $mysql_password, $mysql_database, $mysql_port);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtiene los updates del bot
$updates = file_get_contents("php://input");
$updates = json_decode($updates, TRUE);

$chat_id = $updates['message']['chat']['id'];
$message_text = $updates['message']['text'];

// Función para enviar un mensaje al usuario
function sendMessage($chat_id, $text) {
    global $telegram_api_url;
    file_get_contents($telegram_api_url . "/sendMessage?chat_id=$chat_id&text=" . urlencode($text));
}

// Comando para consultar vuelos
if (strpos($message_text, "/consulta_vuelos") === 0) {
    // Consulta a la base de datos para obtener los vuelos disponibles
    $query = "SELECT * FROM vuelos WHERE estado = 'disponible' LIMIT 5";
    $result = $conn->query($query);
    
    if ($result->num_rows > 0) {
        $response = "Vuelos disponibles:\n";
        while($row = $result->fetch_assoc()) {
            $response .= "Vuelo: " . $row['origen'] . " - " . $row['destino'] . "\nFecha: " . $row['fecha'] . "\n\n";
        }
    } else {
        $response = "No hay vuelos disponibles en este momento.";
    }
    
    sendMessage($chat_id, $response);
}

// Responder a los usuarios
sendMessage($chat_id, "¡Hola! ¿En qué puedo ayudarte? Usa el comando /consulta_vuelos para obtener los vuelos disponibles.");

?>

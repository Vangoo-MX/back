<?php
if ($_GET && isset($_GET['id'])) {
    $uuid = $_GET['id'];
} else {
    echo "El link es incorrecto";
    exit;
}

$urlShare = 'https://dashboard.vangoo.mx/share/list/view.php?id=' . $uuid;

$userUrl = 'https://dashboard.vangoo.mx/api/user/uuid/' . $uuid;
$curl = curl_init($userUrl);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($curl);
curl_close($curl);

$userData = json_decode($response, true);

if (!$userData || isset($userData['error'])) {
    echo "No se ha encontrado el usuario";
    exit;
}

$userId = $userData['id'];
$propertyTypes = [
    'property',
    'apartment',
    'terrain',
    'lot',
    'development-vertical',
    'development-horizontal'
];

$allProperties = [];

foreach ($propertyTypes as $type) {
    $apiUrl = "https://dashboard.vangoo.mx/api/{$type}/user/{$userId}";
    $curl = curl_init($apiUrl);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);

    $properties = json_decode($response, true);

    if (is_array($properties)) {
        if (isset($properties[0])) {
            $allProperties = array_merge($allProperties, $properties);
        } else if (!empty($properties)) {
            $allProperties[] = $properties;
        }
    }
}

$data = [
    'user' => $userData,
    'properties' => $allProperties
];

function debugData($data, $label = '')
{
    $label = $label ? "<h2>$label</h2>" : '';
    $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
    $caller = $backtrace[0];
    $location = "Called from: {$caller['file']} on line {$caller['line']}";

    $output = "<div style='background:#f8f8f8; border:1px solid #ddd; padding:15px; margin:20px; border-radius:5px; font-family:monospace;'>";
    $output .= $label;
    $output .= "<div style='color:#888; font-size:0.9em; margin-bottom:10px;'>$location</div>";
    $output .= "<pre>" . htmlspecialchars(print_r($data, true)) . "</pre>";
    $output .= "</div>";

    echo $output;
}

debugData($data, 'Estructura completa de $data');

function moneyFormat($numero)
{
    $formatted = number_format($numero, 2, '.', ',');
    return '$' . $formatted . ' MXN';
}

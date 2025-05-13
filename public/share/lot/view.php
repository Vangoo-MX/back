<?php
if ($_GET && isset($_GET['id'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    if (!$id) {
        echo "El link es incorrecto";
        exit;
    }
} else {
    echo "El link es incorrecto";
    exit;
}

$typeText = 'lots';
$urlShare = 'https://dashboard.vangoo.mx/share/lot/view.php?id=' . $id;

$urlApi = 'https://dashboard.vangoo.mx/api/lot/detail/' . $id;
$curl = curl_init($urlApi);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($curl);

if ($response === false) {
    echo "Error en la solicitud cURL: " . curl_error($curl);
    curl_close($curl);
    exit;
}

curl_close($curl);
$data = json_decode($response, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo "Error al decodificar la respuesta JSON: " . json_last_error_msg();
    exit;
}

if (!$data) {
    echo "No se ha encontrado el desarrollo";
    exit;
}

$imagePath = 'https://dashboard.vangoo.mx/storage/img/posts/' . $typeText . '/' . $data['id'] . '/1.webp';
$backupImage = 'https://www.vangoo.mx/assets/img/img404.jpg';

$imageHeaders = @get_headers($imagePath);
if ($imageHeaders === false || strpos($imageHeaders[0], '404') !== false) {
    $imageToUse = $backupImage;
} else {
    $imageToUse = $imagePath;
}

function moneyFormat($numero)
{
    $formatted = number_format($numero, 2, '.', ',');
    return '$' . $formatted . ' MXN';
}

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vangoo | <?php echo $data['title']; ?></title>
    <link rel="icon" type="image/png" href="https://vangoo.mx/assets/icon/favicon.png">

    <meta name="description" content="<?php echo $data['description']; ?>">
    <meta name="keywords" content="comprar casa, comprar departamento,rentar,rentar casa,comprar nuevo león, rentar casa en nuevo león, comprar casa monterrey,publicar propiedad, buscar propiedades en nuevo león, buscar departamentos,sitio para vivir, lugar para vivir,encontrar dónde vivir, vender propiedades en nuevo león,comisión por venta de propiedad,propiedades destacados nuevo león,desarrollos inmobiliarios">
    <meta property="image" content="<?php echo $imageToUse; ?>">
    <meta property="image:secure_url" content="<?php echo $imageToUse; ?>">
    <meta property="url" content="<?php echo $urlShare; ?>">
    <link rel="canonical" href="<?php echo $urlShare; ?>">

    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo $data['title']; ?>">
    <meta property="og:description" content="<?php echo $data['description']; ?>">
    <meta property="og:image" content="<?php echo $imageToUse; ?>">
    <meta property="og:image:secure_url" content="<?php echo $imageToUse; ?>">
    <meta property="og:image:type" content="image/jpeg">
    <meta property="og:image:width" content="400">
    <meta property="og:image:height" content="300">
    <meta property="og:url" content="<?php echo $urlShare; ?>">
    <meta property="fb:app_id" content="7865680626775570">

    <script src="https://kit.fontawesome.com/e0df5df9e9.js" crossorigin="anonymous"></script>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
        }

        .container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .logo-section {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 32px;
        }

        .logo {
            height: 80px;
            margin-bottom: 16px;
        }

        .btn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .card {
            width: 100%;
            max-width: 400px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
        }

        .card-header {
            position: relative;
        }

        .image-container {
            position: relative;
        }

        .property-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }

        .icon-btn {
            position: absolute;
            top: 16px;
            right: 16px;
            background: white;
            border: none;
            border-radius: 50%;
            padding: 8px;
            cursor: pointer;
        }

        .icon-btn img {
            width: 16px;
            height: 16px;
        }

        .card-content {
            padding: 16px;
        }

        .property-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .property-price {
            font-size: 1.8rem;
            color: #FC7B97;
            margin-bottom: 16px;
        }

        .property-address {
            color: #666;
            font-size: 1.2rem;
            margin-bottom: 16px;
        }

        .card-footer {
            display: flex;
            justify-content: space-between;
            padding: 16px;
            background-color: #f1f1f1;
        }

        .property-details {
            display: flex;
            gap: 30px;
            align-items: center;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 1.5rem;
            color: #FC7B97;
        }

        .icon {
            width: 40px;
            height: 40px;
        }
    </style>

</head>

<body>
    <div class="container">
        <div class="logo-section">
            <a href="https://vangoo.mx">
                <img src="https://www.vangoo.mx/assets/img/system/new_logo.png" alt="Vangoo Logo" class="logo">
            </a>
            <a href="https://www.vangoo.mx/detailslots/lots/<?php echo $id; ?>"><button class="btn">Ver detalles del lote</button></a>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="image-container">
                    <img src="https://dashboard.vangoo.mx/storage/img/posts/<?php echo $typeText; ?>/<?php echo $data['id']; ?>/1.webp?height=250&width=400" alt="Property" class="property-image" onerror="this.onerror=null;this.src='https://www.vangoo.mx/assets/img/img404.jpg?height=250&width=400';">
                </div>
            </div>

            <div class="card-content">
                <h3 class="property-title"><?php echo $data['title']; ?></h3>
                <p class="property-price"><?php echo moneyFormat($data['price_min']); ?> - <?php echo moneyFormat($data['price_max']); ?></p>
                <p class="property-address"><?php echo $data['location']; ?></p>
            </div>
        </div>
    </div>
</body>

</html>

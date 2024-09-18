<?php
if ($_GET && isset($_GET['id'])) {
    $id = $_GET['id'];
} else {
    echo "El link es incorrecto";
    exit;
}

$typeText = 'properties';
$urlShare = 'https://dashboard.vangoo.mx/share/property/view.php?id=' . $id;

$urlApi = 'https://dashboard.vangoo.mx/ep/getProperty/' . $id;
$curl = curl_init($urlApi);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($curl);
curl_close($curl);
$data = json_decode($response);
$data = json_decode(json_encode($data), true);

if (!$data) {
    echo "No se ha encontrado la propiedad";
    exit;
}
$data = $data[0];

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
    <meta property="image" content="https://dashboard.vangoo.mx/storage/img/posts/<?php echo $typeText; ?>/<?php echo $data['id']; ?>/1.jpg">
    <meta property="image:secure_url" content="https://dashboard.vangoo.mx/storage/img/posts/<?php echo $typeText; ?>/<?php echo $data['id']; ?>/1.jpg">
    <meta property="url" content="<?php echo $urlShare; ?>">
    <link rel="canonical" href="<?php echo $urlShare; ?>">

    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo $data['title']; ?>">
    <meta property="og:description" content="<?php echo $data['description']; ?>">
    <meta property="og:image" content="https://dashboard.vangoo.mx/storage/img/posts/<?php echo $typeText; ?>/<?php echo $data['id']; ?>/1.jpg">
    <meta property="og:image:secure_url" content="https://dashboard.vangoo.mx/storage/img/posts/<?php echo $typeText; ?>/<?php echo $data['id']; ?>/1.jpg">
    <meta property="og:image:type" content="image/jpeg">
    <meta property="og:image:width" content="400">
    <meta property="og:image:height" content="300">
    <meta property="og:url" content="<?php echo $urlShare; ?>">
    <meta property="fb:app_id" content="7865680626775570">

    <script src="https://kit.fontawesome.com/e0df5df9e9.js" crossorigin="anonymous"></script>

    <style>
        * {
            box-sizing: border-box;
            font-family: 'roboto';
        }

        body {
            margin: 0;
            width: 100%;
            height: 100vh;
        }

        .main {
            width: 100%;
            height: 100%;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .title {
            display: flex;
            flex-direction: column;
            gap: 10px;
            align-items: center;
            margin-bottom: 10px;
        }

        .propertyCard {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 350px;
            margin: 20px;
            transition: box-shadow 0.3s ease-in-out;
        }

        .propertyCard:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .propertyCard img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-bottom: 1px solid #e0e0e0;
        }

        .propertyCard h3 {
            font-size: 1.2rem;
            font-weight: bold;
            margin: 10px 0 5px;
            color: #333;
        }

        .propertyCard .price {
            font-size: 1.2rem;
            font-weight: bold;
            color: #ff4081;
        }

        .propertyCard p {
            color: #666;
            margin: 5px 0;
            font-size: 0.9rem;
        }

        .propertyCard .map-location {
            font-size: 0.8rem;
            color: #ff4081;
            margin-left: 5px;
        }

        .propertyCard .icons {
            padding: 10px 15px;
        }

        .propertyCard .icons div {
            display: flex;
            align-items: center;
            font-size: 0.9rem;
        }

        .propertyCard .icons img {
            width: 20px;
            height: 20px;
        }

        .gap-1 {
            gap: 8px;
        }

        .button-primary {
            background-color: #FC7B97;
            border: 0;
            font-size: 20px;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 25px;
            color: white;
            cursor: pointer;
        }

        .button-secondary {
            background-color: #75D5C5;
            border: 0;
            font-size: 14px;
            font-weight: 500;
            padding: 6px 14px;
            border-radius: 25px;
            color: black;
            cursor: pointer;
        }

        .button-secondary:hover,
        .button-primary:hover {
            opacity: 0.7;
        }

        .share {
            margin-top: 20px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: center;
        }
    </style>

</head>

<body>

    <div class="main">

        <div class="title">
            <img src="https://vangoo.mx/assets/img/system/logo.webp" height="80px">
            <a href="https://vangoo.mx/details/propiedad/<?php echo $id; ?>"><button class="button-primary">Ver detalles de la propiedad</button></a>
        </div>

        <!----------CARD----------->
        <div class="propertyCard">
            <div class="element-2">
                <div class="propertyCard d-flex justify-content-center align-items-center h-100">
                    <div class="content d-flex flex-column m-0 cursor-pointer">
                        <a>
                            <div class="content-image m-0"><img alt="house" loading="lazy" src="https://dashboard.vangoo.mx/storage/img/posts/<?php echo $typeText; ?>/<?php echo $data['id']; ?>/1.jpg"></div>
                            <div class="overflow-hidden">
                                <h3><?php echo $data['title']; ?></h3>
                                <span class="d-flex justify-content-between">
                                    <span class="cursor-pointer price"> <?php echo moneyFormat($data['price']); ?></span>
                                </span>
                                <br>
                            </div>
                            <hr>
                            <div class="icons mb-1 d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-1"><img src="https://www.vangoo.mx/assets/img/system/bed.svg"><span><?php echo $data['rooms']; ?></span></div>
                                <div class="d-flex align-items-center gap-1"><img src="https://www.vangoo.mx/assets/img/system/car.svg"><span><?php echo $data['parkings']; ?></span></div>
                                <div class="d-flex align-items-center gap-1"><img src="https://www.vangoo.mx/assets/img/system/bath.svg"><span><?php echo $data['bathrooms']; ?></span></div>
                                <div class="d-flex align-items-center gap-1"><img src="https://www.vangoo.mx/assets/img/system/house.svg"><span><?php echo $data['area']; ?>m²</span></div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!------------------------->

    </div>

</body>

</html>

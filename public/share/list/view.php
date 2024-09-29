<?php
if ($_GET && isset($_GET['id'])) {
    $id = $_GET['id'];
} else {
    echo "El link es incorrecto";
    exit;
}

$urlShare = 'https://dashboard.vangoo.mx/share/list/view.php?id=' . $id;

$urlApi = 'https://dashboard.vangoo.mx/ep/propertiesFromList/' . $id;
$curl = curl_init($urlApi);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($curl);
curl_close($curl);
$data = json_decode($response);
$data = json_decode(json_encode($data), true);

if (!$data) {
    echo "No se ha encontrado la lista";
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
    <title>Vangoo | <?php echo $data['listdata']['title']; ?></title>
    <link rel="icon" type="image/png" href="https://vangoo.mx/assets/icon/favicon.png">

    <meta name="description" content="<?php echo $data['description']; ?>">
    <meta name="keywords" content="comprar casa, comprar departamento,rentar,rentar casa,comprar nuevo león, rentar casa en nuevo león, comprar casa monterrey,publicar propiedad, buscar propiedades en nuevo león, buscar departamentos,sitio para vivir, lugar para vivir,encontrar dónde vivir, vender propiedades en nuevo león,comisión por venta de propiedad,propiedades destacados nuevo león,desarrollos inmobiliarios">
    <meta property="image" content="https://dashboard.vangoo.mx/share/list/preview.jpg">
    <meta property="image:secure_url" content="https://dashboard.vangoo.mx/share/list/preview.jpg">
    <meta property="url" content="<?php echo $urlShare; ?>">
    <link rel="canonical" href="<?php echo $urlShare; ?>">

    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo $data['listdata']['title']; ?>">
    <meta property="og:description" content="<?php echo $data['description']; ?>">
    <meta property="og:image" content="https://dashboard.vangoo.mx/share/list/preview.jpg">
    <meta property="og:image:secure_url" content="https://dashboard.vangoo.mx/share/list/preview.jpg">
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

        .slider-container {
            position: relative;
            width: 100%;
            max-width: 1200px;
            overflow: hidden;
            margin: 0 auto;
        }

        .slider {
            display: flex;
            transition: transform 0.3s ease-in-out;
        }

        .card {
            min-width: calc(100% / 5);
            /* Para mostrar 5 tarjetas */
            margin: 0 10px;
        }

        .prev,
        .next {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(0, 0, 0, 0.5);
            border: none;
            color: white;
            padding: 10px;
            cursor: pointer;
            z-index: 10;
        }

        .prev {
            left: 0;
        }

        .next {
            right: 0;
        }
    </style>

</head>

<body>
    <div class="container">
        <div class="logo-section">
            <img src="https://www.vangoo.mx/assets/img/system/new_logo.png" alt="Vangoo Logo" class="logo">
            <a href="https://vangoo.mx/listdetails/<?php echo $id; ?>"><button class="btn">Ver lista completa</button></a>
        </div>

        <div class="slider-container">
            <div class="slider">

                <!-- Verificar si hay propiedades -->
                <?php if (!empty($data['properties'])) : ?>
                    <!-- Propiedades -->
                    <?php foreach ($data['properties'] as $property) : ?>
                        <div class="card">
                            <div class="card-header">
                                <div class="image-container">
                                    <img src="<?php echo $property['images'][0]; ?>" alt="Property" class="property-image">
                                </div>
                            </div>
                            <div class="card-content">
                                <h3 class="property-title"><?php echo $property['title']; ?></h3>
                                <p class="property-price"><?php echo moneyFormat($property['price']); ?></p>
                                <p class="property-address"><?php echo $property['location']; ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Verificar si hay desarrollos -->
                <?php if (!empty($data['developments'])) : ?>
                    <!-- Desarrollos -->
                    <?php foreach ($data['developments'] as $development) : ?>
                        <div class="card">
                            <div class="card-header">
                                <div class="image-container">
                                    <img src="<?php echo $development['images'][0]; ?>" alt="Development" class="property-image">
                                </div>
                            </div>
                            <div class="card-content">
                                <h3 class="property-title"><?php echo $development['title']; ?></h3>
                                <p class="property-price"><?php echo moneyFormat($development['price_min']); ?> - <?php echo moneyFormat($development['price_max']); ?></p>
                                <p class="property-address"><?php echo $development['location']; ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Verificar si hay lotes -->
                <?php if (!empty($data['lots'])) : ?>
                    <!-- Lotes -->
                    <?php foreach ($data['lots'] as $lot) : ?>
                        <div class="card">
                            <div class="card-header">
                                <div class="image-container">
                                    <img src="<?php echo $lot['images'][0]; ?>" alt="Lot" class="property-image">
                                </div>
                            </div>
                            <div class="card-content">
                                <h3 class="property-title"><?php echo $lot['title']; ?></h3>
                                <p class="property-price"><?php echo moneyFormat($lot['price_min']); ?> - <?php echo moneyFormat($lot['price_max']); ?></p>
                                <p class="property-address"><?php echo $lot['location']; ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

            </div>
            <button class="prev">⟨</button>
            <button class="next">⟩</button>
        </div>
    </div>
</body>

<script>
    const slider = document.querySelector('.slider');
    let currentPosition = 0;
    const totalCards = document.querySelectorAll('.card').length;
    const visibleCards = 5;

    document.querySelector('.next').addEventListener('click', () => {
        if (currentPosition > -(totalCards - visibleCards)) {
            currentPosition--;
            slider.style.transform = `translateX(${currentPosition * (100 / visibleCards)}%)`;
        }
    });

    document.querySelector('.prev').addEventListener('click', () => {
        if (currentPosition < 0) {
            currentPosition++;
            slider.style.transform = `translateX(${currentPosition * (100 / visibleCards)}%)`;
        }
    });
</script>

</html>

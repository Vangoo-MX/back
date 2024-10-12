<?php

if ($_GET && isset($_GET['title'], $_GET['property'], $_GET['development'], $_GET['lot'])) {
    $title = $_GET['title'];
    $propertyIds = array_filter(explode('-', $_GET['property']), function ($id) {
        return $id !== '0';
    });
    $developmentIds = array_filter(explode('-', $_GET['development']), function ($id) {
        return $id !== '0';
    });
    $lotIds = array_filter(explode('-', $_GET['lot']), function ($id) {
        return $id !== '0';
    });
} else {
    echo 'No se encontraron datos';
    exit;
}

$urlShare = 'https://dashboard.vangoo.mx/share/local/view.php?title=' . $title . '&property=' . $_GET['property'] . '&development=' . $_GET['development'] . '&lot=' . $_GET['lot'];

$propertyData = [];
$developmentData = [];
$lotData = [];
$titleFormatted = str_replace('-', ' ', $title);

if (!empty($propertyIds)) {
    foreach ($propertyIds as $propertyId) {
        $urlApi = 'https://dashboard.vangoo.mx/ep/getProperty/' . $propertyId;
        $curl = curl_init($urlApi);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        $property = json_decode($response, true);

        if (!empty($property) && isset($property[0])) {
            $propertyData[] = $property[0];
        } else {
            echo 'No se encontraron propiedades';
            exit;
        }
    }
}

if (!empty($developmentIds)) {
    foreach ($developmentIds as $developmentId) {
        $urlApi = 'https://dashboard.vangoo.mx/ep/getDevelopment/' . $developmentId;
        $curl = curl_init($urlApi);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        $development = json_decode($response, true);

        if (!empty($development) && isset($development[0])) {
            $developmentData[] = $development[0];
        } else {
            echo 'No se encontraron desarrollos';
            exit;
        }
    }
}

if (!empty($lotIds)) {
    foreach ($lotIds as $lotId) {
        $urlApi = 'https://dashboard.vangoo.mx/ep/getLot/' . $lotId;
        $curl = curl_init($urlApi);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        $lot = json_decode($response, true);

        if (!empty($lot) && isset($lot[0])) {
            $lotData[] = $lot[0];
        } else {
            echo 'No se encontraron terrenos';
            exit;
        }
    }
}

if (empty($propertyData) && empty($developmentData) && empty($lotData)) {
    echo 'No se encontraron propiedades, desarrollos o terrenos';
    exit;
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
    <title>Vangoo | <?php echo htmlspecialchars($titleFormatted); ?></title>
    <link rel="icon" type="image/png" href="https://vangoo.mx/assets/icon/favicon.png">

    <meta name="description" content="Mi lista de favoritos compartida desde Vangoo">
    <meta name="keywords" content="comprar casa, comprar departamento,rentar,rentar casa,comprar nuevo león, rentar casa en nuevo león, comprar casa monterrey,publicar propiedad, buscar propiedades en nuevo león, buscar departamentos,sitio para vivir, lugar para vivir,encontrar dónde vivir, vender propiedades en nuevo león,comisión por venta de propiedad,propiedades destacados nuevo león,desarrollos inmobiliarios">
    <meta property="image" content="https://dashboard.vangoo.mx/share/list/preview.jpg">
    <meta property="image:secure_url" content="https://dashboard.vangoo.mx/share/list/preview.jpg">
    <meta property="url" content="<?php echo $urlShare; ?>">
    <link rel="canonical" href="<?php echo $urlShare; ?>">

    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo htmlspecialchars($titleFormatted); ?>">
    <meta property="og:description" content="Mi lista de favoritos compartida desde Vangoo">
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
            width: 380px;
            margin-right: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            flex-shrink: 0;
        }

        .image-container {
            position: relative;
        }

        .property-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
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

        .slider-container {
            position: relative;
            width: 100%;
            max-width: calc(380px * 5 + 30px);
            overflow: hidden;
            margin: 0 auto;
        }

        .slider {
            display: flex;
            transition: transform 0.3s ease-in-out;
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

<body>
    <div class="container">
        <div class="logo-section">
            <a href="https://vangoo.mx">
                <img src="https://www.vangoo.mx/assets/img/system/new_logo.png" alt="Vangoo Logo" class="logo">
            </a>
            <a href="https://www.vangoo.mx/sharesearch/<?php echo $title; ?>/<?php echo $_GET['property']; ?>/<?php echo $_GET['development']; ?>/<?php echo $_GET['lot']; ?>"><button class="btn">Ver lista completa</button></a>
        </div>

        <div class="slider-container">
            <div class="slider">
                <?php if (!empty($propertyData)) : ?>
                    <?php foreach ($propertyData as $property) : ?>
                        <div class="card">
                            <div class="image-container">
                                <img src="https://dashboard.vangoo.mx/storage/img/posts/properties/<?php echo $property['id']; ?>/1.jpg?height=250&width=400" alt="Property" class="property-image">
                            </div>
                            <div class="card-content">
                                <h3 class="property-title"><?php echo $property['title']; ?></h3>
                                <p class="property-price"><?php echo moneyFormat($property['price']); ?></p>
                                <p class="property-address"><?php echo $property['location']; ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <?php if (!empty($developmentData)) : ?>
                    <?php foreach ($developmentData as $development) : ?>
                        <div class="card">
                            <div class="card-header">
                                <div class="image-container">
                                    <img src="https://dashboard.vangoo.mx/storage/img/posts/developments/<?php echo $development['id']; ?>/1.jpg?height=250&width=400" alt="Development" class="property-image">
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

                <?php if (!empty($lotData)) : ?>
                    <?php foreach ($lotData as $lot) : ?>
                        <div class="card">
                            <div class="card-header">
                                <div class="image-container">
                                    <img src="https://dashboard.vangoo.mx/storage/img/posts/lots/<?php echo $lot['id']; ?>/1.jpg?height=250&width=400" alt="Lot" class="property-image">
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

    <script>
        const slider = document.querySelector('.slider');
        const cards = document.querySelectorAll('.card');
        let currentPosition = 0;
        const visibleCards = 6;
        const totalCards = Math.min(cards.length, visibleCards);
        const cardWidth = 380 + 10;

        document.querySelector('.next').addEventListener('click', () => {
            if (currentPosition > -(totalCards - visibleCards)) {
                currentPosition--;
                slider.style.transform = `translateX(${currentPosition * cardWidth}px)`;
            }
        });

        document.querySelector('.prev').addEventListener('click', () => {
            if (currentPosition < 0) {
                currentPosition++;
                slider.style.transform = `translateX(${currentPosition * cardWidth}px)`;
            }
        });
    </script>
</body>

</html>

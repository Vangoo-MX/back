<?php
if ($_GET && isset($_GET['id'])) {
    $uuid = $_GET['id'];
} else {
    echo "El link es incorrecto";
    exit;
}

$urlShare = 'https://dashboard.vangoo.mx/share/portfolio/view.php?id=' . $uuid;

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
];

$allProperties = [];

foreach ($propertyTypes as $type) {
    $apiUrl = "https://dashboard.vangoo.mx/api/{$type}/user/{$userId}";
    $curl = curl_init($apiUrl);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);

    $properties = json_decode($response, true);

    if (!is_array($properties)) {
        continue;
    }

    if (!isset($properties[0]) && !empty($properties)) {
        $properties = [$properties];
    }

    foreach ($properties as $p) {
        $p['property_type'] = $type;
        $allProperties[] = $p;
    }
}

$data = [
    'user' => $userData,
    'properties' => $allProperties
];

function moneyFormat($numero)
{
    $formatted = number_format($numero, 2, '.', ',');
    return '$' . $formatted . ' MXN';
}

function getImageUrl($type, $propertyId, $imageName)
{
    $baseUrl = "https://dashboard.vangoo.mx/storage/img/posts/";

    $folders = [
        'property' => 'properties',
        'apartment' => 'apartments',
        'terrain' => 'terrains'
    ];

    if (isset($folders[$type])) {
        return $baseUrl . $folders[$type] . '/' . $propertyId . '/' . $imageName;
    }

    return "https://www.vangoo.mx/assets/img/img404.jpg?height=300&width=400";
}

function getBadgeText($type)
{
    $types = [
        'property' => 'Casa',
        'apartment' => 'Departamento',
        'terrain' => 'Terreno'
    ];

    return $types[$type] ?? 'Propiedad';
}

function getDetailsUrl($type, $id)
{
    $urls = [
        'property' => 'https://www.vangoo.mx/details/propiedad/',
        'apartment' => 'https://www.vangoo.mx/detailsDepa/apartments/',
        'terrain' => 'https://www.vangoo.mx/detailsTerrain/terrains/'
    ];

    return $urls[$type] . $id;
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vangoo | Portafolio de Propiedades - <?= htmlspecialchars($userData['name']) ?></title>
    <link rel="icon" type="image/png" href="https://vangoo.mx/assets/icon/favicon.png">

    <meta name="description" content="Mi portafolio de propiedades en Vangoo. Encuentra mi información de contacto y las propiedades que tengo disponibles para compra o renta.">
    <meta name="keywords" content="comprar casa, comprar departamento,rentar,rentar casa,comprar nuevo león, rentar casa en nuevo león, comprar casa monterrey,publicar propiedad, buscar propiedades en nuevo león, buscar departamentos,sitio para vivir, lugar para vivir,encontrar dónde vivir, vender propiedades en nuevo león,comisión por venta de propiedad,propiedades destacados nuevo león,desarrollos inmobiliarios">
    <meta property="image" content="https://dashboard.vangoo.mx/share/portfolio/preview.jpg">
    <meta property="image:secure_url" content="https://dashboard.vangoo.mx/share/portfolio/preview.jpg">
    <meta property="url" content="<?php echo $urlShare; ?>">
    <link rel="canonical" href="<?php echo $urlShare; ?>">

    <meta property="og:type" content="website">
    <meta property="og:title" content="Portafolios de <?php echo $data['user']['name']; ?>">
    <meta property="og:description" content="Mi portafolio de propiedades en Vangoo. Encuentra mi información de contacto y las propiedades que tengo disponibles para compra o renta.">
    <meta property="og:image" content="https://dashboard.vangoo.mx/share/portfolio/preview.jpg">
    <meta property="og:image:secure_url" content="https://dashboard.vangoo.mx/share/portfolio/preview.jpg">
    <meta property="og:image:type" content="image/jpeg">
    <meta property="og:image:width" content="400">
    <meta property="og:image:height" content="300">
    <meta property="og:url" content="<?php echo $urlShare; ?>">
    <meta property="fb:app_id" content="7865680626775570">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://kit.fontawesome.com/e0df5df9e9.js" crossorigin="anonymous"></script>

    <style>
        .bg-gray-50 {
            background-color: #f9fafb;
        }

        .text-pink {
            color: #FC7B97;
        }

        .bg-pink {
            background-color: #FC7B97;
        }

        .property-card {
            transition: all 0.3s ease;
            height: 100%;
        }

        .property-card:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            transform: scale(1.03);
            transition: all 0.3s ease-in-out;
        }

        .object-fit-cover {
            object-fit: cover;
        }

        .contact-cta {
            background-image: linear-gradient(to right, #FC7B97, #fc6b8a);
        }

        .text-truncate-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .profile-img {
            width: 128px;
            height: 128px;
            border: 4px solid white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .property-image {
            height: 220px;
            object-fit: cover;
        }

        .no-properties {
            padding: 40px 20px;
            text-align: center;
            background-color: white;
            border-radius: 8px;
        }

        .section-title {
            position: relative;
            margin-bottom: 30px;
            padding-bottom: 15px;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: #FC7B97;
            border-radius: 2px;
        }

        .property-badge {
            font-size: 0.85rem;
            padding: 5px 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .whatsapp-btn {
            transition: all 0.3s;
        }

        .whatsapp-btn:hover {
            background-color: #25D366 !important;
            border-color: #25D366 !important;
            color: white !important;
        }

        .card-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 2;
            cursor: pointer;
        }
    </style>

<body class="bg-gray-50">
    <div class="container-fluid px-4 py-5">
        <div class="card mb-5 border-0 shadow-sm">
            <div class="card-body p-4 d-flex flex-column flex-sm-row align-items-center gap-4">
                <div class="rounded-circle overflow-hidden shadow profile-img">
                    <img src="https://dashboard.vangoo.mx/storage/img/users/<?= htmlspecialchars($userData['profile_image']) ?>"
                        alt="<?= htmlspecialchars($userData['name']) ?>"
                        class="w-100 h-100 object-fit-cover">
                </div>
                <div class="text-center text-sm-start flex-grow-1">
                    <h1 class="fw-bold text-dark fs-2 mb-3"><?= htmlspecialchars($userData['name']) ?></h1>
                    <p class="text-muted mb-4"><?= htmlspecialchars($userData['biography']) ?></p>
                    <div class="row row-cols-1 row-cols-sm-2 gy-2">
                        <div><span class="text-muted">Email: </span><span class="text-dark"><?= htmlspecialchars($userData['email']) ?></span></div>
                        <div><span class="text-muted">Teléfono: </span><span class="text-dark"><?= htmlspecialchars($userData['tel']) ?></span></div>
                        <div><span class="text-muted">Contacto: </span><span class="text-dark"><?= htmlspecialchars($userData['contact_preference']) ?></span></div>
                        <div><span class="text-muted">Horario: </span><span class="text-dark"><?= htmlspecialchars($userData['contact_schedule']) ?></span></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-5 border-0 shadow-sm">
            <div class="card-body p-4">
                <h2 class="text-center fw-bold text-dark fs-3 mb-5 section-title">Mi Portafolio de Propiedades</h2>

                <?php if (empty($allProperties)): ?>
                    <div class="no-properties">
                        <i class="bi bi-house-x text-pink" style="font-size: 3rem;"></i>
                        <h3 class="mt-3">No hay propiedades disponibles</h3>
                        <p class="text-muted">Este usuario aún no ha añadido propiedades a su portafolio</p>
                    </div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($allProperties as $property): ?>
                            <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                                <div class="card h-100 shadow-sm property-card" data-url="<?= getDetailsUrl($property['property_type'], $property['id']) ?>">
                                    <div class="card-overlay"></div>
                                    <div class="position-relative">
                                        <?php if (!empty($property['images'][0])): ?>
                                            <img src="<?= getImageUrl($property['property_type'], $property['id'], $property['images'][0]) ?>"
                                                class="card-img-top property-image"
                                                alt="<?= htmlspecialchars($property['title']) ?>">
                                        <?php else: ?>
                                            <div class="bg-light d-flex align-items-center justify-content-center property-image">
                                                <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                            </div>
                                        <?php endif; ?>
                                        <span class="badge bg-pink position-absolute top-0 start-0 m-2 property-badge">
                                            <?= getBadgeText($property['property_type']) ?>
                                        </span>
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title text-truncate-2"><?= htmlspecialchars($property['title']) ?></h5>
                                        <p class="text-pink fw-bold fs-5 mb-2"><?= moneyFormat($property['price']) ?></p>
                                        <p class="text-muted small mb-2">
                                            <i class="bi bi-geo-alt-fill me-1"></i>
                                            <?= htmlspecialchars($property['location']) ?>
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <span class="text-muted small">
                                                <i class="bi bi-calendar me-1"></i>
                                                <?= date('d M Y', strtotime($property['created_at'])) ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="text-center">
            <div class="card text-white contact-cta border-0 shadow">
                <div class="card-body py-5">
                    <h3 class="fw-bold fs-3 mb-3">¿Interesado en alguna propiedad?</h3>
                    <p class="opacity-85 mb-4">Contáctame para más información y agenda una visita</p>
                    <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                        <?php
                        $phone = preg_replace('/[^0-9]/', '', $userData['tel']);
                        $whatsappLink = "https://wa.me/{$phone}?text=" .
                            urlencode("Hola " . $userData['name'] . ", estoy interesado en una propiedad de tu portafolio.");
                        ?>
                        <a href="<?= $whatsappLink ?>" target="_blank"
                            class="btn btn-light text-pink fw-semibold px-4 py-2 whatsapp-btn">
                            <i class="bi bi-whatsapp me-2"></i> Contactar por WhatsApp
                        </a>
                        <a href="mailto:<?= $userData['email'] ?>" target="_blank"
                            class="btn btn-outline-light fw-semibold px-4 py-2">
                            <i class="bi bi-envelope me-2"></i> Enviar Email
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="py-4 text-center text-muted small">
        <div class="container">
            <p>© <?= date('Y') ?> Vangoo - Todos los derechos reservados</p>
            <p class="mb-0">Este portafolio es generado automáticamente</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.querySelectorAll('.property-card').forEach(card => {
            card.addEventListener('click', function(e) {
                if (e.target.closest('.property-badge')) return;

                const url = this.getAttribute('data-url');
                if (url) {
                    window.open(url, '_blank');
                }
            });
        });
    </script>
</body>

</html>

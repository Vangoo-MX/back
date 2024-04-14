<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>@yield('title')</title>
        <meta name="description" content="">
        <link rel="icon" type="image/png" href="../resources/img/favicon.png">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="../resources/css/app.css" rel="stylesheet">
        <link href="../resources/css/admin.css" rel="stylesheet">
        <!--------FONTAWESOME--------->
        <script src="https://kit.fontawesome.com/e0df5df9e9.js" crossorigin="anonymous"></script>
        <!-- Latest compiled and minified CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Latest compiled JavaScript -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.3.min.js" integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
        <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    </head>
    <body>
        @yield('content')

        <!------JS------>
        <script src="../resources/js/jquery-easing/jquery.easing.min.js"></script>
        <script src="../resources/js/admin.js"></script>
        <script src="../resources/js/chart.js/Chart.min.js"></script>
        <script src="../resources/js/chart-area-demo.js"></script>
        <script src="../resources/js/chart-pie-demo.js"></script>
        <!------/JS------>
        
    </body>
</html>
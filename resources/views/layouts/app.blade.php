<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name', 'StreetPOS') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #fff3e0;
            color: #333;
        }

        .container {
            padding: 20px;
            max-width: 1000px;
            margin: auto;
        }

        /* Buttons */
        .btn-maroon {
            background-color: #800000;
            color: #ffcc00;
            border-radius: 5px;
            border: none;
        }

        .btn-maroon:hover {
            opacity: 0.85;
            color: #fff;
        }

        /* Tables */
        table {
            margin-top: 15px;
        }

        table th, table td {
            border: 1px solid #800000 !important;
            padding: 10px;
            text-align: left;
        }

        /* Inputs */
        input[type="text"], input[type="number"], input[type="email"], input[type="password"], select {
            padding: 8px;
            width: 100%;
            border: 1px solid #800000;
            border-radius: 5px;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        /* Cards */
        .card {
            border: 1px solid #800000;
            border-radius: 8px;
        }

        /* QR Code */
        .qr-wrapper {
            margin-top: 30px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Street Food POS</h1>
    </div>

    @yield('content')

    <!-- QR Code for Mobile Access -->
    <div class="qr-wrapper">
        <h5>Scan to Access</h5>
        {!! QrCode::size(200)->color(128,0,0)->backgroundColor(255,255,255)->generate(url('/')) !!}
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

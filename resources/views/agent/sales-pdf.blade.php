<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ strtoupper($type) }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
            margin: 0;
            padding: 0;
        }

        header, footer {
            width: 100%;
            text-align: center;
            position: fixed;
        }

        header {
            top: 0;
            padding: 10px 0;
        }

        footer {
            bottom: 0;
            font-size: 10px;
            color: #555;
            border-top: 1px solid #ccc;
            padding: 5px 0;
        }

        .logo {
            float: left;
            width: 120px;
            height: auto;
        }

        .company-info {
            float: right;
            text-align: right;
        }

        .clearfix {
            clear: both;
        }

        .content {
            margin: 140px 20px 100px 20px; /* top, sides, bottom for header/footer */
        }

        /* Customer Card */
        .customer-card {
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 5px;
            background-color: #f9f9f9;
            margin-bottom: 20px;
        }

        .customer-card p {
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }

        .total-row td {
            font-weight: bold;
        }

        .agent-info {
            text-align: right;
            margin-top: 40px;
        }

        .agent-info p {
            margin: 5px 0;
        }
    </style>
</head>
<body>

<header>
    <img src="{{ public_path('images/logo.png') }}" class="logo" alt="Logo">
    <div class="company-info">
        <h2>{{ strtoupper($type) }}</h2>
        <p>Date : {{ now()->format('d/m/Y') }}</p>
    </div>
    <div class="clearfix"></div>
</header>

<footer>
    <p>Document généré par le système de gestion | {{ strtoupper($type) }}</p>
</footer>

<div class="content">

    <!-- Customer Info Card -->
    <div class="customer-card">
        <strong>Client :</strong>
        <p>Nom : {{ $customer->customer_name ?? 'Nom client manquant' }}</p>
        @if(!empty($customer->customer_email))
            <p>Email : {{ $customer->customer_email }}</p>
        @endif
        @if(!empty($customer->customer_phone))
            <p>Téléphone : {{ $customer->customer_phone }}</p>
        @endif
        @if(!empty($customer->customer_address))
            <p>Adresse : {{ $customer->customer_address }}</p>
        @endif
    </div>

    <!-- Products Table -->
    <table>
        <thead>
            <tr>
                <th>Produit</th>
                <th>Quantité</th>
                <th>Prix Unitaire</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lines as $line)
                <tr>
                    <td>{{ $line['product']->name ?? $line['name'] }}</td>
                    <td>{{ $line['quantity'] }}</td>
                    <td>{{ number_format($line['unit_price'], 2) }} €</td>
                    <td>{{ number_format($line['total'], 2) }} €</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="3">TOTAL</td>
                <td>{{ number_format($grandTotal, 2) }} €</td>
            </tr>
        </tbody>
    </table>

    <!-- Agent Info -->
    <div class="agent-info">
        <p>Agent : <strong>{{ $agent->name }}</strong></p><br>
        <p>Signature : ____________________</p>
        <p>Date : {{ now()->format('d/m/Y') }}</p>
    </div>

    @if($type === 'proforma')
        <p style="margin-top:20px;"><em>Document sans valeur comptable (Proforma).</em></p>
    @endif

</div>

</body>
</html>

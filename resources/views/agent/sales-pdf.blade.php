<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ strtoupper($type) }}</title>

    <style>
        @page {
            margin: 0;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
            margin: 0;
            padding: 20px;
        }

        /* ================= HEADER ================= */
        header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            width: 100%;
            padding: 20px;
        }

        .header-image {
            width: 100%;
            height: 160px; /* adjust if needed */
            object-fit: cover;
            display: block;
        }

        .header-text {
            text-align: center;
            padding: 10px 0;
        }

        /* ================= FOOTER ================= */
        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #555;
            border-top: 1px solid #ccc;
            padding: 6px 0;
        }

        /* ================= CONTENT ================= */
        .content {
            margin: 230px 20px 100px 20px;
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

<!-- ================= HEADER ================= -->
<header>
    <img src="{{ public_path('photos/entete.png') }}" class="header-image" alt="Header Image">

    <div class="header-text">
        <h2>{{ strtoupper($type) }}</h2> 
    </div>
</header>

<!-- ================= FOOTER ================= -->
<footer>
    <p>Document généré par le système de gestion</p>
    <i>Document généré par le système de gestion</i>
</footer>

<!-- ================= CONTENT ================= -->
<div class="content">

    <!-- Customer Info -->
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
        <p>Agent : <strong>{{ $agent->name }}</strong></p><br><br>
        <p>Signature : ____________________</p>
        <p>Date : {{ now()->format('d/m/Y') }}</p>
    </div>

    @if($type === 'proforma')
        <p style="margin-top:20px;">
            <em>Document sans valeur comptable (Proforma).</em>

        </p>
    @endif

</div>

</body>
</html>

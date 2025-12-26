<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste Paiements - {{ $bank->name }} - {{ $month }}</title>
    <style>
        @page {
            margin: 30px 20px 100px 20px; /* top right bottom left */
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            color: #000;
        }

        header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 150px;
            text-align: center;
        }

        .header-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 40px;
            text-align: center;
            font-size: 10px;
            color: #555;
            border-top: 1px solid #ccc;
            line-height: 20px;
        }

        .content {
            margin: 300px 20px 0 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
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
            text-align: right;
        }

        .total-words {
            margin-top: 10px;
            font-style: italic;
        }

        tr {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>

<header>
    <img src="{{ public_path('photos/entete.png') }}" class="header-image" alt="Header Image">
    <h2>Liste Paiements - {{ $bank->name }}</h2>
    @php
        $monthNumber = \Carbon\Carbon::parse($month)->format('m');
        $year = \Carbon\Carbon::parse($month)->format('Y');
        $monthName = [
            '01'=>'Janvier','02'=>'Février','03'=>'Mars','04'=>'Avril',
            '05'=>'Mai','06'=>'Juin','07'=>'Juillet','08'=>'Août',
            '09'=>'Septembre','10'=>'Octobre','11'=>'Novembre','12'=>'Décembre'
        ][$monthNumber];

        if (!function_exists('convertNumberToWords')) {
            function convertNumberToWords($number) {
                $formatter = new \NumberFormatter('fr', \NumberFormatter::SPELLOUT);
                return $formatter->format($number);
            }
        }
    @endphp
    <p>Mois : {{ $monthName }} {{ $year }}</p>
</header>

<footer>
    <p>Document généré par le système de gestion</p>
</footer>

<div class="content">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nom</th>
                <th>NIN</th>
                <th>Banque / Compte</th>
                <th>Net à Payer (KMF)</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $grandTotal = 0;
                $counter = 1;
            @endphp
            @foreach($payments as $payment)
                @php
                    $employeeSalary = $payment->employee->salary;
                    $igr = match(true) {
                        $employeeSalary <= 70000 => 2000,
                        $employeeSalary <= 80000 => 3000,
                        $employeeSalary <= 100000 => 4000,
                        $employeeSalary <= 110000 => 5000,
                        default => 10000,
                    };
                    $net = ($employeeSalary + $payment->bonus) - $igr;
                    $grandTotal += $net;

                    $accountNumber = $payment->employee->account_number ?? '-';
                @endphp
                <tr>
                    <td>{{ $counter++ }}</td>
                    <td>{{ $payment->employee->first_name }} {{ $payment->employee->last_name }}</td>
                    <td>{{ $payment->employee->nin }}</td>
                    <td>{{ $accountNumber }}</td>
                    <td>{{ number_format($net, 0, ',', ' ') }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4">Total</td>
                <td>{{ number_format($grandTotal, 0, ',', ' ') }}</td>
            </tr>
        </tbody>
    </table>

    <p class="total-words">
        <i>Montant total en lettres : <strong>{{ convertNumberToWords($grandTotal) }}</strong> francs comoriens.</i>
    </p>
</div>

</body>
</html>

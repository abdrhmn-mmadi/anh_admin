<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport des Dépenses - {{ $month }}</title>

    <style>
        @page {
            margin: 30px 20px 100px 20px;
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
            text-align: center;
            font-size: 10px;
            color: #555;
            border-top: 1px solid #ccc;
            padding: 6px 0 20px;
            color: #008000;
            margin-bottom: -100px;
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

@php
    $monthNumber = \Carbon\Carbon::parse($month)->format('m');
    $year = \Carbon\Carbon::parse($month)->format('Y');

    $monthName = \Carbon\Carbon::parse($month)->locale('fr')->translatedFormat('F'); // French month name

    if (!function_exists('convertNumberToWords')) {
        function convertNumberToWords($number) {
            $formatter = new \NumberFormatter('fr', \NumberFormatter::SPELLOUT);
            return $formatter->format($number);
        }
    }
@endphp

<header>
    <img src="{{ public_path('photos/entete.png') }}" class="header-image" alt="Header Image">
    <h2>Rapport des Dépenses</h2>
    <p>Mois : {{ ucfirst($monthName) }} {{ $year }}</p>
</header>

<footer>
   <i>Rue de la COI, Coulée-Yéménia, Moroni, Union des Comores. Tél : +269 733 25 82.</i><br>
   <i>E-mail : contact@anh.km. Site web : https://anh.km</i>
</footer>

<div class="content">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Dépense</th>
                <th>Montant (KMF)</th>
                <th>Date</th>
                <th>Mois</th> <!-- French month column -->
            </tr>
        </thead>
        <tbody>
            @php
                $grandTotal = 0;
                $counter = 1;
            @endphp

            @forelse($depenses as $depense)
                @php
                    $grandTotal += $depense->amount;
                    $depenseMonth = \Carbon\Carbon::parse($depense->created_at)->locale('fr')->translatedFormat('F Y'); // French month
                @endphp
                <tr>
                    <td>{{ $counter++ }}</td>
                    <td>{{ $depense->depense }}</td>
                    <td>{{ number_format($depense->amount, 0, ',', ' ') }}</td>
                    <td>{{ $depense->created_at->format('d/m/Y') }}</td>
                    <td>{{ ucfirst($depenseMonth) }}</td> <!-- Display month in French -->
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align:center;">
                        Aucune dépense enregistrée pour ce mois
                    </td>
                </tr>
            @endforelse

            <tr class="total-row">
                <td colspan="2">Total</td>
                <td colspan="3">{{ number_format($grandTotal, 0, ',', ' ') }}</td>
            </tr>
        </tbody>
    </table>

    <p class="total-words">
        <i>
            Montant total en lettres :
            <strong>{{ convertNumberToWords($grandTotal) }}</strong>
            francs comoriens.
        </i>
    </p>
</div>

</body>
</html>

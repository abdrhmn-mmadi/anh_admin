<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Payslip - {{ $employee->first_name }} {{ $employee->last_name }}</title>

    <style>
        @page { margin: 0; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; margin: 0; padding: 20px; color:#000; }

        header { position: fixed; top: 0; left:0; right:0; width:100%; padding:20px; }
        .header-image { width:100%; height:160px; object-fit: cover; }
        .header-text { text-align:center; padding:10px 0; }

        footer { position: fixed; bottom:0; left:0; right:0; text-align:center; font-size:10px; color:#555; border-top:1px solid #ccc; padding:6px 0; }

        .content { margin: 250px 20px 100px 20px; }

        .card { border:1px solid #ccc; padding:15px; border-radius:5px; background-color:#f9f9f9; margin-bottom:20px;}
        .card p { margin:5px 0; }

        table { width:100%; border-collapse: collapse; margin-bottom:40px; }
        th, td { border:1px solid #000; padding:6px; text-align:left; }
        th { background-color:#f0f0f0; }
        .total-row td { font-weight:bold; }

        .agent-info { text-align:right; margin-top:40px; }
        .agent-info p { margin:5px 0; }
    </style>
</head>

<body>
<header>
    <img src="{{ public_path('photos/entete.png') }}" class="header-image" alt="Header Image">
    <div class="header-text">
        @php
            $monthNumber = \Carbon\Carbon::parse($month)->format('m');
            $year = \Carbon\Carbon::parse($month)->format('Y');
            $monthName = [
                '01'=>'Janvier','02'=>'Février','03'=>'Mars','04'=>'Avril',
                '05'=>'Mai','06'=>'Juin','07'=>'Juillet','08'=>'Août',
                '09'=>'Septembre','10'=>'Octobre','11'=>'Novembre','12'=>'Décembre'
            ][$monthNumber];

            // Determine IGR and Indice
            $salary = $employee->salary;
            $igr = match(true) {
                $salary <= 70000 => 2000,
                $salary <= 80000 => 3000,
                $salary <= 100000 => 4000,
                $salary <= 110000 => 5000,
                default => 10000,
            };

            $indice = match(true) {
                $salary <= 70000 => 100,
                $salary <= 80000 => 200,
                $salary <= 100000 => 300,
                $salary <= 110000 => 400,
                default => 500,
            };

            $total = ($salary + $payment->bonus) - $igr;
            $salaireOrdinaire = $salary + $payment->bonus;
        @endphp
        <h2>FICHE DE PAIE - {{ $monthName }} {{ $year }}</h2> <br><br>
    </div>
</header>

<footer>
    <p>Document généré par le système de gestion</p>
</footer>

<div class="content">

    <!-- Employee Info Card -->
    <div class="card">
        <strong>Employé :</strong>
        <p>Nom : {{ $employee->first_name }} {{ $employee->last_name }}</p>
        <p>NIN : {{ $employee->nin }}</p>
        <p>Adresse : {{ $employee->address ?? '-' }}</p>
        <p>Banque : {{ $employee->bank->name ?? '-' }}</p>
        <p>Région : {{ $employee->region->name ?? '-' }}</p>
    </div>

    <!-- Indice Card -->
    <div class="card">
        <strong>Indice : {{ $indice }}</strong>
    </div>

    <!-- Salary Table -->
    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Montant (KMF)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Salaire de Base</td>
                <td>{{ number_format($employee->salary, 0, ',', ' ') }}</td>
            </tr>
            <tr>
                <td>Bonus</td>
                <td>{{ number_format($payment->bonus, 0, ',', ' ') }}</td>
            </tr>
            <tr class="total-row">
                <td>Salaire Ordonnée</td>
                <td>{{ number_format($salaireOrdinaire, 0, ',', ' ') }}</td>
            </tr>
            <tr>
                <td>IGR</td>
                <td>-{{ number_format($igr, 0, ',', ' ') }}</td>
            </tr>
            <tr class="total-row">
                <td>Net à Payer</td>
                <td>{{ number_format($total, 0, ',', ' ') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Agent Info -->
    <div class="agent-info">
        <p>Généré par : {{ auth()->user()->name }}</p>
        <p>Signature : ____________________</p>
        <p>Date : {{ now()->format('d/m/Y') }}</p>
    </div>

</div>
</body>
</html>

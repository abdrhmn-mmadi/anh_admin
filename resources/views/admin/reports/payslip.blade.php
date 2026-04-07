<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche de paie - {{ $employee->first_name }} {{ $employee->last_name }}</title>

    <style>
        @page { margin: 0; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; margin: 0; padding: 15px; color:#000; }

        header { position: fixed; top: 0; left:0; right:0; width:100%; padding:10px 15px; }
        .header-image { width:95%; height:235px; object-fit: cover;}
        .header-text { text-align:center; padding:5px 0; }

        footer {
            position: fixed;
            bottom: 0;
            left: 0; 
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #008000;
            border-top: 1px solid #ccc;
            padding: 4px 0 10px;
        }

        .content { margin: 150px 15px 60px 15px; }

        .card { border:1px solid #ccc; padding:8px 10px; border-radius:5px; background-color:#f9f9f9; margin-bottom:20px;}
        .card h3 { margin:5px 0 10px 0; font-weight:bold; font-size:13px; }
        .card p { margin:2px 0; font-size:12px; }

        /* Employee info table without borders */
        .employee-table { width:100%; border-collapse: collapse; margin-bottom:5px; }
        .employee-table td { border:none; padding:2px 5px; font-size:12px; }

        table.salary-table { width:100%; border-collapse: collapse; margin-top:5px; }
        table.salary-table th, table.salary-table td { border:1px solid #000; padding:5px; text-align:left; font-size:12px; }
        table.salary-table th { background-color:#f0f0f0; }
        .total-row td { font-weight:bold; }

        .agent-info { text-align:right; margin-top:20px; font-size:12px; }
        .agent-info p { margin:2px 0; }
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

            $salary = $employee->salary;
            $igr = 0;
            $indice = 0;

            if ($salary <= 70000) { $igr = ($salary*0.025)+350; $indice=350; }
            elseif ($salary <= 85000) { $igr = ($salary*0.035)+425; $indice=425; }
            elseif ($salary <= 110000) { $igr = ($salary*0.045)+500; $indice=500; }
            elseif ($salary <= 250000) { $igr = ($salary*0.08); $indice= 0; }
            elseif ($salary <= 300000) { $igr = ($salary*0.11)+1510; $indice=1510; }
            else { $igr = ($salary*0.15)+2633; $indice=2633; }

            $igr = round($igr);
            $total = ($salary + $payment->bonus) - $igr;
            $salaireOrdinaire = $salary + $payment->bonus;
        @endphp
        <h2 style="font-size:16px;">FICHE DE PAIE - {{ $monthName }} {{ $year }}</h2>
    </div>
</header>

<footer>
   <i>Rue de la COI, Coulée-Yéménia, Moroni, Union des Comores. Tél : +269 733 25 82.</i><br>
   <i>E-mail : contact@anh.km. Site web : https://anh.km</i>
</footer>

<div class="content">

<!-- Employee Info Card -->
<div class="card" style="margin-top: 300px;">
    <h3>Informations Employé:</h3>
    <table class="employee-table">
        <tr>
            <td><strong>Nom :</strong> {{ $employee->first_name }} {{ $employee->last_name }}</td>
            <td><strong>Adresse :</strong> {{ $employee->address ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Matricule :</strong> {{ $employee->matricule ?? $employee->employee_id ?? '-' }}</td>
            <td><strong>Région :</strong> {{ $employee->region->name ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>NIN :</strong> {{ $employee->nin ?? '-' }}</td>
            <td><strong>Poste :</strong> {{ $employee->position ?? '-' }}</td>
        </tr>
    </table>
</div>

<!-- Bank Info Card -->
<div class="card" style="padding:6px 8px;">
    <h3>Informations Bancaires:</h3>
    <p><strong>Banque : </strong>{{ $employee->bank->name ?? '-' }}</p>
    <p><strong>Numéro de compte : </strong>{{ $employee->account_number ?? '-' }}</p>
</div>

<!-- Indice Card -->
<div class="card" style="padding:6px 8px;">
    <strong>Indice : {{ $indice }}</strong>
</div>

<!-- Salary Table -->
<table class="salary-table">
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
            <td>Indemnité</td>
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
    <p style="margin-bottom:30px;margin-top:20px;"><strong>LE DIRECTEUR GENERAL</strong></p>
    <p>________________________</p>
    <p>Date : {{ now()->format('d/m/Y') }}</p>
</div>

</div>
</body>
</html>

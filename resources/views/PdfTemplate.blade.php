<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Fichajes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header-table {
            width: 70%;
            border-collapse: collapse;
            margin: 0 auto 20px auto;
        }
        .header-table td {
            padding: 5px;
            vertical-align: top;
        }
        .main-table {
            width: 70%;
            border-collapse: collapse;
            border: 1px solid #000;
            margin: 0 auto;
        }
        .main-table th:first-child, 
        .main-table td:first-child {
            width: 12%;
        }
        .main-table th, .main-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }
        .main-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <!-- Tabla Superior (Datos generales) -->
    <h2 style="text-align: center; margin-bottom: 20px;">Listado Resumen mensual del registro de jornada</h2>
    <table class="header-table">
        <tr>
            <td style="width: 50%;"><strong>Empresa:</strong> Davante</td>
            <td style="width: 50%;"><strong>Trabajador:</strong> {{ $user->name ?? '________________' }}</td>
        </tr>
        <tr>
            <td><strong>Centro de Trabajo:</strong> Medac Arena</td>
            <td><strong>Mes y año:</strong> {{ $month ?? '__' }} / {{ $year ?? '____' }}</td>
        </tr>
    </table>

    <!-- Tabla Inferior (Detalle de registros) -->
    <table class="main-table">
        <thead>
            <tr>
                <th>Días</th>
                <th>Hora de Entrada</th>
                <th>Hora de Salida</th>
                <th>Horas Ordinarias</th>
            </tr>
        </thead>
        <tbody>
            @forelse($entries ?? [] as $entry)
                <tr>
                    <td>{{ $entry['day'] }}</td>
                    <td>{{ $entry['entry_time'] }}</td>
                    <td>{{ $entry['exit_time'] }}</td>
                    <td>{{ $entry['hours'] }}</td>
                </tr>
            @empty
                <!-- Filas de ejemplo si no hay datos -->
                @for($i = 1; $i <= 5; $i++)
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                @endfor
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 30px; width: 70%; margin-left: auto; margin-right: auto;">
        <p style="text-align: right; margin-bottom: 40px;">En _________________, a {{ $currentDate->format('d') }} de {{ $currentDate->translatedFormat('F') }} de {{ $currentDate->year }}</p>
        
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="text-align: center; width: 45%;">
                    <div style="border-bottom: 1px solid black; margin-bottom: 5px; width: 80%; margin-left: auto; margin-right: auto;"></div>
                    <strong>Firma Empresa</strong>
                </td>
                <td style="width: 10%;"></td>
                <td style="text-align: center; width: 45%;">
                    <div style="border-bottom: 1px solid black; margin-bottom: 5px; width: 80%; margin-left: auto; margin-right: auto;"></div>
                    <strong>Firma Trabajador</strong>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>

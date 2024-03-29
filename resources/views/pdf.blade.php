<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="Content-type" content="text/html"; charset="utf-8"/>
        <title>Emploi du temps</title>
    </head>
    <style>
        table {
            border-collapse: collapse;
            border: 2px solid rgb(140 140 140);
            font-family: sans-serif;
            font-size: 0.8rem;
            letter-spacing: 1px;
        }

        caption {
            caption-side: bottom;
            padding: 10px;
            font-weight: bold;
        }

        thead,tfoot {
            background-color: rgb(228 240 245);
        }

        th,td {
            border: 1px solid rgb(160 160 160);
            padding: 8px 10px;
        }

        td:last-of-type {
            text-align: center;
        }

        tbody > tr:nth-of-type(even) {
            background-color: rgb(237 238 242);
        }

        tfoot th {
            text-align: right;
        }

        tfoot td {
            font-weight: bold;
        }
        span{
            display: block;
            text-align: center;
        }
        .container{
            display: flex;
            justify-content: space-between;
            flex-direction: row;
            align-items: flex-start;
        }
        .item{
            display: inline;
            width: 80px;
        }
        .footer{
            font-weight: bold;
            text-align: center;
        }
        .fixed-size{
            width: 90px;
            height: auto;
        }
    </style>
    <body>
        <div>
            <caption style="font-size: 20px">{{ __('Schedule from') }} {{ $debutEdt }} {{ __('to') }} {{ $finEdt }}</caption>
            <caption style="font-size: 20px"> {{ $search }} </caption>
            <table>
                <thead>
                    <tr>
                        <th style="width: 20px">{{ __('Days') }}</th>
                        <th style="width: 90px">07:30-09:00</th>
                        <th>09:00-10:30</th>
                        <th>10:30-12:00</th>
                        <th>13:30-15:00</th>
                        <th>15:00-16:30</th>
                        <th>16:30-18:30</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($joursSemaine as $jours)
                        <tr>
                            <td style="width: 20px"><span style="text-align: center">{{ __($jours) }}</span></td>
                            @foreach ($grilleHoraire[$jours] as $plageHoraire)
                                <td>
                                @if ($jours === 'Monday' && $plageHoraire['heure'] === '07:30-09:00')
                                    <div class="fixed-size">
                                        <span style="display: block; text-align: center;">Levé des</span>
                                        <span style="display: block; text-align: center;">drapeaux et</span>
                                        <span style="display: block text-align: center;">Rassemblement</span>
                                        {{-- {{ __('Levé des drapeaux et Rassemblement') }} --}}
                                    </div>
                                @elseif (strpos($plageHoraire['niveau'], 'IG') !== false)
                                    <div class="container">
                                        <div class="item">
                                            <span>{{ $plageHoraire['niveau'] }}</span>
                                            <span>{{ $plageHoraire['cours'] }}</span>
                                            <span>{{ $plageHoraire['professeur'] }}</span>
                                            <span>S{{ $plageHoraire['salle'] }}</span>
                                        </div>
                                        <div class="item">
                                           <span> </span>
                                           <span> </span>
                                           <span> </span>
                                           <span> </span>
                                        </div>
                                    </div>
                                @elseif (strpos($plageHoraire['niveau'], 'GB') !== false || strpos($plageHoraire['niveau'], 'SR') !== false)
                                    <div class="container">
                                        <div class="item">
                                            <span>{{ $plageHoraire['niveau'] }}</span>
                                            <span>{{ $plageHoraire['cours'] }}</span>
                                            <span>{{ $plageHoraire['professeur'] }}</span>
                                            <span>S{{ $plageHoraire['salle'] }}</span>
                                        </div>
                                        <div class="item">
                                            <span> </span>
                                            <span> </span>
                                            <span> </span>
                                            <span> </span>
                                         </div>
                                    </div>
                                @endif
                        @endforeach
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td class="footer" style="width: 20px">{{ __('Days') }}</td>
                        <td class="footer">07:30-09:00</td>
                        <td class="footer">09:00-10:30</td>
                        <td class="footer">10:30-12:00</td>
                        <td class="footer">13:30-15:00</td>
                        <td class="footer">15:00-16:30</td>
                        <td class="footer">16:30-18:30</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </body>
</html>


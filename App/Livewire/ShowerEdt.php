<?php

namespace App\Livewire;

use App\Models\Emploidutemp;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use DateTime;
use Livewire\Component;

class ShowerEdt extends Component
{
    public $search = "";
    public $isClicked = false;
    public $isGenerate = false;
    public $grilleHoraire;
    public $emploiDuTemps;
    public $joursSemaine = [
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday',
    ];
    public $debutEdt;
    public $finEdt;

    private function getMonday()
    {
        $monday = Emploidutemp::min('DebutCours');
        //mb_convert_encoding($monday, 'UTF-8');
        $mondayDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $monday);
        $this->debutEdt = $mondayDateTime->format('d-m');
        $saturday = Carbon::parse($mondayDateTime)->addDays(5);
        $this->finEdt = $saturday->format('d-m-Y');
    }

    public function generatePdf()
    {
        $this->isGenerate = true;
        $data = [
            'search' => $this->search,
            'isGenerate' => $this->isGenerate,
            'isClicked' => $this->isClicked,
            'grilleHoraire' => $this->grilleHoraire,
            'joursSemaine' => $this->joursSemaine,
            'debutEdt' => $this->debutEdt,
            'finEdt' => $this->finEdt,
        ];

        $pdf = Pdf::loadView('pdf', $data);
        //$pdf->setOption('size', 'a2');
        // return $pdf->download('EmploiduTemps.pdf');
        // return $pdf->download('EmploiduTemps.pdf');
        return response()->streamDownload(function() use ($pdf){
            echo $pdf->stream();
        }, 'emploiDuTemps.pdf');
    }

    private function getDataFromDb()
    {
        $this->search = strtoupper($this->search);
        $this->emploiDuTemps = Emploidutemp::whereHas('classe', function ($query) {
            $query->where('niveau', $this->search);
        })
        ->orderBy('DebutCours')
        ->get();
    }

    public function showResults()
    {
        $this->isClicked = true;
        //$grilleHoraire = [];
        $this->getMonday();
        $this->getDataFromDb();
        //dd($this->emploiDuTemps);
        if (!$this->emploiDuTemps->isEmpty())
        {
            // Initialiser un tableau pour la grille horaire
            $grilleHoraire = [];

            // Parcourir les jours de la semaine
            foreach ($this->joursSemaine as $jour) {
                $grilleHoraire[$jour] = [];

                // Heure de début des cours (7h30)
                $heureDebut = Carbon::createFromTime(7, 30);

                // Heure de fin des cours (18h00)
                $heureFin = Carbon::createFromTime(18, 0);
                //dd($heureDebut);
                // Parcourir toutes les heures de cours possibles dans la journée
                while ($heureDebut->lt($heureFin)) {
                    if ($heureDebut->eq('12:00'))
                    {
                        $heureDebut->addMinutes(90)->format('H:i');
                        continue;
                    }
                    // Ajouter les détails du cours à la grille horaire
                    $grilleHoraire[$jour][] = [
                        'heure' => $heureDebut->format('H:i') . '-' . $heureDebut->addMinutes(90)->format('H:i'), // Durée du cours de 90 minutes
                        'niveau' => '',
                        'cours' => '', // Initialiser à vide pour les plages horaires sans cours
                        'professeur' => '',
                        'salle' => '',
                    ];
                }
            }
            // Remplir les cases avec les cours de l'emploi du temps
            foreach ($this->emploiDuTemps as $cours) {
                $jour = Carbon::parse($cours->DebutCours)->locale('en')->translatedFormat('l'); // Jour du cours
                $heureDebut = Carbon::parse($cours->DebutCours)->format('H:i'); // Heure de début du cours
                $heureFin = Carbon::parse($cours->FinCours)->format('H:i'); // Heure de fin du cours
                //dd($jour);
                // Rechercher l'heure de début du cours dans la grille horaire et mettre à jour les détails du cours
                //dd($grilleHoraire, $jour);
                foreach ($grilleHoraire[$jour] as &$plageHoraire)
                {
                    $groupeClasse = explode(' ', $cours->classe->niveau);
                    if (strpos($plageHoraire['heure'], $heureDebut) !== false) {
                        $plageHoraire['niveau'] = str_replace($groupeClasse[0], "", $cours->classe->niveau);
                        $plageHoraire['cours'] = $cours->Cours;
                        $plageHoraire['professeur'] = $cours->professeur->prenom;
                        $plageHoraire['salle'] = $cours->salle->design;
                        break;
                    }
                }
            }
            //dd($grilleHoraire);
            $this->grilleHoraire = $grilleHoraire;
            //dd($this->grilleHoraire);
        }
    }
    public function render()
    {
        return view('livewire.shower-edt', [
            'grilleHoraire' => $this->grilleHoraire,
            'joursSemaine' => $this->joursSemaine,
        ]);
    }
}

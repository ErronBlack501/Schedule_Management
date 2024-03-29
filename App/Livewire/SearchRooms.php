<?php

namespace App\Livewire;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SearchRooms extends Component
{
    public $data;
    public $isClicked = false;
    public $isValide;
    public $heuresInvalides = [];
    public $date;
    public $isDisplaying = false;
    public $sallesNonReserve = [];
    public $sallesLibres = [];

    public function displayResults()
    {
        $this->isClicked = true;
        $this->resetPropreties();
        if ($this->isDataValide())
        {
            $this->isDisplaying = true;
            $this->getFreeRooms();
            $this->getNotReservedRooms();
        }
    }

    private function resetPropreties()
    {
        $sallesLibres = $this->sallesLibres;
        $sallesNonReserve = $this->sallesNonReserve;

        if (!empty($sallesLibres))
        {
            while (!empty($sallesLibres))
            {
                array_pop($sallesLibres);
            }
        }

        if (!empty($sallesNonReserve))
        {
            while (!empty($sallesNonReserve))
            {
                array_pop($sallesNonReserve);
            }
        }

        $this->sallesLibres = $sallesLibres;
        $this->sallesNonReserve = $sallesNonReserve;
    }

    private function isDataValide(): bool
    {
        $heuresInvalides = [
            '09:00',
            '10:30',
            '15:00',
            '16:30',
        ];
        $this->heuresInvalides = $heuresInvalides;
        $strDateTime = $this->data;
        $dateTime = Carbon::createFromFormat('Y-m-d\TH:i', (string)$strDateTime);
        $heureSaisi = $dateTime->format('H:i');

        foreach ($heuresInvalides as $element)
        {
            if ($heureSaisi == $element)
            {
                $this->isValide = false;
                return false;
            }
        }

        $this->isValide = true;
        return true;
    }

    private function getNotReservedRooms()
    {
        $this->sallesNonReserve = DB::table('salles')
        ->select('id', 'design')
        ->where('occupation', '=', 'libre')
        ->get()
        ->toArray();
    }
    private function plageHoraire()
    {
        $heureDebut = Carbon::createFromTime(7, 30);
        $heureFin = Carbon::createFromTime(18, 0);
        $plageHoraire = [];

        while ($heureDebut->lt($heureFin))
        {
            if ($heureDebut->eq('12:00'))
            {
                $heureDebut->addMinutes(90)->format('H:i');
                continue;
            }

            $heureFinPlage = $heureDebut->copy()->addMinutes(90);
            $plageHoraire[] = [$heureDebut->format('H:i'), $heureFinPlage->format('H:i')];
            $heureDebut->addMinutes(90);
        }

        return $plageHoraire;
    }

    private function getFreeRooms()
    {

        $strDateTime = $this->data;
        //dd($strDateTime);
        $results = [];
        $plageHoraire = $this->plageHoraire();
        $dateTime = Carbon::createFromFormat('Y-m-d\TH:i', (string)$strDateTime);
        $this->date = $dateTime->format('H:i:s');
        $dateTimeYMD = $dateTime->format('Y-m-d');
        //dd($dateTimeYMD);
        $tableauEdt = DB::table('emploidutemps')
        ->select('salle_id', 'DebutCours', 'FinCours')
        ->distinct()
        ->where('DebutCours', 'like', "$dateTimeYMD%")
        ->orderBy('salle_id')
        ->get()
        ->toArray();

        //dd($tableauEdt);
        if (empty($tableauEdt))
        {
            $this->sallesLibres = DB::table('salles')
            ->select('id', 'design')
            ->where('occupation', '=', 'occupée')
            ->get()
            ->toArray();
            // dd($this->sallesLibres);
        }
        else
        {
            $heureSaisi = Carbon::createFromTime((int)$dateTime->format('H'), (int)$dateTime->format('i'));
            $heureDebut = Carbon::createFromTime(7, 30);
            $heureFin = Carbon::createFromTime(18, 0);
            $midiDebut = Carbon::createFromTime(12, 1);
            $midiFin = Carbon::createFromTime(13, 29);
            $condition = !($heureSaisi->betweenIncluded($heureDebut, $heureFin));
            $condition1 = $heureSaisi->betweenIncluded($midiDebut, $midiFin);
            if ($condition)
            {
                $this->sallesLibres = DB::table('salles')
                ->select('id', 'design')
                ->where('occupation', '=', 'occupée')
                ->get()
                ->toArray();
                //dd($this->sallesLibres);
            }
            else if($condition1)
            {
                $this->sallesLibres = DB::table('salles')
                ->select('id', 'design')
                ->where('occupation', '=', 'occupée')
                ->get()
                ->toArray();
                // dd($this->sallesLibres);
            }
            else
            {
                $debutCours = '';
                $finCours = '';
                //dd($plageHoraire);
                foreach ($plageHoraire as $item)
                {
                    $debutCours = Carbon::createFromFormat('H:i', $item[0]);
                    $finCours = Carbon::createFromFormat('H:i', $item[1]);
                    //dd($item[0], $item[1], $heureSaisi, $heureDebut, $finCours, $heureSaisi->between($debutCours, $finCours), $heureSaisi->betweenIncluded($debutCours, $finCours), $heureSaisi->betweenExcluded($debutCours, $finCours));
                    if ($heureSaisi->betweenIncluded($debutCours, $finCours))
                    {
                        $a = $debutCours->format('H:i:s');
                        $a = str_replace(" ", "", $a);
                        $b = $finCours->format('H:i:s');
                        $b = str_replace(" ", "", $b);

                        $results = DB::table('salles')
                        ->select('salles.id', 'salles.design')
                        ->join('emploidutemps', 'salles.id', '=', 'emploidutemps.salle_id')
                        ->where('emploidutemps.DebutCours', '=', "$dateTimeYMD $a")
                        ->where('emploidutemps.FinCours', '=', "$dateTimeYMD $b")
                        ->distinct()
                        ->get()
                        ->toArray();
                        break;
                    }
                }
                if (empty($results))
                {
                    $this->sallesLibres = DB::table('salles')
                    ->select('id', 'design')
                    ->where('occupation', '=', 'occupée')
                    ->get()
                    ->toArray();
                    //dd($this->sallesLibres);
                }
                else
                {
                    $idsToRemove = array_column($results, 'id');
                    //dd($idsToRemove);
                    //dd($tableauEdt);
                    foreach ($tableauEdt as $index => $element)
                    {
                        if (in_array($element->salle_id, $idsToRemove))
                        {
                            unset($tableauEdt[$index]);
                        }
                    }
                    //dd($tableauEdt);
                    if (!empty($tableauEdt))
                    {
                        //$tableauEdt = array_values($tableauEdt);
                        //dd($tableauEdt);
                        foreach ($tableauEdt as $element)
                        {
                            unset($element->DebutCours);
                            unset($element->FinCours);
                        }

                        $temp = [];

                        foreach ($tableauEdt as $element)
                        {
                            $temp[] = $element->salle_id;
                        }

                        //dd($temp);
                        $tableauEdt = array_unique($temp);
                        //dd($tableauEdt);

                        // while (!empty($tableauEdt))
                        // {
                        //     array_pop($tableauEdt);
                        // }

                        // foreach ($temp as $element)
                        // {
                        //     array_push($tableauEdt, $element);
                        // }

                        //dd($tableauEdt);
                        foreach ($tableauEdt as $salle_id)
                        {
                            $query = DB::table('salles')
                            ->select('id', 'design')
                            ->where('id', '=', $salle_id)
                            ->get()
                            ->toArray();
                            //dd($query);
                            //array_push($this->sallesLibres, $query);
                            foreach ($query as $element)
                            {
                                array_push($this->sallesLibres, [
                                    'id' => $element->id,
                                    'design' => $element->design,
                                ]);
                                // $this->sallesLibres[] = [
                                //     'id' => $element->id,
                                //     'design' => $element->design,
                                // ];
                            }
                        }
                        //dd($this->sallesLibres);
                    }
                    else
                    {
                        $this->sallesLibres = $tableauEdt;
                    }

                }
            }
        }
    }

    public function render()
    {
        return view('livewire.search-rooms');
    }
}

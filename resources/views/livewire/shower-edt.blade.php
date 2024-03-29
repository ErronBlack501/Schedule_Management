<div>
    @unless ($isGenerate)
        <div class="pt-2 relative mx-auto text-gray-600">
            <input wire:model='search' class="border-2 border-gray-300 bg-white h-10 px-5 pr-16 rounded-lg text-sm focus:outline-none"
            type="search" name="search" placeholder={{ __('Search....') }}>
            <button wire:click='showResults' type="submit" class="mt-5 mr-4">
                <svg class="text-gray-600 h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg"
                    xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px"
                    viewBox="0 0 56.966 56.966" style="enable-background:new 0 0 56.966 56.966;" xml:space="preserve"
                    width="512px" height="512px">
                    <path d="M55.146,51.887L41.588,37.786c3.486-4.144,5.396-9.358,5.396-14.786c0-12.682-10.318-23-23-23s-23,10.318-23,23  s10.318,23,23,23c4.761,0,9.298-1.436,13.177-4.162l13.661,14.208c0.571,0.593,1.339,0.92,2.162,0.92  c0.779,0,1.518-0.297,2.079-0.837C56.255,54.982,56.293,53.08,55.146,51.887z M23.984,6c9.374,0,17,7.626,17,17s-7.626,17-17,17  s-17-7.626-17-17S14.61,6,23.984,6z" />
                </svg>
            </button>
            @if ($isClicked)
              <button wire:click='generatePdf' class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded inline-flex items-center">
                  <svg class="fill-current w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M13 8V2H7v6H2l8 8 8-8h-5zM0 18h20v2H0v-2z"/></svg>
                  <span>{{ __('Generate pdf') }}</span>
              </button>
            @endif
            <br>
            <br>
        </div>
    @endunless

    @if ($isClicked)
        @if (!$emploiDuTemps->isEmpty())
            <div class="inline-block w-full">
                @if ($isGenerate)
                    <div class="fi-ta-actions flex shrink-0 items-center gap-3 flex-wrap justify-start ms-auto sm:ms-auto">
                        <a href="http://127.0.0.1:8000/admin/emploidutemps/show" style="--c-400:var(--primary-400);--c-500:var(--primary-500);--c-600:var(--primary-600);" class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-custom fi-btn-color-primary fi-size-md fi-btn-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm bg-custom-600 text-white hover:bg-custom-500 focus-visible:ring-custom-500/50 dark:bg-custom-500 dark:hover:bg-custom-400 dark:focus-visible:ring-custom-400/50 fi-ac-btn-action"><span class="fi-btn-label">{{ __('Back') }}</span></a>
                    </div>
                    <br>
                @endif
                <h1 class="border text-center text-bold text-xl">{{ __('Schedule from') }} {{ $debutEdt }} {{ __('to') }} {{ $finEdt }}</h1>
                <h1 class="text-center text-bold italic text-xl"> {{ $search }} </h1>
                <div class="w-full">
                    <table class="table-fixed w-full h-auto">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-2xl border border-gray-400">{{ __('Days') }}</th>
                                <th class="px-4 py-2 border border-gray-400">07:30-09:00</th>
                                <th class="px-4 py-2 border border-gray-400">09:00-10:30</th>
                                <th class="px-4 py-2 border border-gray-400">10:30-12:00</th>
                                <th class="px-4 py-2 border border-gray-400">13:30-15:00</th>
                                <th class="px-4 py-2 border border-gray-400">15:00-16:30</th>
                                <th class="px-4 py-2 border border-gray-400">16:30-18:00</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($joursSemaine as $jours)
                                <tr>
                                    <td class="px-4 text-center text-2xl border border-gray-400">{{ __($jours) }}</td>
                                    @foreach ($grilleHoraire[$jours] as $plageHoraire)
                                        <td class="max-w-35 h-5 border border-gray-400">
                                        @if ($jours === 'Monday' && $plageHoraire['heure'] === '07:30-09:00')
                                            <div class="w-full text-center justify-center inline-block">
                                                <span style="display: block; text-align: center;">Levé des</span>
                                                <span style="display: block; text-align: center;">drapeaux et</span>
                                                <span style="display: block text-align: center;">Rassemblement</span>
                                                {{-- {{ __('Levé des drapeaux et Rassemblement') }} --}}
                                            </div>
                                        @elseif (strpos($plageHoraire['niveau'], 'IG') !== false)
                                            <div class="flex">
                                                <div class="w-1/2 m-4 p-2">
                                                    <div class="mb-1 text-center">{{ $plageHoraire['niveau'] }}</div>
                                                    <div class="mb-1 text-center">{{ $plageHoraire['cours'] }}</div>
                                                    <div class="mb-1 text-center">{{ $plageHoraire['professeur'] }}</div>
                                                    <div class="mb-1 text-center">S{{ $plageHoraire['salle'] }}</div>
                                                </div>
                                                <div class="w-1/2 m-4 p-2">
                                                    <div class="mb-1 text-center"> </div>
                                                    <div class="mb-1 text-center"> </div>
                                                    <div class="mb-1 text-center"> </div>
                                                    <div class="mb-1 text-center"> </div>
                                                </div>
                                            </div>
                                        @elseif (strpos($plageHoraire['niveau'], 'GB') !== false || strpos($plageHoraire['niveau'], 'SR') !== false)
                                            <div class="flex">
                                                <div class="w-1/2 m-4 p-2">
                                                    <div class="mb-1 text-center"> </div>
                                                    <div class="mb-1 text-center"> </div>
                                                    <div class="mb-1 text-center"> </div>
                                                    <div class="mb-1 text-center"> </div>
                                                </div>
                                                <div class="w-1/2 m-4 p-2">
                                                    <div class="mb-1 text-center">{{ $plageHoraire['niveau'] }}</div>
                                                    <div class="mb-1 text-center">{{ $plageHoraire['cours'] }}</div>
                                                    <div class="mb-1 text-center">{{ $plageHoraire['professeur'] }}</div>
                                                    <div class="mb-1 text-center">S{{ $plageHoraire['salle'] }}</div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="px-4 py-2 text-2xl border border-gray-400 text-center font-bold">{{ __('Days') }}</td>
                                <td class="px-4 py-2 border border-gray-400 text-center font-bold">07:30-09:00</td>
                                <td class="px-4 py-2 border border-gray-400 text-center font-bold">09:00-10:30</td>
                                <td class="px-4 py-2 border border-gray-400 text-center font-bold">10:30-12:00</td>
                                <td class="px-4 py-2 border border-gray-400 text-center font-bold">13:30-15:00</td>
                                <td class="px-4 py-2 border border-gray-400 text-center font-bold">15:00-16:30</td>
                                <td class="px-4 py-2 border border-gray-400 text-center font-bold">16:30-18:00</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @elseif($emploiDuTemps->isEmpty())
            <div style="width: 1030px; height: 349px; display: flex; justify-content: center;">
                <div style="display: flex; justify-content: center;">
                    <div style="font-size: 100px; font-weight:bold; text-align: center;">{{ __('No result') }}</div>
                </div>
            </div>
        @endif
    @endif
</div>

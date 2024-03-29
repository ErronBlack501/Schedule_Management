<div>
    <div class="min-w-0 flex-1">
        <h1><strong>{{ __('Enter a date and time of your choice to display available rooms') }}</strong></h1>
        <input id='dateTimePicker' wire:model='data' required="required" type="datetime-local">
        <button wire:click='displayResults' type="submit">{{ __('Submit') }}</button>
    </div>
    @if ($isClicked)
        @if (!$isValide)
            <br>
            <p>{{ __("To ensure that the search system functions properly, the time you have entered must not be equal to the following times : ") }}</p>
            <ul>
                @foreach ($heuresInvalides as $heure)
                    <li>- {{ $heure }}</li>
                @endforeach
            </ul>
        @endif
    @endif

    @if($isValide && $isDisplaying)
        <br>
        <div class="inline-block">
            <h1 class="border text-center text-bold text-xl">{{ __("List of available rooms at the entered time : ") }} "{{ $date }}"</h1>
            <br>
            <table class='table-auto'>
                <thead>
                    <tr>
                        <th class="w-32 px-4 py-2 border border-gray-400">id_salle</th>
                        <th class="w-32 px-4 py-2 border border-gray-400">design</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($sallesLibres))
                        @foreach($sallesLibres as $salleLibre)
                            <tr>
                                {{-- {{ dd(gettype($salleLibre)) }} --}}
                                @if (gettype($salleLibre) === 'array')
                                    <td class="text-center border">{{ $salleLibre['id'] }}</td>
                                    <td class="text-center border">{{ $salleLibre['design'] }}</td>
                                @else
                                    {{-- object --}}
                                    <td class="text-center border">{{ $salleLibre->id }}</td>
                                    <td class="text-center border">{{ $salleLibre->design }}</td>
                                @endif
                            </tr>
                        @endforeach
                    @endif
                    @if(!empty($sallesNonReserve))
                        @foreach($sallesNonReserve as $salleNonReserve)
                            <tr>
                                <td class="text-center border">{{ $salleNonReserve->id }}</td>
                                <td class="text-center border">{{ $salleNonReserve->design }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
            @if (empty($sallesLibres) && empty($sallesNonReserve))
                <br>
                <strong>{{ __("All rooms are occupied at the date you entered and reserved in the 'emploidutemps' table") }}.</strong>
            @endif
        </div>
    @endif
    <script>
        window.addEventListener('beforeunload', function(event) {
            document.getElementById('dateTimePicker').value = '';
        })
    </script>
</div>

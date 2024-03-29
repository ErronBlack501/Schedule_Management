<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClasseResource\Pages;
use App\Filament\Resources\ClasseResource\RelationManagers;
use App\Filament\Resources\ClasseResource\RelationManagers\EmploisDuTempsRelationManager;
use App\Models\Classe;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;
use Filament\Actions\ActionGroup;

class ClasseResource extends Resource
{
    protected static ?string $model = Classe::class;
    //protected static ?string $modelLabel = 'Class';
    //protected static ?string $pluralModelLabel = 'Class';
    protected static ?string $recordTitleAttribute = 'niveau';
    protected static int $globalSearchResultLimit = 10;
    //protected static ?string $navigationGroup = 'Management';
    //protected static ?string $navigationLabel = 'Class';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getPluralModelLabel(): string
    {
        return __(' Class ');
    }
    public static function getModelLabel(): string
    {
        return __('Class');
    }
    public static function getNavigationGroup(): ?string
    {
        return __('Management');

    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('niveau')
                ->label(__('Level'))
                ->unique()
                ->required()
                ->minLength(5)
                ->maxLength(9)
                //->alphaNum()
                ->rules([
                    function (){
                        return function (string $attribute, $value, Closure $fail)
                        {
                            $value = strtoupper($value);
                            $tableau = explode(" ", $value);
                            $isFound = false;
                            $niveaux = [
                                'L1',
                                'L2',
                                'L3',
                                'M1',
                                'M2',
                            ];
                            $parcours = [
                                'PRO',
                                'IG',
                                'GB',
                                'SR',
                            ];

                            foreach (Classe::all(['niveau']) as $niveau)
                            {
                                if ($niveau->niveau == $value)
                                {
                                    $fail(__('The class you entered already exists.'));
                                    //return true;
                                }
                            }

                            if (count($tableau) < 3)
                            {
                                if ($tableau[1] !== 'SR')
                                {
                                    $fail(__("Correct format: for example 'L1 PRO G1'"));
                                }
                            }

                            if (count($tableau) === 3)
                            {
                                foreach($niveaux as $niveau)
                                {
                                    if (strcmp(trim($niveau), trim($tableau[0])) === 0)
                                    {
                                        $isFound = true;
                                        break;
                                    }
                                }
                                if (!$isFound)
                                {
                                    $fail(__("The valid levels are: L1, L2, L3, M1, M2"));
                                }
                                $isFound = false;

                                foreach($parcours as $unParcours)
                                {
                                    if (strcmp(trim($unParcours), trim($tableau[1])) === 0)
                                    {
                                        $isFound = true;
                                        break;
                                    }
                                }

                                if (!$isFound)
                                {
                                    $fail(__("The valid courses are: PRO, GB, IG, SR."));
                                }

                                if ($tableau[1] === 'PRO' && $tableau[0] !== 'L1')
                                {
                                    $fail(__("The PRO course is only available for level L1."));
                                }

                                $strGp = $tableau[2];
                                $isFound = strpos($strGp, 'G');
                                if ($isFound === false && $tableau[1] !== 'SR')
                                {
                                    $fail(__("Correct format: xx xxx Gx or xx xx Gx (xx: Level, xxx or xx: Course, Gx: Group and group number)"));
                                }
                            }
                        };
                    }

                ]),
            ]);
    }

    // protected function onValidationError(ValidationException $exception): void
    // {
    //     Notification::make()
    //     ->title($exception->getMessage())
    //     ->danger()
    //     ->send();
    // }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\textColumn::make('id')->sortable()->searchable(),
                Tables\Columns\textColumn::make('niveau')->label(__('Level'))->sortable()->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
       return [

       ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClasses::route('/'),
            'create' => Pages\CreateClasse::route('/create'),
            'edit' => Pages\EditClasse::route('/{record}/edit'),
        ];
    }
}

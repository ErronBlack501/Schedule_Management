<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmploidutempResource\Pages;
use App\Filament\Resources\EmploidutempResource\Pages\ShowSchedule;
use App\Filament\Resources\EmploidutempResource\RelationManagers;
use App\Models\Emploidutemp;
use App\Rules\EdtValidation;
use DateTime;
use Doctrine\DBAL\Types\DateTimeType;
use Filament\Tables\Actions\CreateAction;
use Filament\Forms;
use Filament\Forms\Components\BelongsToSelect;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\ValidationException;
use Closure;
use Filament\Actions\Action as ActionsAction;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Get;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Date;

class EmploidutempResource extends Resource
{
    //protected static ?string $navigationGroup = 'Management';
    //protected static ?string $navigationLabel = 'Schedules';
    //protected static ?string $modelLabel = 'Schedule';
    //protected static ?string $pluralModelLabel = 'Schedules';
    public static function getPluralModelLabel(): string
    {
        return __('Schedules');
    }
    public static function getModelLabel(): string
    {
        return __('Schedule');
    }
    protected static ?int $navigationSort = 3;
    protected static ?string $recordTitleAttribute = 'Cours';

    protected static int $globalSearchResultLimit = 10;

    protected static ?string $model = Emploidutemp::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static function getNavigationGroup(): ?string
    {
        return __('Management');

    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function getGloballySearchableAttribute(): array
    {
        return [
            'Cours',
            'DebutCours',
            'FinCours',
        ];
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('salle_id')
                //->dependsOn('DebutCours')
                //->dependsOn('FinCours')
                ->label(__('Classroom'))
                //->translateLabel()
                ->relationship('salle', 'design')->required(),

                Select::make('prof_id')
                ->label(__('Professor'))
                ->relationship('professeur', 'nomPrenom')
                ->searchable(['nom', 'prenom'])
                ->preload()
                ->required(),

                Select::make('classe_id')
                ->label(__('Class'))
                ->relationship('classe', 'niveau')->required(),
                Forms\Components\TextInput::make('Cours')
                ->label(__('Course'))
                ->required()->maxLength(255),

                DateTimePicker::make('DebutCours')
                ->label(__('Start at'))
                ->seconds(false)
                //->timePicker()
                //->dependsOn('salle_id')
                //->dependsOn('FinCours')
                ->before('FinCours')
                ->native()
                ->format('Y-m-d H:i')
                ->live()
                ->required()
                ->rules([
                    fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get)
                    {
                        $salle_id = $get('salle_id');
                        $prof_id = $get('prof_id');
                        $isFound = false;
                        $dateString1 =  $value;
                        $dateString2 = $get('FinCours');
                        $dateTime2 = DateTime::createFromFormat('Y-m-d\TH:i', (string)$dateString2);
                        if ($dateTime2 === false)
                        {
                            $dateTime2 = DateTime::createFromFormat('Y-m-d H:i', (string)$dateString2);
                        }
                        //dd($dateString2, $dateTime2);
                        $finCours = $dateTime2->format('Y-m-d H:i:s');
                        $dateTime1 = DateTime::createFromFormat('Y-m-d\TH:i', (string)$dateString1);
                        if ($dateTime1 === false)
                        {
                            $dateTime1 = DateTime::createFromFormat('Y-m-d H:i', (string)$dateString1);
                        }
                        //dd($dateString1, $dateTime1);
                        $debutCours = $dateTime1->format('Y-m-d H:i:s');
                        $valueTosearch = $dateTime1->format('H:i');
                        //dd($valueTosearch, $debutCours);
                        //dd($debutCours, $finCours);
                        $dateDebutValided= [
                            '07:30',
                            '09:00',
                            '10:30',
                            '13:30',
                            '15:00',
                            '16:30',
                        ];

                        foreach (Emploidutemp::all(['salle_id', 'prof_id', 'DebutCours', 'FinCours']) as $edt)
                        {
                            $condition1 = ($edt->prof_id == $prof_id);
                            $condition2 = ($edt->DebutCours ==  $debutCours);
                            $condition3 = ($edt->FinCours == $finCours);
                            //01hpsn40f5ppzgbc8qpvaadd55
                            //dd($condition2, $edt->DebutCours, $debutCours);
                            $condition = ($condition1 && $condition2 && $condition3);
                            //dd($condition);
                            if ($condition && $edt->salle_id != $salle_id)
                            {
                                $fail(__('The teacher is teaching in another classroom at the start and end times of the class you entered.'));
                                //return true;
                            }
                        }

                        foreach($dateDebutValided as $oneDateDebut)
                        {
                            if (strcmp(trim($oneDateDebut), trim($valueTosearch)) === 0)
                            {
                                $isFound = true;
                                break;
                            }
                        }
                        if (!$isFound)
                        {
                            $fail(__('The valid class start times are: 07:30 AM, 09:00 AM, 10:30 AM, 01:30 PM, 03:00 PM, and 04:30 PM.'));
                        }

                        if (!($dateTime1->format('Y-m-d') === $dateTime2->format('Y-m-d')))
                        {
                            $fail(__('The start and end dates of the class must be in the same year, month, and day.'));
                        }

                        switch ($dateTime2->format('H:i'))
                        {
                            case '09:00':
                                if (!($dateTime1->format('H:i') === '07:30'))
                                {
                                    $fail(__('If your class end time is 09:00, then the class start time must be 07:30'));
                                }
                                break;
                            case '10:30':
                                if (!($dateTime1->format('H:i') === '09:00'))
                                {
                                    $fail(__('If your class end time is 10:30, then the class start time must be 09:00.'));
                                }
                                break;
                            case '12:00':
                                if (!($dateTime1->format('H:i') === '10:30'))
                                {
                                    $fail(__('If your class end time is 12:00, then the class start time must be 10:30.'));
                                }
                                break;
                            case '15:00':
                                if (!($dateTime1->format('H:i') === '13:30'))
                                {
                                    $fail(__('If your class end time is 15:00, then the class start time must be 13:30.'));
                                }
                                break;
                            case '16:30':
                                if (!($dateTime1->format('H:i') === '15:00'))
                                {
                                    $fail(__('If your class end time is 16:30, then the class start time must be 15:00.'));
                                }
                                break;
                            case '18:00':
                                if (!($dateTime1->format('H:i') === '16:30'))
                                {
                                    $fail(__('If your class end time is 18:00, then the class start time must be 16:30'));
                                }
                                break;
                            default:
                                $fail('!!Fatal error due to the end time you entered!!');
                        }
                    }
                ]),

                DateTimePicker::make('FinCours')
                ->label(__('End at'))
                ->seconds(false)
                ->format('Y-m-d H:i')
                ->required()
                ->live()
                //->timePicker()
                //->dependsOn('DebutCours')
                //->dependsOn('salle_id')
                ->after('DebutCours')
                ->native()
                ->rules([
                    fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get)
                    {
                        //'La date de fin de cours saisie est entre un intervalle de temps où une salle existante sera occupée.'
                        //$salle_id = $get('salle_id');
                        $dateString1 =  $value;
                        $isFound = false;
                        $dateString2 = $get('DebutCours');
                        $dateTime2 = DateTime::createFromFormat('Y-m-d\TH:i', (string)$dateString2);
                        if ($dateTime2 === false)
                        {
                            $dateTime2 = DateTime::createFromFormat('Y-m-d H:i', (string)$dateString2);
                        }
                        $dateTime1 = DateTime::createFromFormat('Y-m-d\TH:i', (string)$dateString1);
                        if ($dateTime1 === false)
                        {
                            $dateTime1 = DateTime::createFromFormat('Y-m-d H:i', (string)$dateString1);
                        }
                        //$finCours = $dateTime1->format('Y-m-d H:i:s');
                        //$debutCours = $dateTime1->format('Y-m-d H:i:s');
                        $valueTosearch = $dateTime1->format('H:i');
                        $dateFinValided= [
                            '09:00',
                            '10:30',
                            '12:00',
                            '15:00',
                            '16:30',
                            '18:00',
                        ];
                        foreach($dateFinValided as $oneDateFin)
                        {
                            if (strcmp(trim($oneDateFin), trim($valueTosearch)) === 0)
                            {
                                $isFound = true;
                                break;
                            }
                        }
                        if (!$isFound)
                        {
                            $fail(__("The valid class end times are: 09:00 AM, 10:30 AM, 12:00 PM, 03:00 PM, 04:30 PM, and 06:00 PM."));
                        }

                        if (!($dateTime1->format('Y-m-d') === $dateTime2->format('Y-m-d')))
                        {
                            $fail(__('The start and end dates of the class must be in the same year, month, and day.'));
                        }

                        switch ($dateTime2->format('H:i'))
                        {
                            case '07:30':
                                if (!($dateTime1->format('H:i') == '09:00'))
                                {
                                    $fail(__('If your class start time is 07:30, then the class end time must be 09:00.'));
                                }
                                break;
                            case '09:00':
                                if (!($dateTime1->format('H:i') == '10:30'))
                                {
                                    $fail(__('If your class start time is 09:00, then the class end time must be 10:30.'));
                                }
                                break;
                            case '10:30':
                                if (!($dateTime1->format('H:i') == '12:00'))
                                {
                                    $fail(__('If your class start time is 10:30, then the class end time must be 12:00.'));
                                }
                                break;
                            case '13:30':
                                if (!($dateTime1->format('H:i') == '15:00'))
                                {
                                    $fail(__('If your class start time is 13:30, then the class end time must be 15:00.'));
                                }
                                break;
                            case '15:00':
                                if (!($dateTime1->format('H:i') == '16:30'))
                                {
                                    $fail(__('If your class start time is 15:00, then the class end time must be 16:30.'));
                                }
                                break;
                            case '16:30':
                                if (!($dateTime1->format('H:i') == '18:00'))
                                {
                                    $fail(__('If your class start time is 16:30, then the class end time must be 18:00.'));
                                }
                                break;
                            default:
                                $fail(__('!!Fatal error due to the start time you entered!!'));
                        }
                    }
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //Tables\Columns\textColumn::make('salle_id')->searchable(),
                Tables\Columns\textColumn::make('salle.design')->label('Design')->sortable()->searchable(),
                //Tables\Columns\textColumn::make('prof_id')->searchable(),
                Tables\Columns\textColumn::make('professeur.nomPrenom')->label(__('Professor'))->sortable()->searchable(),
                //Tables\Columns\textColumn::make('professeur.prenom')->searchable(),
                //Tables\Columns\textColumn::make('classe_id')->searchable(),
                Tables\Columns\textColumn::make('classe.niveau')->label(__('Level'))->sortable()->searchable(),
                Tables\Columns\textColumn::make('Cours')->label(__('Course'))->sortable()->searchable(),
                Tables\Columns\textColumn::make('DebutCours')->label(__('Start at'))->sortable()->searchable()->dateTime(),
                Tables\Columns\textColumn::make('FinCours')->label(__('End at'))->sortable()->searchable()->dateTime(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Action::make('Go to show timetable')
                ->label(__('Go to show timetable'))
                ->url(fn (): string => ShowSchedule::getUrl())
                // ->openUrlInNewTab()
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmploidutemps::route('/'),
            'create' => Pages\CreateEmploidutemp::route('/create'),
            'edit' => Pages\EditEmploidutemp::route('/{record}/edit'),
            'show' => Pages\ShowSchedule::route('/show'),
        ];
    }
}


<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClasseResource\RelationManagers\EmploisDuTempsRelationManager;
use App\Filament\Resources\SalleResource\Pages;
use App\Filament\Resources\SalleResource\RelationManagers;
use App\Models\Salle;
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
use Filament\Forms\Get;



class SalleResource extends Resource
{
    //protected static ?string $navigationGroup = 'Management';
    //protected static ?string $navigationLabel = 'Classrooms';
    //protected static ?string $modelLabel = 'Classroom';
    //protected static ?string $pluralModelLabel = 'Classrooms';
    protected static ?string $recordTitleAttribute = 'design';
    protected static ?string $model = Salle::class;
    protected static ?int $navigationSort = 0;
    protected static int $globalSearchResultLimit = 10;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static function getNavigationGroup(): ?string
    {
        return __('Management');

    }
    public static function getPluralModelLabel(): string
    {
        return __('Classrooms');
    }
    public static function getModelLabel(): string
    {
        return __('Classroom');
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function getGloballySearchableAttribute(): array
    {
        return [
            'design',
            'occupation',
        ];
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('design')
                ->required()
                ->alphaNum()
                ->maxLength(3)
                ->rules([
                    fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get)
                    {
                        $occupation = $get('occupation');
                        foreach (Salle::all(['design', 'occupation']) as $salle)
                        {
                            if ($salle->design == $value && $salle->occupation == $occupation)
                            {
                                $fail(__('The room you entered already exists.'));
                                //return true;
                            }
                        }
                    }
                ]),
                Forms\Components\Select::make('occupation')->options([
                    __('Free') => 'Libre',
                    __('Occupied') => 'Occupée',
                ])
                ->required()
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
                Tables\Columns\textColumn::make('id')->searchable(),
                Tables\Columns\textColumn::make('design')->searchable(),
                Tables\Columns\textColumn::make('occupation')->searchable(),
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
            'index' => Pages\ListSalles::route('/'),
            'create' => Pages\CreateSalle::route('/create'),
            'edit' => Pages\EditSalle::route('/{record}/edit'),
        ];
    }
}

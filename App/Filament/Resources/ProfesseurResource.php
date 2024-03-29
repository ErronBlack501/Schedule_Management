<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClasseResource\RelationManagers\EmploisDuTempsRelationManager;
use App\Filament\Resources\ProfesseurResource\Pages;
use App\Filament\Resources\ProfesseurResource\RelationManagers;
use App\Models\Professeur;
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


class ProfesseurResource extends Resource
{
    //protected static ?string $navigationGroup = 'Management';
    //protected static ?string $navigationLabel = 'Professors';
    //protected static ?string $modelLabel = 'Professor';
    //protected static ?string $pluralModelLabel = 'Professors';
    protected static ?string $recordTitleAttribute = 'nomPrenom';
    protected static ?string $model = Professeur::class;
    protected static ?int $navigationSort = 1;
    protected static int $globalSearchResultLimit = 10;
    public static function getNavigationGroup(): ?string
    {
        return __('Management');

    }
    public static function getPluralModelLabel(): string
    {
        return __('Professors');
    }
    public static function getModelLabel(): string
    {
        return __('Professor');
    }
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function getGloballySearchableAttribute(): array
    {
        return [
            'nomPrenom',
            'grade',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nom')
                ->label(__('LastName'))
                ->required()->alpha()->maxLength(255),
                Forms\Components\TextInput::make('prenom')
                ->label(__('FirstName'))
                ->required()->alpha()->maxLength(255),
                Forms\Components\Select::make('grade')->options([
                    'Professeur Titulaire' => 'Professeur Titulaire',
                    'Maître de Conférences' => 'Maître de Conférences',
                    "Assistant d'Enseignement Supérieur et de Recherche" => 'Assistant d\'Enseignement Supérieur et de Recherche',
                    'Docteur HDR'=> 'Docteur HDR',
                    'Doctorant en Informatique' => 'Doctorant en Informatique',
                    'Professeur' => 'Professeur',
                ])->required(),
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
                Tables\Columns\textColumn::make('nom')->label(__('LastName'))->searchable(),
                Tables\Columns\textColumn::make('prenom')->label(__('FirstName'))->searchable(),
                Tables\Columns\textColumn::make('grade')->label('Grade')->searchable(),
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
            'index' => Pages\ListProfesseurs::route('/'),
            'create' => Pages\CreateProfesseur::route('/create'),
            'edit' => Pages\EditProfesseur::route('/{record}/edit'),
        ];
    }
}

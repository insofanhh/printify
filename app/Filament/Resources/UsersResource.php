<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UsersResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UsersResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Section::make('User Information')
                ->schema([
                    Forms\Components\Section::make()
        ->schema([
            Forms\Components\TextInput::make('name')->label('Name')->required()->maxLength(50),
            Forms\Components\TextInput::make('email')->label('Email')->required()->email()->maxLength(50)->unique(ignoreRecord: true),
        ])->columns(2),
                    Forms\Components\Section::make()
        ->schema([
            Forms\Components\TextInput::make('password')
                ->label('Password')
                ->password()
                ->required(fn(Page $livewire): bool => $livewire instanceof CreateRecord)
                ->minLength(8)
                ->same('passwordConfirm')
                ->dehydrated(fn($state)=> filled($state))
                ->dehydrateStateUsing(fn ($state) => Hash::make($state)),
            Forms\Components\TextInput::make('passwordConfirm')
                ->label('Confirm Password')
                ->password()
                ->required(fn(Page $livewire): bool => $livewire instanceof CreateRecord)
                ->minLength('8')
                ->dehydrated(false),
        ])->columns(2),
                    Forms\Components\TextInput::make('information')->label('Information')->nullable()->json(),
                    Forms\Components\Toggle::make('is_verified')->label('Verified')->default(false),
                    Forms\Components\FileUpload::make('avatar_path')->label('Avatar')->image()->disk('public')->nullable(),
                    Forms\Components\Select::make('roles')
                        ->relationship('roles', 'name')
                        ->multiple()
                        ->preload()
                        ->searchable(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('email')->label('Email')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('information')->label('Information')->formatStateUsing(fn ($state) => json_encode($state)),
                Tables\Columns\ImageColumn::make('avatar_path')->label('Avatar')->circular(),
                Tables\Columns\IconColumn::make('is_verified')->label('Verified')->boolean(),
                Tables\Columns\TextColumn::make('roles.name')->label('Roles')->formatStateUsing(fn($state): string => str()->headline($state)),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUsers::route('/create'),
            'edit' => Pages\EditUsers::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrintOptionResource\Pages;
use App\Filament\Resources\PrintOptionResource\RelationManagers;
use App\Models\PrintOption;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PrintOptionResource extends Resource
{
    protected static ?string $model = PrintOption::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static ?string $navigationGroup = 'Quản lý Sản phẩm';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Tên tùy chọn in')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('sides')
                    ->label('Kiểu in')
                    ->options([
                        'one_sided' => 'In một mặt',
                        'two_sided' => 'In hai mặt',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->label('Mô tả')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_active')
                    ->label('Kích hoạt')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Tên tùy chọn in')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sides')
                    ->label('Kiểu in')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'one_sided' => 'In một mặt',
                        'two_sided' => 'In hai mặt',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('description')
                    ->label('Mô tả')
                    ->limit(50),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Kích hoạt')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Ngày cập nhật')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListPrintOptions::route('/'),
            'create' => Pages\CreatePrintOption::route('/create'),
            'edit' => Pages\EditPrintOption::route('/{record}/edit'),
        ];
    }
}

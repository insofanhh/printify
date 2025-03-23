<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PriceRuleResource\Pages;
use App\Models\PaperType;
use App\Models\PriceRule;
use App\Models\PrintOption;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PriceRuleResource extends Resource
{
    protected static ?string $model = PriceRule::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationGroup = 'Quản lý Sản phẩm';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('paper_type_id')
                    ->label('Loại giấy')
                    ->options(PaperType::query()->pluck('name', 'id'))
                    ->required()
                    ->searchable(),
                Forms\Components\Select::make('print_option_id')
                    ->label('Tùy chọn in')
                    ->options(PrintOption::query()->pluck('name', 'id'))
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('price_per_page')
                    ->label('Giá cơ bản')
                    ->required()
                    ->numeric()
                    ->prefix('VND'),
                Forms\Components\TextInput::make('min_quantity')
                    ->label('Số lượng tối thiểu')
                    ->required()
                    ->numeric()
                    ->default(1),
                Forms\Components\TextInput::make('max_quantity')
                    ->label('Số lượng tối đa')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('paperType.name')
                    ->label('Loại giấy')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('printOption.name')
                    ->label('Tùy chọn in')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('price_per_page')
                    ->label('Giá cơ bản')
                    ->money('VND')
                    ->sortable(),
                Tables\Columns\TextColumn::make('min_quantity')
                    ->label('Số lượng tối thiểu')
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_quantity')
                    ->label('Số lượng tối đa')
                    ->sortable(),
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
            'index' => Pages\ListPriceRules::route('/'),
            'create' => Pages\CreatePriceRule::route('/create'),
            'edit' => Pages\EditPriceRule::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'orderItems';

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $title = 'Các mục đơn hàng';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin sản phẩm')
                    ->schema([
                        Forms\Components\Select::make('paper_type_id')
                            ->relationship('paperType', 'name')
                            ->required()
                            ->label('Loại giấy'),
                        Forms\Components\Select::make('print_option_id')
                            ->relationship('printOption', 'name')
                            ->required()
                            ->label('Tùy chọn in'),
                        Forms\Components\TextInput::make('quantity')
                            ->required()
                            ->numeric()
                            ->label('Số lượng')
                            ->default(1)
                            ->minValue(1),
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->label('Giá')
                            ->suffix('VND'),
                    ])->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#'),
                Tables\Columns\TextColumn::make('paperType.name')
                    ->label('Loại giấy'),
                Tables\Columns\TextColumn::make('printOption.name')
                    ->label('Kiểu in'),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Số lượng'),
                Tables\Columns\TextColumn::make('price')
                    ->label('Giá')
                    ->money('VND'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('files')
                    ->label('Xem files')
                    ->icon('heroicon-o-document')
                    ->color('success')
                    ->action(function ($record) {
                        // Xử lý khi cần
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Models\OrderStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'Đơn Hàng';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin khách hàng')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('order_number')
                            ->label('Mã đơn hàng')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\Select::make('status_id')
                            ->label('Trạng thái')
                            ->options(OrderStatus::pluck('name', 'id'))
                            ->required(),
                    ])->columns(3),

                Forms\Components\Section::make('Chi tiết đơn hàng')
                    ->schema([
                        Forms\Components\TextInput::make('total_amount')
                            ->label('Tổng tiền')
                            ->numeric()
                            ->suffix('VND')
                            ->disabled(),
                        Forms\Components\Radio::make('pickup_method')
                            ->label('Phương thức nhận hàng')
                            ->options([
                                'store' => 'Nhận tại cửa hàng',
                                'delivery' => 'Giao hàng tận nơi',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('delivery_address')
                            ->label('Địa chỉ giao hàng')
                            ->visible(fn (callable $get) => $get('pickup_method') === 'delivery')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('special_instructions')
                            ->label('Yêu cầu đặc biệt')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Mã đơn hàng')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Khách hàng')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.phone')
                    ->label('Số điện thoại'),
                Tables\Columns\TextColumn::make('orderStatus.name')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pending' => 'warning',
                        'Processing' => 'info',
                        'Ready' => 'success',
                        'Completed' => 'success',
                        'Cancelled' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Tổng tiền')
                    ->money('VND'),
                Tables\Columns\TextColumn::make('pickup_method')
                    ->label('Nhận hàng')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'store' => 'Tại cửa hàng',
                        'delivery' => 'Giao hàng',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày đặt')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_id')
                    ->label('Trạng thái')
                    ->options(OrderStatus::pluck('name', 'id')),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Từ ngày'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Đến ngày'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Chi tiết')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Order $record): string => route('filament.admin.resources.orders.edit', ['record' => $record])),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\OrderItemsRelationManager::class,
            RelationManagers\FilesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}

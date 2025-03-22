<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;

class FilesRelationManager extends RelationManager
{
    protected static string $relationship = 'files';

    protected static ?string $recordTitleAttribute = 'name';
    
    protected static ?string $title = 'Files cần in';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin file')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Tên file')
                            ->disabled(),
                        Forms\Components\TextInput::make('size')
                            ->label('Kích thước')
                            ->formatStateUsing(fn (int $state): string => $this->formatBytes($state))
                            ->disabled(),
                        Forms\Components\TextInput::make('type')
                            ->label('Loại file')
                            ->disabled(),
                        Forms\Components\Toggle::make('is_processed')
                            ->label('Đã xử lý')
                            ->helperText('Đánh dấu nếu file đã được in')
                            ->required(),
                        Forms\Components\Select::make('orderItem.status')
                            ->label('Trạng thái')
                            ->options([
                                'pending' => 'Chờ xử lý',
                                'processing' => 'Đang xử lý',
                                'completed' => 'Hoàn thành',
                                'cancelled' => 'Đã hủy',
                            ])
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Tên file')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('orderItem.paperType.name')
                    ->label('Loại giấy'),
                Tables\Columns\TextColumn::make('orderItem.printOption.name')
                    ->label('Kiểu in'),
                Tables\Columns\TextColumn::make('type')
                    ->label('Định dạng')
                    ->formatStateUsing(fn (string $state): string => strtoupper(str_replace('application/', '', $state))),
                Tables\Columns\TextColumn::make('size')
                    ->label('Kích thước')
                    ->formatStateUsing(fn (int $state): string => $this->formatBytes($state)),
                Tables\Columns\IconColumn::make('is_processed')
                    ->label('Đã xử lý')
                    ->boolean(),
                Tables\Columns\TextColumn::make('orderItem.status')
                    ->label('Trạng thái')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Chờ xử lý',
                        'processing' => 'Đang xử lý',
                        'completed' => 'Hoàn thành',
                        'cancelled' => 'Đã hủy',
                        default => $state,
                    })
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
                Tables\Filters\SelectFilter::make('is_processed')
                    ->label('Trạng thái xử lý')
                    ->options([
                        '0' => 'Chưa xử lý',
                        '1' => 'Đã xử lý',
                    ]),
                Tables\Filters\SelectFilter::make('orderItem.status')
                    ->label('Trạng thái')
                    ->options([
                        'pending' => 'Chờ xử lý',
                        'processing' => 'Đang xử lý',
                        'completed' => 'Hoàn thành',
                        'cancelled' => 'Đã hủy',
                    ]),
            ])
            ->headerActions([
                // Không cần tạo mới files từ đây
            ])
            ->actions([
                Action::make('download')
                    ->label('Tải xuống')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn ($record) => asset('storage/' . $record->path))
                    ->openUrlInNewTab(),
                Action::make('mark_processed')
                    ->label('Đánh dấu đã in')
                    ->icon('heroicon-o-check')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Đánh dấu file đã in')
                    ->modalDescription('Xác nhận file này đã được in xong?')
                    ->modalSubmitActionLabel('Xác nhận')
                    ->action(function ($record) {
                        $record->update(['is_processed' => true]);
                        $record->orderItem->update(['status' => 'completed']);
                    })
                    ->visible(fn ($record) => !$record->is_processed),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('mark_processed_bulk')
                    ->label('Đánh dấu đã in')
                    ->icon('heroicon-o-check')
                    ->color('info')
                    ->requiresConfirmation()
                    ->action(function ($records) {
                        foreach ($records as $record) {
                            $record->update(['is_processed' => true]);
                            $record->orderItem->update(['status' => 'completed']);
                        }
                    }),
            ]);
    }
    
    private function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
} 
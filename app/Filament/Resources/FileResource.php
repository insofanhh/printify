<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FileResource\Pages;
use App\Filament\Resources\FileResource\RelationManagers;
use App\Models\File;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FileResource extends Resource
{
    protected static ?string $model = File::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';

    protected static ?string $navigationGroup = 'Quản lý Files';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Tên file gốc')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Người tải lên')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('disk_name')
                    ->label('Tên file trên ổ đĩa')
                    ->maxLength(255),
                Forms\Components\TextInput::make('path')
                    ->label('Đường dẫn file')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('size')
                    ->label('Kích thước file (bytes)')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('type')
                    ->label('Loại file')
                    ->required()
                    ->maxLength(500),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Tên file gốc')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Người tải lên')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Loại file')
                    ->searchable(),
                Tables\Columns\TextColumn::make('size')
                    ->label('Kích thước')
                    ->formatStateUsing(fn (int $state): string => number_format($state / 1024, 2) . ' KB'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('download')
                    ->label('Tải xuống')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (File $record): string => asset('storage/' . $record->file_path))
                    ->openUrlInNewTab(),
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
            'index' => Pages\ListFiles::route('/'),
            'create' => Pages\CreateFile::route('/create'),
            'edit' => Pages\EditFile::route('/{record}/edit'),
        ];
    }
}

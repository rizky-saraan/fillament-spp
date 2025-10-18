<?php

namespace App\Filament\Resources\Transactions\Schemas;

use App\Models\Department;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required()
                    ->default(fn() => 'TRX' . mt_rand(10000, 99999)),

                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),

                TextInput::make('payment_status')
                    ->readOnly()
                    ->default('pending'),

                Fieldset::make('Department')
                    ->schema([
                        Select::make('department_id')
                            ->label('Department name & semester')
                            ->required()
                            ->options(fn() => Department::all()->mapWithKeys(fn($d) => [
                                $d->id => "{$d->name} - semester {$d->semester}"
                            ])->toArray())
                            ->reactive()
                            ->afterStateUpdated(
                                fn(callable $set, $state) =>
                                $set('department_cost', Department::find($state)?->cost ?? 0)
                            ),

                        TextInput::make('department_cost')
                            ->label('Department Cost')
                            ->disabled()
                            ->default(0),
                    ]),
            ]);
    }
}

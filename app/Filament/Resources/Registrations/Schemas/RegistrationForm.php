<?php

namespace App\Filament\Resources\Registrations\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class RegistrationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('registration_code')
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->default(null),
                Select::make('program_id')
                    ->relationship('program', 'name')
                    ->required(),
                Select::make('class_id')
                    ->relationship('courseClass', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('applicant_name')
                    ->required(),
                TextInput::make('applicant_email')
                    ->email()
                    ->required(),
                TextInput::make('applicant_phone')
                    ->tel()
                    ->required(),
                Select::make('preferred_days')
                    ->options([
            'mon_wed' => 'Mon wed',
            'tue_thu' => 'Tue thu',
            'wed_fri' => 'Wed fri',
            'sat_sun' => 'Sat sun',
            'request' => 'Request',
        ])
                    ->default(null),
                TextInput::make('preferred_time')
                    ->default(null),
                DateTimePicker::make('placement_test_at'),
                Textarea::make('placement_test_result')
                    ->default(null)
                    ->columnSpanFull(),
                Select::make('payment_method')
                    ->options([
            'qris' => 'Qris',
            'bank_transfer' => 'Bank transfer',
            'virtual_account' => 'Virtual account',
            'ewallet' => 'Ewallet',
            'manual' => 'Manual',
        ])
                    ->default(null),
                TextInput::make('payment_amount')
                    ->numeric()
                    ->default(null),
                TextInput::make('original_amount')->numeric()->disabled(),
                TextInput::make('discount_amount')->numeric()->disabled(),
                TextInput::make('final_amount')->numeric()->disabled(),
                TextInput::make('program_promotion_title')->disabled(),
                TextInput::make('midtrans_order_id')->disabled(),
                TextInput::make('midtrans_redirect_url')->disabled()->columnSpanFull(),
                Select::make('payment_status')
                    ->options([
                        'waiting_payment' => 'Waiting payment',
                        'paid' => 'Paid',
                        'expired' => 'Expired',
                        'failed' => 'Failed',
                    ]),
                TextInput::make('payment_gateway_id')
                    ->default(null),
                TextInput::make('payment_proof')
                    ->default(null),
                DateTimePicker::make('paid_at'),
                Select::make('status')
                    ->options([
            'pending_payment' => 'Pending payment',
            'paid' => 'Paid',
            'placement_test' => 'Placement test',
            'enrolled' => 'Enrolled',
            'rejected' => 'Rejected',
            'cancelled' => 'Cancelled',
        ])
                    ->default('pending_payment')
                    ->required(),
                Textarea::make('notes')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}

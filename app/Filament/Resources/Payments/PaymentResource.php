<?php

namespace App\Filament\Resources\Payments;

use App\Filament\Resources\Payments\Pages\ListPayments;
use App\Filament\Resources\Payments\Pages\ViewPayment;
use App\Models\Registration;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PaymentResource extends Resource
{
    protected static ?string $model = Registration::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static string|\UnitEnum|null $navigationGroup = 'Operations';

    protected static ?string $navigationLabel = 'Payments';

    protected static ?string $modelLabel = 'Payment';

    protected static ?string $pluralModelLabel = 'Payments';

    protected static ?string $slug = 'payments';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['program', 'programPromotion'])
            ->where(function (Builder $query): void {
                $query->whereNotNull('payment_amount')
                    ->orWhereNotNull('midtrans_order_id')
                    ->orWhereNotNull('payment_gateway_id');
            });
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Payment summary')->schema([
                Text::make(fn (Registration $record): string => 'Code: '.$record->registration_code),
                Text::make(fn (Registration $record): string => 'Applicant: '.$record->applicant_name.' <'.$record->applicant_email.'>'),
                Text::make(fn (Registration $record): string => 'Program: '.($record->program?->name ?? '-')),
                Text::make(fn (Registration $record): string => 'Status: '.($record->payment_status ?: $record->status)),
                Text::make(fn (Registration $record): string => 'Amount: Rp '.number_format((float) ($record->final_amount ?: $record->payment_amount), 0, ',', '.')),
                Text::make(fn (Registration $record): string => 'Promo: '.($record->program_promotion_title ?: '-')),
                Text::make(fn (Registration $record): string => 'Midtrans order: '.($record->midtrans_order_id ?: '-')),
                Text::make(fn (Registration $record): string => 'Redirect URL: '.($record->midtrans_redirect_url ?: '-')),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('registration_code')->searchable()->sortable(),
                TextColumn::make('applicant_name')->searchable()->sortable(),
                TextColumn::make('program.name')->label('Program')->searchable()->sortable(),
                TextColumn::make('payment_status')->badge()->sortable(),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('final_amount')->money('IDR')->sortable(),
                TextColumn::make('program_promotion_title')->label('Promo')->searchable(),
                TextColumn::make('paid_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('payment_status')
                    ->options([
                        'waiting_payment' => 'Waiting payment',
                        'paid' => 'Paid',
                        'expired' => 'Expired',
                        'cancelled' => 'Cancelled',
                        'failed' => 'Failed',
                    ]),
                SelectFilter::make('status')
                    ->options([
                        'pending_payment' => 'Pending payment',
                        'paid' => 'Paid',
                        'placement_test' => 'Placement test',
                        'enrolled' => 'Enrolled',
                        'rejected' => 'Rejected',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->recordActions([
                Action::make('view')
                    ->url(fn (Registration $record): string => static::getUrl('view', ['record' => $record])),
                Action::make('retry_midtrans')
                    ->label('Retry Midtrans')
                    ->icon('heroicon-m-arrow-path')
                    ->requiresConfirmation()
                    ->action(fn (Registration $record) => app(\App\Services\MidtransPaymentService::class)->createTransaction($record)),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayments::route('/'),
            'view' => ViewPayment::route('/{record}'),
        ];
    }
}

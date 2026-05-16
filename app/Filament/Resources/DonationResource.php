<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DonationResource\Pages;
use App\Models\Donation;
use App\Models\DonationCampaign;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

class DonationResource extends Resource
{
    protected static ?string $model = Donation::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Donations';
    protected static ?string $navigationGroup = '💰 Monetization';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('campaign_id')
                ->label('Campaign')->required()
                ->options(fn () => DonationCampaign::pluck('title', 'id')),
            Forms\Components\TextInput::make('donor_name')->required(),
            Forms\Components\TextInput::make('donor_email')->email(),
            Forms\Components\TextInput::make('donor_phone'),
            Forms\Components\TextInput::make('amount')->numeric()->required()->prefix('₹'),
            Forms\Components\Select::make('currency')->options([
                'INR' => 'INR', 'USD' => 'USD', 'EUR' => 'EUR', 'GBP' => 'GBP',
            ])->default('INR'),
            Forms\Components\Select::make('payment_method')->options([
                'upi' => 'UPI', 'bank_transfer' => 'Bank Transfer',
                'razorpay' => 'Razorpay', 'stripe' => 'Stripe', 'paypal' => 'PayPal',
                'cash' => 'Cash', 'other' => 'Other',
            ])->required(),
            Forms\Components\TextInput::make('payment_reference')->helperText('UTR / transaction ID / receipt number.'),
            Forms\Components\Select::make('status')->required()->options([
                'pending' => 'Pending', 'confirmed' => 'Confirmed',
                'failed' => 'Failed', 'refunded' => 'Refunded',
            ])->default('pending')->helperText('Only "confirmed" donations count toward the campaign total.'),
            Forms\Components\Textarea::make('message')->rows(3),
            Forms\Components\Toggle::make('is_anonymous'),
            Forms\Components\Toggle::make('show_on_wall'),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('campaign.title')->limit(30)->label('Campaign'),
            Tables\Columns\TextColumn::make('donor_name')->searchable(),
            Tables\Columns\TextColumn::make('amount')->money(fn ($record) => $record->currency)->sortable(),
            Tables\Columns\BadgeColumn::make('payment_method'),
            Tables\Columns\BadgeColumn::make('status')->colors([
                'warning' => 'pending', 'success' => 'confirmed',
                'danger' => 'failed', 'gray' => 'refunded',
            ]),
            Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
        ])->filters([
            Tables\Filters\SelectFilter::make('campaign_id')
                ->label('Campaign')
                ->options(fn () => DonationCampaign::pluck('title', 'id')),
            Tables\Filters\SelectFilter::make('status')->options([
                'pending' => 'Pending', 'confirmed' => 'Confirmed',
                'failed' => 'Failed', 'refunded' => 'Refunded',
            ]),
        ])->actions([
            Action::make('confirm')->color('success')->icon('heroicon-o-check')
                ->visible(fn (Donation $r) => $r->status !== 'confirmed')
                ->action(fn (Donation $r) => $r->update(['status' => 'confirmed'])),
            Action::make('reject')->color('danger')->icon('heroicon-o-x-mark')
                ->visible(fn (Donation $r) => $r->status === 'pending')
                ->action(fn (Donation $r) => $r->update(['status' => 'failed'])),
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDonations::route('/'),
            'create' => Pages\CreateDonation::route('/create'),
            'edit' => Pages\EditDonation::route('/{record}/edit'),
        ];
    }
}

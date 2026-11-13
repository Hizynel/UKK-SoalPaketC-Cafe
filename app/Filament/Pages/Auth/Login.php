<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\TextInput;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    protected function getEmailFormComponent(): TextInput
    {
        return TextInput::make('nomer_telepon')
            ->label('Nomor Telepon')
            ->required()
            ->autocomplete('tel')
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'nomer_telepon' => $data['nomer_telepon'],
            'password' => $data['password'],
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.nomer_telepon' => 'Nomor Telepon atau password salah.',
        ]);
    }
}
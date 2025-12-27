<?php

namespace Modules\User\Filament\Pages;

use Filament\Auth\Pages\Login;
use Filament\Forms\Components\TextInput;

use Filament\Schemas\Schema;
use Illuminate\Validation\ValidationException;

class CustomLogin extends Login
{
    protected array $extraBodyAttributes = [
        'class' => 'filament-login-page',
    ];

    public function form(Schema $schema): Schema
    {
       return $schema
            ->components([
                $this->getLoginFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ]);

    }

    protected function getLoginFormComponent()
    {
        return TextInput::make('login')
            ->label(__('username/email/pin'))
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);

    }

    protected function getCredentialsFromFormData(array $data): array
    {
        if (isset($data['login']) && preg_match('/^\d{14}$/', $data['login'])) {
            return [
                'pin' => $data['login'],
                'password' => $data['password'],
            ];
        }

        $loginType = filter_var($data['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        return [
            $loginType => $data['login'],
            'password' => $data['password'],
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.login' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }
}

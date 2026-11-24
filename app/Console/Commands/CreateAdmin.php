<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

#[Signature('loja:criar-admin {email : E-mail de acesso} {--nome=Administrador : Nome exibido no painel}')]
#[Description('Cria um administrador da loja (a senha é pedida sem aparecer no terminal)')]
class CreateAdmin extends Command
{
    public function handle(): int
    {
        $data = [
            'name' => (string) $this->option('nome'),
            'email' => mb_strtolower(trim((string) $this->argument('email'))),
            'password' => (string) $this->secret('Senha (mínimo 8 caracteres, com letras e números)'),
        ];
        $data['password_confirmation'] = (string) $this->secret('Confirme a senha');

        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = new User(['name' => $data['name'], 'email' => $data['email'], 'password' => $data['password']]);
        $user->is_admin = true;
        $user->save();

        $this->info("Administrador {$user->email} criado. Acesse /entrar para usar o painel.");

        return self::SUCCESS;
    }
}

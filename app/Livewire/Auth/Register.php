<?php

namespace App\Livewire\Auth;

use App\Enums\UserType;
use App\Models\Balance;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class Register extends Component
{
    public string $name = '';
    public string $document = '';
    public string $type = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'document' => ['required', 'string', 'max:20', 'unique:users,document'],
            'type' => ['required', 'in:' . UserType::PESSOA->value . ',' . UserType::EMPRESA->value],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(6)],
        ];
    }

    public function register(): void
    {
        $this->validate();

        $user = User::create([
            'name'     => $this->name,
            'document' => $this->document,
            'type'     => $this->type, 
            'email'    => $this->email,
            'password' => Hash::make($this->password),
        ]);

        Balance::create([
            'user_id' => $user->id,
            'amount'  => 0,
        ]);

        Auth::login($user);

        $this->redirectRoute('dashboard');
    }

    public function render()
    {
        return view('livewire.pages.auth.register');
    }
}

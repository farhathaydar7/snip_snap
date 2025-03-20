<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-admin {username?} {email?} {password?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin user for testing the API';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $username = $this->argument('username') ?? $this->ask('Enter username:', 'admin');
        $email = $this->argument('email') ?? $this->ask('Enter email:', 'admin@example.com');
        $password = $this->argument('password') ?? $this->secret('Enter password:') ?? 'password';

        // Check if the user already exists
        $existingUser = User::where('email', $email)->orWhere('username', $username)->first();
        if ($existingUser) {
            $this->error("User with email $email or username $username already exists!");
            return Command::FAILURE;
        }

        // Create the user
        $user = User::create([
            'username' => $username,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $this->info("Admin user created successfully with ID: {$user->id}");
        return Command::SUCCESS;
    }
}

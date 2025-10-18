<?php

namespace Modules\INFRASTOCK\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CheckUserData extends Command
{
    protected $signature = 'infrastock:check-user {id}';
    protected $description = 'Verifica los datos de un usuario específico';

    public function handle()
    {
        $id = $this->argument('id');
        
        $this->info("Verificando usuario ID: {$id}");
        
        $user = User::with(['person', 'roles'])->withTrashed()->find($id);
        
        if (!$user) {
            $this->error('Usuario no encontrado');
            return;
        }
        
        $this->info("Usuario encontrado: {$user->nickname}");
        $this->info("Person ID: {$user->person_id}");
        $this->info("Person exists: " . ($user->person ? 'Yes' : 'No'));
        
        if ($user->person) {
            $this->info("Document Type: " . ($user->person->document_type ?? 'N/A'));
            $this->info("Phone: " . ($user->person->phone ?? 'N/A'));
            $this->info("First Name: " . ($user->person->first_name ?? 'N/A'));
            $this->info("Last Name: " . ($user->person->first_last_name ?? 'N/A'));
        }
        
        if ($user->roles->count() > 0) {
            $this->info("Roles: " . $user->roles->pluck('name')->implode(', '));
        } else {
            $this->warn('No roles assigned');
        }
    }
}

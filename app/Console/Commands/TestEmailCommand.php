<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Modules\INFRASTOCK\Mail\NewSupplyRequestNotification;
use Modules\INFRASTOCK\Entities\Request;
use App\Models\User;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prueba el envío de correo electrónico del sistema INFRASTOCK';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('=== Prueba de Envío de Correo INFRASTOCK ===');
        $this->newLine();

        // Verificar configuración
        $this->info('Verificando configuración...');
        $this->line('MAIL_MAILER: ' . config('mail.default'));
        $this->line('MAIL_HOST: ' . config('mail.mailers.smtp.host'));
        $this->line('MAIL_PORT: ' . config('mail.mailers.smtp.port'));
        $this->line('MAIL_FROM_ADDRESS: ' . config('mail.from.address'));
        $this->line('MAIL_FROM_NAME: ' . config('mail.from.name'));
        $this->newLine();

        // Obtener email de destino
        $email = $this->argument('email');
        if (!$email) {
            $email = $this->ask('Ingresa el correo electrónico de destino para la prueba');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('El correo electrónico no es válido.');
            return 1;
        }

        // Crear una solicitud de prueba
        $this->info('Creando solicitud de prueba...');
        
        try {
            // Buscar un usuario de prueba o crear datos ficticios
            $testUser = User::first();
            if (!$testUser) {
                $this->error('No se encontró ningún usuario en la base de datos.');
                return 1;
            }

            // Crear una solicitud ficticia para la prueba
            $testRequest = new Request();
            $testRequest->id = 999;
            $testRequest->created_at = now();
            $testRequest->description = 'Solicitud de prueba del sistema';
            
            // Simular items
            $testRequest->setRelation('items', collect([
                (object) [
                    'equipment' => (object) ['name' => 'Insumo de Prueba 1', 'category' => null],
                    'requested_amount' => 10
                ],
                (object) [
                    'equipment' => (object) ['name' => 'Insumo de Prueba 2', 'category' => null],
                    'requested_amount' => 5
                ]
            ]));

            $testRequest->setRelation('user', $testUser);

            $this->info('Enviando correo de prueba a: ' . $email);
            $this->newLine();

            // Enviar correo
            Mail::to($email)->send(
                new NewSupplyRequestNotification(
                    $testRequest,
                    $testUser->name ?? 'Usuario de Prueba',
                    'Sistema',
                    2,
                    'Insumo de Prueba 1, Insumo de Prueba 2'
                )
            );

            $this->info('✅ Correo enviado exitosamente!');
            $this->line('Revisa la bandeja de entrada de: ' . $email);
            $this->newLine();
            $this->info('Si no recibes el correo, verifica:');
            $this->line('1. La configuración SMTP en el archivo .env');
            $this->line('2. La carpeta de spam');
            $this->line('3. Los logs de Laravel: storage/logs/laravel.log');

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Error al enviar el correo:');
            $this->error($e->getMessage());
            $this->newLine();
            $this->warn('Verifica tu configuración SMTP en el archivo .env');
            return 1;
        }
    }
}

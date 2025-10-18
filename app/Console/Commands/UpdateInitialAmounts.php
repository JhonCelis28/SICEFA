<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\INFRASTOCK\Entities\Equipment;

class UpdateInitialAmounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'infrastock:update-initial-amounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza la cantidad inicial de todos los equipos basándose en la cantidad actual';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Actualizando cantidades iniciales de equipos...');
        
        $equipments = Equipment::whereNull('initial_amount')->orWhere('initial_amount', 0)->get();
        
        $updated = 0;
        foreach ($equipments as $equipment) {
            $equipment->update(['initial_amount' => $equipment->amount]);
            $updated++;
        }
        
        $this->info("Se actualizaron {$updated} equipos.");
        $this->info('¡Cantidades iniciales actualizadas exitosamente!');
        
        return 0;
    }
}

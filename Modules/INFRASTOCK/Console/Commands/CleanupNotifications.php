<?php

namespace Modules\INFRASTOCK\Console\Commands;

use Illuminate\Console\Command;
use Modules\INFRASTOCK\Entities\Notification;

/**
 * Comando para limpiar notificaciones expiradas según su prioridad.
 *
 * Ejecutar manualmente: php artisan infrastock:cleanup-notifications
 * Programar en el scheduler para ejecución diaria automática.
 *
 * Sistema de prioridades:
 * 🔴 CRÍTICA (supply_expiring, tool_maintenance): Leídas se borran a los 30 días, no leídas a los 60 días
 * 🟠 ALTA (request_created, surplus_reported, loan_created): Leídas a 15 días, no leídas a 30 días
 * 🟡 MEDIA (request_rejected, loan_rejected): Leídas a 7 días, no leídas a 15 días
 * 🟢 BAJA (request_approved, loan_approved): Leídas a 3 días, no leídas a 7 días
 */
class CleanupNotifications extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'infrastock:cleanup-notifications {--dry-run : Mostrar qué se eliminaría sin hacer cambios}';

    /**
     * The console command description.
     */
    protected $description = 'Limpia notificaciones expiradas de INFRASTOCK según su prioridad y estado de lectura';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->info('🔍 Modo simulación (dry-run) — No se eliminarán registros.');
            $this->newLine();
        }

        $this->info('🧹 Iniciando limpieza de notificaciones INFRASTOCK...');
        $this->newLine();

        // Mostrar la política de retención
        $this->table(
            ['Prioridad', 'Tipos', 'Retención (No leída)', 'Retención (Leída)'],
            [
                ['🔴 Crítica', 'supply_expiring, tool_maintenance', '60 días', '30 días'],
                ['🟠 Alta', 'request_created, surplus_reported, loan_created, return_created', '30 días', '15 días'],
                ['🟡 Media', 'request_rejected, loan_rejected, return_rejected', '15 días', '7 días'],
                ['🟢 Baja', 'request_approved, loan_approved, return_approved', '7 días', '3 días'],
            ]
        );
        $this->newLine();

        if ($isDryRun) {
            $stats = $this->simulateCleanup();
        } else {
            $stats = Notification::cleanupExpired();
        }

        if ($stats['deleted'] === 0) {
            $this->info('✅ No hay notificaciones expiradas para eliminar.');
        } else {
            $this->warn("🗑️  Se " . ($isDryRun ? 'eliminarían' : 'eliminaron') . " {$stats['deleted']} notificación(es):");
            $this->newLine();

            $rows = [];
            foreach ($stats['by_priority'] as $type => $data) {
                $rows[] = [
                    $this->formatPriority($data['priority']),
                    $type,
                    $data['read_deleted'],
                    $data['unread_deleted'],
                    $data['read_deleted'] + $data['unread_deleted'],
                ];
            }

            $this->table(
                ['Prioridad', 'Tipo', 'Leídas eliminadas', 'No leídas eliminadas', 'Total'],
                $rows
            );
        }

        // Mostrar estadísticas generales
        $this->newLine();
        $totalNotifications = Notification::count();
        $unreadNotifications = Notification::whereNull('read_at')->count();
        $readNotifications = $totalNotifications - $unreadNotifications;

        $this->info("📊 Estado actual de notificaciones:");
        $this->line("   Total: {$totalNotifications} | No leídas: {$unreadNotifications} | Leídas: {$readNotifications}");
        $this->newLine();

        return 0;
    }

    /**
     * Simular la limpieza sin ejecutar cambios.
     */
    private function simulateCleanup(): array
    {
        $stats = ['deleted' => 0, 'by_priority' => []];

        foreach (Notification::PRIORITY_MAP as $type => $priority) {
            $retentionRead = Notification::RETENTION_READ[$priority];
            $retentionUnread = Notification::RETENTION_UNREAD[$priority];

            $countRead = Notification::where('type', $type)
                ->whereNotNull('read_at')
                ->where('read_at', '<', now()->subDays($retentionRead))
                ->count();

            $countUnread = Notification::where('type', $type)
                ->whereNull('read_at')
                ->where('created_at', '<', now()->subDays($retentionUnread))
                ->count();

            $total = $countRead + $countUnread;
            if ($total > 0) {
                $stats['by_priority'][$type] = [
                    'priority' => $priority,
                    'read_deleted' => $countRead,
                    'unread_deleted' => $countUnread,
                ];
            }
            $stats['deleted'] += $total;
        }

        return $stats;
    }

    /**
     * Formatear la prioridad para mostrar en consola.
     */
    private function formatPriority(string $priority): string
    {
        return match ($priority) {
            'critical' => '🔴 Crítica',
            'high'     => '🟠 Alta',
            'medium'   => '🟡 Media',
            'low'      => '🟢 Baja',
            default    => '⚪ N/A',
        };
    }
}

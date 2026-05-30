<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class TenancyAudit extends Command
{
    protected $signature   = 'tenancy:audit {--fix : Show SQL to correct cross-contaminated rows}';
    protected $description = 'Audit each tenant DB for cross-contamination between subdomains';

    // Map subdomain → DB name (mirrors IdentifyTenant logic)
    private function getTenantDatabases(): array
    {
        // Discover all cp56849_* databases except root
        $rows = DB::connection('mysql')->select("SHOW DATABASES LIKE 'cp56849_%'");
        $dbs  = [];
        foreach ($rows as $row) {
            $name = array_values((array) $row)[0];
            if ($name !== 'cp56849_root') {
                $subdomain     = str_replace('cp56849_', '', $name);
                $dbs[$subdomain] = $name;
            }
        }
        return $dbs;
    }

    public function handle(): int
    {
        $tenants = $this->getTenantDatabases();

        if (empty($tenants)) {
            $this->warn('No tenant databases found (pattern: cp56849_*).');
            return 0;
        }

        $this->info('Found ' . count($tenants) . ' tenant database(s): ' . implode(', ', array_keys($tenants)));
        $this->newLine();

        $issues = 0;

        foreach ($tenants as $subdomain => $dbName) {
            $this->line("── Auditing <fg=cyan>{$subdomain}</> ({$dbName})");

            Config::set('database.connections.tenant_audit.driver',   'mysql');
            Config::set('database.connections.tenant_audit.host',     Config::get('database.connections.mysql.host'));
            Config::set('database.connections.tenant_audit.port',     Config::get('database.connections.mysql.port'));
            Config::set('database.connections.tenant_audit.username', Config::get('database.connections.mysql.username'));
            Config::set('database.connections.tenant_audit.password', Config::get('database.connections.mysql.password'));
            Config::set('database.connections.tenant_audit.database', $dbName);
            Config::set('database.connections.tenant_audit.charset',  'utf8mb4');
            Config::set('database.connections.tenant_audit.collation','utf8mb4_unicode_ci');

            DB::purge('tenant_audit');

            try {
                $conn = DB::connection('tenant_audit');

                // 1. Table row counts
                $tables = ['users', 'invoices', 'invoice_fines', 'invoice_pays', 'menus', 'forms'];
                foreach ($tables as $table) {
                    try {
                        $count = $conn->table($table)->count();
                        $this->line("   {$table}: {$count} rows");
                    } catch (\Throwable $e) {
                        $this->line("   {$table}: <fg=yellow>table missing or inaccessible</>");
                    }
                }

                // 2. Cross-DB reference check — users referencing grops from other tenants
                // In this architecture each DB is self-contained, so orphaned foreign keys indicate contamination.
                $this->checkOrphans($conn, $subdomain, 'user_grops', 'grop_id', 'grops', 'id', $issues);
                $this->checkOrphans($conn, $subdomain, 'invoice_fines', 'id_invoice', 'invoices', 'id', $issues);
                $this->checkOrphans($conn, $subdomain, 'invoice_pays', 'id_invoice', 'invoices', 'id', $issues);

                // 3. Duplicate user_grops entries (known to cause wrong invoices)
                try {
                    $dupes = $conn->select(
                        'SELECT user_id, grop_id, COUNT(*) AS cnt FROM user_grops GROUP BY user_id, grop_id HAVING cnt > 1'
                    );
                    if (!empty($dupes)) {
                        $issues++;
                        $this->warn("   [!] Duplicate user_grops entries: " . count($dupes) . " pair(s)");
                        foreach ($dupes as $d) {
                            $this->line("       user_id={$d->user_id} grop_id={$d->grop_id} count={$d->cnt}");
                        }
                        if ($this->option('fix')) {
                            $this->comment("   FIX: DELETE FROM user_grops WHERE id NOT IN (SELECT MIN(id) FROM user_grops GROUP BY user_id, grop_id);");
                        }
                    }
                } catch (\Throwable $e) {
                    // table may not exist
                }

            } catch (\Throwable $e) {
                $this->error("   Could not connect to {$dbName}: " . $e->getMessage());
                $issues++;
            }

            $this->newLine();
        }

        DB::purge('tenant_audit');

        if ($issues === 0) {
            $this->info('No cross-contamination or integrity issues found.');
        } else {
            $this->error("{$issues} issue(s) found — review warnings above.");
        }

        return $issues > 0 ? 1 : 0;
    }

    private function checkOrphans($conn, string $subdomain, string $childTable, string $fk, string $parentTable, string $pk, int &$issues): void
    {
        try {
            $orphans = $conn->select(
                "SELECT COUNT(*) AS cnt FROM {$childTable} c
                 LEFT JOIN {$parentTable} p ON c.{$fk} = p.{$pk}
                 WHERE p.{$pk} IS NULL AND c.{$fk} IS NOT NULL"
            );
            $count = $orphans[0]->cnt ?? 0;
            if ($count > 0) {
                $issues++;
                $this->warn("   [!] {$childTable}.{$fk} → {$parentTable}: {$count} orphaned row(s)");
            }
        } catch (\Throwable $e) {
            // table may not exist in this tenant DB
        }
    }
}

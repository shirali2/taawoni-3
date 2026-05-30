<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class IdentifyTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $host = $request->getHost();

        // 1. Handle 301 Redirect from old domain
        if (strpos($host, 'maskan-bimehiran.com') !== false) {
            $newHost = str_replace('maskan-bimehiran.com', 'taavoniiran.ir', $host);
            return redirect()->to($request->getScheme() . '://' . $newHost . $request->getRequestUri(), 301);
        }

        // 2. Extract subdomain and set per-tenant session cookie name
        $normalizedHost = strtolower(explode(':', $host)[0]);
        $hostParts = explode('.', $normalizedHost);
        $subdomain = (count($hostParts) >= 3 && $hostParts[0] !== 'www') ? $hostParts[0] : null;
        $tenantKey = $subdomain ?? 'root';
        Config::set('session.cookie', 'tenant_' . $tenantKey . '_session');

        // 3. Identify Tenant and Switch Database
        $dbName = $this->getDatabaseName($host);

        if ($dbName) {
            $currentDb = Config::get('database.connections.mysql.database');
            
            if ($dbName !== $currentDb) {
                Config::set('database.connections.mysql.database', $dbName);
                
                try {
                    // Purge and reconnect to apply changes
                    DB::purge('mysql');
                    DB::reconnect('mysql');
                    
                    // Simple check to ensure connection is valid
                    DB::connection()->getPdo();
                } catch (\Exception $e) {
                    // If connection fails, fallback to root database
                    Config::set('database.connections.mysql.database', 'cp56849_root');
                    DB::purge('mysql');
                    DB::reconnect('mysql');
                }
            }
        }

        return $next($request);
    }

    /**
     * Determine the database name based on the host.
     *
     * @param string $host
     * @return string
     */
    protected function getDatabaseName($host)
    {
        $host = strtolower(explode(':', $host)[0]);
        $parts = explode('.', $host);

        $subdomain = (count($parts) >= 3 && $parts[0] !== 'www') ? $parts[0] : null;

        if (!$subdomain) {
            return 'cp56849_root';
        }

        // Always return the target DB name — handle() will fallback to root if connection fails
        return 'cp56849_' . $subdomain;
    }
}

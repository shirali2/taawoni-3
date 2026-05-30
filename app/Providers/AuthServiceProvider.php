<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        $permissions = [
            // Task 8: granular note permissions
            'notes.create'            => ['admin', 'manager'],
            'notes.list'              => ['admin', 'manager'],
            'notes.edit'              => ['admin', 'manager'],
            'notes.delete'            => ['admin'],
            'notes.approve'           => ['admin', 'manager'],
            'notes.approve_all'       => ['admin'],
            'notes.toggle_visibility' => ['admin', 'manager'],
            'notes.toggle_archive'    => ['admin', 'manager'],
            'notes.report_export'     => ['admin', 'manager'],
            'notes.view_user_side'    => ['admin', 'manager'],
            'notes.view_admin_side'   => ['admin'],
            // backward-compat alias kept during transition
            'notes.edit_delete'       => ['admin', 'manager'],
            'sms.create'              => ['admin', 'manager'],
            'sms.list'                => ['admin', 'manager'],
        ];

        foreach ($permissions as $ability => $roles) {
            Gate::define($ability, function ($user = null) use ($roles) {
                // Session-based admin auth
                if (session()->has('admin')) {
                    return in_array('admin', $roles);
                }
                // Session-based manager auth
                if (session()->has('manager')) {
                    return in_array('manager', $roles);
                }
                // Standard Laravel Auth user fallback
                if ($user) {
                    return in_array($user->role ?? $user->type ?? '', $roles);
                }
                return false;
            });
        }
    }
}

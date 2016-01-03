<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\Access\Gate as GateContract;
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
     * Register any application authentication / authorization services.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate  $gate
     * @return void
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies($gate);

		$gate->before(function ($user, $ability) {
		    if ($user->isAdmin()) {
		        return true;
		    }
		});

		$gate->define('admin', function ($user) {
            return $user->isAdmin();
        });
        
        $gate->define('view-publishers', function ($user) {
            return $user->isManager();
        });
        
        $gate->define('update-publishers', function ($user) {
            return $user->isManager();
        });
        
        $gate->define('delete-publishers', function ($user) {
            return $user->isAdmin();
        });
        
        $gate->define('update-territories', function ($user) {
            return $user->isManager();
        });
        
        $gate->define('delete-teritorries', function ($user) {
            return $user->isAdmin();
        });
        
        $gate->define('create-addresses', function ($user) {
            return $user->isManager();
        });
        
        $gate->define('update-addresses', function ($user) {
            return $user->isEditor();
        });
        
        $gate->define('delete-addresses', function ($user) {
            return $user->isAdmin();
        });
        
        $gate->define('create-notes', function ($user) {
            return $user->isEditor();
        });
        
        $gate->define('update-notes', function ($user, $note) {
            return $user->isOwner($note);
        });
        
    }
}

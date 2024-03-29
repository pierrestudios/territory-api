<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies();

        $gate->before(
            function ($user, $ability) {
                if ($user->isAdmin()) {
                    return true;
                }
            }
        );

        $gate->define(
            'admin', function ($user) {
                return $user->isAdmin();
            }
        );

        $gate->define(
            'view-publishers', function ($user) {
                return $user->isManager();
            }
        );

        $gate->define(
            'update-publishers', function ($user) {
                return $user->isManager();
            }
        );

        $gate->define(
            'delete-publishers', function ($user) {
                return $user->isAdmin();
            }
        );

        $gate->define(
            'update-territories', function ($user) {
                return $user->isManager();
            }
        );

        $gate->define(
            'delete-teritorries', function ($user) {
                return $user->isAdmin();
            }
        );

        $gate->define(
            'create-addresses', function ($user) {
                return $user->isEditor();
            }
        );

        $gate->define(
            'update-addresses', function ($user) {
                return $user->isEditor();
            }
        );

        $gate->define(
            'delete-addresses', function ($user) {
                return $user->isAdmin();
            }
        );

        $gate->define(
            'soft-delete-addresses', function ($user) {
                return $user->isEditor();
            }
        );

        $gate->define(
            'create-notes', function ($user) {
                return $user->isNoteEditor();
            }
        );

        $gate->define(
            'update-notes', function ($user, $note) {
                return $user->isOwner($note);
            }
        );

        $gate->define(
            'create-phones', function ($user) {
                return $user->isNoteEditor();
            }
        );

        $gate->define(
            'update-phones', function ($user, $phone) {
                return $user->isOwner($phone);
            }
        );
    }
}

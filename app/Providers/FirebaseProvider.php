<?php

namespace App\Providers;

use App\Providers\ServiceProvider;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class FirebaseProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton(Factory::class, function ($app)
        {
            $serviceAccount = ServiceAccount::fromJsonFile('/storage/app/public/BeeCreativeManagementLocal.json');
            // dd($serviceAccount);
            $firebase = (new Factory)
                ->withServiceAccount($serviceAccount)
                // The following line is optional if the project id in your credentials file
                // is identical to the subdomain of your Firebase project. If you need it,
                // make sure to replace the URL with the URL of your project.
                ->create();
            // dd($firebase);
            return $firebase;

        });
        // This assumes that you have placed the Firebase credentials in the same directory
        // as this PHP file.

    }
}

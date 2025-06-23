<?php

    namespace App\Providers;

    use Illuminate\Support\Facades\Broadcast;
    use Illuminate\Support\ServiceProvider;

    class BroadcastServiceProvider extends ServiceProvider
    {
        /**
         * Register any application services.
         */
        public function register(): void
        {
            //
        }

        /**
         * Bootstrap any application services.
         */
        public function boot(): void
        {
            // ¡ESTA LÍNEA ES CRUCIAL!
            Broadcast::routes(); // Si tienes un prefijo de API, podría ser Broadcast::routes(['prefix' => 'api']);

            require base_path('routes/channels.php'); // ¡Y esta línea también!
        }
    }
    
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
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
        \Illuminate\Support\Facades\Blade::directive('fonts', function () {
            return <<<'HTML'
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">
HTML;
        });

        \Illuminate\Support\Facades\Blade::directive('waUrl', function ($expression) {
            return "<?php echo (str_starts_with((string) ({$expression}), 'http') ? {$expression} : 'https://wa.me/' . preg_replace('/^0/', '62', preg_replace('/\\D/', '', (string) ({$expression})))); ?>";
        });

        // ponytail: view composer caches Firebase read inside FirebaseService memory/file cache
        \Illuminate\Support\Facades\View::composer('layouts.app', function ($view) {
            try {
                $contact = app(\App\Services\FirebaseService::class)->getDocument('contact', 'main') ?? [];
                $view->with('siteContact', $contact);
            } catch (\Throwable) {
                $view->with('siteContact', []);
            }
        });

        \Illuminate\Support\Facades\View::composer('layouts.admin', function ($view) {
            try {
                $messages = app(\App\Services\FirebaseService::class)->getCollection('messages');
                $unread = count(array_filter($messages, fn ($m) => ! ($m['is_read'] ?? false)));
                $view->with('unreadMessagesCount', $unread);
            } catch (\Throwable) {
                $view->with('unreadMessagesCount', 0);
            }
        });
    }
}

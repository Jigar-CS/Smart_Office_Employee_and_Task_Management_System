<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:check-inactive-users {--days=30 : Mark users inactive after this many days without login}', function () {
    $inactiveDays = (int) $this->option('days');
    $inactiveBefore = now()->subDays($inactiveDays);

    $updatedUsers = User::query()
        ->where('status', 1)
        ->where(function ($query) use ($inactiveBefore) {
            $query->whereNull('last_login_at')
                ->orWhere('last_login_at', '<', $inactiveBefore);
        })
        ->update(['status' => 0]);

    $this->info(sprintf(
        'Marked %d user(s) inactive using a %d-day inactivity threshold.',
        $updatedUsers,
        $inactiveDays
    ));
})->purpose('Mark stale users inactive based on last login date.');

Schedule::command('app:check-inactive-users --days=30')
    ->dailyAt('02:00')
    ->timezone(config('app.timezone'))
    ->withoutOverlapping();

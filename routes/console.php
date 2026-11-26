<?php

use App\Models\Cafe;
use App\Models\Event;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Trials and paid periods that ended become "overdue". Admins decide when to suspend.
Artisan::command('xmenu:subscriptions', function () {
    $trials = Cafe::where('status', 'trial')->whereNotNull('trial_ends_at')->where('trial_ends_at', '<', now())
        ->where(fn ($q) => $q->whereNull('paid_until')->orWhere('paid_until', '<', now()))->get();
    $paid = Cafe::where('status', 'active')->whereNotNull('paid_until')->where('paid_until', '<', now())->get();
    foreach ($trials->merge($paid) as $cafe) {
        $cafe->status = 'overdue';
        $cafe->save();
    }
    $this->info(($trials->count() + $paid->count()).' cafés marked overdue.');
})->purpose('Mark cafés with ended trials or plans as overdue');

// Raw analytics rows are kept 90 days (PRD).
Artisan::command('xmenu:prune-events', function () {
    $n = Event::where('created_at', '<', now()->subDays(90))->delete();
    $this->info("$n old events deleted.");
})->purpose('Delete analytics events older than 90 days');

Schedule::command('xmenu:subscriptions')->dailyAt('01:00');
Schedule::command('xmenu:prune-events')->dailyAt('02:00');

// Create or reset a super admin. Prints a strong random password once; it is never stored in readable form.
Artisan::command('xmenu:admin {email} {--name=Xmenu Admin}', function (string $email) {
    $email = strtolower(trim($email));
    if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return $this->error('That is not a valid email address.');
    }
    $password = \Illuminate\Support\Str::password(20, symbols: false);
    $user = \App\Models\User::firstOrNew(['email' => $email]);
    $user->name = $user->exists ? $user->name : $this->option('name');
    $user->password = $password;
    $user->is_admin = true;
    $user->save();
    $this->info(($user->wasRecentlyCreated ? 'Super admin created.' : 'Super admin password reset.'));
    $this->line("  Login:    ".url('/login'));
    $this->line("  Email:    $email");
    $this->line("  Password: $password");
    $this->warn('Save this password in a password manager now. It will not be shown again.');
})->purpose('Create a super admin, or reset an admin password');

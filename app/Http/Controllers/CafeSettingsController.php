<?php

namespace App\Http\Controllers;

use App\Models\Cafe;
use App\Models\Game;
use App\Models\PaymentAccount;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use RuntimeException;

class CafeSettingsController extends Controller
{
    public function __construct(private ImageService $images) {}

    // ---------- Café info ----------
    public function info()
    {
        $cafe = $this->cafe();
        $hours = $cafe->openingHours()->get()->keyBy('day_of_week');

        return view('dashboard.info', compact('cafe', 'hours'));
    }

    public function updateInfo(Request $request)
    {
        $cafe = $this->cafe();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'tagline_en' => ['nullable', 'string', 'max:120'],
            'tagline_am' => ['nullable', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:40'],
            'address' => ['nullable', 'string', 'max:255'],
            'map_url' => ['nullable', 'url', 'max:500'],
            'wifi_name' => ['nullable', 'string', 'max:100'],
            'wifi_password' => ['nullable', 'string', 'max:100'],
            'socials' => ['nullable', 'array'],
            'socials.*' => ['nullable', 'string', 'max:255'],
            'vat_included' => ['nullable', 'boolean'],
            'service_charge_pct' => ['nullable', 'numeric', 'min:0', 'max:50'],
            'logo' => ['nullable', 'image', 'max:5120'],
            'cover' => ['nullable', 'image', 'max:5120'],
            'remove_logo' => ['nullable', 'boolean'],
            'remove_cover' => ['nullable', 'boolean'],
            'hours' => ['nullable', 'array'],
        ], [
            'map_url.url' => 'Paste the full Google Maps link, starting with https://',
        ]);

        try {
            if ($request->hasFile('logo')) {
                $new = $this->images->store($request->file('logo'), "cafes/{$cafe->id}/brand", ImageService::LOGO_WIDTHS, null, true);
                $this->images->delete($cafe->logo);
                $cafe->logo = $new;
            } elseif ($request->boolean('remove_logo')) {
                $this->images->delete($cafe->logo);
                $cafe->logo = null;
            }
            if ($request->hasFile('cover')) {
                $new = $this->images->store($request->file('cover'), "cafes/{$cafe->id}/brand", ImageService::COVER_WIDTHS, 16 / 9);
                $this->images->delete($cafe->cover);
                $cafe->cover = $new;
            } elseif ($request->boolean('remove_cover')) {
                $this->images->delete($cafe->cover);
                $cafe->cover = null;
            }
        } catch (RuntimeException $e) {
            return back()->withInput()->withErrors(['logo' => $e->getMessage()]);
        }

        $socials = collect($data['socials'] ?? [])
            ->only(array_keys(config('xmenu.socials')))
            ->map(fn ($v) => trim((string) $v))
            ->filter()
            ->map(fn ($v, $k) => $this->normaliseSocial($k, $v))
            ->all();

        $cafe->fill([
            'name' => $data['name'],
            'tagline' => $this->pair($data, 'tagline'),
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'map_url' => $data['map_url'] ?? null,
            'wifi_name' => $data['wifi_name'] ?? null,
            'wifi_password' => $data['wifi_password'] ?? null,
            'socials' => $socials,
            'vat_included' => $request->boolean('vat_included'),
            'service_charge_pct' => $data['service_charge_pct'] ?? 0,
        ])->save();

        foreach ($request->input('hours', []) as $day => $h) {
            if ($day < 0 || $day > 6) {
                continue;
            }
            $cafe->openingHours()->updateOrCreate(['day_of_week' => (int) $day], [
                'opens_at' => $this->time($h['opens_at'] ?? null),
                'closes_at' => $this->time($h['closes_at'] ?? null),
                'closed' => ! empty($h['closed']),
            ]);
        }

        return back()->with('ok', 'Café details saved.');
    }

    private function time(?string $t): ?string
    {
        return $t && preg_match('/^\d{1,2}:\d{2}$/', $t) ? $t : null;
    }

    private function normaliseSocial(string $platform, string $v): string
    {
        if (preg_match('#^https?://#i', $v)) {
            return $v;
        }
        $handle = ltrim($v, '@');

        return match ($platform) {
            'telegram' => 'https://t.me/'.$handle,
            'instagram' => 'https://instagram.com/'.$handle,
            'tiktok' => 'https://www.tiktok.com/@'.$handle,
            'facebook' => 'https://facebook.com/'.$handle,
            'whatsapp' => 'https://wa.me/'.preg_replace('/\D/', '', $handle),
            default => 'https://'.$v,
        };
    }

    // ---------- Payment accounts ----------
    public function payments()
    {
        $cafe = $this->cafe();
        $accounts = $cafe->paymentAccounts()->get();

        return view('dashboard.payments', compact('cafe', 'accounts'));
    }

    public function storePayment(Request $request)
    {
        $cafe = $this->cafe();
        $data = $this->validatePayment($request);
        $cafe->paymentAccounts()->create($data + ['sort_order' => (int) $cafe->paymentAccounts()->max('sort_order') + 1, 'visible' => true]);

        return back()->with('ok', 'Account added.');
    }

    public function updatePayment(Request $request, PaymentAccount $account)
    {
        $this->own($account);
        $account->update($this->validatePayment($request) + ['visible' => $request->boolean('visible')]);

        return back()->with('ok', 'Account saved.');
    }

    private function validatePayment(Request $request): array
    {
        $data = $request->validate([
            'type' => ['required', Rule::in(array_keys(config('xmenu.payment_types')))],
            'provider' => ['nullable', 'required_if:type,bank', 'string', 'max:80'],
            'account_name' => ['nullable', 'string', 'max:100'],
            'account_number' => ['required', 'string', 'max:80'],
        ], ['provider.required_if' => 'Choose the bank.']);
        if ($data['type'] !== 'bank' && $data['type'] !== 'other') {
            $data['provider'] = null;
        }

        return $data;
    }

    public function destroyPayment(PaymentAccount $account)
    {
        $this->own($account);
        $account->delete();

        return back()->with('ok', 'Account removed.');
    }

    public function movePayment(Request $request, PaymentAccount $account)
    {
        $this->own($account);
        $list = $this->cafe()->paymentAccounts()->get()->values();
        $i = $list->search(fn ($a) => $a->id === $account->id);
        $j = $request->input('dir') === 'up' ? $i - 1 : $i + 1;
        if ($j >= 0 && $j < $list->count()) {
            $arr = $list->all();
            [$arr[$i], $arr[$j]] = [$arr[$j], $arr[$i]];
            foreach ($arr as $pos => $a) {
                $a->update(['sort_order' => $pos]);
            }
        }

        return back();
    }

    // ---------- Games ----------
    public function games()
    {
        $cafe = $this->cafe();
        $games = Game::where('active', true)->orderBy('sort_order')->get();

        return view('dashboard.games', compact('cafe', 'games'));
    }

    public function updateGames(Request $request)
    {
        $cafe = $this->cafe();
        $on = array_map('intval', $request->input('games', []));
        $all = Game::where('active', true)->pluck('id')->all();
        $cafe->games_enabled = $request->boolean('games_enabled');
        $cafe->disabled_games = array_values(array_diff($all, $on));
        $cafe->save();

        return back()->with('ok', 'Games updated.');
    }

    // ---------- Account settings ----------
    public function settings(Request $request)
    {
        return view('dashboard.settings', ['cafe' => $this->cafe(), 'user' => $this->cafe()->owner]);
    }

    public function updateSettings(Request $request)
    {
        $cafe = $this->cafe();
        $user = $cafe->owner; // in admin manage mode this is the café owner, not the admin
        $data = $request->validate([
            'slug' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::notIn(Cafe::RESERVED_SLUGS), Rule::unique('cafes', 'slug')->ignore($cafe->id)],
            'currency' => ['required', 'string', 'max:10'],
            'default_lang' => ['required', Rule::in(array_keys(config('xmenu.languages')))],
            'name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
        ], ['slug.regex' => 'Use lowercase letters, numbers and dashes only.', 'slug.unique' => 'That link is taken. Try another.']);

        $cafe->fill(['slug' => $data['slug'], 'currency' => strtoupper($data['currency']), 'default_lang' => $data['default_lang']])->save();
        $user->update(['name' => $data['name'], 'email' => strtolower($data['email']), 'phone' => $data['phone'] ?? null]);

        return back()->with('ok', 'Settings saved.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);
        $request->user()->update(['password' => $request->password]); // hashed by the model cast

        return back()->with('ok', 'Password changed.');
    }
}

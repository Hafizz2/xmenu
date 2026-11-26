<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q'));
        $role = $request->query('role');

        $users = User::with('cafe')
            ->when($q, fn ($query) => $query->where(fn ($w) => $w->where('name', 'like', "%$q%")->orWhere('email', 'like', "%$q%")->orWhere('phone', 'like', "%$q%")))
            ->when($role === 'admin', fn ($query) => $query->where('is_admin', true))
            ->when($role === 'owner', fn ($query) => $query->has('cafe'))
            ->when($role === 'suspended', fn ($query) => $query->whereNotNull('suspended_at'))
            ->when($role === 'inactive', fn ($query) => $query->where(fn ($w) => $w->whereNull('last_login_at')->orWhere('last_login_at', '<', now()->subDays(30))))
            ->latest()->paginate(30)->withQueryString();

        $counts = [
            'all' => User::count(),
            'owner' => User::has('cafe')->count(),
            'admin' => User::where('is_admin', true)->count(),
            'suspended' => User::whereNotNull('suspended_at')->count(),
            'inactive' => User::where(fn ($w) => $w->whereNull('last_login_at')->orWhere('last_login_at', '<', now()->subDays(30)))->count(),
        ];

        return view('admin.users', compact('users', 'q', 'role', 'counts'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'is_admin' => ['nullable', 'boolean'],
        ]);
        $makeAdmin = $request->boolean('is_admin');
        if ($user->id === $request->user()->id && ! $makeAdmin) {
            return back()->with('error', 'You can’t remove your own admin access.');
        }
        $before = $user->only(['name', 'email', 'phone', 'is_admin']);
        $user->fill(['name' => $data['name'], 'email' => strtolower($data['email']), 'phone' => $data['phone'] ?? null]);
        $user->is_admin = $makeAdmin;
        $user->save();
        AdminLog::record('user.update', $user->cafe?->id, ['user' => $user->id, 'before' => $before, 'after' => $user->only(['name', 'email', 'phone', 'is_admin'])]);

        return back()->with('ok', "{$user->name} saved.");
    }

    public function suspend(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'You can’t suspend yourself.');
        }
        $user->suspended_at = $user->suspended_at ? null : now();
        $user->save();
        AdminLog::record($user->suspended_at ? 'user.suspend' : 'user.unsuspend', $user->cafe?->id, ['user' => $user->id, 'email' => $user->email]);

        return back()->with('ok', $user->suspended_at ? "{$user->name} can no longer log in." : "{$user->name} can log in again.");
    }

    public function resetPassword(User $user)
    {
        $password = Str::password(12, symbols: false);
        $user->password = $password;
        $user->setRememberToken(Str::random(60));
        $user->save();
        DB::table('sessions')->where('user_id', $user->id)->delete();
        AdminLog::record('user.reset_password', $user->cafe?->id, ['user' => $user->id, 'email' => $user->email]);

        return back()->with('ok', "New password for {$user->email}: {$password} (shown once; send it to them securely).");
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'You can’t delete your own account here.');
        }
        AdminLog::record('user.delete', $user->cafe?->id, ['user' => $user->id, 'email' => $user->email, 'cafe' => $user->cafe?->name]);
        if ($user->cafe) {
            Storage::disk('public')->deleteDirectory("cafes/{$user->cafe->id}");
        }
        $user->delete(); // cascades to the café and its menu

        return back()->with('ok', 'User and their café were deleted.');
    }
}

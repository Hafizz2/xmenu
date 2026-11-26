<?php

namespace App\Http\Middleware;

use App\Http\Controllers\AuthController;
use App\Models\AdminLog;
use App\Models\Cafe;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

/**
 * Makes the café being edited available as $cafe.
 * Owners get their own café. A super admin in "manage mode" gets the café they picked,
 * so they can fix content without the owner's password. Every change is audited.
 */
class EnsureCafe
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user->isSuspended()) {
            Auth::logout();
            $request->session()->invalidate();

            return redirect()->route('login')->withErrors(['email' => 'This account is suspended. Please contact Xmenu support.']);
        }

        $managing = false;
        $cafe = null;
        if ($user->is_admin && ($id = $request->session()->get('managing_cafe'))) {
            $cafe = Cafe::find($id);
            if (! $cafe) {
                $request->session()->forget('managing_cafe');
            } else {
                $managing = true;
            }
        }

        if (! $cafe) {
            if ($user->is_admin && ! $user->cafe) {
                return redirect()->route('admin.index');
            }
            $cafe = $user->cafe ?? AuthController::createCafe($user, $user->name."'s Café");
        }

        $request->attributes->set('cafe', $cafe);
        View::share('cafe', $cafe);
        View::share('managing', $managing);

        $response = $next($request);

        if ($managing && ! $request->isMethod('GET')) {
            AdminLog::record('content.'.($request->route()?->getName() ?? 'edit'), $cafe->id, [
                'method' => $request->method(),
                'path' => $request->path(),
                'fields' => array_keys($request->except(['_token', '_method', 'password', 'password_confirmation', 'current_password'])),
            ]);
        }

        return $response;
    }
}

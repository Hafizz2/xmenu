<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DesignController extends Controller
{
    public function edit()
    {
        $cafe = $this->cafe();
        $theme = $cafe->resolvedTheme();

        return view('dashboard.design', compact('cafe', 'theme'));
    }

    public function update(Request $request)
    {
        $cafe = $this->cafe();
        $data = $request->validate([
            'color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'layout' => ['required', Rule::in(array_keys(config('xmenu.layouts')))],
            'header' => ['required', Rule::in(array_keys(config('xmenu.headers')))],
            'font' => ['required', Rule::in(array_keys(config('xmenu.fonts')))],
            'radius' => ['required', Rule::in(array_keys(config('xmenu.radii')))],
            'mode' => ['required', Rule::in(['auto', 'light', 'dark'])],
            'surface' => ['required', Rule::in(array_keys(config('xmenu.surfaces')))],
        ]);
        foreach (array_keys(config('xmenu.theme_switches')) as $k) {
            $data[$k] = $request->boolean($k) ? '1' : '0';
        }
        $data['color'] = strtoupper($data['color']);
        $data['_saved'] = true;
        $cafe->theme = $data;
        $cafe->save();

        return back()->with('ok', 'Design published. Your menu is updated.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\ShopSetting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $settings = ShopSetting::getAllSettings();
        $user = Auth::user();

        return view('settings.index', compact('settings', 'user'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'shop_name' => ['required', 'string', 'max:255'],
            'shop_tagline' => ['nullable', 'string', 'max:255'],
            'shop_phone' => ['nullable', 'string', 'max:50'],
            'shop_email' => ['nullable', 'email', 'max:255'],
            'shop_address' => ['nullable', 'string'],
            'currency_symbol' => ['required', 'string', 'max:10'],
            'invoice_terms' => ['nullable', 'string'],
        ]);

        foreach ($validated as $key => $value) {
            ShopSetting::set($key, $value);
        }

        return redirect()->route('settings.index')
            ->with('success', 'Shop settings saved successfully.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        /** @var User $user */
        $user = Auth::user();
        $user->password = Hash::make($validated['password']);
        $user->save();

        return redirect()->route('settings.index')
            ->with('success', 'Your password has been updated.');
    }
}


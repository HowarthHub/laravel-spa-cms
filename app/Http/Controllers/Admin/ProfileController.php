<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Profile\PasswordUpdateRequest;
use App\Http\Requests\Admin\Profile\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function edit(): Response
    {
        return Inertia::render('Admin/Profile/ProfileEditPage', [
            'user' => auth()->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->update($request->validated());

        return redirect()->route('admin.profile.edit')->with('success', 'Profile updated.');
    }

    public function toggleDarkMode(): RedirectResponse
    {
        $user = auth()->user();
        $user->update(['dark_mode' => ! $user->dark_mode]);

        return redirect()->back();
    }

    public function updatePassword(PasswordUpdateRequest $request): RedirectResponse
    {
        $request->user()->update([
            'password' => Hash::make($request->validated()['password']),
        ]);

        return redirect()->route('admin.profile.edit')->with('success', 'Password updated.');
    }
}

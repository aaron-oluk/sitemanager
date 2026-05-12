@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6 max-w-2xl">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Profile</h1>
        <p class="text-sm text-gray-500 mt-0.5">Manage your account details and security settings.</p>
    </div>

    {{-- Update profile info --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900">Profile Information</h2>
            <p class="text-xs text-gray-500 mt-0.5">Update your name and email address.</p>
        </div>
        <div class="px-5 py-4">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    {{-- Update password --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900">Update Password</h2>
            <p class="text-xs text-gray-500 mt-0.5">Use a long, random password to stay secure.</p>
        </div>
        <div class="px-5 py-4">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    {{-- Delete account --}}
    <div class="bg-white rounded-xl border border-red-100 shadow-sm">
        <div class="px-5 py-4 border-b border-red-100">
            <h2 class="font-semibold text-red-700">Delete Account</h2>
            <p class="text-xs text-gray-500 mt-0.5">Permanently delete your account and all data.</p>
        </div>
        <div class="px-5 py-4">
            @include('profile.partials.delete-user-form')
        </div>
    </div>

</div>
@endsection

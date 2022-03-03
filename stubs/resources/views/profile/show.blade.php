<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div>
                @livewire('profile.update-profile-information-form')
            </div>

            <x-divider/>

            <div class="mt-10 sm:mt-0">
                @livewire('profile.update-profile-password-form')
            </div>

            <x-divider/>

            <div class="mt-10 sm:mt-0">
                @livewire('profile.delete-profile-form')
            </div>
        </div>
    </div>
</x-app-layout>

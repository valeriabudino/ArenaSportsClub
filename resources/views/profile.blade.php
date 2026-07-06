@extends('layouts.public')

@section('content')
    <x-public.navbar />

    <section class="min-h-screen bg-[#07110d] text-white px-6 pt-32 pb-20">
        <div class="max-w-5xl mx-auto">
            <div class="mb-10">
                <span class="text-lime-400 font-black uppercase tracking-widest">
                    Mi cuenta
                </span>

                <h1 class="text-4xl md:text-5xl font-black uppercase mt-3">
                    Mi perfil
                </h1>

                <p class="text-white/60 mt-4">
                    Administrá tus datos personales y la seguridad de tu cuenta.
                </p>
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-3xl shadow-2xl p-8 text-[#07110d]">
                    <livewire:profile.update-profile-information-form />
                </div>

                <div class="bg-white rounded-3xl shadow-2xl p-8 text-[#07110d]">
                    <livewire:profile.update-password-form />
                </div>

                <div class="bg-white rounded-3xl shadow-2xl p-8 text-[#07110d]">
                    <livewire:profile.delete-user-form />
                </div>
            </div>
        </div>
    </section>

    <x-public.footer />
@endsection
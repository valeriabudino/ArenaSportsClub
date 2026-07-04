@extends('layouts.public')

@section('content')
    <x-public.navbar />

    <section class="min-h-screen bg-[#07110d] text-white px-6 py-20">
        <div class="max-w-6xl mx-auto">
            <h1 class="text-5xl font-black uppercase text-lime-400 text-center mb-12">
                Canchas
            </h1>

            <livewire:public.courts-list />
        </div>
    </section>

    <x-public.footer />
@endsection
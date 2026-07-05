@extends('layouts.public')

@section('content')
    <x-public.navbar />

    <section class="min-h-screen bg-[#07110d] text-white px-6 pt-32 pb-20">
        <div class="max-w-7xl mx-auto">
            <livewire:pages.reservations.index />
        </div>
    </section>

    <x-public.footer />
@endsection
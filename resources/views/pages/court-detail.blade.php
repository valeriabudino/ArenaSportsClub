@extends('layouts.public')

@section('content')
    <x-public.navbar />

    <section class="min-h-screen bg-[#07110d] text-white px-6 py-20">
        <livewire:public.court-detail :court="$court" />
    </section>

    <x-public.footer />
@endsection
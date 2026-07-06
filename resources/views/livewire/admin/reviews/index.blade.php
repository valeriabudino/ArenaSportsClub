<?php

use App\Models\CourtReview;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('layouts.admin')] class extends Component {
    use WithPagination;

    public function delete($reviewId)
    {
        CourtReview::where('id', $reviewId)->delete();

        session()->flash('success', 'Comentario eliminado correctamente.');
    }

    public function with(): array
    {
        return [
            'reviews' => CourtReview::with('court', 'user')
                ->latest()
                ->paginate(20),
        ];
    }
};

?>

<div>
    <div class="mb-8">
        <h1 class="text-3xl font-black text-gray-900">
            Comentarios y valoraciones
        </h1>

        <p class="text-gray-500 mt-2">
            Moderá los comentarios que dejan los usuarios en las canchas.
        </p>
    </div>

    @if (session('success'))
        <div class="mb-6 bg-lime-100 text-lime-700 px-5 py-4 rounded-xl font-bold">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow p-8">
        <div class="overflow-x-auto rounded-2xl border border-gray-100">
            <table class="min-w-full">
                <thead class="bg-[#07110d] text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-black uppercase">Cancha</th>
                        <th class="px-6 py-4 text-left text-xs font-black uppercase">Usuario</th>
                        <th class="px-6 py-4 text-center text-xs font-black uppercase">Puntuación</th>
                        <th class="px-6 py-4 text-left text-xs font-black uppercase">Comentario</th>
                        <th class="px-6 py-4 text-left text-xs font-black uppercase">Fecha</th>
                        <th class="px-6 py-4 text-center text-xs font-black uppercase">Acción</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($reviews as $review)
                        <tr class="hover:bg-lime-50 transition">
                            <td class="px-6 py-4 font-bold text-gray-900">
                                {{ $review->court->name }}
                            </td>

                            <td class="px-6 py-4 text-gray-700">
                                {{ $review->user->name }}
                            </td>

                            <td class="px-6 py-4 text-center text-lime-600 font-black">
                                {{ str_repeat('★', $review->rating) }}
                            </td>

                            <td class="px-6 py-4 text-gray-700 max-w-xs">
                                {{ $review->comment ?: 'Sin comentario.' }}
                            </td>

                            <td class="px-6 py-4 text-gray-700">
                                {{ $review->created_at->format('d/m/Y') }}
                            </td>

                            <td class="px-6 py-4 text-center">
                                <button wire:click="delete({{ $review->id }})"
                                        wire:confirm="¿Eliminar este comentario? Esta acción no se puede deshacer."
                                        class="bg-red-500 text-white px-4 py-2 rounded-xl font-black text-sm hover:bg-red-600 transition">
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                No hay comentarios todavía.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $reviews->links() }}
        </div>
    </div>
</div>

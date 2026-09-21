<x-layouts.storefront title="Health Articles - Susthayan">
    <div class="mx-auto max-w-6xl px-6 py-10">
        <h1 class="font-display text-2xl font-medium text-ink">Health Articles</h1>

        @if ($articles->isEmpty())
            <p class="mt-6 text-sm text-ink-soft">No articles published yet.</p>
        @else
            <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($articles as $article)
                    @php $videoId = \App\Models\HealthArticle::extractVideoId($article->youtube_url); @endphp
                    <div class="overflow-hidden rounded-2xl border border-line bg-surface" x-data="{ playing: false }">
                        <div class="relative aspect-video bg-mist">
                            @if ($videoId)
                                <template x-if="!playing">
                                    <button @click="playing = true" class="group relative block h-full w-full">
                                        @if ($article->thumbnail_url)
                                            <img src="{{ $article->thumbnail_url }}" alt="{{ $article->title }}" class="h-full w-full object-cover">
                                        @else
                                            <img src="https://img.youtube.com/vi/{{ $videoId }}/hqdefault.jpg" alt="{{ $article->title }}" class="h-full w-full object-cover">
                                        @endif
                                        <span class="absolute inset-0 flex items-center justify-center bg-black/20 transition-colors group-hover:bg-black/30">
                                            <span class="flex h-14 w-14 items-center justify-center rounded-full bg-white/90">
                                                <svg class="ml-1 h-6 w-6 text-forest" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z" /></svg>
                                            </span>
                                        </span>
                                    </button>
                                </template>
                                <template x-if="playing">
                                    <iframe
                                        src="https://www.youtube.com/embed/{{ $videoId }}?autoplay=1"
                                        class="h-full w-full"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen
                                    ></iframe>
                                </template>
                            @endif
                        </div>
                        <div class="p-4">
                            <p class="font-medium text-ink">{{ $article->title }}</p>
                            @if ($article->description)
                                <p class="mt-1 text-sm text-ink-soft">{{ $article->description }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.storefront>

<x-layouts.app>
    <x-slot name="title">{{ $news->title }} | PSG-RAKTÁRAK Hírek</x-slot>
    <x-slot name="meta">
        <meta name="robots" content="index, follow">
        <meta name="googlebot" content="index, follow">
        <meta name="description" content="{{ $news->excerpt }}">
        <meta name="keywords" content="hír, raktár, raktárbérlet">
        <link rel="canonical" href="{{ Request::url() }}">

        <!-- Open Graph -->
        <meta property="og:title" content="{{ $news->title }}">
        <meta property="og:description" content="{{ $news->excerpt }}">
        <meta property="og:type" content="article">
        <meta property="og:url" content="{{ Request::url() }}">
        @if ($news->featured_image)
            <meta property="og:image" content="{{ Storage::url($news->featured_image) }}">
        @endif

        <!-- Article specific -->
        <meta property="article:published_time" content="{{ $news?->published_at?->toISOString() }}">
        <meta property="article:author" content="{{ $news->author->name }}">
        @if ($news->category)
            <meta property="article:section" content="{{ $news->category->name }}">
        @endif
    </x-slot>

    <div class="min-h-screen bg-gray-50">
        <!-- Breadcrumb -->

        <div class="max-w-4xl px-4 py-12 mx-auto sm:px-6 lg:px-8">
            <article class="overflow-hidden bg-white rounded-lg shadow-sm">
                <!-- Article Header -->
                <div class="p-8">

                    <h1 class="mb-4 text-3xl font-bold text-gray-900 lg:text-4xl">{{ $news->title }}</h1>

                </div>

                <!-- Featured Image -->
                @if ($news->featured_image)
                    <div class="px-8 mb-8">
                        <img src="{{ Storage::url($news->featured_image) }}" alt="{{ $news->title }}"
                            class="object-cover w-full h-64 rounded-lg lg:h-96" loading="lazy">
                    </div>
                @endif

                <!-- Article Content -->
                <div class="px-8 pb-8">
                    <div class="prose prose-lg max-w-none">
                        {!! $news->excerpt !!}
                    </div>
                </div>

                <!-- Article Content -->
                <div class="px-8 pb-8">
                    <div class="prose prose-lg max-w-none">
                        {!! $news->content !!}
                    </div>
                </div>
                <!-- Article Content -->
                @if ($news?->source)
                    <div class="px-8 pb-8">
                        <div class="prose prose-lg max-w-none">
                            <span class="px-2 py-1 font-semibold text-blue-700 bg-blue-100 rounded">
                                Forrás: {{ $news->source }}
                            </span>
                        </div>
                    </div>
                @endif

            </article>

            <!-- Back to News -->
            <div class="mt-12 text-center">
                <a href="{{ localized_route('news.index') }}"
                    class="inline-flex items-center px-6 py-3 text-gray-700 transition duration-150 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                        </path>
                    </svg>
                    Vissza a hírekhez
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>

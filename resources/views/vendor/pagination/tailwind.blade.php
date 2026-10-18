@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
        <div class="flex justify-between w-full sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center px-3 py-1.5 font-semibold text-slate-400 bg-slate-50 border border-slate-200 cursor-not-allowed rounded-lg shadow-2xs">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-3 py-1.5 font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg shadow-2xs hover:bg-slate-50 hover:text-slate-900 transition">
                    {!! __('pagination.previous') !!}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-3 py-1.5 font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg shadow-2xs hover:bg-slate-50 hover:text-slate-900 transition">
                    {!! __('pagination.next') !!}
                </a>
            @else
                <span class="inline-flex items-center px-3 py-1.5 font-semibold text-slate-400 bg-slate-50 border border-slate-200 cursor-not-allowed rounded-lg shadow-2xs">
                    {!! __('pagination.next') !!}
                </span>
            @endif
        </div>

        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between w-full">
            <div>
                <p class="text-xs text-slate-500 font-medium">
                    <span>Menampilkan</span>
                    @if ($paginator->firstItem())
                        <span class="font-bold text-slate-800">{{ $paginator->firstItem() }}</span>
                        <span>sampai</span>
                        <span class="font-bold text-slate-800">{{ $paginator->lastItem() }}</span>
                    @else
                        <span class="font-bold text-slate-800">{{ $paginator->count() }}</span>
                    @endif
                    <span>dari</span>
                    <span class="font-bold text-slate-800">{{ $paginator->total() }}</span>
                    <span>hasil</span>
                </p>
            </div>

            <div>
                <span class="inline-flex items-center -space-x-px rounded-lg shadow-2xs border border-slate-200 bg-white overflow-hidden text-xs">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                            <span class="inline-flex items-center px-2.5 py-1.5 text-slate-300 bg-slate-50 cursor-not-allowed" aria-hidden="true">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-2.5 py-1.5 text-slate-600 bg-white hover:bg-slate-50 hover:text-slate-900 transition" aria-label="{{ __('pagination.previous') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-slate-400 bg-slate-50 border-x border-slate-200 cursor-default">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="inline-flex items-center px-3 py-1.5 text-xs font-bold text-white bg-emerald-600 border-x border-emerald-600 cursor-default shadow-2xs z-10">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-slate-700 bg-white hover:bg-emerald-50 hover:text-emerald-700 border-x border-slate-200 transition" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-2.5 py-1.5 text-slate-600 bg-white hover:bg-slate-50 hover:text-slate-900 transition" aria-label="{{ __('pagination.next') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                            <span class="inline-flex items-center px-2.5 py-1.5 text-slate-300 bg-slate-50 cursor-not-allowed" aria-hidden="true">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif

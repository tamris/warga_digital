@php
if (! isset($scrollTo)) {
    $scrollTo = 'body';
}

$scrollIntoViewJsSnippet = ($scrollTo !== false)
    ? <<<JS
       (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()
    JS
    : '';
@endphp

<div>
    @if ($paginator->hasPages())
        <nav role="navigation" aria-label="Pagination Navigation" class="flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
            <!-- Mobile View -->
            <div class="flex justify-between w-full sm:hidden">
                @if ($paginator->onFirstPage())
                    <span class="inline-flex items-center px-3 py-1.5 font-semibold text-slate-400 bg-slate-50 border border-slate-200 cursor-not-allowed rounded-lg shadow-2xs">
                        {!! __('pagination.previous') !!}
                    </span>
                @else
                    <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" class="inline-flex items-center px-3 py-1.5 font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg shadow-2xs hover:bg-slate-50 hover:text-slate-900 transition">
                        {!! __('pagination.previous') !!}
                    </button>
                @endif

                @if ($paginator->hasMorePages())
                    <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" class="inline-flex items-center px-3 py-1.5 font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg shadow-2xs hover:bg-slate-50 hover:text-slate-900 transition">
                        {!! __('pagination.next') !!}
                    </button>
                @else
                    <span class="inline-flex items-center px-3 py-1.5 font-semibold text-slate-400 bg-slate-50 border border-slate-200 cursor-not-allowed rounded-lg shadow-2xs">
                        {!! __('pagination.next') !!}
                    </span>
                @endif
            </div>

            <!-- Desktop View -->
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between w-full">
                <div>
                    <p class="text-xs text-slate-500 font-medium">
                        <span>Menampilkan</span>
                        <span class="font-bold text-slate-800">{{ $paginator->firstItem() }}</span>
                        <span>sampai</span>
                        <span class="font-bold text-slate-800">{{ $paginator->lastItem() }}</span>
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
                            <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" class="inline-flex items-center px-2.5 py-1.5 text-slate-600 bg-white hover:bg-slate-50 hover:text-slate-900 transition" aria-label="{{ __('pagination.previous') }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
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
                                    <span wire:key="paginator-{{ $paginator->getPageName() }}-page{{ $page }}">
                                        @if ($page == $paginator->currentPage())
                                            <span aria-current="page">
                                                <span class="inline-flex items-center px-3 py-1.5 text-xs font-bold text-white bg-emerald-600 border-x border-emerald-600 cursor-default shadow-2xs z-10">{{ $page }}</span>
                                            </span>
                                        @else
                                            <button type="button" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-slate-700 bg-white hover:bg-emerald-50 hover:text-emerald-700 border-x border-slate-200 transition" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                                {{ $page }}
                                            </button>
                                        @endif
                                    </span>
                                @endforeach
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if ($paginator->hasMorePages())
                            <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" class="inline-flex items-center px-2.5 py-1.5 text-slate-600 bg-white hover:bg-slate-50 hover:text-slate-900 transition" aria-label="{{ __('pagination.next') }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
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
</div>

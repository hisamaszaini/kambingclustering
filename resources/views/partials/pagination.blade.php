@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between py-2">
        <!-- Mobile View (Compact buttons) -->
        <div class="flex justify-between flex-1 sm:hidden gap-2">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 text-xxs font-bold text-slate-400 bg-slate-100 rounded-xl cursor-default select-none">
                    Sebelumnya
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-xxs font-bold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition active:scale-95">
                    Sebelumnya
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-xxs font-bold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition active:scale-95">
                    Selanjutnya
                </a>
            @else
                <span class="relative inline-flex items-center px-4 py-2 text-xxs font-bold text-slate-400 bg-slate-100 rounded-xl cursor-default select-none">
                    Selanjutnya
                </span>
            @endif
        </div>

        <!-- Desktop View (Numbered navigation) -->
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-end">
            <div>
                <span class="relative z-0 inline-flex rounded-xl border border-slate-200 bg-white p-1 gap-1">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                            <span class="relative inline-flex items-center p-2 rounded-lg text-slate-300 cursor-default select-none text-xxs" aria-hidden="true">
                                <i class="fa-solid fa-chevron-left"></i>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center p-2 rounded-lg text-slate-500 hover:bg-slate-50 transition text-xxs" aria-label="{{ __('pagination.previous') }}">
                            <i class="fa-solid fa-chevron-left"></i>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span class="relative inline-flex items-center px-3 py-1.5 rounded-lg text-slate-400 text-xxs font-bold select-none cursor-default" aria-disabled="true">{{ $element }}</span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="relative inline-flex items-center px-3.5 py-1.5 rounded-lg bg-primary text-white text-xxs font-extrabold shadow-sm shadow-primary/20 select-none">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="relative inline-flex items-center px-3.5 py-1.5 rounded-lg text-slate-500 hover:bg-slate-50 hover:text-slate-800 text-xxs font-bold transition" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center p-2 rounded-lg text-slate-500 hover:bg-slate-50 transition text-xxs" aria-label="{{ __('pagination.next') }}">
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                            <span class="relative inline-flex items-center p-2 rounded-lg text-slate-300 cursor-default select-none text-xxs" aria-hidden="true">
                                <i class="fa-solid fa-chevron-right"></i>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif

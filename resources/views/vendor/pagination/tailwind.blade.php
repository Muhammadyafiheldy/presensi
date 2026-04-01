@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}">

        {{-- ================= TAMPILAN MOBILE ================= --}}
        <div class="flex gap-2 items-center justify-between sm:hidden">
            @if ($paginator->onFirstPage())
                <span
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-gray-50 border border-gray-300 cursor-not-allowed leading-5 rounded-md">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-600 bg-white border border-gray-300 leading-5 rounded-md hover:bg-blue-50 hover:text-blue-800 focus:outline-none focus:ring ring-blue-300 transition ease-in-out duration-150">
                    {!! __('pagination.previous') !!}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-600 bg-white border border-gray-300 leading-5 rounded-md hover:bg-blue-50 hover:text-blue-800 focus:outline-none focus:ring ring-blue-300 transition ease-in-out duration-150">
                    {!! __('pagination.next') !!}
                </a>
            @else
                <span
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-gray-50 border border-gray-300 cursor-not-allowed leading-5 rounded-md">
                    {!! __('pagination.next') !!}
                </span>
            @endif
        </div>

        {{-- ================= TAMPILAN DESKTOP ================= --}}
        <div class="hidden sm:flex-1 sm:flex sm:gap-2 sm:items-center sm:justify-between">

            {{-- Teks "Showing 1 to 10 of X results" --}}
            <div>
                <p class="text-sm text-gray-700 leading-5">
                    {!! __('Showing') !!}
                    @if ($paginator->firstItem())
                        <span class="font-semibold text-blue-600">{{ $paginator->firstItem() }}</span>
                        {!! __('to') !!}
                        <span class="font-semibold text-blue-600">{{ $paginator->lastItem() }}</span>
                    @else
                        {{ $paginator->count() }}
                    @endif
                    {!! __('of') !!}
                    <span class="font-semibold text-blue-600">{{ $paginator->total() }}</span>
                    {!! __('results') !!}
                </p>
            </div>

            {{-- Tombol-tombol Angka --}}
            <div>
                <span class="inline-flex rtl:flex-row-reverse shadow-sm rounded-md">

                    {{-- Tombol Panah Kiri (Previous) --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                            <span
                                class="inline-flex items-center px-2 py-2 text-sm font-medium text-gray-400 bg-gray-50 border border-gray-300 cursor-not-allowed rounded-l-md leading-5"
                                aria-hidden="true">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                            class="inline-flex items-center px-2 py-2 text-sm font-medium text-blue-600 bg-white border border-gray-300 rounded-l-md leading-5 hover:bg-blue-50 focus:outline-none focus:ring ring-blue-300 transition ease-in-out duration-150"
                            aria-label="{{ __('pagination.previous') }}">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif

                    {{-- Nomor Halaman (Angka) --}}
                    @foreach ($elements as $element)
                        {{-- Pemisah "Titik Tiga" --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span
                                    class="inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Deretan Link Nomor --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    {{-- HALAMAN AKTIF (Background Biru, Teks Putih) --}}
                                    <span aria-current="page">
                                        <span
                                            class="inline-flex items-center px-4 py-2 -ml-px text-sm font-bold text-white bg-blue-600 border border-blue-600 cursor-default leading-5 focus:z-10">{{ $page }}</span>
                                    </span>
                                @else
                                    {{-- HALAMAN TIDAK AKTIF (Background Putih, Teks Biru, Hover Biru Muda) --}}
                                    <a href="{{ $url }}"
                                        class="inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-blue-600 bg-white border border-gray-300 leading-5 hover:bg-blue-50 focus:z-10 focus:outline-none focus:ring ring-blue-300 transition ease-in-out duration-150"
                                        aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Tombol Panah Kanan (Next) --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                            class="inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-blue-600 bg-white border border-gray-300 rounded-r-md leading-5 hover:bg-blue-50 focus:outline-none focus:ring ring-blue-300 transition ease-in-out duration-150"
                            aria-label="{{ __('pagination.next') }}">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                            <span
                                class="inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-400 bg-gray-50 border border-gray-300 cursor-not-allowed rounded-r-md leading-5"
                                aria-hidden="true">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif

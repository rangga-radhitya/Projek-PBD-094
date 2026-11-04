{{-- Pagination ringkas. Dipanggil: $daftar->links('components.pagination') --}}
@php
  $awal = max(1, $paginator->currentPage() - 2);
  $akhir = min($paginator->lastPage(), $paginator->currentPage() + 2);
@endphp
<div class="halaman-bar">
  <span class="halaman-info">
    @if ($paginator->total() > 0)
      Menampilkan {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} dari {{ $paginator->total() }} data
    @else
      0 data
    @endif
  </span>
  @if ($paginator->hasPages())
    <nav class="halaman" aria-label="Navigasi halaman">
      @if ($paginator->onFirstPage())
        <span class="nonaktif" aria-disabled="true" aria-label="Halaman sebelumnya">
          <x-ikon nama="kiri" :ukuran="15" />
        </span>
      @else
        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Halaman sebelumnya">
          <x-ikon nama="kiri" :ukuran="15" />
        </a>
      @endif

      @if ($awal > 1)
        <a href="{{ $paginator->url(1) }}">1</a>
        @if ($awal > 2)<span class="pisah" aria-hidden="true">…</span>@endif
      @endif

      @foreach ($paginator->getUrlRange($awal, $akhir) as $halaman => $url)
        @if ($halaman === $paginator->currentPage())
          <span class="aktif" aria-current="page">{{ $halaman }}</span>
        @else
          <a href="{{ $url }}">{{ $halaman }}</a>
        @endif
      @endforeach

      @if ($akhir < $paginator->lastPage())
        @if ($akhir < $paginator->lastPage() - 1)<span class="pisah" aria-hidden="true">…</span>@endif
        <a href="{{ $paginator->url($paginator->lastPage()) }}">{{ $paginator->lastPage() }}</a>
      @endif

      @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Halaman berikutnya" class="lompat">
          <x-ikon nama="panah" :ukuran="15" />
        </a>
      @else
        <span class="nonaktif lompat" aria-disabled="true" aria-label="Halaman berikutnya">
          <x-ikon nama="panah" :ukuran="15" />
        </span>
      @endif
    </nav>
  @endif
</div>

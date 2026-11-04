{{-- Pagination ringkas. Dipanggil: $daftar->links('components.pagination') --}}
<div class="halaman-bar">
  <span class="halaman-info">
    @if ($paginator->total() > 0)
      Menampilkan {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} dari {{ $paginator->total() }} data
    @else
      0 data
    @endif
  </span>
  @if ($paginator->hasPages())
    <nav class="halaman" aria-label="Pagination">
      @if ($paginator->onFirstPage())
        <span class="tombol sekunder kecil nonaktif" aria-disabled="true">&laquo; Sebelumnya</span>
      @else
        <a class="tombol sekunder kecil" href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo; Sebelumnya</a>
      @endif
      <span class="halaman-posisi">Halaman {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>
      @if ($paginator->hasMorePages())
        <a class="tombol sekunder kecil" href="{{ $paginator->nextPageUrl() }}" rel="next">Berikutnya &raquo;</a>
      @else
        <span class="tombol sekunder kecil nonaktif" aria-disabled="true">Berikutnya &raquo;</span>
      @endif
    </nav>
  @endif
</div>

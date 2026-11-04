{{-- Daftar galat validasi untuk halaman penuh (bukan modal). --}}
@if ($errors->any())
  <div class="peringatan gagal" role="alert">
    <ul class="daftar-galat">
      @foreach ($errors->all() as $galat)
        <li>{{ $galat }}</li>
      @endforeach
    </ul>
  </div>
@endif

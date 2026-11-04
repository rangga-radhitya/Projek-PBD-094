{{-- Daftar galat validasi untuk halaman penuh (bukan modal). --}}
@if ($errors->any())
  <div class="peringatan gagal" role="alert">
    <div>
      <strong>Periksa kembali isian berikut.</strong>
      <ul class="daftar-galat">
        @foreach ($errors->all() as $galat)
          <li>{{ $galat }}</li>
        @endforeach
      </ul>
    </div>
  </div>
@endif

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('judul') - {{ config('app.name', 'Daily Bun') }}</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body>
@php
  // Menu dibaca dari config/menu.php (satu sumber untuk sidebar).
  $menu = config('menu', []);
  $tautan = fn (array $m) => route($m['route'], $m['params'] ?? []);
  $menuAktif = fn (array $m) => request()->is(...($m['match'] ?? []));
@endphp
<a class="lewati" href="#konten">Lewati ke konten</a>
<div class="aplikasi">

  <aside class="sisi" id="sisi" aria-label="Navigasi utama">
    <div class="sisi-merek">
      <a href="{{ route('dashboard') }}">{{ config('app.name', 'Daily Bun') }}</a>
    </div>
    <nav>
      @foreach ($menu as $entri)
        @if (isset($entri['items']))
          @php $terbuka = collect($entri['items'])->contains(fn ($i) => $menuAktif($i)); @endphp
          <details class="grup" @if ($terbuka) open @endif>
            <summary>{{ $entri['label'] }}</summary>
            <ul>
              @foreach ($entri['items'] as $item)
                <li>
                  <a href="{{ $tautan($item) }}" @class(['aktif' => $menuAktif($item)]) @if ($menuAktif($item)) aria-current="page" @endif>{{ $item['label'] }}</a>
                </li>
              @endforeach
            </ul>
          </details>
        @else
          <a class="sisi-tunggal" href="{{ $tautan($entri) }}" @class(['aktif' => $menuAktif($entri)]) @if ($menuAktif($entri)) aria-current="page" @endif>{{ $entri['label'] }}</a>
        @endif
      @endforeach
    </nav>
  </aside>
  <div class="sisi-latar" data-tutup-sisi hidden></div>

  <div class="badan">
    <header class="bar-atas">
      <button type="button" class="tombol-menu" data-buka-sisi aria-controls="sisi" aria-expanded="false" aria-label="Buka menu">&#9776;</button>
      <span class="bar-judul">{{ config('app.name', 'Daily Bun') }}</span>
      <span class="bar-status" title="Belum ada fitur login; transaksi dicatat atas nama pengguna tetap (user id 1).">Mode tanpa login</span>
    </header>

    <main id="konten">
      <nav class="remah" aria-label="Breadcrumb">
        <ol>
          <li><a href="{{ route('dashboard') }}">Beranda</a></li>
          @hasSection('breadcrumb')
            @yield('breadcrumb')
          @else
            @hasSection('grup')<li>@yield('grup')</li>@endif
            <li aria-current="page">@yield('judul')</li>
          @endif
        </ol>
      </nav>

      <div class="kepala-halaman">
        <div>
          <h1>@yield('judul')</h1>
          @hasSection('deskripsi')<p class="deskripsi">@yield('deskripsi')</p>@endif
        </div>
        <div class="kepala-aksi">@yield('aksi')</div>
      </div>

      @if (session('sukses'))<div class="peringatan sukses" role="status">{{ session('sukses') }}</div>@endif
      @if (session('gagal'))<div class="peringatan gagal" role="alert">{{ session('gagal') }}</div>@endif

      @yield('isi')
    </main>
  </div>
</div>

@include('partials.modal-konfirmasi')
@stack('modal')
@stack('skrip')
</body>
</html>

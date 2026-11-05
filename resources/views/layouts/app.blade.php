<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#EAF4FF">
  <meta name="color-scheme" content="light">
  <title>@yield('judul') - {{ config('app.name', 'Daily Bun') }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500;600&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body>
<div class="latar-holo" aria-hidden="true">
  <span></span>
  <span></span>
  <span></span>
</div>
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
      <a href="{{ route('dashboard') }}">
        <span class="merek-ikon" aria-hidden="true">
          <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 15c0-4.4 3.6-8 8-8s8 3.6 8 8"/>
            <path d="M4 15h16"/>
            <path d="M7 15c.5-2 1.6-3.6 3-4.6M17 15c-.5-2-1.6-3.6-3-4.6"/>
            <path d="M9 4.5c0 .8-.7 1.5-1.5 1.5M15 4.5c0 .8-.7 1.5-1.5 1.5"/>
          </svg>
        </span>
        <span class="merek-teks">
          <strong>{{ config('app.name', 'Daily Bun') }}</strong>
          <small>Manajemen Toko</small>
        </span>
      </a>
    </div>

    <nav>
      @foreach ($menu as $entri)
        @if (isset($entri['items']))
          @php $terbuka = collect($entri['items'])->contains(fn ($i) => $menuAktif($i)); @endphp
          <details class="grup" @if ($terbuka) open @endif>
            <summary>
              <span>{{ $entri['label'] }}</span>
              <x-ikon nama="bawah" :ukuran="14" />
            </summary>
            <ul>
              @foreach ($entri['items'] as $item)
                <li>
                  <a href="{{ $tautan($item) }}" @class(['aktif' => $menuAktif($item)]) @if ($menuAktif($item)) aria-current="page" @endif>
                    <x-ikon :nama="$item['ikon'] ?? 'kotak'" :ukuran="18" />
                    <span>{{ $item['label'] }}</span>
                  </a>
                </li>
              @endforeach
            </ul>
          </details>
        @else
          <a @class(['sisi-tunggal', 'aktif' => $menuAktif($entri)]) href="{{ $tautan($entri) }}" @if ($menuAktif($entri)) aria-current="page" @endif>
            <x-ikon :nama="$entri['ikon'] ?? 'kotak'" :ukuran="18" />
            <span>{{ $entri['label'] }}</span>
          </a>
        @endif
      @endforeach
    </nav>

    <div class="sisi-kaki">
      <span class="sisi-kaki-tanda" aria-hidden="true"></span>
      <span>Daily Bun</span>
    </div>
  </aside>
  <div class="sisi-latar" data-tutup-sisi hidden></div>

  <div class="badan">
    <header class="bar-atas">
      <button type="button" class="tombol-menu" data-buka-sisi aria-controls="sisi" aria-expanded="false" aria-label="Buka menu">
        <x-ikon nama="menu" :ukuran="20" />
      </button>

      <a class="bar-merek" href="{{ route('dashboard') }}">{{ config('app.name', 'Daily Bun') }}</a>

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

      <div class="bar-kanan">
        <div class="pengguna" title="Transaksi dicatat atas nama manajer01 (user id 1)">
          <span class="pengguna-avatar" aria-hidden="true">M</span>
          <span class="pengguna-teks">
            <strong>manajer01</strong>
            <small>User ID 1</small>
          </span>
        </div>
      </div>
    </header>

    <main id="konten">
      <div class="kepala-halaman">
        <div class="kepala-teks">
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

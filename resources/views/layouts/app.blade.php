<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('judul') - {{ config('app.name', 'RotiKita Bakery') }}</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
@php
  // nama route => teks menu
  $menu = [
      'role'             => 'Role',
      'user'             => 'User',
      'satuan'           => 'Satuan',
      'vendor'           => 'Vendor',
      'margin_penjualan' => 'Margin Penjualan',
      'barang'           => 'Barang',
  ];
@endphp
<header class="atas">
  <div class="isi">
    <span class="merek">{{ config('app.name', 'RotiKita Bakery') }}</span>
    <nav>
      @foreach ($menu as $rute => $label)
        <a href="{{ route($rute) }}" @class(['aktif' => request()->routeIs($rute)]) @if (request()->routeIs($rute)) aria-current="page" @endif>{{ $label }}</a>
      @endforeach
    </nav>
  </div>
</header>
<main>
  <h1>@yield('judul')</h1>
  @yield('isi')
</main>
</body>
</html>

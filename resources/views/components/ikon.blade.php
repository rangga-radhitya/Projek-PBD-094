@props(['nama' => 'kotak', 'ukuran' => 18])
{{-- Ikon garis 24x24. Dipakai untuk navigasi, tombol, dan penanda kecil. --}}
<svg class="ikon" width="{{ $ukuran }}" height="{{ $ukuran }}" viewBox="0 0 24 24" fill="none"
     stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"
     aria-hidden="true" focusable="false">
  @switch($nama)
    @case('dashboard')
      <rect x="3" y="3" width="7" height="9" rx="2"/>
      <rect x="14" y="3" width="7" height="5" rx="2"/>
      <rect x="14" y="12" width="7" height="9" rx="2"/>
      <rect x="3" y="16" width="7" height="5" rx="2"/>
      @break

    @case('role')
      <path d="M12 3l7 3v5c0 4.4-2.9 7.9-7 10-4.1-2.1-7-5.6-7-10V6l7-3z"/>
      <path d="M9.2 12.2l1.9 1.9 3.9-4"/>
      @break

    @case('user')
      <circle cx="12" cy="8" r="3.5"/>
      <path d="M5 20c0-3.3 3.1-6 7-6s7 2.7 7 6"/>
      @break

    @case('satuan')
      <path d="M4 8h16M4 8v8h16V8"/>
      <path d="M8 8v4M12 8v6M16 8v4"/>
      @break

    @case('vendor')
      <path d="M4 9l1.5-5h13L20 9"/>
      <path d="M5 9v11h14V9"/>
      <path d="M9 20v-6h6v6"/>
      <path d="M4 9h16"/>
      @break

    @case('margin')
      <path d="M19 5L5 19"/>
      <circle cx="7.5" cy="7.5" r="2"/>
      <circle cx="16.5" cy="16.5" r="2"/>
      @break

    @case('barang')
      <path d="M21 8l-9-5-9 5 9 5 9-5z"/>
      <path d="M3 8v8l9 5 9-5V8"/>
      <path d="M12 13v8"/>
      @break

    @case('pengadaan')
      <circle cx="9" cy="20" r="1.2"/>
      <circle cx="18" cy="20" r="1.2"/>
      <path d="M3 4h2l2.4 11.2a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 2-1.6L21 8H6"/>
      @break

    @case('penerimaan')
      <path d="M5 5h14l2 8v6H3v-6l2-8z"/>
      <path d="M3 13h5l2 3h4l2-3h5"/>
      @break

    @case('penjualan')
      <path d="M6 3h12v18l-3-2-3 2-3-2-3 2V3z"/>
      <path d="M9 8h6M9 12h6"/>
      @break

    @case('retur')
      <path d="M3 12a9 9 0 1 0 3-6.7"/>
      <path d="M3 4v5h5"/>
      @break

    @case('kartu_stok')
      <path d="M12 3l9 5-9 5-9-5 9-5z"/>
      <path d="M3 13l9 5 9-5"/>
      @break

    @case('tambah')
      <path d="M12 5v14M5 12h14"/>
      @break

    @case('cari')
      <circle cx="11" cy="11" r="7"/>
      <path d="M20 20l-3.5-3.5"/>
      @break

    @case('panah')
      <path d="M5 12h14M13 6l6 6-6 6"/>
      @break

    @case('kiri')
      <path d="M19 12H5M11 6l-6 6 6 6"/>
      @break

    @case('bawah')
      <path d="M6 9l6 6 6-6"/>
      @break

    @case('menu')
      <path d="M4 7h16M4 12h16M4 17h16"/>
      @break

    @case('cetak')
      <path d="M6 9V3h12v6"/>
      <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
      <path d="M6 14h12v7H6z"/>
      @break

    @case('hapus')
      <path d="M4 7h16M10 11v6M14 11v6"/>
      <path d="M6 7l1 13h10l1-13"/>
      <path d="M9 7V4h6v3"/>
      @break

    @case('edit')
      <path d="M4 20h4L20 8l-4-4L4 16v4z"/>
      <path d="M13.5 6.5l4 4"/>
      @break

    @case('tutup')
      <path d="M6 6l12 12M18 6L6 18"/>
      @break

    @case('peringatan')
      <path d="M12 3l9 17H3L12 3z"/>
      <path d="M12 9v5M12 17.2v.1"/>
      @break

    @case('info')
      <circle cx="12" cy="12" r="9"/>
      <path d="M12 11v5M12 7.8v.1"/>
      @break

    @case('centang')
      <path d="M4 12.5l5 5L20 6.5"/>
      @break

    @case('kalender')
      <rect x="3" y="5" width="18" height="16" rx="3"/>
      <path d="M8 3v4M16 3v4M3 10h18"/>
      @break

    @case('toko')
      <path d="M3 10l2-6h14l2 6"/>
      <path d="M5 10v10h14V10"/>
      <path d="M9 20v-6h6v6"/>
      @break

    @default
      <rect x="4" y="4" width="16" height="16" rx="3"/>
  @endswitch
</svg>

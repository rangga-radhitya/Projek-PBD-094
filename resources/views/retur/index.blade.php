@extends('layouts.app')

@section('judul', 'Data Retur')
@section('grup', 'Transaksi')

@section('aksi')
  <a class="tombol" href="{{ route('retur.create') }}">
    <x-ikon nama="tambah" :ukuran="16" />
    Tambah Retur
  </a>
@endsection

@section('isi')
  <x-filter-bar :reset="route('retur.index')">
    <div class="grup-filter">
      <label for="q">Cari ID retur / penerimaan</label>
      <input type="search" id="q" name="q" value="{{ $q }}" placeholder="ID retur atau ID penerimaan">
    </div>
    <div class="grup-filter">
      <label for="idvendor">Vendor</label>
      <select id="idvendor" name="idvendor">
        <option value="">Semua vendor</option>
        @foreach ($daftarVendor as $v)
          <option value="{{ $v->idvendor }}" @selected($idvendor === (string) $v->idvendor)>{{ $v->nama_vendor }}</option>
        @endforeach
      </select>
    </div>
    <div class="grup-filter">
      <label for="dari">Dari tanggal</label>
      <input type="date" id="dari" name="dari" value="{{ $dari }}">
    </div>
    <div class="grup-filter">
      <label for="sampai">Sampai tanggal</label>
      <input type="date" id="sampai" name="sampai" value="{{ $sampai }}">
    </div>
  </x-filter-bar>

  <div class="tabel-bungkus">
  <table>
    <thead>
      <tr><th class="kode">ID</th><th>Tanggal</th><th>Penerimaan asal</th><th>Vendor</th><th>Dibuat oleh</th><th class="angka">Jumlah item</th><th class="aksi">Aksi</th></tr>
    </thead>
    <tbody>
    @forelse ($daftar as $r)
      <tr>
        <td class="kode">{{ $r->idretur }}</td>
        <td>{{ $r->tanggal ?? '-' }}</td>
        <td>@if ($r->idpenerimaan)<a href="{{ route('penerimaan.show', $r->idpenerimaan) }}">#{{ $r->idpenerimaan }}</a>@else - @endif</td>
        <td>{{ $r->nama_vendor ?? '-' }}</td>
        <td>{{ $r->username ?? '-' }}</td>
        <td class="angka">{{ $r->detail_count }}</td>
        <td class="aksi">
          <a class="tombol sekunder kecil" href="{{ route('retur.show', $r->idretur) }}">
            <x-ikon nama="info" :ukuran="14" />
            Detail
          </a>
          <form method="POST" action="{{ route('retur.destroy', $r->idretur) }}">
            @csrf
            @method('DELETE')
            <button type="button" class="tombol sekunder kecil"
                    data-konfirmasi="Hapus retur #{{ $r->idretur }}? Stok akan dikoreksi otomatis.">
              <x-ikon nama="hapus" :ukuran="14" />
              Hapus
            </button>
          </form>
        </td>
      </tr>
    @empty
      <tr><td colspan="7" class="kosong">{{ request()->hasAny(['q','idvendor','dari','sampai']) ? 'Tidak ada retur yang cocok dengan pencarian/filter.' : 'Belum ada data retur.' }}</td></tr>
    @endforelse
    </tbody>
  </table>
  </div>
  {{ $daftar->links('components.pagination') }}
@endsection

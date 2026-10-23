@extends('layouts.app')

@section('judul', 'Data Margin Penjualan')

@section('isi')
  <div class="tabel-bungkus">
  <table>
    <thead>
      <tr><th class="angka">ID</th><th class="angka">Persen</th><th>Status</th><th>Dibuat oleh</th><th>Dibuat</th><th>Diubah</th></tr>
    </thead>
    <tbody>
    @forelse ($daftar as $r)
      <tr>
        <td class="angka">{{ $r->idmargin_penjualan }}</td>
        <td class="angka">{{ rtrim(rtrim(number_format($r->persen, 2, ',', '.'), '0'), ',') }}%</td>
        <td><span class="lencana {{ $r->status == 1 ? 'aktif' : 'nonaktif' }}">{{ $r->status == 1 ? 'Aktif' : 'Nonaktif' }}</span></td>
        <td>{{ $r->username }}</td>
        <td>{{ $r->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
        <td>{{ $r->updated_at?->format('d/m/Y H:i') ?? '-' }}</td>
      </tr>
    @empty
      <tr><td colspan="6" class="kosong">Belum ada data margin penjualan.</td></tr>
    @endforelse
    </tbody>
  </table>
  </div>
@endsection

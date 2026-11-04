@extends('layouts.app')

@section('judul', 'Data Margin Penjualan')
@section('grup', 'Master Data')
@section('deskripsi', 'Persentase margin penjualan. Data dibaca dari view v_margin_penjualan.')

@section('aksi')
  <button type="button" class="tombol" data-buka-form="m-form" data-judul="Tambah Margin Penjualan">+ Tambah Margin</button>
@endsection

@section('isi')
  @if ($aktif->isNotEmpty())
    <x-catatan jenis="sukses">Margin aktif saat ini: @foreach ($aktif as $m)<strong><x-persen :nilai="$m->persen" /></strong> (ID {{ $m->idmargin_penjualan }})@if (! $loop->last), @endif @endforeach</x-catatan>
  @else
    <x-catatan jenis="info">Tidak ada margin yang berstatus aktif.</x-catatan>
  @endif

  <x-filter-bar :reset="route('margin_penjualan')">
    <div class="grup-filter">
      <label for="status">Status</label>
      <select id="status" name="status">
        <option value="">Semua status</option>
        <option value="1" @selected($status === '1')>Aktif</option>
        <option value="0" @selected($status === '0')>Nonaktif</option>
      </select>
    </div>
  </x-filter-bar>

  <div class="tabel-bungkus">
  <table>
    <thead>
      <tr><th class="angka">ID</th><th class="angka">Persen</th><th>Status</th><th>Dibuat oleh</th><th>Dibuat</th><th>Diubah</th><th class="aksi">Aksi</th></tr>
    </thead>
    <tbody>
    @forelse ($daftar as $r)
      <tr>
        <td class="angka">{{ $r->idmargin_penjualan }}</td>
        <td class="angka"><x-persen :nilai="$r->persen" /></td>
        <td><x-badge :aktif="$r->status == 1" :teks="$r->status == 1 ? 'Aktif' : 'Nonaktif'" /></td>
        <td>{{ $r->username }}</td>
        <td>{{ $r->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
        <td>{{ $r->updated_at?->format('d/m/Y H:i') ?? '-' }}</td>
        <td class="aksi">
          <button type="button" class="tombol sekunder kecil" data-buka-form="m-form" data-judul="Edit Margin Penjualan" data-nilai="{{ json_encode(['persen' => $r->persen, 'status' => $r->status]) }}">Edit</button>
          @if ($r->status != 1)
            <button type="button" class="tombol sekunder kecil" data-konfirmasi="Aktifkan margin {{ rtrim(rtrim(number_format((float) $r->persen, 2, ',', '.'), '0'), ',') }}%? Margin lain akan dinonaktifkan.">Aktifkan</button>
          @endif
        </td>
      </tr>
    @empty
      <tr><td colspan="7" class="kosong">{{ $status !== '' ? 'Tidak ada margin yang cocok dengan filter.' : 'Belum ada data margin penjualan.' }}</td></tr>
    @endforelse
    </tbody>
  </table>
  </div>
  {{ $daftar->links('components.pagination') }}

  @push('modal')
    @include('partials.modal-form', ['id' => 'm-form', 'fields' => [
      ['label' => 'Persen margin', 'name' => 'persen', 'type' => 'number', 'min' => 0, 'wajib' => true, 'hint' => 'Pembuat (user) akan diisi otomatis dari akun yang login.'],
      ['label' => 'Status', 'name' => 'status', 'type' => 'select', 'wajib' => true, 'options' => ['1' => 'Aktif', '0' => 'Nonaktif']],
    ]])
  @endpush
@endsection

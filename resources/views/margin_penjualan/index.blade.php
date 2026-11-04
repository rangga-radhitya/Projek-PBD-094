@extends('layouts.app')

@section('judul', 'Data Margin Penjualan')
@section('grup', 'Tabel Master')

@section('aksi')
  <button type="button" class="tombol" data-buka-form="m-form" data-judul="Tambah Margin Penjualan"
          data-aksi="{{ route('margin_penjualan.store') }}" data-metode="POST">
    <x-ikon nama="tambah" :ukuran="16" />
    Tambah Margin
  </button>
@endsection

@section('isi')
  @if (! empty($aktif))
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
      <tr><th class="kode">ID</th><th class="angka">Persen</th><th>Status</th><th>Dibuat oleh</th><th>Dibuat</th><th>Diubah</th><th class="aksi">Aksi</th></tr>
    </thead>
    <tbody>
    @forelse ($daftar as $r)
      <tr>
        <td class="kode">{{ $r->idmargin_penjualan }}</td>
        <td class="angka"><x-persen :nilai="$r->persen" /></td>
        <td><x-badge :jenis="$r->status == 1 ? 'sukses' : 'netral'" :teks="$r->status == 1 ? 'Aktif' : 'Nonaktif'" /></td>
        <td>{{ $r->username }}</td>
        <td>{{ $r->dibuat ?? '-' }}</td>
        <td>{{ $r->diubah ?? '-' }}</td>
        <td class="aksi">
          <button type="button" class="tombol sekunder kecil" data-buka-form="m-form" data-judul="Edit Margin Penjualan"
                  data-aksi="{{ route('margin_penjualan.update', $r->idmargin_penjualan) }}" data-metode="PUT"
                  data-nilai="{{ json_encode(['persen' => $r->persen, 'status' => $r->status]) }}">
            <x-ikon nama="edit" :ukuran="14" />
            Edit
          </button>
          @if ($r->status != 1)
            <form method="POST" action="{{ route('margin_penjualan.aktifkan', $r->idmargin_penjualan) }}">
              @csrf
              @method('PATCH')
              <button type="button" class="tombol sekunder kecil"
                      data-konfirmasi="Aktifkan margin {{ rtrim(rtrim(number_format((float) $r->persen, 2, ',', '.'), '0'), ',') }}%? Margin lain akan dinonaktifkan.">
                <x-ikon nama="centang" :ukuran="14" />
                Aktifkan
              </button>
            </form>
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
    @include('partials.modal-form', ['id' => 'm-form', 'aksi' => route('margin_penjualan.store'), 'judul' => 'Tambah Margin Penjualan', 'fields' => [
      ['label' => 'Persen margin', 'name' => 'persen', 'type' => 'number', 'min' => 0, 'step' => '0.01', 'wajib' => true, 'hint' => 'Pembuat (user) diisi otomatis (user id 1).'],
      ['label' => 'Status', 'name' => 'status', 'type' => 'select', 'wajib' => true, 'options' => ['1' => 'Aktif', '0' => 'Nonaktif']],
    ]])
  @endpush
@endsection

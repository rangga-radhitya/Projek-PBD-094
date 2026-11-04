@props(['jenis' => 'info', 'judul' => 'Catatan', 'ringkas' => false])
{{-- Kotak catatan. $ringkas = true menampilkan catatan teknis sebagai <details> agar tidak memenuhi halaman. --}}
@if ($ringkas)
  <details {{ $attributes->class(['catatan-ringkas']) }}>
    <summary>
      <x-ikon nama="info" :ukuran="15" />
      <span>{{ $judul }}</span>
    </summary>
    <div class="catatan-isi">{{ $slot }}</div>
  </details>
@else
  <div {{ $attributes->class(['peringatan', $jenis]) }} role="note">{{ $slot }}</div>
@endif

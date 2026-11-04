@props(['jenis' => 'info'])
{{-- Kotak catatan. Dipakai untuk menandai keterbatasan tahap frontend (data/backend belum tersedia). --}}
<div {{ $attributes->class(['peringatan', $jenis]) }} role="note">{{ $slot }}</div>

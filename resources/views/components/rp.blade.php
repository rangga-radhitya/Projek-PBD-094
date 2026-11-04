@props(['nilai' => null])
{{-- Format Rupiah; nilai NULL tampil "-". --}}
@if ($nilai === null)-@else Rp {{ number_format((float) $nilai, 0, ',', '.') }}@endif

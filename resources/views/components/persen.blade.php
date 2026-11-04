@props(['nilai' => null])
{{-- Format persen (sama dengan halaman Margin Penjualan); NULL tampil "-". --}}
@if ($nilai === null)-@else{{ rtrim(rtrim(number_format((float) $nilai, 2, ',', '.'), '0'), ',') }}%@endif

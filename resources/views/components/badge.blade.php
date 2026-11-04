@props(['aktif' => null, 'teks' => ''])
{{-- Badge status. $aktif: true = hijau, false = abu-abu, null = netral (kode yang artinya belum diketahui). --}}
<span {{ $attributes->class(['lencana', 'aktif' => $aktif === true, 'nonaktif' => $aktif === false, 'netral' => $aktif === null]) }}>{{ $teks }}</span>

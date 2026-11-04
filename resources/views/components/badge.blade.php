@props(['aktif' => null, 'teks' => '', 'jenis' => null])
{{-- Badge status. $aktif tetap didukung untuk pemanggilan lama.
     $jenis: sukses | proses | bahaya | netral | info. --}}
@php
  $kelas = $jenis ?? ($aktif === true ? 'sukses' : ($aktif === false ? 'nonaktif' : 'netral'));
@endphp
<span {{ $attributes->class(['lencana', $kelas]) }}>{{ $teks }}</span>

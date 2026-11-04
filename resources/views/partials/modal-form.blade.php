{{--
  Modal form CRUD (benar-benar dikirim ke server).
  Parameter:
    $id     : id elemen modal
    $fields : daftar field, tiap item: label, name, type (text|number|password|select|date),
              options (value => label), wajib (bool), hint (string), min (angka), step (angka)
    $aksi   : URL simpan (opsional; untuk mode tambah)
    $judul  : judul form (opsional)
  Mode edit: action dan method diisi oleh public/js/app.js dari atribut
  data-aksi / data-metode pada tombol pemicu.
--}}
@php $adaGalat = $errors->any(); @endphp
<div class="modal" id="{{ $id }}" @if (! $adaGalat) hidden @endif>
  <div class="modal-kotak" role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-judul">
    <div class="modal-kepala">
      <h2 id="{{ $id }}-judul" data-modal-judul>{{ $judul ?? 'Form' }}</h2>
      <button type="button" class="modal-tutup" data-tutup-modal aria-label="Tutup">&times;</button>
    </div>
    <form class="modal-isi" method="POST" action="{{ $aksi ?? '' }}" autocomplete="off" data-form-crud>
      @csrf
      <input type="hidden" name="_method" value="{{ $metode ?? 'POST' }}" data-metode>

      @if ($adaGalat)
        <div class="peringatan gagal" role="alert">
          <ul class="daftar-galat">
            @foreach ($errors->all() as $galat)
              <li>{{ $galat }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      @foreach ($fields as $f)
        @php
          $fid = $id.'-'.$f['name'];
          $tipe = $f['type'] ?? 'text';
          $nilaiLama = old($f['name']);
        @endphp
        <div class="field">
          <label for="{{ $fid }}">{{ $f['label'] }}@if (!empty($f['wajib']))<span class="wajib" aria-hidden="true"> *</span>@endif</label>
          @if ($tipe === 'select')
            <select id="{{ $fid }}" name="{{ $f['name'] }}" @if (!empty($f['wajib'])) required @endif>
              <option value="">— Pilih —</option>
              @foreach ($f['options'] as $nilai => $teks)
                <option value="{{ $nilai }}" @selected((string) $nilaiLama === (string) $nilai)>{{ $teks }}</option>
              @endforeach
            </select>
          @else
            <input id="{{ $fid }}" name="{{ $f['name'] }}" type="{{ $tipe }}"
                   value="{{ $nilaiLama }}"
                   @if (isset($f['min'])) min="{{ $f['min'] }}" @endif
                   @if (isset($f['step'])) step="{{ $f['step'] }}" @endif
                   @if (!empty($f['wajib'])) required @endif>
          @endif
          @if (!empty($f['hint']))<small class="hint">{{ $f['hint'] }}</small>@endif
        </div>
      @endforeach

      <div class="modal-kaki">
        <button type="button" class="tombol sekunder" data-tutup-modal>Batal</button>
        <button type="submit" class="tombol utama">Simpan</button>
      </div>
    </form>
  </div>
</div>

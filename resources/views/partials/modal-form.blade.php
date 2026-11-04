{{--
  Modal form TAMPILAN (belum tersimpan). Parameter:
    $id     : id elemen modal
    $fields : daftar field, tiap item: label, name, type (text|number|password|select), options (value => label),
              wajib (bool), hint (string), min (angka, opsional)
  Form tidak punya action dan tidak dikirim ke server; tombol Simpan dinonaktifkan.
--}}
<div class="modal" id="{{ $id }}" hidden>
  <div class="modal-kotak" role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-judul">
    <div class="modal-kepala">
      <h2 id="{{ $id }}-judul" data-modal-judul>Form</h2>
      <button type="button" class="modal-tutup" data-tutup-modal aria-label="Tutup">&times;</button>
    </div>
    <form class="modal-isi" onsubmit="return false" autocomplete="off" novalidate>
      <p class="petunjuk">Mode tampilan: isian ini belum dapat disimpan (tahap CRUD).</p>
      @foreach ($fields as $f)
        @php $fid = $id.'-'.$f['name']; $tipe = $f['type'] ?? 'text'; @endphp
        <div class="field">
          <label for="{{ $fid }}">{{ $f['label'] }}@if (!empty($f['wajib']))<span class="wajib" aria-hidden="true"> *</span>@endif</label>
          @if ($tipe === 'select')
            <select id="{{ $fid }}" name="{{ $f['name'] }}" @if (!empty($f['wajib'])) required @endif>
              <option value="">— Pilih —</option>
              @foreach ($f['options'] as $nilai => $teks)
                <option value="{{ $nilai }}">{{ $teks }}</option>
              @endforeach
            </select>
          @else
            <input id="{{ $fid }}" name="{{ $f['name'] }}" type="{{ $tipe }}" @if (isset($f['min'])) min="{{ $f['min'] }}" @endif @if (!empty($f['wajib'])) required @endif>
          @endif
          @if (!empty($f['hint']))<small class="hint">{{ $f['hint'] }}</small>@endif
        </div>
      @endforeach
      <div class="modal-kaki">
        <button type="button" class="tombol sekunder" data-tutup-modal>Batal</button>
        <button type="submit" class="tombol utama" disabled>Simpan</button>
      </div>
    </form>
  </div>
</div>

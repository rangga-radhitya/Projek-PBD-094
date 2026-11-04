{{-- Modal konfirmasi global. Tombol pemicu memakai data-konfirmasi dan berada di dalam <form>. --}}
<div class="modal" id="modal-konfirmasi" hidden>
  <div class="modal-kotak kecil" role="dialog" aria-modal="true" aria-labelledby="modal-konfirmasi-judul">
    <div class="modal-kepala">
      <h2 id="modal-konfirmasi-judul">Konfirmasi</h2>
      <button type="button" class="modal-tutup" data-tutup-modal aria-label="Tutup">
        <x-ikon nama="tutup" :ukuran="18" />
      </button>
    </div>
    <div class="modal-isi">
      <div class="konfirmasi-ikon" aria-hidden="true">
        <x-ikon nama="peringatan" :ukuran="22" />
      </div>
      <p id="modal-konfirmasi-teks" class="konfirmasi-teks"></p>
      <div class="modal-kaki">
        <button type="button" class="tombol sekunder" data-tutup-modal>
          <x-ikon nama="tutup" :ukuran="15" />
          Batal
        </button>
        <button type="button" class="tombol bahaya" data-konfirmasi-lanjut>
          <x-ikon nama="centang" :ukuran="15" />
          Ya, lanjutkan
        </button>
      </div>
    </div>
  </div>
</div>

{{-- Modal konfirmasi global. Tombol pemicu memakai data-konfirmasi dan berada di dalam <form>. --}}
<div class="modal" id="modal-konfirmasi" hidden>
  <div class="modal-kotak kecil" role="dialog" aria-modal="true" aria-labelledby="modal-konfirmasi-judul">
    <div class="modal-kepala">
      <h2 id="modal-konfirmasi-judul">Konfirmasi</h2>
      <button type="button" class="modal-tutup" data-tutup-modal aria-label="Tutup">&times;</button>
    </div>
    <div class="modal-isi">
      <p id="modal-konfirmasi-teks"></p>
    </div>
    <div class="modal-kaki">
      <button type="button" class="tombol sekunder" data-tutup-modal>Batal</button>
      <button type="button" class="tombol bahaya" data-konfirmasi-lanjut>Ya, lanjutkan</button>
    </div>
  </div>
</div>

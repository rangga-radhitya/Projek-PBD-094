/* Interaksi frontend dasar (tanpa dependency): sidebar mobile, modal form, modal konfirmasi. */
(function () {
  'use strict';

  var sisi = document.getElementById('sisi');
  var latar = document.querySelector('[data-tutup-sisi]');
  var tombolMenu = document.querySelector('[data-buka-sisi]');

  function bukaSisi() {
    if (!sisi) return;
    sisi.classList.add('terbuka');
    if (latar) latar.hidden = false;
    if (tombolMenu) tombolMenu.setAttribute('aria-expanded', 'true');
  }
  function tutupSisi() {
    if (!sisi) return;
    sisi.classList.remove('terbuka');
    if (latar) latar.hidden = true;
    if (tombolMenu) tombolMenu.setAttribute('aria-expanded', 'false');
  }
  if (tombolMenu) tombolMenu.addEventListener('click', bukaSisi);
  if (latar) latar.addEventListener('click', tutupSisi);

  var modalAktif = null;
  var pemicu = null;
  var formKonfirmasi = null;

  function bukaModal(modal, dari) {
    if (!modal) return;
    modalAktif = modal;
    pemicu = dari || null;
    modal.hidden = false;
    var pertama = modal.querySelector('input:not([disabled]):not([type="hidden"]), select, button:not([disabled])');
    if (pertama) pertama.focus();
  }
  function tutupModal() {
    if (!modalAktif) return;
    modalAktif.hidden = true;
    modalAktif = null;
    if (pemicu && pemicu.focus) pemicu.focus();
    pemicu = null;
  }

  document.addEventListener('click', function (e) {
    var t = e.target;
    if (!(t instanceof Element)) return;

    var bukaForm = t.closest('[data-buka-form]');
    if (bukaForm) {
      var modal = document.getElementById(bukaForm.getAttribute('data-buka-form'));
      if (modal) {
        var form = modal.querySelector('form');
        if (form) form.reset();

        var judul = modal.querySelector('[data-modal-judul]');
        if (judul) judul.textContent = bukaForm.getAttribute('data-judul') || 'Form';

        // Action & method: tambah (POST) atau edit (PUT/PATCH).
        var aksi = bukaForm.getAttribute('data-aksi');
        if (aksi && form) form.setAttribute('action', aksi);
        var metode = bukaForm.getAttribute('data-metode') || 'POST';
        var metodeEl = form ? form.querySelector('[data-metode]') : null;
        if (metodeEl) metodeEl.value = metode;

        var nilai = bukaForm.getAttribute('data-nilai');
        if (nilai && form) {
          try {
            var data = JSON.parse(nilai);
            Object.keys(data).forEach(function (k) {
              var el = form.elements[k];
              if (el) el.value = data[k] === null ? '' : data[k];
            });
          } catch (err) { /* abaikan data rusak */ }
        }
        bukaModal(modal, bukaForm);
      }
      return;
    }

    var konfirmasi = t.closest('[data-konfirmasi]');
    if (konfirmasi) {
      formKonfirmasi = konfirmasi.closest('form');
      var m = document.getElementById('modal-konfirmasi');
      var teks = document.getElementById('modal-konfirmasi-teks');
      if (teks) teks.textContent = konfirmasi.getAttribute('data-konfirmasi');
      bukaModal(m, konfirmasi);
      return;
    }

    if (t.closest('[data-konfirmasi-lanjut]')) {
      if (formKonfirmasi) {
        var f = formKonfirmasi;
        formKonfirmasi = null;
        tutupModal();
        f.submit();
      }
      return;
    }

    if (t.closest('[data-tutup-modal]')) { tutupModal(); return; }
    if (t.classList.contains('modal')) { tutupModal(); }
  });

  document.addEventListener('keydown', function (e) {
    if (e.key !== 'Escape') return;
    if (modalAktif) tutupModal();
    else tutupSisi();
  });
})();

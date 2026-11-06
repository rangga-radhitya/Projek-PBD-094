/* Interaksi frontend dasar (tanpa dependency): sidebar mobile, modal form, modal konfirmasi. */
(function () {
  'use strict';

  var sisi = document.getElementById('sisi');
  var latar = document.querySelector('[data-tutup-sisi]');
  var tombolMenu = document.querySelector('[data-buka-sisi]');

  function bukaSisi() {
    if (!sisi) return;
    sisi.classList.add('terbuka');
    document.body.classList.add('sisi-terbuka');
    if (latar) latar.hidden = false;
    if (tombolMenu) tombolMenu.setAttribute('aria-expanded', 'true');
  }
  function tutupSisi() {
    if (!sisi) return;
    sisi.classList.remove('terbuka');
    document.body.classList.remove('sisi-terbuka');
    if (latar) latar.hidden = true;
    if (tombolMenu) tombolMenu.setAttribute('aria-expanded', 'false');
  }
  if (tombolMenu) tombolMenu.addEventListener('click', bukaSisi);
  if (latar) latar.addEventListener('click', tutupSisi);
  if (sisi) {
    sisi.querySelectorAll('a').forEach(function (tautan) {
      tautan.addEventListener('click', tutupSisi);
    });
  }
  window.addEventListener('resize', function () {
    if (window.innerWidth > 1024) tutupSisi();
  });

  // Pencarian langsung: perbarui daftar hasil saat mengetik tanpa reload halaman.
  var formCari = document.querySelector('[data-cari-langsung]');
  var wadahHasil = document.querySelector('[data-hasil-daftar]');

  if (formCari && wadahHasil && 'fetch' in window && 'DOMParser' in window) {
    var timerCari = null;
    var urutCari = 0;

    var jalankanCari = function () {
      var tujuan = new URL(formCari.action || window.location.href);
      tujuan.search = '';

      new FormData(formCari).forEach(function (nilai, kunci) {
        if (nilai !== '') tujuan.searchParams.set(kunci, nilai);
      });

      var nomor = ++urutCari;
      wadahHasil.setAttribute('aria-busy', 'true');

      fetch(tujuan.toString(), {
        credentials: 'same-origin',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
        .then(function (respons) {
          if (!respons.ok) throw new Error('Pencarian gagal');
          return respons.text();
        })
        .then(function (html) {
          if (nomor !== urutCari) return;
          var dokumen = new DOMParser().parseFromString(html, 'text/html');
          var hasilBaru = dokumen.querySelector('[data-hasil-daftar]');
          if (hasilBaru) wadahHasil.innerHTML = hasilBaru.innerHTML;
          window.history.replaceState(null, '', tujuan.toString());
        })
        .catch(function () {
          // Biarkan hasil sebelumnya tampil bila permintaan gagal.
        })
        .then(function () {
          if (nomor === urutCari) wadahHasil.removeAttribute('aria-busy');
        });
    };

    var jadwalkanCari = function (jeda) {
      window.clearTimeout(timerCari);
      timerCari = window.setTimeout(jalankanCari, jeda);
    };

    var inputCari = formCari.querySelector('input[type="search"], input[name="q"]');
    if (inputCari) {
      inputCari.addEventListener('input', function () { jadwalkanCari(300); });
    }

    formCari.querySelectorAll('select').forEach(function (pilihan) {
      pilihan.addEventListener('change', function () { jadwalkanCari(0); });
    });

    formCari.addEventListener('submit', function (e) {
      e.preventDefault();
      window.clearTimeout(timerCari);
      jalankanCari();
    });
  }

  var modalAktif = null;
  var pemicu = null;
  var formKonfirmasi = null;

  function daftarFokus(modal) {
    return Array.prototype.slice.call(modal.querySelectorAll(
      'a[href], button:not([disabled]), input:not([disabled]):not([type="hidden"]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
    )).filter(function (el) {
      return el.offsetParent !== null;
    });
  }

  function bukaModal(modal, dari) {
    if (!modal) return;
    modalAktif = modal;
    pemicu = dari || null;
    modal.hidden = false;
    document.body.classList.add('modal-terbuka');
    var pertama = modal.querySelector('input:not([disabled]):not([type="hidden"]), select:not([disabled]), button:not([disabled])');
    if (pertama) pertama.focus();
  }
  function tutupModal() {
    if (!modalAktif) return;
    modalAktif.hidden = true;
    modalAktif = null;
    document.body.classList.remove('modal-terbuka');
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
    if (e.key === 'Tab' && modalAktif) {
      var fokus = daftarFokus(modalAktif);
      if (fokus.length === 0) return;
      var pertama = fokus[0];
      var terakhir = fokus[fokus.length - 1];
      if (e.shiftKey && document.activeElement === pertama) {
        e.preventDefault();
        terakhir.focus();
      } else if (!e.shiftKey && document.activeElement === terakhir) {
        e.preventDefault();
        pertama.focus();
      }
      return;
    }

    if (e.key !== 'Escape') return;
    if (modalAktif) tutupModal();
    else tutupSisi();
  });
})();

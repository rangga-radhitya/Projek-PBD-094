<?php

namespace App\Http\Controllers;

use App\Services\PenerimaanService;
use App\Services\PengadaanService;
use App\Services\VendorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use PDOException;

class PenerimaanController extends Controller
{
    public function __construct(
        private PenerimaanService $layanan,
        private PengadaanService $pengadaan,
        private VendorService $vendor
    ) {}

    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $idvendor = (string) $request->query('idvendor', '');
        $status = (string) $request->query('status', '');
        $dari = $this->tanggal($request, 'dari');
        $sampai = $this->tanggal($request, 'sampai');

        $daftar = $this->layanan->daftar($q, $idvendor, $status, $dari, $sampai, 15, $request);
        $daftarVendor = $this->vendor->semua();
        $daftarStatus = $this->layanan->daftarStatus();

        return view('penerimaan.index', compact('daftar', 'q', 'idvendor', 'status', 'dari', 'sampai', 'daftarVendor', 'daftarStatus'));
    }

    public function show(int $id): View
    {
        $penerimaan = $this->layanan->cari($id);

        abort_if($penerimaan === null, 404);

        return view('penerimaan.show', compact('penerimaan'));
    }

    public function create(Request $request): View
    {
        $idpengadaan = (int) $request->query('idpengadaan', 0);

        $daftarPengadaan = $this->layanan->pengadaanTersedia();
        $pengadaan = null;
        $rincian = [];

        if ($idpengadaan > 0) {
            $pengadaan = $this->pengadaan->cari($idpengadaan);

            if ($pengadaan !== null) {
                $rincian = $this->layanan->sisaPengadaan($idpengadaan);
            }
        }

        return view('penerimaan.create', compact('daftarPengadaan', 'pengadaan', 'rincian', 'idpengadaan'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(
            [
                'idpengadaan' => ['required', 'integer'],
                'status' => ['required', 'in:D,S'],
                'items' => ['required', 'array'],
                'items.*.idbarang' => ['required', 'integer'],
                'items.*.jumlah_terima' => ['required', 'integer', 'min:0'],
                'items.*.harga_satuan_terima' => ['required', 'integer', 'min:0'],
            ],
            [],
            [
                'idpengadaan' => 'pengadaan',
                'status' => 'status',
                'items' => 'daftar barang',
                'items.*.idbarang' => 'barang',
                'items.*.jumlah_terima' => 'jumlah terima',
                'items.*.harga_satuan_terima' => 'harga satuan terima',
            ]
        );

        if ($this->pengadaan->cari((int) $data['idpengadaan']) === null) {
            throw ValidationException::withMessages(['idpengadaan' => 'Pengadaan yang dipilih tidak ada.']);
        }

        $items = array_values(array_filter(
            $data['items'],
            fn ($i) => (int) $i['jumlah_terima'] > 0
        ));

        if ($items === []) {
            throw ValidationException::withMessages(['items' => 'Isi jumlah terima minimal untuk satu barang.']);
        }

        try {
            $id = $this->layanan->tambah(
                (int) $data['idpengadaan'],
                self::ID_USER_DEFAULT,
                $data['status'],
                $items
            );
        } catch (PDOException $e) {
            return back()->withInput()->with('gagal', $e->errorInfo[2] ?? 'Penerimaan gagal disimpan.');
        }

        return redirect()->route('penerimaan.show', $id)->with('sukses', 'Penerimaan berhasil disimpan dan stok sudah diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        if ($this->layanan->cari($id) === null) {
            return redirect()->route('penerimaan.index')->with('gagal', 'Penerimaan tidak ditemukan.');
        }

        try {
            $this->layanan->hapus($id);
        } catch (PDOException $e) {
            return redirect()->route('penerimaan.index')->with('gagal', $e->errorInfo[2] ?? 'Penerimaan gagal dihapus.');
        }

        return redirect()->route('penerimaan.index')->with('sukses', 'Penerimaan dihapus dan stok dikoreksi.');
    }
}

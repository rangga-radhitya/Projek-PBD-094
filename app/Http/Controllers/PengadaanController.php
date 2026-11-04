<?php

namespace App\Http\Controllers;

use App\Services\BarangService;
use App\Services\PengadaanService;
use App\Services\VendorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use PDOException;
use RuntimeException;

class PengadaanController extends Controller
{
    public function __construct(
        private PengadaanService $layanan,
        private VendorService $vendor,
        private BarangService $barang
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

        return view('pengadaan.index', compact('daftar', 'q', 'idvendor', 'status', 'dari', 'sampai', 'daftarVendor', 'daftarStatus'));
    }

    public function show(int $id): View
    {
        $pengadaan = $this->layanan->cari($id);

        abort_if($pengadaan === null, 404);

        $diterima = $this->layanan->diterimaPerBarang($id);

        return view('pengadaan.show', compact('pengadaan', 'diterima'));
    }

    public function create(): View
    {
        $daftarVendor = $this->vendor->semua();
        $daftarBarang = $this->barang->aktif();

        return view('pengadaan.create', compact('daftarVendor', 'daftarBarang'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(
            [
                'idvendor' => ['required', 'integer'],
                'status' => ['required', 'in:D,S'],
                'items' => ['required', 'array', 'min:1'],
                'items.*.idbarang' => ['required', 'integer'],
                'items.*.harga_satuan' => ['required', 'integer', 'min:0'],
                'items.*.jumlah' => ['required', 'integer', 'min:1'],
            ],
            [],
            [
                'idvendor' => 'vendor',
                'status' => 'status',
                'items' => 'daftar barang',
                'items.*.idbarang' => 'barang',
                'items.*.harga_satuan' => 'harga satuan',
                'items.*.jumlah' => 'jumlah',
            ]
        );

        if ($this->vendor->cari((int) $data['idvendor']) === null) {
            throw ValidationException::withMessages(['idvendor' => 'Vendor yang dipilih tidak ada.']);
        }

        $idBarang = array_map(fn ($i) => (int) $i['idbarang'], $data['items']);

        if (count($idBarang) !== count(array_unique($idBarang))) {
            throw ValidationException::withMessages(['items' => 'Satu barang tidak boleh dipilih lebih dari sekali.']);
        }

        foreach ($idBarang as $idb) {
            if ($this->barang->cari($idb) === null) {
                throw ValidationException::withMessages(['items' => 'Ada barang yang tidak ditemukan.']);
            }
        }

        try {
            $id = $this->layanan->tambah(
                self::ID_USER_DEFAULT,
                (int) $data['idvendor'],
                $data['status'],
                $data['items'],
                self::PPN_PERSEN
            );
        } catch (PDOException $e) {
            return back()->withInput()->with('gagal', $e->errorInfo[2] ?? 'Pengadaan gagal disimpan.');
        } catch (RuntimeException $e) {
            return back()->withInput()->with('gagal', $e->getMessage());
        }

        return redirect()->route('pengadaan.show', $id)->with('sukses', 'Pengadaan berhasil disimpan.');
    }

    public function destroy(int $id): RedirectResponse
    {
        if ($this->layanan->cari($id) === null) {
            return redirect()->route('pengadaan.index')->with('gagal', 'Pengadaan tidak ditemukan.');
        }

        if ($this->layanan->dipakaiPenerimaan($id)) {
            return redirect()->route('pengadaan.index')->with('gagal', 'Pengadaan tidak bisa dihapus karena sudah memiliki penerimaan.');
        }

        $this->layanan->hapus($id);

        return redirect()->route('pengadaan.index')->with('sukses', 'Pengadaan berhasil dihapus.');
    }
}

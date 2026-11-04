<?php

namespace App\Http\Controllers;

use App\Services\PenerimaanService;
use App\Services\ReturService;
use App\Services\VendorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use PDOException;

class ReturController extends Controller
{
    public function __construct(
        private ReturService $layanan,
        private PenerimaanService $penerimaan,
        private VendorService $vendor
    ) {}

    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $idvendor = (string) $request->query('idvendor', '');
        $dari = $this->tanggal($request, 'dari');
        $sampai = $this->tanggal($request, 'sampai');

        $daftar = $this->layanan->daftar($q, $idvendor, $dari, $sampai, 15, $request);
        $daftarVendor = $this->vendor->semua();

        return view('retur.index', compact('daftar', 'q', 'idvendor', 'dari', 'sampai', 'daftarVendor'));
    }

    public function show(int $id): View
    {
        $retur = $this->layanan->cari($id);

        abort_if($retur === null, 404);

        return view('retur.show', compact('retur'));
    }

    public function create(Request $request): View
    {
        $idpenerimaan = (int) $request->query('idpenerimaan', 0);

        $daftarPenerimaan = $this->layanan->penerimaanTersedia();
        $penerimaan = null;
        $rincian = [];

        if ($idpenerimaan > 0) {
            $penerimaan = $this->penerimaan->cari($idpenerimaan);

            if ($penerimaan !== null) {
                $rincian = $this->layanan->detailTersedia($idpenerimaan);
            }
        }

        return view('retur.create', compact('daftarPenerimaan', 'penerimaan', 'rincian', 'idpenerimaan'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(
            [
                'idpenerimaan' => ['required', 'integer'],
                'items' => ['required', 'array'],
                'items.*.iddetail_penerimaan' => ['required', 'integer'],
                'items.*.jumlah' => ['required', 'integer', 'min:0'],
                'items.*.alasan' => ['nullable', 'string', 'max:200'],
            ],
            [],
            [
                'idpenerimaan' => 'penerimaan',
                'items' => 'daftar barang',
                'items.*.iddetail_penerimaan' => 'detail penerimaan',
                'items.*.jumlah' => 'jumlah retur',
                'items.*.alasan' => 'alasan',
            ]
        );

        if ($this->penerimaan->cari((int) $data['idpenerimaan']) === null) {
            throw ValidationException::withMessages(['idpenerimaan' => 'Penerimaan yang dipilih tidak ada.']);
        }

        $items = array_values(array_filter(
            $data['items'],
            fn ($i) => (int) $i['jumlah'] > 0
        ));

        if ($items === []) {
            throw ValidationException::withMessages(['items' => 'Isi jumlah retur minimal untuk satu barang.']);
        }

        try {
            $id = $this->layanan->tambah((int) $data['idpenerimaan'], self::ID_USER_DEFAULT, $items);
        } catch (PDOException $e) {
            return back()->withInput()->with('gagal', $e->errorInfo[2] ?? 'Retur gagal disimpan.');
        }

        return redirect()->route('retur.show', $id)->with('sukses', 'Retur berhasil disimpan dan stok sudah dikoreksi.');
    }

    public function destroy(int $id): RedirectResponse
    {
        if ($this->layanan->cari($id) === null) {
            return redirect()->route('retur.index')->with('gagal', 'Retur tidak ditemukan.');
        }

        try {
            $this->layanan->hapus($id);
        } catch (PDOException $e) {
            return redirect()->route('retur.index')->with('gagal', $e->errorInfo[2] ?? 'Retur gagal dihapus.');
        }

        return redirect()->route('retur.index')->with('sukses', 'Retur dihapus dan stok dikoreksi.');
    }
}

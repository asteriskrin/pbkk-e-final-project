<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Core\Domain\Model\Lowongan;
use App\Core\Domain\Model\LowonganId;
use DateTime;
use App\Core\Application\Query\DaftarMataKuliah\DaftarMataKuliahQueryInterface;
use App\Core\Application\Query\DaftarLowongan\DaftarLowonganQueryInterface;
use App\Core\Application\Query\DaftarLowonganByDosen\DaftarLowonganByDosenQueryInterface;
use App\Core\Application\Query\DaftarPelamar\DaftarPelamarQueryInterface;
use App\Core\Application\Service\BuatLowongan\BuatLowonganRequest;
use App\Core\Application\Service\BuatLowongan\BuatLowonganService;
use App\Core\Application\Service\UbahLowongan\UbahLowonganRequest;
use App\Core\Application\Service\UbahLowongan\UbahLowonganService;
use App\Core\Application\Service\HapusLowongan\HapusLowonganRequest;
use App\Core\Application\Service\HapusLowongan\HapusLowonganService;
use App\Core\Application\Service\TutupLowongan\TutupLowonganRequest;
use App\Core\Application\Service\TutupLowongan\TutupLowonganService;
use App\Core\Domain\Repository\LowonganRepository;
use App\Core\Domain\Repository\MataKuliahRepository;
use App\Core\Domain\Repository\DosenRepository;
use Exception;

class LowonganController extends Controller
{
    public function __construct(
        private DaftarLowonganQueryInterface $daftarLowonganQuery,
        private DaftarLowonganByDosenQueryInterface $daftarLowonganByDosenQuery,
        private DaftarMataKuliahQueryInterface $daftarMataKuliahQuery,
        private DaftarPelamarQueryInterface $daftarPelamarQuery,
        private LowonganRepository $lowonganRepository,
        private MataKuliahRepository $mataKuliahRepository,
        private DosenRepository $dosenRepository,
        private UbahLowonganService $ubahLowonganService,
        private HapusLowonganService $hapusLowonganService,
        private TutupLowonganService $tutupLowonganService
    ) { }

    /**
     * Show a list of all available Lowongan.
     * 
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $daftar_lowongan = $this->daftarLowonganQuery->execute();
        return view('lowongan.index', [
            'daftar_lowongan' => $daftar_lowongan
        ]);
    }

    /**
     * Show a list of Lowongan which was created by the user (Dosen)
     */
    public function lowonganku() {
        $dosen_id = auth()->user()->id;
        $daftar_lowongan = $this->daftarLowonganByDosenQuery->execute($dosen_id);
        return view('lowongan.lowonganku', [
            'daftar_lowongan' => $daftar_lowongan
        ]);
    }

    /**
     * Show detail lowongan page.
     */
    public function detail(string $lowonganId) {
        $lowonganId = new LowonganId($lowonganId);
        $lowongan = $this->lowonganRepository->byId($lowonganId);
        if (!$lowongan) return abort(404);
        $mataKuliah = $this->mataKuliahRepository->byId($lowongan->getMataKuliahId());
        $dosen = $this->dosenRepository->byId($lowongan->getDosenId());
        $daftar_pelamar = $this->daftarPelamarQuery->execute($lowonganId->id());
        return view('lowongan.detail', [
            'lowongan' => $lowongan,
            'mataKuliah' => $mataKuliah,
            'dosen' => $dosen,
            'daftar_pelamar' => $daftar_pelamar
        ]);
    }

    /**
     * Show Tambah Lowongan page.
     * 
     * @return \Illuminate\Http\Response
     */
    public function tambah() {
        // TODO: Add authentication
        $daftar_mata_kuliah = $this->daftarMataKuliahQuery->execute();
        return view('lowongan.tambah', [
            'daftar_mata_kuliah' => $daftar_mata_kuliah
        ]);
    }

    /**
     * Show Ubah Lowongan page.
     * 
     * @return \Illuminate\Http\Response
     */
    public function ubah($lowonganId) {
        // TODO: Add authentication

        $lowongan = $this->daftarLowonganQuery->byId($lowonganId);
        
        if (!$lowongan) {
            return abort(404);
        }

        if (auth()->user()->id != $lowongan->dosen_id) return abort(403);

        $daftar_mata_kuliah = $this->daftarMataKuliahQuery->execute();
        
        return view('lowongan.ubah', [
            'lowongan' => $lowongan,
            'daftar_mata_kuliah' => $daftar_mata_kuliah
        ]);
    }

    /**
     * Tambah Lowongan Action
     */
    public function tambahAction(Request $request) {
        // TODO: Add authentication

        $dosenId = auth()->user()->id;
        $mataKuliahId = $request->input('mata_kuliah_id');
        $kodeKelas = $request->input('kode_kelas');
        $gaji = $request->input('gaji');
        $tanggal_mulai = $request->input('tanggal_mulai');
        $tanggal_selesai = $request->input('tanggal_selesai');
        $deskripsi = $request->input('deskripsi');

        $tambahRequest = new BuatLowonganRequest(
            $dosenId,
            $mataKuliahId,
            $kodeKelas,
            $gaji,
            $tanggal_mulai,
            $tanggal_selesai,
            $deskripsi
        );

        $service = new BuatLowonganService(
            lowonganRepository: $this->lowonganRepository
        );

        $service->execute($tambahRequest);

        return response()->redirectTo(route('lowongan'))
            ->with('success', 'berhasil_membuat_lowongan');
    }

    /**
     * Ubah Lowongan Action
     */
    public function ubahAction(string $lowonganId, Request $request) {
        // TODO: Add authentication

        $lowongan = $this->daftarLowonganQuery->byId($lowonganId);

        if (!$lowongan) {
            return abort(404);
        }

        if (auth()->user()->id != $lowongan->dosen_id) return abort(403);

        $id = $lowongan->id;
        $dosenId = $lowongan->dosen_id;
        $mataKuliahId = $request->input('mata_kuliah_id');
        $kodeKelas = $request->input('kode_kelas');
        $gaji = $request->input('gaji');
        $tanggal_mulai = $request->input('tanggal_mulai');
        $tanggal_selesai = $request->input('tanggal_selesai');
        $deskripsi = $request->input('deskripsi');

        $ubahRequest = new UbahLowonganRequest(
            $id,
            $dosenId,
            $mataKuliahId,
            $kodeKelas,
            $gaji,
            $tanggal_mulai,
            $tanggal_selesai,
            $deskripsi
        );

        try {
            $this->ubahLowonganService->execute($ubahRequest);
        }
        catch (Exception $e) {
            return back()->withErrors($e->getMessage())->withInput();
        }

        return response()->redirectToRoute('lowongan')
            ->with('success', 'berhasil_mengubah_lowongan');
    }

    public function deleteAction(string $lowonganId) {
        // TODO: Add authentication

        $lowongan = $this->daftarLowonganQuery->byId($lowonganId);

        if (!$lowongan) {
            return abort(404);
        }

        if (auth()->user()->id != $lowongan->dosen_id) return abort(403);

        $hapusRequest = new HapusLowonganRequest($lowonganId);
        try {
            $this->hapusLowonganService->execute($hapusRequest);
        }
        catch (Exception $e) {
            return back()->withErrors($e->getMessage())->withInput();
        }

        return response()->redirectToRoute('lowongan')
            ->with('success', 'berhasil_menghapus_lowongan');
    }

    public function tutupAction(string $lowonganId) {
        $lowonganId = new LowonganId($lowonganId);
        $lowongan = $this->lowonganRepository->byId($lowonganId);
        if (!$lowongan) return abort(404);
        if (auth()->user()->id != $lowongan->getDosenId()->id()) return abort(403);

        $tutupRequest = new TutupLowonganRequest($lowonganId->id());
        try {
            $this->tutupLowonganService->execute($tutupRequest);
        }
        catch (Exception $e) {
            return back()->withErrors($e->getMessage())->withInput();
        }
        return response()->redirectToRoute('detail-lowongan', ['lowonganId' => $lowonganId->id()])
            ->with('success', 'berhasil_menutup_lowongan');
    }
}

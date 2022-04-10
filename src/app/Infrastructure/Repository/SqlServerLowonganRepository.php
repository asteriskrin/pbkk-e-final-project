<?php

namespace App\Infrastructure\Repository;

use App\Core\Domain\Repository\LowonganRepository;
use App\Core\Domain\Model\Lowongan;
use DB;

class SqlServerLowonganRepository implements LowonganRepository {
    /**
     * Save Lowongan data into database.
     */
    public function save(Lowongan $lowongan) : void {
        $sql = "SELECT id FROM lowongan WHERE id = :id";

        $exists = DB::selectOne($sql, [
            'id' => $lowongan->getId()->id()
        ]);
        
        if ($exists) {
            // TODO: Add UPDATE SQL
        }
        else {
            $data = [
                'id' => $lowongan->getId()->id(),
                'dosen_id' => $lowongan->getDosenId()->id(),
                'mata_kuliah_id' => $lowongan->getMataKuliahId()->id(),
                'kode_kelas' => $lowongan->getKodeKelas(),
                'gaji' => $lowongan->getGaji(),
                'tanggal_mulai' => $lowongan->getTanggalMulai()->format('y-m-d'),
                'tanggal_selesai' => $lowongan->getTanggalSelesai()->format('y-m-d'),
                'deskripsi' => $lowongan->getDeskripsi(),
                'created_at' => $lowongan->getCreatedAt()->format('y-m-d')
            ];
            
            $sql = "INSERT INTO lowongan (id, dosen_id, mata_kuliah_id, kode_kelas, gaji, tanggal_mulai, tanggal_selesai, deskripsi, created_at)
                VALUES (:id, :dosen_id, :mata_kuliah_id, :kode_kelas, :gaji, :tanggal_mulai, :tanggal_selesai, :deskripsi, :created_at)";

            DB::insert($sql, $data);
        }
    }
}
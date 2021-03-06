<?php

namespace App\Core\Application\Query\DaftarLowongan;

use App\Core\Application\Query\DaftarLowongan\DaftarLowonganDto;

interface DaftarLowonganQueryInterface {
    public function execute() : array;
    public function byId(string $lowongan_id) : ?DaftarLowonganDto;
}

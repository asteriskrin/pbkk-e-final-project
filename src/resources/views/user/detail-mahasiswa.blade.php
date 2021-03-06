@extends('layouts.app')
@section('title', 'Detail Mahasiswa')
@section('content')
  <div class="container mt-3">
    <div class="row">
      <div class="col-5">
        <div class="card">
          <div class="card-body">
            <h2 class="card-title mb-3">Detail Mahasiswa</h2>
            <form>
              <!-- Nama Lengkap -->
              <div class="form-floating">
                <input type="text" name="namaLengkap" class="form-control" id="namaLengkap" placeholder="Nama Lengkap" required value="{{ $namaLengkap }}" readonly>
                <label for="namaLengkap">Nama Lengkap</label>
              </div>

              <!-- NIM -->
              <div class="form-floating mt-3">
                <input type="text" name="nim" class="form-control" id="nim" placeholder="NIM" required value="{{ $nim }}" readonly>
                <label for="nim">NIM</label>
              </div>

              <!-- URL Transkrip Mata Kuliah -->
              <div class="form-floating mt-3">
                <input type="url" name="urlTranskripMk" class="form-control" id="urlTranskripMk" placeholder="URL Transkrip Mata Kuliah" required value="{{ $urlTranskripMk }}" readonly>
                <label for="urlTranskripMk">URL Transkrip Mata Kuliah</label>
              </div>

              <div class="row mt-3">
                <div class="col-md">
                  <!-- IPK -->
                  <div class="form-floating">
                    <input type="number" name="ipk" step="0.01" min="0" max="4" class="form-control" id="ipk" placeholder="IPK"
                      required value="{{ $ipk }}" readonly>
                    <label for="ipk">IPK</label>
                  </div>
                </div>

                <div class="col-md">
                  <!-- Semester -->
                  <div class="form-floating">
                    <input type="number" name="semester" min="1" max="14" class="form-control" id="semester" placeholder="Semester"
                      required value="{{ $semester }}" readonly>
                    <label for="semester">Semester</label>
                  </div>
                </div>
              </div>

              <div class="row mt-3">
                <div class="col-md">
                  <!-- Nomor Rekening -->
                  <div class="form-floating">
                    <input type="text" name="nomorRekening" class="form-control" id="nomorRekening" placeholder="Nomor Rekening" required value="{{ $nomorRekening }}" readonly>
                    <label for="nomorRekening">Nomor Rekening</label>
                  </div>
                </div>

                <div class="col-md">
                  <!-- Nomor Telepon -->
                  <div class="form-floating">
                    <input type="text" name="nomorTelepon" class="form-control" id="nomorTelepon" placeholder="Nomor Telepon" required value="{{ $nomorTelepon }}" readonly>
                    <label for="nomorTelepon">Nomor Telepon</label>
                  </div>
                </div>
              </div>

              <!-- Email -->
              <div class="form-floating mt-3">
                <input type="email" name="email" class="form-control" id="email" placeholder="Email" required value="{{ $email }}" readonly>
                <label for="email">Email</label>
              </div>
            </form>
          </div>
        </div>
      </div>
      <div class="col-7">
        <div class="card">
            <div class="card-body">
                <h2>Riwayat Asistensi</h2>
            </div>
        </div>
        <table class="table table-striped mt-3">
          <thead class="table-dark">
            <tr>
              <th scope="col">No.</th>
              <th scope="col">Mata Kuliah</th>
              <th scope="col">Kode Kelas</th>
              <th scope="col">Gaji</th>
              <th scope="col">Tanggal Mulai</th>
              <th scope="col">Tanggal Selesai</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($riwayat_asistensi as $ra)
              <tr>
                <th scope="row">{{ $loop->index + 1 }}</th>
                <td>{{ $ra->mata_kuliah_nama }}</td>
                <td>{{ $ra->kode_kelas }}</td>
                <td>Rp{{ $ra->gaji }}</td>
                <td>{{ $ra->tanggal_mulai }}</td>
                <td>{{ $ra->tanggal_selesai }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
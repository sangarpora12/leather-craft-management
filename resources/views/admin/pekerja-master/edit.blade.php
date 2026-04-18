@extends('layouts.admin')

@section('title', 'Edit Data Pekerja')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Data Pekerja</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.pekerja-master.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <form action="{{ route('admin.pekerja-master.update', $pekerja->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nama">Nama Lengkap</label>
                                    <input type="text" class="form-control @error('nama') is-invalid @enderror"
                                           id="nama" name="nama" value="{{ old('nama', $pekerja->nama) }}" required>
                                    @error('nama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="jabatan">Jabatan</label>
                                    <select class="form-control @error('jabatan') is-invalid @enderror"
                                            id="jabatan" name="jabatan" required>
                                        <option value="">Pilih Jabatan</option>
                                        <option value="Operator Produksi" {{ old('jabatan', $pekerja->jabatan) == 'Operator Produksi' ? 'selected' : '' }}>Operator Produksi</option>
                                        <option value="Quality Control" {{ old('jabatan', $pekerja->jabatan) == 'Quality Control' ? 'selected' : '' }}>Quality Control</option>
                                        <option value="Manager Produksi" {{ old('jabatan', $pekerja->jabatan) == 'Manager Produksi' ? 'selected' : '' }}>Manager Produksi</option>
                                        <option value="Teknisi" {{ old('jabatan', $pekerja->jabatan) == 'Teknisi' ? 'selected' : '' }}>Teknisi</option>
                                        <option value="Admin Gudang" {{ old('jabatan', $pekerja->jabatan) == 'Admin Gudang' ? 'selected' : '' }}>Admin Gudang</option>
                                    </select>
                                    @error('jabatan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="departemen">Departemen</label>
                                    <select class="form-control @error('departemen') is-invalid @enderror"
                                            id="departemen" name="departemen" required>
                                        <option value="">Pilih Departemen</option>
                                        <option value="Produksi" {{ old('departemen', $pekerja->departemen) == 'Produksi' ? 'selected' : '' }}>Produksi</option>
                                        <option value="Quality Control" {{ old('departemen', $pekerja->departemen) == 'Quality Control' ? 'selected' : '' }}>Quality Control</option>
                                        <option value="Gudang" {{ old('departemen', $pekerja->departemen) == 'Gudang' ? 'selected' : '' }}>Gudang</option>
                                        <option value="Maintenance" {{ old('departemen', $pekerja->departemen) == 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                                        <option value="Administrasi" {{ old('departemen', $pekerja->departemen) == 'Administrasi' ? 'selected' : '' }}>Administrasi</option>
                                    </select>
                                    @error('departemen')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select class="form-control @error('status') is-invalid @enderror"
                                            id="status" name="status" required>
                                        <option value="aktif" {{ old('status', $pekerja->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                        <option value="non-aktif" {{ old('status', $pekerja->status) == 'non-aktif' ? 'selected' : '' }}>Non-Aktif</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="alamat">Alamat</label>
                            <textarea class="form-control @error('alamat') is-invalid @enderror"
                                      id="alamat" name="alamat" rows="3">{{ old('alamat', $pekerja->alamat) }}</textarea>
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="no_telepon">No. Telepon</label>
                                    <input type="text" class="form-control @error('no_telepon') is-invalid @enderror"
                                           id="no_telepon" name="no_telepon" value="{{ old('no_telepon', $pekerja->no_telepon) }}">
                                    @error('no_telepon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                           id="email" name="email" value="{{ old('email', $pekerja->email) }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="tanggal_masuk">Tanggal Masuk</label>
                            <input type="date" class="form-control @error('tanggal_masuk') is-invalid @enderror"
                                   id="tanggal_masuk" name="tanggal_masuk" value="{{ old('tanggal_masuk', $pekerja->tanggal_masuk ? $pekerja->tanggal_masuk->format('Y-m-d') : date('Y-m-d')) }}" required>
                            @error('tanggal_masuk')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update
                        </button>
                        <a href="{{ route('admin.pekerja-master.index') }}" class="btn btn-secondary">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

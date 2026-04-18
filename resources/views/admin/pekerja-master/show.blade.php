@extends('layouts.admin')

@section('title', 'Detail Pekerja')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detail Pekerja</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.pekerja-master.edit', $pekerja->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('admin.pekerja-master.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">ID Pekerja</th>
                                    <td>: {{ $pekerja->id }}</td>
                                </tr>
                                <tr>
                                    <th>Nama Lengkap</th>
                                    <td>: {{ $pekerja->nama }}</td>
                                </tr>
                                <tr>
                                    <th>Jabatan</th>
                                    <td>: {{ $pekerja->jabatan }}</td>
                                </tr>
                                <tr>
                                    <th>Departemen</th>
                                    <td>: {{ $pekerja->departemen }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>:
                                        <span class="badge badge-{{ $pekerja->status == 'aktif' ? 'success' : 'danger' }}">
                                            {{ $pekerja->status }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">No. Telepon</th>
                                    <td>: {{ $pekerja->no_telepon ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>: {{ $pekerja->email ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Masuk</th>
                                    <td>: {{ $pekerja->tanggal_masuk ? $pekerja->tanggal_masuk->format('d M Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Dibuat</th>
                                    <td>: {{ $pekerja->created_at->format('d M Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Diupdate</th>
                                    <td>: {{ $pekerja->updated_at->format('d M Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <h5>Alamat</h5>
                            <p class="text-muted">{{ $pekerja->alamat ?: 'Tidak ada alamat tercatat' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

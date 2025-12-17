@extends('layouts.admin')

@section('title', 'My Profile')
@section('breadcrumb', 'Admin / My Profile')

@section('content')
<div class="container mt-4">
  <div class="card shadow-sm border-0 rounded-3">
    <div class="card-header bg-primary text-white">
      <h5 class="mb-0"><i class="ti ti-user me-2"></i>Profil Pengguna</h5>
    </div>

    <div class="card-body">
      <div class="row mb-3">
        <div class="col-md-4 text-center">
          @php
            $foto = $user->ID_FOTO
                ? asset('storage/profile/'.$user->ID_FOTO)
                : asset('assets/images/profile/user-1.jpg');
          @endphp
          <img src="{{ $foto }}" alt="avatar" class="rounded-circle mb-3" width="120" height="120">
          <h5>{{ $user->NAMA }}</h5>
          <span class="badge bg-secondary">{{ $user->JABATAN }}</span>
        </div>

        <div class="col-md-8">
          <table class="table table-borderless">
            <tr>
              <th width="30%">Username</th>
              <td>: {{ $user->USERNAME }}</td>
            </tr>
            <tr>
              <th>NIP</th>
              <td>: {{ $user->NIP ?? '-' }}</td>
            </tr>
            <tr>
              <th>Nama</th>
              <td>: {{ $user->NAMA }}</td>
            </tr>
            <tr>
              <th>Hak Akses</th>
              <td>: {{ $user->HAKAKSES ?? '-' }}</td>
            </tr>
            <tr>
              <th>Nama Unit</th>
              <td>: {{ $user->NAMA_UNIT ?? '-' }}</td>
            </tr>
            <tr>
              <th>Jabatan</th>
              <td>: {{ $user->JABATAN ?? '-' }}</td>
            </tr>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

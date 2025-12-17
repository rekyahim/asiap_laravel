@extends('layouts.admin')

@section('title', 'Profil Pengguna')
@section('breadcrumb', 'Profil Saya')

@section('content')
<style>
  .profile-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.08);
    max-width: 650px;
    margin: 0 auto;
    padding: 25px 30px;
  }
  .profile-pic {
    width: 110px; height: 110px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #2563eb;
  }
</style>

<div class="profile-card">
  <h4 class="mb-4"><i class="ti ti-user me-2"></i>Profil Saya</h4>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <form action="{{ route('pengguna.profil.update') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="d-flex align-items-center gap-4 mb-4">
      @if($user->FOTO && Storage::exists('public/' . $user->FOTO))
        <img src="{{ asset('storage/' . $user->FOTO) }}" alt="Foto Profil" class="profile-pic">
      @else
        <img src="{{ asset('assets/images/profile/user-1.jpg') }}" alt="Default" class="profile-pic">
      @endif

      <div>
        <label class="form-label mb-1">Ubah Foto Profil</label>
        <input type="file" name="FOTO" class="form-control" accept="image/*">
        <small class="text-muted">Format: JPG, PNG, max 2MB</small>
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label fw-semibold">Username</label>
      <input type="text" class="form-control" value="{{ $user->USERNAME }}" readonly>
    </div>

    <div class="mb-3">
      <label class="form-label fw-semibold">Nama Lengkap</label>
      <input type="text" name="NAMA" class="form-control" value="{{ $user->NAMA }}" required>
    </div>

    <div class="mb-3">
      <label class="form-label fw-semibold">NIP</label>
      <input type="text" class="form-control" value="{{ $user->NIP ?? '-' }}" readonly>
    </div>

    <div class="mb-3">
      <label class="form-label fw-semibold">Hak Akses</label>
      <input type="text" class="form-control" value="{{ $user->HAKAKSES ?? '-' }}" readonly>
    </div>

    <div class="text-end mt-4">
      <button type="submit" class="btn btn-primary">
        <i class="ti ti-check me-1"></i> Simpan Perubahan
      </button>
    </div>
  </form>
</div>
@endsection

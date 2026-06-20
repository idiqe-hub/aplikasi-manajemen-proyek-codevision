@php
  $roleVal = old('role', $developer->role ?? 'frontend');
  $isEdit  = isset($developer) && $developer->exists;
@endphp

<div class="form-group">
    <label>Nama</label>
    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
           value="{{ old('name', $developer->name ?? '') }}" required>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label>Email</label>
    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
           value="{{ old('email', $developer->email ?? '') }}" required>
    @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label>Role</label>
    <select name="role" class="form-control @error('role') is-invalid @enderror" required>
        <option value="frontend"  {{ $roleVal==='frontend'  ? 'selected' : '' }}>frontend</option>
        <option value="backend"   {{ $roleVal==='backend'   ? 'selected' : '' }}>backend</option>
        <option value="fullstack" {{ $roleVal==='fullstack' ? 'selected' : '' }}>fullstack</option>
        <option value="pm"        {{ $roleVal==='pm'        ? 'selected' : '' }}>pm</option>
    </select>
    @error('role')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label>Skill</label>
    <input type="text" name="skill" class="form-control @error('skill') is-invalid @enderror"
           value="{{ old('skill', $developer->skill ?? '') }}" placeholder="Laravel, React, MySQL, dll">
    @error('skill')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<hr>
<h6 class="font-weight-bold text-gray-700 mb-1">
    <i class="fas fa-lock mr-1"></i>
    {{ $isEdit ? 'Ganti Password' : 'Password' }}
</h6>
@if($isEdit)
    <small class="text-muted d-block mb-3">Kosongkan jika tidak ingin mengubah password.</small>
@endif

<div class="form-group">
    <label>Password Baru</label>
    <input type="password" name="password"
           class="form-control @error('password') is-invalid @enderror"
           placeholder="{{ $isEdit ? 'Kosongkan jika tidak ingin mengubah password' : 'Minimal 8 karakter' }}"
           {{ $isEdit ? '' : 'required' }}>
    @error('password')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label>Konfirmasi Password Baru</label>
    <input type="password" name="password_confirmation"
           class="form-control"
           placeholder="Ulangi password baru"
           {{ $isEdit ? '' : 'required' }}>
</div>

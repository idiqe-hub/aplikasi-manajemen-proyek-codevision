@php
  $roleVal = old('role', $developer->role ?? 'frontend');
@endphp

<div class="form-group">
    <label>Nama</label>
    <input type="text" name="name" class="form-control"
           value="{{ old('name', $developer->name ?? '') }}" required>
</div>

<div class="form-group">
    <label>Email</label>
    <input type="email" name="email" class="form-control"
           value="{{ old('email', $developer->email ?? '') }}" required>
</div>

<div class="form-group">
    <label>Role</label>
    <select name="role" class="form-control" required>
        <option value="frontend"  {{ $roleVal==='frontend' ? 'selected' : '' }}>frontend</option>
        <option value="backend"   {{ $roleVal==='backend' ? 'selected' : '' }}>backend</option>
        <option value="fullstack" {{ $roleVal==='fullstack' ? 'selected' : '' }}>fullstack</option>
        <option value="pm"        {{ $roleVal==='pm' ? 'selected' : '' }}>pm</option>
    </select>
</div>

<div class="form-group">
    <label>Skill</label>
    <input type="text" name="skill" class="form-control"
           value="{{ old('skill', $developer->skill ?? '') }}" placeholder="Laravel, React, MySQL, dll">
</div>

<div class="form-group">
    <label>Password Baru</label>
    <input type="password" name="password" class="form-control"
    placeholder="Kosongkan jika tidak ingin mengubah password">
</div>

<div class="form-group">
    <label>Konfirmasi Password</label>
    <input type="password" name="password_confirmation" class="form-control"
    placeholder="Ulangi password baru">
</div>
@if(isset($developer) && $developer->exists && auth()->user()->role === 'admin')
    <hr>
    <h6 class="text-muted">Reset Password Developer</h6>

    <form action="{{ route('developers.reset-password', $developer) }}" method="POST" class="mt-3">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Password Baru</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Konfirmasi Password Baru</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>

        <button class="btn btn-warning">Reset Password</button>
    </form>
@endif


@php
    $statusVal = old('status', $project->status ?? 'planned');
@endphp

<div class="mb-3">
    <label class="form-label">Nama Project</label>
    <input type="text" name="name" class="form-control"
           value="{{ old('name', $project->name ?? '') }}" required>
</div>

<div class="mb-3">
    <label class="form-label">Client</label>
    <input type="text" name="client_name" class="form-control"
           value="{{ old('client_name', $project->client_name ?? '') }}">
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Tanggal Mulai</label>
        <input type="date" name="start_date" class="form-control"
               value="{{ old('start_date', $project->start_date ?? '') }}">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Deadline</label>
        <input type="date" name="end_date" class="form-control"
               value="{{ old('end_date', $project->end_date ?? '') }}">
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Status</label>
    <select name="status" class="form-select" required>
        <option value="planned" {{ $statusVal==='planned' ? 'selected' : '' }}>planned</option>
        <option value="on_progress" {{ $statusVal==='on_progress' ? 'selected' : '' }}>on_progress</option>
        <option value="completed" {{ $statusVal==='completed' ? 'selected' : '' }}>completed</option>
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Deskripsi</label>
    <textarea name="description" class="form-control" rows="3">{{ old('description', $project->description ?? '') }}</textarea>
</div>

@php
    $statusVal = old('status', $task->status ?? 'todo');
    $progressVal = old('progress', $task->progress ?? 0);
@endphp

@auth
    @if (auth()->user()->role === 'admin')
        <div class="form-group">
            <label>Project</label>
            <select name="project_id" class="form-control" required>
                <option value="">-- pilih project --</option>
                @foreach ($projects as $p)
                    <option value="{{ $p->id }}" @selected(old('project_id', $task->project_id ?? '') == $p->id)>
                        {{ $p->name }}
                    </option>
                @endforeach
            </select>
        </div>
    @else
        <div class="form-group">
            <label>Project</label>
            <input type="text" class="form-control" value="{{ $task->project?->name ?? '-' }}" readonly>
            <input type="hidden" name="project_id" value="{{ $task->project_id }}">
        </div>
    @endif
@endauth


@auth
    @if (auth()->user()->role === 'admin')
        <div class="form-group">
            <label>Developer</label>
            <select name="developer_id" class="form-control">
                <option value="">-- pilih developer --</option>
                @foreach ($developers as $d)
                    <option value="{{ $d->id }}" @selected(old('developer_id', $task->developer_id ?? '') == $d->id)>
                        {{ $d->name }}
                    </option>
                @endforeach
            </select>
        </div>
    @endif
@endauth

</div>

        <div class="form-group">
            <label>Judul Task</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $task->title ?? '') }}" required>
        </div>

<div class="form-group">
    <label>Deskripsi</label>
    <textarea name="description" class="form-control" rows="3">{{ old('description', $task->description ?? '') }}</textarea>
</div>

<div class="row">
    <div class="col-md-4 form-group">
        <label>Status</label>
        <select name="status" class="form-control" required>
            <option value="todo" {{ $statusVal === 'todo' ? 'selected' : '' }}>todo</option>
            <option value="in_progress" {{ $statusVal === 'in_progress' ? 'selected' : '' }}>in_progress</option>
            <option value="done" {{ $statusVal === 'done' ? 'selected' : '' }}>done</option>
        </select>
    </div>

    <div class="col-md-4 form-group">
        <label>Progress (0-100)</label>
        <input type="number" name="progress" class="form-control" min="0" max="100"
            value="{{ $progressVal }}" required>
    </div>

    <div class="col-md-4 form-group">
        <label>Deadline</label>
        <input type="date" name="deadline" class="form-control"
            value="{{ old('deadline', $task->deadline ?? '') }}">
    </div>
</div>

<div class="row">
    <div class="col-md-6 form-group">
        <label>Estimasi Jam</label>
        <input type="number" name="estimated_hours" class="form-control" min="0"
            value="{{ old('estimated_hours', $task->estimated_hours ?? '') }}">
    </div>

    <div class="col-md-6 form-group">
        <label>Realisasi Jam</label>
        <input type="number" name="actual_hours" class="form-control" min="0"
            value="{{ old('actual_hours', $task->actual_hours ?? '') }}">
    </div>
</div>

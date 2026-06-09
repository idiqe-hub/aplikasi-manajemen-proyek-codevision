@extends('layouts.admin')
@section('title', 'Notifikasi')
@section('page_title', 'Notifikasi')

@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-bell mr-2"></i> Notifikasi
    </h1>
    @if(auth()->user()->unreadNotifications->count() > 0)
        <form action="{{ route('notifications.readAll') }}" method="POST">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-check-double mr-1"></i> Tandai Semua Sudah Dibaca
            </button>
        </form>
    @endif
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            Daftar Notifikasi
            @if(auth()->user()->unreadNotifications->count() > 0)
                <span class="badge badge-danger ml-2">
                    {{ auth()->user()->unreadNotifications->count() }} Belum Dibaca
                </span>
            @endif
        </h6>
    </div>
    <div class="card-body p-0">
        @forelse($notifications as $notif)
            @php
                $data        = $notif->data;
                $isUnread    = is_null($notif->read_at);
                $warningType = $data['warning_type'] ?? '';
                $icon        = match($warningType) {
                    'h-1'     => 'fa-exclamation-circle text-danger',
                    'h-3'     => 'fa-exclamation-triangle text-warning',
                    'overdue' => 'fa-times-circle text-danger',
                    default   => 'fa-bell text-primary',
                };
                $badgeClass  = match($warningType) {
                    'h-1'     => 'badge-danger',
                    'h-3'     => 'badge-warning',
                    'overdue' => 'badge-dark',
                    default   => 'badge-primary',
                };
                $badgeLabel  = match($warningType) {
                    'h-1'     => 'H-1',
                    'h-3'     => 'H-3',
                    'overdue' => 'Terlambat',
                    default   => 'Info',
                };
            @endphp
            <div class="d-flex align-items-start px-4 py-3 {{ $isUnread ? 'bg-light' : '' }} {{ !$loop->last ? 'border-bottom' : '' }}">

                {{-- Icon --}}
                <div class="mr-3 mt-1">
                    <i class="fas {{ $icon }} fa-lg"></i>
                </div>

                {{-- Isi --}}
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="badge {{ $badgeClass }} mr-2">{{ $badgeLabel }}</span>
                            <span class="{{ $isUnread ? 'font-weight-bold' : '' }}">
                                {{ $data['message'] ?? '-' }}
                            </span>
                        </div>
                        <div class="text-right ml-3 flex-shrink-0">
                            <small class="text-muted d-block" title="{{ $notif->created_at->format('d M Y, H:i:s') }}">
                                {{ $notif->created_at->diffForHumans() }}
                            </small>
                            @if($isUnread)
                                <form action="{{ route('notifications.read', $notif->id) }}" method="POST" class="mt-1">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-xs btn-outline-secondary py-0 px-1" style="font-size: 11px;">
                                        Tandai Dibaca
                                    </button>
                                </form>
                            @else
                                <small class="text-success d-block mt-1"><i class="fas fa-check"></i> Dibaca</small>
                            @endif
                        </div>
                    </div>
                    @if(isset($data['url']))
                        @php
                            // Ambil hanya path dari URL agar tidak bergantung APP_URL / host dev.
                            // Berlaku untuk URL lama (http://localhost/tasks/5)
                            // dan URL baru (/tasks/5) sekaligus.
                            $taskPath = parse_url($data['url'], PHP_URL_PATH) ?? $data['url'];
                        @endphp
                        <a href="{{ $taskPath }}" class="small text-primary mt-1 d-inline-block">
                            <i class="fas fa-external-link-alt fa-xs mr-1"></i> Lihat Task
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-5 text-muted">
                <i class="fas fa-bell-slash fa-3x mb-3 d-block text-gray-300"></i>
                Belum ada notifikasi.
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div class="card-footer bg-transparent">
            {{ $notifications->links() }}
        </div>
    @endif
</div>

@endsection

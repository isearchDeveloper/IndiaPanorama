@extends('layouts.app')
@section('title', 'Login History')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Login History</h1>
        <p class="page-title-sub">
            <span class="badge badge-success" style="vertical-align:middle">
                <span class="status-dot online"></span> {{ $onlineCount }} Online
            </span>
            &nbsp; {{ $logs->total() }} total records
        </p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.login-history.export', request()->query()) }}" class="btn btn-outline-success btn-sm">
            <i class="fas fa-download"></i> Export CSV
        </a>
    </div>
</div>

{{-- Filters --}}
<div class="filter-card">
    <form method="GET">
        <div class="filter-row">
            <div class="filter-group">
                <label class="form-label form-label-sm">Email</label>
                <input type="text" name="search" class="form-control form-control-sm"
                    value="{{ request('search') }}" placeholder="Search by email…">
            </div>
            <div class="filter-group" style="max-width:140px">
                <label class="form-label form-label-sm">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="online"  {{ request('status') == 'online'  ? 'selected' : '' }}>Online</option>
                    <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Success</option>
                    <option value="failed"  {{ request('status') == 'failed'  ? 'selected' : '' }}>Failed</option>
                </select>
            </div>
            <div class="filter-group" style="max-width:150px">
                <label class="form-label form-label-sm">Device</label>
                <select name="device_type" class="form-select form-select-sm">
                    <option value="">All Devices</option>
                    <option value="desktop" {{ request('device_type') == 'desktop' ? 'selected' : '' }}>Desktop</option>
                    <option value="mobile"  {{ request('device_type') == 'mobile'  ? 'selected' : '' }}>Mobile</option>
                    <option value="tablet"  {{ request('device_type') == 'tablet'  ? 'selected' : '' }}>Tablet</option>
                </select>
            </div>
            <div class="filter-group" style="max-width:150px">
                <label class="form-label form-label-sm">From Date</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
            </div>
            <div class="filter-group" style="max-width:150px">
                <label class="form-label form-label-sm">To Date</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="{{ route('admin.login-history.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-xmark"></i> Reset
                </a>
            </div>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="table-wrapper">
    <div class="table-scroll">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:44px">#</th>
                    <th>Admin</th>
                    <th style="width:90px">Status</th>
                    <th>Device / OS</th>
                    <th>Location</th>
                    <th style="width:120px">Login Time</th>
                    <th style="width:120px">Last Activity</th>
                    <th style="width:90px" class="text-center">Details</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                @php $online = $log->is_online; @endphp
                <tr>
                    <td class="text-muted" style="font-size:.8rem">{{ $logs->firstItem() + $loop->index }}</td>

                    <td>
                        <div class="cell-primary">{{ $log->email }}</div>
                        <div class="cell-secondary cell-mono">{{ $log->ip_address }}</div>
                    </td>

                    <td>
                        @if($log->status === 'success')
                            @if($online)
                                <span class="badge badge-success">
                                    <span class="status-dot online"></span> Online
                                </span>
                            @else
                                <span class="badge badge-gray">Offline</span>
                            @endif
                        @else
                            <span class="badge badge-danger">Failed</span>
                        @endif
                    </td>

                    <td>
                        <div style="display:flex;align-items:center;gap:7px">
                            <i class="fas {{ $log->device_icon }} text-muted"></i>
                            <div>
                                <div style="font-size:.84rem;font-weight:500">{{ $log->os_name ?? ucfirst($log->device_type ?? '—') }}</div>
                                <div class="cell-secondary">{{ $log->browser_name ?? '' }}
                                    @if($log->browser_version)<span class="text-muted"> {{ $log->browser_version }}</span>@endif
                                </div>
                            </div>
                        </div>
                    </td>

                    <td>
                        @if($log->city || $log->country)
                            <div style="font-size:.84rem">{{ implode(', ', array_filter([$log->city, $log->country])) }}</div>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    <td style="font-size:.82rem">
                        <div>{{ $log->logged_in_at->format('d M Y') }}</div>
                        <div class="text-muted">{{ $log->logged_in_at->format('H:i:s') }}</div>
                    </td>

                    <td style="font-size:.82rem">
                        @if($log->last_activity_at)
                            <span title="{{ $log->last_activity_at->format('d M Y H:i:s') }}">
                                {{ $log->last_activity_at->diffForHumans() }}
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-secondary view-login-detail"
                            data-id="{{ $log->id }}"
                            data-email="{{ $log->email }}"
                            data-ip="{{ $log->ip_address }}"
                            data-status="{{ $log->status }}"
                            data-online="{{ $online ? '1' : '0' }}"
                            data-device="{{ ucfirst($log->device_type ?? '') }}"
                            data-os="{{ $log->os_name ?? '—' }}"
                            data-browser="{{ ($log->browser_name ?? '') . ' ' . ($log->browser_version ?? '') }}"
                            data-location="{{ implode(', ', array_filter([$log->city, $log->country])) ?: '—' }}"
                            data-login="{{ $log->logged_in_at->format('d M Y H:i:s') }}"
                            data-last="{{ $log->last_activity_at ? $log->last_activity_at->format('d M Y H:i:s') : '—' }}"
                            data-logout="{{ $log->logout_at ? $log->logout_at->format('d M Y H:i:s') : '—' }}"
                            data-reason="{{ $log->failure_reason ?? '' }}"
                            data-bs-toggle="modal" data-bs-target="#loginDetailModal"
                            title="View details">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="table-empty">
                            <div class="table-empty-icon"><i class="fas fa-clock-rotate-left"></i></div>
                            <div class="table-empty-title">No login records found</div>
                            <div class="table-empty-sub">Try adjusting your filters</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->lastPage() > 1)
    <div class="card-footer d-flex align-items-center justify-content-between flex-wrap gap-2">
        <span class="text-muted" style="font-size:.82rem">
            Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ $logs->total() }}
        </span>
        @include('admin.common.pagination', ['paginator' => $logs])
    </div>
    @endif
</div>
@endsection

@section('modal')
<div class="modal fade" id="loginDetailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background:var(--gray-50)">
                <h5 class="modal-title">
                    <i class="fas fa-right-to-bracket me-2" style="color:var(--brand)"></i>
                    Login Detail
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                <div class="detail-section">
                    <div class="detail-section-title">Account</div>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>Email</label>
                            <span id="d-email">—</span>
                        </div>
                        <div class="detail-item">
                            <label>IP Address</label>
                            <span id="d-ip" class="cell-mono">—</span>
                        </div>
                        <div class="detail-item">
                            <label>Status</label>
                            <span id="d-status">—</span>
                        </div>
                        <div class="detail-item">
                            <label>Location</label>
                            <span id="d-location">—</span>
                        </div>
                    </div>
                </div>

                <div class="detail-section">
                    <div class="detail-section-title">Device & Browser</div>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>Device Type</label>
                            <span id="d-device">—</span>
                        </div>
                        <div class="detail-item">
                            <label>Operating System</label>
                            <span id="d-os">—</span>
                        </div>
                        <div class="detail-item" style="grid-column:1/-1">
                            <label>Browser</label>
                            <span id="d-browser">—</span>
                        </div>
                    </div>
                </div>

                <div class="detail-section">
                    <div class="detail-section-title">Session Timeline</div>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>Login Time</label>
                            <span id="d-login">—</span>
                        </div>
                        <div class="detail-item">
                            <label>Last Activity</label>
                            <span id="d-last">—</span>
                        </div>
                        <div class="detail-item">
                            <label>Logout Time</label>
                            <span id="d-logout">—</span>
                        </div>
                        <div class="detail-item" id="d-reason-wrap" style="grid-column:1/-1;display:none">
                            <label>Failure Reason</label>
                            <span id="d-reason" class="text-danger">—</span>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('loginDetailModal').addEventListener('show.bs.modal', function (e) {
    var btn = e.relatedTarget;
    if (!btn) return;
    document.getElementById('d-email').textContent    = btn.dataset.email    || '—';
    document.getElementById('d-ip').textContent       = btn.dataset.ip       || '—';
    document.getElementById('d-device').textContent   = btn.dataset.device   || '—';
    document.getElementById('d-os').textContent       = btn.dataset.os       || '—';
    document.getElementById('d-browser').textContent  = btn.dataset.browser  || '—';
    document.getElementById('d-location').textContent = btn.dataset.location || '—';
    document.getElementById('d-login').textContent    = btn.dataset.login    || '—';
    document.getElementById('d-last').textContent     = btn.dataset.last     || '—';
    document.getElementById('d-logout').textContent   = btn.dataset.logout   || '—';

    // status badge
    var statusEl = document.getElementById('d-status');
    var status = btn.dataset.status, online = btn.dataset.online === '1';
    if (status === 'success' && online) {
        statusEl.innerHTML = '<span class="badge badge-success"><span class="status-dot online"></span> Online</span>';
    } else if (status === 'success') {
        statusEl.innerHTML = '<span class="badge badge-gray">Offline</span>';
    } else {
        statusEl.innerHTML = '<span class="badge badge-danger">Failed</span>';
    }

    // failure reason
    var reason = btn.dataset.reason;
    var reasonWrap = document.getElementById('d-reason-wrap');
    if (reason) {
        document.getElementById('d-reason').textContent = reason;
        reasonWrap.style.display = '';
    } else {
        reasonWrap.style.display = 'none';
    }
});
</script>
@endsection

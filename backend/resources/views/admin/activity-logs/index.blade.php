@extends('layouts.app')
@section('title', 'Activity Logs')

@push('style')
<style>
/* ── Layout ───────────────────────────────────── */
.al-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px}
.al-header h1{font-size:22px;font-weight:700;color:var(--text-primary);margin:0 0 2px}
.al-count{font-size:13px;color:var(--text-muted)}

/* ── Filter card ──────────────────────────────── */
.al-filter{background:#fff;border:1px solid var(--border-color);border-radius:var(--border-radius);padding:16px 20px;margin-bottom:20px}
.al-filter-grid{display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end}
.al-filter-group{display:flex;flex-direction:column;gap:4px;flex:1;min-width:140px}
.al-filter-group.stretch{flex:2;min-width:200px}
.al-filter-label{font-size:11.5px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.04em}
.al-filter-actions{display:flex;gap:8px;align-items:center;padding-top:18px}

/* ── Table card ───────────────────────────────── */
.al-card{background:#fff;border:1px solid var(--border-color);border-radius:var(--border-radius);overflow:hidden}
.al-table-wrap{overflow-x:auto}
.al-table{width:100%;border-collapse:collapse;font-size:13px}
.al-table thead th{background:#f8fafc;padding:10px 14px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#64748b;border-bottom:1px solid var(--border-color);white-space:nowrap}
.al-table tbody tr{border-bottom:1px solid #f1f5f9;transition:background .12s}
.al-table tbody tr:last-child{border-bottom:none}
.al-table tbody tr:hover{background:#f8fafc}
.al-table td{padding:10px 14px;vertical-align:middle}

/* ── Number cell ──────────────────────────────── */
.al-num{font-size:11.5px;color:#94a3b8;font-weight:500;text-align:center}

/* ── Admin cell ───────────────────────────────── */
.al-admin{display:flex;align-items:center;gap:10px}
.al-avatar{width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#fff;flex-shrink:0;background:var(--primary)}
.al-admin-info{min-width:0}
.al-admin-name{font-size:13px;font-weight:600;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:130px}
.al-admin-meta{display:flex;align-items:center;gap:6px;margin-top:2px;flex-wrap:wrap}
.al-role{font-size:10.5px;font-weight:600;padding:1px 7px;border-radius:10px;background:#f1f5f9;color:#64748b;white-space:nowrap}
.al-ip{font-size:10.5px;color:#94a3b8;font-family:monospace}

/* ── Action badges ────────────────────────────── */
.al-action{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;white-space:nowrap;letter-spacing:.02em}
.al-action-dot{width:5px;height:5px;border-radius:50%}
.al-action--created   {background:#dcfce7;color:#15803d}
.al-action--created .al-action-dot{background:#16a34a}
.al-action--updated   {background:#fef9c3;color:#a16207}
.al-action--updated .al-action-dot{background:#ca8a04}
.al-action--deleted   {background:#fee2e2;color:#b91c1c}
.al-action--deleted .al-action-dot{background:#dc2626}
.al-action--login     {background:#dbeafe;color:#1d4ed8}
.al-action--login .al-action-dot{background:#2563eb}
.al-action--logout    {background:#f1f5f9;color:#475569}
.al-action--logout .al-action-dot{background:#94a3b8}
.al-action--viewed    {background:#e0f2fe;color:#0369a1}
.al-action--viewed .al-action-dot{background:#0284c7}
.al-action--status-changed  {background:#ffedd5;color:#1e40af}
.al-action--status-changed .al-action-dot{background:#1d4ed8}
.al-action--permission-changed{background:#f3e8ff;color:#7e22ce}
.al-action--permission-changed .al-action-dot{background:#9333ea}
.al-action--default   {background:#f1f5f9;color:#475569}
.al-action--default .al-action-dot{background:#94a3b8}

/* ── Module pill ──────────────────────────────── */
.al-module{display:inline-block;font-size:11px;font-weight:600;color:#334155;background:#f1f5f9;border:1px solid #e2e8f0;padding:2px 9px;border-radius:6px;white-space:nowrap}

/* ── Description ──────────────────────────────── */
.al-desc{font-size:12.5px;color:var(--text-primary);line-height:1.4;max-width:320px}
.al-desc-text{display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}

/* ── Device cell ──────────────────────────────── */
.al-device{display:flex;align-items:center;gap:6px}
.al-device-icon{font-size:14px;color:#94a3b8;flex-shrink:0}
.al-device-info{font-size:11.5px}
.al-device-os{font-weight:600;color:var(--text-primary)}
.al-device-browser{color:#94a3b8;font-size:10.5px;margin-top:1px}

/* ── Time cell ────────────────────────────────── */
.al-date{font-size:12px;font-weight:600;color:var(--text-primary);white-space:nowrap}
.al-time{font-size:11px;color:#94a3b8;margin-top:2px}

/* ── Changes button ───────────────────────────── */
.al-changes-btn{display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:6px;border:1.5px solid #e2e8f0;color:#64748b;background:#fff;cursor:pointer;transition:all .12s;text-decoration:none}
.al-changes-btn:hover{background:#f1f5f9;border-color:#cbd5e1;color:var(--primary)}
.al-changes-none{color:#cbd5e1;font-size:14px;display:block;text-align:center}

/* ── Empty state ──────────────────────────────── */
.al-empty{text-align:center;padding:60px 20px}
.al-empty-icon{font-size:40px;color:#cbd5e1;margin-bottom:12px}
.al-empty-title{font-size:15px;font-weight:600;color:#94a3b8;margin-bottom:4px}
.al-empty-sub{font-size:13px;color:#b0bec5}

/* ── Footer ───────────────────────────────────── */
.al-footer{padding:12px 20px;border-top:1px solid var(--border-color);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px}
.al-footer-count{font-size:12.5px;color:#94a3b8}

/* ── Diff modal ───────────────────────────────── */
.diff-block{background:#f8fafc;border:1px solid var(--border-color);border-radius:6px;padding:12px;font-size:12px;font-family:monospace;max-height:300px;overflow-y:auto;white-space:pre-wrap;word-break:break-all;color:var(--text-primary);margin:0}
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="al-header">
    <div>
        <h1>Activity Logs</h1>
        <span class="al-count">{{ number_format($logs->total()) }} total records</span>
    </div>
    <a href="{{ route('admin.activity-logs.export', request()->query()) }}"
       class="btn btn-sm btn-outline-success" style="gap:6px">
        <i class="fas fa-download"></i> Export CSV
    </a>
</div>

{{-- Filters --}}
<div class="al-filter">
    <form method="GET">
        <div class="al-filter-grid">

            <div class="al-filter-group stretch">
                <span class="al-filter-label">Search</span>
                <input type="text" name="search" class="form-control form-control-sm"
                       value="{{ request('search') }}" placeholder="Admin name or description…">
            </div>

            <div class="al-filter-group">
                <span class="al-filter-label">Module</span>
                <select name="module" class="form-select form-select-sm">
                    <option value="">All Modules</option>
                    @foreach($modules as $mod)
                    <option value="{{ $mod }}" {{ request('module') == $mod ? 'selected' : '' }}>{{ $mod }}</option>
                    @endforeach
                </select>
            </div>

            <div class="al-filter-group">
                <span class="al-filter-label">Action</span>
                <select name="action" class="form-select form-select-sm">
                    <option value="">All Actions</option>
                    @foreach($actions as $act)
                    <option value="{{ $act }}" {{ request('action') == $act ? 'selected' : '' }}>{{ ucfirst(str_replace('-', ' ', $act)) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="al-filter-group" style="min-width:130px;max-width:150px">
                <span class="al-filter-label">From Date</span>
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
            </div>

            <div class="al-filter-group" style="min-width:130px;max-width:150px">
                <span class="al-filter-label">To Date</span>
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
            </div>

            <div class="al-filter-actions">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-xmark me-1"></i> Reset
                </a>
            </div>

        </div>
    </form>
</div>

{{-- Table --}}
<div class="al-card">
    <div class="al-table-wrap">
        <table class="al-table">
            <thead>
                <tr>
                    <th style="min-width:160px">Admin</th>
                    <th style="width:130px">Action</th>
                    <th style="width:140px">Module</th>
                    <th>Description</th>
                    <th style="width:110px">Device</th>
                    <th style="width:115px">Time</th>
                    <th style="width:50px;text-align:center">
                        <i class="fas fa-code-compare" title="Changes"></i>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                @php
                    $actionSlug = strtolower(str_replace([' ', '_'], '-', $log->action ?? 'default'));
                    $actionSlugMap = [
                        'created' => 'created', 'updated' => 'updated', 'deleted' => 'deleted',
                        'viewed' => 'viewed', 'login' => 'login', 'logout' => 'logout',
                        'status-changed' => 'status-changed', 'permission-changed' => 'permission-changed',
                    ];
                    $actionClass = $actionSlugMap[$actionSlug] ?? 'default';
                    $actionLabel = ucfirst(str_replace('-', ' ', $log->action ?? '—'));
                    $hasChanges = !empty($log->old_value) || !empty($log->new_value);
                    $avatarLetter = strtoupper(substr($log->user_name ?? 'A', 0, 1));
                    $avatarColors = ['#6366f1','#0ea5e9','#10b981','#f59e0b','#ef4444','#8b5cf6','#ec4899','#14b8a6'];
                    $avatarColor = $avatarColors[crc32($log->user_name ?? '') % count($avatarColors)];
                @endphp
                <tr>
                    {{-- Admin --}}
                    <td>
                        <div class="al-admin">
                            <div class="al-avatar" style="background:{{ $avatarColor }}">{{ $avatarLetter }}</div>
                            <div class="al-admin-info">
                                <div class="al-admin-name">{{ $log->user_name ?? '—' }}</div>
                                <div class="al-admin-meta">
                                    @if($log->role)
                                    <span class="al-role">{{ $log->role }}</span>
                                    @endif
                                    <span class="al-ip">{{ $log->ip_address ?? '' }}</span>
                                </div>
                            </div>
                        </div>
                    </td>

                    {{-- Action --}}
                    <td>
                        @if($log->action)
                        <span class="al-action al-action--{{ $actionClass }}">
                            <span class="al-action-dot"></span>
                            {{ $actionLabel }}
                        </span>
                        @else
                        <span style="color:#cbd5e1;font-size:12px">—</span>
                        @endif
                    </td>

                    {{-- Module --}}
                    <td>
                        @if($log->module)
                        <span class="al-module">{{ $log->module }}</span>
                        @else
                        <span style="color:#cbd5e1;font-size:12px">—</span>
                        @endif
                    </td>

                    {{-- Description --}}
                    <td class="al-desc">
                        <div class="al-desc-text" title="{{ $log->description }}">
                            {{ $log->description ?? '—' }}
                        </div>
                    </td>

                    {{-- Device --}}
                    <td>
                        <div class="al-device">
                            @php
                                $deviceType = $log->device_type ?? 'desktop';
                                $deviceIcon = match($deviceType) {
                                    'mobile'  => 'fa-mobile-screen',
                                    'tablet'  => 'fa-tablet-screen-button',
                                    default   => 'fa-desktop',
                                };
                            @endphp
                            <i class="fas {{ $deviceIcon }} al-device-icon"></i>
                            <div class="al-device-info">
                                <div class="al-device-os">{{ $log->os_name ?? ucfirst($deviceType) }}</div>
                                @if($log->browser_name)
                                <div class="al-device-browser">{{ $log->browser_name }}</div>
                                @endif
                            </div>
                        </div>
                    </td>

                    {{-- Time --}}
                    <td>
                        <div class="al-date">{{ $log->created_at->format('d M Y') }}</div>
                        <div class="al-time">{{ $log->created_at->format('H:i:s') }}</div>
                    </td>

                    {{-- Changes --}}
                    <td style="text-align:center">
                        @if($hasChanges)
                        <button class="al-changes-btn"
                                data-bs-toggle="modal" data-bs-target="#changesModal"
                                data-old="{{ !empty($log->old_value) ? json_encode($log->old_value, JSON_PRETTY_PRINT) : '' }}"
                                data-new="{{ !empty($log->new_value) ? json_encode($log->new_value, JSON_PRETTY_PRINT) : '' }}"
                                data-action="{{ $log->action }}"
                                data-module="{{ $log->module }}"
                                title="View changes">
                            <i class="fas fa-code-compare" style="font-size:12px"></i>
                        </button>
                        @else
                        <span class="al-changes-none">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="al-empty">
                            <div class="al-empty-icon"><i class="fas fa-clock-rotate-left"></i></div>
                            <div class="al-empty-title">No activity logs found</div>
                            <div class="al-empty-sub">Try adjusting your filters</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->lastPage() > 1)
    <div class="al-footer">
        <span class="al-footer-count">
            Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ number_format($logs->total()) }}
        </span>
        @include('admin.common.pagination', ['paginator' => $logs])
    </div>
    @endif
</div>

@endsection

@section('modal')
{{-- Changes diff modal --}}
<div class="modal fade" id="changesModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-code-compare me-2" style="color:var(--primary)"></i>
                    Change History
                    <span id="changes-label" class="text-muted fw-normal ms-1" style="font-size:.85rem"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div style="display:flex;align-items:center;gap:6px;margin-bottom:8px">
                            <span style="width:8px;height:8px;border-radius:50%;background:#ef4444;display:inline-block"></span>
                            <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8">Before</span>
                        </div>
                        <pre id="oldValuePre" class="diff-block"></pre>
                    </div>
                    <div class="col-md-6">
                        <div style="display:flex;align-items:center;gap:6px;margin-bottom:8px">
                            <span style="width:8px;height:8px;border-radius:50%;background:#10b981;display:inline-block"></span>
                            <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8">After</span>
                        </div>
                        <pre id="newValuePre" class="diff-block"></pre>
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
document.getElementById('changesModal').addEventListener('show.bs.modal', function (e) {
    var btn = e.relatedTarget;
    document.getElementById('oldValuePre').textContent = btn.dataset.old || '(no data)';
    document.getElementById('newValuePre').textContent = btn.dataset.new || '(no data)';
    var label = [btn.dataset.action, btn.dataset.module].filter(Boolean).join(' · ');
    document.getElementById('changes-label').textContent = label ? '— ' + label : '';
});
</script>
@endsection

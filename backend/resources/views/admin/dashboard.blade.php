@extends('layouts.app')
@section('title', 'Dashboard')

@push('style')
<style>
/* ── Welcome Banner ─────────────────────────── */
.db-banner {
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 60%, #eff6ff 100%);
    border: 1px solid #bfdbfe;
    border-radius: 14px;
    padding: 26px 32px;
    color: #1e3a5f;
    position: relative;
    overflow: hidden;
    margin-bottom: 24px;
}
.db-banner::before {
    content: '';
    position: absolute;
    top: -50px; right: -50px;
    width: 220px; height: 220px;
    border-radius: 50%;
    background: rgba(37,99,235,.07);
    pointer-events: none;
}
.db-banner::after {
    content: '';
    position: absolute;
    bottom: -40px; right: 100px;
    width: 140px; height: 140px;
    border-radius: 50%;
    background: rgba(37,99,235,.05);
    pointer-events: none;
}
.db-banner-icon {
    position: absolute;
    right: 28px; top: 50%;
    transform: translateY(-50%);
    font-size: 80px;
    opacity: .08;
    color: #2563eb;
    pointer-events: none;
}
.db-banner h2  { font-size: 20px; font-weight: 700; margin: 0 0 4px; color: #1e3a5f; }
.db-banner p   { font-size: 13px; color: #3b6fa0; margin: 0; }
.db-banner-meta { display: flex; gap: 12px; margin-top: 14px; flex-wrap: wrap; }
.db-meta-pill {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: 12px;
    background: rgba(37,99,235,.10);
    color: #1d4ed8;
    padding: 4px 12px; border-radius: 20px;
}
.db-meta-pill.alert-pill {
    background: rgba(239,68,68,.10);
    color: #dc2626;
}

/* ── Stat Cards ──────────────────────────────── */
.db-stat-link { display: block; text-decoration: none; color: inherit; height: 100%; }
.db-stat-link:hover { color: inherit; }

.db-stat {
    border-radius: 12px;
    padding: 20px 22px;
    position: relative;
    overflow: hidden;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-height: 120px;
    transition: transform .18s, box-shadow .18s;
}
.db-stat-link:hover .db-stat {
    transform: translateY(-3px);
    box-shadow: 0 8px 28px rgba(0,0,0,.16);
}
.db-stat-bg-icon {
    position: absolute;
    right: 16px; top: 50%;
    transform: translateY(-50%);
    font-size: 54px;
    opacity: .13;
    color: #fff;
    pointer-events: none;
}
.db-stat-num  { font-size: 32px; font-weight: 700; color: #fff; line-height: 1; margin-bottom: 4px; }
.db-stat-lbl  { font-size: 13px; font-weight: 500; color: rgba(255,255,255,.85); }
.db-stat-sub  { font-size: 11.5px; color: rgba(255,255,255,.65); margin-top: 8px; display: flex; gap: 10px; flex-wrap: wrap; }
.db-stat-sub i { font-size: 10px; }

.db-c-blue   { background: linear-gradient(135deg, #2563eb, #1d4ed8); }
.db-c-green  { background: linear-gradient(135deg, #059669, #047857); }
.db-c-red    { background: linear-gradient(135deg, #dc2626, #b91c1c); }
.db-c-purple { background: linear-gradient(135deg, #7c3aed, #6d28d9); }
.db-c-sky    { background: linear-gradient(135deg, #0ea5e9, #0284c7); }
.db-c-indigo { background: linear-gradient(135deg, #4f46e5, #4338ca); }

/* ── Quick Actions ───────────────────────────── */
.db-qa-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
@media (max-width: 576px) { .db-qa-grid { grid-template-columns: repeat(2, 1fr); } }

.db-qa {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 18px 10px;
    border-radius: 10px;
    border: 1.5px solid #e9ecef;
    background: #fff;
    text-decoration: none;
    color: #374151;
    font-size: 12.5px;
    font-weight: 500;
    text-align: center;
    transition: all .16s;
    cursor: pointer;
}
.db-qa:hover { color: inherit; }
.db-qa-icon {
    width: 40px; height: 40px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 17px;
}
.db-qa:hover { border-color: transparent; box-shadow: 0 4px 16px rgba(0,0,0,.1); transform: translateY(-2px); }

/* ── Enquiry table ───────────────────────────── */
.db-enq-table td { padding: 11px 16px; vertical-align: middle; font-size: 13px; border-color: #f1f5f9; }
.db-enq-badge {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
}
</style>
@endpush

@section('content')

{{-- Welcome Banner --}}
<div class="db-banner">
    <i class="fas fa-plane-departure db-banner-icon"></i>
    <h2>Welcome back, {{ auth()->user()->name ?? 'Admin' }}! 👋</h2>
    <p>Here's what's happening with Indian Panorama today.</p>
    <div class="db-banner-meta">
        <span class="db-meta-pill">
            <i class="fas fa-calendar-day"></i>
            {{ now()->format('l, d F Y') }}
        </span>
        @if(auth()->user()->is_super_admin)
        <span class="db-meta-pill">
            <i class="fas fa-crown"></i> Super Admin
        </span>
        @endif
        @if($totalPendingEnquery > 0)
        <span class="db-meta-pill alert-pill">
            <i class="fas fa-bell"></i>
            {{ $totalPendingEnquery }} pending enquir{{ $totalPendingEnquery == 1 ? 'y' : 'ies' }}
        </span>
        @endif
    </div>
</div>

{{-- Stat Cards --}}
<div class="row g-3 mb-4">

    <div class="col-xl-3 col-lg-4 col-md-6">
        <a href="{{ route('admin.packages.index') }}" class="db-stat-link">
            <div class="db-stat db-c-blue">
                <i class="fas fa-suitcase-rolling db-stat-bg-icon"></i>
                <div>
                    <div class="db-stat-num">{{ $totalPackages }}</div>
                    <div class="db-stat-lbl">Total Packages</div>
                </div>
                <div class="db-stat-sub">
                    <span><i class="fas fa-check-circle"></i> {{ $activeP }} active</span>
                    <span><i class="fas fa-times-circle"></i> {{ $activeIn }} inactive</span>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-lg-4 col-md-6">
        <a href="{{ route('admin.categories.index') }}" class="db-stat-link">
            <div class="db-stat db-c-green">
                <i class="fas fa-tags db-stat-bg-icon"></i>
                <div>
                    <div class="db-stat-num">{{ $totalCategories }}</div>
                    <div class="db-stat-lbl">Categories</div>
                </div>
                <div class="db-stat-sub">
                    <span>Tour categories active</span>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-lg-4 col-md-6">
        <a href="#" class="db-stat-link">
            <div class="db-stat db-c-red">
                <i class="fas fa-envelope-open db-stat-bg-icon"></i>
                <div>
                    <div class="db-stat-num">{{ $totalPendingEnquery }}</div>
                    <div class="db-stat-lbl">Pending Enquiries</div>
                </div>
                <div class="db-stat-sub">
                    <span>Awaiting response</span>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-lg-4 col-md-6">
        <a href="{{ route('admin.location-setting.index', ['tab' => 'cities']) }}" class="db-stat-link">
            <div class="db-stat db-c-purple">
                <i class="fas fa-map-marker-alt db-stat-bg-icon"></i>
                <div>
                    <div class="db-stat-num">{{ $totalCity }}</div>
                    <div class="db-stat-lbl">Cities / Locations</div>
                </div>
                <div class="db-stat-sub">
                    <span>Travel destinations</span>
                </div>
            </div>
        </a>
    </div>

</div>

{{-- Quick Actions + Enquiry Overview --}}
<div class="row g-3">

    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header fw-semibold">
                <i class="fas fa-bolt text-primary me-2"></i>Quick Actions
            </div>
            <div class="card-body">
                <div class="db-qa-grid">
                    <a href="{{ route('admin.packages.create') }}" class="db-qa">
                        <div class="db-qa-icon" style="background:rgba(37,99,235,.12);color:#2563eb;">
                            <i class="fas fa-plus-circle"></i>
                        </div>
                        New Package
                    </a>
                    <a href="" class="db-qa">
                        <div class="db-qa-icon" style="background:rgba(14,165,233,.12);color:#0ea5e9;">
                            <i class="fas fa-envelope-open-text"></i>
                        </div>
                        Enquiries
                    </a>
                    <a href="{{ route('admin.news.index') }}" class="db-qa">
                        <div class="db-qa-icon" style="background:rgba(245,158,11,.12);color:#b45309;">
                            <i class="fas fa-newspaper"></i>
                        </div>
                        Blog / News
                    </a>
                    <a href="{{ route('admin.banners.index') }}" class="db-qa">
                        <div class="db-qa-icon" style="background:rgba(236,72,153,.12);color:#ec4899;">
                            <i class="fas fa-images"></i>
                        </div>
                        Banners
                    </a>
                    <a href="{{ route('admin.tourist-attractions.index') }}" class="db-qa">
                        <div class="db-qa-icon" style="background:rgba(5,150,105,.12);color:#059669;">
                            <i class="fas fa-mountain"></i>
                        </div>
                        Attractions
                    </a>
                    <a href="{{ route('admin.festival.index') }}" class="db-qa">
                        <div class="db-qa-icon" style="background:rgba(139,92,246,.12);color:#8b5cf6;">
                            <i class="fas fa-star-and-crescent"></i>
                        </div>
                        Festivals
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header fw-semibold">
                <i class="fas fa-chart-bar text-primary me-2"></i>Enquiry Overview
            </div>
            <div class="card-body p-0">
                <table class="table mb-0 db-enq-table">
                    <tbody>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span style="width:8px;height:8px;border-radius:50%;background:#2563eb;display:inline-block;flex-shrink:0;"></span>
                                    Tour Booking
                                </div>
                            </td>
                            <td class="text-end">
                                <a href="#" class="db-enq-badge" style="background:rgba(37,99,235,.1);color:#2563eb;">View</a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span style="width:8px;height:8px;border-radius:50%;background:#0ea5e9;display:inline-block;flex-shrink:0;"></span>
                                    Car Enquiry
                                </div>
                            </td>
                            <td class="text-end">
                                <a href="" class="db-enq-badge" style="background:rgba(14,165,233,.1);color:#0ea5e9;">View</a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span style="width:8px;height:8px;border-radius:50%;background:#64748b;display:inline-block;flex-shrink:0;"></span>
                                    General
                                </div>
                            </td>
                            <td class="text-end">
                                <a href="" class="db-enq-badge" style="background:rgba(100,116,139,.1);color:#64748b;">View</a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span style="width:8px;height:8px;border-radius:50%;background:#8b5cf6;display:inline-block;flex-shrink:0;"></span>
                                    Plan a Trip
                                </div>
                            </td>
                            <td class="text-end">
                                <a href="" class="db-enq-badge" style="background:rgba(139,92,246,.1);color:#8b5cf6;">View</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@endsection

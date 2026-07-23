@extends('layouts.app')

@section('title', 'Holiday Packages Menu — Auto-Generated')

@push('style')
<link rel="stylesheet" href="{{ asset('css/holiday-menu.css') }}">
@endpush

@section('content')

{{-- ── JSON Config Bridge ──────────────────────────────────────── --}}
<script id="holidayMenuConfig" type="application/json">
{
    "reorderUrl": "{{ route('admin.holiday-menu.reorder') }}",
    "toggleUrl":  "{{ route('admin.holiday-menu.toggle') }}",
    "csrfToken":  "{{ csrf_token() }}"
}
</script>

<div class="container-fluid py-4">

    {{-- ── Page Header ── --}}
    <div class="mb-4">
        <h1 class="h4 fw-bold text-dark mb-1">
            <i class="fas fa-bars me-2 text-warning"></i>Menu Management
        </h1>
        <p class="text-muted mb-0" style="font-size:13px;">
            Manage Header, Footer, and Holiday Packages menus from one place.
        </p>
    </div>

    {{-- ── Info Banner ── --}}
    <div class="hm-info-banner mb-4">
        <i class="fas fa-info-circle"></i>
        <div>
            <strong>Read-only structure.</strong>
            This menu is built automatically from your Packages database.
            Locations appear here only when they have at least one published package.
            You can <strong>reorder</strong> regions, states, and cities using drag-and-drop,
            and <strong>hide / show</strong> any item using the eye icon.
            Changes take effect on the live website immediately.
        </div>
    </div>

    {{-- ── Tabs (shared with Menu Builder) ── --}}
    <ul class="nav nav-tabs mb-0" style="border-bottom: 2px solid #e2e8f0;">
        @foreach($menus as $m)
        <li class="nav-item">
            <a class="nav-link fw-semibold"
               href="{{ route('admin.menus.show', $m) }}"
               style="color:#64748b;">
                <i class="fas {{ $m->icon() }} me-2"></i>{{ $m->name }}
                <span class="badge ms-1 bg-light text-muted" style="font-size:10px;">
                    {{ $m->itemCount() }}
                </span>
            </a>
        </li>
        @endforeach
        {{-- Active: Holiday Packages --}}
        <li class="nav-item">
            <a class="nav-link fw-semibold active"
               href="{{ route('admin.holiday-menu.show') }}"
               style="border-bottom:2px solid #2563eb; color:#2563eb;">
                <i class="fas fa-map-marked-alt me-2"></i>Holiday Packages
                <span class="badge ms-1 bg-warning text-dark" style="font-size:10px;">
                    {{ $stats['locations'] }}
                </span>
            </a>
        </li>
    </ul>

    {{-- ── Builder Panel ── --}}
    <div class="hm-panel">

        {{-- Stats Bar --}}
        <div class="hm-stats-bar">
            <div class="hm-stat">
                <span class="hm-stat-num text-warning" id="hmStatRegions">{{ $stats['regions'] }}</span>
                <span class="hm-stat-lbl">Regions</span>
            </div>
            <div class="hm-stat">
                <span class="hm-stat-num text-primary" id="hmStatStates">{{ $stats['states'] }}</span>
                <span class="hm-stat-lbl">States</span>
            </div>
            <div class="hm-stat">
                <span class="hm-stat-num text-success" id="hmStatLocs">{{ $stats['locations'] }}</span>
                <span class="hm-stat-lbl">Cities</span>
            </div>
            @if($stats['hidden_regions'] + $stats['hidden_states'] + $stats['hidden_locations'] > 0)
            <div class="hm-stat">
                <span class="hm-stat-num text-secondary">
                    {{ $stats['hidden_regions'] + $stats['hidden_states'] + $stats['hidden_locations'] }}
                </span>
                <span class="hm-stat-lbl">Hidden</span>
            </div>
            @endif
            <span class="hm-save-indicator" id="hmSaveIndicator">
                <i class="fas fa-cloud text-success me-1"></i>All saved
            </span>
        </div>

        {{-- Canvas --}}
        <div class="hm-canvas">

            @if($tree->isEmpty())

            {{-- Empty State --}}
            <div class="hm-empty">
                <i class="fas fa-suitcase-rolling"></i>
                <div class="hm-empty-title">No holiday packages found</div>
                <div class="hm-empty-sub">
                    Add packages to locations in the
                    <a href="{{ route('admin.packages.index') }}" class="text-warning fw-semibold">Packages</a>
                    section and they will appear here automatically.
                </div>
            </div>

            @else

            {{-- Region List (sortable) --}}
            <div id="hmRegionList">

                @foreach($tree as $region)
                @php
                    $regionStateCount = $region->states->count();
                    $regionLocCount   = $region->states->sum(fn($s) => $s->locations->count());
                    $statesContainerId = 'states-' . $region->id;
                @endphp

                {{-- ── Region Block ──────────────────────────────────────── --}}
                <div class="hm-region {{ !$region->is_visible ? 'hidden-node' : '' }}"
                     data-id="{{ $region->id }}"
                     data-node-wrap>

                    {{-- Region Header --}}
                    <div class="hm-region-header">

                        {{-- Drag handle --}}
                        <div class="hm-handle" data-level="region" title="Drag to reorder">
                            <i class="fas fa-grip-vertical"></i>
                        </div>

                        {{-- Collapse button --}}
                        <button type="button" class="hm-toggle"
                                data-collapse-btn="{{ $statesContainerId }}"
                                title="Expand / Collapse">
                            <i class="fas fa-chevron-down"></i>
                        </button>

                        {{-- Region icon --}}
                        <div class="hm-icon-dot" title="Region">
                            <i class="fas fa-globe-asia"></i>
                        </div>

                        {{-- Label --}}
                        <div class="hm-meta">
                            <div class="hm-name">
                                {{ $region->name }}
                                @if(!$region->is_visible)
                                <span class="badge bg-secondary ms-1 hm-badge" data-vis-badge
                                      style="font-size:10px;">Hidden</span>
                                @else
                                <span class="badge bg-secondary ms-1 hm-badge" data-vis-badge
                                      style="font-size:10px; display:none;"></span>
                                @endif
                            </div>
                            <div class="hm-sub">
                                {{ $regionStateCount }} {{ Str::plural('state', $regionStateCount) }} ·
                                {{ $regionLocCount }} {{ Str::plural('city', $regionLocCount) }}
                            </div>
                        </div>

                        {{-- Toggle visibility --}}
                        <div class="hm-actions">
                            <button type="button"
                                    class="hm-btn {{ $region->is_visible ? 'hm-vis' : 'hm-invis' }}"
                                    data-toggle-vis
                                    data-type="region"
                                    data-id="{{ $region->id }}"
                                    title="{{ $region->is_visible ? 'Hide' : 'Show' }}">
                                <i class="fas {{ $region->is_visible ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                            </button>
                        </div>

                    </div>{{-- /region-header --}}

                    {{-- States Container --}}
                    <div id="{{ $statesContainerId }}" class="hm-states" data-state-list>

                        @forelse($region->states as $state)
                        @php
                            $stateLocCount     = $state->locations->count();
                            $locsContainerId   = 'locs-' . $state->id;
                        @endphp

                        {{-- ── State Row ──────────────────────────────────── --}}
                        <div class="hm-state {{ !$state->is_visible ? 'hidden-node' : '' }}"
                             data-id="{{ $state->id }}"
                             data-node-wrap>

                            {{-- State header row --}}
                            <div class="hm-state-row">

                                <div class="hm-handle" data-level="state" title="Drag to reorder">
                                    <i class="fas fa-grip-vertical"></i>
                                </div>

                                <button type="button" class="hm-toggle"
                                        data-collapse-btn="{{ $locsContainerId }}"
                                        title="Expand / Collapse">
                                    <i class="fas fa-chevron-down"></i>
                                </button>

                                <div class="hm-state-icon-dot" title="State / Package Group">
                                    <i class="fas fa-layer-group"></i>
                                </div>

                                <div class="hm-meta">
                                    <div class="hm-name" style="font-size:13px;">
                                        {{ $state->name }}
                                        @if(!$state->is_visible)
                                        <span class="badge bg-secondary ms-1 hm-badge"
                                              data-vis-badge style="font-size:10px;">Hidden</span>
                                        @else
                                        <span class="badge bg-secondary ms-1 hm-badge"
                                              data-vis-badge style="font-size:10px; display:none;"></span>
                                        @endif
                                    </div>
                                    <div class="hm-sub">
                                        {{ $stateLocCount }} {{ Str::plural('city', $stateLocCount) }}
                                    </div>
                                </div>

                                <div class="hm-actions">
                                    <button type="button"
                                            class="hm-btn {{ $state->is_visible ? 'hm-vis' : 'hm-invis' }}"
                                            data-toggle-vis
                                            data-type="state"
                                            data-id="{{ $state->id }}"
                                            title="{{ $state->is_visible ? 'Hide' : 'Show' }}">
                                        <i class="fas {{ $state->is_visible ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                                    </button>
                                </div>

                            </div>{{-- /state-row --}}

                            {{-- Locations List --}}
                            <div id="{{ $locsContainerId }}"
                                 class="hm-locations"
                                 data-location-list>

                                @forelse($state->locations as $loc)

                                {{-- ── Location Row ────────────────────────── --}}
                                <div class="hm-location {{ !$loc->is_visible ? 'hidden-node' : '' }}"
                                     data-id="{{ $loc->id }}"
                                     data-node-wrap>

                                    <div class="hm-handle" data-level="location" title="Drag to reorder">
                                        <i class="fas fa-grip-vertical"></i>
                                    </div>

                                    <div class="hm-loc-icon-dot" title="City / Destination">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>

                                    <div class="hm-meta" style="flex:1; min-width:0;">
                                        <div class="hm-name" style="font-size:13px;">
                                            {{ $loc->name }}
                                            @if(!$loc->is_visible)
                                            <span class="badge bg-secondary ms-1 hm-badge"
                                                  data-vis-badge style="font-size:9px;">Hidden</span>
                                            @else
                                            <span class="badge bg-secondary ms-1 hm-badge"
                                                  data-vis-badge style="font-size:9px; display:none;"></span>
                                            @endif
                                        </div>
                                        <div class="hm-sub" style="font-size:10px;">
                                            /holidays/{{ $loc->slug }}
                                        </div>
                                    </div>

                                    <div class="hm-actions">
                                        <button type="button"
                                                class="hm-btn {{ $loc->is_visible ? 'hm-vis' : 'hm-invis' }}"
                                                data-toggle-vis
                                                data-type="location"
                                                data-id="{{ $loc->id }}"
                                                title="{{ $loc->is_visible ? 'Hide' : 'Show' }}">
                                            <i class="fas {{ $loc->is_visible ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                                        </button>
                                    </div>

                                </div>{{-- /hm-location --}}

                                @empty
                                <div class="hm-no-packages">No cities with packages in this state.</div>
                                @endforelse

                            </div>{{-- /hm-locations --}}

                        </div>{{-- /hm-state --}}

                        @empty
                        <div class="hm-no-packages">No states with packages in this region.</div>
                        @endforelse

                    </div>{{-- /hm-states --}}

                </div>{{-- /hm-region --}}
                @endforeach

            </div>{{-- /hmRegionList --}}

            @endif

        </div>{{-- /hm-canvas --}}
    </div>{{-- /hm-panel --}}

</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="{{ asset('js/holiday-menu.js') }}"></script>
@endsection

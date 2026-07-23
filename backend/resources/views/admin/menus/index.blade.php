@extends('layouts.app')

@section('title', 'Menu Management — ' . $menu->name)

@push('style')
<link rel="stylesheet" href="{{ asset('css/menu-manager.css') }}">
<style>
    /* Custom badge colours (not in Bootstrap) */
    .bg-purple { background-color: #7c3aed !important; }
    .bg-orange { background-color: #1d4ed8 !important; }
    .bg-teal   { background-color: #0d9488 !important; }
    .type-btn-menu-ref { border-color: #7c3aed; }
    .type-btn-menu-ref.active { background: #7c3aed; color: #fff; border-color: #7c3aed; }
    .type-btn-menu-ref:hover:not(.active) { background: #ede9fe; color: #7c3aed; }

    /* Menu-reference inline badge in item rows */
    .item-ref-badge {
        display: inline-block;
        font-size: 10px;
        padding: 1px 6px;
        background: #ede9fe;
        color: #7c3aed;
        border-radius: 3px;
        margin-left: 4px;
        vertical-align: middle;
    }

    /* Active menu tab highlight */
    .menu-tab-active   { border-bottom: 2px solid #2563eb; color: #2563eb !important; }
    .menu-tab-inactive { color: #64748b; }

    /* ── Content-type toggle (Normal / Mega / Menu Ref) ── */
    .content-type-btn {
        flex: 1;
        min-width: 100px;
        border: 2px solid #e2e8f0;
        background: #f8fafc;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 12.5px;
        font-weight: 600;
        color: #64748b;
        cursor: pointer;
        text-align: center;
        transition: all .15s;
        user-select: none;
    }
    .content-type-btn:hover          { border-color: #2563eb; color: #2563eb; background: #eff6ff; }
    .content-type-btn.active         { border-color: #2563eb; background: #eff6ff; color: #1e40af; }
    .content-type-btn.ctype-mega.active { border-color: #7c3aed; background: #f5f3ff; color: #6d28d9; }

    /* ── Mega settings panel ── */
    #megaMenuPanel {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 18px;
    }
    .mega-source-btn {
        flex: 1;
        min-width: 120px;
        border: 2px solid #e2e8f0;
        background: #fff;
        border-radius: 8px;
        padding: 9px 12px;
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        cursor: pointer;
        text-align: center;
        transition: all .15s;
    }
    .mega-source-btn:hover { border-color: #7c3aed; color: #7c3aed; background: #f5f3ff; }
    .mega-source-btn.active { border-color: #7c3aed; background: #f5f3ff; color: #5b21b6; }

    .mega-mode-btn {
        flex: 1;
        min-width: 110px;
        border: 1px solid #e2e8f0;
        background: #fff;
        border-radius: 7px;
        padding: 7px 10px;
        font-size: 11.5px;
        font-weight: 600;
        color: #64748b;
        cursor: pointer;
        text-align: center;
        transition: all .15s;
    }
    .mega-mode-btn:hover { border-color: #2563eb; color: #2563eb; }
    .mega-mode-btn.active { border-color: #2563eb; background: #eff6ff; color: #1e40af; }

    /* Mega menu badge in item row */
    .item-mega-badge {
        display: inline-block;
        font-size: 10px;
        padding: 1px 6px;
        background: #f5f3ff;
        color: #7c3aed;
        border-radius: 3px;
        margin-left: 4px;
        vertical-align: middle;
    }
</style>
@endpush

@section('content')

{{-- ══════════════════════════════════════════════════════════════
     DATA BRIDGE — passes Laravel routes/IDs to JavaScript safely
════════════════════════════════════════════════════════════════ --}}
<script id="menuBuilderConfig" type="application/json">
{
    "menuId":         {{ $menu->id }},
    "storeUrl":       "{{ route('admin.menu-items.store',    $menu) }}",
    "reorderUrl":     "{{ route('admin.menu-items.reorder',  $menu) }}",
    "availableUrl":   "{{ url('admin/menu-items/available') }}",
    "showItemUrl":    "{{ url('admin/menu-items') }}",
    "updateUrl":      "{{ url('admin/menu-items') }}",
    "deleteUrl":      "{{ url('admin/menu-items') }}",
    "toggleUrl":      "{{ url('admin/menu-items') }}",
    "createMenuUrl":  "{{ route('admin.menus.store') }}",
    "deleteMenuUrl":  "{{ url('admin/menus') }}",
    "settingsUrl":    "{{ url('admin/menus') }}",
    "csrfToken":      "{{ csrf_token() }}",
    "displayMode":    "{{ $menu->display_mode ?? 'manual' }}",
    "isAutoDisplay":  {{ $menu->isAutoDisplay() ? 'true' : 'false' }},
    "displayModes": {
        @foreach(\App\Models\Menu::DISPLAY_MODES as $modeKey => $modeLabel)
        "{{ $modeKey }}": "{{ $modeLabel }}"{{ !$loop->last ? ',' : '' }}
        @endforeach
    },
    "regionsUrl":     "{{ url('admin/menu-items/available/region') }}",
    "statesUrl":      "{{ url('admin/menu-items/available/state') }}",
    "megaSources": {
        @foreach(\App\Models\MenuItem::MEGA_SOURCES as $srcKey => $srcLabel)
        "{{ $srcKey }}": "{{ $srcLabel }}"{{ !$loop->last ? ',' : '' }}
        @endforeach
    },
    "megaDisplayModes": {
        @foreach(\App\Models\MenuItem::MEGA_DISPLAY_MODES as $modeKey => $modeLabel)
        "{{ $modeKey }}": "{{ $modeLabel }}"{{ !$loop->last ? ',' : '' }}
        @endforeach
    }
}
</script>

<div class="container-fluid py-4">

    {{-- ── Page Header ── --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h4 fw-bold text-dark mb-1">
                <i class="fas fa-bars me-2 text-warning"></i>Menu Management
            </h1>
            <p class="text-muted mb-0" style="font-size:13px;">
                All menus are the single source of truth for the frontend API.
            </p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary fw-semibold px-3" id="btnMenuSettings"
                    data-menu-id="{{ $menu->id }}"
                    title="Display settings for this menu">
                <i class="fas fa-cog me-2"></i>Settings
            </button>
            <button class="btn btn-outline-dark fw-semibold px-3" id="btnNewMenu">
                <i class="fas fa-plus-circle me-2"></i>New Menu
            </button>
            <button class="btn btn-warning fw-semibold px-4" id="btnAddItem">
                <i class="fas fa-plus me-2"></i>Add Item
            </button>
        </div>
    </div>

    {{-- ── Menu Tabs ── --}}
    <ul class="nav nav-tabs mb-0" id="menuTabs" style="border-bottom: 2px solid #e2e8f0;">
        @foreach($menus as $m)
        @php $isActive = $m->id === $menu->id; @endphp
        <li class="nav-item d-flex align-items-center">
            <a class="nav-link fw-semibold {{ $isActive ? 'active menu-tab-active' : 'menu-tab-inactive' }}"
               href="{{ route('admin.menus.show', $m) }}">
                <i class="fas {{ $m->icon() }} me-2"></i>{{ $m->name }}
                <span class="badge ms-1 {{ $isActive ? 'bg-warning text-dark' : 'bg-light text-muted' }}"
                      style="font-size:10px;">
                    {{ $m->itemCount() }}
                </span>
            </a>
            @if(! $m->isSystem())
            <button type="button"
                    class="btn btn-link p-0 ms-n1 text-danger ia-delete-menu"
                    data-id="{{ $m->id }}"
                    data-name="{{ $m->name }}"
                    title="Delete this menu">
                <i class="fas fa-times" style="font-size:11px;"></i>
            </button>
            @endif
        </li>
        @endforeach
        <li class="nav-item">
            <a class="nav-link fw-semibold menu-tab-inactive"
               href="{{ route('admin.holiday-menu.show') }}">
                <i class="fas fa-map-marked-alt me-2"></i>Holiday Packages
            </a>
        </li>
    </ul>

    {{-- ── Builder Panel ── --}}
    <div class="builder-panel">

        @if($menu->isAutoDisplay())
        <div class="alert alert-info d-flex align-items-center gap-2 mb-0 rounded-0 border-0 border-bottom"
             style="font-size:13px; background:#eff6ff; border-color:#bfdbfe!important;">
            <i class="fas fa-magic text-primary"></i>
            <div>
                <strong>Auto Display Mode:</strong>
                {{ \App\Models\Menu::DISPLAY_MODES[$menu->display_mode] ?? $menu->display_mode }}.
                This menu builds its structure automatically from the Regions / States / Cities database.
                Manual items are <em>hidden while this mode is active</em>.
                <a href="#" id="btnAutoModeEdit" class="ms-2 fw-semibold">Change settings</a>
            </div>
        </div>
        @endif

        {{-- Stats bar ── --}}
        <div class="builder-stats-bar">
            <div class="builder-stat">
                <span class="stat-num" id="statTotal">{{ $stats['total'] }}</span>
                <span class="stat-lbl">Total</span>
            </div>
            <div class="builder-stat">
                <span class="stat-num text-success" id="statActive">{{ $stats['active'] }}</span>
                <span class="stat-lbl">Active</span>
            </div>
            <div class="builder-stat">
                <span class="stat-num text-primary" id="statRoot">{{ $stats['root'] }}</span>
                <span class="stat-lbl">Top-Level</span>
            </div>
            <div class="builder-stat">
                <span class="stat-num text-secondary" id="statNested">{{ $stats['nested'] }}</span>
                <span class="stat-lbl">Nested</span>
            </div>
            <div class="ms-auto d-flex align-items-center gap-2">
                <span class="text-muted" style="font-size:12px;">
                    API slug: <code>{{ $menu->slug }}</code>
                </span>
                <span class="save-indicator" id="saveIndicator">
                    <i class="fas fa-cloud text-success me-1"></i>All saved
                </span>
            </div>
        </div>

        {{-- Tree canvas ── --}}
        <div class="builder-canvas">
            <div id="itemTree" class="item-tree">
                @if($tree->isEmpty())
                <div class="builder-empty" id="emptyNotice">
                    <i class="fas fa-stream"></i>
                    <div class="empty-title">No menu items yet</div>
                    <div class="empty-sub">Click <strong>Add Item</strong> to start building your menu.</div>
                </div>
                @else
                @foreach($tree as $item)
                    @include('admin.menus.partials.item-row', [
                        'item'   => $item,
                        'urlMap' => $urlMap,
                        'depth'  => 0,
                    ])
                @endforeach
                @endif
            </div>
        </div>

    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     ADD / EDIT ITEM MODAL
════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="itemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-lg">

            <div class="modal-header" style="background:#0f172a; color:#fff; border:0;">
                <h5 class="modal-title fw-bold mb-0" id="modalTitle">
                    <i class="fas fa-plus-circle me-2 text-warning"></i>Add Menu Item
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <form id="itemForm" novalidate autocomplete="off">

                    <input type="hidden" id="fldItemId">
                    <input type="hidden" id="fldParentId">

                    <div class="row g-3">

                        {{-- Title --}}
                        <div class="col-sm-8">
                            <label class="form-label fw-semibold required-label">Display Title</label>
                            <input type="text" id="fldTitle" class="form-control form-control-lg"
                                   placeholder="e.g. Holiday Ideas" maxlength="200" autocomplete="off">
                            <div class="invalid-feedback" id="errTitle"></div>
                        </div>

                        {{-- Open in --}}
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Open In</label>
                            <select id="fldTarget" class="form-select form-select-lg">
                                <option value="_self">Same Tab</option>
                                <option value="_blank">New Tab ↗</option>
                            </select>
                        </div>

                        {{-- ─────────────────────────────────────────────────────
                             CONTENT TYPE  (Normal / Mega Menu / Menu Reference)
                        ───────────────────────────────────────────────────────── --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-layer-group me-1 text-warning"></i>
                                Dropdown Content Type
                            </label>
                            <div class="d-flex gap-2" id="contentTypeGroup">
                                <button type="button" class="content-type-btn active" data-ctype="normal">
                                    <i class="fas fa-link d-block mb-1" style="font-size:16px;"></i>
                                    Normal Link
                                </button>
                                <button type="button" class="content-type-btn ctype-mega" data-ctype="mega_menu">
                                    <i class="fas fa-th-large d-block mb-1" style="font-size:16px;"></i>
                                    Mega Menu
                                </button>
                                <button type="button" class="content-type-btn" data-ctype="menu_reference">
                                    <i class="fas fa-layer-group d-block mb-1" style="font-size:16px;"></i>
                                    Menu Reference
                                </button>
                            </div>
                            <div class="form-text">
                                <strong>Normal:</strong> standard link/dropdown &nbsp;·&nbsp;
                                <strong>Mega Menu:</strong> full-width Region→State→City dropdown with optional banner &nbsp;·&nbsp;
                                <strong>Menu Reference:</strong> inline another menu's items
                            </div>
                        </div>

                        {{-- ─────────────────────────────────────────────────────
                             NORMAL — Link Type grid (hidden when Mega / Ref)
                        ───────────────────────────────────────────────────────── --}}
                        <div class="col-12" id="sectionNormalType">
                            <label class="form-label fw-semibold required-label">Link Type</label>
                            <div class="type-grid" id="typeGrid">
                                @foreach(\App\Models\MenuItem::TYPES as $value => $label)
                                @if($value !== 'menu_reference')
                                <button type="button"
                                        class="type-btn {{ $loop->first ? 'active' : '' }}"
                                        data-type="{{ $value }}">
                                    <i class="fas fa-{{ match($value) {
                                        'custom'   => 'link',
                                        'page'     => 'file-alt',
                                        'package'  => 'suitcase-rolling',
                                        'location' => 'map-marker-alt',
                                        'region'   => 'globe-asia',
                                        'state'    => 'map',
                                        'category' => 'tags',
                                        default    => 'circle',
                                    } }} mb-1 d-block" style="font-size:18px;"></i>
                                    {{ $label }}
                                </button>
                                @endif
                                @endforeach
                            </div>
                            <div class="invalid-feedback d-block" id="errType" style="display:none!important;"></div>
                        </div>

                        {{-- Custom URL field --}}
                        <div class="col-12" id="fieldUrl">
                            <label class="form-label fw-semibold required-label">URL</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-link"></i></span>
                                <input type="text" id="fldUrl" class="form-control"
                                       placeholder="/india-holidays  or  https://example.com">
                            </div>
                            <div class="form-text">Relative path (starts with /) or full URL.</div>
                            <div class="invalid-feedback" id="errUrl"></div>
                        </div>

                        {{-- Linked record select (page/package/location/category/menu_reference) --}}
                        <div class="col-12 d-none" id="fieldLinked">
                            <label class="form-label fw-semibold required-label" id="linkedLabel">Select Record</label>
                            <select id="fldLinked" class="form-select form-select-lg">
                                <option value="">— Select —</option>
                            </select>
                            <div class="form-text" id="linkedHint"></div>
                            <div class="invalid-feedback" id="errLinked"></div>
                        </div>

                        {{-- Menu Reference info box --}}
                        <div class="col-12 d-none" id="fieldMenuRefInfo">
                            <div class="alert alert-info py-2 mb-0" style="font-size:13px;">
                                <i class="fas fa-info-circle me-1"></i>
                                The selected menu's items will appear as children of this item.
                                If the referenced menu changes, this item updates automatically — no duplication.
                            </div>
                        </div>

                        {{-- ─────────────────────────────────────────────────────
                             MEGA MENU SETTINGS PANEL
                        ───────────────────────────────────────────────────────── --}}
                        <div class="col-12 d-none" id="megaMenuPanel">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <i class="fas fa-th-large text-purple" style="color:#7c3aed;"></i>
                                <span class="fw-bold" style="font-size:14px; color:#1e1b4b;">Mega Menu Configuration</span>
                                <span class="badge" style="background:#f5f3ff;color:#7c3aed;font-size:10px;">Full-width dropdown</span>
                            </div>

                            {{-- Item URL (mega items still have their own link) --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold" style="font-size:13px;">
                                    Item URL <span class="text-muted fw-normal">(optional — what the label itself links to)</span>
                                </label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text"><i class="fas fa-link"></i></span>
                                    <input type="text" id="fldMegaUrl" class="form-control"
                                           placeholder="/holidays  or  #">
                                </div>
                            </div>

                            {{-- Display Source --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold" style="font-size:13px;">
                                    <i class="fas fa-database me-1 text-warning"></i>Content Source
                                </label>
                                <div class="d-flex gap-2 flex-wrap" id="megaSourceGroup">
                                    <button type="button" class="mega-source-btn active" data-source="auto">
                                        <i class="fas fa-sitemap d-block mb-1" style="font-size:14px;"></i>
                                        Holiday Packages Tree
                                        <div style="font-size:10px;font-weight:400;color:#94a3b8;">Auto-generated</div>
                                    </button>
                                    <button type="button" class="mega-source-btn" data-source="custom_menu">
                                        <i class="fas fa-layer-group d-block mb-1" style="font-size:14px;"></i>
                                        Custom Menu
                                        <div style="font-size:10px;font-weight:400;color:#94a3b8;">Use existing menu</div>
                                    </button>
                                </div>
                            </div>

                            {{-- Auto source: Display Mode --}}
                            <div id="megaAutoSection">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold" style="font-size:13px;">
                                        <i class="fas fa-project-diagram me-1 text-warning"></i>Display Mode
                                    </label>
                                    <div class="d-flex gap-2 flex-wrap" id="megaModeGroup">
                                        @foreach(\App\Models\MenuItem::MEGA_DISPLAY_MODES as $mKey => $mLabel)
                                        <button type="button" class="mega-mode-btn {{ $loop->first ? 'active' : '' }}"
                                                data-mode="{{ $mKey }}">{{ $mLabel }}</button>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Filters --}}
                                <div class="row g-2 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold" style="font-size:12px;">
                                            Limit Regions <span class="text-muted fw-normal">(blank = all)</span>
                                        </label>
                                        <select id="fldMegaRegionIds" class="form-select form-select-sm" multiple style="min-height:80px;">
                                            {{-- loaded via AJAX --}}
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold" style="font-size:12px;">
                                            Limit States <span class="text-muted fw-normal">(blank = all)</span>
                                        </label>
                                        <select id="fldMegaStateIds" class="form-select form-select-sm" multiple style="min-height:80px;">
                                            {{-- loaded via AJAX --}}
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="fldMegaActiveOnly" checked>
                                            <label class="form-check-label" for="fldMegaActiveOnly" style="font-size:12.5px; font-weight:600;">
                                                Active locations only
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="fldMegaPackageOnly">
                                            <label class="form-check-label" for="fldMegaPackageOnly" style="font-size:12.5px; font-weight:600;">
                                                Cities with packages only
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="fldMegaManageCityOnly">
                                            <label class="form-check-label" for="fldMegaManageCityOnly" style="font-size:12.5px; font-weight:600;">
                                                Only states/cities with a City &amp; State Page
                                            </label>
                                        </div>
                                        <div class="form-text" style="font-size:11px;">Uses Manage Cities content — a state shows if it or any of its cities has a page.</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Custom menu source: Menu picker --}}
                            <div id="megaCustomSection" class="d-none mb-3">
                                <label class="form-label fw-semibold" style="font-size:13px;">
                                    Select Menu to Use as Content
                                </label>
                                <select id="fldMegaLinkedMenu" class="form-select">
                                    <option value="">— Select a Menu —</option>
                                </select>
                                <div class="invalid-feedback" id="errMegaLinkedMenu"></div>
                            </div>

                            <hr class="my-3">

                            {{-- Banner (optional) --}}
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-2 cursor-pointer"
                                     id="megaBannerToggle" style="cursor:pointer;">
                                    <i class="fas fa-image text-warning"></i>
                                    <span class="fw-semibold" style="font-size:13px;">Right-Side Banner</span>
                                    <span class="text-muted fw-normal" style="font-size:12px;">(optional)</span>
                                    <i class="fas fa-chevron-down ms-auto" id="megaBannerChevron"
                                       style="font-size:11px; color:#94a3b8; transition:transform .2s;"></i>
                                </div>
                                <div id="megaBannerFields" class="d-none">
                                    <div class="row g-2">
                                        <div class="col-12">
                                            <label class="form-label fw-semibold" style="font-size:12px;">Banner Image URL</label>
                                            <input type="text" id="fldBannerImage" class="form-control form-control-sm"
                                                   placeholder="https://example.com/image.jpg  or  /storage/banners/img.jpg">
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label fw-semibold" style="font-size:12px;">Alt Text</label>
                                            <input type="text" id="fldBannerAlt" class="form-control form-control-sm"
                                                   placeholder="Image description">
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label fw-semibold" style="font-size:12px;">Banner Title</label>
                                            <input type="text" id="fldBannerTitle" class="form-control form-control-sm"
                                                   placeholder="Escape the Heat">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-semibold" style="font-size:12px;">Description</label>
                                            <textarea id="fldBannerDesc" class="form-control form-control-sm" rows="2"
                                                      placeholder="Short promotional text…"></textarea>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label fw-semibold" style="font-size:12px;">CTA Button Text</label>
                                            <input type="text" id="fldBannerCtaText" class="form-control form-control-sm"
                                                   placeholder="Explore Now">
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label fw-semibold" style="font-size:12px;">CTA URL</label>
                                            <input type="text" id="fldBannerCtaUrl" class="form-control form-control-sm"
                                                   placeholder="/holidays">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ─────────────────────────────────────────────────────
                             PARENT / STATUS
                        ───────────────────────────────────────────────────────── --}}
                        <div class="col-sm-6" id="fieldParent">
                            <label class="form-label fw-semibold">Nest Under</label>
                            <select id="fldParentSelect" class="form-select">
                                <option value="">— Top Level (root) —</option>
                            </select>
                            <div class="form-text">Optional: place this item inside another item.</div>
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label fw-semibold d-block">Status</label>
                            <div class="btn-group" role="group">
                                <input type="radio" class="btn-check" name="statusRadio"
                                       id="statusActive" value="1" checked>
                                <label class="btn btn-outline-success" for="statusActive">
                                    <i class="fas fa-eye me-1"></i>Active
                                </label>
                                <input type="radio" class="btn-check" name="statusRadio"
                                       id="statusHidden" value="0">
                                <label class="btn btn-outline-secondary" for="statusHidden">
                                    <i class="fas fa-eye-slash me-1"></i>Hidden
                                </label>
                            </div>
                        </div>

                    </div>
                </form>
            </div>

            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning fw-semibold px-5" id="btnSaveItem">
                    <span class="spinner-border spinner-border-sm me-2 d-none" id="saveSpinner"></span>
                    <span id="btnSaveLabel">Save Item</span>
                </button>
            </div>

        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     NEW MENU MODAL
════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="newMenuModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">

            <div class="modal-header" style="background:#0f172a; color:#fff; border:0;">
                <h5 class="modal-title fw-bold mb-0">
                    <i class="fas fa-plus-circle me-2 text-warning"></i>Create New Menu
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <label class="form-label fw-semibold required-label">Menu Name</label>
                <input type="text" id="fldNewMenuName" class="form-control form-control-lg"
                       placeholder="e.g. Holiday Packages" maxlength="100" autocomplete="off">
                <div class="form-text mt-1">
                    A URL-friendly slug is auto-generated (e.g. <em>holiday-packages</em>).
                    The API will expose this menu under that slug.
                </div>
                <div class="invalid-feedback d-block" id="errNewMenuName"></div>
            </div>

            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning fw-semibold px-5" id="btnSaveNewMenu">
                    <span class="spinner-border spinner-border-sm me-2 d-none" id="newMenuSpinner"></span>
                    Create Menu
                </button>
            </div>

        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     MENU DISPLAY SETTINGS MODAL
════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="menuSettingsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">

            <div class="modal-header" style="background:#0f172a; color:#fff; border:0;">
                <h5 class="modal-title fw-bold mb-0" id="settingsModalTitle">
                    <i class="fas fa-cog me-2 text-warning"></i>Menu Display Settings
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">

                <div class="mb-4">
                    <label class="form-label fw-semibold">Display Mode</label>
                    <div id="displayModeGrid" class="row g-2">
                        @foreach(\App\Models\Menu::DISPLAY_MODES as $modeKey => $modeLabel)
                        <div class="col-sm-6 col-md-4">
                            <label class="display-mode-card d-flex align-items-start gap-2 p-3 border rounded cursor-pointer"
                                   style="cursor:pointer; transition:all .15s;">
                                <input type="radio" name="display_mode" value="{{ $modeKey }}"
                                       class="flex-shrink-0 mt-1 dm-radio">
                                <div>
                                    <div class="fw-semibold" style="font-size:13px;">
                                        <i class="fas fa-{{ match($modeKey) {
                                            'manual'            => 'list-ul',
                                            'region_state_city' => 'sitemap',
                                            'region_state'      => 'globe-asia',
                                            'state_city'        => 'map',
                                            'city_only'         => 'map-marker-alt',
                                            default             => 'bars',
                                        } }} me-1 text-warning"></i>
                                        {{ $modeLabel }}
                                    </div>
                                    <div class="text-muted" style="font-size:11px; margin-top:2px;">
                                        @switch($modeKey)
                                            @case('manual') Use manually added menu items @break
                                            @case('region_state_city') Region › State › Cities tree @break
                                            @case('region_state') Region › State only @break
                                            @case('state_city') State › Cities only @break
                                            @case('city_only') Flat city list @break
                                        @endswitch
                                    </div>
                                </div>
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div id="autoFilters" style="display:none;">
                    <hr class="my-3">
                    <div class="fw-semibold mb-3" style="font-size:13px; color:#64748b;">
                        <i class="fas fa-filter me-1"></i>Filters
                    </div>
                    <div class="row g-3">
                        <div class="col-12" id="filterRegionWrap">
                            <label class="form-label fw-semibold" style="font-size:13px;">
                                Limit to specific Regions
                                <span class="text-muted fw-normal">(leave blank = all regions)</span>
                            </label>
                            <select id="fldRegionIds" class="form-select" multiple style="min-height:90px;"></select>
                        </div>
                        <div class="col-12" id="filterStateWrap">
                            <label class="form-label fw-semibold" style="font-size:13px;">
                                Limit to specific States
                                <span class="text-muted fw-normal">(leave blank = all states)</span>
                            </label>
                            <select id="fldStateIds" class="form-select" multiple style="min-height:90px;"></select>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="fldActiveOnly" checked>
                                <label class="form-check-label fw-semibold" for="fldActiveOnly" style="font-size:13px;">
                                    Active locations only
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="fldPackageOnly">
                                <label class="form-check-label fw-semibold" for="fldPackageOnly" style="font-size:13px;">
                                    Cities with packages only
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="fldManageCityOnly">
                                <label class="form-check-label fw-semibold" for="fldManageCityOnly" style="font-size:13px;">
                                    Only states/cities with a City &amp; State Page
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning fw-semibold px-5" id="btnSaveSettings">
                    <span class="spinner-border spinner-border-sm me-2 d-none" id="settingsSpinner"></span>
                    Save Settings
                </button>
            </div>

        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="{{ asset('js/menu-manager.js') }}"></script>
@endsection

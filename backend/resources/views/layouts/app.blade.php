<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') — Indian Panorama CRM</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Switchery -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/switchery/0.8.2/switchery.min.css" rel="stylesheet">
    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <!-- jQuery UI (Datepicker — used by the shared Image License "Download Date" field) -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <!-- CRM Styles -->
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    <link rel="icon" href="{{ asset('favicon.webp') }}" type="image/x-icon">

    <style>
        /* The wrapper this overlays can be much taller than the viewport (a long
           table) — centering the spinner within the whole overlay would push it
           off-screen below the fold, so the spinner sticks near the top of the
           current viewport instead while the tint still covers the full area. */
        .ajax-loading-overlay {
            position: absolute; inset: 0; z-index: 5;
            background: rgba(255, 255, 255, 0.7); min-height: 80px;
            text-align: center;
        }
        .ajax-loading-overlay > .spinner-border {
            position: sticky;
            top: 40vh;
        }
    </style>

    @stack('style')
</head>

<body class="crm-body">

    @php
    $u    = auth()->check() ? auth()->user() : null;
    $isSA = $u && $u->is_super_admin;
    $can  = fn(string $p) => $u && ($isSA || $u->hasPermission($p));
    $canM = fn(string $m) => $u && ($isSA ||
        $u->hasPermission($m . '.view') ||
        $u->hasPermission($m . '.create') ||
        $u->hasPermission($m . '.edit') ||
        $u->hasPermission($m . '.delete'));
    $r    = fn(string $name) => request()->routeIs($name);
    @endphp

    {{-- Mobile overlay --}}
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    {{-- ════════════════════════════════════════
         SIDEBAR
    ════════════════════════════════════════ --}}
    <aside class="crm-sidebar" id="crmSidebar">

        {{-- Brand --}}
        <div class="sidebar-brand">
            <a href="{{ route('admin.dashboard') }}">
                <div>
                    <div class="sidebar-brand-text">Indian Panorama</div>
                    <div class="sidebar-brand-sub">CRM Dashboard</div>
                </div>
            </a>
            <button class="sidebar-close-btn" id="sidebarClose" title="Close menu">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- User badge --}}
        @auth
        <div class="sidebar-user">
            <div class="sidebar-user-avatar">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
            </div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name">{{ auth()->user()->name ?? 'Admin' }}</div>
                <div class="sidebar-user-role">{{ $isSA ? 'Super Admin' : 'Admin' }}</div>
            </div>
        </div>
        @endauth

        {{-- Scrollable nav --}}
        <nav class="sidebar-nav">

            {{-- ── MAIN ──────────────────────────────────── --}}
            <div class="nav-section-title">Main</div>
            <ul class="nav-list">
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}"
                       class="nav-link {{ $r('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-chart-pie nav-icon"></i>
                        Dashboard
                    </a>
                </li>
                @if($canM('media'))
                <li class="nav-item">
                    <a href="{{ route('admin.media.index') }}"
                       class="nav-link {{ $r('admin.media.*') ? 'active' : '' }}">
                        <i class="fas fa-images nav-icon"></i>
                        Media Library
                    </a>
                </li>
                @endif
                @if($canM('enquiries'))
                <li class="nav-item">
                    <a href="{{ route('admin.enquiries.index') }}"
                       class="nav-link {{ $r('admin.enquiries.*') ? 'active' : '' }}">
                        <i class="fas fa-envelope-open-text nav-icon"></i>
                        Enquiries
                    </a>
                </li>
                @endif
            </ul>

            {{-- ── TOURS & PACKAGES ──────────────────────── --}}
            @if($canM('packages') || $canM('categories'))
            <div class="nav-section-title">Tours &amp; Packages</div>
            <ul class="nav-list">
                @php $pkgOpen = $r('admin.packages.*') || $r('admin.categories.*'); @endphp
                <li class="nav-item">
                    <a class="nav-link {{ $pkgOpen ? 'active' : '' }}"
                       data-bs-toggle="collapse"
                       href="#navPackages"
                       aria-expanded="{{ $pkgOpen ? 'true' : 'false' }}">
                        <i class="fas fa-suitcase-rolling nav-icon"></i>
                        Packages &amp; Tours
                        <i class="fas fa-chevron-right nav-arrow"></i>
                    </a>
                    <div id="navPackages" class="collapse {{ $pkgOpen ? 'show' : '' }}">
                        <ul class="nav-submenu">
                            @if($canM('packages'))
                            <li>
                                <a href="{{ route('admin.packages.index') }}"
                                   class="{{ $r('admin.packages.*') ? 'active' : '' }}">
                                    All Packages
                                </a>
                            </li>
                            @endif
                            @if($canM('categories'))
                            <li>
                                <a href="{{ route('admin.categories.index') }}"
                                   class="{{ $r('admin.categories.*') ? 'active' : '' }}">
                                    Categories
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>
            </ul>
            @endif

            {{-- ── DESTINATIONS ──────────────────────────── --}}
            @if($canM('locations') || $canM('city-pages') || $canM('festival') || $canM('experiences') || $canM('experience-pages') || $canM('experience-subcategories') || $canM('experience-categories') || $canM('tourist-attractions') || $canM('tourist-activities'))
            <div class="nav-section-title">Destinations</div>
            <ul class="nav-list">

                {{-- Location Setting --}}
                @if($canM('locations'))
                <li class="nav-item">
                    <a href="{{ route('admin.location-setting.index') }}"
                       class="nav-link {{ $r('admin.location-setting.*') || $r('admin.locations.*') ? 'active' : '' }}">
                        <i class="fas fa-map-marked-alt nav-icon"></i>
                        Packages Location
                    </a>
                </li>
                @endif

                {{-- Root Page Setting --}}
                @if($canM('city-pages'))
                <li class="nav-item">
                    <a href="{{ route('admin.city-pages.index') }}"
                       class="nav-link {{ $r('admin.city-pages.*') ? 'active' : '' }}">
                        <i class="fas fa-file-alt nav-icon"></i>
                        City Guide
                    </a>
                </li>
                @endif

                @if($canM('festival'))
                <li class="nav-item">
                    <a href="{{ route('admin.festival.index') }}"
                       class="nav-link {{ $r('admin.festival.*') ? 'active' : '' }}">
                        <i class="fas fa-drum nav-icon"></i>
                        Festival
                    </a>
                </li>
                @endif

                @if($canM('experiences') || $canM('experience-pages') || $canM('experience-subcategories') || $canM('experience-categories'))
                <li class="nav-item">
                    <a href="{{ route('admin.experience-categories.index') }}"
                       class="nav-link {{ $r('admin.experience-categories.*') || $r('admin.experience-subcategories.*') || $r('admin.experiences.*') || $r('admin.experience-pages.*') ? 'active' : '' }}">
                        <i class="fas fa-compass nav-icon"></i>
                        Experiences
                    </a>
                </li>
                @endif

                @if($canM('tourist-attractions'))
                <li class="nav-item">
                    <a href="{{ route('admin.tourist-attractions.index') }}"
                       class="nav-link {{ $r('admin.tourist-attractions.*') ? 'active' : '' }}">
                        <i class="fas fa-mountain-sun nav-icon"></i>
                        Attraction
                    </a>
                </li>
                @endif

                @if($canM('tourist-activities'))
                <li class="nav-item">
                    <a href="{{ route('admin.tourist-activities.index') }}"
                       class="nav-link {{ $r('admin.tourist-activities.*') ? 'active' : '' }}">
                        <i class="fas fa-person-hiking nav-icon"></i>
                        Activity
                    </a>
                </li>
                @endif

            </ul>
            @endif

            {{-- ── CMS CONTENT ───────────────────────────── --}}
            @if($canM('cms-pages') || $canM('news') || $canM('branches'))
            <div class="nav-section-title">CMS Content</div>
            <ul class="nav-list">
                @if($canM('cms-pages'))
                @php $cmsOpen = $r('admin.cms-builder.*'); @endphp
                <li class="nav-item">
                    <a class="nav-link {{ $cmsOpen ? 'active' : '' }}"
                       data-bs-toggle="collapse"
                       href="#navCms"
                       aria-expanded="{{ $cmsOpen ? 'true' : 'false' }}">
                        <i class="fas fa-layer-group nav-icon"></i>
                        CMS Page Settings
                        <i class="fas fa-chevron-right nav-arrow"></i>
                    </a>
                    <div id="navCms" class="collapse {{ $cmsOpen ? 'show' : '' }}">
                        <ul class="nav-submenu">
                            <li>
                                <a href="{{ route('admin.cms-builder.index') }}"
                                   class="{{ $r('admin.cms-builder.index') ? 'active' : '' }}">
                                    All Pages
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.cms-builder.create') }}"
                                   class="{{ $r('admin.cms-builder.create') ? 'active' : '' }}">
                                    New Page
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif

                @if($canM('news'))
                <li class="nav-item">
                    <a href="{{ route('admin.news.index') }}"
                       class="nav-link {{ $r('admin.news.*') ? 'active' : '' }}">
                        <i class="fas fa-newspaper nav-icon"></i>
                        News
                    </a>
                </li>
                @endif

                @if($canM('branches'))
                <li class="nav-item">
                    <a href="{{ route('admin.branches.index') }}"
                       class="nav-link {{ $r('admin.branches.*') ? 'active' : '' }}">
                        <i class="fas fa-building nav-icon"></i>
                        Branches Address
                    </a>
                </li>
                @endif
            </ul>
            @endif

            {{-- ── TRAVEL SERVICES ───────────────────────── --}}
            @if($canM('cars'))
            <div class="nav-section-title">Travel Services</div>
            <ul class="nav-list">
                @php $carOpen = $r('admin.cars.*') || $r('admin.car-categories.*') || $r('admin.car-routes.*') || $r('admin.car-city.*'); @endphp
                <li class="nav-item">
                    <a class="nav-link {{ $carOpen ? 'active' : '' }}"
                       data-bs-toggle="collapse"
                       href="#navCars"
                       aria-expanded="{{ $carOpen ? 'true' : 'false' }}">
                        <i class="fas fa-car nav-icon"></i>
                        Cars
                        <i class="fas fa-chevron-right nav-arrow"></i>
                    </a>
                    <div id="navCars" class="collapse {{ $carOpen ? 'show' : '' }}">
                        <ul class="nav-submenu">
                            <li>
                                <a href="{{ route('admin.cars.index') }}"
                                   class="{{ $r('admin.cars.index') || $r('admin.cars.create') || $r('admin.cars.edit') ? 'active' : '' }}">
                                    All Cars
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.car-categories.index') }}"
                                   class="{{ $r('admin.car-categories.*') ? 'active' : '' }}">
                                    Car Categories
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.car-routes.index') }}"
                                   class="{{ $r('admin.car-routes.*') ? 'active' : '' }}">
                                    Car Routes
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.car-city.index') }}"
                                   class="{{ $r('admin.car-city.*') ? 'active' : '' }}">
                                    Car Cities
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
            @endif

            {{-- ── MENU & PAGE SETTINGS ──────────────────── --}}
            @if($isSA || $canM('page-settings'))
            <div class="nav-section-title">Menu &amp; Settings</div>
            <ul class="nav-list">

                @if($isSA || $canM('page-settings'))
                <li class="nav-item">
                    <a href="{{ route('admin.menus.index') }}"
                       class="nav-link {{ $r('admin.menus.*') || $r('admin.menu-items.*') || $r('admin.holiday-menu.*') ? 'active' : '' }}">
                        <i class="fas fa-bars nav-icon"></i>
                        Menu Management
                    </a>
                </li>
                @endif

                @if($canM('page-settings') || $canM('cars'))
                @php $pgOpen = $r('admin.page-settings.*') || $r('admin.holiday-setting.*') || $r('admin.banners.*'); @endphp
                <li class="nav-item">
                    <a class="nav-link {{ $pgOpen ? 'active' : '' }}"
                       data-bs-toggle="collapse"
                       href="#navPageSettings"
                       aria-expanded="{{ $pgOpen ? 'true' : 'false' }}">
                        <i class="fas fa-sliders-h nav-icon"></i>
                        Page Settings
                        <i class="fas fa-chevron-right nav-arrow"></i>
                    </a>
                    <div id="navPageSettings" class="collapse {{ $pgOpen ? 'show' : '' }}">
                        <ul class="nav-submenu">
                            @if($canM('page-settings'))
                            <li>
                                <a href="{{ route('admin.page-settings.home') }}"
                                   class="{{ $r('admin.page-settings.home') ? 'active' : '' }}">
                                    Home Page
                                </a>
                            </li>
                            @endif
                            @if($canM('cars'))
                            <li>
                                <a href="{{ route('admin.page-settings.car') }}"
                                   class="{{ $r('admin.page-settings.car') ? 'active' : '' }}">
                                    Car Page
                                </a>
                            </li>
                            @endif
                            @if($canM('page-settings'))
                            <li>
                                <a href="{{ route('admin.holiday-setting.index') }}"
                                   class="{{ $r('admin.holiday-setting.*') ? 'active' : '' }}">
                                    Holiday Settings
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.banners.index') }}"
                                   class="{{ $r('admin.banners.*') ? 'active' : '' }}">
                                    Banners
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>
                @endif

            </ul>
            @endif

            {{-- ── TEAM & AWARDS ─────────────────────────── --}}
            {{-- Teams/Awards/Partners routes are folded into the "page-settings"
                 permission module (see config/admin_permissions.php _aliases),
                 same as the middleware that actually guards these pages — so the
                 sidebar must check that module too, not standalone slugs that
                 can never be granted. --}}
            @if($canM('departments') || $canM('page-settings'))
            <div class="nav-section-title">Team &amp; Awards</div>
            <ul class="nav-list">
                @if($canM('departments'))
                <li class="nav-item">
                    <a href="{{ route('admin.departments.index') }}"
                       class="nav-link {{ $r('admin.departments.*') ? 'active' : '' }}">
                        <i class="fas fa-sitemap nav-icon"></i>
                        Departments
                    </a>
                </li>
                @endif
                @if($canM('page-settings'))
                <li class="nav-item">
                    <a href="{{ route('admin.teams.index') }}"
                       class="nav-link {{ $r('admin.teams.*') ? 'active' : '' }}">
                        <i class="fas fa-users nav-icon"></i>
                        Our Team
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.awards.index') }}"
                       class="nav-link {{ $r('admin.awards.*') ? 'active' : '' }}">
                        <i class="fas fa-trophy nav-icon"></i>
                        Awards &amp; Achievements
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.partners.index') }}"
                       class="nav-link {{ $r('admin.partners.*') ? 'active' : '' }}">
                        <i class="fas fa-handshake nav-icon"></i>
                        Partners
                    </a>
                </li>
                @endif
            </ul>
            @endif

            {{-- ── SYSTEM ────────────────────────────────── --}}
            @if($can('admin.manage') || $isSA)
            <div class="nav-section-title">System</div>
            <ul class="nav-list">
                @if($can('admin.manage'))
                <li class="nav-item">
                    <a href="{{ route('admin.admin-management.index') }}"
                       class="nav-link {{ $r('admin.admin-management.*') ? 'active' : '' }}">
                        <i class="fas fa-users-cog nav-icon"></i>
                        Admin Management
                    </a>
                </li>
                @endif
                <li class="nav-item">
                    <a href="{{ route('admin.activity-logs.index') }}"
                       class="nav-link {{ $r('admin.activity-logs.*') ? 'active' : '' }}">
                        <i class="fas fa-history nav-icon"></i>
                        Activity Logs
                    </a>
                </li>
                @if($can('sitemap.manage'))
                <li class="nav-item">
                    <a href="{{ route('admin.sitemap.index') }}"
                       class="nav-link {{ $r('admin.sitemap.*') ? 'active' : '' }}">
                        <i class="fas fa-sitemap nav-icon"></i>
                        Sitemap
                    </a>
                </li>
                @endif
            </ul>
            @endif

        </nav>{{-- end .sidebar-nav --}}

        {{-- Logout --}}
        <div class="sidebar-bottom">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit">
                    <i class="fas fa-sign-out-alt nav-icon"></i>
                    Sign Out
                </button>
            </form>
        </div>

    </aside>{{-- end .crm-sidebar --}}

    {{-- ════════════════════════════════════════
         MAIN WRAPPER
    ════════════════════════════════════════ --}}
    <div class="crm-main" id="crmMain">

        {{-- Top header --}}
        <header class="crm-header">
            <div class="header-left">
                <button class="sidebar-toggle-btn" id="sidebarToggle" title="Toggle menu">
                    <i class="fas fa-bars"></i>
                </button>
                <span class="crm-page-title">@yield('title')</span>
            </div>
            <div class="header-right">
                @auth
                <div class="dropdown">
                    <button class="header-user-btn dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="header-avatar">
                            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <span class="d-none d-sm-inline">{{ auth()->user()->name ?? 'Admin' }}</span>
                        <i class="fas fa-chevron-down" style="font-size:10px;opacity:.6;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" style="min-width:200px;">
                        <li>
                            <div style="padding:10px 14px 8px;border-bottom:1px solid #f1f5f9;margin-bottom:4px;">
                                <div style="font-weight:600;font-size:13px;color:#0f172a;">{{ auth()->user()->name }}</div>
                                <div style="font-size:11px;color:#94a3b8;">{{ auth()->user()->email }}</div>
                            </div>
                        </li>
                        @if($can('admin.manage'))
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.admin-management.index') }}">
                                <i class="fas fa-cog me-2"></i>Settings
                            </a>
                        </li>
                        @endif
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt me-2"></i>Sign Out
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
                @endauth
            </div>
        </header>

        {{-- Flash Messages --}}
        @if(session('success') || session('error') || session('warning') || session('info'))
        <div class="px-4 pt-3">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
        </div>
        @endif

        {{-- jQuery + Bootstrap load here, before page content, so inline scripts embedded
             in @yield('content')/@yield('modal') (e.g. the <x-image-license-fields>
             component's @once script) can rely on $ and bootstrap.* being ready —
             loading them at the bottom of the page ran them too late and silently
             broke window.validateImageLicenseBlocks() and friends on every page. --}}
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
        <script>
            // jQuery UI's datepicker positions itself by walking offsetParent/offsetTop
            // (_findPos), which never accounts for scroll on an intermediate scrollable
            // ancestor — e.g. a Bootstrap modal body with overflow-y:auto. Opening a date
            // field inside a scrolled modal then renders the calendar far from the field
            // (sometimes below the modal entirely). Recomputing from the field's actual
            // getBoundingClientRect() fixes it everywhere at once, for every datepicker
            // on the site, without touching each page's own init code.
            if (window.jQuery && $.datepicker) {
                $.datepicker.setDefaults({
                    beforeShow: function(input, inst) {
                        setTimeout(function() {
                            var rect = input.getBoundingClientRect();
                            inst.dpDiv.css({
                                top: (rect.bottom + window.scrollY) + 'px',
                                left: (rect.left + window.scrollX) + 'px'
                            });
                        }, 0);
                    }
                });
            }
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        {{-- Page Content --}}
        <main class="crm-content">
            @yield('content')
        </main>

    </div>{{-- end .crm-main --}}

    {{-- Modals slot --}}
    @yield('modal')

    {{-- ════════════════════════════════════════
         SCRIPTS
    ════════════════════════════════════════ --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/switchery/0.8.2/switchery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.10.0/tinymce.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        // ── First actual error message out of a Laravel-style {field: [msg,...]} or
        //    {field: "msg"} errors object — used instead of checking one hardcoded
        //    field name, so any validation error (incl. license fields) is shown
        //    to the admin instead of a generic/blank message.
        window.firstErrorMessage = function (errors, fallback) {
            if (!errors) return fallback || 'Validation failed.';
            var keys = Object.keys(errors);
            if (!keys.length) return fallback || 'Validation failed.';
            var val = errors[keys[0]];
            return Array.isArray(val) ? val[0] : val;
        };

        // ── Apply server-side validation errors to image-license-fields blocks
        //    inline (red text right under the failing input, matching Laravel's own
        //    inline error style) instead of a toast, and auto-expand the collapsed
        //    block so the admin actually sees it. Pass `scope` (a selector/element)
        //    to restrict to one form when the same field name (e.g. "banner_license")
        //    appears in more than one modal on the page. Returns true if it found and
        //    displayed at least one license-field error, so callers know whether
        //    anything else (a plain toastr) still needs to be shown for non-license fields.
        window.applyLicenseServerErrors = function (errors, scope) {
            if (!errors) return false;
            var subfieldClass = {
                source_of_image: 'license-source',
                download_date:   'license-download-date',
                account_id:      'license-account',
                license_key:     'license-key-input',
                license_file:    'license-file',
            };
            var handledAny = false;
            var $blocks = scope ? $(scope).find('.image-license-block') : $('.image-license-block');

            $blocks.each(function () {
                var $block = $(this);
                var prefix = $block.data('field-name');
                if (!prefix) return;
                var blockHasError = false;

                Object.keys(subfieldClass).forEach(function (sub) {
                    var $input = $block.find('.' + subfieldClass[sub]).first();
                    $input.removeClass('is-invalid');
                    $input.next('.invalid-feedback').remove();

                    var msg = errors[prefix + '.' + sub];
                    if (!msg) return;
                    msg = Array.isArray(msg) ? msg[0] : msg;
                    $input.addClass('is-invalid').after('<div class="invalid-feedback d-block">' + msg + '</div>');
                    blockHasError = true;
                    handledAny = true;
                });

                if (blockHasError) {
                    var $collapse = $block.find('.collapse').first();
                    if ($collapse.length && typeof bootstrap !== 'undefined') {
                        bootstrap.Collapse.getOrCreateInstance($collapse[0], { toggle: false }).show();
                    }
                    setTimeout(function () {
                        $('html,body').animate({ scrollTop: $block.offset().top - 120 }, 400);
                    }, 150);
                }
            });

            return handledAny;
        };

        // ── One-stop error display for an AJAX form's .fail() handler: shows
        //    license-field errors inline via applyLicenseServerErrors() above, and
        //    falls back to a toast only for whatever isn't a license field.
        //    opts: { scope, fallback }
        window.showFormErrors = function (errors, opts) {
            opts = opts || {};
            if (!errors) { toastr.error(opts.fallback || 'Validation failed.'); return; }

            var handledLicense = window.applyLicenseServerErrors(errors, opts.scope);
            var licenseSuffixes = /\.(source_of_image|download_date|account_id|license_key|license_file)$/;
            var remainingKey = Object.keys(errors).find(function (k) { return !licenseSuffixes.test(k); });

            if (remainingKey) {
                var msg = errors[remainingKey];
                toastr.error(Array.isArray(msg) ? msg[0] : msg);
            } else if (!handledLicense) {
                toastr.error(opts.fallback || 'Validation failed.');
            }
        };

        // ── Sidebar toggle (mobile) ──
        (function () {
            var sidebar  = document.getElementById('crmSidebar');
            var overlay  = document.getElementById('sidebarOverlay');
            var toggle   = document.getElementById('sidebarToggle');
            var closeBtn = document.getElementById('sidebarClose');

            function openSidebar() {
                if (sidebar)  sidebar.classList.add('open');
                if (overlay)  overlay.classList.add('visible');
                document.body.style.overflow = 'hidden';
            }
            function closeSidebar() {
                if (sidebar)  sidebar.classList.remove('open');
                if (overlay)  overlay.classList.remove('visible');
                document.body.style.overflow = '';
            }

            if (toggle)   toggle.addEventListener('click', openSidebar);
            if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
            if (overlay)  overlay.addEventListener('click', closeSidebar);

            // Close on Escape key
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') closeSidebar();
            });
        })();

        // ── Toastr defaults ──
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: 'toast-top-right',
            timeOut: '3500'
        };

        // ── AJAX CSRF ──
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        // ── Shared AJAX loading overlay ──────────────────────────────────────
        // For list/table wrappers refreshed via $.get(...).done(html => wrapper.html(...)):
        // call showAjaxLoader($wrapper) right before the request, hideAjaxLoader($wrapper)
        // in .fail() (the .done() success path replaces the wrapper's content, which
        // removes the overlay along with it — no explicit hide needed there).
        window.showAjaxLoader = function ($wrapper) {
            if (!$wrapper || !$wrapper.length || $wrapper.children('.ajax-loading-overlay').length) return;
            if ($wrapper.css('position') === 'static') $wrapper.css('position', 'relative');
            $wrapper.append(
                '<div class="ajax-loading-overlay">' +
                    '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading…</span></div>' +
                '</div>'
            );
        };
        window.hideAjaxLoader = function ($wrapper) {
            if ($wrapper && $wrapper.length) $wrapper.children('.ajax-loading-overlay').remove();
        };

        // ── Switchery init ──
        function initSwitchery() {
            document.querySelectorAll('.js-switch').forEach(function (elem) {
                if (!elem.classList.contains('switchery-initialized')) {
                    new Switchery(elem, { size: 'small' });
                    elem.classList.add('switchery-initialized');
                }
            });
        }

        // ── S3 Base URL ──
        const s3BaseUrl = "{{ storage_base_url() }}";

        document.addEventListener('DOMContentLoaded', function () {
            initSwitchery();

            document.addEventListener('submit', function (e) {
                const form = e.target;
                if (form && form.tagName && form.tagName.toLowerCase() === 'form') {
                    form.classList.add('submitted');
                }
            }, true);

            document.addEventListener('invalid', function (e) {
                const field = e.target;
                const form = field && (field.form || (field.closest && field.closest('form')));
                if (form) form.classList.add('submitted');
            }, true);
        });

        // ── Input / paste character limits ──
        (function () {
            'use strict';
            const MAX_LENGTH   = 750;
            const MAX_WORD_LEN = 10000;
            const WARNING_TIME = 2000;

            function showWarning(el, msg) {
                try {
                    if (!el) el = document.body;
                    if (el.dataset && el.dataset.warned) return;
                    if (el.dataset) el.dataset.warned = '1';
                    const warn = document.createElement('div');
                    warn.textContent = msg;
                    Object.assign(warn.style, {
                        position:'fixed', bottom:'20px', right:'20px',
                        background:'#ef4444', color:'#fff', padding:'8px 14px',
                        borderRadius:'8px', zIndex:2147483647, fontSize:'13px',
                        boxShadow:'0 4px 12px rgba(0,0,0,0.15)'
                    });
                    document.body.appendChild(warn);
                    setTimeout(() => { warn.remove(); if (el.dataset) delete el.dataset.warned; }, WARNING_TIME);
                } catch (err) { console.warn('showWarning err', err); }
            }

            function isInsideTinyDialog(el) {
                return !!(el && el.closest && (el.closest('.tox-dialog') || el.closest('.tox-tinymce') || el.closest('.tox-tinymce-aux')));
            }

            function enforceCharLimit(e) {
                const el = e.target;
                if (!el || isInsideTinyDialog(el)) return;
                if (el.classList && el.classList.contains('tox-textarea')) return;
                if (el.tagName !== 'INPUT' && el.tagName !== 'TEXTAREA') return;
                if (el.tagName === 'INPUT' && el.type !== 'text') return;
                let val = el.value || '';
                if (el.tagName === 'TEXTAREA') {
                    const words = [...val.matchAll(/\S+/g)];
                    if (words.length > MAX_WORD_LEN) {
                        const last = words[MAX_WORD_LEN - 1];
                        el.value = val.substring(0, last.index + last[0].length);
                        showWarning(el, `Max ${MAX_WORD_LEN} words allowed.`);
                    }
                } else {
                    if (val.length > MAX_LENGTH) {
                        el.value = val.substring(0, MAX_LENGTH);
                        showWarning(el, `Max ${MAX_LENGTH} characters allowed.`);
                    }
                }
            }

            document.addEventListener('input', enforceCharLimit, true);
            document.addEventListener('paste', function (e) {
                const el = e.target;
                if (!el || isInsideTinyDialog(el)) return;
                setTimeout(() => enforceCharLimit({ target: el }), 10);
            }, true);
        })();

        // ── TinyMCE global loader ──
        (function () {
            'use strict';
            const WORD_LIMIT = 10000;

            function isInitialized($t) {
                try {
                    if (!$t || !$t.length) return false;
                    const el = $t[0];
                    if (el.dataset && el.dataset.tinyInit === '1') return true;
                    const id = $t.attr('id');
                    if (id && tinymce.get(id)) return true;
                    return false;
                } catch (e) { return false; }
            }

            function markInitialized($t) {
                try { $t.attr('data-tiny-init', '1'); $t[0].dataset.tinyInit = '1'; } catch (e) {}
            }

            function showWarning(el, msg) {
                const w = document.createElement('div');
                w.textContent = msg;
                Object.assign(w.style, { position:'fixed', bottom:'20px', right:'20px', background:'#ef4444', color:'#fff', padding:'8px 14px', borderRadius:'8px', zIndex:2147483647, fontSize:'13px' });
                document.body.appendChild(w);
                setTimeout(() => w.remove(), 2000);
            }

            function initTinyMCEOn($t) {
                if (!$t || !$t.length || isInitialized($t)) return;
                if ($t.closest('#sectionTemplates').length) return;
                if (!$t.attr('id')) $t.attr('id', 'tinymce_' + Math.random().toString(36).substr(2, 9));
                const id = $t.attr('id');
                try { tinymce.get(id) && tinymce.remove('#' + id); } catch (e) {}

                tinymce.init({
                    selector: '#' + id,
                    height: 600,
                    menubar: true,
                    statusbar: true,
                    branding: false,
                    valid_elements: '*[*]',
                    extended_valid_elements: '*[*]',
                    valid_children: '+*[*]',
                    verify_html: false,
                    cleanup: false,
                    forced_root_block: false,
                    plugins: ['advlist','autolink','lists','charmap','print','preview','hr','pagebreak','searchreplace','wordcount','visualblocks','visualchars','fullscreen','insertdatetime','media','nonbreaking','save','table','directionality','emoticons','template','paste','textpattern','code','image','link'].join(' '),
                    toolbar: 'undo redo | styles | bold italic underline | bullist numlist | alignleft aligncenter alignright alignjustify | image media link | code fullscreen',
                    images_upload_url: "{{ route('admin.upload-image') }}",
                    automatic_uploads: true,
                    headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                    file_picker_types: 'image',
                    file_picker_callback: function (cb, value, meta) {
                        if (meta.filetype === 'image') {
                            const input = document.createElement('input');
                            input.setAttribute('type', 'file');
                            input.setAttribute('accept', 'image/*');
                            input.onchange = function () {
                                const file = this.files[0];
                                const reader = new FileReader();
                                reader.onload = function () {
                                    const id = 'blobid' + (new Date()).getTime();
                                    const bc = tinymce.activeEditor && tinymce.activeEditor.editorUpload && tinymce.activeEditor.editorUpload.blobCache;
                                    if (!bc) { cb(reader.result); return; }
                                    const base64 = reader.result.split(',')[1];
                                    const bi = bc.create(id, file, base64);
                                    bc.add(bi);
                                    cb(bi.blobUri(), { title: file.name });
                                };
                                reader.readAsDataURL(file);
                            };
                            input.click();
                        }
                    },
                    setup: function (editor) {
                        editor.on('change', function () { editor.save(); });
                        const enforceWordLimit = function () {
                            try {
                                const text = editor.getContent({ format: 'text' }).trim();
                                const words = text.split(/\s+/).filter(w => w.length > 0);
                                if (words.length > WORD_LIMIT) {
                                    showWarning(editor.getElement(), `Max ${WORD_LIMIT} words allowed.`);
                                    editor.undoManager && editor.undoManager.undo();
                                }
                            } catch (e) {}
                        };
                        editor.on('input', enforceWordLimit);
                        editor.on('paste', function () { setTimeout(enforceWordLimit, 10); });
                        editor.on('init', function () {
                            try {
                                const container = editor.getContainer();
                                const modal = container && container.closest && container.closest('.modal');
                                if (modal) {
                                    try { if (modal.getAttribute('tabindex') === '-1') modal.setAttribute('tabindex', ''); } catch (e) {}
                                    const handler = function (ev) {
                                        if (ev.target && ev.target.closest && (ev.target.closest('.tox-tinymce') || ev.target.closest('.tox-tinymce-aux') || ev.target.closest('.tox-dialog'))) {
                                            ev.stopImmediatePropagation();
                                        }
                                    };
                                    modal.addEventListener('focusin', handler, true);
                                    try { modal._tiny_focusin_handler = handler; } catch (e) {}
                                }
                                if (!window.__tiny_global_focusin_installed) {
                                    window.__tiny_global_focusin_installed = true;
                                    document.addEventListener('focusin', function (ev) {
                                        if (ev.target && ev.target.closest && (ev.target.closest('.tox-tinymce') || ev.target.closest('.tox-tinymce-aux') || ev.target.closest('.tox-dialog'))) {
                                            ev.stopImmediatePropagation();
                                        }
                                    }, true);
                                }
                            } catch (e) {}
                        });
                    }
                });
                markInitialized($t);
            }

            window.initTinyMCEOn = initTinyMCEOn;

            function initAllExisting() {
                $('textarea.tinymce').each(function () { initTinyMCEOn($(this)); });
            }

            $(document).ready(function () { initAllExisting(); });

            $(document).on('shown.bs.modal', function (ev) {
                try {
                    const modal = $(ev.target);
                    try { const el = modal.get(0); if (el && el.getAttribute && el.getAttribute('tabindex') === '-1') el.setAttribute('tabindex', ''); } catch (e) {}
                    modal.find('textarea.tinymce').each(function () {
                        const $t = $(this);
                        const id = $t.attr('id');
                        if (id && tinymce.get(id)) { try { tinymce.remove('#' + id); } catch (e) {} }
                        initTinyMCEOn($t);
                    });
                    const styleId = 'tiny-zindex-fix';
                    if (!document.getElementById(styleId)) {
                        const s = document.createElement('style');
                        s.id = styleId;
                        s.textContent = '.tox .tox-dialog, .tox-tinymce-aux { z-index: 2147483646 !important; }';
                        document.head.appendChild(s);
                    }
                } catch (e) {}
            });

            $(document).on('hidden.bs.modal', function (ev) {
                try {
                    const modal = $(ev.target);
                    modal.find('textarea.tinymce').each(function () {
                        const $t = $(this);
                        const id = $t.attr('id');
                        if (id && tinymce.get(id)) { try { tinymce.remove('#' + id); } catch (e) {} }
                        try { $t.removeAttr('data-tiny-init'); $t[0] && delete $t[0].dataset.tinyInit; } catch (e) {}
                    });
                    try { const el = $(ev.target).get(0); if (el && el._tiny_focusin_handler) { el.removeEventListener('focusin', el._tiny_focusin_handler, true); delete el._tiny_focusin_handler; } } catch (e) {}
                } catch (e) {}
            });

            const observer = new MutationObserver(function (mutations) {
                mutations.forEach(function (m) {
                    m.addedNodes.forEach(function (n) {
                        if (n.nodeType !== 1) return;
                        const $node = $(n);
                        if ($node.is('textarea.tinymce')) initTinyMCEOn($node);
                        $node.find('textarea.tinymce').each(function () { initTinyMCEOn($(this)); });
                    });
                });
            });
            observer.observe(document.body, { childList: true, subtree: true });
        })();
    </script>

    {{-- ── Global Real-Time Slug Uniqueness Checker ── --}}
    <script>
    (function () {
        'use strict';
        const _CHECK_URL = '{{ route("admin.slug.check") }}';
        const _timers    = {};

        function makeSlug(text) {
            return (text || '').toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .replace(/^-+|-+$/g, '');
        }

        function _fb(el) {
            if (el._scFb) return el._scFb;
            const d = document.createElement('div');
            d.className = 'slug-check-msg small mt-1';
            d.style.display = 'none';
            el.parentNode.insertBefore(d, el.nextSibling);
            return (el._scFb = d);
        }

        function _showError(el, msg) {
            if (!el) return;
            const fb = _fb(el);
            el.classList.remove('is-valid'); el.classList.add('is-invalid');
            fb.innerHTML = '<i class="fas fa-exclamation-circle me-1 text-danger"></i><span class="text-danger">' + msg + '</span>';
            fb.style.display = '';
        }

        function _showOk(el) {
            if (!el) return;
            const fb = _fb(el);
            el.classList.remove('is-invalid'); el.classList.add('is-valid');
            fb.innerHTML = '<i class="fas fa-check-circle me-1 text-success"></i><span class="text-success">Slug is available.</span>';
            fb.style.display = '';
        }

        function _showSpinner(el) {
            if (!el) return;
            const fb = _fb(el);
            fb.innerHTML = '<i class="fas fa-spinner fa-spin me-1 text-muted"></i><span class="text-muted">Checking…</span>';
            fb.style.display = '';
        }

        function _clear(el) {
            if (!el) return;
            el.classList.remove('is-invalid', 'is-valid');
            const fb = _fb(el); fb.innerHTML = ''; fb.style.display = 'none';
        }

        function _setBtns(sel, disabled) {
            if (!sel) return;
            document.querySelectorAll(sel).forEach(function (b) { b.disabled = disabled; });
        }

        function runCheck(slug, type, excludeId, btnSel, inputEl) {
            const key = type + '|' + (excludeId || 0);
            clearTimeout(_timers[key]);
            if (!slug) { _clear(inputEl); _setBtns(btnSel, false); return; }
            _showSpinner(inputEl);
            _timers[key] = setTimeout(function () {
                var url = _CHECK_URL + '?type=' + encodeURIComponent(type) + '&slug=' + encodeURIComponent(slug) + '&exclude=' + encodeURIComponent(excludeId || 0);
                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        if (data.exists) { _showError(inputEl, 'Slug "' + slug + '" is already in use — try a different name.'); _setBtns(btnSel, true); }
                        else { _showOk(inputEl); _setBtns(btnSel, false); }
                    })
                    .catch(function () { _clear(inputEl); _setBtns(btnSel, false); });
            }, 450);
        }

        document.addEventListener('input', function (e) {
            var el = e.target;
            if (!el || !el.dataset || !el.dataset.slugCheck) return;
            var type    = el.dataset.slugCheck;
            var exclude = parseInt(el.dataset.slugExclude || 0, 10);
            var btnSel  = el.dataset.slugSubmit || null;
            var suffix  = el.dataset.slugSuffix  || '';
            var value   = (el.value || '').trim();
            var slug    = value ? makeSlug(value + (suffix ? ' ' + suffix : '')) : '';
            runCheck(slug, type, exclude, btnSel, el);
        });

        window.SlugChecker = { runCheck: runCheck, makeSlug: makeSlug };
    })();
    </script>

    @yield('scripts')
</body>

</html>

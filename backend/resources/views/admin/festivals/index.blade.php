@section('title', 'Manage Festival')
@extends('layouts.app')

@push('style')
<style>
    .btn-outline-purple {
        color: #6d28d9;
        background-color: transparent;
        border: 1px solid #7c3aed;
    }
    .btn-outline-purple:hover {
        color: #fff;
        background-color: #7c3aed;
        border-color: #7c3aed;
    }
    .modal-header-purple {
        background: linear-gradient(135deg, #8b5cf6, #6d28d9);
        color: #fff;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-0 fw-bold"><i class="fas fa-drum me-2 text-primary"></i>Manage Festival</h2>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.festival.setting.index') }}" class="btn btn-outline-purple">
                <i class="fas fa-drum me-2"></i>Festival Setting
            </a>
            <a href="{{ route('admin.festival-state-pages.index') }}" class="btn btn-outline-success">
                <i class="fas fa-map-marked-alt me-2"></i>Festivals By State
            </a>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFestivalModal">
                <i class="fas fa-plus me-2"></i>Add Festival
            </button>
        </div>
    </div>

    <div class="mb-3">
        <div class="position-relative" style="max-width:320px;">
            <i class="fas fa-search position-absolute" style="left:12px; top:50%; transform:translateY(-50%); color:#94a3b8; font-size:.85rem;"></i>
            <input type="text" id="fSearch" class="form-control" style="padding-left:32px;"
                   placeholder="Search festival..." value="{{ request('search') }}">
        </div>
    </div>

    <div class="tab-list mb-3">
        <ul>
            <li><a href="javascript:void(0);" class="tab-link @if($status == 'all') active @endif" data-status="all">All ({{ $allCount }})</a></li>
            <li><a href="javascript:void(0);" class="tab-link @if($status == 'active') active @endif" data-status="active">Active ({{ $activeCount }})</a></li>
            <li><a href="javascript:void(0);" class="tab-link @if($status == 'inactive') active @endif" data-status="inactive">Inactive ({{ $inactiveCount }})</a></li>
        </ul>
    </div>

    <div id="fTableWrapper">
        @include('admin.festivals._table')
    </div>

</div>
@endsection

@section('modal')
{{-- ══════════════ ADD FESTIVAL MODAL ══════════════ --}}
<div class="modal fade" id="addFestivalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add Festival</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addFestivalForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Festival Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">State <span class="text-danger">*</span></label>
                        <select name="state_id" class="form-select" required>
                            <option value="">— Select State —</option>
                            @foreach($states as $state)
                            <option value="{{ $state->id }}">{{ $state->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <x-media-picker name="image" picker-id="festival_image_add" label="Banner Image" folder="festivals" />
                        <div class="text-danger small mt-1 add-festival-image-error" style="display:none"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Banner Image Alt</label>
                        <input type="text" name="image_alt" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Banner Subtitle <small class="text-muted">(short tagline under the title, e.g. "Celebrate Kerala's Grand Harvest Festival")</small></label>
                        <input type="text" name="banner_subtitle" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Banner Description <small class="text-muted">(second line, e.g. "A time of joy, togetherness and timeless traditions.")</small></label>
                        <input type="text" name="banner_description" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Month <small class="text-muted">(for "Explore Festivals by Month" &amp; "Upcoming Festivals")</small></label>
                        <select name="month" class="form-select">
                            <option value="">— None —</option>
                            @foreach(['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'] as $i => $m)
                            <option value="{{ $i + 1 }}">{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Location <small class="text-muted">(city/area badge shown on "Popular Festivals in {State}" cards, e.g. "Pushkar")</small></label>
                        <input type="text" name="location_text" class="form-control" placeholder="e.g. Pushkar">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Month / Season Text <small class="text-muted">(display badge, e.g. "March - April")</small></label>
                        <input type="text" name="month_text" class="form-control" placeholder="e.g. March - April">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Duration Text <small class="text-muted">(shown on the Featured Festival card, e.g. "7 Days")</small></label>
                        <input type="text" name="duration_text" class="form-control" placeholder="e.g. 7 Days">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Short Description <small class="text-muted">(shown on the "Upcoming Festivals" card)</small></label>
                        <textarea name="short_description" class="form-control tinymce" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <x-media-picker name="intro_image" picker-id="festival_intro_image_add" label="Intro Image" folder="festivals" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Intro Image Alt</label>
                        <input type="text" name="intro_image_alt" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Add Festival
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ EDIT FESTIVAL MODAL ══════════════ --}}
<div class="modal fade" id="editFestivalModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Festival</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editFestivalForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Festival Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="ef-name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">State <span class="text-danger">*</span></label>
                        <select name="state_id" id="ef-state" class="form-select" required>
                            <option value="">— Select State —</option>
                            @foreach($states as $state)
                            <option value="{{ $state->id }}">{{ $state->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <x-media-picker name="image" picker-id="festival_image_edit" label="Banner Image" folder="festivals" />
                        <div class="text-danger small mt-1 edit-festival-image-error" style="display:none"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Banner Image Alt</label>
                        <input type="text" name="image_alt" id="ef-image-alt" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Banner Subtitle <small class="text-muted">(short tagline under the title, e.g. "Celebrate Kerala's Grand Harvest Festival")</small></label>
                        <input type="text" name="banner_subtitle" id="ef-banner-subtitle" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Banner Description <small class="text-muted">(second line, e.g. "A time of joy, togetherness and timeless traditions.")</small></label>
                        <input type="text" name="banner_description" id="ef-banner-description" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Month <small class="text-muted">(for "Explore Festivals by Month" &amp; "Upcoming Festivals")</small></label>
                        <select name="month" id="ef-month" class="form-select">
                            <option value="">— None —</option>
                            @foreach(['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'] as $i => $m)
                            <option value="{{ $i + 1 }}">{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Location <small class="text-muted">(city/area badge shown on "Popular Festivals in {State}" cards, e.g. "Pushkar")</small></label>
                        <input type="text" name="location_text" id="ef-location-text" class="form-control" placeholder="e.g. Pushkar">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Month / Season Text <small class="text-muted">(display badge, e.g. "March - April")</small></label>
                        <input type="text" name="month_text" id="ef-month-text" class="form-control" placeholder="e.g. March - April">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Duration Text <small class="text-muted">(shown on the Featured Festival card, e.g. "7 Days")</small></label>
                        <input type="text" name="duration_text" id="ef-duration-text" class="form-control" placeholder="e.g. 7 Days">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Short Description <small class="text-muted">(shown on the "Upcoming Festivals" card)</small></label>
                        <textarea name="short_description" id="ef-short-desc" class="form-control tinymce" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <x-media-picker name="intro_image" picker-id="festival_intro_image_edit" label="Intro Image" folder="festivals" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Intro Image Alt</label>
                        <input type="text" name="intro_image_alt" id="ef-intro-image-alt" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ FESTIVAL SETTING MODAL (Long Description) ══════════════ --}}
<div class="modal fade" id="festivalSettingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header modal-header-purple">
                <h5 class="modal-title"><i class="fas fa-cog me-2"></i>Setting — <span id="fs-setting-title"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="festivalSettingForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Long Description</label>
                        <textarea name="long_description" id="fs-long-description" class="form-control tinymce" rows="6"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Packages Section Title <small class="text-muted">(e.g. "Popular Festival Packages")</small></label>
                        <input type="text" name="packages_title" id="fs-packages-title" class="form-control" placeholder="Popular Festival Packages">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn" style="background:#7c3aed;color:#fff;"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ KEY EXPERIENCE MODAL ══════════════ --}}
<div class="modal fade" id="festivalKeyExperienceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-star me-2"></i>Key Experiences — <span id="fke-title"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="festivalKeyExperienceForm" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Section Title</label>
                        <input type="text" name="key_experience_title" id="fke-section-title" class="form-control" placeholder="Key Experiences during the Festival">
                    </div>
                    <table class="table align-middle mb-2" id="fkeTable">
                        <thead><tr><th style="min-width:220px;">Icon</th><th>Label</th><th class="text-end" style="width:1%;"><button type="button" class="btn btn-sm btn-outline-success" id="fkeAddRow"><i class="fas fa-plus"></i></button></th></tr></thead>
                        <tbody id="fkeTableBody"></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info text-white"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ HOW TO REACH MODAL ══════════════ --}}
<div class="modal fade" id="festivalHowToReachModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-route me-2"></i>How To Reach — <span id="fhtr-title"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="festivalHowToReachForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <table class="table align-middle mb-2" id="fhtrTable">
                        <thead><tr><th width="150">Mode</th><th>Description</th><th class="text-end" style="width:1%;"></th></tr></thead>
                        <tbody id="fhtrTableBody"></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-success" id="fhtrAddRow"><i class="fas fa-plus me-1"></i>Add Row</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ QUICK STATS MODAL ══════════════ --}}
<div class="modal fade" id="festivalStatsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-chart-bar me-2"></i>Quick Stats — <span id="fst-title"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="festivalStatsForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <table class="table align-middle mb-2" id="fstTable">
                        <thead><tr><th style="width:160px;">Value</th><th>Label</th><th class="text-end" style="width:1%;"><button type="button" class="btn btn-sm btn-outline-success" id="fstAddRow"><i class="fas fa-plus"></i></button></th></tr></thead>
                        <tbody id="fstTableBody"></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ FESTIVAL HIGHLIGHTS MODAL ══════════════ --}}
<div class="modal fade" id="festivalHighlightsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-images me-2"></i>Festival Highlights — <span id="fhl-title"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="festivalHighlightsForm" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Section Title</label>
                        <input type="text" name="highlights_title" id="fhl-section-title" class="form-control" placeholder="Festival Highlights">
                    </div>
                    <table class="table align-middle mb-2" id="fhlTable">
                        <thead><tr><th style="min-width:220px;">Image</th><th>Label</th><th class="text-end" style="width:1%;"><button type="button" class="btn btn-sm btn-outline-success" id="fhlAddRow"><i class="fas fa-plus"></i></button></th></tr></thead>
                        <tbody id="fhlTableBody"></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info text-white"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ POPULAR PLACES MODAL ══════════════ --}}
<div class="modal fade" id="festivalPlacesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-map-marker-alt me-2"></i>Popular Places — <span id="fpl-title"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="festivalPlacesForm" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Section Title</label>
                        <input type="text" name="places_title" id="fpl-section-title" class="form-control" placeholder="Popular Places to Experience">
                    </div>
                    <table class="table align-middle mb-2" id="fplTable">
                        <thead><tr><th style="min-width:220px;">Image</th><th>Place Name</th><th class="text-end" style="width:1%;"><button type="button" class="btn btn-sm btn-outline-success" id="fplAddRow"><i class="fas fa-plus"></i></button></th></tr></thead>
                        <tbody id="fplTableBody"></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ WHY VISIT MODAL ══════════════ --}}
<div class="modal fade" id="festivalWhyVisitModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title"><i class="fas fa-heart me-2"></i>Why Visit — <span id="fwv-title"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="festivalWhyVisitForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Section Title</label>
                        <input type="text" name="why_visit_title" id="fwv-section-title" class="form-control" placeholder="Why Visit During Festivals?">
                    </div>
                    <table class="table align-middle mb-2" id="fwvTable">
                        <thead><tr><th>Title</th><th>Description</th><th class="text-end" style="width:1%;"><button type="button" class="btn btn-sm btn-outline-success" id="fwvAddRow"><i class="fas fa-plus"></i></button></th></tr></thead>
                        <tbody id="fwvTableBody"></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning text-white"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ FAQS MODAL ══════════════ --}}
<div class="modal fade" id="festivalFaqsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="fas fa-question-circle me-2"></i>FAQs — <span id="ffaq-title"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="festivalFaqsForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Section Title</label>
                        <input type="text" name="faq_title" id="ffaq-section-title" class="form-control" placeholder="Frequently Asked Questions">
                    </div>
                    <table class="table" id="ffaqTable">
                        <thead><tr><th>Question</th><th>Answer</th><th width="40"></th></tr></thead>
                        <tbody id="ffaqTableBody"></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-success" id="ffaqAddRow"><i class="fas fa-plus me-1"></i>Add FAQ</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning"><i class="fas fa-save me-1"></i>Save FAQs</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ SEO META MODAL ══════════════ --}}
<div class="modal fade" id="festivalMetaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header modal-header-purple">
                <h5 class="modal-title"><i class="fas fa-globe me-2"></i>SEO Meta — <span id="fmeta-title"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="festivalMetaForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Meta Title</label>
                            <input type="text" name="meta_title" id="fmeta-meta-title" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meta Description</label>
                            <input type="text" name="meta_description" id="fmeta-meta-description" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meta Keywords</label>
                            <input type="text" name="meta_keywords" id="fmeta-meta-keywords" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">H1 Heading</label>
                            <input type="text" name="h1_heading" id="fmeta-h1-heading" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Extra Meta Tags</label>
                            <textarea name="meta_details" id="fmeta-meta-details" class="form-control" rows="4"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn" style="background:#7c3aed;color:#fff;"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function () {

    // ── Search (live) + Status filter ───────────────────────────────────────
    let fSearchTimer;
    let fCurrentStatus = '{{ $status }}';

    function fFetchFestivals() {
        let q = $('#fSearch').val();
        showAjaxLoader($('#fTableWrapper'));
        $.get('{{ route("admin.festival.index") }}', { search: q, status: fCurrentStatus, ajax: 1 })
            .done(function (res) {
                $('#fTableWrapper').html(res.html);
                if (typeof window.initSwitchery === 'function') window.initSwitchery();
            })
            .fail(function () { hideAjaxLoader($('#fTableWrapper')); toastr.error('Search failed.'); });
    }

    $('#fSearch').on('input', function () {
        clearTimeout(fSearchTimer);
        fSearchTimer = setTimeout(fFetchFestivals, 300);
    });

    $(document).on('click', '.tab-link', function () {
        $('.tab-link').removeClass('active');
        $(this).addClass('active');
        fCurrentStatus = $(this).data('status');
        fFetchFestivals();
    });

    // ── Add Festival ───────────────────────────────────────────────────────
    $('#addFestivalForm').on('submit', function (e) {
        e.preventDefault();
        $('.add-festival-image-error').hide().text('');
        if (!$('#addFestivalForm input[name=image]').val()) {
            toastr.warning('Please choose a Banner Image.');
            return;
        }
        if (typeof tinymce !== 'undefined') tinymce.triggerSave();
        let fd = new FormData(this);
        $.ajax({
            url: '{{ route("admin.festival.store") }}', type: 'POST', data: fd,
            processData: false, contentType: false,
        }).done(function (r) {
            toastr.success(r.message || 'Festival added.');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('addFestivalModal')).hide();
            document.getElementById('addFestivalForm').reset();
            $('#fSearch').trigger('input');
        }).fail(function (xhr) {
            if (xhr.status === 422 && xhr.responseJSON.errors) {
                let errs = xhr.responseJSON.errors;
                if (errs.image || errs.intro_image) {
                    $('.add-festival-image-error').text((errs.image || errs.intro_image)[0]).show();
                } else {
                    $('.add-festival-image-error').text(window.firstErrorMessage(errs, 'Validation failed.')).show();
                }
            } else {
                toastr.error('Failed to add festival.');
            }
        });
    });

    // ── Edit Festival ──────────────────────────────────────────────────────
    $(document).on('click', '.btn-edit-festival', function () {
        let id = $(this).data('id');
        $.get('{{ url("admin/festivals") }}/' + id).done(function (d) {
            $('#editFestivalForm').attr('data-id', d.id);
            $('#ef-name').val(d.name || '');
            $('#ef-state').val(d.state_id || '');
            $('#ef-image-alt').val(d.image_alt || '');
            $('#ef-banner-subtitle').val(d.banner_subtitle || '');
            $('#ef-banner-description').val(d.banner_description || '');
            if (typeof window.setMediaPickerValue === 'function') {
                window.setMediaPickerValue('festival_image_edit', d.image, d.image ? (s3BaseUrl + d.image) : null);
            }
            $('#ef-month').val(d.month || '');
            $('#ef-location-text').val(d.location_text || '');
            $('#ef-month-text').val(d.month_text || '');
            $('#ef-duration-text').val(d.duration_text || '');
            $('#ef-short-desc').val(d.short_description || '');
            $('#ef-intro-image-alt').val(d.intro_image_alt || '');
            if (typeof window.setMediaPickerValue === 'function') {
                window.setMediaPickerValue('festival_intro_image_edit', d.intro_image, d.intro_image ? (s3BaseUrl + d.intro_image) : null);
            }
            bootstrap.Modal.getOrCreateInstance(document.getElementById('editFestivalModal')).show();
        }).fail(function () { toastr.error('Failed to load festival.'); });
    });

    $('#editFestivalForm').on('submit', function (e) {
        e.preventDefault();
        $('.edit-festival-image-error').hide().text('');
        if (typeof tinymce !== 'undefined') tinymce.triggerSave();
        let id = $(this).attr('data-id');
        let fd = new FormData(this);
        fd.append('_method', 'PUT');
        $.ajax({
            url: '{{ url("admin/festivals") }}/' + id, type: 'POST', data: fd,
            processData: false, contentType: false,
        }).done(function (r) {
            toastr.success(r.message || 'Festival updated.');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('editFestivalModal')).hide();
            $('#fSearch').trigger('input');
        }).fail(function (xhr) {
            if (xhr.status === 422 && xhr.responseJSON.errors) {
                let errs = xhr.responseJSON.errors;
                if (errs.image || errs.intro_image) {
                    $('.edit-festival-image-error').text((errs.image || errs.intro_image)[0]).show();
                } else {
                    $('.edit-festival-image-error').text(window.firstErrorMessage(errs, 'Validation failed.')).show();
                }
            } else {
                toastr.error('Failed to update festival.');
            }
        });
    });

    // ── Status toggle ──────────────────────────────────────────────────────
    $(document).on('change', '.festival-status', function () {
        $.ajax({
            url: $(this).data('url'),
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function () { toastr.success('Status updated'); },
            error: function () { toastr.error('Failed to update status.'); }
        });
    });

    // ── Delete ───────────────────────────────────────────────────────────
    $(document).on('click', '.delete-festival', function () {
        let btn = $(this);
        let row = btn.closest('tr');

        Swal.fire({
            title: 'Are you sure?',
            text: 'This will permanently delete this festival!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e3342f',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it',
        }).then((result) => {
            if (result.isConfirmed) {
                btn.find('.spinner-border').removeClass('d-none');
                btn.find('.icon').addClass('d-none');
                $.ajax({
                    url: btn.data('url'),
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function (res) {
                        if (res.status) {
                            row.remove();
                            toastr.success('Festival deleted.');
                        }
                    },
                    error: function () {
                        toastr.error('Failed to delete festival.');
                        btn.find('.spinner-border').addClass('d-none');
                        btn.find('.icon').removeClass('d-none');
                    }
                });
            }
        });
    });

    // ── Setting (Long Description) ──────────────────────────────────────────
    $(document).on('click', '.btn-festival-setting', function () {
        let btn = $(this);
        let id = btn.data('id');
        $.get('{{ url("admin/festivals") }}/' + id + '/detail').done(function (d) {
            $('#fs-setting-title').text(btn.data('name'));
            $('#festivalSettingForm').attr('action', '{{ url("admin/festivals") }}/' + id + '/setting');
            let longDesc = d.long_description || '';
            $('#fs-long-description').val(longDesc);
            $('#fs-packages-title').val(d.packages_title || '');
            $('#festivalSettingModal').one('shown.bs.modal', function () {
                if (typeof tinymce !== 'undefined' && tinymce.get('fs-long-description')) {
                    tinymce.get('fs-long-description').setContent(longDesc);
                }
            });
            bootstrap.Modal.getOrCreateInstance(document.getElementById('festivalSettingModal')).show();
        }).fail(function () { toastr.error('Failed to load setting.'); });
    });

    $('#festivalSettingForm').on('submit', function (e) {
        e.preventDefault();
        if (typeof tinymce !== 'undefined') tinymce.triggerSave();
        $.ajax({
            url: $(this).attr('action'), type: 'POST', data: new FormData(this),
            processData: false, contentType: false,
        }).done(function (r) {
            toastr.success(r.message || 'Saved!');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('festivalSettingModal')).hide();
        }).fail(function () { toastr.error('Failed to save setting.'); });
    });

    // ── Key Experiences ───────────────────────────────────────────────────────
    let fkePickerSeq = 0;

    function fkeRow(label, icon) {
        const pickerId = 'fke_' + (fkePickerSeq++);
        const pickerHtml = typeof window.mediaPickerFieldHtml === 'function'
            ? window.mediaPickerFieldHtml('key_experience_icons[0]', pickerId, '', 'festivals/key-experiences')
            : '';
        const row = `
            <tr>
                <td>${pickerHtml}</td>
                <td>
                    <input type="text" name="labels[0]" class="form-control" value="${label || ''}" placeholder="e.g. Pookalam Rangoli">
                </td>
                <td class="text-end" style="width:1%;"><button type="button" class="btn btn-sm btn-outline-danger rm-fke-row"><i class="fas fa-trash"></i></button></td>
            </tr>
        `;
        if (icon && typeof window.setMediaPickerValue === 'function') {
            setTimeout(function () { window.setMediaPickerValue(pickerId, icon, s3BaseUrl + icon); }, 0);
        }
        return row;
    }

    function reindexFkeRows() {
        $('#fkeTableBody > tr').each(function (i) {
            $(this).find('[name^="labels["]').attr('name', `labels[${i}]`);
            $(this).find('.media-picker-value').attr('name', `key_experience_icons[${i}]`);
        });
    }

    $(document).on('click', '.btn-festival-key-experience', function () {
        let btn = $(this);
        let id = btn.data('id');
        $.get('{{ url("admin/festivals") }}/' + id + '/detail').done(function (d) {
            $('#fke-title').text(btn.data('name'));
            $('#festivalKeyExperienceForm').attr('action', '{{ url("admin/festivals") }}/' + id + '/key-experiences');
            $('#fke-section-title').val(d.key_experience_title || '');
            let items = d.key_experiences || [];
            let body = $('#fkeTableBody').empty();
            if (items.length) {
                items.forEach(it => body.append(fkeRow(it.label, it.icon)));
            } else {
                body.append(fkeRow('', ''));
            }
            reindexFkeRows();
            bootstrap.Modal.getOrCreateInstance(document.getElementById('festivalKeyExperienceModal')).show();
        }).fail(function () { toastr.error('Failed to load key experiences.'); });
    });

    $(document).on('click', '#fkeAddRow', function (e) {
        e.preventDefault();
        $('#fkeTableBody').append(fkeRow('', ''));
        reindexFkeRows();
    });
    $(document).on('click', '.rm-fke-row', function () {
        $(this).closest('tr').remove();
        reindexFkeRows();
    });

    $('#festivalKeyExperienceForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'), type: 'POST', data: new FormData(this),
            processData: false, contentType: false,
        }).done(function (r) {
            toastr.success(r.message || 'Saved!');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('festivalKeyExperienceModal')).hide();
        }).fail(function (xhr) {
            toastr.error((xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to save key experiences.');
        });
    });

    // ── Quick Stats ─────────────────────────────────────────────────────────
    function fstRow(value, label) {
        return `
            <tr>
                <td><input type="text" name="values[]" class="form-control" value="${value || ''}" placeholder="e.g. 10"></td>
                <td><input type="text" name="labels[]" class="form-control" value="${label || ''}" placeholder="e.g. Days of Celebration"></td>
                <td class="text-end" style="width:1%;"><button type="button" class="btn btn-sm btn-outline-danger rm-fst-row"><i class="fas fa-trash"></i></button></td>
            </tr>
        `;
    }

    $(document).on('click', '.btn-festival-stats', function () {
        let btn = $(this);
        let id = btn.data('id');
        $.get('{{ url("admin/festivals") }}/' + id + '/detail').done(function (d) {
            $('#fst-title').text(btn.data('name'));
            $('#festivalStatsForm').attr('action', '{{ url("admin/festivals") }}/' + id + '/stats');
            let items = d.stats || [];
            let body = $('#fstTableBody').empty();
            if (items.length) {
                items.forEach(it => body.append(fstRow(it.value, it.label)));
            } else {
                body.append(fstRow('', ''));
            }
            bootstrap.Modal.getOrCreateInstance(document.getElementById('festivalStatsModal')).show();
        }).fail(function () { toastr.error('Failed to load stats.'); });
    });

    $(document).on('click', '#fstAddRow', function (e) {
        e.preventDefault();
        $('#fstTableBody').append(fstRow('', ''));
    });
    $(document).on('click', '.rm-fst-row', function () {
        $(this).closest('tr').remove();
    });

    $('#festivalStatsForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'), type: 'POST', data: new FormData(this),
            processData: false, contentType: false,
        }).done(function (r) {
            toastr.success(r.message || 'Saved!');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('festivalStatsModal')).hide();
        }).fail(function () { toastr.error('Failed to save stats.'); });
    });

    // ── Festival Highlights ─────────────────────────────────────────────────
    let fhlPickerSeq = 0;

    function fhlRow(label, image) {
        const pickerId = 'fhl_' + (fhlPickerSeq++);
        const pickerHtml = typeof window.mediaPickerFieldHtml === 'function'
            ? window.mediaPickerFieldHtml('highlight_images[0]', pickerId, '', 'festivals/highlights')
            : '';
        const row = `
            <tr>
                <td>${pickerHtml}</td>
                <td>
                    <input type="text" name="labels[0]" class="form-control" value="${label || ''}" placeholder="e.g. Pookalam Celebrations">
                </td>
                <td class="text-end" style="width:1%;"><button type="button" class="btn btn-sm btn-outline-danger rm-fhl-row"><i class="fas fa-trash"></i></button></td>
            </tr>
        `;
        if (image && typeof window.setMediaPickerValue === 'function') {
            setTimeout(function () { window.setMediaPickerValue(pickerId, image, s3BaseUrl + image); }, 0);
        }
        return row;
    }

    function reindexFhlRows() {
        $('#fhlTableBody > tr').each(function (i) {
            $(this).find('[name^="labels["]').attr('name', `labels[${i}]`);
            $(this).find('.media-picker-value').attr('name', `highlight_images[${i}]`);
        });
    }

    $(document).on('click', '.btn-festival-highlights', function () {
        let btn = $(this);
        let id = btn.data('id');
        $.get('{{ url("admin/festivals") }}/' + id + '/detail').done(function (d) {
            $('#fhl-title').text(btn.data('name'));
            $('#festivalHighlightsForm').attr('action', '{{ url("admin/festivals") }}/' + id + '/highlights');
            $('#fhl-section-title').val(d.highlights_title || '');
            let items = d.highlights || [];
            let body = $('#fhlTableBody').empty();
            if (items.length) {
                items.forEach(it => body.append(fhlRow(it.label, it.image)));
            } else {
                body.append(fhlRow('', ''));
            }
            reindexFhlRows();
            bootstrap.Modal.getOrCreateInstance(document.getElementById('festivalHighlightsModal')).show();
        }).fail(function () { toastr.error('Failed to load highlights.'); });
    });

    $(document).on('click', '#fhlAddRow', function (e) {
        e.preventDefault();
        $('#fhlTableBody').append(fhlRow('', ''));
        reindexFhlRows();
    });
    $(document).on('click', '.rm-fhl-row', function () {
        $(this).closest('tr').remove();
        reindexFhlRows();
    });

    $('#festivalHighlightsForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'), type: 'POST', data: new FormData(this),
            processData: false, contentType: false,
        }).done(function (r) {
            toastr.success(r.message || 'Saved!');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('festivalHighlightsModal')).hide();
        }).fail(function (xhr) {
            toastr.error((xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to save highlights.');
        });
    });

    // ── Popular Places ──────────────────────────────────────────────────────
    let fplPickerSeq = 0;

    function fplRow(name, image) {
        const pickerId = 'fpl_' + (fplPickerSeq++);
        const pickerHtml = typeof window.mediaPickerFieldHtml === 'function'
            ? window.mediaPickerFieldHtml('place_images[0]', pickerId, '', 'festivals/places')
            : '';
        const row = `
            <tr>
                <td>${pickerHtml}</td>
                <td>
                    <input type="text" name="names[0]" class="form-control" value="${name || ''}" placeholder="e.g. Kochi">
                </td>
                <td class="text-end" style="width:1%;"><button type="button" class="btn btn-sm btn-outline-danger rm-fpl-row"><i class="fas fa-trash"></i></button></td>
            </tr>
        `;
        if (image && typeof window.setMediaPickerValue === 'function') {
            setTimeout(function () { window.setMediaPickerValue(pickerId, image, s3BaseUrl + image); }, 0);
        }
        return row;
    }

    function reindexFplRows() {
        $('#fplTableBody > tr').each(function (i) {
            $(this).find('[name^="names["]').attr('name', `names[${i}]`);
            $(this).find('.media-picker-value').attr('name', `place_images[${i}]`);
        });
    }

    $(document).on('click', '.btn-festival-places', function () {
        let btn = $(this);
        let id = btn.data('id');
        $.get('{{ url("admin/festivals") }}/' + id + '/detail').done(function (d) {
            $('#fpl-title').text(btn.data('name'));
            $('#festivalPlacesForm').attr('action', '{{ url("admin/festivals") }}/' + id + '/places');
            $('#fpl-section-title').val(d.places_title || '');
            let items = d.places || [];
            let body = $('#fplTableBody').empty();
            if (items.length) {
                items.forEach(it => body.append(fplRow(it.name, it.image)));
            } else {
                body.append(fplRow('', ''));
            }
            reindexFplRows();
            bootstrap.Modal.getOrCreateInstance(document.getElementById('festivalPlacesModal')).show();
        }).fail(function () { toastr.error('Failed to load places.'); });
    });

    $(document).on('click', '#fplAddRow', function (e) {
        e.preventDefault();
        $('#fplTableBody').append(fplRow('', ''));
        reindexFplRows();
    });
    $(document).on('click', '.rm-fpl-row', function () {
        $(this).closest('tr').remove();
        reindexFplRows();
    });

    $('#festivalPlacesForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'), type: 'POST', data: new FormData(this),
            processData: false, contentType: false,
        }).done(function (r) {
            toastr.success(r.message || 'Saved!');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('festivalPlacesModal')).hide();
        }).fail(function (xhr) {
            toastr.error((xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to save places.');
        });
    });

    // ── How to Reach ──────────────────────────────────────────────────────────
    const FHTR_MODES = ['By Air', 'By Road', 'By Train', 'By Water', 'By Sea'];
    function fhtrRow(mode, description) {
        let options = FHTR_MODES.map(m => `<option value="${m}" ${m === mode ? 'selected' : ''}>${m}</option>`).join('');
        return `
            <tr>
                <td><select name="modes[]" class="form-select">${options}</select></td>
                <td><textarea name="descriptions[]" class="form-control" rows="2">${description || ''}</textarea></td>
                <td class="text-end" style="width:1%;"><button type="button" class="btn btn-sm btn-outline-danger rm-fhtr-row"><i class="fas fa-trash"></i></button></td>
            </tr>
        `;
    }

    $(document).on('click', '.btn-festival-how-to-reach', function () {
        let btn = $(this);
        let id = btn.data('id');
        $.get('{{ url("admin/festivals") }}/' + id + '/detail').done(function (d) {
            $('#fhtr-title').text(btn.data('name'));
            $('#festivalHowToReachForm').attr('action', '{{ url("admin/festivals") }}/' + id + '/how-to-reach');
            let items = d.how_to_reach || [];
            let body = $('#fhtrTableBody').empty();
            if (items.length) {
                items.forEach(it => body.append(fhtrRow(it.mode, it.description)));
            } else {
                body.append(fhtrRow(FHTR_MODES[0], ''));
            }
            bootstrap.Modal.getOrCreateInstance(document.getElementById('festivalHowToReachModal')).show();
        }).fail(function () { toastr.error('Failed to load how to reach.'); });
    });

    $(document).on('click', '#fhtrAddRow', function (e) {
        e.preventDefault();
        $('#fhtrTableBody').append(fhtrRow(FHTR_MODES[0], ''));
    });
    $(document).on('click', '.rm-fhtr-row', function () {
        $(this).closest('tr').remove();
    });

    $('#festivalHowToReachForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'), type: 'POST', data: new FormData(this),
            processData: false, contentType: false,
        }).done(function (r) {
            toastr.success(r.message || 'Saved!');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('festivalHowToReachModal')).hide();
        }).fail(function () { toastr.error('Failed to save how to reach.'); });
    });

    // ── Why Visit ──────────────────────────────────────────────────────────────
    function fwvRow(title, description) {
        return `
            <tr>
                <td><input type="text" name="titles[]" class="form-control" value="${title || ''}" placeholder="e.g. Authentic Cultural Experiences"></td>
                <td><textarea name="descriptions[]" class="form-control" rows="2">${description || ''}</textarea></td>
                <td class="text-end" style="width:1%;"><button type="button" class="btn btn-sm btn-outline-danger rm-fwv-row"><i class="fas fa-trash"></i></button></td>
            </tr>
        `;
    }

    $(document).on('click', '.btn-festival-why-visit', function () {
        let btn = $(this);
        let id = btn.data('id');
        $.get('{{ url("admin/festivals") }}/' + id + '/detail').done(function (d) {
            $('#fwv-title').text(btn.data('name'));
            $('#festivalWhyVisitForm').attr('action', '{{ url("admin/festivals") }}/' + id + '/why-visits');
            $('#fwv-section-title').val(d.why_visit_title || '');
            let items = d.why_visits || [];
            let body = $('#fwvTableBody').empty();
            if (items.length) {
                items.forEach(it => body.append(fwvRow(it.title, it.description)));
            } else {
                body.append(fwvRow('', ''));
            }
            bootstrap.Modal.getOrCreateInstance(document.getElementById('festivalWhyVisitModal')).show();
        }).fail(function () { toastr.error('Failed to load why visit.'); });
    });

    $(document).on('click', '#fwvAddRow', function (e) {
        e.preventDefault();
        $('#fwvTableBody').append(fwvRow('', ''));
    });
    $(document).on('click', '.rm-fwv-row', function () {
        $(this).closest('tr').remove();
    });

    $('#festivalWhyVisitForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'), type: 'POST', data: new FormData(this),
            processData: false, contentType: false,
        }).done(function (r) {
            toastr.success(r.message || 'Saved!');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('festivalWhyVisitModal')).hide();
        }).fail(function () { toastr.error('Failed to save why visit.'); });
    });

    // ── FAQs ─────────────────────────────────────────────────────────────────
    function ffaqRow(question, answer) {
        return `
            <tr>
                <td><input type="text" name="questions[]" class="form-control" value="${question || ''}" placeholder="Question"></td>
                <td><textarea name="answers[]" class="form-control" rows="2">${answer || ''}</textarea></td>
                <td><button type="button" class="btn btn-sm btn-outline-danger rm-ffaq-row"><i class="fas fa-trash"></i></button></td>
            </tr>
        `;
    }

    $(document).on('click', '.btn-festival-faqs', function () {
        let btn = $(this);
        let id = btn.data('id');
        $.get('{{ url("admin/festivals") }}/' + id + '/detail').done(function (d) {
            $('#ffaq-title').text(btn.data('name'));
            $('#festivalFaqsForm').attr('action', '{{ url("admin/festivals") }}/' + id + '/faqs');
            $('#ffaq-section-title').val(d.faq_title || '');
            let items = d.faqs || [];
            let body = $('#ffaqTableBody').empty();
            if (items.length) {
                items.forEach(it => body.append(ffaqRow(it.question, it.answer)));
            } else {
                body.append(ffaqRow('', ''));
            }
            bootstrap.Modal.getOrCreateInstance(document.getElementById('festivalFaqsModal')).show();
        }).fail(function () { toastr.error('Failed to load FAQs.'); });
    });

    $(document).on('click', '#ffaqAddRow', function (e) {
        e.preventDefault();
        $('#ffaqTableBody').append(ffaqRow('', ''));
    });
    $(document).on('click', '.rm-ffaq-row', function () {
        $(this).closest('tr').remove();
    });

    $('#festivalFaqsForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'), type: 'POST', data: new FormData(this),
            processData: false, contentType: false,
        }).done(function (r) {
            toastr.success(r.message || 'Saved!');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('festivalFaqsModal')).hide();
        }).fail(function () { toastr.error('Failed to save FAQs.'); });
    });

    // ── SEO Meta ─────────────────────────────────────────────────────────────
    $(document).on('click', '.btn-festival-meta', function () {
        let btn = $(this);
        let id = btn.data('id');
        $.get('{{ url("admin/festivals") }}/' + id + '/detail').done(function (d) {
            $('#fmeta-title').text(btn.data('name'));
            $('#festivalMetaForm').attr('action', '{{ url("admin/festivals") }}/' + id + '/meta');
            let meta = d.meta || {};
            $('#fmeta-meta-title').val(meta.meta_title || '');
            $('#fmeta-meta-description').val(meta.meta_description || '');
            $('#fmeta-meta-keywords').val(meta.meta_keywords || '');
            $('#fmeta-h1-heading').val(meta.h1_heading || '');
            $('#fmeta-meta-details').val(meta.meta_details || '');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('festivalMetaModal')).show();
        }).fail(function () { toastr.error('Failed to load meta.'); });
    });

    $('#festivalMetaForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'), type: 'POST', data: new FormData(this),
            processData: false, contentType: false,
        }).done(function (r) {
            toastr.success(r.message || 'Saved!');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('festivalMetaModal')).hide();
        }).fail(function () { toastr.error('Failed to save meta.'); });
    });

});
</script>
@endsection

@section('title', $page->exists ? 'Edit: ' . $page->title : 'New CMS Page')
@extends('layouts.app')

@push('style')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.css" crossorigin="anonymous">
<style>
    .section-card {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        margin-bottom: 10px;
        background: #fff;
        transition: box-shadow .15s;
    }

    .section-card.sortable-chosen {
        box-shadow: 0 4px 20px rgba(0, 0, 0, .15);
    }

    .section-card-header {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        background: #f8fafc;
        border-radius: 8px 8px 0 0;
        border-bottom: 1px solid #e2e8f0;
        cursor: default;
    }

    .section-card-body {
        padding: 16px;
    }

    .drag-handle {
        cursor: grab;
        color: #94a3b8;
        font-size: 14px;
        flex-shrink: 0;
    }

    .drag-handle:active {
        cursor: grabbing;
    }

    .section-type-badge {
        font-size: 11px;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 99px;
        flex-shrink: 0;
    }

    .section-label-input {
        border: 1px solid transparent;
        background: transparent;
        font-size: 13px;
        color: #475569;
        padding: 2px 6px;
        border-radius: 4px;
        flex: 1;
        min-width: 0;
    }

    .section-label-input:focus {
        border-color: #cbd5e1;
        background: #fff;
        outline: none;
    }

    .section-type-tile {
        cursor: pointer;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 16px 10px;
        text-align: center;
        transition: all .15s;
    }

    .section-type-tile:hover {
        border-color: #3b82f6;
        background: #eff6ff;
    }

    .section-type-tile .tile-icon {
        font-size: 22px;
        margin-bottom: 6px;
    }

    .section-type-tile .tile-label {
        font-size: 12px;
        font-weight: 600;
        color: #334155;
    }

    #sectionList {
        min-height: 60px;
    }

    .empty-sections {
        text-align: center;
        padding: 40px 20px;
        color: #94a3b8;
        border: 2px dashed #e2e8f0;
        border-radius: 8px;
    }

    .faq-row,
    .stat-row,
    .gallery-row,
    .card-item-row,
    .experience-item-row {
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 10px;
        margin-bottom: 8px;
    }

    .builder-float-btns {
        position: fixed;
        right: 24px;
        bottom: 5px;
        z-index: 1040;
        display: flex;
        gap: 16px;
    }

</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="h4 fw-bold mb-0">
                <i class="fas fa-layer-group me-2 text-primary"></i>
                {{ $page->exists ? 'Edit Page' : 'New CMS Page' }}
            </h2>
            @if($page->exists)
            <small class="text-muted"><code>/{{ $page->slug }}</code></small>
            @endif
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.cms-builder.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
            <button type="button" class="btn btn-sm btn-outline-primary builder-submit-btn" onclick="submitBuilder(0, this)">
                <i class="fas fa-save me-1"></i> Save Draft
            </button>
            <button type="button" class="btn btn-sm btn-success builder-submit-btn" onclick="submitBuilder(1, this)">
                <i class="fas fa-globe me-1"></i> Publish
            </button>
        </div>
    </div>

    {{-- Floating action buttons (always visible while scrolling) --}}
    <div class="builder-float-btns">
        <button type="button" class="btn btn-primary btn-sm shadow"
            data-bs-toggle="modal" data-bs-target="#addSectionModal">
            <i class="fas fa-plus me-1"></i> Add Section
        </button>
        <button type="button" class="btn btn-success btn-sm shadow builder-submit-btn" onclick="submitBuilder(1, this)">
            <i class="fas fa-globe me-1"></i> Publish
        </button>
    </div>

    <form id="builderForm"
        method="POST"
        action="{{ $page->exists ? route('admin.cms-builder.update', $page) : route('admin.cms-builder.store') }}">
        @csrf
        @if($page->exists) @method('PUT') @endif
        <input type="hidden" name="is_published" id="isPublishedInput" value="{{ $page->is_published ? 1 : 0 }}">
        <input type="hidden" name="sections_json" id="sectionsJson">

        <div class="row g-4">

            {{-- ══ LEFT: Page Settings ══ --}}
            <div class="col-lg-4">

                {{-- Basic Info --}}
                <div class="card mb-3">
                    <div class="card-header fw-semibold">
                        <i class="fas fa-info-circle me-2 text-primary"></i>Page Info
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-medium">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="pageTitle" class="form-control"
                                value="{{ old('title', $page->title) }}" required
                                placeholder="e.g. About Us">
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-medium">Slug <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text text-muted">/</span>
                                <input type="text" name="slug" id="pageSlug"
                                    class="form-control bg-light text-muted"
                                    value="{{ old('slug', $page->slug) }}" required readonly
                                    placeholder="auto-generated from title">
                            </div>
                            <small class="text-muted">
                                @if($page->exists)
                                    <i class="fas fa-lock fa-xs me-1"></i>Locked — slug cannot be changed after creation.
                                @else
                                    <i class="fas fa-magic fa-xs me-1"></i>Auto-generated as you type the title.
                                @endif
                            </small>
                        </div>
                        <input type="hidden" name="template" value="{{ $page->template ?? 'default' }}">
                    </div>
                </div>


            </div>

            {{-- ══ RIGHT: Section Builder ══ --}}
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fas fa-th-large me-2 text-primary"></i>Page Sections
                        </h5>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-primary"
                                data-bs-toggle="modal" data-bs-target="#addSectionModal">
                                <i class="fas fa-plus me-1"></i> Add Section
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="sectionList"></div>
                        <div id="emptySections" class="empty-sections">
                            <i class="fas fa-th-large fa-2x mb-2 d-block"></i>
                            No sections yet. Click <strong>+ Add Section</strong> to start building.
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- end .row --}}
    </form>

</div>
@endsection

{{-- ══════════════════════════════════════════════════════════════
     ADD SECTION MODAL
══════════════════════════════════════════════════════════════ --}}
@section('modal')
<div class="modal fade" id="addSectionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add Section</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    @foreach($sectionTypes as $type => $info)
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="section-type-tile" data-type="{{ $type }}" data-bs-dismiss="modal">
                            <div class="tile-icon text-{{ $info['color'] }}">
                                <i class="fas {{ $info['icon'] }}"></i>
                            </div>
                            <div class="tile-label">{{ $info['label'] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══ SECTION TEMPLATES (hidden, cloned by JS for new sections) ══ --}}
<div id="sectionTemplates" class="d-none">
    @foreach($sectionTypes as $type => $info)
    <div class="section-tpl" data-type="{{ $type }}">
        @include('admin.cms-builder.sections._config_' . $type, ['content' => [], 'tpl' => true])
    </div>
    @endforeach
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    const SECTION_TYPES = @json($sectionTypes);
    const EXISTING = @json($existingSections);
    const PAGE_ID = {{ $page->id ?? 'null' }};
    const SLUG_URL = "{{ route('admin.cms-builder.slug') }}";
    const STORAGE_URL = "{{ storage_base_url() }}";
    const TEAM_MEMBER_URL = "{{ route('admin.cms-builder.team-member.store') }}";
    const CSRF = $('meta[name="csrf-token"]').attr('content');

    const sectionList = document.getElementById('sectionList');
    const emptyNotice = document.getElementById('emptySections');
    let sectionCounter = 0;
    let lastAddedCard = null;
    // Must be initialized before renderSection() runs below — a "cards" (Feature
    // Cards) section in EXISTING calls cardItemRowHtml(), which reads this.
    let cmsCardPickerSeq = 0;

    // ── Init Sortable ─────────────────────────────────────────────────────────
    const sortable = new Sortable(sectionList, {
        handle: '.drag-handle',
        animation: 150,
        ghostClass: 'sortable-chosen',
    });

    // ── Render existing sections ──────────────────────────────────────────────
    EXISTING.forEach(s => renderSection(s));
    updateEmptyState();

    // Scroll to newly-added card AFTER modal fully closes (avoids Bootstrap's scroll restore overriding us)
    ['addSectionModal'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('hidden.bs.modal', function() {
            if (lastAddedCard) {
                setTimeout(() => lastAddedCard.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                }), 50);
                lastAddedCard = null;
            }
        });
    });

    const IS_NEW_PAGE = {{ $page->exists ? 'false' : 'true' }};

    // Live slug generation (create mode only) + clear validation highlight
    let _slugDebounce;
    document.getElementById('pageTitle').addEventListener('input', function() {
        this.classList.remove('is-invalid');
        if (!IS_NEW_PAGE) return;
        const title = this.value.trim();
        clearTimeout(_slugDebounce);
        if (!title) { document.getElementById('pageSlug').value = ''; return; }
        _slugDebounce = setTimeout(function() {
            $.get(SLUG_URL, { title, exclude_id: PAGE_ID }, function(res) {
                document.getElementById('pageSlug').value = res.slug;
                document.getElementById('pageSlug').classList.remove('is-invalid');
            });
        }, 400);
    });

    // ── Add Section (type tile click) ─────────────────────────────────────────
    $(document).on('click', '.section-type-tile', function() {
        addSection($(this).data('type'), null);
    });

    // ── Toggle section body ───────────────────────────────────────────────────
    $(document).on('click', '.section-toggle-btn', function() {
        const body = $(this).closest('.section-card').find('.section-card-body');
        const hiding = !body.hasClass('d-none');
        body.toggleClass('d-none');
        $(this).find('i').toggleClass('fa-chevron-up', !hiding).toggleClass('fa-chevron-down', hiding);
    });

    // ── Delete section ────────────────────────────────────────────────────────
    $(document).on('click', '.section-delete-btn', function() {
        $(this).closest('.section-card').remove();
        updateEmptyState();
    });

    // ── Section filter toggles (team/awards dep selects) ─────────────────────
    $(document).on('change', '.team-filter-select', function() {
        const card = $(this).closest('.section-card');
        card.find('.team-members-group').toggle($(this).val() === 'selected');
    });
    $(document).on('change', '.awards-filter-select', function() {
        const card = $(this).closest('.section-card');
        card.find('.awards-selected-group').toggle($(this).val() === 'selected');
    });

    // ── Team member select → sync hidden input ────────────────────────────────
    $(document).on('change', '.team-member-select', function() {
        const vals = Array.from(this.selectedOptions).map(o => o.value);
        $(this).closest('.section-card').find('.team-member-ids-hidden').val(vals.join(','));
    });

    // ── Add New Member form toggle ────────────────────────────────────────────
    $(document).on('click', '.btn-toggle-add-member', function() {
        const form = $(this).closest('.section-card').find('.add-member-form');
        form.toggleClass('d-none');
        $(this).find('i').toggleClass('fa-chevron-down fa-chevron-up');
    });
    $(document).on('click', '.btn-cancel-add-member', function() {
        const form = $(this).closest('.add-member-form');
        form.addClass('d-none');
        form.closest('.section-card').find('.btn-toggle-add-member i')
            .removeClass('fa-chevron-up').addClass('fa-chevron-down');
    });

    // ── Create team member (AJAX) ─────────────────────────────────────────────
    $(document).on('click', '.btn-create-team-member', function() {
        const btn = $(this);
        const card = btn.closest('.section-card');
        const form = card.find('.add-member-form');

        const name  = form.find('.new-member-name').val().trim();
        const depId = form.find('.new-member-dep').val();
        const desc  = form.find('.new-member-desc').val().trim();
        const about = form.find('.new-member-about').val().trim();
        const profileImage = form.find('.media-picker-value').val();

        if (!name)  { toastr.error('Name is required.');        return; }
        if (!depId) { toastr.error('Department is required.');  return; }
        if (!desc)  { toastr.error('Designation is required.'); return; }

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Saving…');

        $.ajax({
            url: TEAM_MEMBER_URL,
            type: 'POST',
            data: {
                _token: CSRF,
                name: name,
                dep_id: depId,
                description: desc,
                about: about,
                profile_image: profileImage,
            },
        }).done(function(res) {
            toastr.success(res.message || 'Member created.');

            // Add option to select and auto-select it
            const select = card.find('.team-member-select');
            if (select.length) {
                select.append(new Option(res.member.name, res.member.id, true, true));
                // Sync hidden input
                const vals = Array.from(select[0].selectedOptions).map(o => o.value);
                card.find('.team-member-ids-hidden').val(vals.join(','));
            }

            // Switch filter to "selected" so the new member shows
            const filterSel = card.find('.team-filter-select');
            if (filterSel.val() === 'all') {
                filterSel.val('selected').trigger('change');
            }

            // Clear and collapse form
            form.find('.new-member-name, .new-member-desc, .new-member-about').val('');
            form.find('.new-member-dep').val('');
            form.find('.media-picker-value').val('');
            form.find('.media-picker-preview').addClass('d-none');
            form.addClass('d-none');
            card.find('.btn-toggle-add-member i').removeClass('fa-chevron-up').addClass('fa-chevron-down');

        }).fail(function(xhr) {
            const errors = xhr.responseJSON?.errors;
            toastr.error(errors ? Object.values(errors).flat().join(' ') : 'Failed to create member.');
        }).always(function() {
            btn.prop('disabled', false).html('<i class="fas fa-plus me-1"></i>Save New Member');
        });
    });

    // ── FAQ add/remove rows ───────────────────────────────────────────────────
    $(document).on('click', '.btn-add-faq', function() {
        const tbody = $(this).closest('.section-card').find('.faq-tbody');
        const i = tbody.find('.faq-row').length;
        tbody.append(faqRowHtml(i, '', ''));
    });
    $(document).on('click', '.btn-rm-faq', function() {
        $(this).closest('.faq-row').remove();
    });

    // ── Stats add/remove rows ─────────────────────────────────────────────────
    $(document).on('click', '.btn-add-stat', function() {
        const tbody = $(this).closest('.section-card').find('.stats-tbody');
        tbody.append(statRowHtml('', '', ''));
    });
    $(document).on('click', '.btn-rm-stat', function() {
        $(this).closest('.stat-row').remove();
    });

    // ── Gallery add/remove rows ───────────────────────────────────────────────
    $(document).on('click', '.btn-add-gallery', function() {
        const tbody = $(this).closest('.section-card').find('.gallery-tbody');
        tbody.append(galleryRowHtml('', ''));
    });
    $(document).on('click', '.btn-rm-gallery', function() {
        $(this).closest('.gallery-row').remove();
    });

    // ── Feature Cards add/remove rows ────────────────────────────────────────
    $(document).on('click', '.btn-add-card-item', function() {
        const tbody = $(this).closest('.section-card').find('.cards-tbody');
        tbody.append(cardItemRowHtml('', '', '', ''));
    });
    $(document).on('click', '.btn-rm-card-item', function() {
        $(this).closest('.card-item-row').remove();
    });

    // ── Experience items add/remove rows ──────────────────────────────────────
    $(document).on('click', '.btn-add-experience-item', function() {
        const tbody = $(this).closest('.section-card').find('.exp-items-tbody');
        tbody.append(experienceItemRowHtml('', ''));
    });
    $(document).on('click', '.btn-rm-experience-item', function() {
        $(this).closest('.experience-item-row').remove();
    });

    // ── Form submit ───────────────────────────────────────────────────────────
    window.submitBuilder = function(publish, btn) {
        // Client-side required-field check
        const titleEl = document.getElementById('pageTitle');
        const slugEl = document.getElementById('pageSlug');
        titleEl.classList.remove('is-invalid');
        slugEl.classList.remove('is-invalid');

        if (!titleEl.value.trim()) {
            titleEl.classList.add('is-invalid');
            titleEl.focus();
            toastr.error('Page title is required.');
            return;
        }
        if (!slugEl.value.trim()) {
            slugEl.classList.add('is-invalid');
            slugEl.focus();
            toastr.error('Page slug is required.');
            return;
        }

        if (typeof tinymce !== 'undefined') tinymce.triggerSave();

        // ── Alt tag validation: required when image is set ────────────────────
        let altError = false;
        const altPairs = [
            { img: '[data-key="banner_image"]', alt: '[data-key="banner_image_alt"]', label: 'Banner Image Alt Text' },
            { img: '[data-key="image"]',        alt: '[data-key="image_alt"]',        label: 'Background Image Alt Text' },
            { img: '[data-key="bg_image"]',     alt: '[data-key="bg_image_alt"]',     label: 'Background Image Alt Text' },
        ];
        sectionList.querySelectorAll('.section-card').forEach(card => {
            altPairs.forEach(({ img, alt, label }) => {
                const imgEl = card.querySelector(img);
                const altEl = card.querySelector(alt);
                if (imgEl?.value?.trim() && altEl && !altEl.value.trim()) {
                    altEl.classList.add('is-invalid');
                    card.querySelector('.section-card-body')?.classList.remove('d-none');
                    if (!altError) { toastr.error(`"${label}" is required when an image is set.`); altError = true; }
                }
            });
            card.querySelectorAll('.card-item-row').forEach(row => {
                const imgEl = row.querySelector('[data-card-key="image"]');
                const altEl = row.querySelector('[data-card-key="image_alt"]');
                if (imgEl?.value?.trim() && altEl && !altEl.value.trim()) {
                    altEl.classList.add('is-invalid');
                    card.querySelector('.section-card-body')?.classList.remove('d-none');
                    if (!altError) { toastr.error('Card image alt text is required.'); altError = true; }
                }
            });
        });
        if (altError) return;

        document.getElementById('isPublishedInput').value = publish;

        const sections = [];
        sectionList.querySelectorAll('.section-card').forEach((card) => {
            const section = {
                id: card.dataset.id || null,
                type: card.dataset.type,
                label: card.querySelector('[data-field="label"]')?.value ?? '',
                is_active: card.querySelector('[data-field="active"]')?.checked ? 1 : 0,
                content: {},
            };

            // Generic data-key fields
            card.querySelectorAll('[data-key]').forEach(el => {
                section.content[el.dataset.key] = el.value;
            });

            // FAQ items
            if (section.type === 'faq') {
                section.content.items = [];
                card.querySelectorAll('.faq-row').forEach(row => {
                    section.content.items.push({
                        question: row.querySelector('[data-key="question"]')?.value ?? '',
                        answer: row.querySelector('[data-key="answer"]')?.value ?? '',
                    });
                });
            }

            // Stats items
            if (section.type === 'stats') {
                section.content.items = [];
                card.querySelectorAll('.stat-row').forEach(row => {
                    section.content.items.push({
                        value: row.querySelector('[data-key="value"]')?.value ?? '',
                        label: row.querySelector('[data-key="label"]')?.value ?? '',
                        icon: row.querySelector('[data-key="icon"]')?.value ?? '',
                    });
                });
            }

            // Gallery images
            if (section.type === 'gallery') {
                section.content.images = [];
                card.querySelectorAll('.gallery-row').forEach(row => {
                    section.content.images.push({
                        src: row.querySelector('[data-key="src"]')?.value ?? '',
                        alt: row.querySelector('[data-key="alt"]')?.value ?? '',
                    });
                });
            }

            // Feature Card items
            if (section.type === 'cards') {
                section.content.cards = [];
                card.querySelectorAll('.card-item-row').forEach(row => {
                    section.content.cards.push({
                        image:            row.querySelector('[data-card-key="image"]')?.value     ?? '',
                        image_alt:        row.querySelector('[data-card-key="image_alt"]')?.value ?? '',
                        title:            row.querySelector('[data-card-key="title"]')?.value     ?? '',
                        description:      row.querySelector('[data-card-key="description"]')?.value ?? '',
                        license_source:   row.querySelector('[data-card-key="license_source"]')?.value  ?? '',
                        license_date:     row.querySelector('[data-card-key="license_date"]')?.value    ?? '',
                        license_account:  row.querySelector('[data-card-key="license_account"]')?.value ?? '',
                        license_key:      row.querySelector('[data-card-key="license_key"]')?.value      ?? '',
                    });
                });
            }

            // Experience items
            if (section.type === 'experience') {
                section.content.items = [];
                card.querySelectorAll('.experience-item-row').forEach(row => {
                    section.content.items.push({
                        icon:  row.querySelector('[data-exp-key="icon"]')?.value  ?? '',
                        title: row.querySelector('[data-exp-key="title"]')?.value ?? '',
                    });
                });
            }

            // Multi-select: member_ids, award_ids (comma-separated → array)
            ['member_ids', 'award_ids'].forEach(key => {
                if (section.content[key] !== undefined) {
                    const raw = section.content[key];
                    section.content[key] = raw ?
                        raw.split(',').map(v => parseInt(v)).filter(Boolean) : [];
                }
            });

            sections.push(section);
        });

        document.getElementById('sectionsJson').value = JSON.stringify(sections);

        // Show a loader on every trigger (top buttons + floating Publish) and lock
        // them all so the page can't be re-submitted while the request is in flight —
        // the full-page reload/redirect on completion naturally clears this state.
        document.querySelectorAll('.builder-submit-btn').forEach(b => b.disabled = true);
        if (btn) {
            btn.dataset.originalHtml = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>' + (publish ? 'Publishing…' : 'Saving…');
        }

        document.getElementById('builderForm').submit();
    };

    // ── Helpers ───────────────────────────────────────────────────────────────

    // Returns clean template HTML — strips TinyMCE artifacts from clones
    function getCleanTemplateHtml(type) {
        const tplEl = document.querySelector(`#sectionTemplates .section-tpl[data-type="${type}"]`);
        if (!tplEl) return null;
        const clone = tplEl.cloneNode(true);
        // Remove any TinyMCE-injected UI containers
        clone.querySelectorAll('.tox-tinymce, .tox-sidebar-wrap, .tox-tinymce-aux').forEach(e => e.remove());
        // The shared Media Library modal (#mediaPickerModal) is a page-wide singleton
        // rendered once via x-media-picker's "once" block — but any section type
        // that uses an image field carries its own baked-in copy inside this hidden
        // template bank. Cloning it into every section card would create duplicate
        // #mediaPickerModal ids, which breaks getElementById() lookups and leaves the
        // real, wired modal stuck (invisible) inside this display:none container.
        // Strip the clone's copy so only the one true instance ever exists in the DOM.
        clone.querySelectorAll('#mediaPickerModal').forEach(e => e.remove());
        clone.querySelectorAll('script').forEach(e => e.remove());
        // Restore textareas to virgin state so MutationObserver inits them fresh
        clone.querySelectorAll('textarea.tinymce').forEach(ta => {
            ta.style.cssText = '';
            ta.removeAttribute('data-tiny-init');
            ta.removeAttribute('data-mce-id');
            ta.removeAttribute('data-mce-placeholder');
            ta.removeAttribute('id');
            ta.value = '';
        });
        return clone.innerHTML;
    }

    function addSection(type, id) {
        const configHtml = getCleanTemplateHtml(type);
        if (configHtml === null) {
            toastr.error('Section type not found: ' + type);
            return;
        }

        const idx = ++sectionCounter;
        const card = buildCard(type, id, {});

        card.querySelector('.section-card-body').innerHTML = configHtml;

        // Assign unique IDs so MutationObserver inits TinyMCE fresh
        card.querySelectorAll('textarea.tinymce').forEach(ta => {
            ta.id = 'tinymce_' + idx + '_' + (ta.dataset.key || 'body');
        });

        sectionList.appendChild(card);
        updateEmptyState();
        lastAddedCard = card; // scroll happens after modal hidden.bs.modal fires
    }

    function renderSection(s) {
        const idx = ++sectionCounter;
        const card = buildCard(s.type, s.id, s.content);
        const configHtml = getCleanTemplateHtml(s.type);
        if (configHtml === null) return;

        card.querySelector('.section-card-body').innerHTML = configHtml;

        // Assign unique IDs to TinyMCE textareas AND pre-load content so
        // TinyMCE reads the value on init (avoids fragile setInterval timing)
        card.querySelectorAll('textarea.tinymce').forEach(ta => {
            ta.id = 'tinymce_' + idx + '_' + (ta.dataset.key || 'body');
            const key = ta.dataset.key;
            if (key && s.content && typeof s.content[key] === 'string') {
                ta.value = s.content[key];
            }
        });

        // Set label
        const labelEl = card.querySelector('[data-field="label"]');
        if (labelEl) labelEl.value = s.label ?? '';

        // Set active
        const activeEl = card.querySelector('[data-field="active"]');
        if (activeEl) activeEl.checked = !!s.is_active;

        if (!s.is_active) card.classList.add('opacity-50');

        sectionList.appendChild(card);

        // Fill remaining values (non-TinyMCE fields + image preview)
        fillCardContent(card, s.content || {});
    }

    function buildCard(type, id, content) {
        const info = SECTION_TYPES[type] || {
            label: type,
            icon: 'fa-cube',
            color: 'secondary'
        };
        const card = document.createElement('div');
        card.className = 'section-card';
        card.dataset.type = type;
        card.dataset.id = id || '';

        card.innerHTML = `
        <div class="section-card-header">
            <i class="fas fa-grip-vertical drag-handle"></i>
            <span class="section-type-badge bg-${info.color} text-white">${info.label}</span>
            <input type="text"
                   class="section-label-input"
                   data-field="label"
                   placeholder="Section label (admin only)"
                   value="">
            <div class="ms-auto d-flex align-items-center gap-2 flex-shrink-0">
                <div class="form-check form-switch mb-0" title="Enable/Disable">
                    <input class="form-check-input" type="checkbox" data-field="active" checked>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary section-toggle-btn" title="Expand/Collapse">
                    <i class="fas fa-chevron-up"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger section-delete-btn" title="Remove">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        <div class="section-card-body"></div>`;

        // Disable/enable toggle styling
        card.querySelector('[data-field="active"]').addEventListener('change', function() {
            card.classList.toggle('opacity-50', !this.checked);
        });

        return card;
    }

    function fillCardContent(card, content) {
        // Simple data-key fields
        Object.entries(content).forEach(([key, val]) => {
            if (Array.isArray(val) || typeof val === 'object') return;
            const el = card.querySelector(`[data-key="${key}"]`);
            if (!el) return;

            if (el.tagName === 'SELECT') {
                el.value = val;
                el.dispatchEvent(new Event('change'));
            } else if (el.tagName === 'TEXTAREA' && el.classList.contains('tinymce')) {
                // TinyMCE: set after editor initialises
                const check = setInterval(() => {
                    if (typeof tinymce === 'undefined') return;
                    const ed = tinymce.get(el.id);
                    if (ed) {
                        ed.setContent(val || '');
                        clearInterval(check);
                    }
                }, 200);
                setTimeout(() => clearInterval(check), 5000);
            } else {
                el.value = val;
            }
        });

        // Preview image (banner_image, image, bg_image) — generic over whichever
        // single media-picker field this section type happens to render. The
        // "data-key" attribute lands on the hidden .media-picker-value input
        // (see x-media-picker), not on the .media-picker-block wrapper itself —
        // querying the wrapper for it never matches, which is why the thumbnail
        // preview stayed empty after save/reload even though the hidden input's
        // value (restored by the generic loop above) was correct all along.
        card.querySelectorAll('.media-picker-value[data-key]').forEach(function(hidden) {
            const key = hidden.dataset.key;
            const raw = content[key];
            if (!raw) return;
            const path = toBarePath(raw);
            const url = normalizeImageUrl(raw);
            hidden.value = path;
            const block = hidden.closest('.media-picker-block');
            const img = block?.querySelector('.media-picker-preview img');
            if (img) img.src = url;
            const preview = block?.querySelector('.media-picker-preview');
            if (preview) preview.classList.remove('d-none');
        });

        // FAQ items
        if (content.items && card.querySelector('.faq-tbody')) {
            const tbody = card.querySelector('.faq-tbody');
            tbody.innerHTML = '';
            content.items.forEach((item, i) => {
                tbody.insertAdjacentHTML('beforeend', faqRowHtml(i, item.question, item.answer));
            });
            if (!content.items.length) {
                tbody.insertAdjacentHTML('beforeend', faqRowHtml(0, '', ''));
            }
        }

        // Stats items
        if (content.items && card.querySelector('.stats-tbody')) {
            const tbody = card.querySelector('.stats-tbody');
            tbody.innerHTML = '';
            content.items.forEach(item => {
                tbody.insertAdjacentHTML('beforeend', statRowHtml(item.value, item.label, item.icon));
            });
            if (!content.items.length) {
                tbody.insertAdjacentHTML('beforeend', statRowHtml('', '', ''));
            }
        }

        // Gallery images
        if (content.images && card.querySelector('.gallery-tbody')) {
            const tbody = card.querySelector('.gallery-tbody');
            tbody.innerHTML = '';
            content.images.forEach(img => {
                tbody.insertAdjacentHTML('beforeend', galleryRowHtml(img.src, img.alt));
            });
            if (!content.images.length) {
                tbody.insertAdjacentHTML('beforeend', galleryRowHtml('', ''));
            }
        }

        // Feature Card items
        if (Array.isArray(content.cards) && card.querySelector('.cards-tbody')) {
            const tbody = card.querySelector('.cards-tbody');
            tbody.innerHTML = '';
            content.cards.forEach(item => {
                tbody.insertAdjacentHTML('beforeend', cardItemRowHtml(item.image || '', item.image_alt || '', item.title || '', item.description || ''));
            });
        }

        // Experience items
        if (Array.isArray(content.items) && card.querySelector('.exp-items-tbody')) {
            const tbody = card.querySelector('.exp-items-tbody');
            tbody.innerHTML = '';
            content.items.forEach(item => {
                tbody.insertAdjacentHTML('beforeend', experienceItemRowHtml(item.icon || '', item.title || ''));
            });
        }

        // member_ids → select options + hidden input
        if (Array.isArray(content.member_ids)) {
            const select = card.querySelector('.team-member-select');
            if (select) {
                const vals = content.member_ids.map(String);
                Array.from(select.options).forEach(opt => { opt.selected = vals.includes(opt.value); });
            }
            const hidden = card.querySelector('.team-member-ids-hidden');
            if (hidden) hidden.value = content.member_ids.join(',');
        }
        if (Array.isArray(content.award_ids)) {
            const el = card.querySelector('[data-key="award_ids"]');
            if (el) el.value = content.award_ids.join(',');
        }
    }

    function updateEmptyState() {
        const hasSections = sectionList.querySelectorAll('.section-card').length > 0;
        emptyNotice.style.display = hasSections ? 'none' : 'block';
    }

    // Build a display URL from a stored path (relative or absolute).
    // Always uses STORAGE_URL so it works across dev/production regardless of subdirectory.
    function normalizeImageUrl(url) {
        if (!url) return url;
        // Strip to just the filename portion after /storage/
        let path = url;
        if (url.startsWith('http')) {
            try { path = new URL(url).pathname; } catch (e) {}
        }
        // path is now like /storage/cms_page/foo.webp or storage/cms_page/foo.webp
        path = path.replace(/^\/?storage\//, '');
        return STORAGE_URL + path;
    }

    // Strip a stored value (bare path, legacy full URL, or /storage/-prefixed
    // URL) down to the bare relative path the media-picker component's
    // hidden inputs expect.
    function toBarePath(url) {
        if (!url) return '';
        let path = url;
        if (url.startsWith('http')) {
            try { path = new URL(url).pathname; } catch (e) {}
        }
        return path.replace(/^\/?storage\//, '').replace(/^\//, '');
    }

    function faqRowHtml(i, q, a) {
        return `<div class="faq-row">
        <div class="row g-2 align-items-start">
            <div class="col-md-5">
                <input type="text" class="form-control form-control-sm" data-key="question" placeholder="Question" value="${escHtml(q)}">
            </div>
            <div class="col-md-6">
                <textarea class="form-control form-control-sm" data-key="answer" rows="2" placeholder="Answer">${escHtml(a)}</textarea>
            </div>
            <div class="col-md-1 text-end">
                <button type="button" class="btn btn-sm btn-outline-danger btn-rm-faq"><i class="fas fa-times"></i></button>
            </div>
        </div>
    </div>`;
    }

    function statRowHtml(val, lbl, icon) {
        return `<div class="stat-row">
        <div class="row g-2">
            <div class="col-4">
                <input type="text" class="form-control form-control-sm" data-key="value" placeholder="e.g. 25000+" value="${escHtml(val)}">
            </div>
            <div class="col-4">
                <input type="text" class="form-control form-control-sm" data-key="label" placeholder="Label" value="${escHtml(lbl)}">
            </div>
            <div class="col-3">
                <input type="text" class="form-control form-control-sm" data-key="icon" placeholder="fas fa-users" value="${escHtml(icon)}">
            </div>
            <div class="col-1 text-end">
                <button type="button" class="btn btn-sm btn-outline-danger btn-rm-stat"><i class="fas fa-times"></i></button>
            </div>
        </div>
    </div>`;
    }

    function galleryRowHtml(src, alt) {
        return `<div class="gallery-row">
        <div class="row g-2">
            <div class="col-7">
                <input type="text" class="form-control form-control-sm" data-key="src" placeholder="Image URL or S3 path" value="${escHtml(src)}">
            </div>
            <div class="col-4">
                <input type="text" class="form-control form-control-sm" data-key="alt" placeholder="Alt text" value="${escHtml(alt)}">
            </div>
            <div class="col-1 text-end">
                <button type="button" class="btn btn-sm btn-outline-danger btn-rm-gallery"><i class="fas fa-times"></i></button>
            </div>
        </div>
    </div>`;
    }

    function cardItemRowHtml(image, imageAlt, title, description) {
        const pickerId = 'cms_card_' + (cmsCardPickerSeq++);
        const pickerHtml = typeof window.mediaPickerFieldHtml === 'function'
            ? window.mediaPickerFieldHtml('_cms_card_image_' + pickerId, pickerId, '', 'cms_page', null, 'image')
            : '';
        const row = `<div class="card-item-row">
        <div class="row g-2 align-items-start">
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Image</label>
                ${pickerHtml}
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Image Alt <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-sm" data-card-key="image_alt" placeholder="Describe image" value="${escHtml(imageAlt)}">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Title</label>
                <input type="text" class="form-control form-control-sm" data-card-key="title" placeholder="Card title" value="${escHtml(title)}">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Description</label>
                <textarea class="form-control form-control-sm" data-card-key="description" rows="3" placeholder="Card description">${escHtml(description)}</textarea>
            </div>
            <div class="col-md-1 text-end">
                <label class="form-label small d-block">&nbsp;</label>
                <button type="button" class="btn btn-sm btn-outline-danger btn-rm-card-item"><i class="fas fa-times"></i></button>
            </div>
        </div>
    </div>`;
        if (image && typeof window.setMediaPickerValue === 'function') {
            const path = toBarePath(image);
            const url = normalizeImageUrl(image);
            setTimeout(function () { window.setMediaPickerValue(pickerId, path, url); }, 0);
        }
        return row;
    }

    function experienceItemRowHtml(icon, title) {
        return `<div class="experience-item-row">
            <div class="row g-2 align-items-center">
                <div class="col-md-4">
                    <input type="text" class="form-control form-control-sm" data-exp-key="icon"
                           placeholder="fas fa-car" value="${escHtml(icon)}">
                </div>
                <div class="col-md-7">
                    <input type="text" class="form-control form-control-sm" data-exp-key="title"
                           placeholder="Item title" value="${escHtml(title)}">
                </div>
                <div class="col-md-1 text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger btn-rm-experience-item">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>`;
    }

    function escHtml(str) {
        if (!str) return '';
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    @if(session('success'))
    toastr.success("{{ session('success') }}");
    @endif
</script>

@if($errors-> any())
<script>
    toastr.error("Please fix validation errors.");
</script>
@endif
@endsection
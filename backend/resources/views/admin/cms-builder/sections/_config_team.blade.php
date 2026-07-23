<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Heading</label>
        <input type="text" class="form-control form-control-sm" data-key="heading"
               value="{{ $content['heading'] ?? '' }}" placeholder="Meet Our Team">
    </div>
    <div class="col-md-6">
        <label class="form-label">Sub Heading</label>
        <input type="text" class="form-control form-control-sm" data-key="subheading"
               value="{{ $content['subheading'] ?? '' }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Filter</label>
        <select class="form-select form-select-sm team-filter-select" data-key="filter">
            <option value="all"      {{ ($content['filter'] ?? 'all') === 'all'      ? 'selected' : '' }}>All Members</option>
            <option value="selected" {{ ($content['filter'] ?? '')    === 'selected' ? 'selected' : '' }}>Selected Members</option>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Max Members <small class="text-muted">(0 = all)</small></label>
        <input type="number" class="form-control form-control-sm" data-key="limit"
               value="{{ $content['limit'] ?? '12' }}" min="0">
    </div>

    {{-- Members multi-select (visible when filter=selected) --}}
    <div class="col-12 team-members-group" style="{{ ($content['filter'] ?? '') === 'selected' ? '' : 'display:none' }}">
        <label class="form-label">Select Members</label>
        <select class="form-select form-select-sm team-member-select" data-key="member_ids_select" multiple>
            @foreach($teamMembers as $member)
            <option value="{{ $member->id }}"
                {{ in_array($member->id, $content['member_ids'] ?? []) ? 'selected' : '' }}>
                {{ $member->name }}
            </option>
            @endforeach
        </select>
        <input type="hidden" data-key="member_ids" class="team-member-ids-hidden"
               value="{{ implode(',', $content['member_ids'] ?? []) }}">
        <small class="text-muted">Hold Ctrl / Cmd to select multiple</small>
    </div>

    {{-- ── Add New Member ──────────────────────────────────────────────────── --}}
    <div class="col-12">
        <hr class="my-2">
        <div class="d-flex justify-content-between align-items-center">
            <span class="fw-semibold text-success small">
                <i class="fas fa-user-plus me-1"></i>Add New Team Member
            </span>
            <button type="button" class="btn btn-sm btn-outline-success btn-toggle-add-member">
                <i class="fas fa-chevron-down fa-xs"></i>
            </button>
        </div>
        <div class="add-member-form d-none mt-2 p-3 border rounded bg-light">
            <div class="row g-2">
                <div class="col-md-6">
                    <label class="form-label form-label-sm fw-medium">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-sm new-member-name" placeholder="Full name">
                </div>
                <div class="col-md-6">
                    <label class="form-label form-label-sm fw-medium">Department Tab <span class="text-danger">*</span></label>
                    <select class="form-select form-select-sm new-member-dep">
                        <option value="">— Select tab —</option>
                        @foreach($departments as $dep)
                        <option value="{{ $dep->id }}">{{ $dep->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label form-label-sm fw-medium">Designation <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-sm new-member-desc" placeholder="e.g. Managing Director">
                </div>
                <div class="col-md-6">
                    <label class="form-label form-label-sm">About <small class="text-muted">(optional)</small></label>
                    <input type="text" class="form-control form-control-sm new-member-about" placeholder="Short bio / paragraph">
                </div>
                <div class="col-12">
                    <x-media-picker name="new_member_profile_image" picker-id="cms_new_member_profile_image" folder="team" label="Profile Image (optional)" />
                </div>
                <div class="col-12 d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-success btn-create-team-member">
                        <i class="fas fa-plus me-1"></i>Save New Member
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary btn-cancel-add-member">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>

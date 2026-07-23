<div class="row g-3">
    <div class="col-12">
        <label class="form-label">Heading</label>
        <input type="text" class="form-control form-control-sm" data-key="heading"
               value="{{ $content['heading'] ?? '' }}" placeholder="Our Awards & Achievements">
    </div>
    <div class="col-md-4">
        <label class="form-label">Filter</label>
        <select class="form-select form-select-sm awards-filter-select" data-key="filter">
            <option value="all"      {{ ($content['filter'] ?? 'all')    === 'all'      ? 'selected' : '' }}>All Awards</option>
            <option value="latest"   {{ ($content['filter'] ?? '')       === 'latest'   ? 'selected' : '' }}>Latest First</option>
            <option value="selected" {{ ($content['filter'] ?? '')       === 'selected' ? 'selected' : '' }}>Selected Awards</option>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Max Awards <small class="text-muted">(0 = all)</small></label>
        <input type="number" class="form-control form-control-sm" data-key="limit"
               value="{{ $content['limit'] ?? '0' }}" min="0">
    </div>
    <div class="col-md-4">
        <div class="form-check mt-4">
            <input type="checkbox" class="form-check-input" data-key="show_year" value="1"
                   {{ ($content['show_year'] ?? true) ? 'checked' : '' }}>
            <label class="form-check-label">Show Award Year</label>
        </div>
    </div>

    {{-- Selected awards multi-select --}}
    <div class="col-12 awards-selected-group" style="{{ ($content['filter'] ?? '') === 'selected' ? '' : 'display:none' }}">
        <label class="form-label">Select Awards</label>
        <select class="form-select form-select-sm select2" data-key="award_ids" multiple>
            @foreach($awards as $award)
            <option value="{{ $award->id }}"
                {{ in_array($award->id, $content['award_ids'] ?? []) ? 'selected' : '' }}>
                {{ $award->title }} ({{ $award->award_year }})
            </option>
            @endforeach
        </select>
        <input type="hidden" data-key="award_ids" value="{{ implode(',', $content['award_ids'] ?? []) }}">
    </div>
</div>

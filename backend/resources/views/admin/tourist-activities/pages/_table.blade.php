<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th width="90">Type</th>
                        <th>Region / State</th>
                        <th width="110">Status</th>
                        <th width="100">Featured</th>
                        <th width="140">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pages as $i => $tp)
                    @php $isCity = (bool) $tp->location_id; @endphp
                    <tr id="tac-page-row-{{ $tp->id }}">
                        <td class="fw-medium">{{ $isCity ? ($tp->location->name ?? '—') : ($tp->state->name ?? '—') }}</td>
                        <td>
                            <span class="badge {{ $isCity ? 'bg-purple' : 'bg-orange' }}">{{ $isCity ? 'City' : 'State' }}</span>
                        </td>
                        <td>{{ $isCity ? ($tp->location->state->name ?? '—') : ($tp->state->region->name ?? '—') }}</td>
                        <td>
                            <input type="checkbox"
                                   class="js-switch tac-page-status"
                                   data-url="{{ route('admin.tourist-activity-pages.toggle-status', $tp->id) }}"
                                   {{ $tp->is_active ? 'checked' : '' }}>
                        </td>
                        <td>
                            <input type="checkbox"
                                   class="js-switch tac-page-featured"
                                   data-url="{{ route('admin.tourist-activity-pages.toggle-featured', $tp->id) }}"
                                   {{ $tp->is_featured ? 'checked' : '' }}>
                        </td>
                        <td class="text-nowrap">
                            <button class="btn btn-sm btn-outline-orange btn-tac-settings"
                                    data-fetch="{{ route('admin.tourist-activity-pages.show', $tp->id) }}"
                                    data-update="{{ route('admin.tourist-activity-pages.update-section', $tp->id) }}"
                                    title="Banner & Settings">
                                <i class="fas fa-cog"></i>
                            </button>
                            @unless($isCity)
                            <button class="btn btn-sm btn-outline-success btn-tac-experiences"
                                    data-fetch="{{ route('admin.tourist-activity-pages.show', $tp->id) }}"
                                    data-update="{{ route('admin.tourist-activity-pages.update-section', $tp->id) }}"
                                    title="Popular Experience">
                                <i class="fas fa-compass"></i>
                            </button>
                            @endunless
                            <button class="btn btn-sm btn-outline-info btn-tac-waterfalls"
                                    data-fetch="{{ route('admin.tourist-activity-pages.show', $tp->id) }}"
                                    data-update="{{ route('admin.tourist-activity-pages.update-section', $tp->id) }}"
                                    title="Explore Waterfalls">
                                <i class="fas fa-water"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-primary btn-tac-things-to-do"
                                    data-fetch="{{ route('admin.tourist-activity-pages.show', $tp->id) }}"
                                    data-update="{{ route('admin.tourist-activity-pages.update-section', $tp->id) }}"
                                    title="Top Things To Do">
                                <i class="fas fa-list-check"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-warning btn-tac-faq"
                                    data-fetch="{{ route('admin.tourist-activity-pages.show', $tp->id) }}"
                                    data-update="{{ route('admin.tourist-activity-pages.update-section', $tp->id) }}"
                                    title="FAQs">
                                <i class="fas fa-question-circle"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-purple btn-tac-meta"
                                    data-fetch="{{ route('admin.tourist-activity-pages.show', $tp->id) }}"
                                    data-update="{{ route('admin.tourist-activity-pages.update-section', $tp->id) }}"
                                    title="SEO Meta">
                                <i class="fas fa-globe"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-tac-page"
                                    data-url="{{ route('admin.tourist-activity-pages.destroy', $tp->id) }}"
                                    title="Delete">
                                <i class="fas fa-trash icon"></i>
                                <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-5">No state/city activity pages found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($pages->lastPage() > 1)
    <div class="card-footer">
        @include('admin.common.pagination', ['paginator' => $pages])
    </div>
    @endif
</div>

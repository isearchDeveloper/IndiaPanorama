<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th width="90">Type</th>
                        <th>Region / State</th>
                        <th width="110">Status</th>
                        <th width="100">Popular</th>
                        <th width="140">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pages as $i => $tp)
                    @php $isCity = (bool) $tp->location_id; @endphp
                    <tr id="ta-page-row-{{ $tp->id }}">
                        <td><img src="{{ storage_link($tp->banner_image) }}" width="100"></td>
                        <td class="fw-medium">{{ $isCity ? ($tp->location->name ?? '—') : ($tp->state->name ?? '—') }}</td>
                        <td>
                            <span class="badge {{ $isCity ? 'bg-purple' : 'bg-orange' }}">{{ $isCity ? 'City' : 'State' }}</span>
                        </td>
                        <td>{{ $isCity ? ($tp->location->state->name ?? '—') : ($tp->state->region->name ?? '—') }}</td>
                        <td>
                            <input type="checkbox"
                                   class="js-switch ta-page-status"
                                   data-url="{{ route('admin.tourist-attraction-pages.toggle-status', $tp->id) }}"
                                   {{ $tp->is_active ? 'checked' : '' }}>
                        </td>
                        <td>
                            <input type="checkbox"
                                   class="js-switch ta-page-popular"
                                   data-url="{{ route('admin.tourist-attraction-pages.toggle-popular', $tp->id) }}"
                                   {{ $tp->is_popular ? 'checked' : '' }}>
                        </td>
                        <td class="text-nowrap">
                            <button class="btn btn-sm btn-outline-orange btn-tap-settings"
                                    data-fetch="{{ route('admin.tourist-attraction-pages.show', $tp->id) }}"
                                    data-update="{{ route('admin.tourist-attraction-pages.update-section', $tp->id) }}"
                                    title="Banner & Settings">
                                <i class="fas fa-cog"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-info btn-tap-best-times"
                                    data-fetch="{{ route('admin.tourist-attraction-pages.show', $tp->id) }}"
                                    data-update="{{ route('admin.tourist-attraction-pages.update-section', $tp->id) }}"
                                    title="Best Time To Visit">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-warning btn-tap-faq"
                                    data-fetch="{{ route('admin.tourist-attraction-pages.show', $tp->id) }}"
                                    data-update="{{ route('admin.tourist-attraction-pages.update-section', $tp->id) }}"
                                    title="FAQs">
                                <i class="fas fa-question-circle"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-purple btn-tap-meta"
                                    data-fetch="{{ route('admin.tourist-attraction-pages.show', $tp->id) }}"
                                    data-update="{{ route('admin.tourist-attraction-pages.update-section', $tp->id) }}"
                                    title="SEO Meta">
                                <i class="fas fa-globe"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-ta-page"
                                    data-url="{{ route('admin.tourist-attraction-pages.destroy', $tp->id) }}"
                                    title="Delete">
                                <i class="fas fa-trash icon"></i>
                                <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-5">No state/city attraction pages found.</td></tr>
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

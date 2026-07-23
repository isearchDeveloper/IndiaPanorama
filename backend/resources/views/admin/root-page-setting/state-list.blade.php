<div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>State Name</th>
                <th>Region</th>
                <th>Cities</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($states as $i => $state)
            <tr>
                <td>{{ $states->firstItem() + $i }}</td>
                <td class="fw-medium">{{ $state->name }}</td>
                <td>{{ $state->region->name ?? '—' }}</td>
                <td>{{ $state->cities_count ?? $state->cities()->count() }}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary btn-state-page"
                            data-id="{{ $state->id }}"
                            data-fetch="{{ route('admin.location-setting.states.show', $state->id) }}"
                            data-update="{{ route('admin.location-setting.states.update', $state->id) }}"
                            title="Page Settings">
                        <i class="fas fa-cog"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-info btn-state-meta"
                            data-id="{{ $state->id }}"
                            data-fetch="{{ route('admin.location-setting.states.show', $state->id) }}"
                            data-update="{{ route('admin.location-setting.states.update', $state->id) }}"
                            title="SEO Meta">
                        <i class="fas fa-globe"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-warning btn-state-faq"
                            data-id="{{ $state->id }}"
                            data-fetch="{{ route('admin.location-setting.states.show', $state->id) }}"
                            data-update="{{ route('admin.location-setting.states.faq', $state->id) }}"
                            title="FAQs">
                        <i class="fas fa-question-circle"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-dark btn-state-besttime"
                            data-id="{{ $state->id }}"
                            data-fetch="{{ route('admin.location-setting.states.show', $state->id) }}"
                            data-update="{{ route('admin.location-setting.states.best-time', $state->id) }}"
                            title="Best Time to Visit">
                        <i class="fas fa-calendar-alt"></i>
                    </button>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center text-muted py-4">No states found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@if($states->lastPage() > 1)
<div class="p-3">
    @include('admin.common.pagination', ['paginator' => $states->appends(['tab' => 'states'])])
</div>
@endif

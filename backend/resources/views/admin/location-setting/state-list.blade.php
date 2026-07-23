<div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead class="table-light">
            <tr>
                <th width="90">Image</th>
                <th>State Name</th>
                <th>Region</th>
                <th>Cities</th>
                <th>Packages</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($states as $i => $state)
            <tr>
                <td><img src="{{ storage_link($state->details->banner_image ?? null) }}" width="70" style="object-fit:cover;aspect-ratio:1/1;border-radius:6px;"></td>
                <td class="fw-medium">{{ $state->name }}</td>
                <td>{{ $state->region->name ?? '—' }}</td>
                <td>{{ $state->cities_count ?? $state->cities()->count() }}</td>
                <td>{{ $state->packages_count }}</td>
                <td>
                    <input type="checkbox" class="js-switch state-status-toggle"
                           data-id="{{ $state->id }}"
                           {{ $state->is_active ? 'checked' : '' }}>
                </td>
                <td>
                    <button class="btn btn-sm btn-outline-secondary btn-edit-state" data-id="{{ $state->id }}" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
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
                    <a href="{{ config('app.frontend_url') }}/{{ $state->slug }}/tour-packages" target="_blank"
                       class="btn btn-sm btn-outline-success" title="View Tour Packages">
                        <i class="fas fa-tv"></i>
                    </a>
                    <button class="btn btn-sm btn-outline-danger btn-delete-state"
                            data-id="{{ $state->id }}" data-name="{{ $state->name }}" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center text-muted py-4">No states found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@if($states->lastPage() > 1)
<div class="p-3">
    @include('admin.common.pagination', ['paginator' => $states->appends(['tab' => 'states'])])
</div>
@endif

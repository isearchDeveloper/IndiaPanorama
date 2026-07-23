<div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead class="table-light">
            <tr>
                <th width="90">Image</th>
                <th>City Name</th>
                <th>State</th>
                <th>Packages</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($locations as $i => $loc)
            <tr>
                <td><img src="{{ storage_link($loc->details->banner_image ?? null) }}" width="70" style="object-fit:cover;aspect-ratio:1/1;border-radius:6px;"></td>
                <td class="fw-medium">{{ $loc->name }}</td>
                <td>{{ $loc->state->name ?? '—' }}</td>
                <td>{{ $loc->packages_count }}</td>
                <td>
                    <input type="checkbox" class="js-switch city-status-toggle"
                           data-id="{{ $loc->id }}"
                           data-url="{{ route('admin.location-setting.cities.toggle-status', $loc->id) }}"
                           {{ $loc->is_active ? 'checked' : '' }}>
                </td>
                <td>
                    <button class="btn btn-sm btn-outline-secondary btn-edit-city"
                            data-url="{{ route('admin.location-setting.cities.show', $loc->id) }}"
                            data-upurl="{{ route('admin.location-setting.cities.update', $loc->id) }}"
                            title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-primary btn-city-page"
                            data-url="{{ route('admin.location-setting.cities.show', $loc->id) }}"
                            data-upurl="{{ route('admin.location-setting.cities.update', $loc->id) }}"
                            title="Page Settings">
                        <i class="fas fa-cog"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-info btn-city-meta"
                            data-url="{{ route('admin.location-setting.cities.meta', $loc->id) }}"
                            data-upurl="{{ route('admin.location-setting.cities.update', $loc->id) }}"
                            title="SEO Meta">
                        <i class="fas fa-globe"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-warning btn-city-faq"
                            data-id="{{ $loc->id }}"
                            data-faqurl="{{ route('admin.location-setting.cities.faq', $loc->id) }}"
                            title="FAQs">
                        <i class="fas fa-question-circle"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-dark btn-city-besttime"
                            data-id="{{ $loc->id }}"
                            data-besttimeurl="{{ route('admin.location-setting.cities.best-time', $loc->id) }}"
                            title="Best Time to Visit">
                        <i class="fas fa-calendar-alt"></i>
                    </button>
                    @if($loc->packages_count > 0)
                    <a href="https://www.indianpanorama.in/india/{{ $loc->slug }}" target="_blank"
                       class="btn btn-sm btn-outline-success" title="Preview">
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                    @endif
                    @if($loc->state)
                    <a href="{{ config('app.frontend_url') }}/{{ $loc->state->slug }}/{{ $loc->slug }}/tour-packages" target="_blank"
                       class="btn btn-sm btn-outline-success" title="View Tour Packages">
                        <i class="fas fa-tv"></i>
                    </a>
                    @endif
                    <button class="btn btn-sm btn-outline-danger btn-delete-city"
                            data-url="{{ route('admin.location-setting.cities.destroy', $loc->id) }}"
                            data-name="{{ $loc->name }}"
                            title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center text-muted py-4">No cities found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@if($locations->lastPage() > 1)
<div class="p-3">
    @include('admin.common.pagination', ['paginator' => $locations->appends(['tab' => 'cities'])])
</div>
@endif

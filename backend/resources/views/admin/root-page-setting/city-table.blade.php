<div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>City Name</th>
                <th>State</th>
                <th>Packages</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($locations as $i => $loc)
            <tr>
                <td>{{ $locations->firstItem() + $i }}</td>
                <td class="fw-medium">{{ $loc->name }}</td>
                <td>{{ $loc->state->name ?? '—' }}</td>
                <td>{{ $loc->packages_location_count + $loc->packages_source_count }}</td>
                <td>
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
                    @if(($loc->packages_location_count + $loc->packages_source_count) > 0)
                    <a href="https://www.indianpanorama.in/india/{{ $loc->slug }}" target="_blank"
                       class="btn btn-sm btn-outline-success" title="Preview">
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center text-muted py-4">No cities found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@if($locations->lastPage() > 1)
<div class="p-3">
    @include('admin.common.pagination', ['paginator' => $locations->appends(['tab' => 'cities'])])
</div>
@endif

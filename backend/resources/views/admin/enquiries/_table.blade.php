<div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead class="table-light">
            <tr>
                <th>Type</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Message</th>
                <th>IP Address</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($enquiries as $enquiry)
            <tr>
                <td class="fw-medium">{{ $enquiry->enquiry_type }}</td>
                <td>{{ $enquiry->name }}</td>
                <td>{{ $enquiry->email }}</td>
                <td>{{ $enquiry->phone }}</td>
                <td class="small text-muted" style="max-width:260px;">{{ \Illuminate\Support\Str::limit($enquiry->message ?? '', 80) }}</td>
                <td class="small text-muted">{{ $enquiry->ip_address ?? '—' }}</td>
                <td>
                    <select class="form-select form-select-sm enquiry-status" data-id="{{ $enquiry->id }}"
                            data-url="{{ route('admin.enquiries.update-status', $enquiry->id) }}">
                        @foreach(\App\Models\Enquiry::STATUSES as $status)
                        <option value="{{ $status }}" {{ $enquiry->status === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </td>
                <td class="small text-muted text-nowrap">{{ $enquiry->created_at->format('d M Y, h:i A') }}</td>
                <td class="text-nowrap">
                    <button class="btn btn-sm btn-outline-primary view-enquiry"
                            data-url="{{ route('admin.enquiries.show', $enquiry->id) }}"
                            title="View">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger delete-enquiry"
                            data-url="{{ route('admin.enquiries.destroy', $enquiry->id) }}"
                            title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
            @empty
            <tr><td colspan="9" class="text-center text-muted py-5">No enquiries found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@if($enquiries->lastPage() > 1)
<div class="card-footer">
    @include('admin.common.pagination', ['paginator' => $enquiries])
</div>
@endif

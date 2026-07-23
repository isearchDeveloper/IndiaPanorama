@section('title', 'Enquiries')
@extends('layouts.app')

@php
    $tabs = [
        'holidays'    => ['label' => 'Holidays',    'icon' => 'fa-suitcase-rolling'],
        'experiences' => ['label' => 'Experiences',  'icon' => 'fa-compass'],
        'destination' => ['label' => 'Destination',  'icon' => 'fa-map-marked-alt'],
        'activities'  => ['label' => 'Activities',   'icon' => 'fa-hiking'],
        'car_rental'  => ['label' => 'Car Rental',   'icon' => 'fa-car'],
        'general'     => ['label' => 'General',      'icon' => 'fa-inbox'],
    ];
@endphp

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-0 fw-bold"><i class="fas fa-envelope-open-text me-2 text-primary"></i>Enquiries</h2>
            <small class="text-muted">Customer enquiries submitted from the website, grouped by module.</small>
        </div>
    </div>

    <ul class="nav nav-tabs mb-3" id="enquiryTabs" role="tablist">
        @foreach($tabs as $key => $tab)
        <li class="nav-item" role="presentation">
            <button class="nav-link enquiry-tab-btn {{ $category === $key ? 'active' : '' }}" data-category="{{ $key }}" type="button">
                <i class="fas {{ $tab['icon'] }} me-1"></i> {{ $tab['label'] }}
                <span class="badge bg-secondary ms-1">{{ $counts[$key] ?? 0 }}</span>
            </button>
        </li>
        @endforeach
    </ul>

    <div class="mb-3">
        <div class="position-relative" style="max-width:320px;">
            <i class="fas fa-search position-absolute" style="left:12px; top:50%; transform:translateY(-50%); color:#94a3b8; font-size:.85rem;"></i>
            <input type="text" id="enquirySearch" class="form-control" style="padding-left:32px;"
                   placeholder="Search name, email, phone..." value="{{ request('search') }}">
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0" id="enquiryTableWrapper">
            @include('admin.enquiries._table')
        </div>
    </div>

</div>
@endsection

@section('modal')
<div class="modal fade" id="viewEnquiryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-eye me-2"></i>Enquiry Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-muted small">Type</div>
                        <div class="fw-medium" id="ve-type"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Category</div>
                        <div class="fw-medium" id="ve-category"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Name</div>
                        <div class="fw-medium" id="ve-name"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Status</div>
                        <div class="fw-medium" id="ve-status"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Email</div>
                        <div class="fw-medium" id="ve-email"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Phone</div>
                        <div class="fw-medium" id="ve-phone"></div>
                    </div>
                    <div class="col-12">
                        <div class="text-muted small">Message</div>
                        <div class="fw-medium" id="ve-message" style="white-space:pre-wrap;"></div>
                    </div>

                    <div class="col-12 d-none" id="ve-trip-section">
                        <hr class="my-1">
                        <h6 class="text-muted mb-2 mt-2"><i class="fas fa-route me-1"></i>Trip Details</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="text-muted small">Country</div>
                                <div class="fw-medium" id="ve-country"></div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted small">Budget</div>
                                <div class="fw-medium" id="ve-budget"></div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted small">No. of Persons</div>
                                <div class="fw-medium" id="ve-persons"></div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted small">Travel Date</div>
                                <div class="fw-medium" id="ve-travel-date"></div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted small">Duration</div>
                                <div class="fw-medium" id="ve-duration"></div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted small">Arrival City</div>
                                <div class="fw-medium" id="ve-arrival"></div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted small">Departure City</div>
                                <div class="fw-medium" id="ve-departure"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="text-muted small">Source Page</div>
                        <div class="fw-medium text-break" id="ve-source"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">IP Address</div>
                        <div class="fw-medium" id="ve-ip"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Submitted On</div>
                        <div class="fw-medium" id="ve-date"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function () {

    var activeCategory = '{{ $category }}';

    function reload() {
        showAjaxLoader($('#enquiryTableWrapper'));
        $.get('{{ route("admin.enquiries.index") }}', { category: activeCategory, search: $('#enquirySearch').val(), ajax: 1 })
            .done(function (res) { $('#enquiryTableWrapper').html(res.html); })
            .fail(function () { hideAjaxLoader($('#enquiryTableWrapper')); toastr.error('Failed to load enquiries.'); });
    }

    $('.enquiry-tab-btn').on('click', function () {
        activeCategory = $(this).data('category');
        $('.enquiry-tab-btn').removeClass('active');
        $(this).addClass('active');
        reload();
    });

    let searchTimer;
    $('#enquirySearch').on('input', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(reload, 300);
    });

    $(document).on('change', '.enquiry-status', function () {
        var $sel = $(this);
        $.ajax({
            url: $sel.data('url'), type: 'POST',
            data: { status: $sel.val(), _token: '{{ csrf_token() }}' },
        }).done(function () { toastr.success('Status updated.'); })
          .fail(function () { toastr.error('Failed to update status.'); });
    });

    $(document).on('click', '.view-enquiry', function () {
        let btn = $(this);
        $.get(btn.data('url'))
            .done(function (d) {
                $('#ve-type').text(d.enquiry_type || '—');
                $('#ve-category').text((d.category || '').replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase()) || '—');
                $('#ve-name').text(d.name || '—');
                $('#ve-status').html('<span class="badge bg-secondary">' + (d.status ? d.status.charAt(0).toUpperCase() + d.status.slice(1) : '—') + '</span>');
                $('#ve-email').text(d.email || '—');
                $('#ve-phone').text(d.phone || '—');
                $('#ve-message').text(d.message || '—');

                let hasTripDetails = d.country || d.budget || d.no_of_persons || d.travel_date || d.duration || d.arrival_city || d.departure_city;
                $('#ve-trip-section').toggleClass('d-none', !hasTripDetails);
                $('#ve-country').text(d.country || '—');
                $('#ve-budget').text(d.budget || '—');
                $('#ve-persons').text(d.no_of_persons || '—');
                $('#ve-travel-date').text(d.travel_date || '—');
                $('#ve-duration').text(d.duration || '—');
                $('#ve-arrival').text(d.arrival_city || '—');
                $('#ve-departure').text(d.departure_city || '—');

                $('#ve-source').text(d.source_url || '—');
                $('#ve-ip').text(d.ip_address || '—');
                $('#ve-date').text(d.created_at || '—');
                bootstrap.Modal.getOrCreateInstance(document.getElementById('viewEnquiryModal')).show();
            })
            .fail(function () { toastr.error('Failed to load enquiry details.'); });
    });

    $(document).on('click', '.delete-enquiry', function () {
        let btn = $(this);
        let row = btn.closest('tr');
        Swal.fire({
            title: 'Are you sure?',
            text: 'This enquiry will be permanently deleted.',
            icon: 'warning', showCancelButton: true,
            confirmButtonColor: '#e3342f', cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: btn.data('url'), type: 'DELETE', data: { _token: '{{ csrf_token() }}' },
                    success: function (res) { if (res.status) { row.remove(); toastr.success('Enquiry deleted.'); } },
                    error: function () { toastr.error('Failed to delete enquiry.'); }
                });
            }
        });
    });

});
</script>
@endsection

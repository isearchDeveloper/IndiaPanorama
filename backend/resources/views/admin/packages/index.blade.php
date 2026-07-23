@php
    $assignedColors = [];
@endphp
@section('title','Packages')
@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2">
                    <i class="fas fa-box me-2"></i>Packages
                </h1>
                <a href="{{ route('admin.packages.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Create Package
                </a>
            </div>
            <div class="mb-3">
                <input type="text"  id="packageSearch" class="form-control" placeholder="Search...">
            </div>
            
            
            
            <div class="tab-list mb-2">
                        <ul>
                            <li>
                                <a href="javascript:void(0);" 
                                   class="tab-link @if(!Request::has('status') || Request::get('status') == 'all') active @endif" 
                                   data-status="all">
                                    All ({{ $allCount }})
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" class="tab-link @if(Request::get('status') == 'active') active @endif " data-status="active">
                                    Active ({{ $activeCount }})
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" class="tab-link @if(Request::get('status') == 'inactive') active @endif " data-status="inactive">
                                    Inactive ({{ $inactiveCount }})
                                </a>
                            </li>
                        </ul>
            </div>

            


        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div id="packageList">
                        @include('admin.packages.list', ['packages' => $packages])
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('modal')
<div class="modal fade" id="viewPackageModal1" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Title:</strong></p>
                        <p><strong>Type:</strong></p>
                        <p><strong>Location:</strong></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Category:</strong></p>
                        <p><strong>Duration:</strong></p>
                        <p><strong>Top Trending Package:</strong></p>
                        <p><strong>Source Location:</strong></p>
                        
                    </div>
                    <div class="col-md-12">
                        <p><strong>Tour Highlights:</strong></p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="fw-bold">Short Description</h6>
                        <p></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold">Long Description</h6>
                        <p></p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <h6 class="fw-bold">Gallery</h6>
                    <div class="col-md-3 text-center">
                        <div class="card mb-3">
                            <img src="" class="card-img-top img-fluid" style="height:150px; object-fit:cover;">
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <h6 class="fw-bold">Itinerary</h6>
                    <div class="mb-2">
                        <strong></strong>
                        <p class="text-muted mb-1"></p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="fw-bold">Inclusions</h6>
                        <p></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold">Exclusions</h6>
                        <p></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="faqModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="city-title">Faqs</h5>
                <button type="button" class="btn-close btn-close-white close-modal" data-bs-dismiss="modal"></button>
            </div>
            <form id="faqForm" method="POST" action="" enctype="multipart/form-data">
            @csrf 
            @method('PUT')
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondaryclose-modal close-modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning" id="update-page-setting-btn" data-url="">
                        <i class="fas fa-save me-2"></i>Save Faqs
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="packageMetaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="package-title">Edit Meta Info</h5>
                <button type="button" class="btn-close btn-close-white close-modal" data-bs-dismiss="modal"></button>
            </div>
            <form id="packageMeta" method="POST" action="" enctype="multipart/form-data">
            @csrf 
            @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="meta_setting">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="package_meta_title" class="form-label">Meta Title</label>
                                <input type="text" class="form-control" id="package_meta_title" name="meta_title">
                                <div class="text-danger d-none" id="package-meta-title-error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="package_meta_description" class="form-label">Meta Description</label>
                                <input type="text" class="form-control" id="package_meta_description" name="meta_description">
                                <div class="text-danger d-none" id="package-meta-description-error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="package_meta_keywords" class="form-label">Meta Keywords</label>
                                <input type="text" class="form-control" id="package_meta_keywords" name="meta_keywords">
                                <div class="text-danger d-none" id="package-meta-keyords-error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="package_h1_heading" class="form-label">H1 Heading</label>
                                <input type="text" class="form-control" id="package_h1_heading" name="h1_heading">
                                <div class="text-danger d-none" id="package-meta-keyords-error"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <label for="package_meta_details" class="form-label">Extra Meta Tag</label>
                            <textarea class="form-control"  name="meta_details" id="package_meta_details" rows="5" id="meta_details"></textarea>
                            <div class="text-danger d-none" id="package-meta-details-error"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondaryclose-modal close-modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning" id="update-package-meta-btn">
                        <i class="fas fa-save me-2"></i>Update Meta Setting
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const searchInput = $('#packageSearch');
    const pckageList = $('#packageList');
    let timer;
    let currentStatus = 'all';

    function fetchPackages(url = "{{ route('admin.packages.index') }}") {
        const keyword = searchInput.val().trim();

        const fullUrl = new URL(url, window.location.origin);

        if (keyword) {
            fullUrl.searchParams.set('package', keyword);
        } else {
            fullUrl.searchParams.delete('package');
        }

        if (currentStatus && currentStatus !== 'all') {
            fullUrl.searchParams.set('status', currentStatus);
        } else {
            fullUrl.searchParams.delete('status');
        }
        window.history.pushState({}, '', fullUrl.toString());

        $.ajax({
            url: url,
            type: 'GET',
            data: {
                package: keyword,
                status: currentStatus
            },
            dataType: 'html',
            beforeSend: function() {
                pckageList.addClass('loading');
            },
            success: function(response) {
                pckageList.html(response);
            },
            complete: function() {
                initSwitchery();
                pckageList.removeClass('loading');
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }
    searchInput.on('input', function() {
        clearTimeout(timer);
        timer = setTimeout(() => {
            fetchPackages();
        }, 300);
    });
    $(document).on('click', '.tab-link', function () {
        $('.tab-link').removeClass('active');
        $(this).addClass('active');

        currentStatus = $(this).data('status'); // all | active | inactive
        fetchPackages();
    });

});
</script>

<script>
    let faqIndex = 0;
    $(document).ready(function () {
        
        @if(session('success'))
            toastr.success("{{ session('success') }}", 'Success');
        @endif
        $(document).on('change', '.package-status', function() {
            let package_status = $(this).prop('checked') === true ? 1 : 0;
            $.ajax({
                type: "PUT",
                dataType: "json",
                url: $(this).data('url'),
                data: {
                    'status': package_status
                },
                success: function(data) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: data.message
                    });
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while processing your request.'
                    });
                }

            });
        });


        

        $(document).on('click', '.package-details', function() {
            let btn = $(this);
            btn.find('.spinner-border').removeClass('d-none');
            btn.find('.icon').addClass('d-none');
            let id = btn.data('id');
            $.ajax({
                type: "GET",
                dataType: "json",
                url: "{{ route('admin.packages.index') }}",
                data: { id: id },
                // success: function(data) {
                //     data = data.package;
                //     $('#viewPackageModal1 .modal-title').text(data.title);

                //     let body = `
                //         <div class="row">
                //             <div class="col-md-6">
                //                 <p><strong>Title:</strong> ${data.title}</p>
                //                 <p><strong>Type:</strong> ${data.type == 1 ? 'India Tour Package' :'International Tour Package'}</p>
                //                 <p><strong>Featured Package:</strong> ${data.is_featured ? 'Yes' : 'No'}</p>
                //                 <p><strong>Destination Location:</strong> ${data.location.name+','+data.location.country.name+','+data.location.country.continent.name}</p>
                //             </div>
                //             <div class="col-md-6">
                //                 <p><strong>Category:</strong> ${data.category.name}</p>
                //                 <p><strong>Duration:</strong> ${data.details.duration_days+(data.details.duration_days > 1 ?' days/' : 'day/')+data.details.duration_nights+(data.details.duration_nights > 1? ' nights' :' night')}</p>
                //                 <p><strong>Top Trending Package:</strong> ${data.is_top_trending ? 'Yes' : 'No'}</p>
                //                 <p><strong>Source Location:</strong> ${data.source_location.name+','+data.source_location.country.name+','+data.source_location.country.continent.name}</p>
                //             </div>
                //             <div class="col-md-12">
                //                 <p><strong>Date:</strong> ${data.details.start_date ? data.details.start_date+' to '+ data.details.start_date : '----' } </p>
                //             </div>
                //             <div class="col-md-12">
                //                 <p><strong>Tour Highlights:</strong> ${data.details.tour_highlights ? data.details.tour_highlights : '----'}</p>
                //             </div>
                //             <div class="col-md-12">
                //                 <p><strong>Facilities:</strong> ${data.details.facilities ? 
                //                     ((Array.isArray(data.details.facilities) && data.details.facilities.length > 0)
                //                         ? data.details.facilities
                //                             .map(f => f.replace(/\b\w/g, c => c.toUpperCase())) // Title Case each word
                //                             .join(', ')
                //                         : (typeof data.details.facilities === "string"
                //                             ? data.details.facilities.replace(/\b\w/g, c => c.toUpperCase())
                //                             : '----'))
                //                     : '-----'
                //                 }</p>
                //             </div>

                //             <div class="col-md-12">
                //                 <p><strong>Route Details:</strong> ${data.details.route_details}</p>
                //             </div>
                //         </div>
                //         <hr>
                //         <div class="row">
                //             <div class="col-md-6">
                //                 <h6 class="fw-bold">Short Description</h6>
                //                 <p>${data.short_description}</p>
                //             </div>
                //             <div class="col-md-6">
                //                 <h6 class="fw-bold">Long Description</h6>
                //                 <p>${data.long_description}</p>
                //             </div>
                //         </div>
                //         <hr>
                //         <div class="row">
                //             <h6 class="fw-bold">Gallery</h6>
                //             <div class="col-md-12 text-center">
                //                 <div class="card mb-3">
                //                     <img src="${s3BaseUrl+data.primary_image}" class="card-img-top img-fluid" style="height:150px; object-fit:cover;">
                //                 </div>
                //             </div>
                //             ${data.images.map(img => `
                //                 <div class="col-md-3 text-center">
                //                     <div class="card mb-3">
                //                         <img src="${s3BaseUrl+img.image_path}" class="card-img-top img-fluid" style="height:150px; object-fit:cover;">
                //                     </div>
                //                 </div>
                //             `).join('')}
                //         </div>
                //         <hr>
                //         <div class="row">
                //             <h6 class="fw-bold">Itinerary</h6>
                //             <div class="col-md-12">
                //                 <p>${data.details.itinerary_overview}</p>
                //             </div>
                //             ${data.itineraries.map((day, i) => `
                //                 <div class="col-md-6">
                //                     <div class="card mt-3">
                //                         <div class="card-header">
                //                             <h6 class="mb-0">Day ${i+1}</h6>
                //                         </div>
                //                         <div class="card-body">
                //                             <div class="mb-3">
                //                                 <label class="form-label">${day.title}</label>
                //                             </div>
                //                             <div class="mb-3">
                //                                 <p class="text-muted mb-1">${day.details}</p>
                //                             </div>
                //                         </div>
                //                     </div>
                //                 </div>
                //             `).join('')}
                //         </div>
                //         <hr>
                //         <div class="row">
                //             <div class="col-md-6">
                //                 <h6 class="fw-bold">Inclusions</h6>
                //                 <p>
                //                     ${data.details.includes ?? '----No Data----'}
                //                 </p>
                //             </div>
                //             <div class="col-md-6">
                //                 <h6 class="fw-bold">Exclusions</h6>
                //                 <p>
                //                     ${data.details.excludes ?? '----No Data----'}
                //                 </p>
                //             </div>
                //         </div>
                //     `;

                //     $('#viewPackageModal1 .modal-body').html(body);

                //     // open modal
                //     $('#viewPackageModal1').modal('show');
                // },
                success: function(res) {
    let data = res?.package || {};

    $('#viewPackageModal1 .modal-title').text(data?.title || 'Package Details');

    const locationText = [
        data?.location?.name,
        data?.location?.country?.name,
        data?.location?.country?.continent?.name
    ].filter(Boolean).join(', ') || '----';

    const sourceLocationText = [
        data?.source_location?.name,
        data?.source_location?.country?.name,
        data?.source_location?.country?.continent?.name
    ].filter(Boolean).join(', ') || '----';

    const imagesHtml = Array.isArray(data?.images)
        ? data.images.map(img => `
            <div class="col-md-3 text-center">
                <div class="card mb-3">
                    <img src="${img.image_path}" class="card-img-top img-fluid" style="height:150px; object-fit:cover;">
                </div>
            </div>
        `).join('')
        : '';

    const itinerariesHtml = Array.isArray(data?.itineraries)
        ? data.itineraries.map((day, i) => `
            <div class="col-md-6">
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">Day ${i + 1}</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">${day?.title || '----'}</label>
                        </div>
                        <div class="mb-3">
                            <p class="text-muted mb-1">${day?.details || '----'}</p>
                        </div>
                    </div>
                </div>
            </div>
        `).join('')
        : '';

    const body = `
    <div class="row">
        <div class="col-md-6">
            <p><strong>Title:</strong> ${data?.title || '----'}</p>
            <p><strong>Type:</strong> India Tour Package</p>
            <p><strong>Destination Location:</strong> ${locationText}</p>
        </div>
        <div class="col-md-6">
            <p><strong>Category:</strong> ${data?.category?.name || '----'}</p>
            <p><strong>Duration:</strong> ${
                data?.details?.duration_days
                ? `${data.details.duration_days} day / ${data.details.duration_nights || 0} night`
                : '----'
            }</p>
            <p><strong>Top Trending Package:</strong> ${data?.is_top_trending ? 'Yes' : 'No'}</p>
            <p><strong>Source Location:</strong> ${sourceLocationText}</p>
        </div>

        <div class="col-md-12">
            <p><strong>Short Description:</strong> ${data?.details?.tour_highlights || '----'}</p>
        </div>
    </div>

    <hr>

    <div class="row">
        <h6 class="fw-bold">Gallery</h6>

        ${data?.primary_image ? `
        <div class="col-md-12 text-center">
            <div class="card mb-3">
                <img src="${data.primary_image}" class="card-img-top img-fluid" style="height:150px; object-fit:cover;">
            </div>
        </div>
        ` : ''}

        ${imagesHtml}
    </div>

    <hr>

    <div class="row">
        <h6 class="fw-bold">Itinerary</h6>
        ${itinerariesHtml}
    </div>
    `;

    $('#viewPackageModal1 .modal-body').html(body);
    $('#viewPackageModal1').modal('show');
},

                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while fetching package details.'
                    });
                },
                complete: function () {
                    // Hide loader, show button text back
                    btn.find('.spinner-border').addClass('d-none');
                    btn.find('.icon').removeClass('d-none');
                }
            });
        });

        $(document).on('click', '.package-faqs', function() {
            let btn = $(this);
            btn.find('.spinner-border').removeClass('d-none');
            btn.find('.icon').addClass('d-none');
            let id = btn.data('id');
            let title = btn.data('title');
            let dataUrl = btn.data('url');
            
            $('#faqForm').attr('action',dataUrl);
            $.ajax({
                type: "GET",
                dataType: "json",
                url: "{{ route('admin.packages.index') }}", // better to point this to a dedicated faq route
                data: { id: id, faqs: 'list' },
                success: function(data) {
                    $('#faqModal .modal-title').text(title+' FAQ');
                    let faqs = data.package.faqs || [];
                    let body = `
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="faq_title" class="form-label">Faq Title<span class="required-text">*</span></label>
                            <input value="${data.package.faq_title ? data.package.faq_title : ''}" class="form-control" name="faq_title" id="faq_title" placeholder="" required>
                        </div>
                    </div>
                    <table class="table" id="faqTable">
                        <thead>
                            <tr>
                                <th>Question</th>
                                <th>Answer</th>
                                <th><button type="button" class="btn btn-sm btn-outline-success" id="addFaqRow"><i class="fas fa-plus"></button></th>
                            </tr>
                        </thead>
                        <tbody>
                    `;

                    if (faqs.length > 0) {
                        $.each(faqs, function(index, faq) {
                            body += `
                                <tr class="b-none">
                                    <td><input type="text" name="faqs[${faqIndex}][question]" value="${faq.question}" class="form-control" required /></td>
                                    <td><textarea name="faqs[${faqIndex}][answer]" class="form-control">${faq.answer ?? ''}</textarea></td>
                                    <td><button type="button" class="btn btn-sm btn-outline-danger removeFaqRow"><i class="fas fa-trash"></button></td>
                                </tr>
                            `;
                            faqIndex++;
                        });
                    } else {
                        body += `
                            <tr class="b-none">
                                <td><input type="text" name="faqs[${faqIndex}][question]" class="form-control" required/></td>
                                <td><textarea name="faqs[${faqIndex}][answer]" class="form-control"></textarea></td>
                                <td></td>
                            </tr>
                        `;
                        faqIndex++;
                    }

                    body += `</tbody></table>`;

                    $('#faqModal .modal-body').html(body);

                    // open modal
                    $('#faqModal').modal('show');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while fetching package FAQs.'
                    });
                },
                complete: function () {
                    btn.find('.spinner-border').addClass('d-none');
                    btn.find('.icon').removeClass('d-none');
                }
            });
        });

        $(document).on('click', '#addFaqRow', function() {
            let row = `
                <tr class="b-none">
                    <td><input type="text" name="faqs[${faqIndex}][question]" class="form-control" required /></td>
                    <td><textarea name="faqs[${faqIndex}][answer]" class="form-control"></textarea></td>
                    <td><button type="button" class="btn btn-sm btn-outline-danger removeFaqRow"><i class="fas fa-trash"></button></td>
                </tr>`;
            faqIndex++;
            $('#faqTable tbody').append(row);
            
        });

        $(document).on('click', '.removeFaqRow', function() {
            $(this).closest('tr').remove();
        });

        $(document).on("click", ".delete-package", function(e) {
            e.preventDefault();
            let itemId = $(this).data("id");
            let itemUrl = $(this).data("url");
            let row = $(this).closest("tr"); // parent <tr>
            Swal.fire({
                title: "Are you sure?",
                text: "This item will be deleted!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#e3342f",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Yes, delete it",
                cancelButtonText: "Cancel",
                customClass: {
                    popup: 'rounded-2xl shadow-lg',  // Rounded + shadow
                    confirmButton: 'px-4 py-2 text-white',
                    cancelButton: 'px-4 py-2 text-white'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    let btn = $(this);
                    btn.find('.spinner-border').removeClass('d-none');
                    btn.find('.icon').addClass('d-none');
                    $.ajax({
                        type: "DELETE",
                        url: itemUrl,
                        success: function(res) {
                            if(res.success){
                                row.remove();
                                Swal.fire(
                                    "Deleted!",
                                    "The item has been deleted successfully.",
                                    "success"
                                );
                            }
                        },
                        error: function(xhr) {
                            toastr.error('Category not deleted!', 'Failed');
                        },
                        complete: function () {
                            // Hide loader, show button text back
                            btn.find('.spinner-border').addClass('d-none');
                            btn.find('.icon').removeClass('d-none');
                        }
                    });
                    
                }
            });
        });

        $(document).on('click', '.package-meta', function() {
            let btn = $(this);
            let id = $(this).data('id');
            let dataUrl = $(this).data('url').trim();
            let dataupUrl = $(this).data('upurl').trim();
            btn.find('.spinner-border').removeClass('d-none');
            btn.find('.icon').addClass('d-none');
            $.ajax({
                type: "GET",
                dataType: "json",
                url: dataUrl,
                success: function(data) {
                    $('#package-title').text('# '+data.title+'-Meta Info');
                    if(data.meta != null){
                        $('#package_meta_title').val(data.meta.meta_title);
                        $('#package_meta_description').val(data.meta.meta_description);
                        $('#package_meta_keywords').val(data.meta.meta_keywords);
                        $('#package_h1_heading').val(data.meta.h1_heading);
                        $('#package_meta_details').val(data.meta.meta_details);
                    }
                    $('#packageMeta').attr('action',dataupUrl);
                    $('#packageMetaModal').modal('show');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while fetching city details.'
                    });
                },
                complete: function () {
                    // Hide loader, show button text back
                    btn.find('.spinner-border').addClass('d-none');
                    btn.find('.icon').removeClass('d-none');
                }
            });
        });

    });

</script>
@endsection
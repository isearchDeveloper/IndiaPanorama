@section('title','Achievements & Awards')
@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2">
                    <i class="fas fa-trophy me-2"></i>Achievements & Awards
                </h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAwardsModal">
                    <i class="fas fa-plus me-2"></i>Add Achievements & Awards
                </button>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                @if(session('success'))
                <div class="card-header d-flex" style="justify-content: flex-end;">
                    <div class="text-success">
                        {{ session('success') }}
                    </div>
                </div>
                @endif
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="bg-light">
                                <th>Title</th>
                                <th>Year</th>
                                <th>Image</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($awards as $key => $c)
                                <tr>
                                    <td><strong>{{ $c->title }}</strong></td>
                                    <td>{{ $c->award_year }}</td>
                                    <td>
                                        <div class="card mb-3">
                                            <img id="banner-image" src="{{ storage_link($c->banner_image) }}" class="card-img-top img-fluid" style="height:50px; object-fit:cover;">
                                        </div>
                                    </td>
                                    <td>
                                        <input id="status_{{$c->id }}" type="checkbox" data-id="{{$c->id }}" data-url="{{ route('admin.awards.update',$c->id) }}" class="js-switch awards-status" <?php echo $c->is_active == 1 ? 'checked' : '' ?>>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <button class="btn btn-outline-primary btn-sm editAwards" data-url="{{ route('admin.awards.edit',$c->id) }}">
                                            <i class="fa fa-edit icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary view-awards" data-id="{{ $c->id }}" data-url="{{ route('admin.awards.index') }}">
                                            <i class="fas fa-eye icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger delete-awards" data-id="{{ $c->id }}" data-url="{{ route('admin.awards.destroy',$c->id) }}">
                                            <i class="fas fa-trash icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @include('admin.common.pagination', ['paginator' => $awards])
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('modal')
<!-- Add Awards Modal -->
<div class="modal fade" id="addAwardsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Add New Awards
                </h5>
                <button type="button" class="btn-close btn-close-white close-modal" data-bs-dismiss="modal"></button>
            </div>
            <form id="awardsForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                                <div class="text-danger title-error" style="display:none"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="award_year" class="form-label">Year</label>
                                <input type="text" class="form-control" id="award_year" name="award_year" required>
                                <div class="text-danger award-year-error" style="display:none"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <x-media-picker name="banner_image" picker-id="award_banner_add" label="Image" folder="award" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                            <!-- <small id="word-count" class="text-muted">0 / 250 words</small> -->
                            <div class="text-danger description-error" style="display:none"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close-modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submit-btn">
                        <i class="fas fa-save me-2"></i>Save Awards
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editAwardsModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<form id="editAwardsForm" data-url="">
			@csrf 
            @method('PUT')
            <input type="hidden" name="id" id="award_id">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Edit Awards</h5>
					<button type="button" class="btn-close close-modal"></button>
				</div>
				<div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="edit-title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="edit-title" name="title" required>
                                <div class="text-danger title-error" style="display:none"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-award-year" class="form-label">Year</label>
                                <input type="text" class="form-control" id="edit-award-year" name="award_year" required>
                                <div class="text-danger edit-award-year-error" style="display:none"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <x-media-picker name="banner_image" picker-id="award_banner_edit" label="Image" folder="award" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="edit-description" class="form-label">Description</label>
                                <textarea class="form-control" id="edit-description" name="description" rows="3" required></textarea>
                                <div class="text-danger description-error" style="display:none"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close-modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="update-btn">
                        <i class="fas fa-save me-2"></i>Update Awards
                    </button>
                </div>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" id="viewAwardsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="page_name"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8">
                        <p id="page-title"><strong>Title:</strong> </p>
                    </div>
                    <div class="col-md-4">
                        <p id="page-year"><strong>Year:</strong> </p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <h6 class="fw-bold">Image</h6>
                    <div class="col-md-12 text-center">
                        <div class="card mb-3">
                            <img id="page-banner-image-view" src="" class="card-img-top img-fluid" style="height:150px; object-fit:cover;">
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <h6 class="fw-bold">Description</h6>
                        <p id="page-about"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script>
    function modalDismis(){
        $('#addAwardsModal').modal('hide');
        $('#editAwardsModal').modal('hide');
        $('#awardsForm')[0].reset();
        $('#editAwardsForm')[0].reset();
        $('.text-danger').text('');
        $('.text-danger').hide();
    }

    $(document).ready(function () {
        $(document).on('click', '.close-modal', function() {
            modalDismis();
        });

        $(document).on('click', '.editAwards', function () {
            let btn = $(this);
            btn.find('.spinner-border').removeClass('d-none');
            btn.find('.icon').addClass('d-none');
            let dataUrl = $(this).data('url');
            $.ajax({
                url: dataUrl,
                type: 'GET',
                success: function (data) {
                    $('#award_id').val(data.id);
                    $('#edit-title').val(data.title);
                    $('#edit-award-year').val(data.award_year);
                    if (typeof window.setMediaPickerValue === 'function') {
                        window.setMediaPickerValue('award_banner_edit', data.banner_image, data.banner_image ? (s3BaseUrl + data.banner_image) : null);
                    }
                    $('#edit-description').val(data.description);
                    $('#editAwardsForm').attr('data-url',dataUrl.replace('/edit',''));
                    $('#editAwardsModal').modal('show');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while fetching awards details.'
                    });
                },
                complete: function () {
                    // Hide loader, show button text back
                    btn.find('.spinner-border').addClass('d-none');
                    btn.find('.icon').removeClass('d-none');
                }
            });
        });

        $('#awardsForm').on('submit', function(e) {
            e.preventDefault();
            if (!$('#awardsForm input[name=banner_image]').val()) {
                toastr.warning('Please choose an image.');
                return;
            }
            $('#submit-btn').attr('disabled',true);
            $('.text-danger').text('');
            let formData = new FormData(this);
            $.ajax({
                type: "POST",
                url: "{{ route('admin.awards.store') }}",
                data: formData,
                contentType: false,  // must be false for FormData
                processData: false,
                success: function(response) {
                    $('#awardsForm')[0].reset();
                    $('#submit-btn').removeAttr('disabled');
                    $('#addAwardsModal').modal('hide');
                    toastr.success('Awards added successfully!', 'Success');
                    $('.text-danger').hide();
                    setTimeout(function(){
                        location.reload();
                    }, 1500);
                },
                error: function(xhr) {
                    $('#submit-btn').removeAttr('disabled');
                    if(xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        if (errors.title) $('.title-error').text(errors.title[0]);
                        if (errors.banner_image) $('.banner-error').text(errors.banner_image[0]);
                        if (errors.description) $('.description-error').text(errors.description[0]);
                        $('.text-danger').show();
                        if (!errors.title && !errors.banner_image && !errors.description) {
                            toastr.error(window.firstErrorMessage(errors, 'Validation failed.'));
                        }
                    }
                }
            });
        });

        $('#editAwardsForm').on('submit', function(e) {
            tinymce.triggerSave();
            e.preventDefault();
            let id = $('#award_id').val();
            $('#update-btn').attr('disabled',true);
            $('.text-danger').text('');
            let formData = new FormData(this);
            $.ajax({
                type: "POST",
                url: $(this).data('url'),
                data: formData,
                contentType: false,  // must be false for FormData
                processData: false,
                success: function(response) {
                    $('#editAwardsForm')[0].reset();
                    $('#update-btn').removeAttr('disabled');
                    $('#editAwardsModal').modal('hide');
                    toastr.success('Awards updated successfully!', 'Success');
                    $('.text-danger').hide();
                    setTimeout(function(){
                        location.reload();
                    }, 1500);
                },
                error: function(xhr) {
                    $('#update-btn').removeAttr('disabled');
                    if(xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        if (errors.title) $('.title-error').text(errors.title[0]);
                        if (errors.banner_image) $('.banner-error').text(errors.banner_image[0]);
                        if (errors.description) $('.description-error').text(errors.description[0]);
                        $('.text-danger').show();
                        if (!errors.title && !errors.banner_image && !errors.description) {
                            toastr.error(window.firstErrorMessage(errors, 'Validation failed.'));
                        }
                    }
                }
            });
        });

        $(document).on('change', '.awards-status', function() {
            let awards_status = $(this).prop('checked') === true ? 1 : 0;
            $.ajax({
                type: "PUT",
                dataType: "json",
                url: $(this).data('url'),
                data: {
                    'status': awards_status
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

        $(document).on("click", ".delete-awards", function(e) {
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
                        complete: function() {
                            // Hide loader, show button text back
                            btn.find('.spinner-border').addClass('d-none');
                            btn.find('.icon').removeClass('d-none');
                        },
                        error: function(xhr) {
                            toastr.error('Awards not deleted!', 'Failed');
                        }
                    });
                    
                }
            });
        });

        $(document).on('click', '.view-awards', function () {
            let btn = $(this);
            btn.find('.spinner-border').removeClass('d-none');
            btn.find('.icon').addClass('d-none');
            let id = $(this).data('id');
            $.ajax({
                type: "GET",
                dataType: "json",
                url: "{{ route('admin.awards.index') }}",
                data: { id: id },
                success: function(data) {
                    $('#page-title').html('<strong>Title:</strong> '+data.awards.title);
                    $('#page-year').html('<strong>Year:</strong> '+data.awards.award_year);
                    $('#page-about').html(data.awards.description);
                    $('#page-banner-image-view').attr('src',s3BaseUrl+data.awards.banner_image);
                    $('#viewAwardsModal').modal('show');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while fetching awards details.'
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

<script>
    document.getElementById('description').addEventListener('input', function () {
        wordLimit(this);
    });

    document.getElementById('edit-description').addEventListener('input', function () {
        wordLimit(this);
    });

    function wordLimit(el) {
        const maxWords = 100;
        const words = el.value.trim().split(/\s+/).filter(word => word.length > 0);
        const wordCount = words.length;

        if (wordCount > maxWords) {
            el.value = words.slice(0, maxWords).join(' ');
            const warn = document.createElement('div');
            warn.textContent =  `Max ${maxWords} words allowed.`;
            Object.assign(warn.style, {
                position: 'fixed',
                bottom: '20px',
                right: '20px',
                background: '#ff4d4d',
                color: '#fff',
                padding: '8px 12px',
                borderRadius: '6px',
                zIndex: 99999,
                fontSize: '14px',
                boxShadow: '0 2px 6px rgba(0,0,0,0.15)'
            });
            document.body.appendChild(warn);

            setTimeout(() => {
                warn.remove();
                delete el.dataset.warned;
            }, 2000);
        }

        document.getElementById('word-count').textContent = `${Math.min(wordCount, maxWords)} / ${maxWords} words`;


    }
</script>

@endsection

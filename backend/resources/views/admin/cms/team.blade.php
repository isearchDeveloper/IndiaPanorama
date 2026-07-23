@section('title','Our Teams')
@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2">
                    <i class="fas fa-user me-2"></i>Our Team
                </h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTeamsModal">
                    <i class="fas fa-plus me-2"></i>Add Team Member
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
                                <th>Name</th>
                                <th>Department</th>
                                <th>Designation</th>
                                <th>Image</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($teams as $key => $c)
                                <tr>
                                    <td><strong class="text-primary">{{ $c->name }}</strong></td>
                                    <td>{{ $c->department?->name }}</td>
                                    <td>{{ $c->description }}</td>
                                    <td>
                                        <div class="card mb-3">
                                            <img id="banner-image" src="{{ storage_link($c->profile_image) }}" class="card-img-top img-fluid" style="height:80px; object-fit:cover;">
                                        </div>
                                    </td>
                                    <td>
                                        <input id="status_{{$c->id }}" type="checkbox" data-id="{{$c->id }}" data-url="{{ route('admin.teams.update',$c->id) }}" class="js-switch teams-status" <?php echo $c->is_active == 1 ? 'checked' : '' ?>>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <button class="btn btn-outline-primary btn-sm editTeams" data-url="{{ route('admin.teams.edit',$c->id) }}">
                                            <i class="fa fa-edit icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary view-teams" data-id="{{ $c->id }}" data-url="{{ route('admin.teams.index') }}">
                                            <i class="fas fa-eye icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger delete-teams" data-id="{{ $c->id }}" data-url="{{ route('admin.teams.destroy',$c->id) }}">
                                            <i class="fas fa-trash icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @include('admin.common.pagination', ['paginator' => $teams])
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('modal')
<!-- Add Teams Modal -->
<div class="modal fade" id="addTeamsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Add New Member
                </h5>
                <button type="button" class="btn-close btn-close-white close-modal" data-bs-dismiss="modal"></button>
            </div>
            <form id="teamsForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                                <div class="text-danger name-error" style="display:none"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="dep_id" class="form-label">Department</label>
                                <select class="form-control" id="dep_id" name="dep_id" required>
                                    <option value="">Select</option>
                                    @foreach($department as $dep)
                                    <option value="{{$dep->id}}">{{$dep->name}}</option>
                                    @endforeach
                                </select>
                                <div class="text-danger dep-id-error" style="display:none"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="description" class="form-label">Designation</label>
                                <input type="text" class="form-control" id="description" name="description" required>
                                <div class="text-danger description-error" style="display:none"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <x-media-picker name="profile_image" picker-id="team_profile_add" label="Profile Image" folder="team" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <label for="about" class="form-label">About</label>
                            <textarea class="form-control about" id="about" name="about" rows="3" required></textarea>
                            <div class="text-danger about-error" style="display:none"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close-modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submit-btn">
                        <i class="fas fa-save me-2"></i>Save Teams
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editTeamsModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<form id="editTeamsForm" data-url="">
			@csrf 
            @method('PUT')
            <input type="hidden" name="id" id="team_id">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Edit Team Member</h5>
					<button type="button" class="btn-close close-modal"></button>
				</div>
				<div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="edit-name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="edit-name" name="name" required>
                                <div class="text-danger edit-name-error" style="display:none"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-dep-id" class="form-label">Department</label>
                                <select class="form-control" id="edit-dep-id" name="dep_id" required>
                                    <option value="">Select</option>
                                    @foreach($department as $dep)
                                    <option value="{{$dep->id}}">{{$dep->name}}</option>
                                    @endforeach
                                </select>
                                <div class="text-danger edit-dep-id-error" style="display:none"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-description" class="form-label">Designation</label>
                                <input type="text" class="form-control" id="edit-description" name="description" required>
                                <div class="text-danger edit-description-error" style="display:none"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <x-media-picker name="profile_image" picker-id="team_profile_edit" label="Profile Image" folder="team" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="edit-about" class="form-label">About</label>
                                <textarea class="form-control about" id="edit-about" name="about" rows="3"></textarea>
                                <div class="text-danger about-error" style="display:none"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close-modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="update-btn">
                        <i class="fas fa-save me-2"></i>Update Teams
                    </button>
                </div>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" id="viewTeamsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="page_name">Profile Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 mb-5" id="member-name"></div>
                    <div class="col-md-6  mb-5" id="member-department"></div>
                    <div class="col-md-6  mb-5" id="member-designation"></div>
                </div>
                <div class="row  mb-5">
                    <div class="col-md-3 text-center">
                        <div class="card mb-3">
                            <img id="member-profile" src="" class="card-img-top img-fluid" style="height:150px; object-fit:cover;">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <h6 class="fw-bold">About</h6>
                    <div class="col-md-12" id="member-about"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script>
    function modalDismis(){
        $('#addTeamsModal').modal('hide');
        $('#editTeamsModal').modal('hide');
        $('#teamsForm')[0].reset();
        $('#editTeamsForm')[0].reset();
        $('.text-danger').text('');
        $('.text-danger').hide();
    }

    $(document).ready(function () {
        $(document).on('click', '.close-modal', function() {
            modalDismis();
        });

        $(document).on('click', '.view-teams', function () {
            let btn = $(this);
            btn.find('.spinner-border').removeClass('d-none');
            btn.find('.icon').addClass('d-none');
            let id = $(this).data('id');
            $.ajax({
                type: "GET",
                dataType: "json",
                url: "{{ route('admin.teams.index') }}",
                data: { id: id },
                success: function(data) {
                    $('#member-name').html('<strong>Name:</strong> '+data.teams.name);
                    $('#member-department').html('<strong>Department:</strong> '+data.teams.department.name);
                    $('#member-designation').html('<strong>Designation:</strong> '+data.teams.description);
                    $('#member-about').html(data.teams.about !== null ?data.teams.about : '-----');
                    $('#member-profile').attr('src',s3BaseUrl+data.teams.profile_image);
                    $('#viewTeamsModal').modal('show');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while fetching member details.'
                    });
                },
                complete: function () {
                    // Hide loader, show button text back
                    btn.find('.spinner-border').addClass('d-none');
                    btn.find('.icon').removeClass('d-none');
                }
            });
        });

        $(document).on('click', '.editTeams', function () {
            let btn = $(this);
            btn.find('.spinner-border').removeClass('d-none');
            btn.find('.icon').addClass('d-none');
            let dataUrl = $(this).data('url');
            $.ajax({
                url: dataUrl,
                type: 'GET',
                success: function (data) {
                    $('#team_id').val(data.id);
                    $('#edit-name').val(data.name);
                    $('#edit-dep-id').val(data.dep_id);
                    if (typeof window.setMediaPickerValue === 'function') {
                        window.setMediaPickerValue('team_profile_edit', data.profile_image, data.profile_image ? (s3BaseUrl + data.profile_image) : null);
                    }
                    $('#edit-description').val(data.description);
                    $('#edit-about').val(data.about);
                    $('#editTeamsForm').attr('data-url',dataUrl.replace('/edit',''));
                    $('#editTeamsModal').modal('show');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while fetching team member details.'
                    });
                },
                complete: function () {
                    // Hide loader, show button text back
                    btn.find('.spinner-border').addClass('d-none');
                    btn.find('.icon').removeClass('d-none');
                }
            });
        });

        $('#teamsForm').on('submit', function(e) {
            e.preventDefault();
            if (!$('#teamsForm input[name=profile_image]').val()) {
                toastr.warning('Please choose an image.');
                return;
            }
            $('#submit-btn').attr('disabled',true);
            $('.text-danger').text('');
            let formData = new FormData(this);
            $.ajax({
                type: "POST",
                url: "{{ route('admin.teams.store') }}",
                data: formData,
                contentType: false,  // must be false for FormData
                processData: false,
                success: function(response) {
                    $('#teamsForm')[0].reset();
                    $('#submit-btn').removeAttr('disabled');
                    $('#addTeamsModal').modal('hide');
                    toastr.success('Member added successfully!', 'Success');
                    $('.text-danger').hide();
                    setTimeout(function(){
                        location.reload();
                    }, 1500);
                },
                error: function(xhr) {
                    $('#submit-btn').removeAttr('disabled');
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        window.showFormErrors(xhr.responseJSON.errors, { scope: '#teamsForm', fallback: 'Failed to add member.' });
                    } else {
                        toastr.error('Failed to add member.');
                    }
                }
            });
        });

        $('#editTeamsForm').on('submit', function(e) {
            tinymce.triggerSave();
            e.preventDefault();
            let id = $('#team_id').val();
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
                    $('#editTeamsForm')[0].reset();
                    $('#update-btn').removeAttr('disabled');
                    $('#editTeamsModal').modal('hide');
                    toastr.success('Member updated successfully!', 'Success');
                    $('.text-danger').hide();
                    setTimeout(function(){
                        location.reload();
                    }, 1500);
                },
                error: function(xhr) {
                    $('#update-btn').removeAttr('disabled');
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        window.showFormErrors(xhr.responseJSON.errors, { scope: '#editTeamsForm', fallback: 'Failed to update member.' });
                    } else {
                        toastr.error('Failed to update member.');
                    }
                }
            });
        });

        $(document).on('change', '.teams-status', function() {
            let teams_status = $(this).prop('checked') === true ? 1 : 0;
            $.ajax({
                type: "PUT",
                dataType: "json",
                url: $(this).data('url'),
                data: {
                    'status': teams_status
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

        $(document).on("click", ".delete-teams", function(e) {
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
                                    "Member has been deleted successfully.",
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
                            toastr.error('Teams not deleted!', 'Failed');
                        }
                    });
                    
                }
            });
        });

    });
</script>
<script>
    document.getElementById('about').addEventListener('input', function () {
        wordLimit(this);
    });

    document.getElementById('edit-about').addEventListener('input', function () {
        wordLimit(this);
    });

    function wordLimit(el) {
        const maxWords = 600;
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

    }
</script>
@endsection

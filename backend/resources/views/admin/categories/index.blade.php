@section('title','Categories')
@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2">
                    <i class="fas fa-tags me-2"></i>Categories
                </h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                    <i class="fas fa-plus me-2"></i>Add Category
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
                                <th>Category Name</th>
                                <th>Page Title</th>
                                <th>Package Count</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($categories as $key => $c)
                                <tr>
                                    <td><strong class="text-primary">{{ $c->name }}</strong></td>
                                    <td>{{ $c->title }}</td>
                                    <td>{{ $c->packages_count }}</td>
                                    <td>
                                        <input id="status_{{$c->id }}" type="checkbox" data-id="{{$c->id }}" data-url="{{ route('admin.categories.update',$c->id) }}" class="js-switch category-status" <?php echo $c->is_active == 1 ? 'checked' : '' ?>>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <button class="btn btn-outline-primary btn-sm editCategory" data-url="{{ route('admin.categories.edit',$c->id) }}">
                                            <i class="fa fa-edit icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary view-category" data-id="{{ $c->id }}" data-url="{{ route('admin.categories.index') }}">
                                            <i class="fas fa-eye icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger delete-category" data-id="{{ $c->id }}" data-url="{{ route('admin.categories.destroy',$c->id) }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @include('admin.common.pagination', ['paginator' => $categories])
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('modal')
<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Add New Category
                </h5>
                <button type="button" class="btn-close btn-close-white close-modal" data-bs-dismiss="modal"></button>
            </div>
            <form id="categoryForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <!-- <div class="col-md-12"> -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="cat-name" class="form-label">Category Name</label>
                                    <input type="text" class="form-control" id="cat-name" name="name" required>
                                    <div class="text-danger name-error" style="display:none"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="slug" class="form-label">Slug</label>
                                    <input type="text" class="form-control rd-only" id="slug" name="slug" readonly>
                                    <div class="text-danger slug-error" style="display:none"></div>
                                </div>
                            </div>
                        <!-- </div> -->
                    </div>
                    <div class="row">
                        <!-- <div class="col-md-12"> -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Page Title</label>
                                    <input type="text" class="form-control" id="title" name="title" required>
                                    <div class="text-danger title-error" style="display:none"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sub-title" class="form-label">Sub Title</label>
                                    <input type="text" class="form-control" id="sub-title" name="sub_title">
                                    <div class="text-danger sub-title-error" style="display:none"></div>
                                </div>
                            </div>
                        <!-- </div> -->
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <x-media-picker name="banner_image" picker-id="category_banner_add" label="Page Banner" folder="category" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control tinymce" id="description" name="description" rows="3"></textarea>
                            <div class="text-danger description-error" style="display:none"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close-modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submit-btn">
                        <i class="fas fa-save me-2"></i>Save Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<form id="editCategoryForm" data-url="">
			@csrf 
            @method('PUT')
            <input type="hidden" name="id" id="cat_id">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Edit Category</h5>
					<button type="button" class="btn-close close-modal"></button>
				</div>
				<div class="modal-body">
                    <div class="row">
                        <!-- <div class="col-md-12"> -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-name" class="form-label">Category Name</label>
                                    <input type="text" class="form-control" id="edit-name" name="name" required>
                                    <div class="text-danger name-error" style="display:none"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-slug" class="form-label">Slug</label>
                                    <input type="text" class="form-control rd-only" id="edit-slug" name="slug" readonly>
                                    <div class="text-danger slug-error" style="display:none"></div>
                                </div>
                            </div>
                        <!-- </div> -->
                    </div>
                    <div class="row">
                        <!-- <div class="col-md-12"> -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-title" class="form-label">Page Title</label>
                                    <input type="text" class="form-control" id="edit-title" name="title" required>
                                    <div class="text-danger title-error" style="display:none"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-sub-title" class="form-label">Sub Title</label>
                                    <input type="text" class="form-control" id="edit-sub-title" name="sub_title">
                                    <div class="text-danger sub-title-error" style="display:none"></div>
                                </div>
                            </div>
                        <!-- </div> -->
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <x-media-picker name="banner_image" picker-id="category_banner_edit" label="Page Banner" folder="category" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="edit-description" class="form-label">Description</label>
                                <textarea class="form-control tinymce" id="edit-description" name="description" rows="3"></textarea>
                                <div class="text-danger description-error" style="display:none"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close-modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="update-btn">
                        <i class="fas fa-save me-2"></i>Update Category
                    </button>
                </div>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" id="viewCategoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="page_name"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <p id="page-title"><strong>Title:</strong> </p>
                    </div>
                    <div class="col-md-12">
                        <p id="page-sub-title"><strong>Sub Title:</strong> </p>
                        
                    </div>
                </div>
                <hr>
                <div class="row">
                    <h6 class="fw-bold">Banner Image</h6>
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
        $('#addCategoryModal').modal('hide');
        $('#editCategoryModal').modal('hide');
        $('#categoryForm')[0].reset();
        $('#editCategoryForm')[0].reset();
        $('.text-danger').text('');
        $('.text-danger').hide();
    }

    function duplicateCheck(name,ele,id=null){
        let slug = name.toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .trim()
        .replace(/\s+/g, '-');
        $('#'+ele).val(slug);
        let obj = {slug:slug};
        if(id) obj.id = id;
        if (slug) {
            $.ajax({
                type: "GET",
                url: "{{ route('admin.categories.slug.duplicate_check') }}",
                data: obj,
                success: function(data) {
                    if (data.exists) {
                        $('.name-error').text('Name should be unique').show();
                        $('.slug-error').text('Slug should be unique').show();
                    } else {
                        $('.text-danger').hide();
                    }
                }
            });
        }
    }

    $(document).ready(function () {
        $(document).on('click', '.close-modal', function() {
            modalDismis();
        });

        $(document).on('input', '#cat-name', function() {
            let name = $(this).val();
            duplicateCheck(name,'slug');
        });

        $(document).on('click', '.editCategory', function () {
            let btn = $(this);
            btn.find('.spinner-border').removeClass('d-none');
            btn.find('.icon').addClass('d-none');
            let dataUrl = $(this).data('url');
            $.ajax({
                url: dataUrl,
                type: 'GET',
                success: function (data) {
                    $('#cat_id').val(data.id);
                    $('#edit-name').val(data.name);
                    $('#edit-slug').val(data.slug);
                    $('#edit-title').val(data.title);
                    $('#edit-sub-title').val(data.sub_title);
                    if (typeof window.setMediaPickerValue === 'function') {
                        window.setMediaPickerValue('category_banner_edit', data.banner_image, data.banner_image ? (s3BaseUrl + data.banner_image) : null);
                    }
                    if (tinymce.get('edit-description')) {
                        tinymce.get('edit-description').setContent(data.description);
                    } else {
                        $('#edit-description').val(data.description);
                    }
                    $('#editCategoryForm').attr('data-url',dataUrl.replace('/edit',''));
                    $('#editCategoryModal').modal('show');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while fetching category details.'
                    });
                },
                complete: function () {
                    // Hide loader, show button text back
                    btn.find('.spinner-border').addClass('d-none');
                    btn.find('.icon').removeClass('d-none');
                }
            });
        });

        $('#categoryForm').on('submit', function(e) {
            tinymce.triggerSave();
            e.preventDefault();
            if (!$('#categoryForm input[name=banner_image]').val()) {
                toastr.warning('Please choose an image.');
                return;
            }
            $('#submit-btn').attr('disabled',true);
            $('.text-danger').text('');
            let formData = new FormData(this);
            $.ajax({
                type: "POST",
                url: "{{ route('admin.categories.store') }}",
                data: formData,
                contentType: false,  // must be false for FormData
                processData: false,
                success: function(response) {
                    $('#categoryForm')[0].reset();
                    $('#submit-btn').removeAttr('disabled');
                    $('#addCategoryModal').modal('hide');
                    toastr.success('Category added successfully!', 'Success');
                    $('.text-danger').hide();
                    setTimeout(function(){
                        location.reload();
                    }, 1500);
                },
                error: function(xhr) {
                    $('#submit-btn').removeAttr('disabled');
                    if(xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        if (errors.name) $('.name-error').text(errors.name[0]);
                        if (errors.slug) $('.slug-error').text(errors.slug[0]);
                        if (errors.title) $('.title-error').text(errors.title[0]);
                        if (errors.sub_title) $('.sub_title-error').text(errors.sub_title[0]);
                        if (errors.banner_image) $('.banner-error').text(errors.banner_image[0]);
                        if (errors.description) $('.description-error').text(errors.description[0]);
                        $('.text-danger').show();
                        if (!errors.name && !errors.slug && !errors.title && !errors.sub_title && !errors.banner_image && !errors.description) {
                            toastr.error(window.firstErrorMessage(errors, 'Validation failed.'));
                        }
                    }
                }
            });
        });

        $('#editCategoryForm').on('submit', function(e) {
            tinymce.triggerSave();
            e.preventDefault();
            let id = $('#cat_id').val();
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
                    $('#editCategoryForm')[0].reset();
                    $('#update-btn').removeAttr('disabled');
                    $('#editCategoryModal').modal('hide');
                    toastr.success('Category updated successfully!', 'Success');
                    $('.text-danger').hide();
                    setTimeout(function(){
                        location.reload();
                    }, 1500);
                },
                error: function(xhr) {
                    $('#update-btn').removeAttr('disabled');
                    if(xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        if (errors.name) $('.name-error').text(errors.name[0]);
                        if (errors.slug) $('.slug-error').text(errors.slug[0]);
                        if (errors.title) $('.title-error').text(errors.title[0]);
                        if (errors.sub_title) $('.sub_title-error').text(errors.sub_title[0]);
                        if (errors.banner_image) $('.banner-error').text(errors.banner_image[0]);
                        if (errors.description) $('.description-error').text(errors.description[0]);
                        $('.text-danger').show();
                        if (!errors.name && !errors.slug && !errors.title && !errors.sub_title && !errors.banner_image && !errors.description) {
                            toastr.error(window.firstErrorMessage(errors, 'Validation failed.'));
                        }
                    }
                }
            });
        });

        $(document).on('change', '.category-status', function() {
            let category_status = $(this).prop('checked') === true ? 1 : 0;
            $.ajax({
                type: "PUT",
                dataType: "json",
                url: $(this).data('url'),
                data: {
                    'status': category_status
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

        $(document).on("click", ".delete-category", function(e) {
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
                        complete: function () {
                            // Hide loader, show button text back
                            btn.find('.spinner-border').addClass('d-none');
                            btn.find('.icon').removeClass('d-none');
                        },
                        error: function(xhr) {
                            toastr.error('Category not deleted!', 'Failed');
                        }
                    });
                    
                }
            });
        });

        $(document).on('click', '.view-category', function () {
            let btn = $(this);
            btn.find('.spinner-border').removeClass('d-none');
            btn.find('.icon').addClass('d-none');
            let id = $(this).data('id');
            $.ajax({
                type: "GET",
                dataType: "json",
                url: "{{ route('admin.categories.index') }}",
                data: { id: id },
                success: function(data) {
                    $('#page_name').text(data.category.name);
                    $('#page-title').text(data.category.title);
                    $('#page-sub-title').text(data.category.sub_title);
                    $('#page-about').html(data.category.description);
                    //$('#page-banner-image').attr('src',s3BaseUrl+data.category.banner_image);
                    $('#page-banner-image-view').attr('src',s3BaseUrl+data.category.banner_image);
                    $('#viewCategoryModal').modal('show');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while fetching category details.'
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

@extends('layouts.app')
@section('title', 'Create Admin')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><i class="fas fa-user-plus me-2"></i>Create Admin</h4>
    <a href="{{ route('admin.admins.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Back</a>
</div>

@if($errors->any())
<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul></div>
@endif

<form method="POST" action="{{ route('admin.admins.store') }}">
@csrf
<div class="row g-3">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header"><strong>Admin Details</strong></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Assign Role</label>
                        <select name="role" class="form-select">
                            <option value="">— No Role —</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('-', ' ', $role->name)) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <div class="mt-2">
                            <input type="checkbox" name="status" value="1" class="js-switch" checked>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Create Admin</button>
        <a href="{{ route('admin.admins.index') }}" class="btn btn-secondary ms-2">Cancel</a>
    </div>
</div>
</form>
@endsection

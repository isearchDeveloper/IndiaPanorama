@extends('layouts.app')
@section('title', '403 - Forbidden')
@section('content')
<div class="text-center py-5">
    <i class="fas fa-lock fa-5x text-danger mb-4"></i>
    <h1 class="display-4 text-danger">403</h1>
    <h4 class="mb-3">Access Denied</h4>
    <p class="text-muted mb-4">You do not have permission to access this page.<br>Contact your Super Admin to request access.</p>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
        <i class="fas fa-home me-1"></i> Back to Dashboard
    </a>
</div>
@endsection

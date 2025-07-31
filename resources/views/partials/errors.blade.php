@if ($errors->any())
<div class="alert alert-danger alert-dismissible" role="alert">
    Vui lòng kiểm tra lại !
    {{-- <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> --}}
</div>
@endif



{{-- @if ($errors->has('fail'))
<div class="alert alert-danger alert-dismissible" role="alert">
    {{ $errors->first('fail') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if ($errors->has('name'))
<div class="alert alert-danger">
    {{ $errors->first('name') }}
</div>
@endif --}}

@if (session('success'))
<div class="alert alert-success alert-dismissible" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
@if (session('errorss'))
<div class="alert alert-danger alert-dismissible" role="alert">
    {{ session('errorss') }}
    {{-- <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> --}}
</div>
@endif

@if (session('delete_success'))
<div class="alert alert-success alert-dismissible" role="alert">
    {{ session('delete_success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if (session('export_fail'))
<div class="alert alert-danger alert-dismissible" role="alert">
    {{ session('export_fail') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if (session('roll_fail'))
<div class="alert alert-danger alert-dismissible" role="alert">
    {{ session('roll_fail') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if (session('exceed_count'))
<div class="alert alert-danger alert-dismissible" role="alert">
    {{ session('exceed_count') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
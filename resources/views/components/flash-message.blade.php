@if ($message = Session::get('success'))

<div class="alert alert-success alert-block">
    <button type="button" class="float-end btn-close btn-close-dark .fade" data-bs-dismiss="alert"></button>
    <strong>{{ $message }}</strong>
    @php
        Session::forget('success');
    @endphp
</div>

@endif



@if ($message = Session::get('error'))
<div class="alert alert-danger alert-block">
    <button type="button" class="float-end btn-close btn-close-dark .fade" data-bs-dismiss="alert"></button>
    <strong>{{ $message }}</strong>
    @php
        Session::forget('error');
    @endphp
</div>

@endif



@if ($message = Session::get('warning'))

<div class="alert alert-warning alert-block">
<button type="button" class="float-end btn-close btn-close-dark .fade" data-bs-dismiss="alert"></button>
    <strong>{{ $message }}</strong>
    @php
        Session::forget('warning');
    @endphp
</div>

@endif



@if ($message = Session::get('info'))

<div class="alert alert-info alert-block">
<button type="button" class="float-end btn-close btn-close-dark .fade" data-bs-dismiss="alert"></button>
    <strong>{{ $message }}</strong>
    @php
        Session::forget('info');
    @endphp
</div>

@endif



@if ($errors->any())

<div class="alert alert-danger">
<button type="button" class="float-end btn-close btn-close-dark .fade" data-bs-dismiss="alert"></button>
    Por favor, revise el formulario y solucione los errores.
</div>

@endif

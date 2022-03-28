@extends('dashboard.profile')

@section('seleccion-perfil')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    Settings
                    <a type="button" class="float-end btn-close btn-close-white" href="{{ route('dashboard.profile') }}"></a>
                </div>

                <div class="card-body bg-light border-none">

                    @include('components.flash-message')

                    <form method="POST" action="{{route('settings.skin')}}">
                        {{ csrf_field() }}
                        <div class="form-row">
                            <div class="col-12 mb-3">
                                <div class="text-center">
                                    <div class="btn-group" role="group" aria-label="radios">
                                        <input type="radio" class="btn-check" name="skin" id="Broadway" value="broadway" autocomplete="off" {{($css_skin == "broadway" ) ? 'checked' : ''}}>
                                        <label class="btn btn-outline-success" for="Broadway">Broadway</label>
                                        <input type="radio" class="btn-check" name="skin" id="contrast_black" value="contrast_black" autocomplete="off" {{($css_skin == "contrast_black" ) ? 'checked' : ''}}>
                                        <label class="btn btn-outline-success" for="contrast_black">contrast_black</label>
                                        <input type="radio" class="btn-check" name="skin" id="contrast_white" value="contrast_white" autocomplete="off" {{($css_skin == "contrast_white" ) ? 'checked' : ''}}>
                                        <label class="btn btn-outline-success" for="contrast_white">contrast_white</label>
                                        <input type="radio" class="btn-check" name="skin" id="material" value="material" autocomplete="off" {{($css_skin == "material" ) ? 'checked' : ''}}>
                                        <label class="btn btn-outline-success" for="material">material</label>
                                        <input type="radio" class="btn-check" name="skin" id="meadow" value="meadow" autocomplete="off" {{($css_skin == "meadow" ) ? 'checked' : ''}}>
                                        <label class="btn btn-outline-success" for="meadow">meadow</label>
                                        <input type="radio" class="btn-check" name="skin" id="skyblue" value="skyblue" autocomplete="off" {{($css_skin == "skyblue" ) ? 'checked' : ''}}>
                                        <label class="btn btn-outline-success" for="skyblue">skyblue</label>
                                        <input type="radio" class="btn-check" name="skin" id="terrace" value="terrace" autocomplete="off" {{($css_skin == "terrace" ) ? 'checked' : ''}}>
                                        <label class="btn btn-outline-success" for="terrace">terrace</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 text-center">
                                <button class="btn btn-primary" type="submit" name="send-nombre">Cambiar aspecto</button>
                            </div>
                        </div>
                    </form>

                    <div id="gantt_container" class="col-12">
                        <div id="gantt_here"></div>
                    </div> 

                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script type="text/javascript">
	var css_folder = "{{asset('css/dhtmlx-skins/')}}";
</script>
<script src="{{ asset('js/profile/settings.js') }}" defer></script>
@endsection
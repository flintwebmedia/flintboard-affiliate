@extends('backpack::layout')

@section('header')
    <section class="content-header">
        <h1>
            Import <span class="text-lowercase">{{ $crud->entity_name }}</span> <span class="small">{{ $importHelper->feed->name }}</span>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url(config('backpack.base.route_prefix'), 'dashboard') }}">{{ trans('backpack::crud.admin') }}</a></li>
            <li><a href="{{ url($crud->route) }}" class="text-capitalize">{{ $crud->entity_name_plural }}</a></li>
        </ol>
    </section>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <!-- Default box -->
            @if ($crud->hasAccess('list'))
                <a href="{{ url($crud->route) }}"><i class="fa fa-angle-double-left"></i> {{ trans('backpack::crud.back_to_all') }} <span class="text-lowercase">{{ $crud->entity_name_plural }}</span></a><br><br>
            @endif

            {!! Form::open(array('route' => 'saveMappings', 'method' => 'post')) !!}
            <div class="box">

                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans('backpack::crud.add_a_new') }} {{ $crud->entity_name }}</h3>
                </div>
                <div class="box-body row">
                    <div class="col-md-12">
                        @foreach($importHelper->fields as $field_index => $field)
                            @include('flintaffiliate::admin.import.fields.field-attribute', ['field_index' => $field_index, 'field' => $field, 'importHelper' => $importHelper])
                        @endforeach
                    </div>
                </div><!-- /.box-body -->
                <div class="box-footer">

                    @include('flintaffiliate::admin.buttons.form_submit_save', ['action' => 'Mappings opslaan'])

                </div><!-- /.box-footer-->

            </div><!-- /.box -->
            {!! Form::close() !!}
        </div>
    </div>

@endsection


@section('after_styles')
    <link rel="stylesheet" href="{{ asset('vendor/backpack/crud/css/crud.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/backpack/crud/css/form.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/backpack/crud/css/create.css') }}">
@endsection

@section('after_scripts')
    <script src="{{ asset('vendor/backpack/crud/js/crud.js') }}"></script>
    <script src="{{ asset('vendor/backpack/crud/js/form.js') }}"></script>
    <script src="{{ asset('vendor/backpack/crud/js/create.js') }}"></script>
@endsection
@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-sm-12 col-md-6 col-md-offset-3 col-xl-4 col-xl-offset-4">

        <div class="card panel-default register">
            <div class="card-heading text-center">
                <div class="title">My Baby Lock Profile</div>
                <div class="subtitle">Join Baby Lock</div>
            </div>

            <div class="card-block">

                {!! Form::open(['url' => 'register']) !!}

                @include('users::partials.name-form', ['data_wrapper' => 'profile'])
                @include('users::partials.basic-info-form')
                @include('users::partials.passwords-form')

                @include('users::partials.checkboxes-form')

                <div class="form-group">
                    {!! Form::submit(trans('labels.frontend.auth.register_button'), ['class' => 'btn btn-primary']) !!}
                </div><!--form-group-->

                {!! Form::close() !!}

                <div class="form-group">
                    {!! link_to('privacy', trans('users::lang.labels.privacy'), ['target' => '_blank']) !!}
                </div><!--form-group-->
            </div><!-- panel body -->

        </div><!-- panel -->

    </div><!-- col-md-4 -->

</div><!-- row -->
@endsection
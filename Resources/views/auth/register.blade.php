@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-xs-12 col-sm-6 col-sm-offset-3 col-lg-4 col-lg-offset-4">

        <div class="panel panel-default register">
            <div class="panel-heading text-center">
                <div class="title">My Baby Lock Profile</div>
                <div class="subtitle">Join Baby Lock</div>
            </div>

            <div class="panel-body">

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

    </div><!-- col-sm-4 -->

</div><!-- row -->
@endsection
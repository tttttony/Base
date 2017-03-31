@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-lg-8 col-lg-offset-2">

            <div class="card panel-default">

                <div class="card-heading">{{ trans('labels.frontend.passwords.reset_password_box_title') }}</div>
                <div class="card-block">

                    {!! Form::open(['url' => 'password/reset']) !!}

                    <input type="hidden" name="token" value="{{ $token }}">

                        <div class="form-group">
                            {!! Form::label('email', trans('validation.attributes.frontend.email'), ['class' => 'col-lg-4 form-control-label']) !!}
                            <div class="col-lg-6">
                                {!! Form::input('email', 'email', null, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.frontend.email')]) !!}
                            </div><!--col-lg-6-->
                        </div><!--form-group-->

                        <div class="form-group">
                            {!! Form::label('password', trans('validation.attributes.frontend.password'), ['class' => 'col-lg-4 form-control-label']) !!}
                            <div class="col-lg-6">
                                {!! Form::input('password', 'password', null, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.frontend.password')]) !!}
                            </div><!--col-lg-6-->
                        </div><!--form-group-->

                        <div class="form-group">
                            {!! Form::label('password_confirmation', trans('validation.attributes.frontend.password_confirmation'), ['class' => 'col-lg-4 form-control-label']) !!}
                            <div class="col-lg-6">
                                {!! Form::input('password', 'password_confirmation', null, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.frontend.password_confirmation')]) !!}
                            </div><!--col-lg-6-->
                        </div><!--form-group-->

                        <div class="form-group">
                            <div class="col-lg-6 col-lg-offset-4">
                                {!! Form::submit(trans('labels.frontend.passwords.reset_password_button'), ['class' => 'btn btn-primary']) !!}
                            </div><!--col-lg-6-->
                        </div><!--form-group-->

                    {!! Form::close() !!}

                </div><!-- panel body -->

            </div><!-- panel -->

        </div><!-- col-lg-8 -->

    </div><!-- row -->
@endsection
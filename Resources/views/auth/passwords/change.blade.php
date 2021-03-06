@extends('layouts.master')

@section('content')
    <div class="row">

        <div class="col-lg-10 col-lg-offset-1">

            <div class="card panel-default">
                <div class="card-heading">{{ trans('labels.frontend.user.passwords.change') }}</div>

                <div class="card-block">

                    {!! Form::open(['route' => ['auth.password.update']]) !!}

                        <div class="form-group">
                            {!! Form::label('old_password', trans('validation.attributes.frontend.old_password'), ['class' => 'form-control-label']) !!}
                            <div class="control-input">
                                {!! Form::input('password', 'old_password', null, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.frontend.old_password')]) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('password', trans('validation.attributes.frontend.new_password'), ['class' => 'form-control-label']) !!}
                            <div class="control-input">
                                {!! Form::input('password', 'password', null, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.frontend.new_password')]) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('password_confirmation', trans('validation.attributes.frontend.new_password_confirmation'), ['class' => 'form-control-label']) !!}
                            <div class="control-input">
                                {!! Form::input('password', 'password_confirmation', null, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.frontend.new_password_confirmation')]) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="control-input">
                                {!! Form::submit(trans('labels.general.buttons.update'), ['class' => 'btn btn-primary']) !!}
                            </div>
                        </div>

                    {!! Form::close() !!}

                </div><!--panel body-->

            </div><!-- panel -->

        </div><!-- col-lg-10 -->

    </div><!-- row -->
@endsection
{!! Form::open(['url' => 'login']) !!}

    <div class="form-group">
        <div class="control-input">
            {!! Form::input('email', 'email', null, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.frontend.email')]) !!}
        </div>
    </div><!--form-group-->

    <div class="form-group">
        <div class="control-input">
            {!! Form::input('password', 'password', null, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.frontend.password')]) !!}
        </div>
    </div><!--form-group-->

    <div class="form-group">
        <div class="control-input">
            {!! Form::submit('Sign In', ['class' => 'btn btn-primary']) !!}
        </div>
    </div><!--form-group-->

    <div class="form-group checkbox checkbox-primary">
        {!! Form::checkbox('remember', 1, null, ['id' => 'remember']) !!}
        {!! Form::label('remember', trans('labels.frontend.auth.remember_me'), ['class' => 'control-label']) !!}
    </div><!--form-group-->

{!! Form::close() !!}
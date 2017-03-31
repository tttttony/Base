{!! Form::open(['url' => 'login']) !!}

    <div class="form-group">
        <div class="control-input">
            {!! Form::input('email', 'email', null, ['class' => 'form-control', 'placeholder' => trans('placeholders.attributes.email')]) !!}
        </div>
    </div><!--form-group-->

    <div class="form-group">
        <div class="control-input">
            {!! Form::input('password', 'password', null, ['class' => 'form-control', 'placeholder' => trans('placeholders.attributes.password')]) !!}
        </div>
    </div><!--form-group-->

    <div class="form-group">
        <div class="control-input">
            {!! Form::submit('Sign In', ['class' => 'btn btn-primary block full-width m-b']) !!}
        </div>
    </div><!--form-group-->

    <div class="form-group checkbox checkbox-primary">
        {!! Form::checkbox('remember', 1, null, ['id' => 'remember']) !!}
        {!! Form::label('remember', trans('labels.auth.remember_me'), ['class' => 'form-control-label']) !!}
    </div><!--form-group-->

{!! Form::close() !!}
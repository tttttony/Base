@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-xs-12 col-sm-8 col-sm-offset-2">

            <div class="panel panel-default">
                <div class="panel-heading">{{ trans('labels.frontend.auth.login_box_title') }}</div>

                <div class="panel-body">

                    @include('base::auth.partials.login-form')

                    <div class="row text-center">
                        {!! $socialite_links !!}
                    </div>
                </div><!-- panel body -->

            </div><!-- panel -->

        </div><!-- col-md-8 -->

    </div><!-- row -->

@endsection
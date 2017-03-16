@extends('layouts.master')

@section ('title', trans('labels.access.users.management'))

@section('content')
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans('labels.access.users.active') }}</h3>

            <div class="box-tools pull-right">
                @include('base::access.includes.partials.user-header-buttons')
            </div><!--box-tools pull-right-->
        </div><!-- /.box-header -->

        <div class="box-body">
            @include('base::items', [
                'items' => $users,
                'label' => trans('base::access.users.management'),
                 'cols' => [
                    ['header' => 'Name', 'attribute' => 'name'],
                    ['header' => 'Email', 'attribute' => 'email'],
                    ['header' => 'Confirmed', 'attribute' => 'confirmed'],
                    ['header' => 'Role', 'attribute' => 'roles'],
                ],
                'actions' => [
                    ['route'=> 'admin.access.users.edit', 'title' => 'Edit', 'attributes' => [], 'permission' => 'users.access.edit'],
                ]
            ])
        </div><!-- /.box-body -->
    </div><!--box-->

    <!--div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans('history.recent_history') }}</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
            {{-- history()->renderType('User') --}}
        </div>
    </div-->
@endsection

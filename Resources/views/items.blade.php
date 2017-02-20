<div class="box box-success">
    <div class="box-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        @foreach ($cols as $table_col)
                            <th>{{ $table_col['header'] }}</th>
                        @endforeach
                            <th></th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($items as $item)
                    <tr>
                        @foreach ($cols as $table_col)
                            <td>{{ $item->{$table_col['attribute']} }}</td>
                        @endforeach
                        <td>
                            @foreach ($actions as $action)
                                @permission($action['permission'])
                                    {{ link_to_route($action['route'], $action['title'], $item->id, $action['attributes']) }}
                                @endauth
                            @endforeach
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="pull-left">
            {!! $items->total() !!} {{ trans_choice($label ?: 'items', $items->total())  }}
        </div>

        <div class="pull-right">
            {!! $items->render() !!}
        </div>

        <div class="clearfix"></div>
    </div><!-- /.box-body -->
</div><!--box-->
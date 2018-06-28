@extends('partials.table-wrapper')

@section('table')
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
                <td>
                    @if(array_key_exists('relationship', $table_col) && $table_col['relationship'])
                        @php
                            //TODO: make this better
                            $display = [];
                            if(count($item->{$table_col['relationship']}) > 0) {
                                if($item->{$table_col['relationship']} instanceof \Illuminate\Database\Eloquent\Model) {
                                    $display[] = $item->{$table_col['relationship']}->present()->{$table_col['attribute']};
                                }
                                else {
                                    $item->{$table_col['relationship']}->each(function($i) use(&$display, $table_col){
                                        $display[] = $i->present()->{$table_col['attribute']};
                                    });
                                }
                            }
                        @endphp

                        @if(array_key_exists('html', $table_col) && $table_col['html'])
                            {!! implode('<br />', $display) !!}
                        @else
                            {{ implode(', ', $display) }}
                        @endif
                    @else
                        @if(array_key_exists('html', $table_col) && $table_col['html'])
                            {!! $item->present()->{$table_col['attribute']} !!}
                        @else
                            {{ $item->present()->{$table_col['attribute']} }}
                        @endif
                    @endif
                </td>
            @endforeach
            <td>
                <div class="float-right">
                @foreach ($actions as $action)
                    @permission($action['permission'])
                        <span class="action">
                            @if(isset($action['template']))
                                @includeIf($action['template'])
                            @endif

                            @if(isset($action['route']))
                                <a href="{{ URL::route($action['route'], array_merge(['id' => $item->getKey()], ($action['params'])?$action['params']:[])) }}" {!! app('html')->attributes($action['attributes']) !!}>
                                    @if(isset($action['icon']))
                                        <i class="fa fa-{{ $action['icon'] }}" data-toggle="tooltip" title="{{ $action['title'] }}"></i>
                                    @else
                                        {{ $action['title'] }}
                                    @endif
                                </a>
                            @endif
                        </span>
                    @endauth
                @endforeach
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
@endsection
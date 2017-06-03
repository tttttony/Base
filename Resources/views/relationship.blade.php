{{--
    relationship
        title - title for item
        add_view - blade template for form to add item
        view - relationship view or item
        selected - already selected id's
--}}
@if((array_key_exists('title', $relationship) and $relationship['title']) or (array_key_exists('add_view', $relationship) and $relationship['add_view']))
<div class="card relationship">
    <div class="card-header">
        {{ $relationship['title'] }}

        @if(array_key_exists('add_view', $relationship) and $relationship['add_view'])
            <a class="add-relationship" href="#"><i class="fa fa-plus"></i></a>
        @endif
    </div>
    <div class="card-block">
        @if(array_key_exists('add_view', $relationship) and $relationship['add_view'])
            <script class="add-form" type="template">
            @include($relationship['add_view'])
            </script>
        @endif

        @include($relationship["view"], ['selected' => $relationship['selected']])
    </div>
</div>
@else
    @include($relationship["view"], ['selected' => $relationship['selected']])
@endif
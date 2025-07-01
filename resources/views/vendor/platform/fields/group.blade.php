<div class="d-flex flex-column grid d-md-grid form-group {{ $align }}"
    @php
        $columns = $widthColumns;

        // Optionally allow fallback to dynamic responsive grid
        if ($widthColumns && preg_match('/repeat\((\d+),/', $columns, $matches)) {
            $updatedColumns = "repeat(auto-fit, minmax(150px, 1fr))";
        } else {
            $updatedColumns = $columns;
        }
    @endphp

    @style([
        '--bs-columns: '.count($group),
        'grid-template-columns: '. $updatedColumns => $widthColumns !== null,
        'gap: 1rem',
    ])>
    
    @foreach($group as $field)
        <div class="{{ $class }}
                    {{ $loop->first && $itemToEnd ? 'ms-auto': '' }}
            ">
            {!! $field !!}
        </div>
    @endforeach
</div>

<div class="d-flex flex-column grid d-md-grid form-group {{ $align }}"
    @php
        // Example value: "repeat(6, max-content)"
        $columns = $widthColumns;

        // Use regex to extract the number
        if (preg_match('/repeat\((\d+),/', $columns, $matches)) {
            $number = (int)$matches[1] -  (int)$matches[1]/2;
            $updatedColumns = "repeat($number, max-content)";
        } else {
            $updatedColumns = $columns;
        }
    @endphp

    @style([
        '--bs-columns: '.count($group),
        'grid-template-columns: '. $updatedColumns => $widthColumns !== null,
    ])>
    
    @foreach($group as $field)
        <div class="{{ $class }}
                    {{ $loop->first && $itemToEnd ? 'ms-auto': '' }}
            ">
            {!! $field !!}
        </div>
    @endforeach
</div>

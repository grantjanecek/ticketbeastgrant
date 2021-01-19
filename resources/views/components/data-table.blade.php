<div class="border bg-white rounded shadow-md overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-100">
        <tr>
            @foreach($columns as $column)
                <x-data-table-heading>
                    {{ $column['label'] }}
                </x-data-table-heading>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @foreach($rows as $row)
            <tr>
                @foreach($columns as $column)
                    <x-data-table-cell>
                        @php $slot_name = "{$column['name']}_column"; @endphp
                        {{ ${"{$column['name']}_column"} ?? $row->{$column['name']} }}
                    </x-data-table-cell>
                @endforeach
            </tr>
        @endforeach
      </tbody>
    </table>
</div>

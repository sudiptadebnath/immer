@props([
    'name' => 'tblunk',
    'title' => 'Table',
    'url' => "",
    'data' => [],
    'opts' => [],
])

@php

    function getTH($nm,$st) {
        $ans = $nm;
        $ans = preg_replace('/[^a-zA-Z0-9]+/', ' ', $ans);
        $ans = ucwords(strtolower(trim($ans)));
        return $st.$ans;
    }

    foreach($data as &$itm) {
        $tdt = $itm["data"];
        $st = "";
        if(str_starts_with($tdt,"*")) {
            $st = "*";
            $tdt= str_replace("*","",$tdt);
        } elseif (str_starts_with($tdt,"...")) {
            $st = "...";
            $tdt= str_replace("...","",$tdt);
        }
        $itm["data"]= $tdt;
        if(!isset($itm["th"])) $itm["th"] = getTH($tdt,$st);
        if(!isset($itm["name"])) $itm["name"] = $tdt;
    }

    $opts = array_merge([
        "responsive"=>false,
        "style"=>"primary",
        "add"=>"",
        "edit"=>"",
        "delete"=>"",
        "actions"=>"",
        "imp"=>[],
    ], $opts);
    extract($opts);
    $act = ($add || $edit || $delete || $actions);
    $efnm = str_replace(" ","_",$title);
    $autoWidth = true;
@endphp


<div class="container vw-100 mb-3">
    <h3 class="d-flex border-1 border-bottom pb-2">
        {{ $title }}
        @if($imp)
        <div class="dropdown mb-0 ms-auto exportmenu">
            <button class="btn btn-sm btn-outline-{{$style}} dropdown-toggle" type="button" id="exportMenu-{{ $name }}" data-bs-toggle="dropdown" aria-expanded="false">
                Export
            </button>
            <ul class="dropdown-menu" aria-labelledby="exportMenu-{{ $name }}">
                <li><a class="dropdown-item export-btn-{{ $name }}" data-type="copy"><i class="bi bi-clipboard me-2"></i>Copy</a></li>
                <li><a class="dropdown-item export-btn-{{ $name }}" data-type="csv"><i class="bi bi-file-earmark-text me-2"></i>CSV</a></li>
                <li><a class="dropdown-item export-btn-{{ $name }}" data-type="excel"><i class="bi bi-file-earmark-excel me-2"></i>Excel</a></li>
                <li><a class="dropdown-item export-btn-{{ $name }}" data-type="pdf"><i class="bi bi-file-earmark-pdf me-2"></i>PDF</a></li>
                <li><a class="dropdown-item export-btn-{{ $name }}" data-type="print"><i class="bi bi-printer me-2"></i>Print</a></li>
            </ul>
        </div>
        @endif
    </h3>

    <table id="{{ $name }}" class="table table-bordered table-hover table-striped w-100">
        <thead class="table-{{ $style }}">
            <tr>
            @foreach($data as $opt)
            @php
            if(isset($opt["width"])) $autoWidth = false;
            @endphp
                <th>{{ str_replace("*","",str_replace("...","",$opt["th"])) }}</th>
            @endforeach
            @if($act)
                <th>
                    Action
                    @if($add)
                    <button class="btn btn-sm btn-outline-{{$style}}" title="Add"
                        onclick="{{ $add }}()">
                        <i class="bi bi-plus"></i>
                    </button>
                    @endif
                </th>
            @endif           
            </tr>
        </thead>
    </table>
</div>


@push('scripts')
<script>
$(document).ready(function () {
	$.fn.dataTable.moment('DD-MM-YYYY HH:mm:ss');

    @if($imp)
	const skipExport{{ $name }} = { 
		columns: function (idx, data, node) {
			return @json($imp).includes(idx);
		},
        format: {
          body: function (data, row, column, node) {
			return String(data || '')
				.replace(/<br\s*\/?>/gi, '\n')
				.replace(/<\/p>/gi, '\n')
				.replace(/<\/div>/gi, '\n')
				.replace(/<\/?p[^>]*>/gi, '')
				.replace(/<\/?div[^>]*>/gi, '')
				.replace(/<img[^>]*>/gi, '') 
				.replace(/<[^>]+>/g, '')
				.trim();
		  }
        }
	};
    @endif
    
    var {{ $name }} = $('#{{ $name }}').DataTable({
        autoWidth: {{ $autoWidth ? 'true' : 'false' }},
        order: [],
    @if($responsive)
        responsive: true,
    @else
        scrollX: true,
    @endif
        processing: true,
        serverSide: true,
        ajax: "{{ $url }}",
    @if($imp)
        buttons: [
			{ extend: 'copy', filename: '{{ $efnm }}', title: '{{ $title }}', 
            className: 'btn-copy d-none', exportOptions: skipExport{{ $name }} },
			{ extend: 'csv', filename: '{{ $efnm }}', title: '{{ $title }}', 
            className: 'btn-csv d-none', exportOptions: skipExport{{ $name }} },
			{ extend: 'excel', filename: '{{ $efnm }}', title: '{{ $title }}', 
            className: 'btn-excel d-none', exportOptions: skipExport{{ $name }} },
			{ extend: 'pdf', filename: '{{ $efnm }}', title: '{{ $title }}', 
            className: 'btn-pdf d-none', exportOptions: skipExport{{ $name }} },
			{ extend: 'print', filename: '{{ $efnm }}', title: '{{ $title }}', 
            className: 'btn-print d-none', exportOptions: skipExport{{ $name }} },
		],
    @endif
        createdRow: function (row, data, dataIndex) {
        @foreach($data as $opt)
        @if(strpos($opt["th"], "*") !== false )
            $(row).addClass(data["{{ $opt['name'] }}"].toLowerCase());
        @endif
        @endforeach
        },
        columns: [
        @foreach ($data as $col)
        {
            @foreach ($col as $key => $val)
                @if ($key === 'render')
                    {{ $key }}: {!! $val !!},
                @else
                    {{ $key }}: '{{ $val }}',
                @endif
                @if(strpos($col["th"], "...") !== false )
                    render: function(data, type, row) {
                        return "<div class='bigtxt'>"+data+"</div>";
                    },
                @endif
        @endforeach
        },
        @endforeach
        @if ($edit || $delete || $actions)
        {
            data: null, orderable: false, searchable: false,
            className: 'text-center',
            render: function actBtns(data, type, row) {
                return `
                @if($edit)
                <button class="btn btn-sm btn-link px-1" title="Edit"
                    onclick="{{ $edit }}(${row.id})">
                    <i class="text-info bi bi-pencil"></i>
                </button>
                @endif
                @if($delete)
                <button class="btn btn-sm btn-link px-1" title="Delete"
                    onclick="{{ $delete }}(${row.id})">
                    <i class="text-danger bi bi-trash"></i>
                </button>
                @endif
                {!! str_replace('__', '${row.id}', $actions) !!}
                `;                    
            },
        },
        @endif
        ],
    });

    @if($imp)
	$('.export-btn-{{ $name }}').on('click', function () {
		var type = $(this).data('type');
		{{ $name }}.button(`.btn-${type}`).trigger();
	});	
    @endif
    
    window.addEventListener('resize', function () {
        $('#{{ $name }}').DataTable().columns.adjust().responsive.recalc();
    });
});


</script>
@endpush

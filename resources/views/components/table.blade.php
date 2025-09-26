@props([
    'name' => 'tblunk',
    'title' => 'Table',
    'url' => "",
    'data' => [],
    'opts' => [],
])

@once
@push("styles")
<style>
.fullwidth { width: calc(100vw - 15rem) !important; }
@media (max-width: 576px) {
    .fullwidth { width: calc(100vw - 1rem) !important; }
}
.dataTables_wrapper .dataTables_paginate {
    margin-top: 5px !important;
}
</style>
@endpush
@endonce

@php

    if(!function_exists("getTH")) {
        function getTH($nm,$st) {
            $ans = $nm;
            $ans = preg_replace('/[^a-zA-Z0-9]+/', ' ', $ans);
            $ans = ucwords(strtolower(trim($ans)));
            return $st.$ans;
        }
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
        "rowreorder"=>[],
        "responsive"=>false,
        "scrollY"=>"",
        "style"=>"primary",
        "add"=>"",
        "edit"=>"",
        "delete"=>"",
        "actions"=>"",
        "plain"=>false,
        "imp"=>[],
		"ajaxdata"=>"",
    ], $opts);
    extract($opts);
    $act = ($add || $edit || $delete || $actions);
    $efnm = str_replace(" ","_",$title);
    $autoWidth = $responsive;
@endphp

@if(!$plain)
<div class="container-fluid m-0 p-3 fullwidth">
    <div class="d-flex flex-wrap align-items-center justify-content-between border-1 border-bottom pb-1 mb-2 gap-1">
        <h3 class="d-flex flex-wrap gap-1">
            {{ $title }}
            @if(!empty($imp))
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
        {{ $slot }}
        @if($act)
        @if(is_array($add))
			@foreach($add as $btn)
				<button class="btn btn-sm btn-link text-{{ $style }} m-0 p-1" data-bs-toggle="tooltip" data-bs-placement="top" title="{{$btn[0]}}" 
					onclick="{{$btn[2]}}()">
					<i class="bi bi-{{$btn[1]}}"></i>
				</button>
			@endforeach
		@else
			<button class="addmore_btn btn btn-sm btn-outline-{{ $style }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Add"
				onclick="{{ $add }}()">
				<i class="bi bi-plus"></i>
				Add
			</button>
		@endif
		@endif
    </div>
@endif

    <table id="{{ $name }}" class="table table-bordered table-hover table-striped {{$responsive ?  'w-100' : ''}}">
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
                    <!-- @if(is_array($add))
                        @foreach($add as $btn)
                            <button class="btn btn-sm btn-link text-{{ $style }} m-0 p-1" title="{{$btn[0]}}"
                                onclick="{{$btn[2]}}()">
                                <i class="bi bi-{{$btn[1]}}"></i>
                            </button>
                        @endforeach
                    @else
                        <button class="btn btn-sm btn-outline-{{ $style }}" title="Add"
                            onclick="{{ $add }}()">
                            <i class="bi bi-plus"></i>
                        </button>
                    @endif -->
                </th>
            @endif           
            </tr>
        </thead>
    </table>
@if(!$plain)
</div>
@endif


@push('scripts')
<script>
var {{ $name }};
$(document).ready(function () {
	$.fn.dataTable.moment('DD-MM-YYYY HH:mm:ss');

    @if(!empty($imp))
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
    
    {{ $name }} = $('#{{ $name }}').DataTable({
        autoWidth: {{ $autoWidth ? 'true' : 'false' }},
        order: [],
    @if($rowreorder)
        rowReorder: {
            dataSrc: '{{ $rowreorder[0] }}'
        },
    @endif
    @if($responsive)
        responsive: true,
    @else
        scrollX: true,
    @endif
    @if($scrollY)
        scrollY: "{{ $scrollY }}", 
    @endif
        processing: true,
        serverSide: true,
        ajax: {
			url: "{{ $url }}",
    @if($ajaxdata)
            data: {{ $ajaxdata }},
    @endif
		},
        language: {
            paginate: {
                previous: '<<',
                next: '>>'
            }
        },
        @if(!empty($imp))
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
				@if ($key === 'render' || is_array($val) || is_object($val))
					{{ $key }}: {!! is_string($val) ? $val : json_encode($val) !!},
				@elseif (is_bool($val))
					{{ $key }}: {{ $val ? 'true' : 'false' }},
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
                <button class="btn btn-sm btn-link px-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"
                    onclick="{{ $edit }}(${row.id})">
                    <i class="text-info bi bi-pencil"></i>
                </button>
                @endif
                @if($delete)
                <button class="btn btn-sm btn-link px-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete"
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

    @if(!empty($imp))
	$('.export-btn-{{ $name }}').on('click', function () {
		var type = $(this).data('type');
		{{ $name }}.button(`.btn-${type}`).trigger();
	});	
    @endif
    
    window.addEventListener('resize', function () {
        $('#{{ $name }}').DataTable().columns.adjust().responsive.recalc();
    });
    
    @if($rowreorder)
    {{ $name }}.on('row-reorder', function (e, diff, edit) {
        let order = [];
        for (let i = 0; i < diff.length; i++) {
            order.push({
                id: {{ $name }}.row(diff[i].node).data().id,
                position: diff[i].newPosition 
            });
        }
        $.ajax({
            url: '{{ $rowreorder[1] }}',
            method: 'POST',
            data: { order: order, _token: '{{ csrf_token() }}' }
        });
    });
    @endif

    {{ $name }}.on('draw', function () {
        $('[data-bs-toggle="tooltip"]').each(function () {
            var existing = bootstrap.Tooltip.getInstance(this);
            if (existing) existing.dispose();
            new bootstrap.Tooltip(this);
        });
    });    

});


</script>
@endpush

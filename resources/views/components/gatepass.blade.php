@props([
	"file",
	"puja"
])

@push("scripts")
<style>
	body { font-family: DejaVu Sans, sans-serif; }
	.container { text-align: center; margin:0px; padding:0px; }
	.qr { margin: 20px auto; }
	td { padding: 8px 12px; border: 1px solid #ddd; }
	table { margin: 0px auto; }
</style>
@endpush

<div class="container">
	<h2>GatePass</h2>
	
	<div class="container border p-0 d-flex flex-column">
	<div class="qr">
		<img src="{{ $file }}" width="200" alt="QR Code">
	</div>

	<table class="table table-bordered table-sm m-0 p-0" cellspacing="0" cellpadding="0">
		<thead class="table-light">
			<tr>
				<th colspan="2">Puja Committee Details</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><strong>Name</strong></td>
				<td>{{ $puja->puja_committee_name }}</td>
			</tr>
			@if($puja->action_area)
			<tr>
				<td><strong>Location</strong></td>
				<td>{{ $puja->action_area }}, {{ $puja->category }}</td>
			</tr>
			@endif
			@if($puja->puja_committee_address)
			<tr>
				<td><strong>Address</strong></td>
				<td>{{ $puja->puja_committee_address }}</td>
			</tr>
			@endif
			<tr>
				<td><strong>Secretary</strong></td>
				<td>{{ $puja->secretary_name }} ({{ $puja->secretary_mobile }})</td>
			</tr>
			<tr>
				<td><strong>Chairman</strong></td>
				<td>{{ $puja->chairman_name }} ({{ $puja->chairman_mobile }})</td>
			</tr>
			<tr>
				<td><strong>Proposed Immersion</strong></td>
				<td>
					{{ $puja->proposed_immersion_date ? \Carbon\Carbon::parse($puja->proposed_immersion_date)->format('d M Y') : '' }}
					{{ $puja->proposed_immersion_time }}
				</td>
			</tr>
			@if($puja->vehicle_no)
			<tr>
				<td><strong>Vehicle No</strong></td>
				<td>{{ $puja->vehicle_no }}</td>
			</tr>
			@endif
			@if(1>2 && $puja->team_members)
			<tr>
				<td><strong>Team Members</strong></td>
				<td>{{ $puja->team_members }}</td>
			</tr>
			@endif
		</tbody>
	</table>
	</div>
</div>


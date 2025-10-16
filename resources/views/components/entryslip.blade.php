@props([
"src",
"puja",
"repoAtt",
"passtitle"
])

<div class="card Registration_card">
	<h4 class="head">
		<span>{{ $passtitle }}</span>
		Immersion of Durga Idol at NKDA Bisarjan Ghat
	</h4>
	<div class="content">
		<!-- QR Code -->
		<div class="qr-cell">
			<img src="{{asset("public/qrs/{$puja->id}.png")}}" alt="QR Code">
		</div>
		<div class="info2">
			<span>Puja in New Town Area ?</span>
			<span style="border: 1px solid #585858; border-radius: 2px;">{{ $puja->action_area ? "✔️" : "❌" }}</span>
		</div>

		<!-- Details Table -->
		<div class="details-cell">
			<table class="details">
				<thead>
					<tr>
						<th colspan="2">
							<p>Puja Committee Details</p>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="2" class="commitee">{{ $puja->puja_committee_name }}</td>
					</tr>
					@if($puja->puja_committee_address)
					<tr>
						<td colspan="2">{{ $puja->puja_committee_address }}</td>
					</tr>
					@endif
					<tr>
						<td colspan="2">{{ $puja->secretary_name ? $puja->secretary_name. " - " : "" }} <b>{{ $puja->secretary_mobile }}</b> 
						@if($puja->no_of_vehicles)
							- (Vehicle# {{ $puja->no_of_vehicles }}) 
						@endif
						</td>
					</tr>
					@if($puja->chairman_name)
					<tr>
						<td colspan="2">{{ $puja->chairman_name }} - <b>{{ $puja->chairman_mobile }}</b></td>
					</tr>
					@endif
					<tr>
						<td colspan="2" class="datetime">
							<span>Date of Immersion:</span>
							{{ $puja->proposed_immersion_date ? \Carbon\Carbon::parse($puja->proposed_immersion_date)->format('d M Y') : '' }}
							{{ $puja->proposed_immersion_time ? \Carbon\Carbon::parse($puja->proposed_immersion_time)->format('h:i A') : '' }}
						</td>
					</tr>
					@if($repoAtt && $repoAtt->scan_datetime)
					<tr>
						<td colspan="2" class="datetime">
							<span>Reported At</span>
							{{ \Carbon\Carbon::parse($repoAtt->scan_datetime)->setTimezone('Asia/Kolkata')->format('d M Y h:i A') }}
						</td>
					</tr>
					@endif
					@if($puja->team_members)
					<tr>
						<td colspan="2" style="padding-top: 6px;">No of Dhunuchi Participant - <b>{{ $puja->team_members }}</b></td>
					</tr>
					<tr>
						<td colspan="2" style="font-size: 0.62rem;">(According to space max 15 persons shall be allowed for participation.)</td>
					</tr>
					@endif
				</tbody>
			</table>
		</div>
	</div>
	<!-- <div class="bottombar">
		<div class="info">
			<span class="label">Address:</span>
			<span>Administrative Building. Plot No - DG/13, Premises
				No - 04-3333, Action Area - ID, New Town, Kolkata - 700156</span>
		</div>
		<div class="countersig">
			<p class="blank"></p>
			<p class="sigtext">Counter Signature</p>
		</div>
	</div> -->
	<!-- <div class="info" style="font-size: 0.6em; margin: 0; padding-top: 10px; border-top: 1px solid #ccc;">
		<span class="label" style="min-width: auto;">Note:</span>
		<span>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has
			been the industry's standard dummy text ever since the 1500s,</span>
	</div> -->
</div>
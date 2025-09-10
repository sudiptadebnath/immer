<x-gatepass :file="asset('qrs/'.$puja->id.'.png')" 
	:puja="$puja" :pdf="false">
<div class="download-container">
    <a class="btn-danger" href="{{ route('puja.gpass.pdf', $puja->token) }}">
        &#128462; Download PDF
    </a>
</div>
</x-gatepass>

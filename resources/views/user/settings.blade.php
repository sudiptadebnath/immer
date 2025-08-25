@extends('layouts.app')

@section('content')
<div class="container m-3">
<div class="row g-2">
    <x-card title="Settings" icon="gear">
        <div class="row g-5">
        <form>
        <x-checkbox name="terms" title="Show SignUp" checked="true" size="6"/>
        <div class="col-md-12">
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="bi bi-save"></i> Save
            </button>
            <button type="reset" class="btn btn-info btn-sm">
                <i class="bi bi-arrow-counterclockwise"></i> Reset
            </button>
        </div>       
        </form>
        </div>       
    </x-card>
</div>
</div>
@endsection

@push('scripts')
<script>
</script>
@endpush

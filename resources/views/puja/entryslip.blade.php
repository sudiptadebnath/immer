<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Immersion Pass</title>
</head>

<body>

<x-entryslip src="{{ asset('resources/img/logo-nkda.png') }}" :puja="$puja" :repoAtt="$repoAtt" />

@if($puja->team_members)
<div class="pgbrk"></div>
<x-entryslip src="{{ asset('resources/img/logo-nkda.png') }}" :puja="$puja" :repoAtt="$repoAtt" />
@endif

</body>

</html>
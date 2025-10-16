<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>NKDA Pass</title>

    <style>
	@font-face {
		font-family: 'Book Antiqua';
		font-weight: bold;
		src: url("<?= asset('public/fonts/BookAntiqua-Bold.woff2') ?>") format('woff2'),
			url("<?= asset('public/fonts/BookAntiqua-Bold.woff') ?>") format('woff'),
			url("<?= asset('public/fonts/bookantiqua_bold.ttf') ?>") format('truetype');
	}

	@font-face {
		font-family: 'Book Antiqua';
		font-weight: normal;
		src: url("<?= asset('public/fonts/BookAntiqua.woff2') ?>") format('woff2'),
			url("<?= asset('public/fonts/BookAntiqua.woff') ?>") format('woff'),
			url("<?= asset('public/fonts/bookantiqua.ttf') ?>") format('truetype');
	}

	@font-face {
		font-family: 'Book Antiqua';
		font-weight: normal;
		src: url("{{ asset('public/fonts/bookantiqua.ttf') }}") format('truetype');
	}

	@font-face {
		font-family: 'Book Antiqua';
		font-weight: bold;
		src: url("{{ asset('public/fonts/bookantiqua_bold.ttf') }}") format('truetype');
	}


	body {
		font-family: 'Book Antiqua';
		font-size: 12px;
		margin: 20px auto;
		padding: 20px;
	}

	.card {
		max-width: 350px;
		padding: 5px;
		margin: 0 auto;
		box-shadow: 3px 3px 8px rgba(0, 0, 0, 0.3);
		border: 0;
		width: 100%;
		box-sizing: border-box;
		border-radius: 8px;
	}

	.Registration_card {
		position: relative;
		/* padding: 10px;
            border: 1px solid #585858;
            max-width: 600px;
            margin: 0 auto; */
		background-image: url('../../../resources/img/bg-mobile.jpg'),
		url('{{ asset("resources/img/bg-mobile.jpg") }}');
		background-repeat: no-repeat;
		background-size: cover;
		background-position: top center;
		/* background-color: #736c24; */
		z-index: 1;
		overflow: hidden;
	}

	.Registration_card::before {
		content: "";
		position: absolute;
		/* top: -5px; */
		/* left: 50%; */
		/* transform: translateX(-50%); */
		inset: 0;
		background-color: rgba(255, 255, 255, 0.7);
		/* background-color: #e3e1d2; */
		/* display: inline-block; */
		/* width: 60px; */
		/* height: 31px; */
		width: 100%;
		height: 100%;
		/* border-radius: 0px 0px 50px 50px; */
		overflow: hidden;
		z-index: -1;
	}


	/* .Registration_card .head {
            text-align: center;
            margin: 5px 0 10px 0;
        } */

	.Registration_card .head {
		font-size: 12px;
		width: 100%;
		text-align: center;
		margin: 10px auto 15px;
		font-weight: bold;
		/* color: #ddcbb5; */
		color: #000;
		letter-spacing: normal;
	}

	.Registration_card .head span {
		display: block;
		font-size: 22px;
		color: #177f88;
		font-weight: bold;
		text-transform: uppercase;
		padding-bottom: 2px;
	}

	h2 {
		text-align: center;
		margin: 5px 0 10px 0;
	}

	/* .content {
            display: table;
            width: 100%;
        } */
	.content {
		display: block;
		width: 100%;
	}

	.qr-cell,
	.details-cell {
		/* display: table-cell; */
		vertical-align: top;
	}

	/* .qr-cell {
            width: 35%;
            text-align: center;
        } */

	.qr-cell {
		width: 100%;
		max-width: 150px;
		height: auto;
		text-align: center;
		margin: 0 auto 10px;
	}

	.qr-cell img {
		max-width: 100%;
		height: auto;
		object-fit: contain;
	}

	/* .details-cell {
            width: 65%;
            padding-left: 5px;
        } */

	.details-cell {
		width: 95%;
		margin: 0 auto 10px;
	}

	table.details {
		width: 100%;
		border-collapse: collapse;
	}

	table.details th {
		/* background: #fff; */
		text-align: center;
		padding: 2px 5px 4px;
		border-top: 1px solid #177f88;
		border-bottom: 1px solid #177f88;
	}

	table.details th p {
		font-size: 14px;
		font-weight: normal;
		letter-spacing: 1px;
		line-height: normal;
		/* color: #fff; */
		color: #177f88;
		padding: 0;
		margin: 0;
	}

	table.details td {
		padding: 0px 5px;
		font-size: 11px;
		line-height: normal;
		text-align: center;
		/* color: #c9c3a3; */
		color: #000;
		font-weight: normal;
		vertical-align: top;
		/* border: 1px solid #000000a4; */
	}

	table.details tr:last-child td:last-child {
		vertical-align: bottom;
	}

	table.details td.commitee {
		font-size: 16px;
		line-height: 16px;
		font-weight: bold;
		color: #030000;
		padding: 6px 5px 2px;
	}

	table.details td.datetime {
		font-size: 12px;
		font-weight: bold;
		/* color: #f1e3e0; */
		color: #000;
		padding-top: 6px;
	}

	.download-container {
		text-align: center;
		margin-top: 20px;
	}

	.btn {
		display: inline-block;
		padding: 8px 16px;
		background: #354edc;
		color: #fff;
		text-decoration: none;
		border-radius: 6px;
		font-size: 14px;
		font-family: DejaVu Sans, sans-serif;
		cursor: pointer;
		border: 0px;
	}

	.btn-danger {
		background: #dc3545;
		color: #fff;
	}

	.btn-danger:hover {
		background: #bb2d3b;
	}

	.btn-primary {
		background: #354edc;
		color: #fff;
	}

	.btn-primary:hover {
		background: #2f3e94;
	}


	#sms-result {
		margin-top: 30px;
	}

	.alert {
		box-shadow: 3px 3px 8px rgba(0, 0, 0, 0.3);
		width: 100%;
		max-width: 450px;
		padding: 0.75rem 1rem;
		margin: 0.5rem auto;
		border-radius: 0.375rem;
		font-family: system-ui, sans-serif;
		font-size: 0.95rem;
		border-left: 4px solid transparent;
		text-align: left;
	}

	.success {
		background: #e9f7ef;
		color: #0f5132;
		border-left-color: #198754;
	}

	.danger {
		background: #f8d7da;
		color: #842029;
		border-left-color: #dc3545;
	}

	.info2 {
		font-size: 0.7em;
		margin-bottom: 6px;
		display: flex;
		align-items: center;
		justify-content: center;
		gap: 8px;
		color: black;
	}
	
	
		
	@media print {
		.pgbrk::before {
			content: "";
			display: block;
			page-break-before: always;
		}
	}
	
	
</style>
</head>

<body>

<x-entryslip src="{{ asset('resources/img/logo-nkda.png') }}" :puja="$puja" :repoAtt="$repoAtt" passtitle="Immersion Pass" />

@if($puja->team_members)
<div class="pgbrk"></div>
<x-entryslip src="{{ asset('resources/img/logo-nkda.png') }}" :puja="$puja" :repoAtt="$repoAtt" passtitle="Dunachi Dance Pass" />
@endif

</body>

</html>
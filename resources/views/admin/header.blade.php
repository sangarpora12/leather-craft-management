<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berliano Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black text-white flex">

    <?php 
        // Default page kosong jika tidak didefinisikan
        if(!isset($page)) { $page = ''; } 
    ?>

    @if($page != 'laporan')
        @include('components.sidebar') 
    @endif

    <main class="flex-1 p-8 {{ $page != 'laporan' ? 'ml-64' : '' }}">
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Email Robot</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100">
    <div class="container mx-auto">
        <x-cabecalho-git></x-cabecalho-git>
        <x-notificacao-erro></x-notificacao-erro>
        <x-notificacao-sucesso></x-notificacao-sucesso>
        <x-gerenciar-regras></x-gerenciar-regras>
    </div>
</body>

</html>

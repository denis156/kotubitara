<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Kematian {{ $record->no_surat_kematian }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    @include('filament.resources.kematians.pages._kematian-content', [
        'record' => $record
    ])
</body>
</html>

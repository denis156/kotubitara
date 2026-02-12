<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Pengantar Kelahiran {{ $record->no_surat_kelahiran }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    @include('filament.resources.kelahirans.pages._kelahiran-content', [
        'record' => $record
    ])
</body>
</html>

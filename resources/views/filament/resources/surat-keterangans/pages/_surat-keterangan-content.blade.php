@php
    $jenisSurat = $record->jenis_surat->value;
    $templatePath = "filament.resources.surat-keterangans.pages.templates.{$jenisSurat}";
@endphp

@if(view()->exists($templatePath))
    @include($templatePath, ['record' => $record])
@else
    {{-- Fallback jika template belum dibuat --}}
    <div class="mx-auto overflow-hidden bg-white" style="width: 210mm; height: 297mm; padding: 15mm 20mm; color: #000000 !important; font-family: 'Times New Roman', Times, serif;">
        <div class="flex items-center justify-center h-full">
            <div class="text-center">
                <h2 class="text-2xl font-bold mb-4">Template Belum Tersedia</h2>
                <p class="text-lg">Template untuk {{ $record->jenis_surat->getLabel() }} sedang dalam pengembangan.</p>
                <p class="text-sm text-gray-600 mt-2">Jenis: {{ $jenisSurat }}</p>
            </div>
        </div>
    </div>
@endif

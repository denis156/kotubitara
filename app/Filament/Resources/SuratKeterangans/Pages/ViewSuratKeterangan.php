<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuratKeterangans\Pages;

use App\Filament\Resources\SuratKeterangans\SuratKeteranganResource;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Spatie\LaravelPdf\Facades\Pdf;

class ViewSuratKeterangan extends Page
{
    use InteractsWithRecord;

    protected static string $resource = SuratKeteranganResource::class;

    protected static ?string $navigationLabel = 'Lihat Surat';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected string $view = 'filament.resources.surat-keterangans.pages.view-surat-keterangan';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->icon(Heroicon::ArrowLeft)
                ->color('gray')
                ->url(static::getResource()::getUrl('index')),
            Action::make('downloadPdf')
                ->label('Download PDF')
                ->icon(Heroicon::ArrowDownTray)
                ->color('success')
                ->action(fn () => $this->downloadPdf()),
        ];
    }

    public function downloadPdf(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $safeNoSurat = str_replace(['/', '\\'], '-', $this->record->no_surat ?? 'DRAFT');
        $fileName = 'Surat-Keterangan-'.$safeNoSurat.'.pdf';

        $tempPath = storage_path('app/temp-'.uniqid().'.pdf');

        // Load relasi yang diperlukan
        $this->record->load([
            'penduduk.kartuKeluarga',
            'desa.kecamatan',
            'kepalaDesa',
            'kematian',
        ]);

        Pdf::view('pdfs.surat-keterangan', [
            'record' => $this->record,
        ])
            ->format('a4')
            ->margins(0, 0, 0, 0)
            ->save($tempPath);

        return response()->download($tempPath, $fileName, [
            'Content-Type' => 'application/pdf',
        ])->deleteFileAfterSend(true);
    }

    public function getHeading(): string
    {
        return 'Lihat '.($this->record->no_surat ?? 'DRAFT');
    }

    public function getTitle(): string|Htmlable
    {
        return 'Lihat '.($this->record->no_surat ?? 'DRAFT');
    }

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        // Eager load relasi untuk view
        $this->record->load([
            'penduduk.kartuKeluarga',
            'desa.kecamatan',
            'kepalaDesa',
            'kematian',
        ]);
    }
}

<div class="mx-auto overflow-hidden bg-white" style="width: 210mm; height: 297mm; padding: 15mm 20mm; color: #000000 !important; font-family: 'Times New Roman', Times, serif;">
    {{-- KOP SURAT --}}
    <div class="mb-4 border-b-2 border-black pb-3">
        <div class="flex items-center justify-between gap-4">
            {{-- Logo Kiri --}}
            <div class="shrink-0" style="width: 80px;">
                <img src="{{ asset('images/logo/Logo Sulawesi Tenggara.png') }}" alt="Logo Sulawesi Tenggara" style="width: 80px; height: auto;">
            </div>

            {{-- Kop Tengah --}}
            <div class="grow text-center">
                <h1 class="text-xl font-bold uppercase">Pemerintah {{ $record->desa->nama_kabupaten ?? 'Kabupaten Konawe' }}</h1>
                <h2 class="text-lg font-bold uppercase">Kecamatan {{ $record->desa->nama_kecamatan ?? '' }}</h2>
                <h2 class="text-lg font-bold uppercase">Desa {{ $record->desa->nama_desa ?? '' }}</h2>
                <p class="mt-2 text-sm">{{ $record->desa->alamat ?? '' }}</p>
                @if($record->desa->telepon || $record->desa->email)
                <p class="text-sm">
                    @if($record->desa->telepon)
                        Telp: {{ $record->desa->telepon }}
                    @endif
                    @if($record->desa->telepon && $record->desa->email)
                        |
                    @endif
                    @if($record->desa->email)
                        Email: {{ $record->desa->email }}
                    @endif
                </p>
                @endif
            </div>

            {{-- Logo Kanan --}}
            <div class="shrink-0" style="width: 76px;">
                <img src="{{ asset('images/logo/Logo Kabupaten Konawe.png') }}" alt="Logo Kabupaten Konawe" style="width: 80px; height: auto;">
            </div>
        </div>
    </div>

    {{-- JUDUL SURAT --}}
    <div class="mb-4 text-center">
        <h3 class="text-lg font-bold uppercase underline">Surat Pengantar Kelahiran</h3>
        <p class="mt-1 text-sm">Nomor: {{ $record->no_surat_kelahiran ?? '___________' }}</p>
    </div>

    {{-- ISI SURAT --}}
    <div class="mb-4 text-justify" style="font-size: 13px; line-height: 1.4;">
        <p class="mb-3">Yang bertanda tangan di bawah ini, Kepala Desa {{ $record->desa->nama_desa ?? '' }}, Kecamatan {{ $record->desa->nama_kecamatan ?? '' }}, {{ $record->desa->nama_kabupaten ?? 'Kabupaten Konawe' }}, menerangkan bahwa:</p>

        <table class="mb-3 w-full">
            <tr>
                <td class="w-1/3 py-1">Nama Bayi</td>
                <td class="w-8 py-1">:</td>
                <td class="py-1 font-semibold">{{ $record->nama_bayi ?? '' }}</td>
            </tr>
            @if($record->nik_bayi)
            <tr>
                <td class="py-1">NIK Bayi</td>
                <td class="py-1">:</td>
                <td class="py-1">{{ $record->nik_bayi }}</td>
            </tr>
            @endif
            <tr>
                <td class="py-1">Jenis Kelamin</td>
                <td class="py-1">:</td>
                <td class="py-1">{{ $record->jenis_kelamin?->getLabel() ?? '' }}</td>
            </tr>
            <tr>
                <td class="py-1">Tempat, Tanggal Lahir</td>
                <td class="py-1">:</td>
                <td class="py-1 font-semibold">{{ $record->tempat_lahir ?? '' }}, {{ $record->tanggal_lahir?->format('d F Y') ?? '' }}</td>
            </tr>
            @if($record->waktu_lahir)
            <tr>
                <td class="py-1">Waktu Lahir</td>
                <td class="py-1">:</td>
                <td class="py-1">Pukul {{ \Carbon\Carbon::parse($record->waktu_lahir)->format('H:i') }} WIT</td>
            </tr>
            @endif
            @if($record->berat_lahir || $record->panjang_lahir)
            <tr>
                <td class="py-1">Berat/Panjang Lahir</td>
                <td class="py-1">:</td>
                <td class="py-1">
                    @if($record->berat_lahir)
                        {{ number_format($record->berat_lahir, 2) }} kg
                    @endif
                    @if($record->berat_lahir && $record->panjang_lahir) / @endif
                    @if($record->panjang_lahir)
                        {{ number_format($record->panjang_lahir, 2) }} cm
                    @endif
                </td>
            </tr>
            @endif
        </table>

        <p class="mb-3">Adalah anak dari:</p>

        <table class="mb-3 w-full">
            <tr>
                <td class="w-1/3 py-1">Nama Ayah</td>
                <td class="w-8 py-1">:</td>
                <td class="py-1 font-semibold">{{ $record->ayah->nama_lengkap ?? '-' }}</td>
            </tr>
            @if($record->ayah?->nik)
            <tr>
                <td class="py-1">NIK Ayah</td>
                <td class="py-1">:</td>
                <td class="py-1">{{ $record->ayah->nik }}</td>
            </tr>
            @endif
            @if($record->ayah?->pekerjaan)
            <tr>
                <td class="py-1">Pekerjaan Ayah</td>
                <td class="py-1">:</td>
                <td class="py-1">{{ $record->ayah->pekerjaan }}</td>
            </tr>
            @endif
        </table>

        <table class="mb-3 w-full">
            <tr>
                <td class="w-1/3 py-1">Nama Ibu</td>
                <td class="w-8 py-1">:</td>
                <td class="py-1 font-semibold">{{ $record->ibu->nama_lengkap ?? '-' }}</td>
            </tr>
            @if($record->ibu?->nik)
            <tr>
                <td class="py-1">NIK Ibu</td>
                <td class="py-1">:</td>
                <td class="py-1">{{ $record->ibu->nik }}</td>
            </tr>
            @endif
            @if($record->ibu?->pekerjaan)
            <tr>
                <td class="py-1">Pekerjaan Ibu</td>
                <td class="py-1">:</td>
                <td class="py-1">{{ $record->ibu->pekerjaan }}</td>
            </tr>
            @endif
        </table>

        <p class="mb-3">Surat pengantar ini dibuat berdasarkan laporan dari:</p>

        <table class="mb-3 w-full">
            <tr>
                <td class="w-1/3 py-1">Nama Pelapor</td>
                <td class="w-8 py-1">:</td>
                <td class="py-1 font-semibold">{{ $record->nama_pelapor ?? '' }}</td>
            </tr>
            @if($record->nik_pelapor)
            <tr>
                <td class="py-1">NIK Pelapor</td>
                <td class="py-1">:</td>
                <td class="py-1">{{ $record->nik_pelapor }}</td>
            </tr>
            @endif
            <tr>
                <td class="py-1">Hubungan Pelapor</td>
                <td class="py-1">:</td>
                <td class="py-1">{{ $record->hubungan_pelapor?->getLabel() ?? '' }}</td>
            </tr>
        </table>

        <p class="mb-4">Surat pengantar ini dibuat untuk dipergunakan sebagai kelengkapan pembuatan Akta Kelahiran pada Dinas Kependudukan dan Pencatatan Sipil.</p>
    </div>

    {{-- TTD --}}
    <div class="mt-8" style="display: grid; grid-template-columns: 1fr auto 1fr; grid-template-rows: repeat(3, auto); gap: 0 4rem;">
        {{-- Kolom Kiri: Pelapor --}}
        {{-- Baris 1: Label Pelapor --}}
        <div class="text-center" style="grid-column: 1; grid-row: 1;">
            <p style="margin-bottom: 4px;">Pelapor,</p>
        </div>

        {{-- Baris 2: Tanda Tangan Pelapor --}}
        <div class="flex items-center justify-center" style="grid-column: 1; grid-row: 2; height: 70px; margin-bottom: 4px;">
            @if($record->ttd_pelapor)
                <img src="{{ $record->ttd_pelapor }}" alt="TTD Pelapor" style="max-height: 70px; max-width: 180px; object-fit: contain;">
            @elseif($record->foto_ttd_pelapor)
                <img src="{{ asset('storage/' . $record->foto_ttd_pelapor) }}" alt="Foto TTD Pelapor" style="max-height: 70px; max-width: 180px; object-fit: contain;">
            @endif
        </div>

        {{-- Baris 3: Nama Pelapor --}}
        <div class="text-center" style="grid-column: 1; grid-row: 3;">
            <p class="font-semibold underline" style="margin-bottom: 2px;">{{ $record->nama_pelapor ?? '' }}</p>
        </div>

        {{-- Kolom Kanan: Kepala Desa --}}
        {{-- Baris 1: Label + Tanggal --}}
        <div class="text-center" style="grid-column: 3; grid-row: 1;">
            <p style="margin-bottom: 2px;">{{ $record->desa->nama_desa ?? '' }}, {{ $record->tanggal_surat?->format('d F Y') ?? now()->format('d F Y') }}</p>
            <p style="margin-bottom: 4px;">Kepala Desa {{ $record->desa->nama_desa ?? '' }}</p>
        </div>

        {{-- Baris 2: Tanda Tangan Kepala Desa --}}
        <div class="flex items-center justify-center" style="grid-column: 3; grid-row: 2; height: 70px; margin-bottom: 4px;">
            @if($record->kepalaDesa?->ttd_digital)
                <img src="{{ $record->kepalaDesa->ttd_digital }}" alt="TTD Kepala Desa" style="max-height: 70px; max-width: 180px; object-fit: contain;">
            @endif
        </div>

        {{-- Baris 3: Nama + NIP Kepala Desa --}}
        <div class="text-center" style="grid-column: 3; grid-row: 3;">
            <p class="font-semibold underline" style="margin-bottom: 2px;">{{ $record->kepalaDesa->nama_lengkap ?? '(________________)' }}</p>
            @if($record->kepalaDesa?->nip)
            <p class="text-sm">NIP. {{ $record->kepalaDesa->nip }}</p>
            @endif
        </div>
    </div>
</div>

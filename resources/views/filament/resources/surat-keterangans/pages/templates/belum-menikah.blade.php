@php
            // Helper untuk capitalize nama desa/kecamatan/kabupaten
            $namaDesa = \Str::title(strtolower($record->desa->nama_desa ?? ''));
            $namaKecamatan = \Str::title(strtolower($record->desa->kecamatan->nama_kecamatan ?? ''));
            $namaKabupaten = \Str::title(strtolower($record->desa->kecamatan->nama_kabupaten ?? 'Kabupaten Konawe'));

            // Data pemohon/pelapor
            $namaPemohon = $record->penduduk?->nama_lengkap ?? $record->data_tambahan['nama_pemohon_manual'] ?? '';
            $nikPemohon = $record->penduduk?->nik ?? $record->data_tambahan['nik_pemohon_manual'] ?? '';

            // Data status pernikahan
            $statusPernikahan = $record->data_status_pernikahan['status_pernikahan'] ?? null;
        @endphp

<div class="mx-auto overflow-hidden bg-white" style="width: 210mm; height: 297mm; padding: 15mm 20mm; color: #000000 !important; font-family: 'Times New Roman', Times, serif;">
    {{-- KOP SURAT --}}
    <div class="mb-4 border-b-2 border-black pb-3">
        <div class="flex items-center justify-between gap-4">
            <div class="shrink-0" style="width: 80px;">
                <img src="{{ asset('images/logo/Logo Sulawesi Tenggara.png') }}" alt="Logo Sulawesi Tenggara" style="width: 80px; height: auto;">
            </div>

            <div class="grow text-center">
                <h1 class="text-xl font-bold uppercase">Pemerintah {{ $namaKabupaten }}</h1>
                <h2 class="text-lg font-bold uppercase">Kecamatan {{ $namaKecamatan }}</h2>
                <h2 class="text-lg font-bold uppercase">Desa {{ $namaDesa }}</h2>
                <p class="mt-2 text-sm">{{ $record->desa->alamat ?? '' }}</p>
                @if($record->desa->telepon || $record->desa->email)
                <p class="text-sm">
                    @if($record->desa->telepon)
                        Telp: {{ $record->desa->telepon }}
                    @endif
                    @if($record->desa->telepon && $record->desa->email) | @endif
                    @if($record->desa->email)
                        Email: {{ $record->desa->email }}
                    @endif
                </p>
                @endif
            </div>

            <div class="shrink-0" style="width: 76px;">
                <img src="{{ asset('images/logo/Logo Kabupaten Konawe.png') }}" alt="Logo Kabupaten Konawe" style="width: 80px; height: auto;">
            </div>
        </div>
    </div>

    {{-- JUDUL SURAT --}}
    <div class="mb-4 text-center">
        <h3 class="text-lg font-bold uppercase underline">Surat Keterangan Belum Menikah</h3>
        <p class="mt-1 text-sm">Nomor: {{ $record->no_surat ?? '___________' }}</p>
    </div>

    {{-- ISI SURAT --}}
    <div class="mb-4 text-justify" style="font-size: 13px; line-height: 1.4;">
        <p class="mb-3">Yang bertanda tangan di bawah ini, Kepala Desa {{ $namaDesa }}, Kecamatan {{ $namaKecamatan }}, {{ $namaKabupaten }}, Provinsi Sulawesi Tenggara, dengan ini menerangkan bahwa:</p>

        <table class="mb-3 w-full">
            <tr>
                <td class="w-1/3 py-1">Nama Lengkap</td>
                <td class="w-8 py-1">:</td>
                <td class="py-1 font-semibold">{{ $namaPemohon }}</td>
            </tr>
            <tr>
                <td class="py-1">NIK</td>
                <td class="py-1">:</td>
                <td class="py-1 font-semibold">{{ $nikPemohon }}</td>
            </tr>
            @if($record->penduduk)
            <tr>
                <td class="py-1">Tempat, Tanggal Lahir</td>
                <td class="py-1">:</td>
                <td class="py-1">{{ $record->penduduk->tempat_lahir ?? '' }}, {{ $record->penduduk->tanggal_lahir?->format('d F Y') ?? '' }}</td>
            </tr>
            <tr>
                <td class="py-1">Jenis Kelamin</td>
                <td class="py-1">:</td>
                <td class="py-1">{{ $record->penduduk->jenis_kelamin?->getLabel() ?? '' }}</td>
            </tr>
            <tr>
                <td class="py-1">Pekerjaan</td>
                <td class="py-1">:</td>
                <td class="py-1">{{ $record->penduduk->pekerjaan ?? '' }}</td>
            </tr>
            <tr>
                <td class="py-1" style="vertical-align: top;">Alamat</td>
                <td class="py-1" style="vertical-align: top;">:</td>
                <td class="py-1" style="vertical-align: top;">
                    @if($record->penduduk?->kartuKeluarga)
                        {{ $record->penduduk->kartuKeluarga->alamat ?? '' }}
                        @if($record->penduduk->kartuKeluarga->rt || $record->penduduk->kartuKeluarga->rw)
                            RT {{ $record->penduduk->kartuKeluarga->rt ?? '-' }}/RW {{ $record->penduduk->kartuKeluarga->rw ?? '-' }},
                        @endif
                        Desa {{ $namaDesa }}, Kecamatan {{ $namaKecamatan }}, {{ $namaKabupaten }}
                    @else
                        Desa {{ $namaDesa }}, Kecamatan {{ $namaKecamatan }}, {{ $namaKabupaten }}
                    @endif
                </td>
            </tr>
            @endif
        </table>

        <p class="mb-3">Adalah benar warga Desa {{ $namaDesa }} yang hingga saat ini <strong>belum pernah menikah</strong> dan masih berstatus lajang/bujangan.</p>

        @if($record->keperluan)
        <p class="mb-3">Surat keterangan belum menikah ini dibuat untuk keperluan: <strong>{{ $record->keperluan }}</strong></p>
        @endif

        @if($record->keterangan)
        <p class="mb-4"><strong>Keterangan:</strong> {{ $record->keterangan }}</p>
        @endif

        <p class="mb-4">Demikian surat keterangan belum menikah ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.</p>
    </div>

    {{-- TTD --}}
    <div class="mt-8" style="display: grid; grid-template-columns: 1fr auto 1fr; grid-template-rows: repeat(3, auto); gap: 0 4rem;">
        {{-- Kolom Kiri: Yang Bersangkutan --}}
        <div class="text-center" style="grid-column: 1; grid-row: 1;">
            <p style="margin-bottom: 4px;">Yang Bersangkutan,</p>
        </div>

        <div class="flex items-center justify-center" style="grid-column: 1; grid-row: 2; height: 100px; margin-bottom: 4px;">
            @if(isset($record->data_tambahan['ttd_pemohon']))
                <img src="{{ $record->data_tambahan['ttd_pemohon'] }}" alt="TTD Pemohon" style="max-height: 100px; max-width: 220px; object-fit: contain;">
            @elseif(isset($record->data_tambahan['foto_ttd_pemohon']))
                <img src="{{ asset('storage/' . $record->data_tambahan['foto_ttd_pemohon']) }}" alt="Foto TTD Pemohon" style="max-height: 100px; max-width: 220px; object-fit: contain;">
            @endif
        </div>

        <div class="text-center" style="grid-column: 1; grid-row: 3;">
            <p class="font-semibold underline" style="margin-bottom: 2px;">{{ $namaPemohon }}</p>
        </div>

        {{-- Kolom Kanan: Kepala Desa --}}
        <div class="text-center" style="grid-column: 3; grid-row: 1;">
            <p style="margin-bottom: 2px;">{{ $namaDesa }}, {{ $record->tanggal_surat?->format('d F Y') ?? now()->format('d F Y') }}</p>
            <p style="margin-bottom: 4px;">Kepala Desa {{ $namaDesa }}</p>
        </div>

        <div class="flex items-center justify-center" style="grid-column: 3; grid-row: 2; height: 70px; margin-bottom: 4px;">
            @if($record->kepalaDesa?->ttd_digital)
                <img src="{{ $record->kepalaDesa->ttd_digital }}" alt="TTD Kepala Desa" style="max-height: 70px; max-width: 180px; object-fit: contain;">
            @endif
        </div>

        <div class="text-center" style="grid-column: 3; grid-row: 3;">
            <p class="font-semibold underline" style="margin-bottom: 2px;">{{ $record->kepalaDesa->nama_lengkap ?? '(________________)' }}</p>
            @if($record->kepalaDesa?->nip)
            <p class="text-sm">NIP. {{ $record->kepalaDesa->nip }}</p>
            @endif
        </div>
    </div>
</div>

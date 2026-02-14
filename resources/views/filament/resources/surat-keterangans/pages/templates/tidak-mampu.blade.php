@php
    // Helper untuk capitalize nama desa/kecamatan/kabupaten
    $namaDesa = \Str::title(strtolower($record->desa->nama_desa ?? ''));
    $namaKecamatan = \Str::title(strtolower($record->desa->kecamatan->nama_kecamatan ?? ''));
    $namaKabupaten = \Str::title(strtolower($record->desa->kecamatan->nama_kabupaten ?? 'Kabupaten Konawe'));

    // Data pemohon/pelapor
    $namaPemohon = $record->penduduk?->nama_lengkap ?? $record->data_tambahan['nama_pemohon_manual'] ?? '';
    $nikPemohon = $record->penduduk?->nik ?? $record->data_tambahan['nik_pemohon_manual'] ?? '';

    // Data ekonomi
    $pekerjaan = $record->data_ekonomi['pekerjaan'] ?? ($record->penduduk?->pekerjaan ?? null);
    $penghasilanPerbulan = $record->data_ekonomi['penghasilan_perbulan'] ?? null;
    $jumlahTanggungan = $record->data_ekonomi['jumlah_tanggungan'] ?? null;
    $kondisiRumah = $record->data_ekonomi['kondisi_rumah'] ?? null;
    $kondisiRumahLabel = match($kondisiRumah) {
        'permanen_baik' => 'Permanen (Kondisi Baik)',
        'permanen_rusak' => 'Permanen (Kondisi Rusak)',
        'semi_permanen' => 'Semi Permanen',
        'kayu' => 'Kayu',
        'bambu' => 'Bambu',
        default => $kondisiRumah
    };
    $kepemilikanRumah = $record->data_ekonomi['kepemilikan_rumah'] ?? null;
    $kepemilikanRumahLabel = match($kepemilikanRumah) {
        'milik_sendiri' => 'Milik Sendiri',
        'milik_orangtua' => 'Milik Orang Tua',
        'milik_keluarga' => 'Milik Keluarga',
        'menumpang' => 'Menumpang',
        'kontrak' => 'Kontrak/Sewa',
        default => $kepemilikanRumah
    };
    $alasan = $record->data_ekonomi['alasan'] ?? null;
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
        <h3 class="text-lg font-bold uppercase underline">Surat Keterangan Tidak Mampu</h3>
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
            @endif
            @if($pekerjaan)
            <tr>
                <td class="py-1">Pekerjaan</td>
                <td class="py-1">:</td>
                <td class="py-1">{{ $pekerjaan }}</td>
            </tr>
            @endif
            @if($record->penduduk)
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

        <p class="mb-3">Adalah benar warga Desa {{ $namaDesa }} yang tergolong keluarga kurang mampu dengan kondisi ekonomi sebagai berikut:</p>

        <table class="mb-3 w-full">
            @if($penghasilanPerbulan)
            <tr>
                <td class="w-1/3 py-1">Penghasilan Per Bulan</td>
                <td class="w-8 py-1">:</td>
                <td class="py-1">Rp {{ number_format($penghasilanPerbulan, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($jumlahTanggungan)
            <tr>
                <td class="py-1">Jumlah Tanggungan</td>
                <td class="py-1">:</td>
                <td class="py-1">{{ $jumlahTanggungan }} orang</td>
            </tr>
            @endif
            @if($kondisiRumah)
            <tr>
                <td class="py-1">Kondisi Rumah</td>
                <td class="py-1">:</td>
                <td class="py-1">{{ $kondisiRumahLabel }}</td>
            </tr>
            @endif
            @if($kepemilikanRumah)
            <tr>
                <td class="py-1">Kepemilikan Rumah</td>
                <td class="py-1">:</td>
                <td class="py-1">{{ $kepemilikanRumahLabel }}</td>
            </tr>
            @endif
        </table>

        @if($alasan)
        <p class="mb-3">Dengan alasan: {{ $alasan }}</p>
        @endif

        <p class="mb-3">Yang bersangkutan benar-benar tergolong keluarga kurang mampu dan membutuhkan bantuan.</p>

        @if($record->keperluan)
        <p class="mb-3">Surat keterangan tidak mampu ini dibuat untuk keperluan: <strong>{{ $record->keperluan }}</strong></p>
        @endif

        @if($record->keterangan)
        <p class="mb-4"><strong>Keterangan:</strong> {{ $record->keterangan }}</p>
        @endif

        <p class="mb-4">Demikian surat keterangan tidak mampu ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.</p>
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

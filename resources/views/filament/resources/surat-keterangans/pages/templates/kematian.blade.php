@php
            // Helper untuk capitalize nama desa/kecamatan/kabupaten
            $namaDesa = \Str::title(strtolower($record->desa->nama_desa ?? ''));
            $namaKecamatan = \Str::title(strtolower($record->desa->kecamatan->nama_kecamatan ?? ''));
            $namaKabupaten = \Str::title(strtolower($record->desa->kecamatan->nama_kabupaten ?? 'Kabupaten Konawe'));

            // Data kematian
            $namaAlmarhum = $record->data_kematian['nama_almarhum'] ?? null;
            $nikAlmarhum = $record->data_kematian['nik_almarhum'] ?? null;
            $jenisKelamin = $record->data_kematian['jenis_kelamin'] ?? null;
            $jenisKelaminLabel = match($jenisKelamin) {
                'L' => 'Laki-laki',
                'P' => 'Perempuan',
                default => $jenisKelamin
            };
            $tempatLahir = $record->data_kematian['tempat_lahir'] ?? null;
            $tanggalLahir = $record->data_kematian['tanggal_lahir'] ?? null;
            $umur = $record->data_kematian['umur'] ?? null;
            $agama = $record->data_kematian['agama'] ?? null;
            $pekerjaan = $record->data_kematian['pekerjaan'] ?? null;
            $alamatAlmarhum = $record->data_kematian['alamat_almarhum'] ?? null;
            $tanggalMeninggal = $record->data_kematian['tanggal_meninggal'] ?? null;
            $waktuMeninggal = $record->data_kematian['waktu_meninggal'] ?? null;
            $tempatMeninggal = $record->data_kematian['tempat_meninggal'] ?? null;
            $sebabKematian = $record->data_kematian['sebab_kematian'] ?? null;
            $tempatPemakaman = $record->data_kematian['tempat_pemakaman'] ?? null;

            // Data pelapor
            $namaPelapor = $record->data_kematian['nama_pelapor'] ?? null;
            $nikPelapor = $record->data_kematian['nik_pelapor'] ?? null;
            $hubunganPelapor = $record->data_kematian['hubungan_pelapor'] ?? null;
            $hubunganLabel = match($hubunganPelapor) {
                'suami' => 'Suami',
                'istri' => 'Istri',
                'anak_kandung' => 'Anak Kandung',
                'anak_tiri' => 'Anak Tiri',
                'ayah' => 'Ayah',
                'ibu' => 'Ibu',
                'saudara_kandung' => 'Saudara Kandung',
                'cucu' => 'Cucu',
                'lainnya' => 'Lainnya',
                default => $hubunganPelapor
            };
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
        <h3 class="text-lg font-bold uppercase underline">Surat Keterangan Kematian</h3>
        <p class="mt-1 text-sm">Nomor: {{ $record->no_surat ?? '___________' }}</p>
    </div>

    {{-- ISI SURAT --}}
    <div class="mb-4 text-justify" style="font-size: 13px; line-height: 1.4;">
        <p class="mb-3">Yang bertanda tangan di bawah ini, Kepala Desa {{ $namaDesa }}, Kecamatan {{ $namaKecamatan }}, {{ $namaKabupaten }}, Provinsi Sulawesi Tenggara, dengan ini menerangkan bahwa:</p>

        <p class="mb-3"><strong>Data Almarhum/Almarhumah:</strong></p>

        <table class="mb-3 w-full">
            @if($namaAlmarhum)
            <tr>
                <td class="w-1/3 py-1">Nama Lengkap</td>
                <td class="w-8 py-1">:</td>
                <td class="py-1 font-semibold">{{ $namaAlmarhum }}</td>
            </tr>
            @endif
            @if($nikAlmarhum)
            <tr>
                <td class="py-1">NIK</td>
                <td class="py-1">:</td>
                <td class="py-1 font-semibold">{{ $nikAlmarhum }}</td>
            </tr>
            @endif
            @if($jenisKelamin)
            <tr>
                <td class="py-1">Jenis Kelamin</td>
                <td class="py-1">:</td>
                <td class="py-1">{{ $jenisKelaminLabel }}</td>
            </tr>
            @endif
            @if($tempatLahir || $tanggalLahir)
            <tr>
                <td class="py-1">Tempat, Tanggal Lahir</td>
                <td class="py-1">:</td>
                <td class="py-1">
                    {{ $tempatLahir ?? '' }}{{ $tempatLahir && $tanggalLahir ? ', ' : '' }}{{ $tanggalLahir ? \Carbon\Carbon::parse($tanggalLahir)->format('d F Y') : '' }}
                </td>
            </tr>
            @endif
            @if($umur)
            <tr>
                <td class="py-1">Umur</td>
                <td class="py-1">:</td>
                <td class="py-1">{{ $umur }} tahun</td>
            </tr>
            @endif
            @if($agama)
            <tr>
                <td class="py-1">Agama</td>
                <td class="py-1">:</td>
                <td class="py-1">{{ $agama }}</td>
            </tr>
            @endif
            @if($pekerjaan)
            <tr>
                <td class="py-1">Pekerjaan</td>
                <td class="py-1">:</td>
                <td class="py-1">{{ $pekerjaan }}</td>
            </tr>
            @endif
            @if($alamatAlmarhum)
            <tr>
                <td class="py-1" style="vertical-align: top;">Alamat</td>
                <td class="py-1" style="vertical-align: top;">:</td>
                <td class="py-1" style="vertical-align: top;">{{ $alamatAlmarhum }}</td>
            </tr>
            @endif
        </table>

        <p class="mb-3"><strong>Data Kematian:</strong></p>

        <table class="mb-3 w-full">
            @if($tanggalMeninggal)
            <tr>
                <td class="w-1/3 py-1">Tanggal Meninggal</td>
                <td class="w-8 py-1">:</td>
                <td class="py-1 font-semibold">{{ \Carbon\Carbon::parse($tanggalMeninggal)->format('d F Y') }}</td>
            </tr>
            @endif
            @if($waktuMeninggal)
            <tr>
                <td class="py-1">Waktu Meninggal</td>
                <td class="py-1">:</td>
                <td class="py-1">{{ $waktuMeninggal }} WITA</td>
            </tr>
            @endif
            @if($tempatMeninggal)
            <tr>
                <td class="py-1">Tempat Meninggal</td>
                <td class="py-1">:</td>
                <td class="py-1">{{ $tempatMeninggal }}</td>
            </tr>
            @endif
            @if($sebabKematian)
            <tr>
                <td class="py-1">Sebab Kematian</td>
                <td class="py-1">:</td>
                <td class="py-1">{{ $sebabKematian }}</td>
            </tr>
            @endif
            @if($tempatPemakaman)
            <tr>
                <td class="py-1">Tempat Pemakaman</td>
                <td class="py-1">:</td>
                <td class="py-1">{{ $tempatPemakaman }}</td>
            </tr>
            @endif
        </table>

        @if($namaPelapor)
        <p class="mb-3"><strong>Data Pelapor:</strong></p>

        <table class="mb-3 w-full">
            <tr>
                <td class="w-1/3 py-1">Nama Pelapor</td>
                <td class="w-8 py-1">:</td>
                <td class="py-1">{{ $namaPelapor }}</td>
            </tr>
            @if($nikPelapor)
            <tr>
                <td class="py-1">NIK Pelapor</td>
                <td class="py-1">:</td>
                <td class="py-1">{{ $nikPelapor }}</td>
            </tr>
            @endif
            @if($hubunganPelapor)
            <tr>
                <td class="py-1">Hubungan dengan Almarhum</td>
                <td class="py-1">:</td>
                <td class="py-1">{{ $hubunganLabel }}</td>
            </tr>
            @endif
        </table>
        @endif

        @if($record->keperluan)
        <p class="mb-3">Surat keterangan kematian ini dibuat untuk keperluan: <strong>{{ $record->keperluan }}</strong></p>
        @endif

        @if($record->keterangan)
        <p class="mb-4"><strong>Keterangan:</strong> {{ $record->keterangan }}</p>
        @endif

        <p class="mb-4">Demikian surat keterangan kematian ini dibuat dengan sebenarnya berdasarkan keterangan pelapor untuk dapat dipergunakan sebagaimana mestinya.</p>
    </div>

    {{-- TTD --}}
    <div class="mt-8" style="display: grid; grid-template-columns: 1fr auto 1fr; grid-template-rows: repeat(3, auto); gap: 0 4rem;">
        {{-- Kolom Kiri: Pelapor --}}
        <div class="text-center" style="grid-column: 1; grid-row: 1;">
            <p style="margin-bottom: 4px;">Pelapor,</p>
        </div>

        <div class="flex items-center justify-center" style="grid-column: 1; grid-row: 2; height: 100px; margin-bottom: 4px;">
            @if(isset($record->data_tambahan['ttd_pemohon']))
                <img src="{{ $record->data_tambahan['ttd_pemohon'] }}" alt="TTD Pelapor" style="max-height: 100px; max-width: 220px; object-fit: contain;">
            @elseif(isset($record->data_tambahan['foto_ttd_pemohon']))
                <img src="{{ asset('storage/' . $record->data_tambahan['foto_ttd_pemohon']) }}" alt="Foto TTD Pelapor" style="max-height: 100px; max-width: 220px; object-fit: contain;">
            @endif
        </div>

        <div class="text-center" style="grid-column: 1; grid-row: 3;">
            <p class="font-semibold underline" style="margin-bottom: 2px;">{{ $namaPelapor ?? '' }}</p>
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

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Hasil K-Means Sesi {{ $sesi->id }} - GoatMonitor</title>

    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            background-color: #fff;
            margin: 0;
            padding: 40px;
            font-size: 11px;
            line-height: 1.5;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #111;
        }

        .header p {
            margin: 5px 0 0;
            font-size: 10px;
            color: #666;
            font-weight: 500;
        }

        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 25px;
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #eee;
        }

        .info-card {
            display: table-cell;
            width: 25%;
            text-align: center;
            vertical-align: middle;
        }

        .info-title {
            font-size: 8px;
            text-transform: uppercase;
            color: #888;
            font-weight: bold;
            display: block;
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 11px;
            font-weight: bold;
            color: #222;
        }

        .section-title {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: bold;
            margin: 25px 0 10px;
            color: #111;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th {
            background-color: #f5f5f5;
            color: #444;
            font-weight: bold;
            text-align: left;
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
            font-size: 9px;
            text-transform: uppercase;
        }

        td {
            padding: 8px 10px;
            border-bottom: 1px solid #eee;
            color: #555;
        }

        tr:nth-child(even) td {
            background-color: #fafafa;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .badge {
            display: inline-block;
            padding: 3px 7px;
            border-radius: 12px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-rendah {
            background-color: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
        }

        .badge-sedang {
            background-color: #fef3c7;
            color: #b45309;
            border: 1px solid #fcd34d;
        }

        .badge-tinggi {
            background-color: #d1fae5;
            color: #047857;
            border: 1px solid #6ee7b7;
        }

        .footer {
            margin-top: 50px;
            text-align: right;
            font-size: 9px;
            color: #777;
        }

        .signature {
            margin-top: 30px;
            display: inline-block;
            text-align: center;
            width: 200px;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none;
            }

            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>

<body>

    <!-- Floating Print Control (hidden during print) -->
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 16px; background-color: #FF8400; color: white; border: none; border-radius: 6px; font-weight: bold; font-size: 11px; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            Cetak Laporan
        </button>
    </div>

    <!-- REPORT CONTENT -->
    <div class="header">
        <h1>Laporan Hasil Analisis K-Means Clustering</h1>
        <p>Sistem Monitoring Produktivitas Kambing - GoatMonitor</p>
    </div>

    <!-- Metadata Grid -->
    <div class="info-grid">
        <div class="info-card">
            <span class="info-title">ID Sesi</span>
            <span class="info-value">#{{ $sesi->id }}</span>
        </div>
        <div class="info-card">
            <span class="info-title">Diproses Oleh</span>
            <span class="info-value">{{ $sesi->user->name }}</span>
        </div>
        <div class="info-card">
            <span class="info-title">Tanggal Proses</span>
            <span class="info-value">{{ $sesi->created_at->translatedFormat('d F Y') }}</span>
        </div>
        <div class="info-card">
            <span class="info-title">Total Data</span>
            <span class="info-value">{{ $sesi->total_data }} ekor</span>
        </div>
    </div>

    <!-- Centroid Table -->
    <div class="section-title">Profil & Nilai Tengah Kluster Akhir</div>
    <table>
        <thead>
            <tr>
                <th>Kluster</th>
                <th class="text-center">Rata-rata Bobot (kg)</th>
                <th class="text-center">Maks Tingkat Kelahiran (ekor)</th>
                <th class="text-center">Rata-rata Produksi Susu (Liter)</th>
                <th class="text-center">Jumlah Anggota</th>
            </tr>
        </thead>
        <tbody>
            @php
            $centroid = $sesi->centroid_akhir;
            $counts = [
            'Rendah' => $hasils->where('cluster', 'Rendah')->count(),
            'Sedang' => $hasils->where('cluster', 'Sedang')->count(),
            'Tinggi' => $hasils->where('cluster', 'Tinggi')->count(),
            ];
            @endphp
            <tr>
                <td><strong>Cluster 1 - Produktivitas Rendah</strong></td>
                <td class="text-center">{{ number_format($centroid['Rendah']['C1'], 2) }} kg</td>
                <td class="text-center">{{ number_format($centroid['Rendah']['C2'], 0) }} ekor</td>
                <td class="text-center">{{ number_format($centroid['Rendah']['C3'], 2) }} L</td>
                <td class="text-center font-bold">{{ $counts['Rendah'] }} kambing</td>
            </tr>
            <tr>
                <td><strong>Cluster 2 - Produktivitas Sedang</strong></td>
                <td class="text-center">{{ number_format($centroid['Sedang']['C1'], 2) }} kg</td>
                <td class="text-center">{{ number_format($centroid['Sedang']['C2'], 0) }} ekor</td>
                <td class="text-center">{{ number_format($centroid['Sedang']['C3'], 2) }} L</td>
                <td class="text-center font-bold">{{ $counts['Sedang'] }} kambing</td>
            </tr>
            <tr>
                <td><strong>Cluster 3 - Produktivitas Tinggi</strong></td>
                <td class="text-center">{{ number_format($centroid['Tinggi']['C1'], 2) }} kg</td>
                <td class="text-center">{{ number_format($centroid['Tinggi']['C2'], 0) }} ekor</td>
                <td class="text-center">{{ number_format($centroid['Tinggi']['C3'], 2) }} L</td>
                <td class="text-center font-bold">{{ $counts['Tinggi'] }} kambing</td>
            </tr>
        </tbody>
    </table>

    <!-- Data Details Table -->
    <div class="section-title" style="margin-top: 30px;">Rincian Hasil Pengelompokan Kambing</div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 15%">Kode Kambing</th>
                <th style="width: 15%">Jenis Kelamin</th>
                <th class="text-center" style="width: 15%">Avg Bobot</th>
                <th class="text-center" style="width: 15%">Max Lahir</th>
                <th class="text-center" style="width: 15%">Avg Susu</th>
                <th class="text-center" style="width: 20%">Kluster Akhir</th>
            </tr>
        </thead>
        <tbody {!! count($hasils) <=3 ? 'style="page-break-inside: avoid;"' : '' !!}>
            @foreach($hasils as $idx => $h)
            @if(count($hasils) > 3 && $loop->remaining == 2)
        </tbody>
        <tbody style="page-break-inside: avoid;">
            @endif
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td><strong>{{ $h->kambing->kode_kambing }}</strong></td>
                <td>{{ $h->kambing->jenis_kelamin }}</td>
                <td class="text-center">{{ $h->bobot_badan_val }} kg</td>
                <td class="text-center">
                    @if($h->kambing->jenis_kelamin == 'Jantan')
                    -
                    @else
                    {{ $h->tingkat_kelahiran_val }} ekor
                    @endif
                </td>
                <td class="text-center">
                    @if($h->kambing->jenis_kelamin == 'Jantan')
                    -
                    @else
                    {{ $h->produksi_susu_val }} L
                    @endif
                </td>
                <td class="text-center">
                    @if($h->cluster == 'Rendah')
                    <span class="badge badge-rendah">Rendah</span>
                    @elseif($h->cluster == 'Sedang')
                    <span class="badge badge-sedang">Sedang</span>
                    @else
                    <span class="badge badge-tinggi">Tinggi</span>
                    @endif
                </td>
            </tr>
            @endforeach

            <!-- Signature Row attached to the table -->
            <tr>
                <td colspan="7" style="border: none; padding-top: 40px; padding-bottom: 0;">
                    <div class="footer" style="margin-top: 0; padding-top: 0;">
                        <p>Laporan dicetak pada {{ now()->timezone('Asia/Jakarta')->translatedFormat('d F Y, H:i') }} WIB</p>
                        <div class="signature">
                            <p>Petugas Administrasi,</p>
                            <div style="height: 50px;"></div>
                            <p><strong>{{ Auth::user()->name }}</strong></p>
                        </div>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>
</body>

</html>
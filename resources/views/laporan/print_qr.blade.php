<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Label QR Code</title>
    
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
        }
        
        .label-container {
            display: inline-block;
            width: 280px;
            height: 320px;
            border: 2px solid #333;
            margin: 10px;
            padding: 15px;
            background: white;
            page-break-inside: avoid; /* Agar tidak terpotong saat print */
            vertical-align: top;
            text-align: center;
        }
        
        .qr-code {
            width: 200px;
            height: 200px;
            margin: 0 auto 10px;
        }
        
        .label-info {
            font-size: 12px;
            line-height: 1.4;
            text-align: left;
            border-top: 1px dashed #ccc;
            padding-top: 8px;
        }
        
        .label-info p {
            margin-bottom: 4px;
        }
        
        .label-info strong {
            display: inline-block;
            width: 100px;
        }
        
        /* Style khusus print */
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .label-container {
                border: 1px solid #000;
                margin: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="no-print text-center mb-4">
        <h3>Preview Label QR Code</h3>
        <p>Total Barang Terpilih: <strong>{{ count($barangs) }}</strong></p>
        <button onclick="window.print()" class="btn btn-primary btn-lg">
            <i class="fas fa-print"></i> Cetak Sekarang
        </button>
        <a href="{{ route('barang.index') }}" class="btn btn-secondary btn-lg ms-2">Kembali</a>
    </div>

    <div class="text-center">
        @foreach($barangs as $barang)
        <div class="label-container">
            <!-- QR Code SVG Inline -->
            <div class="qr-code">
                @php
                    $isiQr = "No Inv: {$barang->kode_aset}\n" .
                             "Nama: {$barang->merek} {$barang->jenis}\n" .
                             "Lokasi: " . ($barang->lokasi->nama_ruangan ?? '-') . "\n" .
                             "Tgl. Perolehan: " . \Carbon\Carbon::parse($barang->tanggal_perolehan)->format('d-m-Y');
                @endphp
                {!! QrCode::size(180)->generate($isiQr) !!}
            </div>
            
            <!-- Informasi Barang -->
            <div class="label-info">
                <p><strong>No Inv:</strong> {{ $barang->kode_aset }}</p>
                <p><strong>Nama:</strong> {{ Str::limit($barang->merek . ' ' . $barang->jenis, 25) }}</p>
                <p><strong>Lokasi:</strong> {{ Str::limit($barang->lokasi->nama_ruangan ?? '-', 20) }}</p>
                <p><strong>Tgl. Perolehan:</strong> {{ \Carbon\Carbon::parse($barang->tanggal_perolehan)->format('d-m-Y') }}</p>
            </div>
        </div>
        @endforeach
    </div>
</body>
</html>
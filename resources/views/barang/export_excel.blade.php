<?xml version="1.0" encoding="UTF-8"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <Worksheet ss:Name="Laporan Inventaris">
  <Table>
   <Row>
    <Cell><Data ss:Type="String">LAPORAN BARANG SD Muhammadiyah Metro Pusat</Data></Cell>
   </Row>
   <Row>
    <Cell><Data ss:Type="String">Ruang: {{ $selectedLokasi->nama_ruangan ?? 'Semua Ruangan' }}</Data></Cell>
   </Row>
   <Row>
    <Cell><Data ss:Type="String">Dicetak pada: {{ date('d-m-Y H:i:s') }}</Data></Cell>
   </Row>
   <Row>
   </Row>
   <Row>
    <Cell><Data ss:Type="String">NO</Data></Cell>
    <Cell><Data ss:Type="String">KODE LOKASI</Data></Cell>
    <Cell><Data ss:Type="String">KODE ASET</Data></Cell>
    <Cell><Data ss:Type="String">KATEGORI</Data></Cell>
    <Cell><Data ss:Type="String">KELOMPOK</Data></Cell>
    <Cell><Data ss:Type="String">JENIS</Data></Cell>
    <Cell><Data ss:Type="String">NAMA</Data></Cell>
    <Cell><Data ss:Type="String">KONDISI</Data></Cell>
    <Cell><Data ss:Type="String">PEROLEHAN</Data></Cell>
    <Cell><Data ss:Type="String">HARGA</Data></Cell>
    <Cell><Data ss:Type="String">TANGGAL</Data></Cell>
    <Cell><Data ss:Type="String">KET</Data></Cell>
   </Row>
   @foreach($barangs as $index => $barang)
   <Row>
    <Cell><Data ss:Type="Number">{{ $index + 1 }}</Data></Cell>
    <Cell><Data ss:Type="String">{{ $barang->lokasi->kode_ruangan ?? '-' }}</Data></Cell>
    <Cell><Data ss:Type="String">{{ $barang->kode_aset }}</Data></Cell>
    <Cell><Data ss:Type="String">{{ $barang->masterKodeAset->kategori ?? $barang->kategori ?? '-' }}</Data></Cell>
    <Cell><Data ss:Type="String">{{ $barang->masterKodeAset->kelompok ?? '-' }}</Data></Cell>
    <Cell><Data ss:Type="String">{{ $barang->masterKodeAset->jenis ?? '-' }}</Data></Cell>
    <Cell><Data ss:Type="String">{{ $barang->nama_barang }}</Data></Cell>
    <Cell><Data ss:Type="String">{{ $barang->kondisi_terkini }}</Data></Cell>
    <Cell><Data ss:Type="String">{{ $barang->sumber_perolehan ?? 'Beli' }}</Data></Cell>
    <Cell><Data ss:Type="String">Rp {{ number_format($barang->harga_perolehan, 0, ',', '.') }}</Data></Cell>
    <Cell><Data ss:Type="String">{{ $barang->tanggal_perolehan ? \Carbon\Carbon::parse($barang->tanggal_perolehan)->format('d-m-Y') : '-' }}</Data></Cell>
    <Cell><Data ss:Type="String">{{ $barang->sumber_dana ?? '-' }}</Data></Cell>
   </Row>
   @endforeach
  </Table>
 </Worksheet>
</Workbook>
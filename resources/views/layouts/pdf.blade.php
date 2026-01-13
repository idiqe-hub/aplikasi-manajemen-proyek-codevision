<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>{{ $docTitle ?? 'Laporan' }}</title>
  <style>
    @page { size: A4; margin: 2.2cm; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 11pt; color: #000; }

    .kop-table { width: 100%; border-collapse: collapse; }
    .kop-table td { vertical-align: middle; }
    .kop-logo { width: 80px; }
    .kop-title { text-align: center; }
    .kop-title h2 { margin: 0; font-size: 14pt; }
    .kop-title h3 { margin: 3px 0 0; font-size: 12pt; }
    .kop-title p  { margin: 3px 0 0; font-size: 10pt; }

    .line-1 { border-top: 2px solid #000; margin-top: 8px; }
    .line-2 { border-top: 1px solid #000; margin-top: 2px; }

    .meta { margin: 14px 0 10px; font-size: 10.5pt; }
    .meta table { width: 100%; border-collapse: collapse; }
    .meta td { padding: 2px 0; }
    .meta .label { width: 150px; }

    .doc-title { text-align: center; margin: 10px 0 6px; }
    .doc-title h1 { margin: 0; font-size: 13pt; text-transform: uppercase; }
    .doc-subtitle { text-align: center; margin: 0 0 12px; font-size: 10.5pt; }

    table.report { width: 100%; border-collapse: collapse; margin-top: 8px; }
    table.report th, table.report td { border: 1px solid #000; padding: 6px; }
    table.report th { background: #efefef; text-align: center; }

    .text-center { text-align: center; }
    .text-right { text-align: right; }
  </style>
</head>
<body>

  {{-- KOP --}}
  <table class="kop-table">
    <tr>
      <td class="kop-logo">
        @if(!empty($logoPath) && file_exists($logoPath))
          <img src="{{ $logoPath }}" style="width:78px;height:auto;">
        @endif
      </td>
      <td class="kop-title">
        <h2>{{ $instansiName ?? 'CODEVISION.ID' }}</h2>
        <h3>{{ $instansiTagline ?? 'Software House & IT Solutions' }}</h3>
        <p>{{ $instansiAddress ?? '' }}</p>
      </td>
      <td style="width:80px;"></td>
    </tr>
  </table>
  <div class="line-1"></div>
  <div class="line-2"></div>

  {{-- JUDUL --}}
  <div class="doc-title">
    <h1>{{ $reportTitle ?? 'Laporan' }}</h1>
  </div>
  <div class="doc-subtitle">
    {{ $reportSubtitle ?? 'Sistem Manajemen Project & Task Developer' }}
  </div>

  {{-- META --}}
  <div class="meta">
    <table>
      <tr>
        <td class="label"><strong>Tanggal Cetak</strong></td>
        <td>: {{ $printedAt ?? now()->format('d-m-Y H:i') }}</td>
      </tr>
      @if(!empty($filters))
      <tr>
        <td class="label"><strong>Filter</strong></td>
        <td>: {{ $filters }}</td>
      </tr>
      @endif
      @if(!empty($keterangan))
      <tr>
        <td class="label"><strong>Keterangan</strong></td>
        <td>: {{ $keterangan }}</td>
      </tr>
      @endif
    </table>
  </div>
  
  @yield('content')
  <div style="
    margin-top: 60px;
    width: 100%;
    page-break-inside: avoid;
">
    <div style="
        width: 40%;
        float: right;
        text-align: center;
    ">
        Banjarmasin, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
        Mengetahui,<br>
        Pimpinan Perusahaan<br><br><br><br>

        <b>(_____________________)</b><br>
    </div>

    <div style="clear: both;"></div>
</div>



</body>
</html>

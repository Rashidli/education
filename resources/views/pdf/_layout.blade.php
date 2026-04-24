<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
        h1 { font-size: 16px; margin: 0 0 4px; }
        .meta { color: #666; font-size: 10px; margin-bottom: 16px; border-bottom: 1px solid #ddd; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { padding: 6px 8px; text-align: left; border-bottom: 1px solid #e5e5e5; }
        th { background: #f4f4f4; text-transform: uppercase; font-size: 9px; letter-spacing: 0.04em; }
        tfoot td { font-weight: 600; background: #fafafa; }
        .text-right { text-align: right; }
        .summary { display: table; width: 100%; margin-bottom: 12px; }
        .summary-box { display: table-cell; padding: 8px; border: 1px solid #e5e5e5; border-radius: 4px; }
        .summary-label { font-size: 9px; color: #666; text-transform: uppercase; }
        .summary-value { font-size: 14px; font-weight: 700; margin-top: 2px; }
    </style>
</head>
<body>
    <h1>@yield('title')</h1>
    <div class="meta">
        {{ $from->format('d.m.Y') }} — {{ $to->format('d.m.Y') }} ·
        hazırlandı: {{ now()->format('d.m.Y H:i') }}
    </div>

    @yield('content')
</body>
</html>

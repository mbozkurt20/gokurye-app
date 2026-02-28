<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Fatura #{{ $order->tracking_id }}</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Arial', sans-serif; background: #f8fafc; display: flex; justify-content: center; padding: 40px 20px; }

    .invoice-wrap { width: 720px; background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 40px rgba(0,0,0,0.08); }

    .invoice-header { background: #0f172a; color: white; padding: 40px 48px; display: flex; justify-content: space-between; align-items: flex-start; }
    .invoice-header .brand { font-size: 22px; font-weight: 900; letter-spacing: -0.5px; text-transform: uppercase; }
    .invoice-header .brand span { color: #6366f1; }
    .invoice-header .restaurant-info { font-size: 11px; color: #94a3b8; line-height: 1.8; margin-top: 6px; }
    .invoice-header .invoice-label { text-align: right; }
    .invoice-header .invoice-label .title { font-size: 28px; font-weight: 900; text-transform: uppercase; letter-spacing: 4px; opacity: 0.15; }
    .invoice-header .invoice-label .number { font-size: 14px; font-weight: 900; color: #6366f1; margin-top: 6px; }
    .invoice-header .invoice-label .date { font-size: 11px; color: #64748b; margin-top: 4px; }

    .invoice-body { padding: 40px 48px; }

    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 36px; }
    .info-box { background: #f8fafc; border-radius: 12px; padding: 20px; border: 1px solid #f1f5f9; }
    .info-box .label { font-size: 9px; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 8px; }
    .info-box .value { font-size: 13px; font-weight: 700; color: #1e293b; }
    .info-box .sub { font-size: 11px; color: #64748b; margin-top: 4px; }

    .items-table { width: 100%; border-collapse: collapse; margin-bottom: 28px; }
    .items-table thead tr { border-bottom: 2px solid #f1f5f9; }
    .items-table thead th { font-size: 9px; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 2px; padding: 10px 12px; text-align: left; }
    .items-table thead th.right { text-align: right; }
    .items-table tbody tr { border-bottom: 1px solid #f8fafc; }
    .items-table tbody tr:last-child { border-bottom: none; }
    .items-table tbody td { padding: 12px 12px; font-size: 12px; color: #334155; font-weight: 600; }
    .items-table tbody td.right { text-align: right; font-weight: 700; }
    .items-table tbody td .item-name { font-weight: 700; color: #1e293b; }
    .items-table tbody td .item-opts { font-size: 10px; color: #94a3b8; margin-top: 2px; }
    .items-table tbody td .badge { background: #f1f5f9; color: #64748b; font-size: 10px; font-weight: 900; padding: 2px 8px; border-radius: 6px; display: inline-block; }

    .totals { margin-left: auto; width: 280px; }
    .totals-row { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #f1f5f9; }
    .totals-row:last-child { border-bottom: none; }
    .totals-row .t-label { font-size: 11px; color: #64748b; font-weight: 700; }
    .totals-row .t-value { font-size: 12px; color: #1e293b; font-weight: 700; }
    .totals-total { background: #0f172a; border-radius: 14px; padding: 16px 20px; display: flex; justify-content: space-between; align-items: center; margin-top: 12px; }
    .totals-total .t-label { font-size: 11px; color: #94a3b8; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; }
    .totals-total .t-value { font-size: 18px; color: white; font-weight: 900; }

    .invoice-footer { background: #f8fafc; border-top: 1px solid #f1f5f9; padding: 24px 48px; display: flex; justify-content: space-between; align-items: center; margin-top: 40px; }
    .invoice-footer .payment-badge { display: flex; align-items: center; gap: 8px; background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 8px 16px; font-size: 11px; font-weight: 900; color: #334155; }
    .invoice-footer .status-badge { background: #dcfce7; color: #16a34a; font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; padding: 6px 14px; border-radius: 8px; }
    .invoice-footer .footer-note { font-size: 10px; color: #94a3b8; }

    .print-bar { position: fixed; top: 0; left: 0; right: 0; background: #0f172a; padding: 12px 24px; display: flex; justify-content: space-between; align-items: center; z-index: 9999; }
    .print-bar a { color: #64748b; text-decoration: none; font-size: 11px; font-weight: 700; }
    .print-bar button { background: #6366f1; color: white; border: none; border-radius: 8px; padding: 8px 20px; font-size: 11px; font-weight: 900; cursor: pointer; text-transform: uppercase; letter-spacing: 1px; }

    @media print {
        body { background: white; padding: 0; }
        .invoice-wrap { box-shadow: none; border-radius: 0; }
        .print-bar { display: none; }
    }
</style>
</head>
<body>

<div class="print-bar">
    <a href="{{ url()->previous() }}">← Geri Dön</a>
    <button onclick="window.print()">Yazdır / PDF Olarak Kaydet</button>
</div>

<div style="margin-top: 60px;">
<div class="invoice-wrap">

    {{-- Header --}}
    <div class="invoice-header">
        <div>
            <div class="brand">{{ $restaurant->restaurant_name ?? $restaurant->name }}<span>.</span></div>
            <div class="restaurant-info">
                @if($restaurant->phone) 📞 {{ $restaurant->phone }}<br>@endif
                @if($restaurant->address) 📍 {{ $restaurant->address }}<br>@endif
                @if($restaurant->tax_name) Vergi Dairesi: {{ $restaurant->tax_name }}<br>@endif
                @if($restaurant->tax_number) Vergi No: {{ $restaurant->tax_number }}@endif
            </div>
        </div>
        <div class="invoice-label">
            <div class="title">Fatura</div>
            <div class="number">#{{ $order->tracking_id }}</div>
            <div class="date">{{ \Carbon\Carbon::parse($order->created_at)->format('d.m.Y H:i') }}</div>
            <div class="date" style="margin-top: 2px; color: #6366f1; font-weight: 700; text-transform: uppercase; font-size:10px;">
                {{ ucfirst($order->platform) }}
            </div>
        </div>
    </div>

    {{-- Body --}}
    <div class="invoice-body">

        {{-- Info Grid --}}
        <div class="info-grid">
            <div class="info-box">
                <div class="label">Müşteri Bilgisi</div>
                <div class="value">{{ $order->full_name }}</div>
                <div class="sub">{{ $order->phone }}</div>
                @if($order->address)
                <div class="sub" style="margin-top:6px; font-size:10px; line-height:1.5">{{ $order->address }}</div>
                @endif
            </div>
            <div class="info-box" style="text-align:right;">
                <div class="label">Teslimat Bilgisi</div>
                @if($courier)
                <div class="value">{{ $courier->name }}</div>
                <div class="sub">Kurye</div>
                @else
                <div class="value">Restoran</div>
                <div class="sub">Öz Teslimat</div>
                @endif
                @if($order->assigned_at)
                <div class="sub" style="margin-top:6px; font-size:10px;">Atandı: {{ \Carbon\Carbon::parse($order->assigned_at)->format('H:i') }}</div>
                @endif
            </div>
        </div>

        {{-- Items --}}
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width:40%">Ürün Adı</th>
                    <th>Adet</th>
                    <th class="right">Birim Fiyat</th>
                    <th class="right">Toplam</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                @php
                    $name  = $item['name'] ?? ($item['productName'] ?? 'Ürün');
                    $qty   = (int)($item['quantity'] ?? ($item['qty'] ?? 1));
                    $price = (float)($item['price'] ?? 0);
                    $opts  = $item['options'] ?? ($item['selectedOptions'] ?? []);
                @endphp
                <tr>
                    <td>
                        <div class="item-name">{{ $name }}</div>
                        @if(!empty($opts))
                        <div class="item-opts">
                            @foreach((array)$opts as $opt)
                                {{ is_string($opt) ? $opt : ($opt['name'] ?? '') }}
                            @endforeach
                        </div>
                        @endif
                    </td>
                    <td><span class="badge">×{{ $qty }}</span></td>
                    <td class="right">₺{{ number_format($price, 2, ',', '.') }}</td>
                    <td class="right">₺{{ number_format($qty * $price, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Totals --}}
        <div class="totals">
            @if($order->sub_amount && $order->sub_amount != $order->amount)
            <div class="totals-row">
                <span class="t-label">Ara Toplam</span>
                <span class="t-value">₺{{ number_format($order->sub_amount, 2, ',', '.') }}</span>
            </div>
            @endif
            @if($order->discount && $order->discount > 0)
            <div class="totals-row">
                <span class="t-label" style="color:#ef4444">İndirim</span>
                <span class="t-value" style="color:#ef4444">−₺{{ number_format($order->discount, 2, ',', '.') }}</span>
            </div>
            @endif
            <div class="totals-total">
                <span class="t-label">Genel Toplam</span>
                <span class="t-value">₺{{ number_format($order->amount, 2, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="invoice-footer">
        <div>
            <div class="payment-badge">
                @if(stripos($order->payment_method, 'card') !== false || $order->payment_method === 'PAY_WITH_CARD')
                    💳 Kredi Kartı ile Ödendi
                @elseif(stripos($order->payment_method, 'ticket') !== false || stripos($order->payment_method, 'yemek') !== false)
                    🎫 Yemek Çeki ile Ödendi
                @else
                    💵 Nakit Ödeme
                @endif
            </div>
        </div>
        <div class="status-badge">✓ Teslim Edildi</div>
        <div class="footer-note">
            Bu belge bilgilendirme amaçlıdır.<br>
            Yasal fatura için muhasebecenizle iletişime geçin.
        </div>
    </div>

</div>
</div>

</body>
</html>

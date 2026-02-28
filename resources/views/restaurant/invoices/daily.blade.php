<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>Günlük Fatura — {{ \Carbon\Carbon::parse($date)->format('d.m.Y') }}</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: Arial, sans-serif; background: #f8fafc; padding: 40px 20px; }

    .page { width: 780px; margin: 0 auto; }

    .page-header { background: #0f172a; color: white; border-radius: 18px 18px 0 0; padding: 32px 40px; display: flex; justify-content: space-between; align-items: flex-start; }
    .brand { font-size: 20px; font-weight: 900; text-transform: uppercase; letter-spacing: -0.5px; }
    .brand span { color: #6366f1; }
    .rest-info { font-size: 10px; color: #64748b; line-height: 1.8; margin-top: 4px; }
    .report-title { text-align: right; }
    .report-title .rtitle { font-size: 18px; font-weight: 900; text-transform: uppercase; letter-spacing: 3px; color: #6366f1; }
    .report-title .rdate { font-size: 12px; color: #94a3b8; margin-top: 4px; font-weight: 700; }

    .summary-bar { background: white; border: 1px solid #f1f5f9; border-top: none; padding: 20px 40px; display: grid; grid-template-columns: repeat(4, 1fr); gap: 0; }
    .sum-item { padding: 0 20px; border-right: 1px solid #f1f5f9; }
    .sum-item:first-child { padding-left: 0; }
    .sum-item:last-child { border-right: none; }
    .sum-item .s-label { font-size: 9px; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 4px; }
    .sum-item .s-value { font-size: 18px; font-weight: 900; color: #1e293b; }
    .sum-item .s-value.green { color: #16a34a; }

    .orders-container { background: white; border: 1px solid #f1f5f9; border-top: none; padding: 24px 40px; border-radius: 0 0 18px 18px; }

    .order-block { border: 1px solid #f1f5f9; border-radius: 12px; margin-bottom: 16px; overflow: hidden; }
    .order-block:last-child { margin-bottom: 0; }
    .order-block-head { background: #f8fafc; padding: 12px 18px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; }
    .order-block-head .oh-left { display: flex; align-items: center; gap: 12px; }
    .order-block-head .tracking { font-size: 11px; font-weight: 900; color: #1e293b; }
    .order-block-head .platform { font-size: 9px; font-weight: 900; color: #6366f1; text-transform: uppercase; background: #ede9fe; padding: 2px 8px; border-radius: 6px; }
    .order-block-head .time { font-size: 10px; color: #94a3b8; font-weight: 700; }
    .order-block-head .amount { font-size: 14px; font-weight: 900; color: #1e293b; }

    .order-customer { padding: 10px 18px; border-bottom: 1px solid #f8fafc; display: flex; justify-content: space-between; }
    .order-customer .cname { font-size: 11px; font-weight: 700; color: #334155; }
    .order-customer .cphone { font-size: 10px; color: #94a3b8; }
    .order-customer .payment { font-size: 9px; font-weight: 900; background: #f1f5f9; color: #64748b; padding: 3px 8px; border-radius: 6px; text-transform: uppercase; }

    .order-items { padding: 10px 18px; }
    .order-item { display: flex; justify-content: space-between; align-items: center; padding: 4px 0; border-bottom: 1px solid #f8fafc; }
    .order-item:last-child { border-bottom: none; }
    .order-item .iname { font-size: 11px; color: #334155; font-weight: 600; }
    .order-item .iqty { font-size: 10px; font-weight: 900; color: #6366f1; background: #ede9fe; padding: 1px 6px; border-radius: 5px; }
    .order-item .iprice { font-size: 11px; font-weight: 700; color: #1e293b; }

    .grand-total { background: #0f172a; border-radius: 12px; padding: 20px 28px; display: flex; justify-content: space-between; align-items: center; margin-top: 24px; }
    .grand-total .gt-label { font-size: 12px; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 2px; }
    .grand-total .gt-value { font-size: 24px; font-weight: 900; color: white; }

    .print-bar { position: fixed; top: 0; left: 0; right: 0; background: #0f172a; padding: 12px 24px; display: flex; justify-content: space-between; align-items: center; z-index: 9999; }
    .print-bar a { color: #64748b; text-decoration: none; font-size: 11px; font-weight: 700; }
    .print-bar button { background: #6366f1; color: white; border: none; border-radius: 8px; padding: 8px 20px; font-size: 11px; font-weight: 900; cursor: pointer; text-transform: uppercase; letter-spacing: 1px; margin-left: 8px; }

    @media print {
        body { background: white; padding: 0; }
        .print-bar { display: none; }
        .page { width: 100%; }
        .order-block { break-inside: avoid; }
    }
</style>
</head>
<body>

<div class="print-bar">
    <a href="{{ route('restaurant.invoices') }}">← Fatura Listesi</a>
    <div>
        <button onclick="window.print()">🖨 Yazdır / PDF Kaydet</button>
    </div>
</div>

<div style="margin-top: 64px;">
<div class="page">

    {{-- Header --}}
    <div class="page-header">
        <div>
            <div class="brand">{{ $restaurant->restaurant_name ?? $restaurant->name }}<span>.</span></div>
            <div class="rest-info">
                @if($restaurant->phone) 📞 {{ $restaurant->phone }}<br>@endif
                @if($restaurant->address) 📍 {{ $restaurant->address }}<br>@endif
                @if($restaurant->tax_name) Vergi Dairesi: {{ $restaurant->tax_name }}<br>@endif
                @if($restaurant->tax_number) Vergi No: {{ $restaurant->tax_number }}@endif
            </div>
        </div>
        <div class="report-title">
            <div class="rtitle">Günlük Rapor</div>
            <div class="rdate">{{ \Carbon\Carbon::parse($date)->format('d F Y, l') }}</div>
        </div>
    </div>

    {{-- Summary Bar --}}
    <div class="summary-bar">
        <div class="sum-item">
            <div class="s-label">Toplam Sipariş</div>
            <div class="s-value">{{ $totalOrders }}</div>
        </div>
        <div class="sum-item">
            <div class="s-label">Toplam Ciro</div>
            <div class="s-value green">₺{{ number_format($totalAmount, 2, ',', '.') }}</div>
        </div>
        <div class="sum-item">
            <div class="s-label">Ortalama Sipariş</div>
            <div class="s-value">₺{{ $totalOrders > 0 ? number_format($totalAmount / $totalOrders, 2, ',', '.') : '0,00' }}</div>
        </div>
        <div class="sum-item">
            <div class="s-label">Oluşturulma</div>
            <div class="s-value" style="font-size: 13px; color: #64748b;">{{ now()->format('d.m.Y H:i') }}</div>
        </div>
    </div>

    {{-- Orders --}}
    <div class="orders-container">

        @forelse($orders as $order)
        <div class="order-block">
            <div class="order-block-head">
                <div class="oh-left">
                    <span class="tracking">#{{ $order->tracking_id }}</span>
                    <span class="platform">{{ $order->platform }}</span>
                    <span class="time">{{ \Carbon\Carbon::parse($order->created_at)->format('H:i') }}</span>
                </div>
                <span class="amount">₺{{ number_format($order->amount, 2, ',', '.') }}</span>
            </div>
            <div class="order-customer">
                <div>
                    <div class="cname">{{ $order->full_name }}</div>
                    <div class="cphone">{{ $order->phone }}</div>
                </div>
                <span class="payment">
                    @if(stripos($order->payment_method, 'card') !== false || $order->payment_method === 'PAY_WITH_CARD')
                        Kart
                    @elseif(stripos($order->payment_method, 'ticket') !== false)
                        Yemek Çeki
                    @else
                        Nakit
                    @endif
                </span>
            </div>
            @if(!empty($order->parsedItems))
            <div class="order-items">
                @foreach($order->parsedItems as $item)
                @php
                    $iname  = $item['name'] ?? ($item['productName'] ?? 'Ürün');
                    $iqty   = (int)($item['quantity'] ?? ($item['qty'] ?? 1));
                    $iprice = (float)($item['price'] ?? 0);
                @endphp
                <div class="order-item">
                    <span class="iname">{{ $iname }}</span>
                    <div style="display:flex; align-items:center; gap:12px;">
                        <span class="iqty">×{{ $iqty }}</span>
                        <span class="iprice">₺{{ number_format($iprice * $iqty, 2, ',', '.') }}</span>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
        @empty
        <p style="text-align:center; color: #94a3b8; font-size: 13px; padding: 40px 0;">Bu tarihte teslim edilmiş sipariş bulunmuyor.</p>
        @endforelse

        @if($totalOrders > 0)
        <div class="grand-total">
            <span class="gt-label">{{ $totalOrders }} Sipariş — Gün Sonu Toplam</span>
            <span class="gt-value">₺{{ number_format($totalAmount, 2, ',', '.') }}</span>
        </div>
        @endif
    </div>

</div>
</div>

</body>
</html>

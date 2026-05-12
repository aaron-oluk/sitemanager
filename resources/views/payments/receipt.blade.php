<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Receipt {{ $payment->receipt_number }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 13px; color: #111827; background: #fff; }
        .page { max-width: 720px; margin: 0 auto; padding: 48px 48px; }

        /* Top: brand left, title right */
        .top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 40px; }
        .brand-name { font-size: 17px; font-weight: 700; color: #111827; margin-bottom: 16px; }
        .brand-address { font-size: 12px; color: #6b7280; line-height: 1.7; }
        .doc-title { text-align: right; }
        .doc-title h1 { font-size: 32px; font-weight: 700; color: #111827; letter-spacing: 0.02em; }
        .doc-title .rct-num { font-size: 12px; color: #6b7280; margin-top: 4px; }

        /* Bill to + meta row */
        .bill-row { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 32px; }
        .bill-to-label { font-size: 12px; color: #6b7280; margin-bottom: 4px; }
        .bill-to-name { font-size: 14px; font-weight: 700; color: #111827; }
        .bill-to-sub { font-size: 12px; color: #6b7280; margin-top: 2px; }
        .meta-right { text-align: right; }
        .meta-line { display: flex; justify-content: flex-end; gap: 24px; margin-bottom: 5px; }
        .meta-key { font-size: 12px; color: #6b7280; }
        .meta-val { font-size: 12px; font-weight: 600; color: #111827; min-width: 110px; text-align: right; }

        /* Table */
        table { width: 100%; border-collapse: collapse; margin-bottom: 0; }
        thead tr { background: #1f2937; }
        th { padding: 10px 14px; font-size: 11px; font-weight: 700; color: #fff; text-transform: uppercase; letter-spacing: 0.06em; text-align: left; }
        th:first-child { width: 36px; }
        th:last-child { text-align: right; }
        td { padding: 14px 14px; font-size: 13px; color: #374151; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
        td:first-child { color: #9ca3af; font-size: 12px; padding-top: 16px; }
        td:last-child { text-align: right; font-weight: 600; color: #111827; white-space: nowrap; }
        .item-name { font-weight: 600; color: #111827; }
        .item-badge { display: inline-block; margin-top: 4px; padding: 2px 7px; border-radius: 4px; font-size: 10px; font-weight: 700; }
        .badge-hosting { background: #dbeafe; color: #1d4ed8; }
        .badge-domain  { background: #ede9fe; color: #6d28d9; }
        .badge-email   { background: #ffedd5; color: #c2410c; }

        /* Totals block (right-aligned, below table) */
        .totals { margin-top: 0; }
        .totals-row { display: flex; justify-content: flex-end; }
        .totals-inner { width: 260px; }
        .totals-line { display: flex; justify-content: space-between; padding: 7px 14px; font-size: 13px; color: #374151; border-bottom: 1px solid #f3f4f6; }
        .totals-line:last-child { border-bottom: none; }
        .totals-final { background: #1f2937; display: flex; justify-content: space-between; padding: 11px 14px; }
        .totals-final span:first-child { font-size: 13px; font-weight: 700; color: #fff; }
        .totals-final span:last-child  { font-size: 15px; font-weight: 700; color: #fff; }

        /* Notes */
        .notes { margin-top: 36px; }
        .notes-label { font-size: 12px; font-weight: 600; color: #6b7280; margin-bottom: 5px; }
        .notes-text  { font-size: 12px; color: #374151; line-height: 1.6; }

        /* Footer */
        .footer { margin-top: 48px; border-top: 1px solid #e5e7eb; padding-top: 14px; display: flex; justify-content: space-between; }
        .footer-text { font-size: 10px; color: #9ca3af; }
    </style>
</head>
<body>
<div class="page">

    {{-- Brand + Document title --}}
    <div class="top">
        <div>
            <div class="brand-name">SiteManager</div>
        </div>
        <div class="doc-title">
            <h1>RECEIPT</h1>
            <div class="rct-num"># {{ $payment->receipt_number ?? ('RCT-' . strtoupper($payment->id)) }}</div>
        </div>
    </div>

    {{-- Bill to + date/method --}}
    <div class="bill-row">
        <div>
            <div class="bill-to-label">Bill To</div>
            <div class="bill-to-name">{{ $payment->website->name ?? 'N/A' }}</div>
            @if($payment->website?->domain)
                <div class="bill-to-sub">{{ $payment->website->domain }}</div>
            @endif
        </div>
        <div class="meta-right">
            <div class="meta-line">
                <span class="meta-key">Date :</span>
                <span class="meta-val">{{ optional($payment->payment_date)->format('d M Y') }}</span>
            </div>
            <div class="meta-line">
                <span class="meta-key">Payment Method :</span>
                <span class="meta-val">{{ $payment->payment_method }}</span>
            </div>
            @if($payment->transaction_id)
            <div class="meta-line">
                <span class="meta-key">Ref# :</span>
                <span class="meta-val" style="font-family: monospace; font-size: 11px;">{{ $payment->transaction_id }}</span>
            </div>
            @endif
        </div>
    </div>

    {{-- Items table --}}
    @php $lineItems = $payment->lineItems; @endphp
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Item &amp; Description</th>
                <th style="text-align:right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @if($lineItems->count())
                @foreach($lineItems as $i => $item)
                @php
                    $sym = ['USD'=>'$','UGX'=>'USh ','EUR'=>'€','GBP'=>'£','KES'=>'KSh ','TZS'=>'TSh ','NGN'=>'₦'][$item->currency] ?? ($item->currency . ' ');
                    $dec = in_array($item->currency, ['UGX','TZS']) ? 0 : 2;
                    $badgeClass = $item->item_type === 'domain' ? 'badge-domain' : ($item->item_type === 'email' ? 'badge-email' : 'badge-hosting');
                @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        <div class="item-name">{{ $item->label }}</div>
                        <span class="item-badge {{ $badgeClass }}">{{ ucfirst($item->item_type) }}</span>
                    </td>
                    <td>{{ $sym }}{{ number_format($item->total_amount, $dec) }}</td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td>1</td>
                    <td><div class="item-name">{{ ucfirst($payment->payment_type ?? 'Website') }} payment</div></td>
                    <td>{{ $payment->formatted_amount }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- Totals --}}
    <div class="totals">
        <div class="totals-row">
            <div class="totals-inner">
                <div class="totals-final">
                    <span>Total Paid</span>
                    <span>{{ $payment->formatted_amount }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Notes --}}
    @if($payment->notes)
    <div class="notes">
        <div class="notes-label">Notes</div>
        <div class="notes-text">{{ $payment->notes }}</div>
    </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <div class="footer-text">Generated by SiteManager &bull; {{ now()->format('M d, Y') }}</div>
        <div class="footer-text">{{ $payment->receipt_number }}</div>
    </div>

</div>
</body>
</html>

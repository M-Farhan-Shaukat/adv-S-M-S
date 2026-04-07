<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; overflow: hidden; }
        .header { background: linear-gradient(135deg, #1e3a5f, #2d6a9f); color: white; padding: 30px; text-align: center; }
        .body { padding: 30px; }
        .table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .table th { background: #f8f9fa; padding: 10px; text-align: left; border-bottom: 2px solid #dee2e6; }
        .table td { padding: 10px; border-bottom: 1px solid #dee2e6; }
        .total-row { font-weight: bold; background: #f8f9fa; }
        .badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; }
        .badge-danger { background: #fee2e2; color: #dc2626; }
        .badge-success { background: #dcfce7; color: #16a34a; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; color: #6c757d; font-size: 12px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2 style="margin:0">{{ $voucher->student?->school?->name ?? 'School' }}</h2>
        <p style="margin:5px 0 0">Fee Voucher - {{ date('F Y', mktime(0,0,0,$voucher->month,1,$voucher->year)) }}</p>
    </div>
    <div class="body">
        <p>Dear <strong>{{ $voucher->student?->name }}</strong>,</p>
        <p>Your fee voucher for <strong>{{ date('F Y', mktime(0,0,0,$voucher->month,1,$voucher->year)) }}</strong> has been generated.</p>

        <table class="table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align:right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($voucher->items as $item)
                <tr>
                    <td>{{ $item->title }}</td>
                    <td style="text-align:right">Rs. {{ number_format($item->total_amount, 0) }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td>Total Amount</td>
                    <td style="text-align:right">Rs. {{ number_format($voucher->total_amount, 0) }}</td>
                </tr>
                <tr>
                    <td>Paid Amount</td>
                    <td style="text-align:right; color: green;">Rs. {{ number_format($voucher->paid_amount, 0) }}</td>
                </tr>
                <tr class="total-row">
                    <td>Remaining</td>
                    <td style="text-align:right; color: red;">Rs. {{ number_format($voucher->total_amount - $voucher->paid_amount, 0) }}</td>
                </tr>
            </tbody>
        </table>

        <p><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($voucher->due_date)->format('d F Y') }}</p>
        <p>
            <strong>Status:</strong>
            <span class="badge {{ $voucher->status === 'paid' ? 'badge-success' : 'badge-danger' }}">
                {{ ucfirst($voucher->status) }}
            </span>
        </p>

        <p style="color: #6c757d; font-size: 13px;">Please pay before the due date to avoid any late fees. You can pay online through the parent portal or visit the school office.</p>
    </div>
    <div class="footer">
        <p>This is an automated email. Please do not reply.</p>
        <p>{{ $voucher->student?->school?->name ?? 'School Management System' }}</p>
    </div>
</div>
</body>
</html>

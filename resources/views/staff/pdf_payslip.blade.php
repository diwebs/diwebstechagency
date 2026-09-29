<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payslip - {{ $staff->staff_id }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
        .header {
            border-bottom: 2px solid #0097A7;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #0097A7;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
            text-align: right;
            float: right;
            margin-top: -30px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 5px 0;
            vertical-align: top;
        }
        .info-table td.label {
            color: #777;
            width: 120px;
            font-weight: bold;
        }
        .info-table td.value {
            color: #111;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .details-table th {
            background-color: #f5f5f5;
            border-bottom: 1px solid #ddd;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        .details-table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .details-table td.amount {
            text-align: right;
        }
        .summary-box {
            float: right;
            width: 250px;
            margin-top: 10px;
        }
        .summary-row {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
            overflow: hidden;
        }
        .summary-row.total {
            border-bottom: 2px solid #0097A7;
            font-size: 14px;
            font-weight: bold;
            color: #0097A7;
        }
        .summary-label {
            float: left;
            color: #555;
        }
        .summary-value {
            float: right;
            font-weight: bold;
        }
        .clear {
            clear: both;
        }
        .footer {
            margin-top: 80px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
            text-align: center;
            color: #888;
            font-size: 10px;
        }
        .signatures {
            margin-top: 50px;
            width: 100%;
        }
        .signature-box {
            width: 45%;
            float: left;
            text-align: center;
        }
        .signature-box.right {
            float: right;
        }
        .signature-line {
            border-top: 1px solid #999;
            margin-top: 40px;
            padding-top: 5px;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="company-name">DIWEBS TECH AGENCY</div>
        <div class="title">OFFICIAL PAYSLIP</div>
        <div style="font-size: 10px; color: #777; margin-top: 5px;">Enterprise Software & Digital Infrastructure Solutions</div>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Staff ID:</td>
            <td class="value">{{ $staff->staff_id }}</td>
            <td class="label">Pay Period:</td>
            <td class="value" style="font-weight: bold;">{{ date('F Y', strtotime($payroll->month . '-01')) }}</td>
        </tr>
        <tr>
            <td class="label">Employee Name:</td>
            <td class="value">{{ $staff->name }}</td>
            <td class="label">Department:</td>
            <td class="value">{{ $staff->department->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Job Position:</td>
            <td class="value">{{ $staff->role->title ?? 'N/A' }}</td>
            <td class="label">Employment Type:</td>
            <td class="value">{{ $staff->employment_type }}</td>
        </tr>
        <tr>
            <td class="label">Salary Grade:</td>
            <td class="value">{{ $staff->salary_grade ?: 'N/A' }}</td>
            <td class="label">Payment Method:</td>
            <td class="value">Direct Bank Wire</td>
        </tr>
    </table>

    <table class="details-table">
        <thead>
            <tr>
                <th>Description</th>
                <th style="text-align: right;">Earnings (₦)</th>
                <th style="text-align: right;">Deductions (₦)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Basic Salary</td>
                <td class="amount">₦{{ number_format($payroll->base_salary, 2) }}</td>
                <td class="amount">-</td>
            </tr>
            @if($payroll->bonuses > 0)
                <tr>
                    <td>Performance Allowance / Bonuses</td>
                    <td class="amount" style="color: green;">₦{{ number_format($payroll->bonuses, 2) }}</td>
                    <td class="amount">-</td>
                </tr>
            @endif
            @if($payroll->tax > 0)
                <tr>
                    <td>Estimated Income Tax (PAYE)</td>
                    <td class="amount">-</td>
                    <td class="amount" style="color: red;">₦{{ number_format($payroll->tax, 2) }}</td>
                </tr>
            @endif
            @if($payroll->deductions > 0)
                <tr>
                    <td>Operational Deductions</td>
                    <td class="amount">-</td>
                    <td class="amount" style="color: red;">₦{{ number_format($payroll->deductions, 2) }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="clear"></div>

    <div class="summary-box">
        <div class="summary-row">
            <div class="summary-label">Gross Earnings:</div>
            <div class="summary-value">₦{{ number_format($payroll->base_salary + $payroll->bonuses, 2) }}</div>
        </div>
        <div class="summary-row">
            <div class="summary-label">Total Deductions:</div>
            <div class="summary-value" style="color: red;">₦{{ number_format($payroll->deductions + $payroll->tax, 2) }}</div>
        </div>
        <div class="summary-row total">
            <div class="summary-label">Net Take Home Pay:</div>
            <div class="summary-value">₦{{ number_format($payroll->net_salary, 2) }}</div>
        </div>
    </div>

    <div class="clear"></div>

    <div class="signatures">
        <div class="signature-box">
            <div class="signature-line">
                Employee Signature<br>
                Date: ________________________
            </div>
        </div>
        <div class="signature-box right">
            <div class="signature-line">
                Authorized Signatory (Diwebs HR)<br>
                Date: {{ $payroll->paid_at ? $payroll->paid_at->format('Y-m-d') : date('Y-m-d') }}
            </div>
        </div>
    </div>

    <div class="clear"></div>

    <div class="footer">
        This document is system-generated and electronically validated. For inquiries, contact payroll@diwebstechagency.website.<br>
        © {{ date('Y') }} Diwebs Tech Agency. All rights reserved.
    </div>

</body>
</html>

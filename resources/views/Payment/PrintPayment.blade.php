<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Receipt</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 20mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            /* color: #000; */


        }

        .receipt-box {
            max-width: 100%;
            margin: auto;
            padding: 10px;
        }

        .page-header {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 0;
        }

        .page-subtext {
            text-align: center;
            font-size: 12px;
            margin: 0;
        }

        .title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 10px 0;
            border-top: 1px solid #ccc;
            border-bottom: 1px solid #ccc;
            padding: 5px 0;
        }

        .subtitle {
            font-size: 18px;
            font-weight: bold;
            margin: 10px 0;
            border-bottom: 1px solid #ccc;
            padding: 5px 0;
        }

        .section-title {
            font-weight: bold;
            margin: 10px 0 5px;
            border-bottom: 1px solid #ccc;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table td {
            padding: 4px;
            vertical-align: top;
        }

        .bordered td {
            border: 1px solid #ddd;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .signature {
            margin-top: 40px;
            text-align: center;
        }

        .signature-line {
            margin-top: 40px;
            border-top: 1px solid #000;
            width: 200px;
            margin-left: auto;
            margin-right: auto;
            padding-top: 4px;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .justify-between {
            display: flex;
            justify-content: space-between;
            align-items: center; /* optional, aligns items vertically */
        }


    </style>
</head>
<body >
    <img src="{{ asset('images/logo/smis-logo.png') }}"
     style="position: fixed;
            top: 20%; left:40%;
            width: 450px; height: 450px;
            opacity: 0.05;
            transform: translate(-50%, -50%);
            z-index: -1;"
     alt="Watermark">

      {{-- Header --}}
        <div class="page-header">Central Philippine State University</div>
        <div class="page-subtext">#6 Intersection Magapit, Lal-lo, Cagayan</div>
        <div class="page-subtext">0921-348-9722 / 0997-552-2319</div>
        <div class="page-subtext">yvonne_yves@yahoo.com</div>

    <div class="receipt-box">
        <table style="width: 100%; padding-bottom: 10px">
            <tr>
                <td class="subtitle" style="width: 50%; text-align: left;">Payment Receipt</td>
                <td class="subtitle" style="width: 50%; text-align: right;">#{{ $payment->reference_number }}</td>
            </tr>
        </table>


        {{-- Student Info --}}

        <table>
            <tr>
                <td><strong>Name:</strong> {{ $payment->enrollment->student->last_name }}, {{ $payment->enrollment->student->first_name }} {{ $payment->enrollment->student->middle_name }} {{ $payment->enrollment->student->extension_name }}</td>
                <td class="text-right"><strong>Student ID:</strong> {{ $payment->enrollment->student->student_id_number }}</td>
            </tr>
            <tr>
                <td><strong>Email:</strong> {{ $payment->enrollment->student->email }}</td>
                <td class="text-right"><strong>Phone:</strong> {{ $payment->enrollment->student->phone }}</td>
            </tr>
        </table>

        {{-- Enrollment Info --}}

        <table>
            <tr>
                <td><strong>Paid to Ref#:</strong> {{ $payment->enrollment->reference_number }}</td>
                <td class="text-right"><strong>School Year:</strong> {{ $payment->enrollment->school_year_from }} - {{ $payment->enrollment->school_year_to }}</td>
            </tr>

            <tr>
                <td><strong>Current Balance:</strong> - </td>

            </tr>
        </table>

        {{-- Payment Info --}}

        <table class="bordered" style="padding-top: 10px">

            <tr>
                <td><strong>Status</strong></td>
                <td>{{ ucfirst($payment->status) }}</td>
            </tr>
            <tr>
                <td><strong>Payment Method</strong></td>
                <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
            </tr>

            <tr>
                <td><strong>Payment Date</strong></td>
                <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('F d, Y h:i A') }}</td>
            </tr>

            @if ($payment->payment_method === 'cash')
                <tr>
                    <td><strong>Paid Amount</strong></td>
                    <td>{{ number_format($payment->pay_amount, 2) }}</td>
                </tr>
            @elseif ($payment->payment_method === 'gcash')
                <tr>
                    <td><strong>GCash Ref #</strong></td>
                    <td>{{ $payment->gcash_reference_number }}</td>
                </tr>
                <tr>
                    <td><strong>GCash Amount</strong></td>
                    <td>{{ number_format($payment->gcash_pay_amount, 2) }}</td>
                </tr>
            @elseif ($payment->payment_method === 'bank_transfer')
                <tr>
                    <td><strong>Bank Name</strong></td>
                    <td>{{ $payment->bank_name }}</td>
                </tr>
                <tr>
                    <td><strong>Account #</strong></td>
                    <td>{{ $payment->bank_account_number }}</td>
                </tr>
                <tr>
                    <td><strong>Bank Ref #</strong></td>
                    <td>{{ $payment->bank_reference_number }}</td>
                </tr>
                <tr>
                    <td><strong>Bank Amount</strong></td>
                    <td>{{ number_format($payment->bank_pay_amount, 2) }}</td>
                </tr>
            @elseif ($payment->payment_method === 'other')
                <tr>
                    <td><strong>Reference #</strong></td>
                    <td>{{ $payment->other_reference_number }}</td>
                </tr>
                <tr>
                    <td><strong>Notes</strong></td>
                    <td>{{ $payment->other_notes }}</td>
                </tr>
                <tr>
                    <td><strong>Amount</strong></td>
                    <td>{{ number_format($payment->other_pay_amount, 2) }}</td>
                </tr>
            @endif

                <tr>
                    <td><strong>Ending Balance</strong></td>
                    <td> - </td>
                </tr>
        </table>

        {{-- Signature --}}
        <div class="signature">
            <div class="signature-line">Authorized Signature</div>
        </div>

        <div class="footer">
            This is a system-generated receipt. No signature is required.
        </div>
    </div>
</body>
</html>

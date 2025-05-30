<?php

namespace App\Http\Controllers;

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use App\Models\StudentPayment;

class PrintPaymentController extends Controller
{
    public function __invoke(Request $request, $payment_id)
    {

        // Example: check if the user is authenticated and has a 'paid' status
        $user = $request->user();
        $payment = StudentPayment::select(
            'enrollment_id',
            'id',
            'amount',
            'status',
            'payment_method',
            'pay_amount',
            'cash_tendered',
            'reference_number',
            'payment_date',
            'notes',
            'gcash_reference_number',
            'bank_reference_number',
            'created_by',
            'bank_name',
            'bank_account_number',
            'other_reference_number',
            'other_notes',
            'gcash_pay_amount',
            'bank_pay_amount',
            'other_pay_amount'
        )
        ->with([
            'enrollment',
            'enrollment.student',

        ])
        ->findOrFail($payment_id);


        if (!$user || $user->id !== 1) {
            return abort(403, 'Unauthorized action.');
        }




        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);


        // Create a new instance of Dompdf
        $dompdf = new Dompdf($options);

        // Load HTML content into Dompdf
        $dompdf->loadHtml(view('Payment.PrintPayment', compact('payment'))->render());

        // Set paper size (optional)
        $dompdf->setPaper('letter', 'landscape');

        // Render the HTML as PD
        $dompdf->render();

        // Output the generated PDF (you can save it to a file if needed)


        $dompdf->stream("payment_{$payment->reference_number}.pdf", ["Attachment" => false]);
        exit;
        // return response($dompdf->output(), 200)
        //     ->header('Content-Type', 'application/pdf')
        //     ->header('Content-Disposition', 'inline; filename="payment_'.$payment->id.'.pdf"');



        //return view('Payment.PrintPayment', compact('payment'));

    }
}

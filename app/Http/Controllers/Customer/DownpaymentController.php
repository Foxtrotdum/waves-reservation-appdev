<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\DownPayment;
use App\Models\Balance;
use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\ReservedAmenity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DownpaymentController extends Controller
{
    public function showReceipt($reservationId)
    {
        $reservation = Reservation::with('bill')
            ->where('id', $reservationId)
            ->where('customer_id', auth()->id()) 
            ->firstOrFail();

        if (!$reservation->bill) {
            return back()->withErrors([
                'bill' => 'No billing information found.'
            ]);
        }

        return view('customer.downpayment', [
            'reservation' => $reservation,
            'bill' => $reservation->bill
        ]);
    }
    public function billing($reservationId)
    {
        $customer = auth()->user();
        // Eager load bill (singular), not bills (plural)
        $reservation = Reservation::with('reservedAmenities.amenity', 'bill')
            ->where('id', $reservationId)
            ->where('customer_id', auth()->id()) 
            ->firstOrFail();

        return view('customer.payment', [
            'customer' => auth()->user(),
            'reservation' => $reservation,
            'bill' => $reservation->bill
        ]);

        // Access the bill directly from the relationship
        $bill = $reservation->bill;

        if (!$bill) {
            return back()->withErrors(['bill' => 'No billing information found. Please contact support.']);
        }

        return view('customer.payment', compact('customer', 'reservation', 'bill'));
    }

public function storePayment(Request $request, $reservationId)
    {
        $request->validate([
            'ref_number' => 'required|string|max:20|regex:/^[A-Za-z0-9]+$/',
            'payment_proof' => 'required|file|mimes:jpeg,png,jpg|max:2048'
        ]);


        $reservation = Reservation::with([
                'bill',
                'reservedAmenities.amenity',
                'downpayment'
            ])
            ->where('id', $reservationId)
            ->where('customer_id', auth()->id())
            ->firstOrFail();


        if (!$reservation->bill) {

            return back()->withErrors([
                'bill' => 'Billing not found.'
            ]);

        }


        try {

            DB::beginTransaction();


            $imagePath = $request->file('payment_proof')
                ->store('proofs', 'private');



            $downpayment = DownPayment::create([

                'id' => Str::uuid(),

                'res_num' => $reservation->id,

                'bill_id' => $reservation->bill->id,

                'amount' => null,

                'ref_number' => $request->ref_number,

                'img_proof' => $imagePath,

                'date' => now(),

                'status' => 'pending',

                'verified_by' => null,

            ]);



            $existingBalance = Balance::where('bill_id', $reservation->bill->id)->first();

            if ($existingBalance) {
                $existingBalance->update([
                    'dp_id' => $downpayment->id,
                ]);
        }



            DB::commit();


            return redirect()
                ->route('customer.reservation')
                ->with('success', 'Downpayment submitted successfully!');



        } catch (\Exception $e) {

    DB::rollBack();

        // Log full error for developer
    Log::error('Downpayment Error: ' . $e->getMessage(), [
        'file' => $e->getFile(),
        'line' => $e->getLine(),
    ]);

    // Show detailed error only in local environment
    if (config('app.env') === 'local') {
        return back()->withErrors([
            'error' => 'Payment failed: ' . $e->getMessage()
        ]);
    }

    // Production safe message
    return back()->withErrors([
        'error' => 'Payment failed. Please try again or contact support.'
    ]);

        }

    }

}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\InactiveCustomers;
use App\Models\DownPayment;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Rules\UniqueEmailAcrossTables;

class ManagerProfileController extends Controller
{

    public function view_all_reservations()
    {
        return view('admin.manager.reservations.all_reservations');
    }

    public function view_profile()
    {
        $admin = Auth::guard('admin')->user();
        return view('admin.manager.profile.profile', compact('admin'));
    }

    public function view_vendors_list()
    {
        $vendors = Admin::where('role', 'Vendor')->get();
        $admin = Auth::guard('admin')->user();

        return view('admin.manager.profile.vendors_list', compact('vendors', 'admin'));
    }

    public function update_profile(Request $request, $id)
    {
        // Get the currently authenticated admin (modify as needed)
        $admin = Admin::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'number' => 'required|regex:/^[0-9]{11}$/',
            'email' => ['required', 'string', 'email', 'max:255', new UniqueEmailAcrossTables($admin->id)],
        ]);

        $admin->update($validated);

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
    public function update_vendors_list(Request $request, $id)
    {
        $vendor = Admin::find($request->id);
        if ($vendor) {
            $vendor->role = $request->role;
            $vendor->save();

            return response()->json(['success' => true, 'message' => 'Role updated successfully.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Vendor not found.']);
        }
    }


    public function view_del_req()
    {
        $pendingRequests = InactiveCustomers::where('status', 'pending')
            ->with('customer')
            ->get();
        return view('admin.manager.profile.del_req', compact('pendingRequests'));
    }

    public function approveRequest($id)
    {
        $request = InactiveCustomers::findOrFail($id);
        $request->status = 'approved';
        $request->archived_by = auth()->id();
        $request->save();
    
        // Assuming there is a customer_id foreign key in the InactiveCustomers model
        if ($request->customer_id) {
            $customer = Customer::find($request->customer_id);
            if ($customer) {
                $customer->is_active = 0;
                $customer->save();
            }
        }

        return redirect()->back()->with('success', 'Request approved successfully.');
    }

    public function declineRequest($id)
    {
        $request = InactiveCustomers::findOrFail($id);
        $request->status = 'rejected';
        $request->archived_by = auth()->id();
        $request->save();

        return redirect()->back()->with('success', 'Request declined successfully.');
    }

    public function view_del_acc_details($id)
    {
        $customer = Customer::findOrFail($id);

        // Fetch all reservations for this customer
        $allReservations = $customer->reservations()
            ->with(['reservedAmenities.amenity', 'bill.balance', 'downPayment'])
            ->get();

        $allReservations->each(function ($reservation) {
            $grandTotal = optional($reservation->bill)->grand_total ?? 0;
            $paidAmount = DownPayment::where('res_num', $reservation->id)
                ->where('status', 'verified')
                ->sum('amount');

            $reservation->paidAmount = $paidAmount;
            $reservation->grandTotal = $grandTotal;
            $reservation->balance = $grandTotal - $paidAmount;
        });

        // Group reservations
        $cancelledReservations = $allReservations->where('status', 'cancelled');
        $invalidReservations = $allReservations->where('status', 'invalid');
        $pendingReservations = $allReservations->where('status', 'pending');
        $verifiedReservations = $allReservations->where('status', 'verified');
        $completedReservations = $allReservations->where('status', 'completed');

        $redReservations = $cancelledReservations->merge($invalidReservations);
        $paidReservations = $verifiedReservations->filter(function ($reservation) {
            return in_array(optional($reservation->bill)->status, ['paid', 'partially paid']);
        });

        return view('admin.manager.profile.acc_details', compact(
            'customer',
            'pendingReservations',
            'cancelledReservations',
            'invalidReservations',
            'verifiedReservations',
            'completedReservations',
            'redReservations',
            'paidReservations',
            'allReservations'
        ));
    }
}

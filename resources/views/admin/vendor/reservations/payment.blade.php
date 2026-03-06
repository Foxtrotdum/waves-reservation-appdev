<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="{{ asset('css/ven_pay.css') }}">
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  <title>Dashboard</title>
</head>

<body>

  <!-- NAVIGATION BAR SECTION -->
  <nav class="navbar">
    @php
    // $user = \App\Models\Admin::find(Auth::id());
    $user = Auth::guard('admin')->user();
    $calendar_route = $user && $user->role === 'Manager' ? route('admin.reservation.list') : route('admin.vendor.reservation_calendar');
    $amenities_route = $user && $user->role === 'Manager' ? route('admin.manager.amenities', ['type' => 'cottage']) : route('admin.vendor.amenities', ['type' => 'cottage']);
    @endphp

    <div class="left-side-nav">
      <a href="{{ route('admin.dashboard') }}">
        <button class="dashboard" id="dashboard">
          <i class="material-icons nav-icons">dashboard</i> Dashboard
        </button>
      </a>
      <a href="{{ $amenities_route }}">
        <button class="ameneties" id="ameneties">
          <i class="material-icons nav-icons">holiday_village</i> Amenities
        </button>
      </a>
      <a href="{{ $calendar_route }}">
        <button class="reservations" id="reservation">
          <i class="material-icons nav-icons">date_range</i> Reservations
        </button>
      </a>
    </div>

    <div class="right-side-nav">
      @if($user->role === 'Manager')
      <a href="{{ route('admin.manager.profile') }}">
        <button class="profile">
          <i class="material-icons" style="font-size:45px; color: white">account_circle</i>
        </button>
      </a>
      @elseif($user->role === 'Vendor')
      <a href="{{ route('admin.vendor.profile') }}">
        <button class="profile">
          <i class="material-icons" style="font-size:45px; color: white">account_circle</i>
        </button>
      </a>
      @endif
    </div>
  </nav>

  @extends('admin.vendor.reservation')

  @section('styles')
  <link rel="stylesheet" href="{{ asset('css/ven_pay.css') }}">
  @endsection

  @section('reservation-content')           
    <div class="profile-content">
      <div class="balance-container">
        <div class="balance-card">
          <div class="balance-header">
            <h2>Payment Details</h2>
            @php
                  $hours = $reservation->hours ?? 0;
                  $total = $reservation->reservedAmenities->sum(function ($reserved) use ($hours) {
                      $price = optional($reserved->amenity)->price ?? 0;
                      return $price * $hours;
                  });
                  $downpayment = $total * 0.5;
            @endphp

            @if ($errors->any())
              <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc pl-5 space-y-1">
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif
          </div>
          
          <div class="balance-content">
            <div class="reservation-details">
              <div class="detail-group">
                <span class="detail-label">Reserved By:</span>
                <span class="detail-value">{{ $reservation->customer->name }}</span>
              </div>
              
              <div class="detail-group">
                <span class="detail-label">Reservation Date:</span>
                <span class="detail-value">{{ \Carbon\Carbon::parse($reservation->date)->format('m-d-Y') }}</span>
              </div>
              
              <div class="detail-group">
                <span class="detail-label">Time:</span>
                <span class="detail-value">{{ \Carbon\Carbon::parse($reservation->startTime)->format('g:i A') }} - {{ \Carbon\Carbon::parse($reservation->endTime)->format('g:i A') }}</span>
              </div>
              
              <div class="detail-group">
                <span class="detail-label">Reservation ID:</span>
                <span class="detail-value">{{ $reservation->id }}</span>
              </div>
            </div>

                  <div class="payment-summary">
                     <div class="w-full bg-gray-50 border border-gray-200 border-dashed rounded-lg p-4">
                         <h6 class="text-base text-center font-semibold text-gray-700 mb-2">Payment Summary</h6>
                         <ul class="text-base text-gray-800 space-y-1 mb-2">
                            @foreach ($reservation->reservedAmenities as $reserved)
                              @php
                                $hours = $reservation->hours ?? 0;
                                $price    = optional($reserved->amenity)->price ?? 0;
                                $name     = optional($reserved->amenity)->name  ?? 'Amenity';
                                $itemTotal = $price * $hours;
                              @endphp
                              <li class="flex justify-between text-xs">
                                <span>{{ $reserved->amenity->name }} (₱{{ number_format($reserved->amenity->price, 2) }} x {{ $hours }} hrs)</span>
                                <span class="font-bold">₱{{ number_format($itemTotal, 2) }}</span>
                              </li>
                            @endforeach
                         </ul>
                     
                         <hr class="my-2 border-gray-300">
                     
                         <div class="flex justify-between text-base text-gray-700">
                             <strong class="">Total:</strong>
                             <span class="font-bold">₱{{ number_format($total, 2) }}</span>
                         </div>
                     
                         <div class="flex justify-between text-base text-gray-700">
                             <strong class="">Down Payment (50%):</strong>
                             <span class=" font-bold">₱{{ number_format($downpayment, 2) }}</span>
                         </div>
                     </div>
                 </div>
                  
                  <div class="payment-methods">
                     <form class="payment-instructions" action="{{ route('admin.vendor.process-walkin')}}" method="POST" enctype="multipart/form-data" onsubmit="return validateSelection()">
                        @csrf
                           <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                            <input type="hidden" name="bill_id" value="{{ optional($reservation->bill)->id }}">
                            <input type="hidden" name="status" value="verified" hidden>

                <label for="payment_amount" class="upload-instruction my-3">Enter Payment Amount:</label>
                <input type="number" name="payment_amount" id="payment_amount" class="w-full border border-gray-300 rounded px-3 py-2" required>
                <div id="error-message" class="text-red-600 text-sm mb-2 hidden"></div>
                
                <div class="button-container mt-4">
                  <button class="pay-button" type="submit" id="pay-button">
                    <i class="fas fa-check"></i> Pay Now
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  @endsection

  @section('scripts')
  <script>
    function validateSelection() {
      const paymentAmount = document.getElementById('payment_amount').value;
      const button = document.getElementById("pay-button");
      const errorMessageContainer = document.getElementById("error-message");
      const total = {{ $total }};
      const downpayment = total * 0.5;
      let errorMessages = [];

      if (parseFloat(paymentAmount) < downpayment) {
        errorMessages.push('Payment amount must be at least 50% of the total.');
      }

      // Disable the button to prevent multiple submissions
      if (button) {
        button.disabled = true;
        button.textContent = "Processing..."; // Optional
      }
      
      if (errorMessages.length > 0) {
        errorMessageContainer.style.display = "block";
        errorMessageContainer.innerHTML = errorMessages.join("<br>");
        return false;
      }

      errorMessageContainer.style.display = "none";
      return true;
    }
  </script>
  @endsection
</body>
</html>
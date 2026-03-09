<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vendor Reservation Record</title>
  @yield('styles')
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet"> <!-- For Calendar CSS -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
    rel="stylesheet">
    <!-- Include these in your layout (e.g., in app.blade.php head and scripts section) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script> <!-- For Calendar JS -->
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script> <!-- For handling Alert -->
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">
  <nav class="navbar">
    @php
    $user = Auth::guard('admin')->user();
    @endphp
    
    <div class="left-side-nav">
      <a href="{{ route('admin.dashboard') }}">
        <button class="dashboard inactive" id="dashboard">
          <i class="material-icons nav-icons">dashboard</i> Dashboard
        </button>
      </a>
      <a href="{{ route('admin.vendor.amenities', ['type' => 'cottage']) }}">
        <button class="ameneties" id="ameneties">
          <i class="material-icons nav-icons">holiday_village</i> Amenities
        </button>
      </a>
      <a href="{{ route('admin.vendor.reservation_calendar') }}">
        <button class="reservations active" id="reservation">
          <i class="material-icons nav-icons">date_range</i> Reservations
        </button>
      </a>
    </div>

    <div class="right-side-nav">
      <a href="{{ route('admin.vendor.profile') }}" style="display: flex; align-items: center; text-decoration: none;">
          <span class="text-white font-semibold" style="margin-right: 8px; font-size: 20px; color: white">
              {{ $user->name ?? 'Unknown User' }}
            </span>  
          <i class="material-icons" style="font-size:45px; color: white; margin-right: 50px;">account_circle</i>
          </a>
    </div>
  </nav>
  <main class="main">
    @yield('reservation-content')
  </main>

  @yield('scripts')
</body>

</html>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="{{ asset('css/cottages.css') }}">
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
    rel="stylesheet">
  <title>Cottages Management</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
  <!-- NAVIGATION BAR SECTION -->
  <nav class="navbar">
    @php
    $user = Auth::guard('admin')->user();

    $calendar_route = $user && $user->role === 'Manager'
      ? route('admin.reservation.list')
      : route('admin.vendor.reservation_calendar');

    $amenities_route = $user && $user->role === 'Manager'
      ? route('admin.manager.amenities', ['type' => 'cottage'])
      : route('admin.vendor.amenities', ['type' => 'cottage']);

    $audit_route = route('admin.audit.logs');
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
      <a href="{{ $audit_route }}">
        <button class="audit-logs" id="audit">
          <i class="material-icons nav-icons">history</i> Audit Logs
        </button>
      </a>
      <a href="{{ $calendar_route }}">
        <button class="reservations" id="reservation">
          <i class="material-icons nav-icons">date_range</i> Reservations
        </button>
      </a>
    </div>
    <div class="right-side-nav">
      <a href="{{ route('admin.manager.profile') }}" style="display: flex; align-items: center; text-decoration: none;">
          <span class="text-white font-semibold" style="margin-right: 8px; font-size: 18px; color: white">
              {{ $user->name ?? 'Unknown User' }}
            </span>  
          <i class="material-icons" style="font-size:45px; color: white; margin-right: 50px;">account_circle</i>
            
          </a>
    </div>
  </nav>

  <!-- SIDEBAR SECTION -->
  <div class="container">
    <aside class="sidebar">
      <div class="icon-container">
        <a href="{{ route('admin.manager.amenities', ['type' => 'cottage']) }}">
          <i class="material-icons side-icons">storefront</i>
        </a>
        <p class="icon-label">Cottages</p>
      </div>
      <div class="icon-container">
        <a href="{{ route('admin.manager.amenities', ['type' => 'table']) }}">
          <i class="material-icons side-icons">dining</i>
        </a>
        <p class="icon-label">Tables</p>
      </div>
    </aside>

    <!-- MAIN SECTION -->
    <main class="main">
      <p class="label">{{ ucfirst($type) }}s</p>
      @if(session('success'))
      <div class="success-message" id="successMessage">
      <span>{{ session('success') }}</span>
      <button class="close-btn" onclick="dismissMessage()">×</button>
      </div>
    @endif
      <!-- Combined Form and Button Container -->
      <div class="form-button-container">
        <!-- Add Cottage Form -->
        <form action="{{ route('amenities.store') }}" method="POST" class="inline-form">
          @csrf
          <input type="text" name="name" placeholder="Amenity Name" required class="form-input">
          <input type="number" name="price" placeholder="Price per hour" required step="0.01" class="form-input">

          <select name="type" required class="form-input">
            <option value="" disabled selected>Select</option>
            <option value="cottage">Cottage</option>
            <option value="table">Table</option>
          </select>

          <button type="submit" class="add-amenity-btn">Add Amenity</button>

          <!-- Toggle Archived Button - now placed right after the submit button -->
          <button type="button" id="toggleButton" onclick="toggleArchived()" class="toggle-archive-btn">
            Show Archived {{ ucfirst($type) }}
          </button>

        </form>
      </div>

      <!--Amenity Table -->
      @yield('amenities-content')
    </main>
  </div>
  <!-- SCRIPT SECTION -->
  @yield('scripts')

  <script>
    function dismissMessage() {
      const message = document.getElementById('successMessage');
      if (message) {
        message.style.transition = 'opacity 0.3s ease';
        message.style.opacity = '0';
        setTimeout(() => {
          message.style.display = 'none';
        }, 300);
      }
    }
  </script>
</body>

</html>
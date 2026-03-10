<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Logs - Manager Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/admin_dashboard.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        .audit-container {
            padding: 20px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .audit-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .audit-title {
            font-size: 28px;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }

        .filters-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .filters-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            align-items: end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-label {
            font-weight: 500;
            margin-bottom: 5px;
            color: #555;
        }

        .filter-input, .filter-select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .filter-button {
            padding: 8px 20px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
        }

        .filter-button:hover {
            background: #2980b9;
        }

        .audit-table {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .audit-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .audit-table th,
        .audit-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .audit-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
        }

        .audit-table tr:hover {
            background: #f8f9fa;
        }

        .operation-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
        }

        .operation-create { background: #d4edda; color: #155724; }
        .operation-update { background: #fff3cd; color: #856404; }
        .operation-delete { background: #f8d7da; color: #721c24; }
        .operation-restore { background: #d1ecf1; color: #0c5460; }

        .user-info {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #3498db;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 14px;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 30px;
            gap: 8px;
        }

        .pagination-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: #3498db;
            font-size: 14px;
            font-weight: 500;
            min-width: 80px;
            height: 40px;
            transition: all 0.2s ease;
        }

        .pagination-button:hover:not(.disabled) {
            background: #f8f9fa;
            border-color: #3498db;
        }

        .pagination-button.disabled {
            color: #6c757d;
            cursor: not-allowed;
            background: #e9ecef;
            border-color: #dee2e6;
        }

        .view-details {
            color: #3498db;
            text-decoration: none;
            font-weight: 500;
        }

        .view-details:hover {
            text-decoration: underline;
        }

        .no-logs {
            text-align: center;
            padding: 40px;
            color: #666;
        }
    </style>
</head>
<body>
    <!-- NAVIGATION BAR SECTION -->
    <nav class="navbar">
        @php
        $user = Auth::guard('admin')->user();
        $calendar_route = route('admin.reservation.list');
        $amenities_route = route('admin.manager.amenities', ['type' => 'cottage']);
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
                <button class="audit-logs active" id="audit">
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
                <span class="text-white font-semibold" style="margin-right: 8px; font-size: 20px; color: white; font-family: sans-serif; font-weight: 550">
                    {{ $user->name ?? 'Unknown User' }}
                </span>
                <i class="material-icons" style="font-size:45px; color: white; margin-right: 50px;">account_circle</i>
            </a>
        </div>
    </nav>

    <div class="audit-container">
        <div class="audit-header">
            <h1 class="audit-title">Database Audit Logs</h1>
        </div>

        <!-- Filters Section -->
        <div class="filters-section">
            <form method="GET" class="filters-form">
                <div class="filter-group">
                    <label class="filter-label">Table</label>
                    <select name="table" class="filter-select">
                        <option value="">All Tables</option>
                        @foreach($tables as $table)
                            <option value="{{ $table }}" {{ request('table') == $table ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $table)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label">Operation</label>
                    <select name="operation" class="filter-select">
                        <option value="">All Operations</option>
                        @foreach($operations as $operation)
                            <option value="{{ $operation }}" {{ request('operation') == $operation ? 'selected' : '' }}>
                                {{ ucfirst($operation) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label">User Type</label>
                    <select name="user_type" class="filter-select">
                        <option value="">All Users</option>
                        @foreach($userTypes as $type)
                            <option value="{{ $type }}" {{ request('user_type') == $type ? 'selected' : '' }}>
                                {{ ucfirst($type) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="filter-input">
                </div>

                <div class="filter-group">
                    <label class="filter-label">Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="filter-input">
                </div>

                <div class="filter-group">
                    <label class="filter-label">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="User name, email, or record ID" class="filter-input">
                </div>

                <div class="filter-group">
                    <button type="submit" class="filter-button">Filter</button>
                </div>
            </form>
        </div>

        <!-- Audit Logs Table -->
        <div class="audit-table">
            @if($auditLogs->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>User</th>
                            <th>Table</th>
                            <th>Operation</th>
                            <th>Record ID</th>
                            <th>IP Address</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($auditLogs as $log)
                            <tr>
                                <td>{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            {{ strtoupper(substr($log->user ? $log->user->name : 'System', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div>{{ $log->user ? $log->user->name : 'System' }}</div>
                                            <small style="color: #666;">{{ ucfirst($log->user_type ?? 'system') }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ ucfirst(str_replace('_', ' ', $log->table_name)) }}</td>
                                <td>
                                    <span class="operation-badge operation-{{ $log->operation }}">
                                        {{ ucfirst(str_replace('_', ' ', $log->operation)) }}
                                    </span>
                                </td>
                                <td>{{ $log->record_id }}</td>
                                <td>{{ $log->ip_address ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('admin.audit.show', $log->id) }}" class="view-details">View Details</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="no-logs">
                    <h3>No audit logs found</h3>
                    <p>Try adjusting your filters or check back later for new activity.</p>
                </div>
            @endif
        </div>

        <!-- Pagination -->
        @if($auditLogs->hasPages())
            <div class="pagination">
                @if($auditLogs->onFirstPage())
                    <span class="pagination-button disabled">Previous</span>
                @else
                    <a href="{{ $auditLogs->previousPageUrl() }}" class="pagination-button">Previous</a>
                @endif

                @if($auditLogs->hasMorePages())
                    <a href="{{ $auditLogs->nextPageUrl() }}" class="pagination-button">Next</a>
                @else
                    <span class="pagination-button disabled">Next</span>
                @endif
            </div>
        @endif
    </div>

    <script>
        // Add active class to current nav item
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname;
            const navButtons = document.querySelectorAll('.navbar button');

            navButtons.forEach(button => {
                button.classList.remove('active');
            });

            if (currentPath.includes('audit')) {
                document.getElementById('audit').classList.add('active');
            }
        });
    </script>
</body>
</html>
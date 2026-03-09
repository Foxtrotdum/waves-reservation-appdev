<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Log Details - Manager Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/admin_dashboard.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        .audit-detail-container {
            padding: 20px;
            max-width: 1200px;
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

        .back-button {
            padding: 10px 20px;
            background: #6c757d;
            color: white;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .back-button:hover {
            background: #5a6268;
            color: white;
        }

        .audit-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .card-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            background: #f8f9fa;
        }

        .card-title {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
            color: #2c3e50;
        }

        .card-content {
            padding: 20px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
        }

        .info-label {
            font-weight: 600;
            color: #555;
            margin-bottom: 5px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            font-size: 16px;
            color: #2c3e50;
            word-break: break-word;
        }

        .operation-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-block;
        }

        .operation-create { background: #d4edda; color: #155724; }
        .operation-update { background: #fff3cd; color: #856404; }
        .operation-delete { background: #f8d7da; color: #721c24; }
        .operation-restore { background: #d1ecf1; color: #0c5460; }

        .changes-section {
            margin-top: 30px;
        }

        .changes-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #2c3e50;
        }

        .changes-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .changes-box {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 15px;
        }

        .changes-box h4 {
            margin: 0 0 10px 0;
            color: #2c3e50;
            font-size: 16px;
        }

        .change-item {
            margin-bottom: 8px;
            padding: 8px;
            background: white;
            border-radius: 4px;
            border-left: 3px solid #ddd;
        }

        .change-field {
            font-weight: 600;
            color: #555;
            font-size: 14px;
        }

        .change-old {
            color: #dc3545;
            font-size: 13px;
        }

        .change-new {
            color: #28a745;
            font-size: 13px;
        }

        .change-added {
            color: #28a745;
            font-weight: 600;
        }

        .change-removed {
            color: #dc3545;
            text-decoration: line-through;
        }

        .no-changes {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 20px;
        }

        .json-view {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 15px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            white-space: pre-wrap;
            word-break: break-word;
            max-height: 400px;
            overflow-y: auto;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #3498db;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 18px;
        }

        .user-details h3 {
            margin: 0 0 5px 0;
            color: #2c3e50;
        }

        .user-details p {
            margin: 0;
            color: #666;
        }

        @media (max-width: 768px) {
            .changes-container {
                grid-template-columns: 1fr;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }
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

    <div class="audit-detail-container">
        <div class="audit-header">
            <h1 class="audit-title">Audit Log Details</h1>
            <a href="{{ route('admin.audit.logs') }}" class="back-button">
                <i class="material-icons" style="font-size: 18px;">arrow_back</i>
                Back to Audit Logs
            </a>
        </div>

        <!-- Basic Information Card -->
        <div class="audit-card">
            <div class="card-header">
                <h2 class="card-title">Basic Information</h2>
            </div>
            <div class="card-content">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Operation</div>
                        <div class="info-value">
                            <span class="operation-badge operation-{{ $auditLog->operation }}">
                                {{ ucfirst(str_replace('_', ' ', $auditLog->operation)) }}
                            </span>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Table</div>
                        <div class="info-value">{{ ucfirst(str_replace('_', ' ', $auditLog->table_name)) }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Record ID</div>
                        <div class="info-value">{{ $auditLog->record_id }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Timestamp</div>
                        <div class="info-value">{{ $auditLog->created_at->format('M d, Y \a\t H:i:s') }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">IP Address</div>
                        <div class="info-value">{{ $auditLog->ip_address ?? 'N/A' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">User Agent</div>
                        <div class="info-value" style="font-size: 12px; font-family: monospace;">
                            {{ $auditLog->user_agent ? Str::limit($auditLog->user_agent, 50) : 'N/A' }}
                        </div>
                    </div>
                </div>

                <!-- User Information -->
                <div class="user-info">
                    <div class="user-avatar">
                        {{ strtoupper(substr($auditLog->user ? $auditLog->user->name : 'System', 0, 1)) }}
                    </div>
                    <div class="user-details">
                        <h3>{{ $auditLog->user ? $auditLog->user->name : 'System' }}</h3>
                        <p>{{ $auditLog->user ? $auditLog->user->email : 'Automated Process' }} • {{ ucfirst($auditLog->user_type ?? 'system') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Changes Section -->
        <div class="audit-card">
            <div class="card-header">
                <h2 class="card-title">Data Changes</h2>
            </div>
            <div class="card-content">
                @if($auditLog->operation === 'update')
                    @if($auditLog->old_values && $auditLog->new_values)
                        <div class="changes-container">
                            <div class="changes-box">
                                <h4>Before (Old Values)</h4>
                                <div class="json-view">{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT) }}</div>
                            </div>
                            <div class="changes-box">
                                <h4>After (New Values)</h4>
                                <div class="json-view">{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT) }}</div>
                            </div>
                        </div>

                        <!-- Field-by-field comparison -->
                        <div class="changes-section">
                            <h3 class="changes-title">Field Changes</h3>
                            @php
                                $changes = [];
                                if ($auditLog->old_values && $auditLog->new_values) {
                                    foreach ($auditLog->new_values as $field => $newValue) {
                                        $oldValue = $auditLog->old_values[$field] ?? null;
                                        if ($oldValue !== $newValue) {
                                            $changes[] = [
                                                'field' => $field,
                                                'old' => $oldValue,
                                                'new' => $newValue
                                            ];
                                        }
                                    }
                                }
                            @endphp

                            @if(count($changes) > 0)
                                @foreach($changes as $change)
                                    <div class="change-item">
                                        <div class="change-field">{{ ucfirst(str_replace('_', ' ', $change['field'])) }}</div>
                                        <div class="change-old">Old: {{ $change['old'] ?? 'null' }}</div>
                                        <div class="change-new">New: {{ $change['new'] ?? 'null' }}</div>
                                    </div>
                                @endforeach
                            @else
                                <div class="no-changes">No field changes detected</div>
                            @endif
                        </div>
                    @else
                        <div class="no-changes">No change data available</div>
                    @endif
                @elseif($auditLog->operation === 'create')
                    @if($auditLog->new_values)
                        <div class="changes-box">
                            <h4>Created Record Data</h4>
                            <div class="json-view">{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT) }}</div>
                        </div>
                    @else
                        <div class="no-changes">No creation data available</div>
                    @endif
                @elseif($auditLog->operation === 'delete')
                    @if($auditLog->old_values)
                        <div class="changes-box">
                            <h4>Deleted Record Data</h4>
                            <div class="json-view">{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT) }}</div>
                        </div>
                    @else
                        <div class="no-changes">No deletion data available</div>
                    @endif
                @else
                    <div class="no-changes">No change data available for this operation type</div>
                @endif
            </div>
        </div>
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
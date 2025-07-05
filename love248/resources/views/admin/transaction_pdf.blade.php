<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Transaction #{{ $transaction->id }} - PDF Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #fff;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }

        .header h1 {
            color: #333;
            margin: 0;
        }

        .header p {
            color: #666;
            margin: 5px 0;
        }

        .info-section {
            margin-bottom: 25px;
        }

        .info-section h3 {
            background-color: #f5f5f5;
            padding: 10px;
            margin: 0 0 15px 0;
            border-left: 4px solid #007bff;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .info-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #eee;
        }

        .info-table td:first-child {
            font-weight: bold;
            background-color: #f9f9f9;
            width: 30%;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-failed {
            background-color: #f8d7da;
            color: #721c24;
        }

        .amount {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }

        .metadata {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 12px;
            white-space: pre-wrap;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Transaction Report</h1>
        <p>Transaction ID: #{{ $transaction->id }}</p>
        <p>Generated on: {{ now()->format('jS F Y, g:i A') }}</p>
    </div>

    <div class="info-section">
        <h3>Transaction Details</h3>
        <table class="info-table">
            <tr>
                <td>Transaction ID</td>
                <td>#{{ $transaction->id }}</td>
            </tr>
            <tr>
                <td>Reference ID</td>
                <td>{{ $transaction->reference_id ?: 'N/A' }}</td>
            </tr>
            <tr>
                <td>Transaction Type</td>
                <td>{{ ucfirst(str_replace('_', ' ', $transaction->transaction_type)) }}</td>
            </tr>
            <tr>
                <td>Description</td>
                <td>{{ $transaction->description ?: 'No description' }}</td>
            </tr>
            <tr>
                <td>Amount</td>
                <td class="amount">{{ $transaction->currency }} {{ number_format($transaction->amount, 2) }}</td>
            </tr>
            <tr>
                <td>Payment Method</td>
                <td>{{ $transaction->payment_method ?: 'N/A' }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td>
                    <span class="status-badge status-{{ $transaction->status }}">
                        {{ ucfirst($transaction->status) }}
                    </span>
                </td>
            </tr>
            <tr>
                <td>Gateway Response</td>
                <td>{{ $transaction->gateway_response ?: 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <div class="info-section">
        <h3>User Information</h3>
        <table class="info-table">
            @if ($transaction->user)
                <tr>
                    <td>User Name</td>
                    <td>{{ $transaction->user->name }}</td>
                </tr>
                <tr>
                    <td>Username</td>
                    <td>{{ '@' . $transaction->user->username }}</td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td>{{ $transaction->user->email }}</td>
                </tr>
                <tr>
                    <td>User Type</td>
                    <td>{{ $transaction->user->is_streamer === 'yes' ? 'Streamer' : 'Regular User' }}</td>
                </tr>
            @else
                <tr>
                    <td>User Status</td>
                    <td>Deleted User</td>
                </tr>
            @endif
        </table>
    </div>

    <div class="info-section">
        <h3>Timestamps</h3>
        <table class="info-table">
            <tr>
                <td>Created Date</td>
                <td>{{ $transaction->created_at->format('jS F Y, g:i A') }}</td>
            </tr>
            <tr>
                <td>Updated Date</td>
                <td>{{ $transaction->updated_at->format('jS F Y, g:i A') }}</td>
            </tr>
            <tr>
                <td>Time Ago</td>
                <td>{{ $transaction->created_at->diffForHumans() }}</td>
            </tr>
        </table>
    </div>

    @if ($transaction->metadata)
        <div class="info-section">
            <h3>Additional Metadata</h3>
            <div class="metadata">{{ json_encode(json_decode($transaction->metadata), JSON_PRETTY_PRINT) }}</div>
        </div>
    @endif

    @if ($transaction->payment_data)
        <div class="info-section">
            <h3>Payment Gateway Data</h3>
            <div class="metadata">{{ json_encode(json_decode($transaction->payment_data), JSON_PRETTY_PRINT) }}</div>
        </div>
    @endif

    <div class="footer">
        <p>This report was generated automatically by the admin panel.</p>
        <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</body>

</html>

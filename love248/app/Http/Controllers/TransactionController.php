<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TransactionController extends Controller
{
    /**
     * Display a listing of the user's transactions
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $transactions = Transaction::where('user_id', Auth::id())
            ->when($request->filled('type'), function ($query) use ($request) {
                return $query->where('transaction_type', $request->type);
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                return $query->where('status', $request->status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $types = Transaction::where('user_id', Auth::id())
            ->distinct()
            ->pluck('transaction_type');

        $statuses = Transaction::where('user_id', Auth::id())
            ->distinct()
            ->pluck('status');

        return Inertia::render('Transactions/Index', [
            'transactions' => $transactions,
            'filters' => [
                'type' => $request->type,
                'status' => $request->status,
            ],
            'types' => $types,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Display the specified transaction details
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Inertia\Response
     */
    public function show(Transaction $transaction)
    {
        // Ensure user can only view their own transactions
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        return Inertia::render('Transactions/Show', [
            'transaction' => $transaction,
        ]);
    }

    /**
     * Get transaction types for filtering
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTransactionTypes()
    {
        $types = Transaction::where('user_id', Auth::id())
            ->distinct()
            ->pluck('transaction_type');

        return response()->json(['types' => $types]);
    }

    /**
     * Get transaction statuses for filtering
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTransactionStatuses()
    {
        $statuses = Transaction::where('user_id', Auth::id())
            ->distinct()
            ->pluck('status');

        return response()->json(['statuses' => $statuses]);
    }
}

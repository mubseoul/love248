<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SendMail;
use App\Models\User;
use App\Jobs\SendEmailJob;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EmailCampaignController extends Controller
{
    /**
     * Display a listing of email campaigns.
     */
    public function index(Request $request)
    {
        $campaigns = SendMail::latest()->paginate(15);
        return view('admin.email-campaigns.index', compact('campaigns'));
    }

    /**
     * Show the form for creating a new email campaign.
     */
    public function create()
    {
        $users = User::where('is_admin', 'no')
                    ->select('id', 'name', 'email', 'username')
                    ->orderBy('name')
                    ->get();
        
        return view('admin.email-campaigns.create', compact('users'));
    }

    /**
     * Store a newly created email campaign.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'recipient_type' => 'required|in:all,selected',
            'selected_users' => 'required_if:recipient_type,selected|array',
            'selected_users.*' => 'exists:users,email',
        ], [
            'subject.required' => 'Email subject is required.',
            'message.required' => 'Email message is required.',
            'recipient_type.required' => 'Please select recipient type.',
            'selected_users.required_if' => 'Please select at least one user when using selected recipients.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = Auth::user();
        
        // Determine recipients
        if ($request->recipient_type === 'all') {
            $recipients = User::where('is_admin', 'no')->pluck('email')->toArray();
        } else {
            $recipients = $request->selected_users;
        }

        // Send emails via queue
        foreach ($recipients as $email) {
            $mailData = [
                'title' => $request->subject,
                'body' => $request->message,
                'subject' => $request->subject,
            ];
            SendEmailJob::dispatch($email, $mailData);
        }

        // Save campaign record
        SendMail::create([
            'send_email' => $user->email,
            'receiver_email' => json_encode($recipients),
            'subject' => $request->subject,
            'message' => $request->message,
            'recipient_count' => count($recipients),
            'status' => 'sent',
        ]);

        return redirect()->route('admin.email-campaigns.index')
                        ->with('success', 'Email campaign sent successfully to ' . count($recipients) . ' recipients.');
    }

    /**
     * Display the specified email campaign.
     */
    public function show($id)
    {
        $campaign = SendMail::findOrFail($id);
        
        // Get recipients - receiver_email is already cast to array in the model
        $recipients = $campaign->receiver_email ?? [];
        
        return view('admin.email-campaigns.show', compact('campaign', 'recipients'));
    }

    /**
     * Remove the specified email campaign.
     */
    public function destroy($id)
    {
        $campaign = SendMail::findOrFail($id);
        $campaign->delete();
        
        return back()->with('success', 'Email campaign deleted successfully.');
    }
} 
 
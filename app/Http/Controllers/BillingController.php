<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Setting;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Owner side: pick a plan, pay by transfer, upload the receipt. Admin approves in Sales. */
class BillingController extends Controller
{
    public function index()
    {
        $cafe = $this->cafe();
        $plans = Plan::where('active', true)->where('key', '!=', 'trial')->orderBy('sort_order')->get();
        $subs = $cafe->subscriptions()->get();
        $accounts = Setting::get('billing_accounts', []);
        $support = Setting::get('support', []);

        return view('dashboard.billing', compact('cafe', 'plans', 'subs', 'accounts', 'support'));
    }

    public function store(Request $request)
    {
        $cafe = $this->cafe();
        $data = $request->validate([
            'plan' => ['required', Rule::exists('plans', 'key')->where('active', true)],
            'months' => ['required', 'integer', Rule::in([1, 3, 6, 12])],
            'method' => ['required', Rule::in(['bank', 'telebirr', 'cbe_birr', 'other'])],
            'reference' => ['required', 'string', 'max:120'],
            'receipt' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
        ], ['reference.required' => 'Enter the transaction or reference number from your receipt.']);

        if ($cafe->subscriptions()->where('status', 'pending')->exists()) {
            return back()->with('error', 'You already have a payment waiting for review. We’ll confirm it soon.');
        }

        $plan = Plan::where('key', $data['plan'])->first();
        $sub = new Subscription([
            'plan' => $plan->key,
            'months' => $data['months'],
            'amount' => $plan->price * $data['months'],
            'currency' => $plan->currency,
            'method' => $data['method'],
            'reference' => $data['reference'],
            'receipt' => $request->file('receipt')->store("receipts/{$cafe->id}", 'local'),
            'status' => 'pending',
        ]);
        $sub->cafe_id = $cafe->id;
        $sub->save();

        return back()->with('ok', 'Thanks! We got your receipt and will confirm it within a few hours.');
    }
}

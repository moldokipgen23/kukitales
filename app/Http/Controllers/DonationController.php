<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\DonationCampaign;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    public function index()
    {
        $campaigns = DonationCampaign::active()->orderByDesc('is_featured')->orderByDesc('created_at')->get();
        return view('donations.index', compact('campaigns'));
    }

    public function show(string $slug)
    {
        $campaign = DonationCampaign::where('slug', $slug)->firstOrFail();
        $recent = $campaign->donations()
            ->where('status', 'confirmed')->where('show_on_wall', true)
            ->latest()->limit(10)->get();
        $paymentMethods = $this->paymentMethods();
        return view('donations.show', compact('campaign', 'recent', 'paymentMethods'));
    }

    public function store(Request $request, string $slug)
    {
        $campaign = DonationCampaign::active()->where('slug', $slug)->firstOrFail();

        $data = $request->validate([
            'donor_name' => 'required|string|max:120',
            'donor_email' => 'nullable|email|max:160',
            'donor_phone' => 'nullable|string|max:30',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string|in:upi,bank_transfer,razorpay,stripe,paypal,cash,other',
            'payment_reference' => 'nullable|string|max:120',
            'message' => 'nullable|string|max:500',
            'is_anonymous' => 'nullable|boolean',
        ]);

        Donation::create([
            ...$data,
            'campaign_id' => $campaign->id,
            'user_id' => auth()->id(),
            'currency' => $campaign->currency,
            'status' => 'pending',
            'is_anonymous' => (bool) ($data['is_anonymous'] ?? false),
            'show_on_wall' => true,
        ]);

        return redirect()->route('donate.show', $campaign->slug)
            ->with('success', 'Thank you! Your donation is recorded as PENDING. We will mark it confirmed after we verify the transfer.');
    }

    /** Read configured payment methods from SiteSetting */
    protected function paymentMethods(): array
    {
        return [
            'upi' => SiteSetting::get('donation_upi_id'),                 // e.g. kukitales@upi
            'bank_name' => SiteSetting::get('donation_bank_name'),
            'bank_account' => SiteSetting::get('donation_bank_account'),
            'bank_ifsc' => SiteSetting::get('donation_bank_ifsc'),
            'razorpay_link' => SiteSetting::get('donation_razorpay_link'),
            'stripe_link' => SiteSetting::get('donation_stripe_link'),
            'paypal_link' => SiteSetting::get('donation_paypal_link'),
        ];
    }
}

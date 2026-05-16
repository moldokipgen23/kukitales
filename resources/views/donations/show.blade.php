@extends('layouts.app')

@section('title', 'Donate to ' . $campaign->title . ' — KukiTales')
@section('meta_description', $campaign->short_description)

@section('content')

<div class="breadcrumb">
  <a href="{{ route('home') }}">Home</a> › <a href="{{ route('donate.index') }}">Donate</a> › <span>{{ \Illuminate\Support\Str::limit($campaign->title, 50) }}</span>
</div>

<section class="page" style="max-width:1000px;">
  <div style="display:grid;grid-template-columns:1.5fr 1fr;gap:30px;align-items:start;">
    <div>
      <div class="donate-img" @if($campaign->cover_url) style="height:260px;background-image:url('{{ $campaign->cover_url }}')" @endif>
        @if(!$campaign->cover_url)❤️@endif
      </div>
      <h1 style="font-family:'Cormorant Garamond',serif;font-size:34px;font-weight:700;margin:18px 0 10px;color:var(--text);">{{ $campaign->title }}</h1>
      @if($campaign->beneficiary)<p style="color:var(--text-muted);font-size:13px;margin-bottom:14px;">For: <strong style="color:var(--text-mid);">{{ $campaign->beneficiary }}</strong></p>@endif
      <div class="article-body" style="padding:0;font-size:15px;">{!! $campaign->description !!}</div>

      @if($recent->isNotEmpty())
        <h3 style="font-family:'Cormorant Garamond',serif;font-size:22px;font-weight:700;margin:28px 0 12px;color:var(--text);">Recent supporters</h3>
        <ul style="list-style:none;display:flex;flex-direction:column;gap:8px;">
          @foreach($recent as $d)
            <li style="background:#fff;border:1px solid var(--border);border-radius:8px;padding:10px 14px;display:flex;justify-content:space-between;font-size:13px;">
              <span><strong style="color:var(--text-mid);">{{ $d->display_name }}</strong>@if($d->message) <span style="color:var(--text-muted);font-style:italic;">— "{{ \Illuminate\Support\Str::limit($d->message, 100) }}"</span>@endif</span>
              <strong style="color:var(--red);">{{ $d->currency }} {{ number_format($d->amount, 0) }}</strong>
            </li>
          @endforeach
        </ul>
      @endif
    </div>

    <aside>
      <div class="donate-form">
        @if($campaign->goal_amount)
          <div class="donate-progress"><div style="width:{{ $campaign->progress_percent }}%"></div></div>
          <div class="donate-stats">
            <span><strong>{{ $campaign->currency }} {{ number_format($campaign->raised_amount, 0) }}</strong> raised</span>
            <span>{{ $campaign->progress_percent }}% of {{ $campaign->currency }} {{ number_format($campaign->goal_amount, 0) }}</span>
          </div>
        @endif

        <h3 style="font-family:'Cormorant Garamond',serif;font-size:22px;font-weight:700;margin:6px 0 12px;color:var(--text);">Make a donation</h3>

        <form method="POST" action="{{ route('donate.store', $campaign->slug) }}">
          @csrf

          <div class="field">
            <label>Choose amount ({{ $campaign->currency }})</label>
            <div class="donate-amounts" id="amountPicker">
              @foreach([100, 250, 500, 1000, 2500, 5000] as $a)
                <button type="button" data-amt="{{ $a }}">{{ $campaign->currency }} {{ $a }}</button>
              @endforeach
            </div>
            <input type="number" name="amount" id="amountInput" min="1" step="1" placeholder="Or enter custom amount" required value="{{ old('amount') }}">
            @error('amount')<span class="err">{{ $message }}</span>@enderror
          </div>

          <div class="field"><label>Your name</label>
            <input type="text" name="donor_name" required value="{{ old('donor_name', auth()->user()->name ?? '') }}">
          </div>
          <div class="field"><label>Email (optional)</label>
            <input type="email" name="donor_email" value="{{ old('donor_email', auth()->user()->email ?? '') }}">
          </div>
          <div class="field"><label>Phone (optional)</label>
            <input type="text" name="donor_phone" value="{{ old('donor_phone') }}">
          </div>

          <div class="field">
            <label>How are you sending it?</label>
            <select name="payment_method" required>
              @if($paymentMethods['upi'])<option value="upi">UPI</option>@endif
              @if($paymentMethods['bank_account'])<option value="bank_transfer">Bank transfer</option>@endif
              @if($paymentMethods['razorpay_link'])<option value="razorpay">Razorpay (online)</option>@endif
              @if($paymentMethods['stripe_link'])<option value="stripe">Stripe (online)</option>@endif
              @if($paymentMethods['paypal_link'])<option value="paypal">PayPal</option>@endif
              <option value="cash">Cash (in person)</option>
              <option value="other">Other</option>
            </select>
          </div>

          <div class="field"><label>Transaction reference / UTR (optional)</label>
            <input type="text" name="payment_reference" placeholder="Helps us match your transfer faster">
          </div>

          <div class="field"><label>Message (optional)</label>
            <textarea name="message" rows="2" maxlength="500" placeholder="Why you're giving — shown on the supporters wall if you choose to be public.">{{ old('message') }}</textarea>
          </div>

          <div class="field">
            <label style="display:flex;align-items:center;gap:6px;text-transform:none;font-weight:500;">
              <input type="checkbox" name="is_anonymous" value="1" style="width:auto;"> Donate anonymously
            </label>
          </div>

          <button type="submit" class="btn-primary btn-block">Record my donation</button>
        </form>

        <div class="pay-method-list">
          <p style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:1px;font-weight:700;margin-top:14px;">Send your contribution via</p>
          @if($paymentMethods['upi'])
            <div class="pay-method">📱 <strong>UPI:</strong> {{ $paymentMethods['upi'] }}</div>
          @endif
          @if($paymentMethods['bank_account'])
            <div class="pay-method">🏦 <strong>{{ $paymentMethods['bank_name'] ?: 'Bank' }}</strong><br>A/c: {{ $paymentMethods['bank_account'] }}@if($paymentMethods['bank_ifsc']) · IFSC: {{ $paymentMethods['bank_ifsc'] }}@endif</div>
          @endif
          @if($paymentMethods['razorpay_link'])
            <div class="pay-method">💳 <a href="{{ $paymentMethods['razorpay_link'] }}" target="_blank" rel="noopener" style="color:var(--red);font-weight:700;">Pay with Razorpay →</a></div>
          @endif
          @if($paymentMethods['stripe_link'])
            <div class="pay-method">💳 <a href="{{ $paymentMethods['stripe_link'] }}" target="_blank" rel="noopener" style="color:var(--red);font-weight:700;">Pay with Stripe →</a></div>
          @endif
          @if($paymentMethods['paypal_link'])
            <div class="pay-method">💸 <a href="{{ $paymentMethods['paypal_link'] }}" target="_blank" rel="noopener" style="color:var(--red);font-weight:700;">Donate via PayPal →</a></div>
          @endif
        </div>
      </div>
    </aside>
  </div>
</section>

@push('scripts')
<script>
document.querySelectorAll('#amountPicker button').forEach(b => {
  b.addEventListener('click', () => {
    document.querySelectorAll('#amountPicker button').forEach(x => x.classList.remove('active'));
    b.classList.add('active');
    document.getElementById('amountInput').value = b.dataset.amt;
  });
});
</script>
@endpush
@endsection

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Sabify — Sign in</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
  :root{--brand:#4CAF50;--brand-dark:#2E7D32;--brand-deep:#1B3A2A;}
  html,body{font-family:'Inter',ui-sans-serif,system-ui,sans-serif;}
  .mesh{background-image:linear-gradient(#fff 1px,transparent 1px),linear-gradient(90deg,#fff 1px,transparent 1px);background-size:44px 44px;}
  .dots{background-image:radial-gradient(circle at 1px 1px,rgba(76,175,80,.08) 1px,transparent 0);background-size:22px 22px;}
  .hero-bg{background:radial-gradient(120% 80% at 0% 0%,#4CAF5055 0%,transparent 55%),radial-gradient(90% 70% at 100% 100%,#2E7D3280 0%,transparent 60%),linear-gradient(135deg,#07120D 0%,#0E2018 100%);}
  .grad-text{background:linear-gradient(90deg,#4CAF50 0%,#A5D6A7 100%);-webkit-background-clip:text;background-clip:text;color:transparent;}
  .grad-btn{background:linear-gradient(135deg,#4CAF50 0%,#2E7D32 100%);box-shadow:0 14px 30px -12px #4CAF50;}
  .grad-logo{background:linear-gradient(135deg,#4CAF50 0%,#2E7D32 100%);box-shadow:0 10px 30px -10px #4CAF50;}
  .feat-icon{background:linear-gradient(135deg,#4CAF5033,#2E7D3222);border:1px solid #4CAF5044;}
  .feat-icon-sm{background:#4CAF5022;border:1px solid #4CAF5044;}
  .badge{border-color:#4CAF5066;background:#4CAF501a;color:#C8E6C9;}
  .field:focus-within{border-color:var(--brand);box-shadow:0 0 0 4px rgba(76,175,80,.12);}
  .orb1{background:#4CAF50;}
  .orb2{background:#A5D6A7;}
  .shine{position:absolute;inset:0;transform:translateX(-100%);background:linear-gradient(90deg,transparent,rgba(255,255,255,.25),transparent);transition:transform .7s;}
  .grad-btn:hover .shine{transform:translateX(100%);}
  .grad-btn:hover .arr{transform:translateX(2px);}
  .arr{transition:transform .2s;}
  .cb{appearance:none;width:1rem;height:1rem;border:1px solid rgba(15,26,20,.2);background:#fff;border-radius:.25rem;display:inline-grid;place-items:center;cursor:pointer;}
  .cb:checked{border-color:transparent;background:#fff;}
  .cb:checked::after{content:"";width:.5rem;height:.5rem;background:#4CAF50;border-radius:2px;}
  .pwd-toggle .eye-off{display:none;}
  .pwd-toggle.show .eye{display:none;}
  .pwd-toggle.show .eye-off{display:inline;}
  .error-alert{background:#fed7d7;color:#c53030;padding:12px;border-radius:8px;margin-bottom:20px;font-size:14px;border-left:4px solid #e53e3e;}
</style>
</head>
<body class="min-h-screen bg-[#0B1410] text-white antialiased overflow-x-hidden">
<div class="grid min-h-screen lg:grid-cols-[1.05fr_1fr]">

  <!-- LEFT -->
  <aside class="relative hidden lg:flex flex-col justify-between p-12 xl:p-16 overflow-hidden">
    <div class="absolute inset-0 hero-bg"></div>
    <div class="absolute inset-0 mesh opacity-[0.07]"></div>
    <div class="orb1 absolute -top-32 -left-24 h-[28rem] w-[28rem] rounded-full blur-3xl opacity-40 animate-pulse"></div>
    <div class="orb2 absolute -bottom-40 -right-24 h-[26rem] w-[26rem] rounded-full blur-3xl opacity-30"></div>

    <header class="relative z-10 flex items-center gap-3">
      <div class="grad-logo grid h-11 w-11 place-items-center rounded-xl">
        <svg viewBox="0 0 24 24" class="h-6 w-6 text-white" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-6 9 6v11a2 2 0 0 1-2 2h-4v-7H9v7H5a2 2 0 0 1-2-2z"/></svg>
      </div>
      <div>
        <p class="text-lg font-semibold tracking-tight">Sabify</p>
        <p class="text-[11px] uppercase tracking-[0.22em] text-white/50">Operations · Unified</p>
      </div>
    </header>

    <div class="relative z-10 max-w-xl">
      <p class="badge mb-6 inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-medium tracking-wide">
        <span class="h-1.5 w-1.5 rounded-full" style="background:var(--brand)"></span>
        Built for Retail &amp; Restaurants
      </p>
      <h1 class="text-4xl xl:text-5xl font-semibold leading-[1.05] tracking-tight">
        Run every counter,<br/>kitchen and aisle from
        <span class="grad-text">one calm dashboard.</span>
      </h1>
      <p class="mt-5 text-base text-white/65 max-w-md leading-relaxed">
        Purchase to payout, warehouse to waiter — Sabify keeps your stock honest and your teams aligned.
      </p>

      <div class="mt-10 grid grid-cols-2 gap-3 max-w-xl">
        <div class="rounded-2xl border border-white/10 bg-white/[0.04] backdrop-blur-sm p-4 hover:border-white/20 hover:bg-white/[0.06] transition-all">
          <div class="feat-icon mb-3 grid h-9 w-9 place-items-center rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#4CAF50" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
          </div>
          <p class="text-sm font-semibold">Purchase</p>
          <p class="mt-1 text-[12px] text-white/55 leading-relaxed">Streamlined supplier orders &amp; GRNs.</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/[0.04] backdrop-blur-sm p-4 hover:border-white/20 hover:bg-white/[0.06] transition-all">
          <div class="feat-icon mb-3 grid h-9 w-9 place-items-center rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#4CAF50" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m17 2 4 4-4 4"/><path d="M3 11v-1a4 4 0 0 1 4-4h14"/><path d="m7 22-4-4 4-4"/><path d="M21 13v1a4 4 0 0 1-4 4H3"/></svg>
          </div>
          <p class="text-sm font-semibold">Demands &amp; Transfer</p>
          <p class="mt-1 text-[12px] text-white/55 leading-relaxed">Inter-branch requests in one tap.</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/[0.04] backdrop-blur-sm p-4 hover:border-white/20 hover:bg-white/[0.06] transition-all">
          <div class="feat-icon mb-3 grid h-9 w-9 place-items-center rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#4CAF50" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.97 12.92A2 2 0 0 0 2 14.63v3.24a2 2 0 0 0 .97 1.71l3 1.8a2 2 0 0 0 2.06 0L12 19v-5.5l-5-3-4.03 2.42Z"/><path d="m7 16.5-4.74-2.85"/><path d="m7 16.5 5-3"/><path d="M7 16.5v5.17"/><path d="M12 13.5V19l3.97 2.38a2 2 0 0 0 2.06 0l3-1.8a2 2 0 0 0 .97-1.71v-3.24a2 2 0 0 0-.97-1.71L17 10.5l-5 3Z"/><path d="m17 16.5-5-3"/><path d="m17 16.5 4.74-2.85"/><path d="M17 16.5v5.17"/><path d="M7.97 4.42A2 2 0 0 0 7 6.13v4.37l5 3 5-3V6.13a2 2 0 0 0-.97-1.71l-3-1.8a2 2 0 0 0-2.06 0l-3 1.8Z"/><path d="M12 8 7.26 5.15"/><path d="m12 8 4.74-2.85"/><path d="M12 13.5V8"/></svg>
          </div>
          <p class="text-sm font-semibold">Branch-wise Stock</p>
          <p class="mt-1 text-[12px] text-white/55 leading-relaxed">Granular control across every outlet.</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/[0.04] backdrop-blur-sm p-4 hover:border-white/20 hover:bg-white/[0.06] transition-all">
          <div class="feat-icon mb-3 grid h-9 w-9 place-items-center rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#4CAF50" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M8 16H3v5"/></svg>
          </div>
          <p class="text-sm font-semibold">Real-time Inventory Sync</p>
          <p class="mt-1 text-[12px] text-white/55 leading-relaxed">Every counter, always in sync.</p>
        </div>
      </div>
    </div>

    <div class="relative z-10 max-w-xl">
      <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-4 backdrop-blur-md">
        <p class="mb-3 text-[11px] uppercase tracking-[0.2em] text-white/45">Also inside Sabify</p>
        <div class="grid grid-cols-2 gap-3">
          <div class="flex items-start gap-3">
            <div class="feat-icon-sm grid h-8 w-8 shrink-0 place-items-center rounded-lg">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#4CAF50" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m16 16 2 2 4-4"/><path d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0"/><path d="m7.5 4.27 9 5.15"/><polyline points="3.29 7 12 12 20.71 7"/><line x1="12" x2="12" y1="22" y2="12"/></svg>
            </div>
            <div>
              <p class="text-sm font-medium text-white/90">Stock Update</p>
              <p class="text-[11px] text-white/50 leading-snug">Instant adjustments with full audit.</p>
            </div>
          </div>
          <div class="flex items-start gap-3">
            <div class="feat-icon-sm grid h-8 w-8 shrink-0 place-items-center rounded-lg">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#4CAF50" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="4" width="16" height="16" rx="2"/><rect x="9" y="9" width="6" height="6"/><path d="M15 2v2"/><path d="M15 20v2"/><path d="M2 15h2"/><path d="M2 9h2"/><path d="M20 15h2"/><path d="M20 9h2"/><path d="M9 2v2"/><path d="M9 20v2"/></svg>
            </div>
            <div>
              <p class="text-sm font-medium text-white/90">Sunmi Devices Integrated</p>
              <p class="text-[11px] text-white/50 leading-snug">Plug-and-play POS hardware.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </aside>

  <!-- RIGHT -->
  <main class="relative flex items-center justify-center px-6 py-10 sm:px-10 bg-[#FAFBF7]">
    <div class="pointer-events-none absolute inset-0 dots opacity-[0.5]"></div>
    <div class="relative z-10 w-full max-w-md text-[#0F1A14]">

      <div class="lg:hidden mb-8 flex items-center gap-3">
        <div class="grad-logo grid h-10 w-10 place-items-center rounded-xl">
          <svg viewBox="0 0 24 24" class="h-5 w-5 text-white" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-6 9 6v11a2 2 0 0 1-2 2h-4v-7H9v7H5a2 2 0 0 1-2-2z"/></svg>
        </div>
        <p class="font-semibold">Sab<span style="color:var(--brand)">ify</span></p>
      </div>

      <div>
        <p class="text-xs font-semibold uppercase tracking-[0.22em]" style="color:var(--brand-dark)">Welcome back</p>
        <h2 class="mt-2 text-3xl sm:text-[34px] font-semibold tracking-tight leading-tight">Sign in to your<br/>workspace.</h2>
        <p class="mt-3 text-sm text-[#0F1A14]/60">Enter your credentials to access the Sabify console.</p>
      </div>

      @if ($errors->any())
        <div class="error-alert mt-6">
          {{ $errors->first() }}
        </div>
      @endif

      <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
        @csrf
        
        <label class="block group">
          <span class="mb-1.5 block text-xs font-medium text-[#0F1A14]/70">Username</span>
          <span class="field relative flex items-center rounded-xl border border-[#0F1A14]/10 bg-white px-3.5 py-3 transition-all">
            <span class="mr-2.5 text-[#0F1A14]/40">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </span>
            <input type="text" name="username" value="{{ old('username') }}" autocomplete="username" placeholder="Enter your username" class="w-full bg-transparent text-sm placeholder:text-[#0F1A14]/35 focus:outline-none" required autofocus/>
          </span>
        </label>

        <div>
          <label class="block group">
            <span class="mb-1.5 block text-xs font-medium text-[#0F1A14]/70">Password</span>
            <span class="field relative flex items-center rounded-xl border border-[#0F1A14]/10 bg-white px-3.5 py-3 transition-all">
              <span class="mr-2.5 text-[#0F1A14]/40">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              </span>
              <input id="pwd" type="password" name="password" autocomplete="current-password" placeholder="Enter your password" class="w-full bg-transparent text-sm placeholder:text-[#0F1A14]/35 focus:outline-none" required/>
              <button type="button" class="pwd-toggle ml-2 text-[#0F1A14]/40 hover:text-[#0F1A14]" aria-label="Toggle password" onclick="(function(b){var p=document.getElementById('pwd');b.classList.toggle('show');p.type=p.type==='password'?'text':'password';})(this)">
                <svg class="eye" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                <svg class="eye-off" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
              </button>
            </span>
          </label>
          <div class="mt-3 flex items-center justify-between text-xs">
            <label class="inline-flex items-center gap-2 text-[#0F1A14]/70 cursor-pointer select-none">
              <input type="checkbox" name="remember" class="cb" {{ old('remember') ? 'checked' : '' }}/>
              Keep me signed in
            </label>
            @if (Route::has('password.request'))
              <a href="{{ route('password.request') }}" class="font-medium hover:underline" style="color:var(--brand-dark)">Forgot password?</a>
            @endif
          </div>
        </div>

        <button type="submit" class="grad-btn group relative w-full overflow-hidden rounded-xl px-5 py-3.5 text-sm font-semibold text-white active:scale-[0.99] transition-transform">
          <span class="relative z-10 inline-flex items-center justify-center gap-2">
            Sign in to Sabify
            <svg class="arr" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
          </span>
          <span class="shine"></span>
        </button>
      </form>

      
    </div>
  </main>
</div>
</body>
</html>

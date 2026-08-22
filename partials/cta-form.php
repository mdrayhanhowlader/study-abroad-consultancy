<?php /** @var array $site */ $email = $site['contact']['email']; ?>
<section id="contact" class="section bg-navy text-white relative overflow-hidden" style="background:#1A2B6D">
  <div class="bg-orb" style="width:600px;height:600px;background:#006837;top:-200px;right:-150px;opacity:0.12"></div>
  <div class="bg-orb" style="width:400px;height:400px;background:#B8923B;bottom:-120px;left:-100px;opacity:0.10"></div>
  <div class="absolute inset-0 opacity-[0.03]" style="background-image:radial-gradient(#fff 1px,transparent 1px);background-size:24px 24px"></div>

  <div class="mx-auto max-w-7xl px-4 lg:px-6 grid lg:grid-cols-2 gap-12 lg:gap-16 items-center relative">
    <div class="reveal">
      <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 text-gold text-xs font-semibold tracking-wider uppercase" style="color:#B8923B">
        <span class="w-1.5 h-1.5 rounded-full bg-gold" style="background:#B8923B"></span>
        <?= h(t('cta_form.eyebrow')) ?>
      </div>
      <h2 class="mt-5 font-display text-4xl sm:text-5xl font-bold leading-[1.05] text-white"><?= h(t('cta_form.headline')) ?></h2>
      <p class="mt-5 text-white/75 leading-relaxed max-w-md text-lg"><?= h(t('cta_form.body')) ?></p>

      <div class="mt-8 grid sm:grid-cols-3 gap-4">
        <div class="bg-white/5 border border-white/10 rounded-2xl p-4 text-center">
          <div class="w-10 h-10 rounded-xl bg-brand/20 text-brand grid place-items-center mx-auto mb-3" style="background:rgba(0,104,55,0.2);color:#cce8d6">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
          </div>
          <div class="text-sm font-semibold text-white">Free call</div>
          <div class="text-[10px] text-white/60 mt-1">Within 24 hours</div>
        </div>
        <div class="bg-white/5 border border-white/10 rounded-2xl p-4 text-center">
          <div class="w-10 h-10 rounded-xl bg-brand/20 text-brand grid place-items-center mx-auto mb-3" style="background:rgba(0,104,55,0.2);color:#cce8d6">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
          </div>
          <div class="text-sm font-semibold text-white">30 minutes</div>
          <div class="text-[10px] text-white/60 mt-1">Personal session</div>
        </div>
        <div class="bg-white/5 border border-white/10 rounded-2xl p-4 text-center">
          <div class="w-10 h-10 rounded-xl bg-brand/20 text-brand grid place-items-center mx-auto mb-3" style="background:rgba(0,104,55,0.2);color:#cce8d6">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          </div>
          <div class="text-sm font-semibold text-white">No obligation</div>
          <div class="text-[10px] text-white/60 mt-1">100% free</div>
        </div>
      </div>
    </div>

    <form id="assessmentForm" method="post"
          x-data="assessmentForm('<?= h($email) ?>')"
          @submit.prevent="submit()"
          class="bg-white text-ink rounded-3xl shadow-2xl p-7 sm:p-9 reveal reveal-d2">
      <?= App\Csrf::field() ?>

      <div class="mb-6">
        <h3 class="font-display text-2xl font-bold text-ink">Get Free Assessment</h3>
        <p class="text-sm text-ink-3 mt-1">Tell us about yourself and we will guide you.</p>
      </div>

      <div class="grid sm:grid-cols-2 gap-4">
        <label class="block">
          <span class="text-[11px] font-semibold text-ink-2 uppercase tracking-widest"><?= h(t('cta_form.name')) ?> *</span>
          <input name="name" required type="text" placeholder="Your full name" class="input mt-1.5 rounded-xl bg-paper">
        </label>
        <label class="block">
          <span class="text-[11px] font-semibold text-ink-2 uppercase tracking-widest"><?= h(t('cta_form.phone')) ?> *</span>
          <input name="phone" required type="tel" placeholder="+880 1XXXXXXXXX" class="input mt-1.5 rounded-xl bg-paper">
        </label>
      </div>

      <label class="block mt-4">
        <span class="text-[11px] font-semibold text-ink-2 uppercase tracking-widest"><?= h(t('cta_form.email')) ?> *</span>
        <input name="email" required type="email" placeholder="you@example.com" class="input mt-1.5 rounded-xl bg-paper">
      </label>

      <div class="grid sm:grid-cols-2 gap-4 mt-4">
        <label class="block">
          <span class="text-[11px] font-semibold text-ink-2 uppercase tracking-widest"><?= h(t('cta_form.destination')) ?> *</span>
          <select name="destination" required class="input mt-1.5 rounded-xl bg-paper">
            <option value=""><?= h(t('common.select')) ?></option>
            <option value="Japan"><?= h(t('nav.study_in_japan')) ?></option>
            <option value="Australia"><?= h(t('nav.study_australia')) ?></option>
            <option value="South Korea"><?= h(t('nav.study_korea')) ?></option>
            <option value="Malaysia"><?= h(t('nav.study_malaysia')) ?></option>
          </select>
        </label>
        <label class="block">
          <span class="text-[11px] font-semibold text-ink-2 uppercase tracking-widest"><?= h(t('cta_form.level')) ?> *</span>
          <select name="level" required class="input mt-1.5 rounded-xl bg-paper">
            <option value=""><?= h(t('common.select')) ?></option>
            <option><?= h(t('cta_form.level_foundation')) ?></option>
            <option><?= h(t('cta_form.level_bachelor')) ?></option>
            <option><?= h(t('cta_form.level_master')) ?></option>
            <option><?= h(t('cta_form.level_phd')) ?></option>
            <option><?= h(t('cta_form.level_language')) ?></option>
          </select>
        </label>
      </div>

      <label class="block mt-4">
        <span class="text-[11px] font-semibold text-ink-2 uppercase tracking-widest"><?= h(t('cta_form.message')) ?></span>
        <textarea name="message" rows="3" placeholder="Tell us about your study goals..." class="input mt-1.5 rounded-xl bg-paper"></textarea>
      </label>

      <button type="submit" class="w-full btn btn-primary justify-center rounded-xl py-3.5 mt-5 shadow-lg text-white font-semibold" :disabled="sending">
        <span x-show="!sending && !sent"><?= h(t('cta_form.send')) ?></span>
        <span x-show="sending" x-cloak><?= h(t('cta_form.sending')) ?></span>
        <span x-show="sent" x-cloak><?= h(t('cta_form.sent')) ?></span>
      </button>

      <div x-show="error" x-cloak class="mt-3 text-sm text-accent text-center bg-accent/5 rounded-xl py-2.5" style="background:rgba(227,30,36,0.06)" x-text="error"></div>
      <div x-show="sent" x-cloak class="mt-3 text-sm text-brand text-center bg-brand/5 rounded-xl py-2.5" style="background:rgba(0,104,55,0.06)">
        Thank you! We have received your request and will call you soon.
      </div>
    </form>
  </div>
</section>

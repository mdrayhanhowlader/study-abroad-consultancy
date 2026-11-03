<?php
/** @var array $site */
$assessmentWhatsapp = preg_replace('/\D+/', '', (string) ($site['contact']['phone_tel'] ?? ''));
?>
<section id="contact" class="assessment-section section text-white relative overflow-hidden">
  <div class="bg-orb" style="width:600px;height:600px;background:#006837;top:-200px;right:-150px;opacity:0.12"></div>
  <div class="bg-orb" style="width:400px;height:400px;background:#B8923B;bottom:-120px;left:-100px;opacity:0.10"></div>
  <div class="absolute inset-0 opacity-[0.03]" style="background-image:radial-gradient(#fff 1px,transparent 1px);background-size:24px 24px"></div>

  <div class="mx-auto max-w-7xl px-4 lg:px-6 grid lg:grid-cols-2 gap-12 lg:gap-16 items-center relative">
    <div class="reveal">
      <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 text-gold text-xs font-semibold tracking-wider uppercase" style="color:#B8923B">
        <span class="w-1.5 h-1.5 rounded-full bg-gold" style="background:#B8923B"></span>
        <?= h(App\Homepage::text('cta_form.eyebrow', t('cta_form.eyebrow'))) ?>
      </div>
      <h2 class="mt-5 font-display text-4xl sm:text-5xl font-bold leading-[1.05] text-white"><?= h(App\Homepage::text('cta_form.headline', t('cta_form.headline'))) ?></h2>
      <p class="mt-5 text-white/75 leading-relaxed max-w-md text-lg"><?= h(App\Homepage::text('cta_form.body', t('cta_form.body'))) ?></p>

      <div class="assessment-guide-card mt-7 flex items-center gap-4 rounded-2xl border border-white/15 bg-white/5 p-4">
        <div class="assessment-guide-avatar relative grid h-20 w-20 shrink-0 place-items-center overflow-hidden rounded-full" aria-hidden="true">
          <span class="absolute inset-0 bg-gradient-to-br from-emerald-300 to-teal-700"></span>
          <svg class="relative h-12 w-12 text-white" viewBox="0 0 48 48" fill="none">
            <circle cx="24" cy="17" r="8" fill="currentColor" fill-opacity=".95"/>
            <path d="M9 43c.8-9.1 6.8-14 15-14s14.2 4.9 15 14" fill="currentColor" fill-opacity=".95"/>
            <path d="M31 8h10a3 3 0 0 1 3 3v5a3 3 0 0 1-3 3h-3l-4 3v-3h-3a3 3 0 0 1-3-3v-5a3 3 0 0 1 3-3Z" fill="#F2C76B"/>
            <path d="M33 13h7" stroke="#25483A" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
          <span class="absolute bottom-1 right-1 h-4 w-4 rounded-full border-2 border-[#25483A] bg-emerald-300"></span>
        </div>
        <div>
          <div class="text-[10px] font-bold uppercase tracking-[0.18em] text-[#F2C76B]"><?= h(App\Homepage::text('cta_form.guide_label', 'Personal guidance')) ?></div>
          <div class="mt-1 font-display text-lg font-semibold text-white"><?= h(App\Homepage::text('cta_form.guide_title', 'Talk to a Japan counsellor')) ?></div>
          <div class="mt-1 text-sm leading-relaxed text-white/65"><?= h(App\Homepage::text('cta_form.guide_body', 'Get clear answers for your next step.')) ?></div>
        </div>
      </div>

      <div class="assessment-benefits mt-8 grid sm:grid-cols-3 gap-4">
        <div class="assessment-benefit-card bg-white/5 border border-white/10 rounded-2xl p-4 text-center">
          <div class="w-10 h-10 rounded-xl bg-brand/20 text-brand grid place-items-center mx-auto mb-3" style="background:rgba(0,104,55,0.2);color:#cce8d6">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
          </div>
          <div class="text-sm font-semibold text-white"><?= h(App\Homepage::text('cta_form.benefit_1_title', 'Free call')) ?></div>
          <div class="text-[10px] text-white/60 mt-1"><?= h(App\Homepage::text('cta_form.benefit_1_desc', 'Within 24 hours')) ?></div>
        </div>
        <div class="assessment-benefit-card bg-white/5 border border-white/10 rounded-2xl p-4 text-center">
          <div class="w-10 h-10 rounded-xl bg-brand/20 text-brand grid place-items-center mx-auto mb-3" style="background:rgba(0,104,55,0.2);color:#cce8d6">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
          </div>
          <div class="text-sm font-semibold text-white"><?= h(App\Homepage::text('cta_form.benefit_2_title', '30 minutes')) ?></div>
          <div class="text-[10px] text-white/60 mt-1"><?= h(App\Homepage::text('cta_form.benefit_2_desc', 'Personal session')) ?></div>
        </div>
        <div class="assessment-benefit-card bg-white/5 border border-white/10 rounded-2xl p-4 text-center">
          <div class="w-10 h-10 rounded-xl bg-brand/20 text-brand grid place-items-center mx-auto mb-3" style="background:rgba(0,104,55,0.2);color:#cce8d6">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          </div>
          <div class="text-sm font-semibold text-white"><?= h(App\Homepage::text('cta_form.benefit_3_title', 'No obligation')) ?></div>
          <div class="text-[10px] text-white/60 mt-1"><?= h(App\Homepage::text('cta_form.benefit_3_desc', '100% free')) ?></div>
        </div>
      </div>
    </div>

    <form id="assessmentForm" method="post"
          data-whatsapp-number="<?= h($assessmentWhatsapp) ?>"
          data-whatsapp-greeting="<?= h(t('cta_form.whatsapp_greeting')) ?>"
          class="assessment-form bg-white text-ink rounded-3xl shadow-2xl p-7 sm:p-9 reveal reveal-d2">
      <?= App\Csrf::field() ?>

      <div class="mb-6">
        <h3 class="font-display text-2xl font-bold text-ink"><?= h(App\Homepage::text('cta_form.send', t('cta_form.send'))) ?></h3>
        <p class="text-sm text-ink-3 mt-1"><?= h(App\Homepage::text('cta_form.form_hint', t('cta_form.form_hint'))) ?></p>
      </div>

      <div class="grid sm:grid-cols-2 gap-4">
        <label class="block">
          <span class="text-[11px] font-semibold text-ink-2 uppercase tracking-widest"><?= h(App\Homepage::text('cta_form.name', t('cta_form.name'))) ?> *</span>
          <input name="name" required type="text" placeholder="<?= h(App\Homepage::text('cta_form.name_placeholder', 'Your full name')) ?>" class="input mt-1.5 rounded-xl bg-paper">
        </label>
        <label class="block">
          <span class="text-[11px] font-semibold text-ink-2 uppercase tracking-widest"><?= h(App\Homepage::text('cta_form.phone', t('cta_form.phone'))) ?> *</span>
          <input name="phone" required type="tel" placeholder="<?= h(App\Homepage::text('cta_form.phone_placeholder', '+880 1XXXXXXXXX')) ?>" class="input mt-1.5 rounded-xl bg-paper">
        </label>
        <label class="block sm:col-span-2">
          <span class="text-[11px] font-semibold text-ink-2 uppercase tracking-widest"><?= h(t('cta_form.email')) ?> *</span>
          <input name="email" required type="email" autocomplete="email" placeholder="<?= h(t('cta_form.email_placeholder')) ?>" class="input mt-1.5 rounded-xl bg-paper">
        </label>
      </div>

      <div class="grid sm:grid-cols-2 gap-4 mt-4">
        <label class="block">
          <span class="text-[11px] font-semibold text-ink-2 uppercase tracking-widest"><?= h(App\Homepage::text('cta_form.destination', t('cta_form.destination'))) ?> <span class="font-normal text-ink-3">(optional)</span></span>
          <select name="destination" class="input mt-1.5 rounded-xl bg-paper">
            <option value=""><?= h(t('common.select')) ?></option>
            <option value="Japan"><?= h(App\Homepage::text('cta_form.destination_japan', t('nav.study_in_japan'))) ?></option>
            <option value="Australia"><?= h(App\Homepage::text('cta_form.destination_australia', t('nav.study_australia'))) ?></option>
            <option value="South Korea"><?= h(App\Homepage::text('cta_form.destination_korea', t('nav.study_korea'))) ?></option>
            <option value="Malaysia"><?= h(App\Homepage::text('cta_form.destination_malaysia', t('nav.study_malaysia'))) ?></option>
          </select>
        </label>
        <label class="block">
          <span class="text-[11px] font-semibold text-ink-2 uppercase tracking-widest"><?= h(App\Homepage::text('cta_form.level', t('cta_form.level'))) ?> <span class="font-normal text-ink-3">(optional)</span></span>
          <select name="study_level" class="input mt-1.5 rounded-xl bg-paper">
            <option value=""><?= h(t('common.select')) ?></option>
            <option><?= h(App\Homepage::text('cta_form.level_foundation', t('cta_form.level_foundation'))) ?></option>
            <option><?= h(App\Homepage::text('cta_form.level_bachelor', t('cta_form.level_bachelor'))) ?></option>
            <option><?= h(App\Homepage::text('cta_form.level_master', t('cta_form.level_master'))) ?></option>
            <option><?= h(App\Homepage::text('cta_form.level_phd', t('cta_form.level_phd'))) ?></option>
            <option><?= h(App\Homepage::text('cta_form.level_language', t('cta_form.level_language'))) ?></option>
          </select>
        </label>
      </div>

      <label class="block mt-4">
        <span class="text-[11px] font-semibold text-ink-2 uppercase tracking-widest"><?= h(App\Homepage::text('cta_form.message', t('cta_form.message'))) ?></span>
        <textarea name="message" rows="3" placeholder="<?= h(App\Homepage::text('cta_form.message_placeholder', 'Tell us about your study goals...')) ?>" class="input mt-1.5 rounded-xl bg-paper"></textarea>
      </label>

      <button type="submit" class="w-full btn btn-primary justify-center rounded-xl py-3.5 mt-5 shadow-lg text-white font-semibold">
        <span><?= h(t('cta_form.send')) ?></span>
        <span aria-hidden="true">→</span>
      </button>

      <a id="assessmentWhatsappLink"
         href="https://wa.me/<?= h($assessmentWhatsapp) ?>?text=<?= rawurlencode(t('cta_form.whatsapp_greeting')) ?>"
         target="_blank"
         rel="noopener"
         class="w-full btn justify-center rounded-xl py-3.5 mt-3 border border-[#25D366] text-[#128C7E] font-semibold hover:bg-[#25D366]/10">
        <?= h(t('cta_form.whatsapp_expert')) ?>
      </a>

      <div id="assessmentError" hidden role="alert" class="mt-3 text-sm text-accent text-center bg-accent/5 rounded-xl py-2.5" style="background:rgba(227,30,36,0.06)"></div>
      <div id="assessmentSuccess" hidden role="status" class="mt-3 text-sm text-brand text-center bg-brand/5 rounded-xl py-2.5" style="background:rgba(0,104,55,0.06)">
        <?= h(t('cta_form.sent')) ?>
      </div>
    </form>
  </div>
</section>

<?php
require __DIR__ . '/partials/_icons.php';
require __DIR__ . '/app/bootstrap.php';
$site = App\DataAdapter::site();
$page_title = t('nav.contact') . ' — ' . $site['brand']['full_name'];
$page_desc  = 'Contact Manami Education at our Farmgate, Dhaka office. Phone, email, address, and free assessment form.';
$canonical  = $site['brand']['domain'] . '/contact.php';
$email      = $site['contact']['email'];
$success    = false;
$error      = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    App\Csrf::verifyRequest();
    $name    = trim((string)($_POST['name'] ?? ''));
    $phone   = trim((string)($_POST['phone'] ?? ''));
    $emailIn = trim((string)($_POST['email'] ?? ''));
    $subject = trim((string)($_POST['subject'] ?? ''));
    $body    = trim((string)($_POST['message'] ?? ''));
    if ($name && $phone && $body) {
        $leadId = App\Lead::create([
            'name'    => $name,
            'phone'   => $phone,
            'email'   => $emailIn ?: null,
            'message' => $body,
            'source'  => 'contact_form',
        ]);

        $mailBody  = '<p>New contact form submission from the Manami website.</p>';
        $mailBody .= '<p><strong>Name:</strong> ' . h($name) . '<br><strong>Phone:</strong> ' . h($phone) . '<br><strong>Email:</strong> ' . h($emailIn) . '<br><strong>Subject:</strong> ' . h($subject) . '</p>';
        $mailBody .= '<p><strong>Message:</strong><br>' . nl2br(h($body)) . '</p>';
        App\Mail::send($email, $site['contact']['email'], 'Contact form: ' . ($subject ?: 'New message'), App\Mail::wrap('New contact form message', $mailBody));
        App\Pixel::logEvent('Lead', ['source' => 'contact_form', 'lead_id' => $leadId]);
        $success = true;
    } else {
        $error = t('contact.error_required');
    }
}
?>
<!doctype html>
<html lang="<?= h(current_lang()) ?>" dir="<?= h(\App\I18n::dir(current_lang())) ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title><?= h($page_title) ?></title>
  <meta name="description" content="<?= h($page_desc) ?>">
  <link rel="canonical" href="<?= h($canonical) ?>">
  <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
  <script>
    tailwind.config = { theme: { extend: {
      colors: { paper:'#FAF8F4','paper-2':'#F4F1EA',ink:{DEFAULT:'#1E242B',2:'#4A5159',3:'#8A857C'},hair:{DEFAULT:'#E5E1D9',2:'#D7D2C7'},brand:{DEFAULT:'#006837',dark:'#004d28',light:'#cce8d6'},navy:{DEFAULT:'#1A2B6D',dark:'#101f4f',light:'#d6dcec'},accent:{DEFAULT:'#E31E24',dark:'#a8131a',light:'#f7c8ca'},gold:'#B8923B' },
      fontFamily: { display:['"Playfair Display"','Georgia','serif'], sans:['Inter','ui-sans-serif','system-ui','sans-serif'] }
    }}}
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,600;0,700;0,800;1,500;1,600;1,700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <!-- PWA -->
  <link rel="manifest" href="manifest.json">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="Manami Education">
  <?php require __DIR__ . '/partials/favicon.php'; ?>

  <script defer src="https://unpkg.com/alpinejs@3.13.5/dist/cdn.min.js"></script>
<script>
    if ('serviceWorker' in navigator) {
      window.addEventListener('load', () => navigator.serviceWorker.register('./sw.js'));
    }
  </script>
</head>
<body class="font-sans text-ink antialiased bg-paper">
  <?php require __DIR__ . '/partials/topbar.php'; ?>
  <?php require __DIR__ . '/partials/header.php'; ?>

  <main>
    <section class="relative overflow-hidden" style="background: linear-gradient(180deg, #1E242B 0%, #1A2B6D 100%);">
      <div class="bg-orb" style="width:500px;height:500px;background:#006837;top:-100px;right:-100px;opacity:0.18"></div>
      <div class="mx-auto max-w-7xl px-4 lg:px-6 py-20 lg:py-32 relative">
        <div class="max-w-3xl">
          <div class="eyebrow" data-num="08" style="color:#B8923B"><?= h(t('nav.contact')) ?></div>
          <h1 class="mt-5 font-display text-5xl sm:text-6xl lg:text-7xl font-bold leading-[0.95] text-white tracking-tight">Get in touch</h1>
          <p class="mt-6 text-white/80 text-lg max-w-2xl">Visit our Farmgate office, call us, or send a message — we usually reply within an hour during business hours.</p>
        </div>
      </div>
    </section>

    <section class="section bg-paper">
      <div class="mx-auto max-w-7xl px-4 lg:px-6 grid lg:grid-cols-2 gap-12 lg:gap-16">
        <div class="reveal">
          <div class="eyebrow" data-num="·">Office</div>
          <h2 class="mt-4 font-display text-3xl font-bold text-ink">Manami Education</h2>

          <div class="mt-6 space-y-6">
            <div>
              <div class="text-[10px] tracking-widest text-ink-3 uppercase mb-1">Address</div>
              <a href="<?= h($site['contact']['address_link']) ?>" target="_blank" rel="noopener" class="text-ink hover:text-brand leading-relaxed block">
                <?= h($site['contact']['address']) ?>
              </a>
            </div>
            <div class="grid sm:grid-cols-2 gap-6">
              <div>
                <div class="text-[10px] tracking-widest text-ink-3 uppercase mb-1">Phone</div>
                <a href="tel:<?= h($site['contact']['phone_tel']) ?>" class="text-ink hover:text-brand font-semibold"><?= h($site['contact']['phone']) ?></a>
              </div>
              <div>
                <div class="text-[10px] tracking-widest text-ink-3 uppercase mb-1">Email</div>
                <a href="mailto:<?= h($site['contact']['email']) ?>" class="text-ink hover:text-brand break-all"><?= h($site['contact']['email']) ?></a>
              </div>
            </div>
            <div>
              <div class="text-[10px] tracking-widest text-ink-3 uppercase mb-1">Hours</div>
              <div class="text-ink">Sun – Thu · 10:00 – 19:00</div>
              <div class="text-ink-3 text-sm">Friday closed · Saturday by appointment</div>
            </div>
            <div>
              <a href="<?= h($site['contact']['address_link']) ?>" target="_blank" rel="noopener" class="btn btn-primary mt-2"><?= h(t('common.open_in_maps')) ?> →</a>
            </div>
          </div>

          <div class="mt-10 aspect-[4/3] bg-paper-2 overflow-hidden" style="background:#F4F1EA">
            <iframe src="https://maps.google.com/maps?q=<?= urlencode($site['contact']['address']) ?>&output=embed" width="100%" height="100%" style="border:0" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Office location"></iframe>
          </div>
        </div>

        <div class="reveal reveal-d2">
          <div class="eyebrow" data-num="·">Message</div>
          <h2 class="mt-4 font-display text-3xl font-bold text-ink">Send us a message</h2>
          <p class="mt-2 text-ink-2 text-sm">We usually reply within an hour during business hours.</p>

          <?php if ($success): ?>
            <div class="mt-6 bg-brand/5 border border-brand/30 p-6 text-ink" style="background:rgba(0,104,55,0.05);border-color:rgba(0,104,55,0.3)">
              <div class="font-display text-xl font-bold text-brand">✓ Message sent</div>
              <p class="mt-2 text-ink-2 text-sm">Thank you — we'll get back to you within an hour during business hours.</p>
            </div>
          <?php endif; ?>
          <?php if ($error): ?>
            <div class="mt-6 bg-accent/5 border border-accent/30 p-4 text-sm" style="background:rgba(227,30,36,0.05);border-color:rgba(227,30,36,0.3);color:#E31E24"><?= h($error) ?></div>
          <?php endif; ?>

          <form method="post" class="mt-6 bg-white border border-hair p-7 sm:p-8 space-y-4" style="border-color:#E5E1D9">
            <?= App\Csrf::field() ?>
            <div class="grid sm:grid-cols-2 gap-4">
              <label class="block">
                <span class="text-[10px] font-semibold text-ink-2 uppercase tracking-widest">Name *</span>
                <input name="name" required class="input mt-2" value="<?= h($_POST['name'] ?? '') ?>">
              </label>
              <label class="block">
                <span class="text-[10px] font-semibold text-ink-2 uppercase tracking-widest">Phone *</span>
                <input name="phone" required type="tel" class="input mt-2" value="<?= h($_POST['phone'] ?? '') ?>">
              </label>
            </div>
            <label class="block">
              <span class="text-[10px] font-semibold text-ink-2 uppercase tracking-widest">Email</span>
              <input name="email" type="email" class="input mt-2" value="<?= h($_POST['email'] ?? '') ?>">
            </label>
            <label class="block">
              <span class="text-[10px] font-semibold text-ink-2 uppercase tracking-widest">Subject</span>
              <input name="subject" class="input mt-2" value="<?= h($_POST['subject'] ?? '') ?>" placeholder="Free assessment / General enquiry / Partnership">
            </label>
            <label class="block">
              <span class="text-[10px] font-semibold text-ink-2 uppercase tracking-widest">Message *</span>
              <textarea name="message" rows="5" required class="input mt-2"><?= h($_POST['message'] ?? '') ?></textarea>
            </label>
            <button class="w-full btn btn-primary justify-center">Send message →</button>
          </form>
        </div>
      </div>
    </section>

    <?php require __DIR__ . '/partials/pre-footer.php'; ?>
  </main>

  <?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html>

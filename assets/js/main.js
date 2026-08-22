/* Custom interactivity for the landing page.
   Alpine handles most UI (mobile menu, accordion, testimonial slider, countdown, form).
   This file adds scroll reveal + animated stat counters. */

document.addEventListener('DOMContentLoaded', () => {
  /* ---------- Scroll reveal ---------- */
  const revealTargets = document.querySelectorAll('section, .reveal');
  revealTargets.forEach(el => el.classList.add('reveal'));
  const io = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        e.target.classList.add('in-view');
        io.unobserve(e.target);
      }
    });
  }, { threshold: 0.08 });
  revealTargets.forEach(el => io.observe(el));

  /* ---------- Animated stat counters ---------- */
  const counters = document.querySelectorAll('[data-counter]');
  const animateCounter = (el) => {
    const target = parseInt(el.dataset.target, 10) || 0;
    const suffix = el.textContent.replace(/[0-9]/g, '');
    const dur = 1800;
    const start = performance.now();
    const step = (now) => {
      const p = Math.min(1, (now - start) / dur);
      const eased = 1 - Math.pow(1 - p, 3);
      el.textContent = Math.floor(target * eased).toLocaleString() + suffix;
      if (p < 1) requestAnimationFrame(step);
      else el.textContent = target.toLocaleString() + suffix;
    };
    requestAnimationFrame(step);
  };
  const cio = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting) { animateCounter(e.target); cio.unobserve(e.target); }
    });
  }, { threshold: 0.5 });
  counters.forEach(c => cio.observe(c));
});

/* ---------- Alpine.js components ---------- */
function countdown(targetIso) {
  return {
    units: [
      { k: 'Days',    v: '00' },
      { k: 'Hours',   v: '00' },
      { k: 'Minutes', v: '00' },
      { k: 'Seconds', v: '00' },
    ],
    _t: null,
    start() {
      const tick = () => {
        const diff = Math.max(0, new Date(targetIso) - new Date());
        const d = Math.floor(diff / 86400000);
        const h = Math.floor((diff % 86400000) / 3600000);
        const m = Math.floor((diff % 3600000) / 60000);
        const s = Math.floor((diff % 60000) / 1000);
        this.units = [
          { k: 'Days',    v: String(d).padStart(2, '0') },
          { k: 'Hours',   v: String(h).padStart(2, '0') },
          { k: 'Minutes', v: String(m).padStart(2, '0') },
          { k: 'Seconds', v: String(s).padStart(2, '0') },
        ];
      };
      tick();
      this._t = setInterval(tick, 1000);
    }
  };
}

function assessmentForm(toEmail) {
  return {
    sending: false,
    sent: false,
    error: '',
    submit() {
      this.sending = true;
      this.error = '';
      const form = document.getElementById('assessmentForm');
      const data = new FormData(form);
      data.append('source', 'cta_form');

      const csrfToken = form.querySelector('[name="_csrf"]');
      if (csrfToken) data.append('_csrf', csrfToken.value);

      fetch('api/lead.php', { method: 'POST', body: data })
        .then(r => r.json())
        .then(json => {
          if (json.ok) {
            this.sending = false;
            this.sent = true;
          } else {
            this.sending = false;
            this.error = json.message || 'Something went wrong.';
          }
        })
        .catch(() => {
          this.sending = false;
          this.error = 'Network error. Please try again.';
        });
    }
  };
}

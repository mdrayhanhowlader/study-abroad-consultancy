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

  const assessment = document.getElementById('assessmentForm');
  if (assessment) {
    assessment.addEventListener('submit', (event) => {
      event.preventDefault();
      submitAssessmentForm(assessment);
    });
    const whatsappLink = document.getElementById('assessmentWhatsappLink');
    whatsappLink?.addEventListener('click', () => {
      const data = new FormData(assessment);
      const fields = [
        ['Name', data.get('name')],
        ['Phone', data.get('phone')],
        ['Preferred destination', data.get('destination')],
        ['Study level', data.get('study_level')],
        ['Inquiry', data.get('message')]
      ].filter(([, value]) => String(value || '').trim());
      const greeting = assessment.dataset.whatsappGreeting || '';
      const message = [greeting, ...fields.map(([label, value]) => `${label}: ${String(value).trim()}`)].filter(Boolean).join('\n\n');
      whatsappLink.href = `https://wa.me/${assessment.dataset.whatsappNumber}?text=${encodeURIComponent(message)}`;
    });
  }
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

function submitAssessmentForm(form) {
  const button = form.querySelector('button[type="submit"]');
  const errorBox = document.getElementById('assessmentError');
  const successBox = document.getElementById('assessmentSuccess');
  const data = new FormData(form);
  data.append('source', 'cta_form');
  errorBox.hidden = true;
  successBox.hidden = true;
  button.disabled = true;

  fetch('api/lead.php', { method: 'POST', body: data })
    .then(response => response.json())
    .then(result => {
      if (!result.ok) throw new Error(result.message || 'Something went wrong.');
      successBox.hidden = false;
    })
    .catch(error => {
      errorBox.textContent = error.message || 'Network error. Please try again.';
      errorBox.hidden = false;
    })
    .finally(() => {
      button.disabled = false;
    });
}

/* Custom page interactivity not covered by Alpine components. */

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

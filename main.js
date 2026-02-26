// global script for UI enhancements
window.addEventListener('DOMContentLoaded', () => {
  // mobile nav toggle
  const toggle = document.querySelector('.nav-toggle');
  const nav = document.querySelector('.navbar');
  if (toggle && nav) {
    toggle.addEventListener('click', () => {
      nav.classList.toggle('open');
      toggle.classList.toggle('open');
    });
  }

  // scroll-to-top button
  const scrollBtn = document.getElementById('scrollTop');
  window.addEventListener('scroll', () => {
    if (!scrollBtn) return;
    if (window.scrollY > 300) {
      scrollBtn.classList.add('show');
    } else {
      scrollBtn.classList.remove('show');
    }
  });
  if (scrollBtn) {
    scrollBtn.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  // intersection observer for fade-in elements
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.15 });

  document.querySelectorAll('.fade-in, .blog-card, .feature-box, .service, .stats-box').forEach(el => {
    el.classList.add('fade-in');
    observer.observe(el);
  });

  // simple testimonials slider
  const cards = document.querySelectorAll('.testimonial-card');
  if (cards.length) {
    let idx = 0;
    cards.forEach((c, i) => {
      c.style.display = i === 0 ? 'block' : 'none';
    });
    setInterval(() => {
      cards[idx].style.display = 'none';
      idx = (idx + 1) % cards.length;
      cards[idx].style.display = 'block';
    }, 5000);
  }
});
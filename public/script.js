document.addEventListener('DOMContentLoaded', function() {
  // Mobile nav toggle
  const mobileToggle = document.getElementById('mobileToggle');
  const navLinks = document.getElementById('navLinks');
  if (mobileToggle) {
    mobileToggle.addEventListener('click', function() {
      navLinks.classList.toggle('show');
    });
  }

  document.querySelectorAll('.nav-links a').forEach(link => {
    link.addEventListener('click', () => {
      if (navLinks) navLinks.classList.remove('show');
    });
  });

  // Smooth scroll
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
      const href = this.getAttribute('href');
      if (href === '#') return;
      e.preventDefault();
      const target = document.querySelector(href);
      if (target) {
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });

  // FAQ accordion
  document.querySelectorAll('.faq-question').forEach(q => {
    q.addEventListener('click', function() {
      const item = this.parentElement;
      item.classList.toggle('open');
    });
  });

  // Admin lock modal
  const adminLock = document.getElementById('adminLock');
  const adminModal = document.getElementById('adminModal');
  const modalClose = document.getElementById('modalClose');

  if (adminLock && adminModal) {
    adminLock.addEventListener('click', function() {
      adminModal.classList.add('show');
    });

    if (modalClose) {
      modalClose.addEventListener('click', function() {
        adminModal.classList.remove('show');
      });
    }

    adminModal.addEventListener('click', function(e) {
      if (e.target === adminModal) {
        adminModal.classList.remove('show');
      }
    });
  }
});

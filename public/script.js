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

  // GSAP Pricing Cards Hover Animation (like FoxiBytes)
  if (typeof gsap !== 'undefined') {
    const pricingCards = document.querySelectorAll('.pricing-card');
    const pricingGrid = document.querySelector('.pricing-grid');

    if (pricingCards.length && pricingGrid) {
      var bgHighlight = document.createElement('div');
      bgHighlight.id = 'bgHighlight';
      bgHighlight.style.cssText = 'position:absolute;border-radius:20px;background:linear-gradient(90deg, rgba(255,0,0,0.08), rgba(154,3,30,0.05));transition:all 0.3s;pointer-events:none;z-index:0;';
      pricingGrid.style.position = 'relative';
      pricingGrid.appendChild(bgHighlight);

      function moveHighlight(target) {
        if (!target) return;
        const rect = target.getBoundingClientRect();
        const parentRect = pricingGrid.getBoundingClientRect();

        gsap.to(bgHighlight, {
          x: rect.left - parentRect.left,
          y: rect.top - parentRect.top,
          width: rect.width,
          height: rect.height,
          duration: 0.3,
          ease: 'power3.out'
        });
      }

      if (pricingCards.length > 0) {
        moveHighlight(pricingCards[1]);

        pricingCards.forEach(function(card) {
          card.addEventListener('mouseenter', function() { moveHighlight(card); });
        });

        pricingGrid.addEventListener('mouseleave', function() { moveHighlight(pricingCards[1]); });
      }
    }
  }
});

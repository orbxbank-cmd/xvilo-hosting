// Mobile nav toggle
document.getElementById('mobileToggle').addEventListener('click', function() {
  document.getElementById('navLinks').classList.toggle('show');
});

document.querySelectorAll('.nav-links a').forEach(link => {
  link.addEventListener('click', () => {
    document.getElementById('navLinks').classList.remove('show');
  });
});

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

// Currency toggle
document.querySelectorAll('.toggle-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    document.querySelectorAll('.toggle-btn').forEach(b => b.classList.remove('active'));
    this.classList.add('active');
    const currency = this.dataset.currency;
    document.querySelectorAll('.pricing-card .price').forEach(price => {
      const eur = price.dataset.eur;
      const mad = price.dataset.mad;
      if (currency === 'eur') {
        price.innerHTML = '<span class="currency">€</span>' + eur + '<span class="period">/mo</span>';
        price.style.display = eur === '-' ? 'none' : '';
        if (eur === '-') price.closest('.pricing-card').style.display = 'none';
        else price.closest('.pricing-card').style.display = '';
      } else {
        if (mad === '-') {
          price.closest('.pricing-card').style.display = 'none';
          return;
        }
        price.closest('.pricing-card').style.display = '';
        price.innerHTML = '<span class="currency">DH</span>' + mad + '<span class="period">/mo</span>';
      }
    });
  });
});

// Order button — scroll to payment section
document.querySelectorAll('.order-btn').forEach(btn => {
  btn.addEventListener('click', function(e) {
    e.preventDefault();
    const plan = this.closest('.pricing-card').querySelector('h3').textContent;
    document.getElementById('payment').scrollIntoView({ behavior: 'smooth', block: 'start' });
  });
});

// FAQ accordion
document.querySelectorAll('.faq-question').forEach(q => {
  q.addEventListener('click', function() {
    const item = this.parentElement;
    item.classList.toggle('open');
  });
});

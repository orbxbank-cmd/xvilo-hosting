<?php require __DIR__ . '/../templates/header.php'; ?>

  <section class="hero">
    <div class="hero-bg"></div>
    <div class="container hero-inner">
      <div class="hero-badge">SA-MP 0.3.7</div>
      <h1 class="hero-title">
        SA-MP Server<br />
        <span class="gradient-text">Hosting</span>
      </h1>
      <p class="hero-sub">
        Hébergement SA-MP performant avec protection DDoS, plugin vocal, et panel Pterodactyl.<br />
        Paiement via Inwi &amp; Orange Money.
      </p>
      <div class="hero-cta">
        <a href="#pricing" class="btn btn-primary btn-lg">Voir les Plans</a>
        <a href="https://discord.gg/E9HjMePMsalBUEHScBfiw4" class="btn btn-outline btn-lg" target="_blank">Discord</a>
      </div>
      <div class="hero-stats">
        <div class="stat"><span class="stat-num">99.9%</span><span class="stat-label">Uptime</span></div>
        <div class="stat"><span class="stat-num">4</span><span class="stat-label">Plans</span></div>
        <div class="stat"><span class="stat-num">Instant</span><span class="stat-label">Setup</span></div>
      </div>
    </div>
  </section>

  <section id="pricing" class="section pricing">
    <div class="container">
      <h2 class="section-title">Plans &amp; <span class="gradient-text">Pricing</span></h2>
      <p class="section-sub">Tous les plans incluent la protection DDoS, support du plugin vocal et accès au panel Pterodactyl.</p>
      <div class="pricing-grid">
        <div class="pricing-card">
          <div class="pricing-header">
            <h3>SAMP I</h3>
            <div class="price"><span class="currency">DH</span>15<span class="period">/mo</span></div>
          </div>
          <ul class="pricing-features">
            <li><strong>250</strong> Player Slots</li>
            <li>1x CPU Thread</li>
            <li>1 GB DDR5 RAM</li>
            <li>50 GB NVMe SSD</li>
            <li>17 Tbit DDoS Protection</li>
            <li>Voice Plugin Support</li>
            <li>Pterodactyl Panel</li>
          </ul>
          <a href="/order.php?plan=SAMP+I&price=15" class="btn btn-primary btn-block">Order Now</a>
        </div>
        <div class="pricing-card featured">
          <div class="badge-popular">Le plus populaire</div>
          <div class="pricing-header">
            <h3>SAMP II</h3>
            <div class="price"><span class="currency">DH</span>20<span class="period">/mo</span></div>
          </div>
          <ul class="pricing-features">
            <li><strong>500</strong> Player Slots</li>
            <li>1x CPU Thread</li>
            <li>2 GB DDR5 RAM</li>
            <li>100 GB NVMe SSD</li>
            <li>17 Tbit DDoS Protection</li>
            <li>Voice Plugin Support</li>
            <li>Pterodactyl Panel</li>
          </ul>
          <a href="/order.php?plan=SAMP+II&price=20" class="btn btn-primary btn-block">Order Now</a>
        </div>
        <div class="pricing-card">
          <div class="pricing-header">
            <h3>SAMP III</h3>
            <div class="price"><span class="currency">DH</span>30<span class="period">/mo</span></div>
          </div>
          <ul class="pricing-features">
            <li><strong>750</strong> Player Slots</li>
            <li>1x CPU Thread</li>
            <li>3 GB DDR5 RAM</li>
            <li>150 GB NVMe SSD</li>
            <li>17 Tbit DDoS Protection</li>
            <li>Voice Plugin Support</li>
            <li>Pterodactyl Panel</li>
          </ul>
          <a href="/order.php?plan=SAMP+III&price=30" class="btn btn-primary btn-block">Order Now</a>
        </div>
        <div class="pricing-card">
          <div class="pricing-header">
            <h3>SAMP IV <span style="font-size:13px;color:var(--text-muted);font-weight:400;">MAX</span></h3>
            <div class="price"><span class="currency">DH</span>45<span class="period">/mo</span></div>
          </div>
          <ul class="pricing-features">
            <li><strong>1000</strong> Player Slots</li>
            <li>2x CPU Threads</li>
            <li>4 GB DDR5 RAM</li>
            <li>200 GB NVMe SSD</li>
            <li>17 Tbit DDoS Protection</li>
            <li>Voice Plugin Support</li>
            <li>Pterodactyl Panel</li>
          </ul>
          <a href="/order.php?plan=SAMP+IV+MAX&price=45" class="btn btn-primary btn-block">Order Now</a>
        </div>
      </div>
      <p class="pricing-note">* Xvilo Hosting utilise des processeurs AMD Ryzen 9950X. Selon la disponibilité, ton serveur peut être livré avec un AMD Ryzen 7950X.</p>
    </div>
  </section>

  <section id="features" class="section features">
    <div class="container">
      <h2 class="section-title">Pourquoi <span class="gradient-text">Xvilo</span> ?</h2>
      <p class="section-sub">Tout ce qu'il faut pour lancer et gérer ton serveur SA-MP.</p>
      <div class="features-grid">
        <div class="feature-card">
          <div class="feature-icon">⚡</div>
          <h3>Installation Instantanée</h3>
          <p>Ton serveur est déployé automatiquement après approbation du paiement.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">🛡️</div>
          <h3>Protection DDoS</h3>
          <p>Protection DDoS 17 Tbit incluse sur tous les plans.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">🎙️</div>
          <h3>Plugin Vocal</h3>
          <p>Support du plugin vocal SA-MP intégré. Aucune configuration supplémentaire.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">📂</div>
          <h3>Panel Pterodactyl</h3>
          <p>Contrôle total via Pterodactyl — fichiers, console, base de données.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">🇪🇺</div>
          <h3>Localisation UE</h3>
          <p>Serveurs hébergés en Allemagne pour une faible latence en Europe et MENA.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">💬</div>
          <h3>Support 24/7</h3>
          <p>Besoin d'aide ? Rejoins notre Discord, on répond en quelques minutes.</p>
        </div>
      </div>
    </div>
  </section>

  <section id="faq" class="section faq">
    <div class="container">
      <h2 class="section-title">Foire Aux <span class="gradient-text">Questions</span></h2>
      <div class="faq-list">
        <div class="faq-item">
          <div class="faq-question">Quel matériel utilisez-vous ?</div>
          <div class="faq-answer">Nous utilisons les processeurs AMD Ryzen 9950X. Selon la disponibilité, votre serveur peut être livré avec un AMD Ryzen 7950X.</div>
        </div>
        <div class="faq-item">
          <div class="faq-question">Comment payer ?</div>
          <div class="faq-answer">On accepte Inwi et Orange Money. Tu choisis ton plan, remplis le formulaire, on t'envoie un code de paiement, tu paies et tu uploads la capture d'écran. Je vérifie et j'approuve.</div>
        </div>
        <div class="faq-item">
          <div class="faq-question">C'est rapide ?</div>
          <div class="faq-answer">Une fois le paiement approuvé, ton serveur est créé via Pterodactyl. Tu reçois tes accès sous 1h à 4h.</div>
        </div>
        <div class="faq-item">
          <div class="faq-question">J'aurai accès à quoi ?</div>
          <div class="faq-answer">Accès complet au panel Pterodactyl : gestionnaire de fichiers, console, base de données et support du plugin vocal.</div>
        </div>
      </div>
    </div>
  </section>

<?php require __DIR__ . '/../templates/footer.php'; ?>

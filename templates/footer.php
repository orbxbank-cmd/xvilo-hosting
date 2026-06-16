  <!-- Admin Lock -->
  <div class="admin-lock" id="adminLock" title="Admin Panel">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
  </div>

  <!-- Admin PIN Modal -->
  <div class="modal-overlay" id="adminModal">
    <div class="modal">
      <h3>Admin Access</h3>
      <p>Entrez le code PIN pour accéder au panel.</p>
      <form action="/admin/" method="POST">
        <input type="password" name="code" placeholder="Code PIN" required autocomplete="off" />
        <button type="submit" class="btn btn-primary btn-block">Se connecter</button>
      </form>
      <button class="modal-close" id="modalClose">&times;</button>
    </div>
  </div>

  <footer class="footer">
    <div class="container footer-inner">
      <div class="footer-brand">
        <img src="https://i.postimg.cc/hGHBgg77/In-Shot-20260616-023140548.jpg" alt="Xvilo Hosting" style="height:28px;width:auto;margin-bottom:12px;">
        <p>Hébergement SA-MP premium.</p>
      </div>
      <div class="footer-links">
        <h4>Liens</h4>
        <ul>
          <li><a href="/#pricing">Plans</a></li>
          <li><a href="/#features">Features</a></li>
          <li><a href="/#faq">FAQ</a></li>
        </ul>
      </div>
      <div class="footer-links">
        <h4>Support</h4>
        <ul>
          <li><a href="https://discord.gg/E9HjMePMsalBUEHScBfiw4">Discord</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <p>&copy; 2026 Xvilo Hosting. Tous droits réservés.</p>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
  <script src="/script.js"></script>
</body>
</html>

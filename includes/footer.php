  <!-- premium footer -->
  <footer class="premium-footer">
    <div class="container footer-grid">
      
      <div class="footer-brand">
        <h3 class="footer-logo"><i class="fa-solid fa-compass"></i> Mawanella <span>Tourism</span></h3>
        <p class="footer-desc">Discover the hidden gems, majestic waterfalls, and historic temples of Mawanella. Plan your perfect day trip with ease.</p>
        <div class="social-icons">
          <a href="https://github.com/mt-abdulla-it" target="_blank" title="GitHub"><i class="fa-brands fa-github"></i></a>
          <a href="https://www.linkedin.com/in/mt-abdulla-it/" target="_blank" title="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
        </div>
      </div>

      <div class="footer-nav">
        <h4>Quick Links</h4>
        <div class="footer-links-col">
          <a href="index.php"><i class="fa-solid fa-chevron-right"></i> Attractions</a>
          <a href="planner.php"><i class="fa-solid fa-chevron-right"></i> Day Plan</a>
          <a href="#" class="open-emergency-link" style="color:#ef4444;"><i class="fa-solid fa-chevron-right"></i> Emergency Contacts</a>
          <a href="admin/login.php"><i class="fa-solid fa-chevron-right"></i> Admin Login</a>
        </div>
      </div>

      <div class="footer-contact">
        <h4>Project Details</h4>
        <p><i class="fa-solid fa-graduation-cap"></i> BIT Coursework Project</p>
        <p><i class="fa-solid fa-code"></i> Developed by <strong>Abdulla MT</strong></p>
        <p><i class="fa-solid fa-id-badge"></i> Reg No: E2410182</p>
      </div>

    </div>
    
    <div class="footer-bottom">
      <div class="container flex-between mobile-col">
        <p>&copy; <?= date('Y'); ?> Mawanella Tourism Information System. All rights reserved.</p>
        <p class="made-with">Made with <i class="fa-solid fa-heart" style="color: #ef4444;"></i> for ITE2953</p>
      </div>
    </div>
  </footer>

  <!-- Scroll to Top Button -->
  <button id="scrollTopBtn" title="Go to top"><i class="fa-solid fa-arrow-up"></i></button>

  <!-- Toast Container -->
  <div id="toast-container"></div>

  <!-- Emergency Contacts Modal -->
  <div id="emergencyModal" class="modal-backdrop">
    <div class="modal-content">
      <div class="modal-header">
        <h3><i class="fa-solid fa-shield-heart"></i> Mawanella Tourist Emergency & Safety Info</h3>
        <button id="closeEmergencyModal" class="modal-close-btn">&times;</button>
      </div>
      <div class="modal-body">
        <div class="emergency-grid">
          <div class="emergency-card">
            <h4><i class="fa-solid fa-building-shield" style="color: #3b82f6;"></i> Mawanella Police Station</h4>
            <p>035-224-6222</p>
            <small style="color: var(--text-muted);">Emergency Line: 119</small>
          </div>
          <div class="emergency-card">
            <h4><i class="fa-solid fa-hospital" style="color: #ef4444;"></i> Mawanella Base Hospital</h4>
            <p>035-224-6261</p>
            <small style="color: var(--text-muted);">Ambulance (Suwa Seriya): 1990</small>
          </div>
          <div class="emergency-card">
            <h4><i class="fa-solid fa-user-shield" style="color: #10b981;"></i> Tourist Police Hotline</h4>
            <p>1912</p>
            <small style="color: var(--text-muted);">24/7 Islandwide Tourist Helpline</small>
          </div>
          <div class="emergency-card">
            <h4><i class="fa-solid fa-cloud-showers-heavy" style="color: #f59e0b;"></i> Disaster Management (DMC)</h4>
            <p>117</p>
            <small style="color: var(--text-muted);">Weather & Flash Flood Alerts</small>
          </div>
        </div>

        <div style="background: var(--input-bg); border-left: 4px solid var(--primary-color); padding: 12px 16px; border-radius: 6px; font-size: 0.88rem; color: var(--text-main);">
          <strong><i class="fa-solid fa-lightbulb"></i> Local Safety Tips for Mawanella Visitors:</strong>
          <ul style="margin: 8px 0 0 18px; padding: 0; line-height: 1.5;">
            <li><strong>Waterfalls (Asupini Ella / Bo Ella):</strong> Do not climb wet rocks or swim during heavy rain. Currents can rise rapidly.</li>
            <li><strong>Cave Temples:</strong> Dress respectfully (cover shoulders & knees) and remove shoes before entering shrines.</li>
            <li><strong>Wildlife (Pinnawala):</strong> Always keep a safe distance from elephants and follow handler instructions.</li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <script src="assets/js/main.js"></script>
</body>
</html>

document.addEventListener('DOMContentLoaded', () => {
  // 1. Preloader
  const preloader = document.getElementById('preloader');
  if (preloader) {
    const hidePreloader = () => {
      preloader.style.opacity = '0';
      setTimeout(() => {
        preloader.style.display = 'none';
      }, 500);
    };

    if (document.readyState === 'complete') {
      hidePreloader();
    } else {
      window.addEventListener('load', hidePreloader);
    }
  }

  // 1b. Mobile Hamburger Toggle
  const menuToggle = document.getElementById('menu-toggle');
  const navLinks = document.getElementById('nav-links');
  if (menuToggle && navLinks) {
    menuToggle.addEventListener('click', () => {
      navLinks.classList.toggle('open');
      menuToggle.classList.toggle('active');
    });

    // close menu when a link is clicked
    navLinks.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', () => {
        navLinks.classList.remove('open');
        menuToggle.classList.remove('active');
      });
    });
  }

  // 2. Dark Mode Toggle
  const themeToggle = document.getElementById('theme-toggle');
  const currentTheme = localStorage.getItem('theme');
  if (currentTheme) {
    document.documentElement.setAttribute('data-theme', currentTheme);
    if (currentTheme === 'dark' && themeToggle) {
      themeToggle.innerHTML = '<i class="fa-solid fa-sun"></i>';
    }
  }

  if (themeToggle) {
    themeToggle.addEventListener('click', () => {
      let theme = document.documentElement.getAttribute('data-theme');
      if (theme === 'dark') {
        document.documentElement.removeAttribute('data-theme');
        localStorage.setItem('theme', 'light');
        themeToggle.innerHTML = '<i class="fa-solid fa-moon"></i>';
      } else {
        document.documentElement.setAttribute('data-theme', 'dark');
        localStorage.setItem('theme', 'dark');
        themeToggle.innerHTML = '<i class="fa-solid fa-sun"></i>';
      }
    });
  }

  // 3. Scroll Reveal Animations (Intersection Observer)
  const revealOptions = {
    root: null,
    rootMargin: '0px',
    threshold: 0.1
  };
  
  const revealObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-visible');
        revealObserver.unobserve(entry.target); // Stop observing once revealed
      }
    });
  }, revealOptions);

  // Observe all elements that have the animate-fade-up class
  document.querySelectorAll('.animate-fade-up').forEach(el => {
    revealObserver.observe(el);
  });

  // 3. Scroll to Top Button
  const scrollTopBtn = document.getElementById('scrollTopBtn');
  if (scrollTopBtn) {
    window.addEventListener('scroll', () => {
      if (window.scrollY > 300) {
        scrollTopBtn.classList.add('show');
      } else {
        scrollTopBtn.classList.remove('show');
      }
    });

    scrollTopBtn.addEventListener('click', () => {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    });
  }

  // 4. Toast Notification System
  window.showToast = function(message, type = 'success') {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    
    let iconClass = 'fa-check-circle';
    if (type === 'error') iconClass = 'fa-exclamation-circle';
    if (type === 'warning') iconClass = 'fa-triangle-exclamation';

    // Use textContent to prevent XSS
    const iconDiv = document.createElement('div');
    iconDiv.className = 'toast-icon';
    iconDiv.innerHTML = `<i class="fa-solid ${iconClass}"></i>`;
    
    const messageDiv = document.createElement('div');
    messageDiv.className = 'toast-message';
    messageDiv.textContent = message;

    toast.appendChild(iconDiv);
    toast.appendChild(messageDiv);

    container.appendChild(toast);

    // Slide in
    setTimeout(() => toast.classList.add('show'), 10);

    // Slide out and remove
    setTimeout(() => {
      toast.classList.remove('show');
      setTimeout(() => toast.remove(), 300);
    }, 3000);
  };

  // Override Add to Plan buttons to use AJAX & Toast
  const addButtons = document.querySelectorAll('.btn-add-plan');
  addButtons.forEach(btn => {
    // If it currently has onclick, we override it or just remove onclick in PHP.
    // Let's attach our own listener
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      const placeId = this.getAttribute('data-id');
      const placeName = this.getAttribute('data-name');
      
      fetch(`planner.php?action=add&id=${placeId}&json=1`)
        .then(response => response.json())
        .then(data => {
          if (data.status === 'success') {
            showToast(`Added ${placeName} to your Day Plan!`, 'success');
            // update badge if exists
            const badge = document.getElementById('planner-badge');
            if (badge) badge.innerText = data.count;
          } else if (data.status === 'exists') {
            showToast(`${placeName} is already in your Day Plan.`, 'warning');
          } else {
            showToast('Failed to add to Day Plan.', 'error');
          }
        })
        .catch(err => {
          console.error(err);
          showToast('Error occurred.', 'error');
        });
    });
  });

  // Admin Sidebar / Drawer Toggle for Tablet and Mobile
  const adminDrawerToggle = document.getElementById('admin-drawer-toggle');
  const adminSidebar = document.getElementById('admin-sidebar');
  const adminSidebarOverlay = document.getElementById('admin-sidebar-overlay');
  
  if (adminDrawerToggle && adminSidebar) {
    adminDrawerToggle.addEventListener('click', () => {
      adminSidebar.classList.toggle('active');
      if (adminSidebarOverlay) adminSidebarOverlay.classList.toggle('active');
    });

    if (adminSidebarOverlay) {
      adminSidebarOverlay.addEventListener('click', () => {
        adminSidebar.classList.remove('active');
        adminSidebarOverlay.classList.remove('active');
      });
    }
  }

  // Emergency Modal Handlers
  const emergencyModal = document.getElementById('emergencyModal');
  const openEmergencyBtn = document.getElementById('openEmergencyModal');
  const closeEmergencyBtn = document.getElementById('closeEmergencyModal');
  const openEmergencyLinks = document.querySelectorAll('.open-emergency-link');

  function showEmergencyModal(e) {
    if (e) e.preventDefault();
    if (emergencyModal) emergencyModal.classList.add('active');
  }

  function hideEmergencyModal() {
    if (emergencyModal) emergencyModal.classList.remove('active');
  }

  if (openEmergencyBtn) openEmergencyBtn.addEventListener('click', showEmergencyModal);
  openEmergencyLinks.forEach(link => link.addEventListener('click', showEmergencyModal));
  if (closeEmergencyBtn) closeEmergencyBtn.addEventListener('click', hideEmergencyModal);
  if (emergencyModal) {
    emergencyModal.addEventListener('click', (e) => {
      if (e.target === emergencyModal) hideEmergencyModal();
    });
  }
});


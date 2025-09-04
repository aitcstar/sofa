// ===== NAV BAR FUNCTIONALITY =====
document.addEventListener('DOMContentLoaded', function () {
  // Mobile Navigation Elements
  const mobileMenuToggle = document.getElementById('mobileMenuToggle');
  const navMobileOverlay = document.getElementById('navMobileOverlay');
  const navMobileDrawer = document.getElementById('navMobileDrawer');
  const navMobileClose = document.getElementById('navMobileClose');
  const navMobileLanguageOptions = document.querySelectorAll('.nav-mobile-language-option');

  console.log('Mobile nav elements found:', {
    mobileMenuToggle: !!mobileMenuToggle,
    navMobileOverlay: !!navMobileOverlay,
    navMobileDrawer: !!navMobileDrawer,
    navMobileClose: !!navMobileClose,
    navMobileLanguageOptions: navMobileLanguageOptions.length
  });

  // Mobile Navigation Functions
  function openMobileNav() {
    console.log('Opening mobile nav...');
    navMobileOverlay.style.display = 'block';
    navMobileDrawer.style.display = 'flex';

    // Trigger animation after display is set
    setTimeout(() => {
      navMobileOverlay.classList.add('active');
      navMobileDrawer.classList.add('active');
    }, 10);

    // Prevent body scroll
    document.body.style.overflow = 'hidden';
  }

  function closeMobileNav() {
    navMobileOverlay.classList.remove('active');
    navMobileDrawer.classList.remove('active');

    // Hide elements after animation
    setTimeout(() => {
      navMobileOverlay.style.display = 'none';
      navMobileDrawer.style.display = 'none';
      document.body.style.overflow = '';
    }, 300);
  }

  // Event Listeners
  console.log('Setting up mobile nav event listeners...');
  console.log('mobileMenuToggle:', mobileMenuToggle);
  console.log('navMobileOverlay:', navMobileOverlay);
  console.log('navMobileDrawer:', navMobileDrawer);
  console.log('navMobileClose:', navMobileClose);
  
  if (mobileMenuToggle) {
    mobileMenuToggle.addEventListener('click', openMobileNav);
    console.log('Added click listener to mobile menu toggle');
  } else {
    console.error('mobileMenuToggle not found!');
  }
  
  if (navMobileClose) {
    navMobileClose.addEventListener('click', closeMobileNav);
    console.log('Added click listener to mobile nav close');
  } else {
    console.error('navMobileClose not found!');
  }
  
  if (navMobileOverlay) {
    navMobileOverlay.addEventListener('click', closeMobileNav);
    console.log('Added click listener to mobile nav overlay');
  } else {
    console.error('navMobileOverlay not found!');
  }

  // Mobile language selection
  navMobileLanguageOptions.forEach(option => {
    option.addEventListener('click', (e) => {
      const language = option.getAttribute('data-language');
      const radio = option.querySelector('input[type="radio"]');
      
      // Update radio buttons
      document.querySelectorAll('input[name="mobile-language"]').forEach(r => r.checked = false);
      radio.checked = true;
      
      // Update main language dropdown
      const mainRadio = document.getElementById(language + 'Radio');
      if (mainRadio) {
        mainRadio.checked = true;
      }
      
      // Change page direction
      if (window.LanguageDropdown && window.LanguageDropdown.changeDirection) {
        window.LanguageDropdown.changeDirection(language);
      }
      
      // Close mobile nav
      closeMobileNav();
    });
  });

  // Close on escape key
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      if (navMobileDrawer.classList.contains('active')) {
        closeMobileNav();
      }
    }
  });

  // Prevent mobile nav drawer close when clicking inside it
  if (navMobileDrawer) {
    navMobileDrawer.addEventListener('click', function (e) {
      e.stopPropagation();
    });
  }

  // Set active navigation item based on current page
  function setActiveNavItem() {
    const currentPath = window.location.pathname;
    const navItems = document.querySelectorAll('.nav-mobile-nav-item');
    
    navItems.forEach(item => {
      const link = item.querySelector('.nav-mobile-nav-link');
      if (link) {
        const href = link.getAttribute('href');
        // Remove active class from all items
        item.classList.remove('active');
        
        // Add active class to current page item
        if (currentPath === href || 
            (currentPath === '/' && href === '/') ||
            (currentPath.includes('/pages/') && href.includes(currentPath.split('/').pop()))) {
          item.classList.add('active');
        }
      }
    });
  }

  // Set active nav item on page load
  setActiveNavItem();
});

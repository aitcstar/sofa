// ===== LANGUAGE DROPDOWN FUNCTIONALITY =====
console.log("Language.js file loaded successfully at:", new Date().toLocaleTimeString());

// Language dropdown elements
let languageDropdown;
let languageOptions;
let languageRadios;

// Initialize language dropdown functionality
function initLanguageDropdown() {
  // Get language dropdown elements
  languageDropdown = document.getElementById("languageDropdown");
  languageOptions = document.querySelectorAll(".language-option");
  languageRadios = document.querySelectorAll('input[name="language"]');

  // Ensure language dropdown starts closed
  const dropdownMenu = document.querySelector(".language-dropdown-menu");
  if (dropdownMenu) {
    dropdownMenu.classList.remove("show");
    dropdownMenu.style.display = "none";
  }

  // Set up event listeners
  setupLanguageEventListeners();
  console.log("Language dropdown initialized");
}

// Set up all language dropdown event listeners
function setupLanguageEventListeners() {
  // Handle language dropdown toggle
  if (languageDropdown) {
    languageDropdown.addEventListener("click", handleLanguageDropdownToggle);
  }

  // Language option click events
  languageOptions.forEach((option) => {
    option.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      const language = option.getAttribute("data-language");
      handleLanguageSelection(language);
    });
  });

  // Radio button change events
  languageRadios.forEach((radio) => {
    radio.addEventListener("change", (e) => {
      e.stopPropagation();
      const language = e.target.id.replace("Radio", "");
      handleLanguageSelection(language);
    });

    // Prevent event bubbling when clicking on radio
    radio.addEventListener("click", (e) => {
      e.stopPropagation();
    });
  });

  // Close dropdown when clicking outside
  document.addEventListener("click", handleOutsideClick);

  // Close dropdown on escape key
  document.addEventListener("keydown", handleEscapeKey);
}

// Handle language dropdown toggle
function handleLanguageDropdownToggle(e) {
  e.preventDefault();
  e.stopPropagation();

  const dropdownMenu = document.querySelector(".language-dropdown-menu");
  const isVisible = dropdownMenu && dropdownMenu.classList.contains("show");

  if (isVisible) {
    // If dropdown is visible, close it
    closeLanguageDropdown();
  } else {
    // If not visible, manually show it
    showLanguageDropdown();
  }
}

// Handle language selection
function handleLanguageSelection(selectedLanguage) {
  console.log("Language selected:", selectedLanguage);
  
  // Update radio buttons
  languageRadios.forEach((radio) => {
    radio.checked = radio.id === selectedLanguage + "Radio";
  });

  // Close dropdown after selection
  closeLanguageDropdown();
  
  // Change page direction only
  changePageLanguage(selectedLanguage);
}

// Change page direction only (no text translation)
function changePageLanguage(language) {
  const html = document.documentElement;
  const body = document.body;
  
  if (language === 'ar') {
    // Arabic - RTL direction
    html.setAttribute('lang', 'ar');
    html.setAttribute('dir', 'rtl');
    body.classList.add('rtl');
    body.classList.remove('ltr');
    console.log("Direction changed to RTL (Arabic)");
    
  } else if (language === 'en') {
    // English - LTR direction
    html.setAttribute('lang', 'en');
    html.setAttribute('dir', 'ltr');
    body.classList.add('ltr');
    body.classList.remove('rtl');
    console.log("Direction changed to LTR (English)");
  }
  
  // Save language preference
  localStorage.setItem('selectedLanguage', language);
}









// Function to close language dropdown
function closeLanguageDropdown() {
  const dropdown = bootstrap.Dropdown.getInstance(languageDropdown);
  if (dropdown) {
    dropdown.hide();
  } else {
    // Fallback: manually hide the dropdown
    const dropdownMenu = document.querySelector(".language-dropdown-menu");
    if (dropdownMenu) {
      dropdownMenu.classList.remove("show");
      dropdownMenu.style.display = "none";
    }
    // Remove show class from button
    const dropdownButton = document.getElementById("languageDropdown");
    if (dropdownButton) {
      dropdownButton.setAttribute("aria-expanded", "false");
    }
  }
}

// Function to show language dropdown
function showLanguageDropdown() {
  const dropdown = bootstrap.Dropdown.getInstance(languageDropdown);
  if (dropdown) {
    dropdown.show();
  } else {
    // Fallback: manually show the dropdown
    const dropdownMenu = document.querySelector(".language-dropdown-menu");
    if (dropdownMenu) {
      dropdownMenu.classList.add("show");
      dropdownMenu.style.display = "flex";
    }
    // Add show class to button
    const dropdownButton = document.getElementById("languageDropdown");
    if (dropdownButton) {
      dropdownButton.setAttribute("aria-expanded", "true");
    }
  }
}

// Handle clicking outside dropdown
function handleOutsideClick(e) {
  const dropdown = document.querySelector(".language-dropdown");
  const dropdownMenu = document.querySelector(".language-dropdown-menu");

  // Check if click is outside dropdown and dropdown is currently shown
  if (
    dropdown &&
    dropdownMenu &&
    !dropdown.contains(e.target) &&
    dropdownMenu.classList.contains("show")
  ) {
    closeLanguageDropdown();
  }
}

// Handle escape key press
function handleEscapeKey(e) {
  if (e.key === "Escape") {
    // Close language dropdown
    const dropdownMenu = document.querySelector(".language-dropdown-menu");
    if (dropdownMenu && dropdownMenu.classList.contains("show")) {
      closeLanguageDropdown();
    }
  }
}

// Add smooth animations to dropdown
function setupDropdownAnimations() {
  const dropdownMenu = document.querySelector(".language-dropdown-menu");
  if (dropdownMenu) {
    dropdownMenu.addEventListener("show.bs.dropdown", function () {
      this.style.opacity = "0";
      this.style.transform = "translateY(-10px)";

      setTimeout(() => {
        this.style.opacity = "1";
        this.style.transform = "translateY(0)";
      }, 10);
    });

    // Ensure dropdown can be opened
    dropdownMenu.addEventListener("hidden.bs.dropdown", function () {
      this.style.display = "";
      this.style.opacity = "";
      this.style.transform = "";
    });
  }
}

// Initialize when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
  initLanguageDropdown();
  setupDropdownAnimations();
  loadSavedLanguage();
});

// Also try to initialize immediately if DOM is already loaded
if (document.readyState === 'loading') {
  // DOM still loading, event listener will handle it
} else {
  // DOM already loaded, initialize immediately
  initLanguageDropdown();
  setupDropdownAnimations();
  loadSavedLanguage();
}

// Load saved language preference
function loadSavedLanguage() {
  const savedLanguage = localStorage.getItem('selectedLanguage');
  
  if (savedLanguage) {
    // Update radio button
    const radio = document.getElementById(savedLanguage + 'Radio');
    if (radio) {
      radio.checked = true;
    }
    
    // Apply language direction
    changePageLanguage(savedLanguage);
    console.log("Loaded saved language preference:", savedLanguage);
  }
}

// Export functions for external use
window.LanguageDropdown = {
  init: initLanguageDropdown,
  show: showLanguageDropdown,
  close: closeLanguageDropdown,
  select: handleLanguageSelection,
  changeDirection: changePageLanguage
};

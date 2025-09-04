// Main JavaScript Functions

// Initialize when document is ready
$(document).ready(function() {
    // Add a small delay to ensure all elements are properly loaded
    setTimeout(function() {
        initializeTestimonialCarouselHomepage();
        initializeTestimonialCarouselCategoryDetails();
        initializeScrollToTop();
        initializeLanguageSelector();
        initializeFormValidation();
        initializeModalHandlers();
    }, 100);
});

// Also initialize when window is fully loaded
$(window).on('load', function() {
    console.log("Window loaded, re-initializing carousel...");
    setTimeout(function() {
        initializeTestimonialCarouselHomepage();
        initializeTestimonialCarouselCategoryDetails();
    }, 200);
});

// Carousel Initialization
function initializeTestimonialCarouselHomepage() {
    if ($("#testimonial-carousel-homepage").length > 0) {
        var owl = $("#testimonial-carousel-homepage").owlCarousel({
            rtl: true,
            loop: true,
            margin: 0,
            dots: true,
            autoplay: true,
            autoplayTimeout: 4000,
            autoplayHoverPause: true,
            slideBy: 3, // هنا السلايد هيتحرك 3 سلايدات مرة واحدة
            responsive: {
                0: { items: 1 },
                576: { items: 1 },
                768: { items: 2 },
                992: { items: 3 },
                1200: { items: 3 }
            }
        });

        // تحديث النقاط مع السلايد
        owl.on('changed.owl.carousel', function(event) {
            var totalItems = event.item.count; // عدد السلايدات الحقيقية
            var slideBy = 3; // عدد السلايدات اللي بتتحرك في كل مرة
            var totalDots = Math.ceil(totalItems / slideBy); // عدد النقاط الكلي
            var current = Math.floor(event.item.index / slideBy) % totalDots; // النقطة الحالية

            $(".owl-dot").removeClass("active");
            $(".owl-dot").eq(current).addClass("active");
        });

    } else {
        console.log("No testimonial-carousel-homepage elements found");
    }
}
// Testimonial Carousel for Category Details
function initializeTestimonialCarouselCategoryDetails() {
    if ($("#testimonial-carousel-category-details").length > 0) {
        $("#testimonial-carousel-category-details").owlCarousel({
            rtl: true,
            loop: true,
            margin: 0,
            dots: true,
            autoplay: true,
            autoplayTimeout: 5000,
            responsive: {
                0: {
                    items: 1,
                },
                576: {
                    items: 1,
                },
                768: {
                    items: 2,
                },
                992: {
                    items: 3,
                },
                1200: {
                    items: 3.5,
                }
            }
        });
    } else {
        console.log("No testimonial-carousel-category-details element found");
    }
}

// Scroll to Top Functionality
function initializeScrollToTop() {
    const scrollBtn = document.getElementById("scrollBtn");

    window.onscroll = function() {
        if (window.scrollY > 300) {
            scrollBtn.style.display = "block";
        } else {
            scrollBtn.style.display = "none";
        }
    };
}

function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Language Selector - Now handled by LanguageManager
function initializeLanguageSelector() {
    // Language switching is now handled by LanguageManager
    // This function is kept for any additional language-related functionality

    // Close dropdown after language selection
    $('#arabicCheckbox, #englishCheckbox').on('change', function() {
        setTimeout(() => {
            $('.dropdown-menu').removeClass('show');
        }, 300);
    });
}

// Form Validation
function initializeFormValidation() {
    $('.form-section').on('submit', function(e) {
        e.preventDefault();

        const formData = {
            unit: $('input[name="unit"]:checked').val(),
            finish: $('input[name="finish"]:checked').val(),
            design: $('input[name="design"]:checked').val(),
            electric: $('input[name="electric"]:checked').val()
        };

        if (validateForm(formData)) {
            submitForm(formData);
        }
    });
}

function validateForm(data) {
    let isValid = true;
    const requiredFields = ['unit', 'finish', 'design', 'electric'];

    requiredFields.forEach(field => {
        if (!data[field]) {
            isValid = false;
            showError(`يرجى اختيار ${getFieldName(field)}`);
        }
    });

    return isValid;
}

function getFieldName(field) {
    const names = {
        unit: 'نوع الوحدة',
        finish: 'نمط التشطيب',
        design: 'التصميم الداخلي',
        electric: 'اختيار الألوان'
    };
    return names[field] || field;
}

function showError(message) {
    // Create and show error message
    const errorDiv = $('<div class="alert alert-danger mt-3">' + message + '</div>');
    $('.form-section').append(errorDiv);

    setTimeout(() => {
        errorDiv.fadeOut(() => errorDiv.remove());
    }, 3000);
}

function submitForm(data) {
    // Show loading state
    const submitBtn = $('.btn-submit');
    const originalText = submitBtn.text();
    submitBtn.text('جاري الإرسال...').prop('disabled', true);

    // Simulate API call
    setTimeout(() => {
        submitBtn.text(originalText).prop('disabled', false);
        showSuccess('تم إرسال الطلب بنجاح! سنتواصل معك قريباً.');

        // Reset form
        $('.form-section')[0].reset();
    }, 2000);
}

function showSuccess(message) {
    const successDiv = $('<div class="alert alert-success mt-3">' + message + '</div>');
    $('.form-section').append(successDiv);

    setTimeout(() => {
        successDiv.fadeOut(() => successDiv.remove());
    }, 5000);
}

// Modal Handlers
function initializeModalHandlers() {
    // Handle modal form submissions
    $('#exampleModal form').on('submit', function(e) {
        e.preventDefault();

        const formType = $(this).closest('.tab-pane').attr('id');
        const formData = new FormData(this);

        if (formType === 'home') {
            handleLogin(formData);
        } else if (formType === 'profile') {
            handleRegistration(formData);
        }
    });
}

function handleLogin(formData) {
    const phone = formData.get('phone');

    if (!phone) {
        showModalError('يرجى إدخال رقم الهاتف');
        return;
    }

    // Simulate login process
    showModalSuccess('تم إرسال رمز التحقق إلى هاتفك');
}

function handleRegistration(formData) {
    const name = formData.get('name');
    const phone = formData.get('phone');
    const email = formData.get('email');

    if (!name || !phone) {
        showModalError('يرجى إدخال الاسم ورقم الهاتف');
        return;
    }

    // Simulate registration process
    showModalSuccess('تم إنشاء الحساب بنجاح!');
}

function showModalError(message) {
    const errorDiv = $('<div class="alert alert-danger mt-3">' + message + '</div>');
    $('#exampleModal .modal-body').append(errorDiv);

    setTimeout(() => {
        errorDiv.fadeOut(() => errorDiv.remove());
    }, 3000);
}

function showModalSuccess(message) {
    const successDiv = $('<div class="alert alert-success mt-3">' + message + '</div>');
    $('#exampleModal .modal-body').append(successDiv);

    setTimeout(() => {
        successDiv.fadeOut(() => successDiv.remove());
        $('#exampleModal').modal('hide');
    }, 2000);
}

// Utility Functions
function formatPrice(price) {
    return new Intl.NumberFormat('ar-SA', {
        style: 'currency',
        currency: 'SAR'
    }).format(price);
}

function animateOnScroll() {
    $('.package-card, .step, .testimonial-card').each(function() {
        const elementTop = $(this).offset().top;
        const elementBottom = elementTop + $(this).outerHeight();
        const viewportTop = $(window).scrollTop();
        const viewportBottom = viewportTop + $(window).height();

        if (elementBottom > viewportTop && elementTop < viewportBottom) {
            $(this).addClass('animate-in');
        }
    });
}

// Initialize scroll animations
$(window).on('scroll', animateOnScroll);
$(document).ready(animateOnScroll);

// Export functions for global use
window.scrollToTop = scrollToTop;
window.formatPrice = formatPrice;

// ===== FILTER FUNCTIONALITY =====
document.addEventListener('DOMContentLoaded', function() {
  // Filter functionality for categories page
  const filterCheckboxes = document.querySelectorAll('.filter-checkbox');

  filterCheckboxes.forEach(checkbox => {
    checkbox.addEventListener('click', function() {
      // Toggle checked state
      this.classList.toggle('checked');

      // Get filter type and value
      const filterType = this.dataset.filter;
      const filterValue = this.dataset.value;

      console.log(`Filter: ${filterType} = ${filterValue}, Checked: ${this.classList.contains('checked')}`);

      // Here you can add logic to filter the packages
      // For now, we'll just log the filter changes
    });
  });

  // Add hover effect for filter options
  const filterOptions = document.querySelectorAll('.filter-option');

  filterOptions.forEach(option => {
    option.addEventListener('mouseenter', function() {
      this.style.backgroundColor = 'var(--surface-dropbox)';
    });

    option.addEventListener('mouseleave', function() {
      this.style.backgroundColor = 'transparent';
    });
  });
});

// ===== TAB FUNCTIONALITY =====
document.addEventListener('DOMContentLoaded', function() {
  // Tab functionality
  const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
  const tabPanes = document.querySelectorAll('.tab-pane');

  tabButtons.forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();

      // Remove active class from all buttons and panes
      tabButtons.forEach(btn => btn.classList.remove('active'));
      tabPanes.forEach(pane => {
        pane.classList.remove('show', 'active');
        pane.style.display = 'none';
      });

      // Add active class to clicked button
      this.classList.add('active');

      // Show target pane
      const targetId = this.getAttribute('data-bs-target');
      const targetPane = document.querySelector(targetId);

      if (targetPane) {
        targetPane.style.display = 'flex';
        targetPane.classList.add('show', 'active');
      }
    });
  });

  // Initialize first tab
  const firstTab = document.querySelector('.tab-pane.show.active');
  if (firstTab) {
    firstTab.style.display = 'flex';
  }
});

// ===== COUNTRY DROPDOWN FUNCTIONALITY =====
document.addEventListener('DOMContentLoaded', function() {
  // Country dropdown functionality
  const countryDropdown = document.querySelector('.country-select');
  const selectedFlag = document.getElementById('selected-flag');
  const selectedCode = document.getElementById('selected-code');
  const dropdownItems = document.querySelectorAll('.dropdown-item[data-flag]');
  const searchInput = document.querySelector('.dropdown-menu .input-search');
  const dropdownIcon = document.querySelector('.dropdown-icon');

  // Handle dropdown open/close animation
  if (countryDropdown && dropdownIcon) {
    countryDropdown.addEventListener('click', function() {
      const isExpanded = this.getAttribute('aria-expanded') === 'true';

      if (isExpanded) {
        // Closing dropdown
        dropdownIcon.style.transform = 'rotate(0deg)';
      } else {
        // Opening dropdown
        dropdownIcon.style.transform = 'rotate(180deg)';
      }
    });

    // Reset icon when dropdown is closed by clicking outside
    document.addEventListener('click', function(e) {
      if (!countryDropdown.contains(e.target) && !e.target.closest('.dropdown-menu')) {
        dropdownIcon.style.transform = 'rotate(0deg)';
      }
    });
  }

  // Handle country selection
  dropdownItems.forEach(item => {
    item.addEventListener('click', function(e) {
      e.preventDefault();

      const flag = this.getAttribute('data-flag');
      const code = this.getAttribute('data-code');

      // Update selected country
      selectedFlag.className = `flag fi fi-${flag}`;
      selectedCode.textContent = code;

      // Reset dropdown icon
      if (dropdownIcon) {
        dropdownIcon.style.transform = 'rotate(0deg)';
      }

      // Close dropdown
      const dropdown = this.closest('.dropdown-menu');
      if (dropdown) {
        const bsDropdown = bootstrap.Dropdown.getInstance(countryDropdown);
        if (bsDropdown) {
          bsDropdown.hide();
        }
      }
    });
  });

  // Search functionality
  if (searchInput) {
    searchInput.addEventListener('input', function() {
      const searchTerm = this.value.toLowerCase().trim();
      const items = document.querySelectorAll('.dropdown-item[data-flag]');

      items.forEach(item => {
        const countryName = item.querySelector('.body-2').textContent.toLowerCase();
        const countryCode = item.getAttribute('data-code').toLowerCase();

        if (searchTerm === '' || countryName.includes(searchTerm) || countryCode.includes(searchTerm)) {
          item.style.display = 'flex';
        } else {
          item.style.display = 'none';
        }
      });

      // Show/hide "no results" message
      const visibleItems = document.querySelectorAll('.dropdown-item[data-flag][style*="flex"]');
      let noResultsMsg = document.querySelector('.no-results-message');

      if (visibleItems.length === 0 && searchTerm !== '') {
        if (!noResultsMsg) {
          noResultsMsg = document.createElement('li');
          noResultsMsg.className = 'no-results-message';
          noResultsMsg.innerHTML = '<span class="body-2 text-caption p-3 d-block text-center">لا توجد نتائج</span>';
          searchInput.parentElement.parentElement.appendChild(noResultsMsg);
        }
        noResultsMsg.style.display = 'block';
      } else if (noResultsMsg) {
        noResultsMsg.style.display = 'none';
      }
    });

    // Clear search when dropdown closes
    const dropdown = searchInput.closest('.dropdown-menu');
    if (dropdown) {
      dropdown.addEventListener('hidden.bs.dropdown', function() {
        searchInput.value = '';
        const items = document.querySelectorAll('.dropdown-item[data-flag]');
        items.forEach(item => {
          item.style.display = 'flex';
        });

        const noResultsMsg = document.querySelector('.no-results-message');
        if (noResultsMsg) {
          noResultsMsg.style.display = 'none';
        }

        // Reset dropdown icon
        if (dropdownIcon) {
          dropdownIcon.style.transform = 'rotate(0deg)';
        }
      });
    }
  }
});

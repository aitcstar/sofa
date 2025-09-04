/* ===== SIMPLE COMPONENTS JAVASCRIPT ===== */

// Initialize when document is ready
$(document).ready(function() {
    initSimpleComponents();
});

function initSimpleComponents() {
    // Initialize FAQ
    initFAQ();
    
    // Initialize Package Cards
    initPackageCards();
    
    // Initialize Mobile Menu
    initMobileMenu();
    
    // Initialize Smooth Scrolling
    initSmoothScrolling();
}

// ===== FAQ COMPONENT =====
function initFAQ() {
    $('.faq-question').on('click', function() {
        const $question = $(this);
        const $answer = $question.siblings('.faq-answer');
        
        // Close other FAQs
        $('.faq-question').not($question).removeClass('active');
        $('.faq-answer').not($answer).removeClass('show');
        
        // Toggle current FAQ
        $question.toggleClass('active');
        $answer.toggleClass('show');
    });
    
    // Initialize existing FAQ items
    $('.faq-question').each(function() {
        const icon = $(this).find('.faq-icon');
        if (icon.length === 0) {
            $(this).append('<div class="faq-icon"></div>');
        }
    });
}

// ===== PACKAGE CARDS =====
function initPackageCards() {
    // WhatsApp button
    $('.package-card .btn-custom-primary').on('click', function(e) {
        e.preventDefault();
        const $card = $(this).closest('.package-card');
        const title = $card.find('.sub-heading-3').text();
        const price = $card.find('.heading-h6').text();
        
        // Simple alert for now
        alert(`سيتم إرسال عرض السعر لـ: ${title}\nالسعر: ${price}\nسيتم التواصل معك عبر واتساب قريباً.`);
    });
    
    // Details button
    $('.package-card .btn-custom-secondary').on('click', function(e) {
        e.preventDefault();
        const $card = $(this).closest('.package-card');
        const title = $card.find('.sub-heading-3').text();
        const description = $card.find('.body-3').text();
        
        // Simple alert for now
        alert(`تفاصيل الباكج: ${title}\n\n${description}`);
    });
}

// ===== MOBILE MENU =====
function initMobileMenu() {
    // Toggle mobile menu
    $('.btn[data-bs-toggle="offcanvas"]').on('click', function() {
        const target = $(this).data('bs-target');
        $(target).toggleClass('show');
    });
    
    // Close mobile menu when clicking on links
    $('.offcanvas-body .nav-link').on('click', function() {
        $('.offcanvas').removeClass('show');
    });
    
    // Close mobile menu when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.offcanvas, .btn[data-bs-toggle="offcanvas"]').length) {
            $('.offcanvas').removeClass('show');
        }
    });
}

// ===== SMOOTH SCROLLING =====
function initSmoothScrolling() {
    // Smooth scroll for anchor links
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        const target = $(this.getAttribute('href'));
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 80
            }, 600);
        }
    });
    
    // Scroll to top button
    $(window).on('scroll', function() {
        if ($(this).scrollTop() > 300) {
            $('#scrollBtn').fadeIn();
        } else {
            $('#scrollBtn').fadeOut();
        }
    });
    
    // Scroll to top function
    window.scrollToTop = function() {
        $('html, body').animate({
            scrollTop: 0
        }, 600);
    };
}

// ===== UTILITY FUNCTIONS =====

// Check if device is mobile
window.isMobile = function() {
    return window.innerWidth <= 768;
};

// Check if device is tablet
window.isTablet = function() {
    return window.innerWidth > 768 && window.innerWidth <= 992;
};

// Check if device is desktop
window.isDesktop = function() {
    return window.innerWidth > 992;
};

// Format price
window.formatPrice = function(price) {
    return new Intl.NumberFormat('ar-SA', {
        style: 'currency',
        currency: 'SAR'
    }).format(price);
};

// Show loading spinner
window.showLoading = function() {
    const loadingHTML = `
        <div class="loading-overlay">
            <div class="loading-spinner">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">جاري التحميل...</span>
                </div>
            </div>
        </div>
    `;
    $('body').append(loadingHTML);
};

// Hide loading spinner
window.hideLoading = function() {
    $('.loading-overlay').remove();
};

// Show success message
window.showSuccess = function(message) {
    const successHTML = `
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    $('body').prepend(successHTML);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        $('.alert-success').fadeOut();
    }, 5000);
};

// Show error message
window.showError = function(message) {
    const errorHTML = `
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    $('body').prepend(errorHTML);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        $('.alert-danger').fadeOut();
    }, 5000);
};

// ===== RESPONSIVE HELPERS =====

// Handle window resize
$(window).on('resize', function() {
    // Update mobile menu state
    if (isDesktop()) {
        $('.offcanvas').removeClass('show');
    }
    
    // Update carousel items based on screen size
    if (isMobile()) {
        $('.owl-carousel').trigger('refresh.owl.carousel');
    }
});

// ===== FORM HANDLING =====

// Handle form submissions
$('form').on('submit', function(e) {
    e.preventDefault();
    
    const $form = $(this);
    const $submitBtn = $form.find('button[type="submit"]');
    const originalText = $submitBtn.text();
    
    // Show loading
    $submitBtn.text('جاري الإرسال...').prop('disabled', true);
    showLoading();
    
    // Simulate API call
    setTimeout(() => {
        hideLoading();
        $submitBtn.text(originalText).prop('disabled', false);
        showSuccess('تم إرسال الطلب بنجاح!');
        $form[0].reset();
    }, 2000);
});

// ===== ANIMATIONS =====

// Animate elements on scroll
function animateOnScroll() {
    $('.package-card, .step, .testimonial-item').each(function() {
        const $element = $(this);
        const elementTop = $element.offset().top;
        const elementBottom = elementTop + $element.outerHeight();
        const viewportTop = $(window).scrollTop();
        const viewportBottom = viewportTop + $(window).height();
        
        if (elementBottom > viewportTop && elementTop < viewportBottom) {
            $element.addClass('animate-in');
        }
    });
}

// Initialize scroll animations
$(window).on('scroll', animateOnScroll);
$(document).ready(animateOnScroll); 
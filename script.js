// Hero Slider
const slides = document.querySelectorAll('.slide');
const dots = document.querySelectorAll('.dot');
const prevBtn = document.querySelector('.prev-btn');
const nextBtn = document.querySelector('.next-btn');
let currentSlide = 0;

function showSlide(n) {
    slides.forEach(slide => slide.classList.remove('active'));
    dots.forEach(dot => dot.classList.remove('active'));
    
    currentSlide = (n + slides.length) % slides.length;
    
    slides[currentSlide].classList.add('active');
    dots[currentSlide].classList.add('active');
}

prevBtn.addEventListener('click', () => {
    showSlide(currentSlide - 1);
});

nextBtn.addEventListener('click', () => {
    showSlide(currentSlide + 1);
});

dots.forEach((dot, index) => {
    dot.addEventListener('click', () => {
        showSlide(index);
    });
});

// Auto slide
let slideInterval = setInterval(() => {
    showSlide(currentSlide + 1);
}, 5000);

// Pause on hover
const sliderContainer = document.querySelector('.slider-container');
sliderContainer.addEventListener('mouseenter', () => {
    clearInterval(slideInterval);
});

sliderContainer.addEventListener('mouseleave', () => {
    slideInterval = setInterval(() => {
        showSlide(currentSlide + 1);
    }, 5000);
});

// Cart functionality
document.addEventListener('DOMContentLoaded', function() {
    // Update cart count
    function updateCartCount() {
        const cartCount = document.querySelector('.cart-count');
        if (cartCount) {
            // This would normally come from the server
            fetch('get_cart_count.php')
                .then(response => response.json())
                .then(data => {
                    cartCount.textContent = data.count;
                });
        }
    }
    
    // Add to cart buttons
    const addToCartForms = document.querySelectorAll('.add-to-cart-form');
    addToCartForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!document.body.classList.contains('logged-in')) {
                e.preventDefault();
                showLoginModal();
            }
        });
    });
    
    // Login modal
    const loginModal = document.getElementById('loginModal');
    const closeModal = document.querySelector('.close-modal');
    const loginRequiredButtons = document.querySelectorAll('.login-required');
    
    function showLoginModal() {
        loginModal.style.display = 'flex';
    }
    
    function hideLoginModal() {
        loginModal.style.display = 'none';
    }
    
    if (closeModal) {
        closeModal.addEventListener('click', hideLoginModal);
    }
    
    loginRequiredButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!document.body.classList.contains('logged-in')) {
                e.preventDefault();
                showLoginModal();
            }
        });
    });
    
    window.addEventListener('click', function(e) {
        if (e.target === loginModal) {
            hideLoginModal();
        }
    });
    
    // Search functionality
    const searchInput = document.querySelector('.search-bar input');
    const searchButton = document.querySelector('.search-bar button');
    
    if (searchButton) {
        searchButton.addEventListener('click', function() {
            const query = searchInput.value.trim();
            if (query) {
                window.location.href = `product.php?search=${encodeURIComponent(query)}`;
            }
        });
    }
    
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const query = this.value.trim();
                if (query) {
                    window.location.href = `product.php?search=${encodeURIComponent(query)}`;
                }
            }
        });
    }
    
    // Mobile menu toggle
    const createMobileMenu = () => {
        if (window.innerWidth <= 768) {
            const nav = document.querySelector('.main-nav ul');
            const menuToggle = document.createElement('button');
            menuToggle.className = 'mobile-menu-toggle';
            menuToggle.innerHTML = '<i class="fas fa-bars"></i>';
            
            const headerTop = document.querySelector('.header-top');
            if (!document.querySelector('.mobile-menu-toggle')) {
                headerTop.appendChild(menuToggle);
                
                menuToggle.addEventListener('click', () => {
                    nav.classList.toggle('show');
                    menuToggle.innerHTML = nav.classList.contains('show') 
                        ? '<i class="fas fa-times"></i>' 
                        : '<i class="fas fa-bars"></i>';
                });
            }
        }
    };
    
    createMobileMenu();
    window.addEventListener('resize', createMobileMenu);
    
    // Add to cart animation
    document.querySelectorAll('.btn-add-to-cart').forEach(button => {
        button.addEventListener('click', function(e) {
            if (!this.classList.contains('login-required')) {
                const button = this;
                const originalText = button.innerHTML;
                
                button.innerHTML = '<i class="fas fa-check"></i> Added!';
                button.style.background = '#4CAF50';
                
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.style.background = '';
                }, 2000);
                
                // Update cart count
                const cartCount = document.querySelector('.cart-count');
                if (cartCount) {
                    let count = parseInt(cartCount.textContent);
                    cartCount.textContent = count + 1;
                    
                    // Add animation to cart icon
                    const cartIcon = document.querySelector('.cart-icon');
                    cartIcon.classList.add('pulse');
                    setTimeout(() => {
                        cartIcon.classList.remove('pulse');
                    }, 300);
                }
            }
        });
    });
    
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            if (targetId !== '#') {
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 100,
                        behavior: 'smooth'
                    });
                }
            }
        });
    });
});

// Cart icon pulse animation
const style = document.createElement('style');
style.textContent = `
    .pulse {
        animation: pulse 0.3s ease-in-out;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }
    
    @media (max-width: 768px) {
        .main-nav ul {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            background: white;
            flex-direction: column;
            padding: 20px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        
        .main-nav ul.show {
            display: flex;
        }
        
        .mobile-menu-toggle {
            background: var(--primary-color);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 5px;
            font-size: 20px;
            cursor: pointer;
        }
        
        .dropdown:hover .mega-menu {
            display: none;
        }
    }
`;
document.head.appendChild(style);
/**
 * Avenda Aurora - Main JavaScript
 * Mexican Cuisine Blog
 */

(function() {
    'use strict';

    // Mobile Navigation Toggle
    const navToggle = document.querySelector('.nav__toggle');
    const navMenu = document.querySelector('.nav__menu');

    if (navToggle && navMenu) {
        navToggle.addEventListener('click', function() {
            navMenu.classList.toggle('is-open');
            navToggle.classList.toggle('is-active');
            document.body.classList.toggle('nav-open');
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!navMenu.contains(e.target) && !navToggle.contains(e.target)) {
                navMenu.classList.remove('is-open');
                navToggle.classList.remove('is-active');
                document.body.classList.remove('nav-open');
            }
        });
    }

    // Ingredient Checkbox Persistence
    const ingredientCheckboxes = document.querySelectorAll('.ingredient__checkbox');

    if (ingredientCheckboxes.length > 0) {
        const recipeId = window.location.pathname;
        const storageKey = 'checkedIngredients_' + recipeId;

        // Load saved state
        const savedState = JSON.parse(localStorage.getItem(storageKey) || '{}');

        ingredientCheckboxes.forEach(function(checkbox, index) {
            // Restore saved state
            if (savedState[index]) {
                checkbox.checked = true;
                checkbox.closest('.ingredient').classList.add('is-checked');
            }

            // Save state on change
            checkbox.addEventListener('change', function() {
                savedState[index] = this.checked;
                localStorage.setItem(storageKey, JSON.stringify(savedState));

                if (this.checked) {
                    this.closest('.ingredient').classList.add('is-checked');
                } else {
                    this.closest('.ingredient').classList.remove('is-checked');
                }
            });
        });

        // Add "Clear All" button functionality
        const clearAllBtn = document.querySelector('.ingredients-clear-all');
        if (clearAllBtn) {
            clearAllBtn.addEventListener('click', function() {
                ingredientCheckboxes.forEach(function(checkbox) {
                    checkbox.checked = false;
                    checkbox.closest('.ingredient').classList.remove('is-checked');
                });
                localStorage.removeItem(storageKey);
            });
        }
    }

    // Smooth Scroll for Anchor Links
    document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
        anchor.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;

            const target = document.querySelector(targetId);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Recipe Category Filter (Client-side)
    const filterButtons = document.querySelectorAll('.filter-btn');
    const recipeCards = document.querySelectorAll('.recipe-card');

    if (filterButtons.length > 0 && recipeCards.length > 0) {
        filterButtons.forEach(function(btn) {
            btn.addEventListener('click', function() {
                const category = this.dataset.category;

                // Update active state
                filterButtons.forEach(function(b) {
                    b.classList.remove('is-active');
                });
                this.classList.add('is-active');

                // Filter cards with animation
                recipeCards.forEach(function(card) {
                    const cardCategory = card.dataset.category;

                    if (category === 'all' || cardCategory === category) {
                        card.style.display = '';
                        card.classList.remove('is-hidden');
                    } else {
                        card.classList.add('is-hidden');
                        setTimeout(function() {
                            if (card.classList.contains('is-hidden')) {
                                card.style.display = 'none';
                            }
                        }, 300);
                    }
                });
            });
        });
    }

    // Search Functionality
    const searchInput = document.querySelector('.search__input');
    const searchResults = document.querySelector('.search__results');

    if (searchInput) {
        let searchTimeout;

        searchInput.addEventListener('input', function() {
            const query = this.value.trim();

            clearTimeout(searchTimeout);

            if (query.length < 2) {
                if (searchResults) searchResults.innerHTML = '';
                return;
            }

            searchTimeout = setTimeout(function() {
                // In a real implementation, this would call an API
                // For now, we'll just show a placeholder
                if (searchResults) {
                    searchResults.innerHTML = '<p class="search__loading">Buscando...</p>';
                }
            }, 300);
        });

        // Close search on escape
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                this.value = '';
                if (searchResults) searchResults.innerHTML = '';
            }
        });
    }

    // Lazy Loading Images
    if ('IntersectionObserver' in window) {
        const lazyImages = document.querySelectorAll('img[loading="lazy"]');

        const imageObserver = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                    }
                    imageObserver.unobserve(img);
                }
            });
        });

        lazyImages.forEach(function(img) {
            imageObserver.observe(img);
        });
    }

    // Print Recipe Functionality
    window.printRecipe = function() {
        window.print();
    };

    // Share Recipe Functionality
    window.shareRecipe = function() {
        const title = document.querySelector('.recipe__title');
        const description = document.querySelector('.recipe__description');

        if (navigator.share) {
            navigator.share({
                title: title ? title.textContent : document.title,
                text: description ? description.textContent : '',
                url: window.location.href
            }).catch(function(err) {
                console.log('Share cancelled:', err);
            });
        } else {
            // Fallback: Copy to clipboard
            navigator.clipboard.writeText(window.location.href).then(function() {
                showToast('Link copiado al portapapeles');
            }).catch(function() {
                // Fallback for older browsers
                const input = document.createElement('input');
                input.value = window.location.href;
                document.body.appendChild(input);
                input.select();
                document.execCommand('copy');
                document.body.removeChild(input);
                showToast('Link copiado al portapapeles');
            });
        }
    };

    // Toast Notification
    function showToast(message, duration) {
        duration = duration || 3000;

        const existing = document.querySelector('.toast');
        if (existing) existing.remove();

        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.textContent = message;
        document.body.appendChild(toast);

        // Trigger animation
        setTimeout(function() {
            toast.classList.add('is-visible');
        }, 10);

        setTimeout(function() {
            toast.classList.remove('is-visible');
            setTimeout(function() {
                toast.remove();
            }, 300);
        }, duration);
    }

    // Servings Calculator
    const servingsInput = document.querySelector('.servings-input');

    if (servingsInput) {
        const originalServings = parseInt(servingsInput.value) || 4;

        servingsInput.addEventListener('change', function() {
            const newServings = parseInt(this.value) || originalServings;
            const ratio = newServings / originalServings;

            document.querySelectorAll('.ingredient__quantity').forEach(function(qty) {
                const original = parseFloat(qty.dataset.original || qty.textContent);
                if (!qty.dataset.original) {
                    qty.dataset.original = original;
                }

                const newQty = (original * ratio).toFixed(2).replace(/\.?0+$/, '');
                qty.textContent = newQty;
            });
        });
    }

    // Scroll Progress Indicator
    const progressBar = document.querySelector('.reading-progress');

    if (progressBar) {
        window.addEventListener('scroll', function() {
            const scrollTop = window.scrollY;
            const docHeight = document.documentElement.scrollHeight - window.innerHeight;
            const progress = (scrollTop / docHeight) * 100;
            progressBar.style.width = progress + '%';
        });
    }

    // Newsletter Form
    const newsletterForm = document.querySelector('.newsletter__form');

    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const email = this.querySelector('input[type="email"]').value;
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;

            submitBtn.disabled = true;
            submitBtn.textContent = 'Enviando...';

            // Simulate API call (replace with actual implementation)
            setTimeout(function() {
                submitBtn.textContent = 'Suscrito!';
                showToast('Gracias por suscribirte!');

                setTimeout(function() {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                    newsletterForm.reset();
                }, 2000);
            }, 1000);
        });
    }

    // Heat Level Tooltip
    const heatDots = document.querySelectorAll('.heat-meter__dot');

    heatDots.forEach(function(dot, index) {
        dot.addEventListener('mouseenter', function() {
            const levels = [
                'Sin picor', 'Muy suave', 'Suave', 'Suave-Medio',
                'Medio', 'Medio-Alto', 'Alto', 'Muy alto',
                'Extremo', 'Extremadamente picante'
            ];
            dot.setAttribute('title', levels[index] || '');
        });
    });

    // Animate on Scroll
    if ('IntersectionObserver' in window) {
        const animateElements = document.querySelectorAll('.animate-on-scroll');

        const animateObserver = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-animated');
                    animateObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1
        });

        animateElements.forEach(function(el) {
            animateObserver.observe(el);
        });
    }

})();

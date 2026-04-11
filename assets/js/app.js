/**
 * Aurora - Mexican Cuisine Blog
 * Main JavaScript
 */

(function() {
    'use strict';

    // ========================================
    // Header Scroll Effect (Epicurious-style logo grow)
    // ========================================
    var header = document.getElementById('siteHeader');

    if (header) {
        var lastScroll = 0;

        window.addEventListener('scroll', function() {
            var scrollY = window.scrollY;

            if (scrollY > 50) {
                header.classList.add('is-scrolled');
            } else {
                header.classList.remove('is-scrolled');
            }

            lastScroll = scrollY;
        }, { passive: true });
    }

    // ========================================
    // Mobile Navigation Toggle
    // ========================================
    var menuToggle = document.getElementById('menuToggle');
    var mainNav = document.getElementById('mainNav');

    if (menuToggle && mainNav) {
        menuToggle.addEventListener('click', function() {
            mainNav.classList.toggle('is-open');
            menuToggle.classList.toggle('is-active');
            document.body.classList.toggle('nav-open');
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!mainNav.contains(e.target) && !menuToggle.contains(e.target)) {
                mainNav.classList.remove('is-open');
                menuToggle.classList.remove('is-active');
                document.body.classList.remove('nav-open');
            }
        });
    }

    // ========================================
    // Ingredient Checkbox Persistence
    // ========================================
    var ingredientCheckboxes = document.querySelectorAll('.ingredient__checkbox');

    if (ingredientCheckboxes.length > 0) {
        var recipeId = window.location.pathname;
        var storageKey = 'checkedIngredients_' + recipeId;

        // Load saved state
        var savedState = JSON.parse(localStorage.getItem(storageKey) || '{}');

        ingredientCheckboxes.forEach(function(checkbox, index) {
            if (savedState[index]) {
                checkbox.checked = true;
                checkbox.closest('.ingredient').classList.add('is-checked');
            }

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

        var clearAllBtn = document.querySelector('.ingredients-clear-all');
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

    // ========================================
    // Smooth Scroll for Anchor Links
    // ========================================
    document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
        anchor.addEventListener('click', function(e) {
            var targetId = this.getAttribute('href');
            if (targetId === '#') return;

            var target = document.querySelector(targetId);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // ========================================
    // Header Search Toggle
    // ========================================
    var searchForm = document.getElementById('headerSearch');
    var searchToggle = document.getElementById('headerSearchToggle');
    var searchInput = document.getElementById('headerSearchInput');

    if (searchForm && searchToggle && searchInput) {
        searchToggle.addEventListener('click', function() {
            var isOpen = searchForm.classList.toggle('is-open');
            searchToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

            if (isOpen) {
                searchInput.focus();
            } else if (searchInput.value.trim() === '') {
                return;
            } else {
                searchForm.submit();
            }
        });

        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                this.value = '';
                searchForm.classList.remove('is-open');
                searchToggle.setAttribute('aria-expanded', 'false');
                this.blur();
            }
        });

        document.addEventListener('click', function(e) {
            if (!searchForm.contains(e.target) && searchInput.value.trim() === '') {
                searchForm.classList.remove('is-open');
                searchToggle.setAttribute('aria-expanded', 'false');
            }
        });
    }

    // ========================================
    // Account Dropdown
    // ========================================
    var accountWrap = document.getElementById('headerAccount');
    var accountToggle = document.getElementById('headerAccountToggle');

    if (accountWrap && accountToggle) {
        accountToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            var isOpen = accountWrap.classList.toggle('is-open');
            accountToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        document.addEventListener('click', function(e) {
            if (!accountWrap.contains(e.target)) {
                accountWrap.classList.remove('is-open');
                accountToggle.setAttribute('aria-expanded', 'false');
            }
        });
    }

    // ========================================
    // Favorite Button
    // ========================================
    document.querySelectorAll('[data-favorite-btn]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            if (btn.dataset.loggedIn !== '1') {
                window.location.href = btn.dataset.loginUrl;
                return;
            }

            var recipeId = btn.dataset.recipeId;
            btn.classList.add('is-loading');

            fetch('/api/favorite/' + recipeId, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' }
            })
            .then(function(res) {
                return res.json().then(function(data) {
                    return { ok: res.ok, status: res.status, data: data };
                });
            })
            .then(function(result) {
                if (!result.ok) {
                    var msg = (result.data && result.data.error) || ('HTTP ' + result.status);
                    showToast(msg);
                    return;
                }
                var data = result.data;
                btn.classList.toggle('is-favorite', data.favorited);
                btn.setAttribute('aria-pressed', data.favorited ? 'true' : 'false');
                var svg = btn.querySelector('svg');
                if (svg) svg.setAttribute('fill', data.favorited ? 'currentColor' : 'none');
                var label = btn.querySelector('.recipe__action-label');
                if (label) label.textContent = data.favorited ? (window.AURORA_T_UNFAV || 'Quitar') : (window.AURORA_T_FAV || 'Favorito');
            })
            .catch(function(err) {
                showToast('Network error: ' + err.message);
            })
            .finally(function() {
                btn.classList.remove('is-loading');
            });
        });
    });

    // ========================================
    // Lazy Loading Images
    // ========================================
    if ('IntersectionObserver' in window) {
        var lazyImages = document.querySelectorAll('img[loading="lazy"]');

        var imageObserver = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    var img = entry.target;
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

    // ========================================
    // Print & Share Recipe
    // ========================================
    window.printRecipe = function() {
        window.print();
    };

    window.shareRecipe = function() {
        var title = document.querySelector('.recipe__title');
        var description = document.querySelector('.recipe__description');

        if (navigator.share) {
            navigator.share({
                title: title ? title.textContent : document.title,
                text: description ? description.textContent : '',
                url: window.location.href
            }).catch(function(err) {
                console.log('Share cancelled:', err);
            });
        } else {
            navigator.clipboard.writeText(window.location.href).then(function() {
                showToast('Link copiado al portapapeles');
            }).catch(function() {
                var input = document.createElement('input');
                input.value = window.location.href;
                document.body.appendChild(input);
                input.select();
                document.execCommand('copy');
                document.body.removeChild(input);
                showToast('Link copiado al portapapeles');
            });
        }
    };

    // ========================================
    // Toast Notification
    // ========================================
    function showToast(message, duration) {
        duration = duration || 3000;

        var existing = document.querySelector('.toast');
        if (existing) existing.remove();

        var toast = document.createElement('div');
        toast.className = 'toast';
        toast.textContent = message;
        document.body.appendChild(toast);

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

    // ========================================
    // Servings Calculator
    // ========================================
    var servingsInput = document.querySelector('.servings-input');

    if (servingsInput) {
        var originalServings = parseInt(servingsInput.value) || 4;

        servingsInput.addEventListener('change', function() {
            var newServings = parseInt(this.value) || originalServings;
            var ratio = newServings / originalServings;

            document.querySelectorAll('.ingredient__quantity').forEach(function(qty) {
                var original = parseFloat(qty.dataset.original || qty.textContent);
                if (!qty.dataset.original) {
                    qty.dataset.original = original;
                }

                var newQty = (original * ratio).toFixed(2).replace(/\.?0+$/, '');
                qty.textContent = newQty;
            });
        });
    }

    // ========================================
    // Scroll Progress Indicator
    // ========================================
    var progressBar = document.querySelector('.reading-progress');

    if (progressBar) {
        window.addEventListener('scroll', function() {
            var scrollTop = window.scrollY;
            var docHeight = document.documentElement.scrollHeight - window.innerHeight;
            var progress = (scrollTop / docHeight) * 100;
            progressBar.style.width = progress + '%';
        }, { passive: true });
    }

    // ========================================
    // Newsletter Form
    // ========================================
    var newsletterForm = document.querySelector('.newsletter__form');

    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();

            var email = this.querySelector('input[type="email"]').value;
            var submitBtn = this.querySelector('button[type="submit"]');
            var originalText = submitBtn.textContent;

            submitBtn.disabled = true;
            submitBtn.textContent = 'Enviando...';

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

    // ========================================
    // Heat Level Tooltip
    // ========================================
    var heatDots = document.querySelectorAll('.heat-meter__dot');

    heatDots.forEach(function(dot, index) {
        dot.addEventListener('mouseenter', function() {
            var levels = [
                'Sin picor', 'Muy suave', 'Suave', 'Suave-Medio',
                'Medio', 'Medio-Alto', 'Alto', 'Muy alto',
                'Extremo', 'Extremadamente picante'
            ];
            dot.setAttribute('title', levels[index] || '');
        });
    });

    // ========================================
    // Animate on Scroll
    // ========================================
    if ('IntersectionObserver' in window) {
        var animateElements = document.querySelectorAll('.animate-on-scroll');

        var animateObserver = new IntersectionObserver(function(entries) {
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
/**
 * Template Name: Simple - Responsive Admin & Dashboard Template
 * By (Author): Coderthemes
 * Module/App (File Name): Main App JS File
 * Version: 3.0.0
 */

//
// ------------------------------ Required main scripts ------------------------------
//

// Common
class App {

    init() {
        this.initComponents();
        this.initPreloader();
        this.initPortletCard();
        this.initMultiDropdown();
        this.initFormValidation();
        this.initCounter();
        this.initToggle();
        this.initPassword();
        this.initDismissible();
        this.initSidenav();
    }

    // Bootstrap Components
    initComponents() {

        if (typeof lucide.createIcons === 'function') {
            lucide.createIcons();
        }

        // Popovers
        document.querySelectorAll('[data-bs-toggle="popover"]').forEach(el => {
            new bootstrap.Popover(el);
        });

        // Tooltips
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
            new bootstrap.Tooltip(el);
        });

        // Offcanvas
        document.querySelectorAll('.offcanvas').forEach(el => {
            new bootstrap.Offcanvas(el);
        });

        // Toasts
        document.querySelectorAll('.toast').forEach(el => {
            new bootstrap.Toast(el);
        });
    }

    // Preloader
    initPreloader() {
        window.addEventListener('load', () => {
            const status = document.getElementById('status');
            const preloader = document.getElementById('preloader');
            if (status) status.style.display = 'none';
            if (preloader) {
                setTimeout(() => preloader.style.display = 'none', 350);
            }
        });
    }

    // Portlet Widget (Card Reload, Collapse, and Delete)
    initPortletCard() {

        // Handle card close
        $('[data-action="card-close"]').on('click', function (event) {
            event.preventDefault();

            const $card = $(this).closest('.card');
            $card.fadeOut(300, function () {
                $card.remove();
            });
        });

        // Handle card toggle
        $('[data-action="card-toggle"]').on('click', function (event) {
            event.preventDefault();

            const $card = $(this).closest('.card');
            const $icon = $(this).find('i').eq(0);
            const $body = $card.find('.card-body');
            const $footer = $card.find('.card-footer');

            $body.slideToggle(300);
            $footer.slideToggle(200);
            $icon.toggleClass('ti-chevron-up ti-chevron-down');
            $card.toggleClass('card-collapse');
        });

        // Handle card refresh
        const refreshButtons = document.querySelectorAll('[data-action="card-refresh"]');
        if (refreshButtons) {
            refreshButtons.forEach(function (button) {
                button.addEventListener('click', function (event) {
                    event.preventDefault();

                    const card = event.target.closest('.card');
                    const cardBody = card.querySelector('.card-body');

                    // Ensure .card-body has relative positioning
                    cardBody.style.position = 'relative';

                    let overlay = cardBody.querySelector('.card-overlay');
                    if (!overlay) {
                        overlay = document.createElement('div');
                        overlay.classList.add('card-overlay');

                        const spinner = document.createElement('div');
                        spinner.classList.add('spinner-border', 'text-primary');

                        overlay.appendChild(spinner);
                        cardBody.appendChild(overlay);
                    }

                    overlay.style.display = 'flex';

                    setTimeout(function () {
                        overlay.style.display = 'none';
                    }, 1500);
                });
            });
        }

        // Handle code preview collapse
        $('[data-action="code-collapse"]').on('click', function (event) {
            event.preventDefault();

            const $card = $(this).closest('.card');
            const $icon = $(this).find('i').eq(0);
            const $codeBody = $card.find('.code-body');

            $codeBody.slideToggle(300);
            $icon.toggleClass('ti-chevron-up ti-chevron-down');
        });
    }

    //  Multi Dropdown
    initMultiDropdown() {
        $('.dropdown-menu a.dropdown-toggle').on('click', function () {
            const dropdown = $(this).next('.dropdown-menu');
            const otherDropdown = $(this).parent().parent().find('.dropdown-menu').not(dropdown);
            otherDropdown.removeClass('show')
            otherDropdown.parent().find('.dropdown-toggle').removeClass('show')
            return false;
        });
    }

    // Form Validation
    initFormValidation() {
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        // Loop over them and prevent submission
        document.querySelectorAll('.needs-validation').forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }

                form.classList.add('was-validated')
            }, false)
        })
    }

    // Counter for Numbers
    initCounter() {
        const counters = document.querySelectorAll('[data-target]');

        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counter = entry.target;

                    // Parse the target value, removing any commas first
                    let target = counter.getAttribute('data-target').replace(/,/g, '');

                    target = parseFloat(target); // Convert to float

                    let current = 0; // Initial counter value

                    let increment;  // Increment step for smooth animation

                    if (Number.isInteger(target)) {
                        increment = Math.floor(target / 25); // Increment for integer values
                    } else {
                        increment = target / 25; // Increment for float values
                    }

                    const updateCounter = () => {
                        if (current < target) {
                            current += increment;
                            if (current > target) current = target; // Avoid overshooting
                            // Format as integer or decimal and add commas
                            counter.innerText = formatNumber(current);
                            requestAnimationFrame(updateCounter); // Continue animation frame by frame
                        } else {
                            counter.innerText = formatNumber(target); // Final display
                        }
                    };

                    updateCounter(); // Start the animation

                    // Function to format numbers with commas and decimal places if necessary
                    function formatNumber(num) {
                        if (num % 1 === 0) {
                            // Format as integer with commas
                            return num.toLocaleString();
                        } else {
                            // Format as float with two decimal places and commas
                            return num.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        }
                    }

                    observer.unobserve(counter);
                }
            });
        }, {
            threshold: 1 // Adjust this threshold to control when to trigger (e.g., 0.5 means 50% of the element is visible)
        });

        counters.forEach(counter => observer.observe(counter));
    }

    // Toggle logic based on data attributes
    initToggle() {
        document.querySelectorAll('[data-toggler]').forEach(wrapper => {
            const toggleOn = wrapper.querySelector('[data-toggler-on]');
            const toggleOff = wrapper.querySelector('[data-toggler-off]');
            let isOn = wrapper.getAttribute('data-toggler') === 'on';

            const updateToggleState = () => {
                if (isOn) {
                    toggleOn?.classList.remove('d-none');
                    toggleOff?.classList.add('d-none');
                } else {
                    toggleOn?.classList.add('d-none');
                    toggleOff?.classList.remove('d-none');
                }
            };

            toggleOn?.addEventListener('click', () => {
                isOn = false;
                updateToggleState();
            });

            toggleOff?.addEventListener('click', () => {
                isOn = true;
                updateToggleState();
            });

            updateToggleState();
        });
    }

    // Password Show/Hide based on data attributes [data-password]
    initPassword() {
        document.querySelectorAll("[data-password]").forEach((element) => {
            const password = element.querySelector(".form-password");
            const eyeOn = element.querySelector(".password-eye-on");
            const eyeOff = element.querySelector(".password-eye-off");

            if (!password || !eyeOn || !eyeOff) return;

            // Initially show "eye-on" only
            eyeOff.classList.add("d-none");

            eyeOn.addEventListener("click", () => {
                password.type = "text";
                eyeOn.classList.add("d-none");
                eyeOff.classList.remove("d-none");
            });

            eyeOff.addEventListener("click", () => {
                password.type = "password";
                eyeOff.classList.add("d-none");
                eyeOn.classList.remove("d-none");
            });
        });

    }

    // Dismiss elements with [data-dismissible]
    initDismissible() {
        document.querySelectorAll('[data-dismissible]').forEach(trigger => {
            trigger.addEventListener('click', () => {
                const selector = trigger.getAttribute('data-dismissible');
                const target = document.querySelector(selector);
                if (target) target.remove();
            });
        });
    }

    // Sidenav Link Activation
    initSidenav() {

        const sideNav = document.querySelector('.side-nav');
        if (!sideNav) return;

        // Prevent default toggle behavior
        sideNav.querySelectorAll("li [data-bs-toggle='collapse']").forEach(toggle => {
            toggle.addEventListener('click', e => e.preventDefault());
        });

        // Ensure only one collapse is open at a time
        const allCollapses = sideNav.querySelectorAll('li .collapse');
        allCollapses.forEach(collapse => {
            collapse.addEventListener('show.bs.collapse', event => {
                const currentCollapse = event.target;

                const ancestors = [];
                let el = currentCollapse.parentElement;
                while (el && el !== sideNav) {
                    if (el.classList.contains('collapse')) {
                        ancestors.push(el);
                    }
                    el = el.parentElement;
                }

                allCollapses.forEach(other => {
                    if (other !== currentCollapse && !ancestors.includes(other)) {
                        new bootstrap.Collapse(other, {toggle: false}).hide();
                    }
                });
            });
        });

        // Get current page
        const currentUrl = window.location.href.split(/[?#]/)[0];
        const currentPage = window.location.pathname.split("/").pop();

        const allLinks = sideNav.querySelectorAll('.side-nav-link[href]');
        allLinks.forEach(link => {
            const linkHref = link.getAttribute('href');
            if (!linkHref) return;

            const match = linkHref === currentPage || link.href === currentUrl;
            if (!match) return;

            // Clear previous active/show states
            sideNav.querySelectorAll('a.active, li.active, .collapse.show').forEach(el => {
                el.classList.remove('active', 'show');
            });

            // Mark link and <li> active
            link.classList.add('active');
            const li = link.closest('li.side-nav-item');
            if (li) li.classList.add('active');

            // Recursively walk up and show all parent collapses
            let parent = link.closest('li');
            while (parent && parent !== sideNav) {
                parent.classList.add('active');

                const parentCollapse = parent.closest('.collapse');
                if (parentCollapse) {
                    parentCollapse.classList.add('show');

                    const toggleLink = sideNav.querySelector(`a[href="#${parentCollapse.id}"]`);
                    if (toggleLink) {
                        toggleLink.setAttribute('aria-expanded', 'true');
                        const parentLi = toggleLink.closest('li.side-nav-item');
                        if (parentLi) parentLi.classList.add('active');
                    }

                    parent = parentCollapse.closest('li');
                } else {
                    parent = parent.parentElement;
                }
            }
        });

       // Auto-scroll to active item
        setTimeout(() => {
            const activeItem = sideNav.querySelector('li.active .active');
            const scrollContainer = document.querySelector('.sidenav-menu .simplebar-content-wrapper');

            if (activeItem && scrollContainer) {
                const offset = activeItem.offsetTop - 300;
                if (offset > 100) {
                    scrollToPosition(scrollContainer, offset, 600);
                }
            }
        }, 200);

        // Smooth scroll utility
        function scrollToPosition(element, to, duration) {
            const start = element.scrollTop;
            const change = to - start;
            const increment = 20;
            let currentTime = 0;

            function animateScroll() {
                currentTime += increment;
                element.scrollTop = easeInOutQuad(currentTime, start, change, duration);
                if (currentTime < duration) {
                    setTimeout(animateScroll, increment);
                }
            }

            animateScroll();
        }

        function easeInOutQuad(t, b, c, d) {
            t /= d / 2;
            if (t < 1) return c / 2 * t * t + b;
            t--;
            return -c / 2 * (t * (t - 2) - 1) + b;
        }
    }
}

// Layout Customizer
class LayoutCustomizer {
    constructor() {
        this.html = document.documentElement;
        this.config = {};
    }

    init() {
        this.initConfig();
        this.monochromeMode()
        this.initSwitchListener();
        this.initWindowSize();
        this._adjustLayout();
        this.setSwitchFromConfig();
        this.openCustomizer() // demo only
    }

    initConfig() {
        this.defaultConfig = JSON.parse(JSON.stringify(window.defaultConfig));
        this.config = JSON.parse(JSON.stringify(window.config));
        this.setSwitchFromConfig();
    }

    // demo only
    isFirstVisit() {
        const visited = localStorage.getItem('__user_has_visited__');
        if (!visited) {
            localStorage.setItem('__user_has_visited__', 'true');
            return true;
        }
        return false;
    }

    // demo only
    openCustomizer() {
        const layoutCustomizer = document.getElementById('theme-settings-offcanvas');
        if (layoutCustomizer && this.isFirstVisit()) {
            const offcanvas = new bootstrap.Offcanvas(layoutCustomizer);
            setTimeout(() => {
                offcanvas.show()
            }, 1000)
        }
    }

    monochromeMode() {
        const toggleBtn = document.getElementById("monochrome-mode");
        if (toggleBtn) {
            toggleBtn.addEventListener("click", () => {
                this.config.monochrome = !this.config.monochrome;

                if (this.config.monochrome) {
                    this.html.classList.add("monochrome");
                } else {
                    this.html.classList.remove("monochrome");
                }

                // persist config
                this.setSwitchFromConfig();
            });
        }
    }

    changeSkin(skin) {
        this.config.skin = skin;
        this.html.setAttribute('data-skin', skin);
        this.setSwitchFromConfig();
    }

    changeSidenavColor(color) {
        this.config.sidenav.color = color;
        this.html.setAttribute('data-sidenav-color', color);
        this.setSwitchFromConfig();
    }

    changeSidenavSize(size, save = true) {
        this.html.setAttribute('data-sidenav-size', size);
        if (save) {
            this.config.sidenav.size = size;
            this.setSwitchFromConfig();
        }
    }

    changeLayoutPosition(position) {
        this.config.layout.position = position;
        this.html.setAttribute('data-layout-position', position);
        this.setSwitchFromConfig();
    }

    changeTheme(color) {
        this.config.theme = color;
        this.html.setAttribute('data-bs-theme', color);
        this.setSwitchFromConfig();
    }

    changeTopbarColor(color) {
        this.config.topbar.color = color;
        this.html.setAttribute('data-topbar-color', color);
        this.setSwitchFromConfig();
    }

    changeSidenavUser(showUser) {
        this.config.sidenav.user = showUser;
        if (showUser) {
            this.html.setAttribute('data-sidenav-user', showUser);
        } else {
            this.html.removeAttribute('data-sidenav-user');
        }
        this.setSwitchFromConfig();
    }

    resetTheme() {
        this.config = JSON.parse(JSON.stringify(window.defaultConfig));
        this.changeSkin(this.config.skin);
        this.changeSidenavColor(this.config.sidenav.color);
        this.changeSidenavSize(this.config.sidenav.size);
        this.changeTheme(this.config.theme);
        this.changeLayoutPosition(this.config.layout.position);
        this.changeTopbarColor(this.config.topbar.color);
        this.changeSidenavUser(this.config.sidenav.user);
        this._adjustLayout();
    }

    setSwitchFromConfig() {
        const config = this.config;

        sessionStorage.setItem('__SIMPLE_CONFIG__', JSON.stringify(config));

        document.querySelectorAll('.right-bar input[type=checkbox]').forEach(cb => cb.checked = false);

        const select = (name, val) => document.querySelector(`input[name="${name}"][value="${val}"]`);
        const toggle = (sel, state) => {
            const el = document.querySelector(sel);
            if (el) el.checked = state;
        };

        toggle('input[name="sidebar-user"]', config.sidenav.user === true);

        [
            ['data-skin', config.skin],
            ['data-bs-theme', config.theme],
            ['data-layout-position', config.layout.position],
            ['data-topbar-color', config.topbar.color],
            ['data-sidenav-color', config.sidenav.color],
            ['data-sidenav-size', config.sidenav.size]
        ].forEach(([name, val]) => {
            const el = select(name, val);
            if (el) el.checked = true;
        });
    }

    initSwitchListener() {
        const bindChange = (selector, handler) => {
            document.querySelectorAll(selector).forEach(input =>
                input.addEventListener('change', () => handler(input))
            );
        };

        // Bind skin related buttons
        document.querySelectorAll("button[data-skin]").forEach(btn =>
            btn.addEventListener('click', () => this.changeSkin(btn.getAttribute('data-skin')))
        );

        // Bind theme and layout related radios
        // bindChange('input[name="data-skin"]', input => this.changeSkin(input.value));
        bindChange('input[name="data-sidenav-color"]', input => this.changeSidenavColor(input.value));
        bindChange('input[name="data-sidenav-size"]', input => this.changeSidenavSize(input.value));
        bindChange('input[name="data-bs-theme"]', input => this.changeTheme(input.value));
        bindChange('input[name="data-layout-position"]', input => this.changeLayoutPosition(input.value));
        bindChange('input[name="data-topbar-color"]', input => this.changeTopbarColor(input.value));
        bindChange('input[name="sidebar-user"]', input => this.changeSidenavUser(input.checked));

        const themeToggle = document.getElementById('light-dark-mode');
        if (themeToggle) {
            themeToggle.addEventListener('click', () => {
                const newTheme = this.config.theme === 'light' ? 'dark' : 'light';
                this.changeTheme(newTheme);
            });
        }

        const resetBtn = document.querySelector('#reset-layout');
        if (resetBtn) {
            resetBtn.addEventListener('click', () => this.resetTheme());
        }

        const closeBtn = document.querySelector('.button-close-offcanvas');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                this.html.classList.remove('sidebar-enable');
                this.hideBackdrop();
            });
        }

        document.querySelectorAll('.button-collapse-toggle').forEach(el => {
            el.addEventListener('click', () => {
                const current = this.html.getAttribute('data-sidenav-size');

                if (current === 'offcanvas') {
                    this.showBackdrop();
                    this.html.classList.toggle('sidebar-enable');
                    return;
                }

                this.changeSidenavSize({default: 'collapse', 'collapse': 'default'}[current], true);
            });
        });

    }

    showBackdrop() {
        const backdrop = document.createElement('div');

        const getScrollbarWidth = () => {
            const container = document.createElement('div');
            container.style.visibility = 'hidden';
            container.style.overflow = 'scroll';
            container.style.width = '100px';
            container.style.height = '100px';
            document.body.appendChild(container);

            const inner = document.createElement('div');
            inner.style.width = '100%';
            container.appendChild(inner);

            const scrollbarWidth = container.offsetWidth - container.clientWidth;

            document.body.removeChild(container);
            return scrollbarWidth;
        };

        backdrop.id = 'custom-backdrop';
        backdrop.className = 'offcanvas-backdrop fade show';
        document.body.appendChild(backdrop);
        document.body.style.overflow = 'hidden';
        document.body.style.paddingRight = `${getScrollbarWidth()}px`;
        backdrop.addEventListener('click', () => {
            this.html.classList.remove('sidebar-enable');
            this.hideBackdrop();
        });
    }

    hideBackdrop() {
        const backdrop = document.getElementById('custom-backdrop');
        if (backdrop) {
            document.body.removeChild(backdrop);
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        }
    }

    _adjustLayout() {
        const width = window.innerWidth;
        const size = this.config.sidenav.size;

        if (width <= 1199) {
            this.changeSidenavSize('offcanvas', false);
        } else {
            this.changeSidenavSize(size);
        }
    }

    initWindowSize() {
        window.addEventListener('resize', () => this._adjustLayout());
    }
}

// If you need only theme toggler not whole layout customizer, you can use this.
// Note: If you are using this, comment or remove LayoutCustomizer.

// const themeToggle = document.getElementById('light-dark-mode');
// if (themeToggle) {
//     const html = document.documentElement;
//
//     const storageKey = '__Simple_CONFIG__';
//     const savedConfig = sessionStorage.getItem(storageKey);
//     const config = savedConfig ? JSON.parse(savedConfig) : {
//         theme: html.getAttribute('data-bs-theme') || 'light'
//     };
//
//     themeToggle.addEventListener('click', () => {
//         const newTheme = config.theme === 'light' ? 'dark' : 'light';
//         config.theme = newTheme;
//         html.setAttribute('data-bs-theme', newTheme);
//         sessionStorage.setItem(storageKey, JSON.stringify(config));
//     });
// }


//
// ------------------------------ Optional scripts / plugin scripts ------------------------------
//

class Plugins {
    init() {
        // comment or remove plugins you don't need
        this.initFlatPicker();
        this.initTouchSpin();
    }

    // Flatpickr / Timepickr
    initFlatPicker() {
        document.querySelectorAll("[data-provider]").forEach(item => {
            const type = item.getAttribute("data-provider");
            const attrs = item.attributes;
            const dateConfig = {disableMobile: true, defaultDate: new Date()};

            if (type === "flatpickr") {
                if (attrs["data-date-format"]) dateConfig.dateFormat = attrs["data-date-format"].value;
                if (attrs["data-enable-time"]) {
                    dateConfig.enableTime = true;
                    dateConfig.dateFormat += " H:i";
                }
                if (attrs["data-altFormat"]) {
                    dateConfig.altInput = true;
                    dateConfig.altFormat = attrs["data-altFormat"].value;
                }
                if (attrs["data-minDate"]) dateConfig.minDate = attrs["data-minDate"].value;
                if (attrs["data-maxDate"]) dateConfig.maxDate = attrs["data-maxDate"].value;
                if (attrs["data-default-date"]) dateConfig.defaultDate = attrs["data-default-date"].value;
                if (attrs["data-multiple-date"]) dateConfig.mode = "multiple";
                if (attrs["data-range-date"]) dateConfig.mode = "range";
                if (attrs["data-inline-date"]) {
                    dateConfig.inline = true;
                    dateConfig.defaultDate = attrs["data-default-date"].value;
                }
                if (attrs["data-disable-date"]) {
                    dateConfig.disable = attrs["data-disable-date"].value.split(",");
                }
                if (attrs["data-week-number"]) {
                    dateConfig.weekNumbers = true;
                }

                flatpickr(item, dateConfig);
            } else if (type === "timepickr") {
                const timeConfig = {
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",
                    defaultDate: new Date(),
                };
                if (attrs["data-time-hrs"]) timeConfig.time_24hr = true;
                if (attrs["data-min-time"]) timeConfig.minTime = attrs["data-min-time"].value;
                if (attrs["data-max-time"]) timeConfig.maxTime = attrs["data-max-time"].value;
                if (attrs["data-default-time"]) timeConfig.defaultDate = attrs["data-default-time"].value;
                if (attrs["data-time-inline"]) {
                    timeConfig.inline = true;
                    timeConfig.defaultDate = attrs["data-time-inline"].value;
                }

                flatpickr(item, timeConfig);
            }
        });
    }

    // Touchspin: plus/minus increment controls
    initTouchSpin() {
        document.querySelectorAll('[data-touchspin]').forEach(spin => {
            const minusBtn = spin.querySelector('[data-minus]');
            const plusBtn = spin.querySelector('[data-plus]');
            const input = spin.querySelector('input');

            if (input) {
                const min = Number(input.min);
                const max = Number(input.max ?? 0);
                const hasMin = Number.isFinite(min);
                const hasMax = Number.isFinite(max);

                const getValue = () => Number.parseInt(input.value) || 0;

                const isReadonly = () => input.hasAttribute('readonly');

                if (!isReadonly()) {
                    if (minusBtn) minusBtn.addEventListener('click', () => {
                        let newVal = getValue() - 1;
                        if (!hasMin || newVal >= min) {
                            input.value = newVal.toString();
                        }
                    });

                    if (plusBtn) plusBtn.addEventListener('click', () => {
                        let newVal = getValue() + 1;
                        if (!hasMax || newVal <= max) {
                            input.value = newVal.toString();
                        }
                    });
                }
            }
        });

    }
}

class I18nManager {
    constructor({
                    defaultLang = 'en',
                    langPath = 'assets/data/translations/',
                    langImageSelector = '#selected-language-image',
                    langCodeSelector = '#selected-language-code',
                    translationKeySelector = '[data-lang]',
                    translationKeyAttribute = 'data-lang',
                    languageSelector = '[data-translator-lang]'
                } = {}) {
        this.selectedLanguage = sessionStorage.getItem('__Simple_LANG__') || defaultLang;
        this.langPath = langPath;
        this.langImageSelector = langImageSelector;
        this.langCodeSelector = langCodeSelector;
        this.translationKeySelector = translationKeySelector;
        this.translationKeyAttribute = translationKeyAttribute;
        this.languageSelector = languageSelector;

        this.selectedLanguageImage = document.querySelector(this.langImageSelector);
        this.selectedLanguageCode = document.querySelector(this.langCodeSelector);
        this.languageButtons = document.querySelectorAll(this.languageSelector);
    }

    async init() {
        await this.applyTranslations();
        this.updateSelectedImage();
        this.updateSelectedCode();
        this.bindLanguageSwitchers();
    }

    async loadTranslations() {
        try {
            const response = await fetch(`${this.langPath}${this.selectedLanguage}.json`);
            if (!response.ok) throw new Error(`Failed to load ${this.selectedLanguage}.json`);
            return await response.json();
        } catch (err) {
            console.error('Translation load error:', err);
            return {};
        }
    }

    async applyTranslations() {
        const translations = await this.loadTranslations();

        const getNestedValue = (obj, keyPath) => {
            return keyPath.split('.').reduce((acc, key) => acc?.[key] ?? null, obj);
        };

        document.querySelectorAll(this.translationKeySelector).forEach(el => {
            const key = el.getAttribute(this.translationKeyAttribute);
            const value = getNestedValue(translations, key);
            if (value) {
                el.innerHTML = value;
            } else {
                console.warn(`Missing translation for key: ${key}`);
            }
        });
    }

    setLanguage(lang) {
        this.selectedLanguage = lang;
        localStorage.setItem('__Simple_LANG__', lang);
        this.applyTranslations();
        this.updateSelectedImage();
        this.updateSelectedCode();
    }

    updateSelectedImage() {
        const activeImage = document.querySelector(`[data-translator-lang="${this.selectedLanguage}"] [data-translator-image]`);
        if (activeImage && this.selectedLanguageImage) {
            this.selectedLanguageImage.src = activeImage.src;
        }
    }

    updateSelectedCode() {
        if (this.selectedLanguageCode) {
            this.selectedLanguageCode.textContent = this.selectedLanguage.toUpperCase();
        }
    }

    bindLanguageSwitchers() {
        this.languageButtons.forEach(button => {
            button.addEventListener('click', () => {
                const lang = button.dataset.translatorLang;
                if (lang && lang !== this.selectedLanguage) {
                    this.setLanguage(lang);
                }
            });
        });
    }
}

document.addEventListener('DOMContentLoaded', function (e) {
    new App().init();
    new LayoutCustomizer().init();
    new Plugins().init();
    new I18nManager().init();
});


//
// ------------------------------ Required helpers ------------------------------
//

// Chart Color
const ins = (v, a = 1) => {
    const val = getComputedStyle(document.documentElement).getPropertyValue(`--ins-${v}`).trim();
    return v.includes('-rgb') ? `rgba(${val}, ${a})` : val;
};

// Debounce function for performance
function debounce(func, wait) {
    let timeout;
    return function () {
        clearTimeout(timeout);
        timeout = setTimeout(func, wait);
    };
}


// Updating charts on skin and theme change

// For ChartJs
class CustomChartJs {
    static instances = [];

    constructor({selector, options = () => ({})}) {
        if (!selector) {
            console.warn("CustomChartJs: 'selector' is required.");
            return;
        }

        this.selector = selector;
        this.getOptions = typeof options === 'function' ? options : () => options;
        this.element = null;
        this.chart = null;

        try {
            this.render();
            CustomChartJs.instances.push(this);
        } catch (err) {
            console.error("CustomChartJs: Initialization error", err);
        }
    }

    static getDefaultOptions() {
        const bodyFont = getComputedStyle(document.body).fontFamily.trim();

        return {
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: {
                    top: -10
                }
            },
            scales: {
                x: {
                    ticks: {
                        font: {family: bodyFont},
                        color: ins('secondary-color'),
                        display: true,
                        drawTicks: true
                    },
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    border: {
                        display: false // Hides bottom X axis line
                    }
                },
                y: {
                    ticks: {
                        font: {family: bodyFont},
                        color: ins('secondary-color')
                    },
                    grid: {
                        display: true,              // Keeps horizontal lines
                        drawBorder: false,          // Hides Y axis border line
                        color: ins('chart-border-color'),
                        lineWidth: 1
                    },
                    border: {
                        display: false,             // Hides Y axis line (left)
                        dash: [5, 5]
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        font: {family: bodyFont},
                        color: ins('secondary-color'),
                        usePointStyle: true,      // Show circles instead of default box
                        pointStyle: 'circle',     // Circle shape
                        boxWidth: 8,             // Circle size
                        boxHeight: 8,            // (optional) same as width by default
                        padding: 15,              // Space between legend items
                    }
                },
                tooltip: {
                    enabled: true,
                    titleFont: {family: bodyFont},
                    bodyFont: {family: bodyFont}
                }
            }
        };

    }

    render() {
        try {
            this.element = this.selector instanceof HTMLElement
                ? this.selector
                : document.querySelector(this.selector);

            if (!this.element) {
                console.warn(`CustomChartJs: No element found for selector '${this.selector}'`);
                return;
            }

            // Destroy existing chart instance if present
            if (this.chart) {
                this.chart.destroy();
            }

            const {type, data, options, plugins} = this.getOptions();

            // Merge dynamic default options with instance-specific options
            this.chart = new Chart(this.element, {
                type: type || 'bar',
                data,
                options: {
                    ...structuredClone(CustomChartJs.getDefaultOptions()),
                    ...(options || {})
                },
                plugins: plugins || []
            });

            // Resize listener
            window.addEventListener('resize', debounce(() => {
                if (this.chart) {
                    this.chart.resize();
                }
            }, 200));
        } catch (err) {
            console.error(`CustomChartJs: Render error for '${this.selector}'`, err);
        }
    }

    static rerenderAll() {
        for (const instance of CustomChartJs.instances) {
            instance.render();
        }
    }

    static reSizeAll() {
        for (const instance of CustomChartJs.instances) {
            if (instance.chart) {
                instance.chart.resize();
            }
        }
    }

    static destroyAll() {
        for (const instance of CustomChartJs.instances) {
            if (instance.chart) {
                instance.chart.destroy();
            }
        }
        CustomChartJs.instances = [];
    }
}

// Track instances
CustomChartJs.instances = [];

// Observe theme changes
const themeObserver = new MutationObserver(() => {
    CustomChartJs.rerenderAll();
});

themeObserver.observe(document.documentElement, {
    attributes: true,
    attributeFilter: ['data-skin', 'data-bs-theme']
});

// Observe menu size changes
const menuObserver = new MutationObserver(() => {
    requestAnimationFrame(() => {
        CustomChartJs.reSizeAll();
    });
});

menuObserver.observe(document.documentElement, {
    attributes: true,
    attributeFilter: ['data-sidenav-size']
});
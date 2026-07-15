/**
 * Template Name: Simple - Admin & Dashboard Template
 * By (Author): Coderthemes
 * Module/App (File Name): Config
 * Version: 3.0.0
 */

(function () {
    const html = document.documentElement;
    const storageKey = "__SIMPLE_CONFIG__";
    const savedConfig = sessionStorage.getItem(storageKey);

    // Default Configuration
    const defaultConfig = {
        skin: "shadcn",               // Theme skin: default, two, three, four, five, six
        monochrome: false,             // Enable monochrome mode
        theme: "light",                // App theme: light or dark
        sidenav: {
            size: "default",           // Sidebar size: default, collapse, or offcanvas
            color: "light",            // Sidebar color: light, dark, or gray
            user: true,                // Show user info in sidebar: true or false
        },
        topbar: {
            color: "light",            // Topbar color: light, dark, or gray
        },
        layout: {
            position: "fixed",         // Layout position: fixed or scrollable
        },
    };

    // Build config from HTML attributes
    const htmlConfig = {
        skin: html.getAttribute("data-skin") || defaultConfig.skin,
        monochrome: html.classList.contains("monochrome") || defaultConfig.monochrome,
        theme: html.getAttribute("data-bs-theme") || defaultConfig.theme,
        sidenav: {
            color: html.getAttribute("data-sidenav-color") || defaultConfig.sidenav.color,
            size: html.getAttribute("data-sidenav-size") || defaultConfig.sidenav.size,
            user: html.hasAttribute("data-sidenav-user") || defaultConfig.sidenav.user,
        },
        topbar: {
            color: html.getAttribute("data-topbar-color") || defaultConfig.topbar.color,
        },
        layout: {
            position: html.getAttribute("data-layout-position") || defaultConfig.layout.position,
        },
    };

    // Save merged config as defaults globally
    window.defaultConfig = structuredClone(htmlConfig);

    // Load from session if exists
    let config = savedConfig ? JSON.parse(savedConfig) : htmlConfig;
    window.config = config;

    // Apply layout attributes immediately
    html.setAttribute("data-skin", config.skin);
    html.setAttribute("data-bs-theme", config.theme);
    html.setAttribute("data-sidenav-color", config.sidenav.color);
    html.setAttribute("data-topbar-color", config.topbar.color);
    html.setAttribute("data-layout-position", config.layout.position);
    html.classList.toggle("monochrome", config.monochrome);

    if (config.sidenav.size) {
        let size = config.sidenav.size;

        if (window.innerWidth <= 1140) {
            size = "offcanvas";
        }

        html.setAttribute("data-sidenav-size", size);

        if (config.sidenav.user === true) {
            html.setAttribute("data-sidenav-user", "true");
        } else {
            html.removeAttribute("data-sidenav-user");
        }
    }
})();

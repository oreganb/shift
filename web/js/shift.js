/**
 * SHIFT global search — client-side with AJAX fallback to api/search.php
 */
(function () {
    'use strict';

    let searchIndex = null;

    async function loadIndex() {
        if (searchIndex) return searchIndex;
        try {
            const r = await fetch('data/json/search-index.json');
            const data = await r.json();
            searchIndex = data.items || [];
        } catch (e) {
            searchIndex = [];
        }
        return searchIndex;
    }

    function renderResults(items, container) {
        if (!items.length) {
            container.innerHTML = '<p class="text-muted px-3">No results.</p>';
            return;
        }
        container.innerHTML = items.map(item => `
            <a href="${item.url}" class="search-result-item">
                <span class="search-result-kind">${item.kind || 'item'}</span>
                <div class="fw-semibold">${escapeHtml(item.label || item.name)}</div>
                <div class="small text-muted">${escapeHtml((item.definition || '').slice(0, 120))}</div>
            </a>
        `).join('');
    }

    function escapeHtml(s) {
        const d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    async function search(q) {
        const container = document.getElementById('searchResults');
        if (!container || !q.trim()) {
            if (container) container.innerHTML = '';
            return;
        }

        try {
            const r = await fetch('api/search.php?q=' + encodeURIComponent(q));
            const data = await r.json();
            renderResults(data.results || [], container);
        } catch (e) {
            const index = await loadIndex();
            const ql = q.toLowerCase();
            const filtered = index.filter(item => {
                const hay = ((item.label || '') + ' ' + (item.name || '') + ' ' + (item.definition || '')).toLowerCase();
                return hay.includes(ql);
            }).slice(0, 20);
            renderResults(filtered, container);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('globalSearchInput');
        const modal = document.getElementById('searchModal');

        if (input) {
            let timer;
            input.addEventListener('input', function () {
                clearTimeout(timer);
                timer = setTimeout(() => search(input.value), 200);
            });
        }

        document.addEventListener('keydown', function (e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                const m = bootstrap.Modal.getOrCreateInstance(document.getElementById('searchModal'));
                m.show();
                setTimeout(() => input && input.focus(), 300);
            }
            if ((e.ctrlKey || e.metaKey) && e.key === 'g') {
                window.location.href = 'graph.php';
            }
        });
    });
})();

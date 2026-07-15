/**
 * SHIFT Knowledge Graph viewer — tree, note pane, backlinks, force graph
 */
(function () {
    'use strict';

    let vaultData = null;
    let graphData = null;
    let currentSlug = null;
    let globalGraphMode = false;

    const simNodes = [];
    const simEdges = [];

    async function loadVault() {
        const [treeR, graphR] = await Promise.all([
            fetch('api/vault-tree.php'),
            fetch('api/vault-graph.php')
        ]);
        const treeData = await treeR.json();
        graphData = await graphR.json();
        vaultData = treeData;
        renderTree(treeData.tree || []);
    }

    function renderTree(nodes, container = null, filter = '') {
        const el = container || document.getElementById('kgTree');
        if (!el) return;
        el.innerHTML = '';
        const fl = filter.toLowerCase();

        function walk(items, depth) {
            items.forEach(item => {
                if (item.type === 'folder') {
                    const folder = document.createElement('div');
                    folder.className = 'kg-tree-folder';
                    folder.style.paddingLeft = (depth * 8 + 8) + 'px';
                    folder.textContent = item.name.replace(/_/g, ' ');
                    el.appendChild(folder);
                    walk(item.children || [], depth + 1);
                } else if (item.type === 'file') {
                    if (fl && !item.name.toLowerCase().includes(fl) && !item.slug.toLowerCase().includes(fl)) return;
                    const row = document.createElement('div');
                    row.className = 'kg-tree-item';
                    row.style.paddingLeft = (depth * 8 + 12) + 'px';
                    row.textContent = item.name;
                    row.dataset.slug = item.slug;
                    row.addEventListener('click', () => loadNote(item.slug));
                    el.appendChild(row);
                }
            });
        }
        walk(nodes, 0);
    }

    async function loadNote(slug) {
        currentSlug = slug;
        document.querySelectorAll('.kg-tree-item').forEach(el => {
            el.classList.toggle('active', el.dataset.slug === slug);
        });

        const r = await fetch('api/vault-note.php?slug=' + encodeURIComponent(slug));
        const note = await r.json();
        if (note.error) return;

        document.getElementById('kgNoteMeta').innerHTML = Object.entries(note.meta || {})
            .filter(([k]) => typeof note.meta[k] === 'string')
            .map(([k, v]) => `<span class="me-3"><strong>${k}:</strong> ${v}</span>`)
            .join('');

        document.getElementById('kgNoteContent').innerHTML = `<h4>${note.title}</h4>` + note.html;

        const bl = document.getElementById('kgBacklinks');
        bl.innerHTML = (note.backlinks || []).length
            ? note.backlinks.map(b => `
                <div class="kg-backlink">
                    <a href="#" data-slug="${b.from}">${b.title}</a>
                    <div class="small text-muted">${(b.context || '').slice(0, 80)}…</div>
                </div>`).join('')
            : '<p class="small text-muted">No backlinks.</p>';

        bl.querySelectorAll('[data-slug]').forEach(a => {
            a.addEventListener('click', e => { e.preventDefault(); loadNote(a.dataset.slug); });
        });

        document.getElementById('kgNoteContent').querySelectorAll('.wiki-link').forEach(link => {
            link.addEventListener('click', e => {
                const href = link.getAttribute('href');
                const m = href && href.match(/note=([^&]+)/);
                if (m) { e.preventDefault(); loadNote(decodeURIComponent(m[1])); }
            });
        });

        drawLocalGraph(slug);
        history.replaceState(null, '', 'graph.php?note=' + encodeURIComponent(slug));
    }

    function drawLocalGraph(slug) {
        const canvas = document.getElementById('kgLocalGraph');
        if (!canvas || !graphData) return;
        const ctx = canvas.getContext('2d');
        const w = canvas.width = canvas.offsetWidth;
        const h = canvas.height = 180;

        fetch('api/vault-graph.php?focus=' + encodeURIComponent(slug))
            .then(r => r.json())
            .then(data => {
                ctx.clearRect(0, 0, w, h);
                const nodes = data.nodes || [];
                const edges = data.edges || [];
                const cx = w / 2, cy = h / 2;
                const positions = {};
                nodes.forEach((n, i) => {
                    const angle = (i / nodes.length) * Math.PI * 2;
                    const r = n.id === slug ? 0 : 60;
                    positions[n.id] = {
                        x: cx + Math.cos(angle) * r,
                        y: cy + Math.sin(angle) * r,
                        color: n.color || '#1F3D7A',
                        title: n.title
                    };
                });
                edges.forEach(e => {
                    const a = positions[e.source], b = positions[e.target];
                    if (!a || !b) return;
                    ctx.strokeStyle = 'rgba(46,98,176,0.4)';
                    ctx.beginPath();
                    ctx.moveTo(a.x, a.y);
                    ctx.lineTo(b.x, b.y);
                    ctx.stroke();
                });
                Object.entries(positions).forEach(([id, p]) => {
                    ctx.beginPath();
                    ctx.arc(p.x, p.y, id === slug ? 10 : 6, 0, Math.PI * 2);
                    ctx.fillStyle = p.color;
                    ctx.fill();
                });
            });
    }

    function initGlobalGraph() {
        const canvas = document.getElementById('kgGraphCanvas');
        if (!canvas || !graphData) return;
        canvas.classList.remove('d-none');
        document.getElementById('kgNoteView').classList.add('d-none');

        const ctx = canvas.getContext('2d');
        let w, h;
        const nodes = graphData.nodes.map(n => ({
            ...n, x: Math.random() * 800, y: Math.random() * 600,
            vx: 0, vy: 0, r: Math.min(14, Math.max(4, 4 + n.degree * 0.3))
        }));
        const nodeMap = Object.fromEntries(nodes.map(n => [n.id, n]));
        const edges = graphData.edges.filter(e => nodeMap[e.source] && nodeMap[e.target]);

        function resize() {
            w = canvas.width = canvas.offsetWidth;
            h = canvas.height = canvas.offsetHeight || 500;
        }
        resize();
        window.addEventListener('resize', resize);

        let hovered = null;

        canvas.addEventListener('mousemove', e => {
            const rect = canvas.getBoundingClientRect();
            const mx = e.clientX - rect.left, my = e.clientY - rect.top;
            hovered = nodes.find(n => (mx - n.x) ** 2 + (my - n.y) ** 2 < n.r * n.r * 4) || null;
            canvas.style.cursor = hovered ? 'pointer' : 'default';
        });

        canvas.addEventListener('click', () => {
            if (hovered) {
                globalGraphMode = false;
                document.getElementById('kgGraphCanvas').classList.add('d-none');
                document.getElementById('kgNoteView').classList.remove('d-none');
                document.getElementById('btnViewNote').classList.add('active');
                document.getElementById('btnViewGraph').classList.remove('active');
                loadNote(hovered.id);
            }
        });

        function tick() {
            if (!globalGraphMode) return;
            resize();
            const cx = w / 2, cy = h / 2;
            nodes.forEach(n => {
                n.vx += (cx - n.x) * 0.0003;
                n.vy += (cy - n.y) * 0.0003;
                nodes.forEach(m => {
                    if (m === n) return;
                    const dx = n.x - m.x, dy = n.y - m.y;
                    const dist = Math.sqrt(dx * dx + dy * dy) || 1;
                    if (dist < 120) {
                        n.vx += dx / dist * 0.5;
                        n.vy += dy / dist * 0.5;
                    }
                });
            });
            edges.forEach(e => {
                const a = nodeMap[e.source], b = nodeMap[e.target];
                const dx = b.x - a.x, dy = b.y - a.y;
                const dist = Math.sqrt(dx * dx + dy * dy) || 1;
                const f = (dist - 80) * 0.002;
                a.vx += dx / dist * f; a.vy += dy / dist * f;
                b.vx -= dx / dist * f; b.vy -= dy / dist * f;
            });
            nodes.forEach(n => {
                n.x += n.vx * 0.3; n.y += n.vy * 0.3;
                n.vx *= 0.85; n.vy *= 0.85;
                n.x = Math.max(n.r, Math.min(w - n.r, n.x));
                n.y = Math.max(n.r, Math.min(h - n.r, n.y));
            });

            ctx.clearRect(0, 0, w, h);
            edges.forEach(e => {
                const a = nodeMap[e.source], b = nodeMap[e.target];
                const fade = hovered && hovered.id !== e.source && hovered.id !== e.target &&
                    !edges.some(ed => (ed.source === hovered.id || ed.target === hovered.id) &&
                        (ed.source === e.source || ed.target === e.source || ed.source === e.target || ed.target === e.target));
                ctx.strokeStyle = fade ? 'rgba(46,98,176,0.08)' : 'rgba(46,98,176,0.25)';
                ctx.beginPath();
                ctx.moveTo(a.x, a.y);
                ctx.lineTo(b.x, b.y);
                ctx.stroke();
            });
            nodes.forEach(n => {
                const isH = hovered && (n.id === hovered.id || edges.some(e =>
                    (e.source === hovered.id && e.target === n.id) || (e.target === hovered.id && e.source === n.id)));
                const fade = hovered && !isH;
                ctx.globalAlpha = fade ? 0.15 : 1;
                ctx.beginPath();
                ctx.arc(n.x, n.y, n.r, 0, Math.PI * 2);
                ctx.fillStyle = n.color || '#1F3D7A';
                ctx.fill();
                if (isH || n.degree > 8) {
                    ctx.fillStyle = '#1A2440';
                    ctx.font = '10px Inter, sans-serif';
                    ctx.fillText(n.title.slice(0, 20), n.x + n.r + 2, n.y + 3);
                }
                ctx.globalAlpha = 1;
            });
            requestAnimationFrame(tick);
        }
        tick();
    }

    document.addEventListener('DOMContentLoaded', async function () {
        await loadVault();

        document.getElementById('kgTreeSearch')?.addEventListener('input', e => {
            renderTree(vaultData.tree || [], null, e.target.value);
        });

        document.getElementById('btnViewGraph')?.addEventListener('click', () => {
            globalGraphMode = true;
            document.getElementById('btnViewGraph').classList.add('active');
            document.getElementById('btnViewNote').classList.remove('active');
            initGlobalGraph();
        });

        document.getElementById('btnViewNote')?.addEventListener('click', () => {
            globalGraphMode = false;
            document.getElementById('kgGraphCanvas')?.classList.add('d-none');
            document.getElementById('kgNoteView')?.classList.remove('d-none');
            document.getElementById('btnViewNote').classList.add('active');
            document.getElementById('btnViewGraph').classList.remove('active');
        });

        const init = window.SHIFT_KG || {};
        if (init.initialNote) {
            loadNote(init.initialNote);
        } else if (init.initialFocus) {
            const search = init.initialFocus.toLowerCase();
            for (const [slug, note] of Object.entries((await (await fetch('data/json/vault.json')).json()).notes || {})) {
                if (note.title.toLowerCase().includes(search) || slug.toLowerCase().includes(search)) {
                    loadNote(slug);
                    break;
                }
            }
        }
    });
})();

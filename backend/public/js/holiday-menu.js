/**
 * holiday-menu.js
 * ───────────────
 * Admin UI for the auto-generated Holiday Packages menu.
 *
 * Capabilities:
 *  • Drag-drop reorder for regions, states, and locations
 *  • Toggle visibility (show / hide) per node
 *  • Expand / collapse region and state groups
 *
 * Reads config from: <script id="holidayMenuConfig" type="application/json">
 */

document.addEventListener('DOMContentLoaded', function () {

    /* ── Config ──────────────────────────────────────────────────── */
    const configEl = document.getElementById('holidayMenuConfig');
    if (!configEl) { console.error('[HolidayMenu] Config element not found.'); return; }

    const CFG = JSON.parse(configEl.textContent);
    // CFG: { reorderUrl, toggleUrl, csrfToken }

    const headers = {
        'X-CSRF-TOKEN': CFG.csrfToken,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    };

    /* ── Save indicator ──────────────────────────────────────────── */
    const saveIndicator = document.getElementById('hmSaveIndicator');

    function setSaving() {
        if (!saveIndicator) return;
        saveIndicator.innerHTML = '<i class="fas fa-spinner fa-spin text-warning me-1"></i>Saving…';
    }

    function setSaved() {
        if (!saveIndicator) return;
        saveIndicator.innerHTML = '<i class="fas fa-cloud text-success me-1"></i>All saved';
    }

    function setSaveError() {
        if (!saveIndicator) return;
        saveIndicator.innerHTML = '<i class="fas fa-exclamation-triangle text-danger me-1"></i>Save failed';
    }

    /* ── Toastr helper ───────────────────────────────────────────── */
    function toast(type, msg) {
        if (typeof toastr !== 'undefined') {
            toastr[type](msg);
        }
    }

    /* ══════════════════════════════════════════════════════════════
       DRAG-DROP REORDER
       ══════════════════════════════════════════════════════════════ */

    let reorderTimer = null;

    function debounceReorder(type, listEl) {
        clearTimeout(reorderTimer);
        reorderTimer = setTimeout(() => saveOrder(type, listEl), 700);
    }

    function saveOrder(type, listEl) {
        const ids = Array.from(listEl.children)
            .map(el => parseInt(el.dataset.id, 10))
            .filter(id => !isNaN(id));

        if (!ids.length) return;

        setSaving();

        fetch(CFG.reorderUrl, {
            method: 'POST',
            headers,
            body: JSON.stringify({ type, ids }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) { setSaved(); }
            else { setSaveError(); toast('error', 'Reorder failed.'); }
        })
        .catch(() => { setSaveError(); toast('error', 'Network error.'); });
    }

    /* Regions list */
    const regionList = document.getElementById('hmRegionList');
    if (regionList) {
        Sortable.create(regionList, {
            handle: '.hm-handle[data-level="region"]',
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: () => debounceReorder('region', regionList),
        });
    }

    /* State lists (one per region) */
    document.querySelectorAll('[data-state-list]').forEach(stateList => {
        Sortable.create(stateList, {
            handle: '.hm-handle[data-level="state"]',
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: () => debounceReorder('state', stateList),
        });
    });

    /* Location lists (one per state) */
    document.querySelectorAll('[data-location-list]').forEach(locList => {
        Sortable.create(locList, {
            handle: '.hm-handle[data-level="location"]',
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: () => debounceReorder('location', locList),
        });
    });

    /* ══════════════════════════════════════════════════════════════
       TOGGLE VISIBILITY
       ══════════════════════════════════════════════════════════════ */

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-toggle-vis]');
        if (!btn) return;

        const type = btn.dataset.type;
        const id   = parseInt(btn.dataset.id, 10);

        btn.disabled = true;

        fetch(CFG.toggleUrl, {
            method: 'POST',
            headers,
            body: JSON.stringify({ type, id }),
        })
        .then(r => r.json())
        .then(data => {
            if (!data.success) throw new Error(data.message || 'Toggle failed');

            const visible = data.is_visible === 1 || data.is_visible === true;

            // Swap icon and class on the button
            const icon = btn.querySelector('i');
            if (icon) {
                icon.className = visible ? 'fas fa-eye' : 'fas fa-eye-slash';
            }
            btn.classList.toggle('hm-vis',   visible);
            btn.classList.toggle('hm-invis', !visible);
            btn.title = visible ? 'Hide' : 'Show';

            // Dim the node wrapper
            const wrap = btn.closest('[data-node-wrap]');
            if (wrap) {
                wrap.classList.toggle('hidden-node', !visible);
            }

            // Update badge
            const badge = wrap?.querySelector('[data-vis-badge]');
            if (badge) {
                badge.textContent = visible ? '' : 'Hidden';
                badge.style.display = visible ? 'none' : '';
            }

            toast(visible ? 'success' : 'warning', data.message);
        })
        .catch(err => {
            toast('error', err.message || 'Network error.');
        })
        .finally(() => { btn.disabled = false; });
    });

    /* ══════════════════════════════════════════════════════════════
       EXPAND / COLLAPSE
       ══════════════════════════════════════════════════════════════ */

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-collapse-btn]');
        if (!btn) return;

        const targetId = btn.dataset.collapseBtn;
        const target   = document.getElementById(targetId);
        if (!target) return;

        const icon = btn.querySelector('i');
        const isOpen = !target.classList.contains('collapsed');

        target.classList.toggle('collapsed', isOpen);
        if (icon) {
            icon.style.transform = isOpen ? 'rotate(-90deg)' : 'rotate(0deg)';
        }
    });

});

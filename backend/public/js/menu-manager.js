/**
 * menu-manager.js  ·  Menu Management System
 * ─────────────────────────────────────────────
 * Depends on:  SortableJS, Bootstrap 5, Toastr, SweetAlert2
 *
 * Config is read from <script id="menuBuilderConfig" type="application/json">
 *
 * Supports item content types:
 *   normal         → standard link/dropdown (custom|page|package|location|etc.)
 *   mega_menu      → full-width dropdown from Region→State→City tree or custom menu
 *   menu_reference → inline another menu's items as children
 */

document.addEventListener('DOMContentLoaded', function () {

    // ─────────────────────────────────────────────────────────────
    // 1. CONFIG
    // ─────────────────────────────────────────────────────────────
    const configEl = document.getElementById('menuBuilderConfig');
    if (!configEl) { console.error('[MenuManager] Config script tag not found.'); return; }

    let CFG;
    try { CFG = JSON.parse(configEl.textContent); }
    catch (e) { console.error('[MenuManager] Failed to parse config:', e); return; }

    const CSRF = CFG.csrfToken;
    if (!CSRF) { console.error('[MenuManager] CSRF token missing.'); return; }

    // ─────────────────────────────────────────────────────────────
    // 2. ELEMENT REFERENCES
    // ─────────────────────────────────────────────────────────────
    const itemTree         = document.getElementById('itemTree');
    const btnAddItem       = document.getElementById('btnAddItem');
    const btnNewMenu       = document.getElementById('btnNewMenu');
    const itemModalEl      = document.getElementById('itemModal');
    const newMenuModalEl   = document.getElementById('newMenuModal');
    const itemForm         = document.getElementById('itemForm');
    const modalTitle       = document.getElementById('modalTitle');
    const btnSaveItem      = document.getElementById('btnSaveItem');
    const btnSaveLabel     = document.getElementById('btnSaveLabel');
    const saveSpinner      = document.getElementById('saveSpinner');
    const fldItemId        = document.getElementById('fldItemId');
    const fldParentId      = document.getElementById('fldParentId');
    const fldTitle         = document.getElementById('fldTitle');
    const fldTarget        = document.getElementById('fldTarget');
    const fldUrl           = document.getElementById('fldUrl');
    const fldLinked        = document.getElementById('fldLinked');
    const fldParentSelect  = document.getElementById('fldParentSelect');
    const linkedLabel      = document.getElementById('linkedLabel');
    const linkedHint       = document.getElementById('linkedHint');
    const saveIndicator    = document.getElementById('saveIndicator');
    const emptyNotice      = document.getElementById('emptyNotice');
    const typeGrid         = document.getElementById('typeGrid');
    const fieldUrl         = document.getElementById('fieldUrl');
    const fieldLinked      = document.getElementById('fieldLinked');
    const fieldMenuRefInfo = document.getElementById('fieldMenuRefInfo');
    const sectionNormalType= document.getElementById('sectionNormalType');
    // New Menu
    const fldNewMenuName   = document.getElementById('fldNewMenuName');
    const btnSaveNewMenu   = document.getElementById('btnSaveNewMenu');
    const newMenuSpinner   = document.getElementById('newMenuSpinner');
    const errNewMenuName   = document.getElementById('errNewMenuName');
    // Content-type toggle
    const contentTypeGroup = document.getElementById('contentTypeGroup');
    // Mega menu panel
    const megaMenuPanel    = document.getElementById('megaMenuPanel');
    const megaSourceGroup  = document.getElementById('megaSourceGroup');
    const megaModeGroup    = document.getElementById('megaModeGroup');
    const megaAutoSection  = document.getElementById('megaAutoSection');
    const megaCustomSection= document.getElementById('megaCustomSection');
    const fldMegaUrl       = document.getElementById('fldMegaUrl');
    const fldMegaRegionIds = document.getElementById('fldMegaRegionIds');
    const fldMegaStateIds  = document.getElementById('fldMegaStateIds');
    const fldMegaActiveOnly= document.getElementById('fldMegaActiveOnly');
    const fldMegaPackageOnly=document.getElementById('fldMegaPackageOnly');
    const fldMegaManageCityOnly=document.getElementById('fldMegaManageCityOnly');
    const fldMegaLinkedMenu= document.getElementById('fldMegaLinkedMenu');
    const megaBannerToggle = document.getElementById('megaBannerToggle');
    const megaBannerFields = document.getElementById('megaBannerFields');
    const megaBannerChevron= document.getElementById('megaBannerChevron');
    const fldBannerImage   = document.getElementById('fldBannerImage');
    const fldBannerAlt     = document.getElementById('fldBannerAlt');
    const fldBannerTitle   = document.getElementById('fldBannerTitle');
    const fldBannerDesc    = document.getElementById('fldBannerDesc');
    const fldBannerCtaText = document.getElementById('fldBannerCtaText');
    const fldBannerCtaUrl  = document.getElementById('fldBannerCtaUrl');

    if (!itemModalEl || !btnAddItem || !itemForm) {
        console.error('[MenuManager] Critical DOM elements missing.');
        return;
    }

    const itemModal    = new bootstrap.Modal(itemModalEl,   { backdrop: true, keyboard: true });
    const newMenuModal = newMenuModalEl
        ? new bootstrap.Modal(newMenuModalEl, { backdrop: true, keyboard: true })
        : null;

    // ─────────────────────────────────────────────────────────────
    // 3. STATE
    // ─────────────────────────────────────────────────────────────
    let isEditing         = false;
    let activeType        = 'custom';
    let activeContentType = 'normal';   // normal | mega_menu | menu_reference
    let activeMegaSource  = 'auto';     // auto | custom_menu
    let activeMegaMode    = 'region_state_city';
    let linkedCache       = {};
    let megaRegionsCache  = null;
    let megaStatesCache   = null;
    let megaMenusCache    = null;
    let reorderDebounce   = null;

    const IS_AUTO_MODE = !! CFG.isAutoDisplay;
    if (IS_AUTO_MODE && btnAddItem) {
        btnAddItem.title   = 'Auto-display mode active — manual items are ignored by the API.';
        btnAddItem.style.opacity = '0.5';
    }

    // Types that need a linked-record select (normal mode)
    const LINKED_TYPES = ['page', 'package', 'location', 'region', 'state', 'category', 'menu_reference'];

    const LINKED_LABELS = {
        page:           'Select CMS Page',
        package:        'Select Package',
        location:       'Select City / Location',
        region:         'Select Region',
        state:          'Select State',
        category:       'Select Category',
        menu_reference: 'Select Menu to Reference',
    };

    // ─────────────────────────────────────────────────────────────
    // 4. CONTENT TYPE SWITCHING
    // ─────────────────────────────────────────────────────────────

    function activateContentType(ctype) {
        activeContentType = ctype;

        // Highlight the button
        contentTypeGroup.querySelectorAll('.content-type-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.ctype === ctype);
        });

        const isNormal = ctype === 'normal';
        const isMega   = ctype === 'mega_menu';
        const isRef    = ctype === 'menu_reference';

        // Show/hide sections
        sectionNormalType?.classList.toggle('d-none', !isNormal);
        megaMenuPanel?.classList.toggle('d-none',     !isMega);

        if (isNormal) {
            // Restore current normal type layout
            activateType(activeType);
        } else if (isMega) {
            // Mega menu: hide normal URL/linked sections, show mega panel
            fieldUrl?.classList.add('d-none');
            fieldLinked?.classList.add('d-none');
            fieldMenuRefInfo?.classList.add('d-none');
            // Load region/state options for mega filters
            loadMegaRegionOptions();
            loadMegaStateOptions();
        } else if (isRef) {
            // Menu Reference: treat like normal type=menu_reference
            fieldUrl?.classList.add('d-none');
            fieldLinked?.classList.remove('d-none');
            fieldMenuRefInfo?.classList.remove('d-none');
            // Force type to menu_reference
            activeType = 'menu_reference';
            typeGrid?.querySelectorAll('.type-btn').forEach(b => b.classList.remove('active'));
            loadLinkedItems('menu_reference', null, `?exclude_menu=${CFG.menuId}`);
        }
    }

    contentTypeGroup?.addEventListener('click', function (e) {
        const btn = e.target.closest('.content-type-btn');
        if (btn) activateContentType(btn.dataset.ctype);
    });

    // ─────────────────────────────────────────────────────────────
    // 5. MEGA SOURCE / MODE SWITCHING
    // ─────────────────────────────────────────────────────────────

    function activateMegaSource(source) {
        activeMegaSource = source;
        megaSourceGroup?.querySelectorAll('.mega-source-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.source === source);
        });
        const isAuto   = source === 'auto';
        megaAutoSection?.classList.toggle('d-none',   !isAuto);
        megaCustomSection?.classList.toggle('d-none', isAuto);
        if (!isAuto) loadMegaMenuOptions();
    }

    function activateMegaMode(mode) {
        activeMegaMode = mode;
        megaModeGroup?.querySelectorAll('.mega-mode-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.mode === mode);
        });
    }

    megaSourceGroup?.addEventListener('click', function (e) {
        const btn = e.target.closest('.mega-source-btn');
        if (btn) activateMegaSource(btn.dataset.source);
    });

    megaModeGroup?.addEventListener('click', function (e) {
        const btn = e.target.closest('.mega-mode-btn');
        if (btn) activateMegaMode(btn.dataset.mode);
    });

    // Banner accordion
    megaBannerToggle?.addEventListener('click', function () {
        const open = megaBannerFields?.classList.toggle('d-none');
        if (megaBannerChevron) {
            megaBannerChevron.style.transform = open ? '' : 'rotate(180deg)';
        }
    });

    // ─────────────────────────────────────────────────────────────
    // 6. MEGA AJAX LOADERS
    // ─────────────────────────────────────────────────────────────

    function loadMegaRegionOptions() {
        if (megaRegionsCache) return;
        fetch(`${CFG.availableUrl}/region`, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } })
        .then(r => r.json())
        .then(d => {
            megaRegionsCache = d.items || [];
            if (fldMegaRegionIds) {
                fldMegaRegionIds.innerHTML = megaRegionsCache
                    .map(r => `<option value="${r.id}">${escHtml(r.label)}</option>`)
                    .join('');
            }
        })
        .catch(err => console.error('[MegaMenu] loadRegions:', err));
    }

    function loadMegaStateOptions() {
        if (megaStatesCache) return;
        fetch(`${CFG.availableUrl}/state`, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } })
        .then(r => r.json())
        .then(d => {
            megaStatesCache = d.items || [];
            if (fldMegaStateIds) {
                fldMegaStateIds.innerHTML = megaStatesCache
                    .map(s => `<option value="${s.id}">${escHtml(s.label)}</option>`)
                    .join('');
            }
        })
        .catch(err => console.error('[MegaMenu] loadStates:', err));
    }

    function loadMegaMenuOptions() {
        if (megaMenusCache) {
            populateMegaMenuSelect(megaMenusCache, null);
            return;
        }
        fetch(`${CFG.availableUrl}/menu_reference?exclude_menu=${CFG.menuId}`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        })
        .then(r => r.json())
        .then(d => {
            megaMenusCache = d.items || [];
            populateMegaMenuSelect(megaMenusCache, null);
        })
        .catch(err => console.error('[MegaMenu] loadMenus:', err));
    }

    function populateMegaMenuSelect(items, selectedId) {
        if (!fldMegaLinkedMenu) return;
        fldMegaLinkedMenu.innerHTML = '<option value="">— Select a Menu —</option>';
        items.forEach(item => {
            const opt = document.createElement('option');
            opt.value = item.id;
            opt.textContent = item.label + (item.slug ? '  [' + item.slug + ']' : '');
            if (parseInt(selectedId) === item.id) opt.selected = true;
            fldMegaLinkedMenu.appendChild(opt);
        });
    }

    function markMegaSelectedIds(select, ids) {
        if (!select || !ids || !ids.length) return;
        Array.from(select.options).forEach(opt => {
            opt.selected = ids.includes(parseInt(opt.value, 10));
        });
    }

    // ─────────────────────────────────────────────────────────────
    // 7. MODAL OPEN HELPERS
    // ─────────────────────────────────────────────────────────────

    function openAddModal(parentId) {
        isEditing = false;
        resetForm();

        modalTitle.innerHTML = '<i class="fas fa-plus-circle me-2 text-warning"></i>Add Menu Item';
        btnSaveLabel.textContent = 'Add Item';
        fldItemId.value   = '';
        fldParentId.value = parentId ?? '';

        if (parentId && fldParentSelect) fldParentSelect.value = parentId;

        populateParentSelect(null);
        activateContentType('normal');
        activateType('custom');
        itemModal.show();
    }

    function openEditModal(itemId) {
        isEditing = true;
        resetForm();

        modalTitle.innerHTML = '<i class="fas fa-pencil-alt me-2 text-warning"></i>Edit Menu Item';
        btnSaveLabel.textContent = 'Save Changes';

        setLoading(true);
        itemModal.show();

        fetch(`${CFG.showItemUrl}/${itemId}`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        })
        .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
        .then(d => {
            setLoading(false);
            if (!d.success) { showToast('error', d.message || 'Failed to load item.'); return; }

            const item = d.item;
            fldItemId.value = item.id;
            fldTitle.value  = item.title;
            fldTarget.value = item.target || '_self';
            document.querySelector(`input[name="statusRadio"][value="${item.status}"]`).checked = true;
            populateParentSelect(item.id);

            // Determine content type from mega_settings
            const mega = item.mega_settings || {};
            const ctype = mega.content_type || 'normal';

            if (ctype === 'mega_menu') {
                activateContentType('mega_menu');
                // Prefill mega fields
                if (fldMegaUrl) fldMegaUrl.value = item.url || '';
                activateMegaSource(mega.display_source || 'auto');
                activateMegaMode(mega.display_mode || 'region_state_city');

                // Load region/state options then mark selected
                Promise.all([loadMegaRegionOptions(), loadMegaStateOptions()]).then(() => {
                    markMegaSelectedIds(fldMegaRegionIds, mega.region_ids || []);
                    markMegaSelectedIds(fldMegaStateIds,  mega.state_ids  || []);
                });

                if (fldMegaActiveOnly)  fldMegaActiveOnly.checked  = mega.active_only  !== false;
                if (fldMegaPackageOnly) fldMegaPackageOnly.checked = !! mega.package_only;
                if (fldMegaManageCityOnly) fldMegaManageCityOnly.checked = !! mega.manage_city_only;

                if (mega.display_source === 'custom_menu' && mega.linked_menu_id) {
                    loadMegaMenuOptions();
                    setTimeout(() => {
                        if (fldMegaLinkedMenu) fldMegaLinkedMenu.value = mega.linked_menu_id;
                    }, 400);
                }

                // Prefill banner
                const banner = mega.banner || {};
                if (fldBannerImage)   fldBannerImage.value   = banner.image       || '';
                if (fldBannerAlt)     fldBannerAlt.value     = banner.alt         || '';
                if (fldBannerTitle)   fldBannerTitle.value   = banner.title       || '';
                if (fldBannerDesc)    fldBannerDesc.value    = banner.description || '';
                if (fldBannerCtaText) fldBannerCtaText.value = banner.cta_text    || '';
                if (fldBannerCtaUrl)  fldBannerCtaUrl.value  = banner.cta_url     || '';

                // Show banner section if it has content
                if (banner.image || banner.title || banner.description) {
                    megaBannerFields?.classList.remove('d-none');
                    if (megaBannerChevron) megaBannerChevron.style.transform = 'rotate(180deg)';
                }

            } else if (item.type === 'menu_reference') {
                activateContentType('menu_reference');
                loadLinkedItems('menu_reference', item.linked_id, `?exclude_menu=${CFG.menuId}`);
            } else {
                activateContentType('normal');
                activateType(item.type, item);
            }
        })
        .catch(err => { setLoading(false); showToast('error', 'Could not load item: ' + err.message); });
    }

    // ─────────────────────────────────────────────────────────────
    // 8. NORMAL TYPE SWITCHING
    // ─────────────────────────────────────────────────────────────

    function activateType(type, existingItem) {
        activeType = type;

        typeGrid?.querySelectorAll('.type-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.type === type);
        });

        fieldUrl?.classList.toggle('d-none', type !== 'custom');

        const needsLinked = LINKED_TYPES.includes(type);
        fieldLinked?.classList.toggle('d-none', !needsLinked);

        if (fieldMenuRefInfo) {
            fieldMenuRefInfo.classList.toggle('d-none', type !== 'menu_reference');
        }

        if (type === 'custom') { clearErrors(); return; }

        if (needsLinked) {
            if (linkedLabel) linkedLabel.textContent = LINKED_LABELS[type] || 'Select Record';
            if (linkedHint)  linkedHint.textContent  = '';

            const extraParams = type === 'menu_reference'
                ? `?exclude_menu=${CFG.menuId}`
                : '';

            loadLinkedItems(type, existingItem?.linked_id ?? null, extraParams);
        }

        clearErrors();
    }

    typeGrid?.addEventListener('click', function (e) {
        const btn = e.target.closest('.type-btn');
        if (btn) activateType(btn.dataset.type);
    });

    // ─────────────────────────────────────────────────────────────
    // 9. LOAD LINKED ITEMS (AJAX with cache)
    // ─────────────────────────────────────────────────────────────

    function loadLinkedItems(type, selectedId, extraParams = '') {
        if (!fldLinked) return;
        fldLinked.innerHTML = '<option value="">Loading...</option>';
        fldLinked.disabled  = true;

        const cacheKey = type === 'menu_reference' ? null : type;

        if (cacheKey && linkedCache[cacheKey]) {
            populateLinkedSelect(linkedCache[cacheKey], selectedId);
            fldLinked.disabled = false;
            return;
        }

        fetch(`${CFG.availableUrl}/${type}${extraParams}`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        })
        .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
        .then(d => {
            fldLinked.disabled = false;
            if (d.success) {
                if (cacheKey) linkedCache[cacheKey] = d.items;
                populateLinkedSelect(d.items, selectedId);
                if (linkedHint) linkedHint.textContent = d.count + ' record(s) available.';
            } else {
                fldLinked.innerHTML = '<option value="">Error loading records</option>';
            }
        })
        .catch(err => {
            if (fldLinked) { fldLinked.disabled = false; fldLinked.innerHTML = '<option value="">Network error</option>'; }
            console.error('[MenuManager] loadLinkedItems:', err);
        });
    }

    function populateLinkedSelect(items, selectedId) {
        if (!fldLinked) return;
        fldLinked.innerHTML = '<option value="">— Select —</option>';
        items.forEach(item => {
            const opt = document.createElement('option');
            opt.value       = item.id;
            opt.textContent = item.label + (item.slug ? '  [' + item.slug + ']' : '');
            if (parseInt(selectedId) === item.id) opt.selected = true;
            fldLinked.appendChild(opt);
        });
    }

    // ─────────────────────────────────────────────────────────────
    // 10. POPULATE PARENT SELECT
    // ─────────────────────────────────────────────────────────────

    function populateParentSelect(excludeId) {
        if (!fldParentSelect) return;
        fldParentSelect.innerHTML = '<option value="">— Top Level (root) —</option>';

        document.querySelectorAll('#itemTree [data-sortable-item][data-depth="0"]').forEach(el => {
            const id    = parseInt(el.dataset.id);
            const title = el.querySelector('.item-title')?.textContent?.trim() ?? 'Item #' + id;
            if (id === excludeId) return;
            const opt = document.createElement('option');
            opt.value       = id;
            opt.textContent = title;
            if (parseInt(fldParentId.value) === id) opt.selected = true;
            fldParentSelect.appendChild(opt);
        });
    }

    fldParentSelect?.addEventListener('change', function () {
        fldParentId.value = this.value;
    });

    // ─────────────────────────────────────────────────────────────
    // 11. SAVE ITEM  (Create or Update)
    // ─────────────────────────────────────────────────────────────

    btnSaveItem?.addEventListener('click', function () {
        clearErrors();

        const itemId  = fldItemId.value   ? parseInt(fldItemId.value)   : null;
        const parentId= fldParentId.value ? parseInt(fldParentId.value) : null;
        const title   = fldTitle.value.trim();
        const target  = fldTarget.value || '_self';
        const status  = parseInt(document.querySelector('input[name="statusRadio"]:checked')?.value ?? 1);
        const ctype   = activeContentType;

        // ── Client-side validation ──────────────────────────────
        let valid = true;
        if (!title) { showFieldError('fldTitle', 'errTitle', 'Display title is required.'); valid = false; }

        let type      = null;
        let url       = null;
        let linkedId  = null;
        let megaSettings = null;

        if (ctype === 'mega_menu') {
            type = 'custom';
            url  = fldMegaUrl?.value.trim() || '#';

            // Build mega_settings payload
            megaSettings = {
                content_type:   'mega_menu',
                display_source: activeMegaSource,
                display_mode:   activeMegaMode,
                linked_menu_id: activeMegaSource === 'custom_menu'
                    ? (parseInt(fldMegaLinkedMenu?.value) || null)
                    : null,
                region_ids:  getSelectedIds(fldMegaRegionIds),
                state_ids:   getSelectedIds(fldMegaStateIds),
                active_only: fldMegaActiveOnly  ? fldMegaActiveOnly.checked  : true,
                package_only:fldMegaPackageOnly ? fldMegaPackageOnly.checked : false,
                manage_city_only: fldMegaManageCityOnly ? fldMegaManageCityOnly.checked : false,
                banner: {
                    image:       fldBannerImage?.value.trim()   || '',
                    alt:         fldBannerAlt?.value.trim()     || '',
                    title:       fldBannerTitle?.value.trim()   || '',
                    description: fldBannerDesc?.value.trim()    || '',
                    cta_text:    fldBannerCtaText?.value.trim() || '',
                    cta_url:     fldBannerCtaUrl?.value.trim()  || '',
                },
            };

            if (activeMegaSource === 'custom_menu' && !megaSettings.linked_menu_id) {
                document.getElementById('errMegaLinkedMenu')?.textContent
                    && (document.getElementById('errMegaLinkedMenu').textContent = 'Please select a menu.');
                fldMegaLinkedMenu?.classList.add('is-invalid');
                valid = false;
            }

        } else if (ctype === 'menu_reference') {
            type     = 'menu_reference';
            linkedId = fldLinked?.value ? parseInt(fldLinked.value) : null;
            url      = null;
            if (!linkedId) { showFieldError('fldLinked', 'errLinked', 'Please select a menu to reference.'); valid = false; }

        } else {
            // Normal
            type     = activeType;
            url      = fldUrl?.value.trim() || null;
            linkedId = fldLinked?.value ? parseInt(fldLinked.value) : null;
            megaSettings = null;  // clear any previous mega settings

            if (type === 'custom' && !url) { showFieldError('fldUrl', 'errUrl', 'Please enter a URL.'); valid = false; }
            if (LINKED_TYPES.includes(type) && type !== 'menu_reference' && !linkedId) {
                const msg = { region: 'Please select a region.', state: 'Please select a state.' }[type]
                    ?? 'Please select a record to link.';
                showFieldError('fldLinked', 'errLinked', msg);
                valid = false;
            }
        }

        if (!valid) return;

        const payload = {
            title,
            type,
            linked_id:     linkedId,
            url,
            target,
            status,
            parent_id:     isEditing ? null : parentId,
            mega_settings: megaSettings,
        };

        const endpoint = isEditing ? `${CFG.updateUrl}/${itemId}` : CFG.storeUrl;
        const method   = isEditing ? 'PUT' : 'POST';

        setLoading(true);

        fetch(endpoint, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'Accept':       'application/json',
                'X-CSRF-TOKEN': CSRF,
            },
            body: JSON.stringify(payload),
        })
        .then(r => r.json())
        .then(d => {
            setLoading(false);

            if (d.success) {
                itemModal.hide();
                showToast('success', d.message);

                if (isEditing) {
                    updateItemInDOM(d.item);
                } else {
                    insertItemInDOM(d.html, d.item.parent_id);
                }

                refreshStats();
                refreshEmptyNotice();
                if (type) invalidateLinkedCache(type);
            } else {
                if (d.errors) {
                    const map = { title: 'errTitle', url: 'errUrl', linked_id: 'errLinked', type: 'errType' };
                    Object.entries(d.errors).forEach(([field, msgs]) => {
                        const errId   = map[field];
                        const inputId = field === 'linked_id' ? 'fldLinked'
                                      : field === 'url'       ? 'fldUrl'
                                      : field === 'title'     ? 'fldTitle'
                                      : null;
                        if (errId && inputId) {
                            showFieldError(inputId, errId, Array.isArray(msgs) ? msgs[0] : msgs);
                        }
                    });
                }
                showToast('error', d.message || 'Save failed. Please check the form.');
            }
        })
        .catch(err => {
            setLoading(false);
            console.error('[MenuManager] Save error:', err);
            showToast('error', 'Network error. Please try again.');
        });
    });

    // ─────────────────────────────────────────────────────────────
    // 12. NEW MENU MODAL
    // ─────────────────────────────────────────────────────────────

    if (btnNewMenu && newMenuModal) {
        btnNewMenu.addEventListener('click', function () {
            if (fldNewMenuName) { fldNewMenuName.value = ''; fldNewMenuName.classList.remove('is-invalid'); }
            if (errNewMenuName) errNewMenuName.textContent = '';
            newMenuModal.show();
            setTimeout(() => fldNewMenuName?.focus(), 300);
        });

        btnSaveNewMenu?.addEventListener('click', function () {
            const name = fldNewMenuName?.value.trim();
            if (!name || name.length < 2) {
                fldNewMenuName?.classList.add('is-invalid');
                if (errNewMenuName) errNewMenuName.textContent = 'Menu name must be at least 2 characters.';
                return;
            }

            newMenuSpinner?.classList.remove('d-none');
            btnSaveNewMenu.disabled = true;

            fetch(CFG.createMenuUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ name }),
            })
            .then(r => r.json())
            .then(d => {
                newMenuSpinner?.classList.add('d-none');
                btnSaveNewMenu.disabled = false;
                if (d.success) { newMenuModal.hide(); showToast('success', d.message); window.location.href = d.menu.url; }
                else { fldNewMenuName?.classList.add('is-invalid'); if (errNewMenuName) errNewMenuName.textContent = d.message || 'Failed.'; }
            })
            .catch(() => { newMenuSpinner?.classList.add('d-none'); btnSaveNewMenu.disabled = false; showToast('error', 'Network error.'); });
        });

        fldNewMenuName?.addEventListener('keydown', e => { if (e.key === 'Enter') btnSaveNewMenu?.click(); });
    }

    // ─────────────────────────────────────────────────────────────
    // 13. DELETE MENU
    // ─────────────────────────────────────────────────────────────

    document.querySelectorAll('.ia-delete-menu').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = parseInt(this.dataset.id), name = this.dataset.name;
            Swal.fire({
                title: 'Delete Menu?',
                html:  `Delete menu <strong>${escHtml(name)}</strong> and all its items?`,
                icon:  'warning', showCancelButton: true,
                confirmButtonColor: '#ef4444', confirmButtonText: 'Yes, Delete',
            }).then(result => {
                if (!result.isConfirmed) return;
                fetch(`${CFG.deleteMenuUrl}/${id}`, {
                    method: 'DELETE', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                })
                .then(r => r.json())
                .then(d => {
                    if (d.success) { showToast('success', d.message); window.location.href = CFG.deleteMenuUrl.replace(/\/\d+$/, ''); }
                    else showToast('error', d.message || 'Delete failed.');
                })
                .catch(() => showToast('error', 'Network error.'));
            });
        });
    });

    // ─────────────────────────────────────────────────────────────
    // 14. EVENT DELEGATION (add / edit / toggle / delete / expand)
    // ─────────────────────────────────────────────────────────────

    btnAddItem?.addEventListener('click', () => openAddModal(null));

    itemTree?.addEventListener('click', function (e) {
        const addBtn    = e.target.closest('.ia-add');
        if (addBtn)    { e.stopPropagation(); openAddModal(parseInt(addBtn.dataset.parentId)); return; }
        const editBtn   = e.target.closest('.ia-edit');
        if (editBtn)   { e.stopPropagation(); openEditModal(parseInt(editBtn.dataset.id)); return; }
        const toggleBtn = e.target.closest('.ia-toggle');
        if (toggleBtn) { e.stopPropagation(); toggleItemStatus(toggleBtn); return; }
        const deleteBtn = e.target.closest('.ia-delete');
        if (deleteBtn) { e.stopPropagation(); confirmDelete(deleteBtn); return; }
        const expandBtn = e.target.closest('.item-toggle');
        if (expandBtn) { e.stopPropagation(); toggleExpand(expandBtn); return; }
    });

    // ─────────────────────────────────────────────────────────────
    // 15. EXPAND / COLLAPSE
    // ─────────────────────────────────────────────────────────────

    function toggleExpand(btn) {
        const wrap     = btn.closest('[data-sortable-item]');
        const children = wrap?.querySelector('[data-child-list]');
        if (!children) return;
        const isOpen = children.classList.toggle('open');
        const icon   = btn.querySelector('i');
        if (icon) icon.style.transform = isOpen ? 'rotate(90deg)' : '';
    }

    // ─────────────────────────────────────────────────────────────
    // 16. TOGGLE STATUS
    // ─────────────────────────────────────────────────────────────

    function toggleItemStatus(btn) {
        const id = parseInt(btn.dataset.id);
        fetch(`${CFG.toggleUrl}/${id}/toggle`, {
            method: 'POST', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        })
        .then(r => r.json())
        .then(d => {
            if (!d.success) { showToast('error', d.message || 'Toggle failed.'); return; }

            const active = d.status === 1;
            const wrap   = document.querySelector(`[data-id="${id}"][data-sortable-item]`);
            if (!wrap) return;

            btn.classList.toggle('ia-active',   active);
            btn.classList.toggle('ia-inactive', !active);
            btn.querySelector('i').className = `fas fa-${active ? 'eye' : 'eye-slash'}`;
            btn.title = active ? 'Hide item' : 'Show item';

            wrap.classList.toggle('item-hidden', !active);
            const titleEl = wrap.querySelector('.item-title');
            if (titleEl) {
                titleEl.classList.toggle('text-decoration-line-through', !active);
                titleEl.classList.toggle('text-muted', !active);
            }

            const subEl     = wrap.querySelector('.item-sub');
            let hiddenBadge = subEl?.querySelector('.item-hidden-badge');
            if (!active && !hiddenBadge && subEl) {
                const b = document.createElement('span');
                b.className = 'badge bg-secondary ms-1 item-hidden-badge';
                b.style.fontSize = '10px';
                b.textContent = 'Hidden';
                subEl.appendChild(b);
            } else if (active && hiddenBadge) {
                hiddenBadge.remove();
            }

            showToast('success', d.message);
            refreshStats();
        })
        .catch(() => showToast('error', 'Network error.'));
    }

    // ─────────────────────────────────────────────────────────────
    // 17. DELETE ITEM
    // ─────────────────────────────────────────────────────────────

    function confirmDelete(btn) {
        const id       = parseInt(btn.dataset.id);
        const title    = btn.dataset.title;
        const children = parseInt(btn.dataset.children || 0);

        Swal.fire({
            title: 'Delete Item?',
            html: children > 0
                ? `Delete <strong>${escHtml(title)}</strong>?<br><span class="text-danger small">⚠ ${children} child item(s) will also be deleted.</span>`
                : `Delete <strong>${escHtml(title)}</strong>?`,
            icon: 'warning', showCancelButton: true,
            confirmButtonColor: '#ef4444', confirmButtonText: 'Yes, Delete',
        }).then(result => {
            if (!result.isConfirmed) return;
            fetch(`${CFG.deleteUrl}/${id}`, {
                method: 'DELETE', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
            })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    document.querySelector(`[data-id="${id}"][data-sortable-item]`)?.remove();
                    showToast('success', d.message);
                    refreshStats();
                    refreshEmptyNotice();
                } else {
                    showToast('error', d.message || 'Delete failed.');
                }
            })
            .catch(() => showToast('error', 'Network error.'));
        });
    }

    // ─────────────────────────────────────────────────────────────
    // 18. SORTABLE
    // ─────────────────────────────────────────────────────────────

    function initSortable(container) {
        if (!container || container._sortableInited) return;
        container._sortableInited = true;
        Sortable.create(container, {
            group:     { name: 'menu-items', pull: true, put: true },
            handle:    '.item-handle',
            animation: 150,
            ghostClass:'sortable-ghost',
            onEnd:     () => debounceReorder(),
        });
    }

    function initAllSortables() {
        initSortable(itemTree);
        document.querySelectorAll('[data-child-list]').forEach(el => initSortable(el));
    }

    function debounceReorder() {
        setSaveIndicator('saving');
        clearTimeout(reorderDebounce);
        reorderDebounce = setTimeout(saveOrder, 700);
    }

    function saveOrder() {
        const payload = [];
        itemTree?.querySelectorAll(':scope > [data-sortable-item]').forEach((el, i) => {
            payload.push({ id: +el.dataset.id, parent_id: null, sort_order: i });
            el.querySelectorAll(':scope > [data-child-list] > [data-sortable-item]').forEach((ch, j) => {
                payload.push({ id: +ch.dataset.id, parent_id: +el.dataset.id, sort_order: j });
                ch.querySelectorAll(':scope > [data-child-list] > [data-sortable-item]').forEach((gc, k) => {
                    payload.push({ id: +gc.dataset.id, parent_id: +ch.dataset.id, sort_order: k });
                });
            });
        });

        fetch(CFG.reorderUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ items: payload }),
        })
        .then(r => r.json())
        .then(d => setSaveIndicator(d.success ? 'saved' : 'error'))
        .catch(() => setSaveIndicator('error'));
    }

    // ─────────────────────────────────────────────────────────────
    // 19. DOM MUTATION HELPERS
    // ─────────────────────────────────────────────────────────────

    function insertItemInDOM(html, parentId) {
        if (!html) return;
        const node = htmlToNode(html);
        if (!node) return;

        if (parentId) {
            const parentWrap = document.querySelector(`[data-id="${parentId}"][data-sortable-item]`);
            const childList  = parentWrap?.querySelector('[data-child-list]');
            if (childList) {
                childList.appendChild(node);
                childList.classList.add('open');
                parentWrap.querySelector('.item-toggle')?.classList.remove('invisible');
                initSortable(childList);
            } else {
                itemTree?.appendChild(node);
            }
        } else {
            itemTree?.appendChild(node);
        }

        initAllSortables();
    }

    function updateItemInDOM(item) {
        const wrap = document.querySelector(`[data-id="${item.id}"][data-sortable-item]`);
        if (!wrap) return;

        const titleEl = wrap.querySelector('.item-title');
        if (titleEl) {
            const firstText = Array.from(titleEl.childNodes).find(n => n.nodeType === Node.TEXT_NODE);
            if (firstText) firstText.textContent = item.title + ' ';
            else titleEl.prepend(document.createTextNode(item.title + ' '));
        }

        const sub = wrap.querySelector('.item-sub');
        if (sub) {
            const oldBadge = sub.querySelector('.badge:first-child');
            if (oldBadge) {
                oldBadge.className      = `badge ${item.type_badge}`;
                oldBadge.style.fontSize = '10px';
                oldBadge.textContent    = item.type_label;
            }
        }

        const iconDot = wrap.querySelector('.item-type-dot i');
        if (iconDot) iconDot.className = `fas ${item.type_icon}`;

        if (sub) {
            let urlSpan = sub.querySelector('.item-url');
            if (item.resolved_url && item.resolved_url !== '#') {
                if (!urlSpan) { urlSpan = document.createElement('span'); urlSpan.className = 'item-url ms-1'; sub.appendChild(urlSpan); }
                urlSpan.innerHTML = `<i class="fas fa-external-link-alt" style="font-size:9px;"></i> ${escHtml(item.resolved_url.replace(/^https?:\/\/[^/]+/, ''))}`;
            } else if (urlSpan) { urlSpan.remove(); }
        }

        // Mega badge
        if (sub) {
            let megaBadge = sub.querySelector('.item-mega-badge');
            if (item.is_mega_menu && !megaBadge) {
                const b = document.createElement('span');
                b.className = 'item-mega-badge';
                b.textContent = '⚡ Mega';
                sub.appendChild(b);
            } else if (!item.is_mega_menu && megaBadge) {
                megaBadge.remove();
            }
        }

        const active = item.status === 1;
        wrap.classList.toggle('item-hidden', !active);
        if (titleEl) {
            titleEl.classList.toggle('text-decoration-line-through', !active);
            titleEl.classList.toggle('text-muted', !active);
        }

        const toggleBtn = wrap.querySelector('.ia-toggle');
        if (toggleBtn) {
            toggleBtn.classList.toggle('ia-active',   active);
            toggleBtn.classList.toggle('ia-inactive', !active);
            toggleBtn.querySelector('i').className = `fas fa-${active ? 'eye' : 'eye-slash'}`;
            toggleBtn.title = active ? 'Hide item' : 'Show item';
        }

        if (sub) {
            let hiddenBadge = sub.querySelector('.item-hidden-badge');
            if (!active && !hiddenBadge) {
                const b = document.createElement('span'); b.className = 'badge bg-secondary ms-1 item-hidden-badge'; b.style.fontSize = '10px'; b.textContent = 'Hidden'; sub.appendChild(b);
            } else if (active && hiddenBadge) { hiddenBadge.remove(); }
        }

        showToast('success', 'Item updated.');
    }

    function htmlToNode(html) {
        const tpl = document.createElement('template');
        tpl.innerHTML = html.trim();
        return tpl.content.firstElementChild;
    }

    // ─────────────────────────────────────────────────────────────
    // 20. STATS / UI STATE
    // ─────────────────────────────────────────────────────────────

    function refreshStats() {
        const all    = document.querySelectorAll('[data-sortable-item]');
        const active = document.querySelectorAll('[data-sortable-item]:not(.item-hidden)');
        const roots  = document.querySelectorAll('#itemTree > [data-sortable-item]');
        const set = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
        set('statTotal',  all.length);
        set('statActive', active.length);
        set('statRoot',   roots.length);
        set('statNested', all.length - roots.length);
    }

    function refreshEmptyNotice() {
        if (!emptyNotice) return;
        emptyNotice.style.display = itemTree?.querySelectorAll('[data-sortable-item]').length > 0 ? 'none' : '';
    }

    function setSaveIndicator(state) {
        if (!saveIndicator) return;
        saveIndicator.innerHTML = {
            saving: '<i class="fas fa-spinner fa-spin text-warning me-1"></i>Saving...',
            saved:  '<i class="fas fa-cloud text-success me-1"></i>All saved',
            error:  '<i class="fas fa-exclamation-triangle text-danger me-1"></i>Save failed',
        }[state] ?? '';
    }

    function setLoading(on) {
        if (saveSpinner)  saveSpinner.classList.toggle('d-none', !on);
        if (btnSaveItem)  btnSaveItem.disabled = on;
        if (btnSaveLabel) btnSaveLabel.textContent = on ? 'Saving...' : (isEditing ? 'Save Changes' : 'Add Item');
    }

    // ─────────────────────────────────────────────────────────────
    // 21. FORM RESET / ERROR HELPERS
    // ─────────────────────────────────────────────────────────────

    function resetForm() {
        if (itemForm) itemForm.reset();
        if (fldItemId)  fldItemId.value  = '';
        if (fldParentId) fldParentId.value = '';
        if (fldTitle)   fldTitle.value   = '';
        if (fldUrl)     fldUrl.value     = '';
        if (fldLinked)  fldLinked.innerHTML = '<option value="">— Select —</option>';
        if (fldTarget)  fldTarget.value  = '_self';
        if (fldMegaUrl) fldMegaUrl.value = '';

        // Reset mega fields
        if (fldMegaActiveOnly)  fldMegaActiveOnly.checked  = true;
        if (fldMegaPackageOnly) fldMegaPackageOnly.checked = false;
        if (fldMegaManageCityOnly) fldMegaManageCityOnly.checked = false;
        if (fldMegaLinkedMenu)  fldMegaLinkedMenu.value = '';
        if (fldBannerImage)     fldBannerImage.value   = '';
        if (fldBannerAlt)       fldBannerAlt.value     = '';
        if (fldBannerTitle)     fldBannerTitle.value   = '';
        if (fldBannerDesc)      fldBannerDesc.value    = '';
        if (fldBannerCtaText)   fldBannerCtaText.value = '';
        if (fldBannerCtaUrl)    fldBannerCtaUrl.value  = '';
        megaBannerFields?.classList.add('d-none');
        if (megaBannerChevron) megaBannerChevron.style.transform = '';

        document.getElementById('statusActive')?.checked !== undefined &&
            (document.getElementById('statusActive').checked = true);

        activateContentType('normal');
        activateType('custom');
        activateMegaSource('auto');
        activateMegaMode('region_state_city');
        clearErrors();
        setLoading(false);
    }

    function clearErrors() {
        ['fldTitle', 'fldUrl', 'fldLinked', 'fldMegaLinkedMenu'].forEach(id => {
            document.getElementById(id)?.classList.remove('is-invalid');
        });
        ['errTitle', 'errUrl', 'errLinked', 'errType', 'errMegaLinkedMenu'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.textContent = '';
        });
    }

    function showFieldError(inputId, errId, msg) {
        document.getElementById(inputId)?.classList.add('is-invalid');
        const err = document.getElementById(errId);
        if (err) { err.textContent = msg; err.style.display = 'block'; }
    }

    function invalidateLinkedCache(type) { delete linkedCache[type]; }

    function getSelectedIds(select) {
        if (!select) return [];
        return Array.from(select.selectedOptions).map(o => parseInt(o.value, 10)).filter(Boolean);
    }

    // ─────────────────────────────────────────────────────────────
    // 22. UTILITIES
    // ─────────────────────────────────────────────────────────────

    function showToast(type, msg) {
        if (typeof toastr !== 'undefined') toastr[type](msg);
        else console.log('[MenuManager]', type, msg);
    }

    function escHtml(str) {
        return String(str ?? '')
            .replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    // ─────────────────────────────────────────────────────────────
    // 23. MENU DISPLAY SETTINGS  (per-menu auto-tree settings)
    // ─────────────────────────────────────────────────────────────

    const menuSettingsModalEl = document.getElementById('menuSettingsModal');
    const menuSettingsModal   = menuSettingsModalEl
        ? new bootstrap.Modal(menuSettingsModalEl, { backdrop: true, keyboard: true })
        : null;
    const btnMenuSettings  = document.getElementById('btnMenuSettings');
    const btnAutoModeEdit  = document.getElementById('btnAutoModeEdit');
    const btnSaveSettings  = document.getElementById('btnSaveSettings');
    const settingsSpinner  = document.getElementById('settingsSpinner');
    const fldActiveOnly    = document.getElementById('fldActiveOnly');
    const fldPackageOnly   = document.getElementById('fldPackageOnly');
    const fldManageCityOnly= document.getElementById('fldManageCityOnly');
    const autoFilters      = document.getElementById('autoFilters');
    const fldRegionIds     = document.getElementById('fldRegionIds');
    const fldStateIds      = document.getElementById('fldStateIds');
    const filterRegionWrap = document.getElementById('filterRegionWrap');
    const filterStateWrap  = document.getElementById('filterStateWrap');

    let regionsCache = null;
    let statesCache  = null;

    function openSettingsModal() {
        if (!menuSettingsModal) return;
        menuSettingsModal.show();

        fetch(`${CFG.settingsUrl}/${CFG.menuId}/settings`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        })
        .then(r => r.json())
        .then(d => {
            if (!d.success) { showToast('error', 'Failed to load settings.'); return; }

            const radio = document.querySelector(`input[name="display_mode"][value="${d.display_mode}"]`);
            if (radio) { radio.checked = true; onModeChange(d.display_mode); }

            const s = d.display_settings || {};
            if (fldActiveOnly)  fldActiveOnly.checked  = s.active_only  !== false;
            if (fldPackageOnly) fldPackageOnly.checked = !! s.package_only;
            if (fldManageCityOnly) fldManageCityOnly.checked = !! s.manage_city_only;

            Promise.all([loadRegionOptions(), loadStateOptions()]).then(() => {
                markSelected(fldRegionIds, s.region_ids || []);
                markSelected(fldStateIds,  s.state_ids  || []);
            });
        })
        .catch(() => showToast('error', 'Failed to load settings.'));
    }

    function onModeChange(mode) {
        if (autoFilters) autoFilters.style.display = (mode === 'manual') ? 'none' : '';
        if (filterRegionWrap) filterRegionWrap.style.display = ['region_state_city', 'region_state'].includes(mode) ? '' : 'none';
        if (filterStateWrap)  filterStateWrap.style.display  = ['region_state_city', 'region_state', 'state_city'].includes(mode) ? '' : 'none';
        document.querySelectorAll('.display-mode-card').forEach(card => {
            const r = card.querySelector('input[name="display_mode"]');
            const active = r && r.value === mode;
            card.style.borderColor = active ? '#f97316' : '';
            card.style.background  = active ? '#fff7ed' : '';
        });
    }

    function loadRegionOptions() {
        if (regionsCache) return Promise.resolve();
        return fetch(`${CFG.availableUrl}/region`, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } })
        .then(r => r.json())
        .then(d => {
            regionsCache = d.items || [];
            if (fldRegionIds) fldRegionIds.innerHTML = regionsCache.map(r => `<option value="${r.id}">${escHtml(r.label)}</option>`).join('');
        });
    }

    function loadStateOptions() {
        if (statesCache) return Promise.resolve();
        return fetch(`${CFG.availableUrl}/state`, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } })
        .then(r => r.json())
        .then(d => {
            statesCache = d.items || [];
            if (fldStateIds) fldStateIds.innerHTML = statesCache.map(s => `<option value="${s.id}">${escHtml(s.label)}</option>`).join('');
        });
    }

    function markSelected(select, ids) {
        if (!select || !ids.length) return;
        Array.from(select.options).forEach(opt => { opt.selected = ids.includes(parseInt(opt.value, 10)); });
    }

    const dmGrid = document.getElementById('displayModeGrid');
    if (dmGrid) {
        dmGrid.addEventListener('change', e => { if (e.target.name === 'display_mode') onModeChange(e.target.value); });
        dmGrid.addEventListener('click', e => {
            const card = e.target.closest('.display-mode-card');
            if (!card) return;
            const radio = card.querySelector('input[name="display_mode"]');
            if (radio) { radio.checked = true; onModeChange(radio.value); }
        });
    }

    if (btnMenuSettings) btnMenuSettings.addEventListener('click', openSettingsModal);
    if (btnAutoModeEdit) btnAutoModeEdit.addEventListener('click', e => { e.preventDefault(); openSettingsModal(); });

    if (btnSaveSettings) {
        btnSaveSettings.addEventListener('click', () => {
            const modeRadio = document.querySelector('input[name="display_mode"]:checked');
            if (!modeRadio) { showToast('error', 'Please select a display mode.'); return; }

            const payload = {
                display_mode: modeRadio.value,
                display_settings: {
                    region_ids:  getSelectedIds(fldRegionIds),
                    state_ids:   getSelectedIds(fldStateIds),
                    active_only:  fldActiveOnly  ? fldActiveOnly.checked  : true,
                    package_only: fldPackageOnly ? fldPackageOnly.checked : false,
                    manage_city_only: fldManageCityOnly ? fldManageCityOnly.checked : false,
                },
            };

            if (settingsSpinner) settingsSpinner.classList.remove('d-none');
            btnSaveSettings.disabled = true;

            fetch(`${CFG.settingsUrl}/${CFG.menuId}/settings`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify(payload),
            })
            .then(r => r.json())
            .then(d => {
                if (settingsSpinner) settingsSpinner.classList.add('d-none');
                btnSaveSettings.disabled = false;
                if (d.success) { showToast('success', d.message || 'Settings saved.'); menuSettingsModal.hide(); setTimeout(() => window.location.reload(), 600); }
                else showToast('error', d.message || 'Failed to save settings.');
            })
            .catch(() => {
                if (settingsSpinner) settingsSpinner.classList.add('d-none');
                btnSaveSettings.disabled = false;
                showToast('error', 'Network error saving settings.');
            });
        });
    }

    // ─────────────────────────────────────────────────────────────
    // 24. INIT
    // ─────────────────────────────────────────────────────────────
    initAllSortables();
    refreshEmptyNotice();
    setSaveIndicator('saved');

    console.log('[MenuManager] Ready. Menu ID:', CFG.menuId);

}); // end DOMContentLoaded

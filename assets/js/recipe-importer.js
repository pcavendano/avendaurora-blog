(function() {
    'use strict';

    const state = {
        step: 1,
        file: null,
        recipe: null,
        imageToken: null,
        imageFilename: null,
        pageId: null,
        panelUrl: null,
        ingredientOptions: [],
    };

    // Fetch existing ingredient titles for autocomplete
    fetch('/api/ingredients', { credentials: 'same-origin' })
        .then(r => r.ok ? r.json() : null)
        .then(data => {
            if (data && Array.isArray(data.ingredients)) {
                state.ingredientOptions = data.ingredients;
                const dl = document.getElementById('ingredientsDatalist');
                if (dl) {
                    dl.innerHTML = state.ingredientOptions
                        .map(i => `<option value="${i.title.replace(/"/g, '&quot;')}"></option>`)
                        .join('');
                }
            }
        })
        .catch(() => { /* autocomplete is optional, ignore */ });

    const CATEGORIES = {
        'antojitos': 'Antojitos y Botanas',
        'platos-fuertes': 'Platos Fuertes',
        'sopas-caldos': 'Sopas y Caldos',
        'salsas': 'Salsas y Aderezos',
        'mariscos': 'Mariscos',
        'desayunos': 'Desayunos',
        'postres': 'Postres',
        'bebidas': 'Bebidas',
        'vegetarianos': 'Vegetarianos',
    };

    const UNITS = ['', 'piezas', 'tazas', 'cucharadas', 'cucharaditas', 'gramos', 'kg', 'ml', 'litros', 'lb', 'oz'];
    const DIFFICULTIES = { '': '—', 'easy': 'Fácil', 'medium': 'Medio', 'hard': 'Difícil' };
    const REGIONS = { '': '—', 'oaxaca': 'Oaxaca', 'yucatan': 'Yucatán', 'jalisco': 'Jalisco', 'michoacan': 'Michoacán', 'veracruz': 'Veracruz', 'puebla': 'Puebla', 'norte': 'Norte', 'centro': 'Centro', 'sur': 'Sur', 'costeno': 'Costeño' };

    const el = (id) => document.getElementById(id);
    const $$ = (sel) => document.querySelectorAll(sel);

    function goStep(n) {
        state.step = n;
        $$('.wizard__step').forEach(s => s.classList.toggle('is-active', Number(s.dataset.step) <= n));
        $$('.wizard__panel').forEach(p => p.classList.toggle('is-active', Number(p.dataset.panel) === n));
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function escapeHtml(str) {
        return String(str ?? '').replace(/[&<>"']/g, m => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[m]));
    }

    // Step 1: Upload
    const dropZone = el('dropZone');
    const fileInput = el('fileInput');
    const filePreview = el('filePreview');
    const previewImg = el('previewImg');
    const previewName = el('previewName');
    const removeFileBtn = el('removeFile');
    const extractBtn = el('extractBtn');

    function setFile(file) {
        if (!file) {
            state.file = null;
            filePreview.hidden = true;
            extractBtn.disabled = true;
            return;
        }
        if (file.size > 10 * 1024 * 1024) {
            alert('Archivo demasiado grande. Máximo 10 MB.');
            return;
        }
        state.file = file;
        const reader = new FileReader();
        reader.onload = e => { previewImg.src = e.target.result; };
        reader.readAsDataURL(file);
        previewName.textContent = file.name + ' (' + Math.round(file.size / 1024) + ' KB)';
        filePreview.hidden = false;
        extractBtn.disabled = false;
    }

    fileInput.addEventListener('change', e => setFile(e.target.files[0]));
    removeFileBtn.addEventListener('click', () => {
        fileInput.value = '';
        setFile(null);
    });

    ['dragover', 'dragenter'].forEach(ev => dropZone.addEventListener(ev, e => {
        e.preventDefault();
        dropZone.classList.add('is-dragging');
    }));
    ['dragleave', 'drop'].forEach(ev => dropZone.addEventListener(ev, e => {
        e.preventDefault();
        dropZone.classList.remove('is-dragging');
    }));
    dropZone.addEventListener('drop', e => {
        const f = e.dataTransfer.files[0];
        if (f) setFile(f);
    });

    extractBtn.addEventListener('click', async () => {
        if (!state.file) return;
        goStep(2);
        try {
            const fd = new FormData();
            fd.append('image', state.file);
            const res = await fetch('/api/import/extract', { method: 'POST', body: fd, credentials: 'same-origin' });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || ('HTTP ' + res.status));
            state.recipe = data.recipe;
            state.imageToken = data.image_token;
            state.imageFilename = data.image_filename;
            renderReview();
            goStep(3);
        } catch (err) {
            alert('Error al extraer: ' + err.message);
            goStep(1);
        }
    });

    // Step 3: Review form
    function renderReview() {
        const r = state.recipe || {};
        const container = el('reviewForm');

        const cats = Array.isArray(r.category) ? r.category : [];
        const catsHtml = Object.entries(CATEGORIES).map(([v, label]) =>
            `<label class="review-chip"><input type="checkbox" name="category" value="${v}" ${cats.includes(v) ? 'checked' : ''}> ${label}</label>`
        ).join('');

        const unitOpts = UNITS.map(u => `<option value="${u}">${u || '—'}</option>`).join('');

        const ingredientsHtml = (r.ingredients || []).map((ing, i) => `
            <div class="review-row" data-ing-index="${i}">
                <input type="text" class="ing-qty" placeholder="Cant." value="${escapeHtml(ing.quantity || '')}" style="width:70px">
                <select class="ing-unit">${unitOpts}</select>
                <input type="text" class="ing-name" list="ingredientsDatalist" placeholder="Ingrediente" value="${escapeHtml(ing.ingredient || '')}" style="flex:2">
                <input type="text" class="ing-prep" placeholder="Preparación" value="${escapeHtml(ing.preparation || '')}" style="flex:1">
                <label class="review-optional"><input type="checkbox" class="ing-opt" ${ing.optional ? 'checked' : ''}> opt.</label>
                <button type="button" class="btn btn--ghost btn--icon" data-remove-ing="${i}">✕</button>
            </div>
        `).join('');

        const instructionsHtml = (r.instructions || []).map((step, i) => `
            <div class="review-step" data-step-index="${i}">
                <div class="review-step__num">${i + 1}</div>
                <div class="review-step__body">
                    <input type="text" class="step-title" placeholder="Título del paso (opcional)" value="${escapeHtml(step.step_title || '')}">
                    <textarea class="step-instr" rows="3" placeholder="Instrucción">${escapeHtml(step.instruction || '')}</textarea>
                    <input type="text" class="step-tip" placeholder="Tip del chef (opcional)" value="${escapeHtml(step.tip || '')}">
                </div>
                <button type="button" class="btn btn--ghost btn--icon" data-remove-step="${i}">✕</button>
            </div>
        `).join('');

        const regionOpts = Object.entries(REGIONS).map(([v, l]) => `<option value="${v}">${l}</option>`).join('');
        const diffOpts = Object.entries(DIFFICULTIES).map(([v, l]) => `<option value="${v}">${l}</option>`).join('');

        container.innerHTML = `
            <div class="review-section">
                <label>Título <input type="text" id="f-title" value="${escapeHtml(r.title || '')}"></label>
                <label>Descripción <textarea id="f-description" rows="3">${escapeHtml(r.description || '')}</textarea></label>
            </div>

            <div class="review-section">
                <h3>Categorías</h3>
                <div class="review-chips">${catsHtml}</div>
                <label>Subcategorías (separadas por coma) <input type="text" id="f-subcategory" value="${escapeHtml((r.subcategory || []).join(', '))}"></label>
                <div class="review-row">
                    <label>Región <select id="f-region">${regionOpts}</select></label>
                    <label>Dificultad <select id="f-difficulty">${diffOpts}</select></label>
                    <label>Porciones <input type="number" id="f-servings" value="${r.servings || ''}" style="width:80px"></label>
                </div>
                <div class="review-row">
                    <label>Prep (min) <input type="number" id="f-prep" value="${r.prep_time_minutes || ''}" style="width:80px"></label>
                    <label>Cocción (min) <input type="number" id="f-cook" value="${r.cook_time_minutes || ''}" style="width:80px"></label>
                    <label>Total (min) <input type="number" id="f-total" value="${r.total_time_minutes || ''}" style="width:80px"></label>
                </div>
            </div>

            <div class="review-section">
                <h3>Ingredientes</h3>
                <div id="ingList">${ingredientsHtml}</div>
                <button type="button" class="btn btn--ghost" id="addIng">+ Añadir ingrediente</button>
            </div>

            <div class="review-section">
                <h3>Instrucciones</h3>
                <div id="stepList">${instructionsHtml}</div>
                <button type="button" class="btn btn--ghost" id="addStep">+ Añadir paso</button>
            </div>

            <div class="review-section">
                <label>Tips <textarea id="f-tips" rows="3">${escapeHtml(r.tips || '')}</textarea></label>
                <label>Historia <textarea id="f-history" rows="3">${escapeHtml(r.history || '')}</textarea></label>
                <label>Etiquetas (separadas por coma) <input type="text" id="f-tags" value="${escapeHtml((r.tags || []).join(', '))}"></label>
            </div>
        `;

        // Set pre-selected values for selects
        el('f-region').value = r.region || '';
        el('f-difficulty').value = r.difficulty || '';

        (r.ingredients || []).forEach((ing, i) => {
            const row = container.querySelector(`[data-ing-index="${i}"] .ing-unit`);
            if (row) row.value = ing.unit || '';
        });

        wireReviewHandlers();
    }

    function wireReviewHandlers() {
        el('addIng').addEventListener('click', () => {
            state.recipe.ingredients = state.recipe.ingredients || [];
            state.recipe.ingredients.push({ ingredient: '', quantity: '', unit: '', preparation: '', optional: false });
            renderReview();
        });
        el('addStep').addEventListener('click', () => {
            state.recipe.instructions = state.recipe.instructions || [];
            state.recipe.instructions.push({ step_title: '', instruction: '', tip: '' });
            renderReview();
        });
        document.querySelectorAll('[data-remove-ing]').forEach(btn => {
            btn.addEventListener('click', () => {
                const i = Number(btn.dataset.removeIng);
                collectReview();
                state.recipe.ingredients.splice(i, 1);
                renderReview();
            });
        });
        document.querySelectorAll('[data-remove-step]').forEach(btn => {
            btn.addEventListener('click', () => {
                const i = Number(btn.dataset.removeStep);
                collectReview();
                state.recipe.instructions.splice(i, 1);
                renderReview();
            });
        });
    }

    function collectReview() {
        const r = state.recipe;
        r.title = el('f-title').value.trim();
        r.description = el('f-description').value.trim();
        r.category = Array.from(document.querySelectorAll('input[name="category"]:checked')).map(c => c.value);
        r.subcategory = el('f-subcategory').value.split(',').map(s => s.trim()).filter(Boolean);
        r.region = el('f-region').value || null;
        r.difficulty = el('f-difficulty').value || null;
        r.servings = Number(el('f-servings').value) || null;
        r.prep_time_minutes = Number(el('f-prep').value) || null;
        r.cook_time_minutes = Number(el('f-cook').value) || null;
        r.total_time_minutes = Number(el('f-total').value) || null;
        r.tips = el('f-tips').value.trim();
        r.history = el('f-history').value.trim();
        r.tags = el('f-tags').value.split(',').map(s => s.trim()).filter(Boolean);

        r.ingredients = Array.from(document.querySelectorAll('[data-ing-index]')).map(row => ({
            quantity: row.querySelector('.ing-qty').value.trim(),
            unit: row.querySelector('.ing-unit').value,
            ingredient: row.querySelector('.ing-name').value.trim(),
            preparation: row.querySelector('.ing-prep').value.trim(),
            optional: row.querySelector('.ing-opt').checked,
        })).filter(i => i.ingredient);

        r.instructions = Array.from(document.querySelectorAll('[data-step-index]')).map(row => ({
            step_title: row.querySelector('.step-title').value.trim(),
            instruction: row.querySelector('.step-instr').value.trim(),
            tip: row.querySelector('.step-tip').value.trim(),
        })).filter(s => s.instruction);
    }

    el('backToUpload').addEventListener('click', () => goStep(1));

    el('saveDraftBtn').addEventListener('click', async () => {
        collectReview();
        if (!state.recipe.title) {
            alert('El título es obligatorio');
            return;
        }
        el('saveDraftBtn').disabled = true;
        el('saveDraftBtn').textContent = 'Guardando...';
        try {
            const res = await fetch('/api/import/save', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    recipe: state.recipe,
                    image_token: state.imageToken,
                    image_filename: state.imageFilename,
                }),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || ('HTTP ' + res.status));
            state.pageId = data.page_id;
            state.panelUrl = data.panel_url;
            el('editInPanelLink').href = data.panel_url;
            goStep(4);
        } catch (err) {
            alert('Error al guardar: ' + err.message);
        } finally {
            el('saveDraftBtn').disabled = false;
            el('saveDraftBtn').textContent = 'Guardar borrador →';
        }
    });

    // Step 4: translate + reset
    async function translate(lang) {
        const btn = lang === 'fr' ? el('translateFrBtn') : el('translateEnBtn');
        const statusEl = el('translationStatus');
        btn.disabled = true;
        const originalText = btn.textContent;
        btn.textContent = 'Traduciendo...';
        try {
            const res = await fetch('/api/import/translate', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ page_id: state.pageId, lang: lang }),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || ('HTTP ' + res.status));
            const msg = document.createElement('p');
            msg.className = 'translation-success';
            msg.textContent = '✓ Traducción a ' + (lang === 'fr' ? 'Francés' : 'Inglés') + ' guardada';
            statusEl.appendChild(msg);
            btn.textContent = '✓ ' + (lang === 'fr' ? 'Francés' : 'Inglés');
        } catch (err) {
            alert('Error al traducir: ' + err.message);
            btn.textContent = originalText;
            btn.disabled = false;
        }
    }

    el('translateFrBtn').addEventListener('click', () => translate('fr'));
    el('translateEnBtn').addEventListener('click', () => translate('en'));

    el('importAnother').addEventListener('click', () => {
        state.file = null;
        state.recipe = null;
        state.imageToken = null;
        state.pageId = null;
        fileInput.value = '';
        filePreview.hidden = true;
        extractBtn.disabled = true;
        el('translationStatus').innerHTML = '';
        el('translateFrBtn').disabled = false;
        el('translateFrBtn').textContent = 'Traducir a Francés';
        el('translateEnBtn').disabled = false;
        el('translateEnBtn').textContent = 'Traducir a Inglés';
        goStep(1);
    });
})();

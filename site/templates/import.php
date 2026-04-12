<?php
$user = $kirby->user();
if (!$user || $user->role()->name() !== 'admin') {
    go($site->url());
}
?>
<?php snippet('header') ?>

<section class="page-header">
    <div class="container">
        <h1 class="page-header__title"><?= $page->title() ?></h1>
        <p class="page-header__description">Sube una imagen de una receta y la IA extraerá el contenido para revisión.</p>
    </div>
</section>

<section class="section">
    <div class="container container--narrow">

        <div class="wizard" id="importWizard">
            <!-- Progress -->
            <div class="wizard__progress">
                <span class="wizard__step is-active" data-step="1">1. Subir</span>
                <span class="wizard__step" data-step="2">2. Extraer</span>
                <span class="wizard__step" data-step="3">3. Revisar</span>
                <span class="wizard__step" data-step="4">4. Guardar</span>
            </div>

            <!-- Step 1: Upload -->
            <div class="wizard__panel is-active" data-panel="1">
                <h2>Sube una imagen de la receta</h2>
                <p class="wizard__help">Formatos: JPG, PNG, WebP. Máximo 10 MB. Para PDFs, toma una captura de pantalla primero.</p>
                <label class="wizard__drop" id="dropZone">
                    <input type="file" id="fileInput" accept="image/jpeg,image/png,image/webp" hidden>
                    <div class="wizard__drop-inner">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="17 8 12 3 7 8"/>
                            <line x1="12" y1="3" x2="12" y2="15"/>
                        </svg>
                        <p>Arrastra una imagen o haz clic para seleccionar</p>
                    </div>
                </label>
                <div class="wizard__preview" id="filePreview" hidden>
                    <img id="previewImg" alt="">
                    <div class="wizard__preview-meta">
                        <span id="previewName"></span>
                        <button type="button" class="btn btn--ghost" id="removeFile">Quitar</button>
                    </div>
                </div>
                <div class="wizard__actions">
                    <button type="button" class="btn btn--primary" id="extractBtn" disabled>Extraer receta con IA →</button>
                </div>
            </div>

            <!-- Step 2: Loading -->
            <div class="wizard__panel" data-panel="2">
                <div class="wizard__loading">
                    <div class="wizard__spinner"></div>
                    <p>Extrayendo receta...</p>
                    <p class="wizard__help">Esto puede tardar 10-30 segundos.</p>
                </div>
            </div>

            <!-- Step 3: Review -->
            <div class="wizard__panel" data-panel="3">
                <h2>Revisa la receta extraída</h2>
                <p class="wizard__help">Edita cualquier campo antes de guardar como borrador.</p>
                <div id="reviewForm"></div>
                <div class="wizard__actions">
                    <button type="button" class="btn btn--ghost" id="backToUpload">← Volver</button>
                    <button type="button" class="btn btn--primary" id="saveDraftBtn">Guardar borrador →</button>
                </div>
            </div>

            <!-- Step 4: Saved -->
            <div class="wizard__panel" data-panel="4">
                <div class="wizard__saved">
                    <div class="wizard__check">✓</div>
                    <h2>Borrador guardado</h2>
                    <p>La receta se guardó como borrador. Ahora puedes:</p>
                    <div class="wizard__actions wizard__actions--center">
                        <a href="#" class="btn btn--primary" id="editInPanelLink" target="_blank">Abrir en el panel</a>
                        <button type="button" class="btn btn--secondary" id="translateFrBtn">Traducir a Francés</button>
                        <button type="button" class="btn btn--secondary" id="translateEnBtn">Traducir a Inglés</button>
                    </div>
                    <div id="translationStatus"></div>
                    <div class="wizard__actions wizard__actions--center">
                        <button type="button" class="btn btn--ghost" id="importAnother">Importar otra receta</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<script src="<?= url('assets/js/recipe-importer.js') ?>?v=<?= filemtime(kirby()->root('assets') . '/js/recipe-importer.js') ?>" defer></script>

<?php snippet('footer') ?>

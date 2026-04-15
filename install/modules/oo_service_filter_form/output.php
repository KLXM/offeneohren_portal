<?php
$title = trim('REX_VALUE[1]');
$filters = rex_offeneohren_portal_service_finder::getRequestFilters();
$districts = rex_offeneohren_portal_service_finder::districtOptions($filters);
$groups = rex_offeneohren_portal_service_finder::groupOptions($filters);
$languages = rex_offeneohren_portal_service_finder::languageOptions($filters);
$isFiltered = ($filters['q'] !== '' || $filters['district_id'] > 0 || $filters['group_id'] > 0 || $filters['language_id'] > 0);
?>
<section id="oo-filter-container" class="oo-filter uk-section uk-section-xsmall">
    <div class="uk-container uk-container-large">
        <?php if ('' !== $title): ?>
            <h2 class="uk-heading-divider uk-margin-small-bottom"><?= rex_escape($title) ?></h2>
        <?php endif ?>

        <div class="uk-card uk-card-default uk-card-body uk-padding-small">
            <form id="oo-service-filter-form" method="get" class="uk-form-stacked uk-grid-small" uk-grid>

                                <div class="uk-width-1-1 uk-width-3-4@l">
                    <div class="uk-grid-collapse" uk-grid>
                        <div class="uk-width-1-1 uk-width-1-3@s">
                            <label class="uk-form-label" for="oo-district">Zuständigkeitsbereich</label>
                            <select class="uk-select" id="oo-district" name="district_id">
                                <?php foreach ($districts as $id => $label): ?>
                                    <?php if ($id === -1): ?>
                                        <option disabled><?= rex_escape($label) ?></option>
                                    <?php else: ?>
                                        <option value="<?= (int) $id ?>"<?= (int) $filters['district_id'] === (int) $id ? ' selected' : '' ?>><?= rex_escape($label) ?></option>
                                    <?php endif; ?>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <div class="uk-width-1-1 uk-width-1-3@s">
                            <label class="uk-form-label" for="oo-group">Thema</label>
                            <select class="uk-select" id="oo-group" name="group_id">
                                <?php foreach ($groups as $id => $label): ?>
                                    <option value="<?= (int) $id ?>"<?= (int) $filters['group_id'] === (int) $id ? ' selected' : '' ?>><?= rex_escape($label) ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <div class="uk-width-1-1 uk-width-1-3@s">
                            <label class="uk-form-label" for="oo-language">Sprache</label>
                            <select class="uk-select" id="oo-language" name="language_id">
                                <?php foreach ($languages as $id => $label): ?>
                                    <option value="<?= (int) $id ?>"<?= (int) $filters['language_id'] === (int) $id ? ' selected' : '' ?>><?= rex_escape($label) ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="uk-width-1-1 uk-width-1-4@l">
                    <label class="uk-form-label" for="oo-q">Freitextsuche (Live)</label>
                    <div class="uk-flex uk-flex-middle" style="gap: 10px;">
                        <div class="uk-search uk-search-default uk-width-expand">
                            <span uk-search-icon></span>
                            <input class="uk-search-input" id="oo-q" type="search" name="q" value="<?= rex_escape($filters['q']) ?>" placeholder="Name, Ort, Beschreibung" autocomplete="off">
                        </div>
                        
                        <div id="oo-reset-container" style="<?= !$isFiltered ? 'display: none;' : '' ?>">
                            <a href="<?= rex_getUrl(rex_article::getCurrentId()) ?>#oo-filter-container" class="uk-button uk-button-default" id="oo-filter-reset" title="Filter aufheben" uk-tooltip="Filter aufheben">Zurücksetzen</a>
                        </div>
                    </div>
                </div>

                <button type="submit" class="uk-hidden" aria-hidden="true" tabindex="-1">Filtern</button>
            </form>
        </div>
    </div>
</section>

<script>
(function () {
    const form = document.getElementById('oo-service-filter-form');
    if (!form) {
        return;
    }

    const inputQ = form.querySelector('#oo-q');
    const selectFields = form.querySelectorAll('select');
    let timer = null;

    const runLiveSearch = function () {
        const url = new URL(window.location.href);
        const params = new URLSearchParams(new FormData(form));

        // Remove empty/neutral filter values from URL.
        let hasFilter = false;
        for (const [key, value] of params.entries()) {
            if (value === '' || value === '0') {
                params.delete(key);
            } else {
                hasFilter = true;
            }
        }
        
        // Show or hide the reset X icon dynamically 
        const resetContainer = document.getElementById('oo-reset-container');
        if (resetContainer) {
            resetContainer.style.display = hasFilter ? '' : 'none';
        }

        url.search = params.toString();

        fetch(url.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(function (response) {
                return response.text();
            })
            .then(function (html) {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const nextResults = doc.getElementById('oo-service-results');
                const currentResults = document.getElementById('oo-service-results');
                
                const nextFilter = doc.getElementById('oo-filter-container');
                const currentFilter = document.getElementById('oo-filter-container');

                if (!nextResults || !currentResults) {
                    window.location.href = url.toString();
                    return;
                }

                currentResults.replaceWith(nextResults);
                
                if (currentFilter && nextFilter) {
                    // Update selects without replacing the whole form to avoid input focus/blur issues
                    ['oo-district', 'oo-group', 'oo-language'].forEach(function(id) {
                        const nextSelect = nextFilter.querySelector('#' + id);
                        const currentSelect = document.getElementById(id);
                        if (nextSelect && currentSelect) {
                            currentSelect.innerHTML = nextSelect.innerHTML;
                        }
                    });
                }
                
                window.history.replaceState({}, '', url.toString());
            })
            .catch(function () {
                window.location.href = url.toString();
            });
    };

    const debounceRun = function () {
        window.clearTimeout(timer);
        timer = window.setTimeout(runLiveSearch, 280);
    };

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        runLiveSearch();
    });

    if (inputQ) {
        inputQ.addEventListener('input', debounceRun);
    }

    selectFields.forEach(function (field) {
        field.addEventListener('change', runLiveSearch);
    });
})();
</script>

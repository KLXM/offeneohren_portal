<?php
$title = trim('REX_VALUE[1]');
$csrf = rex_csrf_token::factory('oo_submission_form');
$result = null;

if ('POST' === rex_server('REQUEST_METHOD', 'string', '') && rex_post('oo_submit', 'bool')) {
    $result = rex_offeneohren_portal_submission_service::handlePublicSubmission((array) $_POST);
}

$districts = rex_offeneohren_portal_service_finder::districtOptions();
$groups = rex_offeneohren_portal_service_finder::groupOptions();
$languages = rex_offeneohren_portal_service_finder::languageOptions();
?>
<section class="uk-section uk-section-small oo-submission-form">
    <div class="uk-container uk-container-large">
        <?php if ('' !== $title): ?>
            <h2 class="uk-heading-small"><?= rex_escape($title) ?></h2>
        <?php endif ?>

        <div class="uk-alert-primary" uk-alert>
            <p>Meldungen werden redaktionell geprüft und nicht sofort live übernommen.</p>
        </div>

        <?php if (is_array($result)): ?>
            <?php if ($result['success']): ?>
                <div class="uk-alert-success" uk-alert><p><?= rex_escape($result['message']) ?></p></div>
            <?php else: ?>
                <div class="uk-alert-danger" uk-alert><p><?= rex_escape($result['message']) ?></p></div>
            <?php endif ?>
        <?php endif ?>

        <?php if (!$result || !$result['success']): ?>
        <form method="post" class="uk-form-stacked uk-grid-small" uk-grid>
            <?= $csrf->getHiddenField() ?>
            <input type="hidden" name="oo_submit" value="1">
            <input type="hidden" name="started_at" value="<?= time() ?>">
            <input type="hidden" name="submission_type" value="new">

            <div style="position:absolute;left:-9999px;top:-9999px;" aria-hidden="true">
                <label for="oo-website">Website</label>
                <input id="oo-website" type="text" name="website" value="" tabindex="-1" autocomplete="off">
            </div>

            <div class="uk-width-1-1 uk-width-1-2@m">
                <label class="uk-form-label" for="oo-reporter-name">Ihr Name</label>
                <input class="uk-input" id="oo-reporter-name" type="text" name="reporter_name" maxlength="191" required>
            </div>

            <div class="uk-width-1-1 uk-width-1-2@m">
                <label class="uk-form-label" for="oo-reporter-email">Ihre E-Mail</label>
                <input class="uk-input" id="oo-reporter-email" type="email" name="reporter_email" maxlength="191" required>
            </div>

            <div class="uk-width-1-1 uk-width-1-2@m">
                <label class="uk-form-label" for="oo-city">Ort <span class="uk-text-danger">*</span></label>
                <input class="uk-input" id="oo-city" type="text" name="city" required>
            </div>

            <div class="uk-width-1-1 uk-width-1-2@m">
                <label class="uk-form-label" for="oo-name">Name/Organisation <span class="uk-text-danger">*</span></label>
                <input class="uk-input" id="oo-name" type="text" name="name" required>
            </div>

            <div class="uk-width-1-1 uk-width-1-2@m">
                <label class="uk-form-label" for="oo-district">Zuständigkeitsbereiche</label>
                <select class="uk-select oo-multi-select" id="oo-district" name="district_ids[]" multiple>
                    <?php foreach ($districts as $id => $label): ?>
                        <option value="<?= (int) $id ?>"><?= rex_escape($label) ?></option>
                    <?php endforeach ?>
                </select>
            </div>

            <div class="uk-width-1-1 uk-width-1-2@m">
                <label class="uk-form-label" for="oo-phone">Telefon</label>
                <input class="uk-input" id="oo-phone" type="tel" pattern="[0-9\s\+]+" title="Bitte nur Ziffern, Leerzeichen und +" name="phone" placeholder="z.B. 069 1234567">
            </div>

            <div class="uk-width-1-1 uk-width-1-2@m">

                <label class="uk-form-label" for="oo-email">E-Mail</label>
                <input class="uk-input" id="oo-email" type="email" name="email" placeholder="mail@beispiel.de">
            </div>

            <div class="uk-width-1-1 uk-width-1-2@m">
                <label class="uk-form-label" for="oo-group-ids">Themen</label>
                <select class="uk-select oo-multi-select" id="oo-group-ids" name="group_ids[]" multiple required>
                    <?php foreach ($groups as $id => $label): if ((int) $id === 0) continue; ?>
                        <option value="<?= (int) $id ?>"><?= rex_escape($label) ?></option>
                    <?php endforeach ?>
                </select>
            </div>

            <div class="uk-width-1-1 uk-width-1-2@m">
                <label class="uk-form-label" for="oo-language-ids">Sprachen</label>
                <select class="uk-select oo-multi-select" id="oo-language-ids" name="language_ids[]" multiple required>
                    <?php foreach ($languages as $id => $label): if ((int) $id === 0) continue; ?>
                        <option value="<?= (int) $id ?>"><?= rex_escape($label) ?></option>
                    <?php endforeach ?>
                </select>
            </div>

            <div class="uk-width-1-1 uk-width-1-2@m">
                <label class="uk-form-label" for="oo-office-hours">Sprechzeiten</label>
                <textarea class="uk-textarea" id="oo-office-hours" name="office_hours" rows="3"></textarea>
            </div>
            
            <div class="uk-width-1-1 uk-width-1-2@m">
                <label class="uk-form-label" for="oo-focus">Schwerpunkte</label>
                <textarea class="uk-textarea" id="oo-focus" name="focus" rows="3"></textarea>
            </div>
            
            <div class="uk-width-1-1 uk-width-1-2@m">
                <label class="uk-form-label" for="oo-carer-qualification">Qualifikation</label>
                <input class="uk-input" id="oo-carer-qualification" type="text" name="carer_qualification">
            </div>

            <div class="uk-width-1-1">
                <label class="uk-form-label" for="oo-description">Beschreibung <span class="uk-text-danger">*</span></label>
                <textarea class="uk-textarea" id="oo-description" name="description" rows="5" required></textarea>
            </div>

            <div class="uk-width-1-1 uk-width-1-2@m">
                <label class="uk-form-label" for="oo-url">Website</label>
                <input class="uk-input" id="oo-url" type="url" name="url" placeholder="https://">
            </div>

            <div class="uk-width-1-1 uk-width-1-2@m">
                <label class="uk-form-label" for="oo-url-chat">Chat-URL</label>
                <input class="uk-input" id="oo-url-chat" type="url" name="url_chat" placeholder="https://">
            </div>

            <div class="uk-width-1-1 uk-margin-top">
                <label><input class="uk-checkbox" type="checkbox" name="privacy" required> <?= nl2br(rex_escape(\FriendsOfRedaxo\TemplateManager\TemplateManager::get('tm_privacy_notice', 'Ihre Kontaktdaten werden nur zum Zwecke der Bearbeitung dieser Anfrage gespeichert. Keine Weitergabe an Dritte.'))) ?> <a href="/datenschutz/" target="_blank">Datenschutz</a></label>
            </div>

            <div class="uk-width-1-1">
                <button class="uk-button uk-button-primary" type="submit">Eintrag vorschlagen</button>
            </div>
        </form>
        <?php endif; ?>
    </div>
</section>

<style>
.oo-select-container {
    position: relative;
    width: 100%;
}
.oo-select-button {
    width: 100%;
    padding: 10px 15px;
    font-size: 16px;
    cursor: pointer;
    background-color: #fff;
    color: #333;
    border: 1px solid #e5e5e5;
    border-radius: 4px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    transition: all 0.3s ease;
}
.oo-select-button[disabled] {
    background-color: #f8f8f8;
    color: #999;
    cursor: not-allowed;
}
.oo-select-button.oo-selected {
    background-color: #1e87f0;
    color: white;
    border-color: #1e87f0;
}
.oo-selected-count {
    padding: 2px 6px;
    background-color: rgba(255, 255, 255, 0.3);
    color: inherit;
    border-radius: 10px;
    font-size: 14px;
    margin-left: 10px;
    flex-shrink: 0;
}
.oo-select-modal {
    position: fixed;
    z-index: 1010;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.6);
    display: flex;
    justify-content: center;
    align-items: center;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease;
}
.oo-select-modal.oo-active {
    opacity: 1;
    pointer-events: auto;
}
.oo-select-modal-content {
    background-color: #fff;
    padding: 20px;
    border: 1px solid #888;
    width: 90%;
    max-width: 800px;
    border-radius: 5px;
    transition: all 0.3s ease;
    transform: scale(0.9);
    opacity: 0;
    display: flex;
    flex-direction: column;
    max-height: 90vh;
}
.oo-select-modal.oo-active .oo-select-modal-content {
    transform: scale(1);
    opacity: 1;
}
.oo-modal-panes {
    display: flex;
    flex-direction: column;
    margin-top: 15px;
    overflow: hidden;
}
@media (min-width: 640px) {
    .oo-modal-panes {
        flex-direction: row;
    }
}
.oo-options-pane, .oo-selected-pane {
    flex: 1;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    max-height: 300px;
    overflow-y: auto;
    background: #fafafa;
}
@media (min-width: 640px) {
    .oo-options-pane {
        margin-right: 15px;
        margin-bottom: 0;
    }
}
.oo-selected-pane {
    background-color: #f2f9f2;
}
.oo-select-modal-close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    line-height: 1;
}
.oo-select-modal-close:hover {
    color: black;
}
.oo-search-input {
    width: 100%;
    padding: 10px;
    margin-top: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
}
.oo-options-container label, .oo-selected-option {
    display: block;
    margin-bottom: 8px;
    cursor: pointer;
    background: #fff;
    padding: 6px 10px;
    border-radius: 3px;
    border: 1px solid #eee;
}
.oo-options-container label:hover,
.oo-options-container label:focus-within {
    background: #eef7ff;
    outline: 2px solid #2933F0;
    outline-offset: -1px;
}
.oo-modal-buttons {
    text-align: right;
    margin-top: 20px;
}
.oo-modal-buttons button {
    cursor: pointer;
}
/* Custom Button Styles */
.oo-select-button {
    border-radius: 0 !important;
}
.oo-select-modal-content {
    border-radius: 0 !important;
}
.oo-options-pane, .oo-selected-pane, .oo-search-input {
    border-radius: 0 !important;
}
.oo-modal-buttons button {
    border-radius: 0 !important;
    box-shadow: none !important;
}
.oo-modal-buttons .oo-cancel-button,
.oo-modal-buttons .oo-deselect-all-button {
    background-color: #fff !important;
    color: #333 !important;
    border: 1px solid #ccc !important;
}
.oo-modal-buttons .oo-save-button {
    background-color: #2933F0 !important;
    color: #fff !important;
    border: 1px solid #2933F0 !important;
}

/* Custom Validation and Selected Styles */
.oo-select-button.oo-selected {
    background-color: transparent !important;
    color: inherit !important;
    border: 1px solid green !important;
}
.oo-selected-count {
    background-color: #eee !important;
    color: #333 !important;
}
/* Wenn Pflichtfeld und nichts ausgewählt */
select[required] + .oo-select-button:not(.oo-selected) {
    border: 1px solid red !important;
}

/* Standardsytling für normale Formularfelder (kein farbiger Glow) */
.oo-submission-form .uk-input:not([disabled]),
.oo-submission-form .uk-textarea:not([disabled]),
.oo-submission-form .uk-select:not([disabled]),
.oo-select-button:not([disabled]) {
    border: 1px solid #e5e5e5 !important;
    background-color: #fff !important;
    transition: all 0.2s ease-in-out;
}

/* Fokussierter Zustand der Felder mit starkem Glow */
.oo-submission-form .uk-input:not([disabled]):focus,
.oo-submission-form .uk-textarea:not([disabled]):focus,
.oo-submission-form .uk-select:not([disabled]):focus,
.oo-select-button:not([disabled]):focus,
.oo-select-button:not([disabled]).oo-active-focus {
    border-color: #2933F0 !important;
    box-shadow: 0 0 8px rgba(41, 51, 240, 0.4) !important;
    outline: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function initCustomSelect(selectElement) {
        
        let container = selectElement.parentElement;
        if (!container.classList.contains('oo-select-container')) {
            const wrapper = document.createElement('div');
            wrapper.className = 'oo-select-container';
            selectElement.parentNode.insertBefore(wrapper, selectElement);
            wrapper.appendChild(selectElement);
            container = wrapper;
        }

        const isMultiple = selectElement.multiple;
        selectElement.style.display = 'none';

        let optionsData = Array.from(selectElement.options).map(option => ({
            value: option.value,
            text: option.text,
            selected: option.selected
        }));

        const oldBtn = container.querySelector('.oo-select-button');
        if (oldBtn) oldBtn.remove();

        const selectButton = document.createElement('button');
        selectButton.className = 'oo-select-button uk-button';
        selectButton.type = 'button';
        selectButton.setAttribute('aria-haspopup', 'dialog');
        selectButton.setAttribute('aria-expanded', 'false');
        
        if (selectElement.hasAttribute('disabled')) {
            selectButton.setAttribute('disabled', 'disabled');
        }

        const selectLabelText = (selectElement.id === 'oo-district') ? 'Zuständigkeitsbereiche' : 
                                (selectElement.id === 'oo-group-ids') ? 'Themen' : 
                                (selectElement.id === 'oo-language-ids') ? 'Sprachen' : 'Bitte wählen...';
        
        const btnTextSpan = document.createElement('span');
        selectButton.appendChild(btnTextSpan);

        const selectedCount = document.createElement('span');
        selectedCount.className = 'oo-selected-count';
        container.appendChild(selectButton);

        const modalId = 'oo-select-modal-' + Math.random().toString(36).substr(2, 9);
        const modal = document.createElement('div');
        modal.id = modalId;
        modal.className = 'oo-select-modal';
        modal.setAttribute('role', 'dialog');
        modal.setAttribute('aria-modal', 'true');
        modal.setAttribute('aria-label', selectLabelText);
        modal.innerHTML = `
            <div class="oo-select-modal-content">
                <div class="uk-text-right"><button type="button" class="oo-select-modal-close" aria-label="Schließen" style="background:none;border:none;padding:0;">&times;</button></div>
                <h3 class="uk-margin-remove-top">${selectLabelText}</h3>
                <input type="text" class="oo-search-input" placeholder="Suchen...">
                <div class="uk-text-meta uk-text-small uk-margin-small-top uk-margin-small-bottom">Tasten: [↓] [↑] Liste navigieren, [Enter] Speichern, [Tab] Springt zu den Buttons</div>
                <div class="oo-modal-panes">
                    <div class="oo-options-pane">
                        <h4 class="uk-text-small uk-margin-small-bottom">Verfügbar</h4>
                        <div class="oo-options-container"></div>
                    </div>
                    <div class="oo-selected-pane">
                        <h4 class="uk-text-small uk-margin-small-bottom">Ausgewählt</h4>
                        <div class="oo-selected-container"></div>
                    </div>
                </div>
                <div class="oo-modal-buttons">
                    <button type="button" class="oo-cancel-button uk-button uk-button-default uk-button-small uk-margin-small-right">Abbrechen</button>
                    ${isMultiple ? '<button type="button" class="oo-deselect-all-button uk-button uk-button-default uk-button-small uk-margin-small-right">Alle abwählen</button>' : ''}
                    <button type="button" class="oo-save-button uk-button uk-button-primary uk-button-small">Übernehmen</button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);

        const closeBtn = modal.querySelector('.oo-select-modal-close');
        const searchInput = modal.querySelector('.oo-search-input');
        const optionsContainer = modal.querySelector('.oo-options-container');
        const selectedContainer = modal.querySelector('.oo-selected-container');
        const cancelButton = modal.querySelector('.oo-cancel-button');
        const saveButton = modal.querySelector('.oo-save-button');
        const deselectAllButton = modal.querySelector('.oo-deselect-all-button');

        function updateModalUI() {
            optionsContainer.innerHTML = optionsData.map((option, idx) => {
                const optText = option.text.trim().toLowerCase();
                if (option.value === "" || option.value === "0" || optText === "bitte wählen" || optText === "bitte wählen...") {
                    return "";
                }
                return `
                <label>
                    <input type="${isMultiple ? 'checkbox' : 'radio'}" 
                           class="uk-${isMultiple ? 'checkbox' : 'radio'}"
                           name="temp_opt_${selectElement.id}" 
                           value="${option.value}"
                           data-index="${idx}"
                           tabindex="-1"
                           ${option.selected ? 'checked' : ''}> <span class="uk-margin-small-left">${option.text}</span>
                </label>
                `;
            }).join('');

            selectedContainer.innerHTML = optionsData
                .filter(option => {
                    const optText = option.text.trim().toLowerCase();
                    return option.selected && option.value !== "0" && option.value !== "" && optText !== "bitte wählen" && optText !== "bitte wählen...";
                })
                .map(option => `<div class="oo-selected-option">${option.text}</div>`)
                .join('');
        }

        function updateButtonUI() {
            const selectedOptions = Array.from(selectElement.options).filter(opt => {
                const optText = opt.text.trim().toLowerCase();
                return opt.selected && opt.value !== "0" && opt.value !== "" && optText !== "bitte wählen" && optText !== "bitte wählen...";
            });
            if (selectedOptions.length === 0) {
                btnTextSpan.textContent = 'Bitte wählen...';
                selectButton.classList.remove('oo-selected');
                if (selectButton.contains(selectedCount)) selectedCount.remove();
            } else if (selectedOptions.length > 0 && selectedOptions.length <= 3) {
                btnTextSpan.textContent = selectedOptions.map(opt => opt.text).join(', ');
                selectButton.classList.add('oo-selected');
                if (selectButton.contains(selectedCount)) selectedCount.remove();
            } else {
                btnTextSpan.textContent = `${selectedOptions.length} ausgewählt`;
                selectButton.classList.add('oo-selected');
                selectedCount.textContent = selectedOptions.length;
                selectButton.appendChild(selectedCount);
            }
            
            if (selectElement.hasAttribute('disabled')) {
                selectButton.setAttribute('disabled', 'disabled');
            } else {
                selectButton.removeAttribute('disabled');
            }
        }

        function openModal() {
            if (selectButton.hasAttribute('disabled')) return;
            selectButton.setAttribute('aria-expanded', 'true');
            optionsData = Array.from(selectElement.options).map(option => ({
                value: option.value,
                text: option.text,
                selected: option.selected
            }));
            updateModalUI();
            modal.classList.add('oo-active');
            searchInput.value = '';
            searchInput.dispatchEvent(new Event('input')); // reset search
            
            // Fokus für Tastatur-Nutzer in das Modal setzen
            setTimeout(() => searchInput.focus(), 50);
        }

        function closeModal() {
            modal.classList.remove('oo-active');
            selectButton.setAttribute('aria-expanded', 'false');
            selectButton.focus(); // Fokus zurück auf Button
        }

        selectButton.addEventListener('click', function(e) {
            e.preventDefault();
            openModal();
        });
        
        closeBtn.addEventListener('click', closeModal);
        
        modal.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeModal();

            // Prevent page scroll natively when using arrows inside the options container
            if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                e.preventDefault();
            }

            // Focus Trap
            if (e.key === 'Tab') {
                const focusable = modal.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
                if (focusable.length) {
                    const firstElement = focusable[0];
                    const lastElement = focusable[focusable.length - 1];

                    if (e.shiftKey) { // Shift + Tab
                        if (document.activeElement === firstElement) {
                            e.preventDefault();
                            lastElement.focus();
                        }
                    } else { // Tab
                        if (document.activeElement === lastElement) {
                            e.preventDefault();
                            firstElement.focus();
                        }
                    }
                }
            }
        });

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            Array.from(optionsContainer.children).forEach(label => {
                const optionText = label.textContent.toLowerCase();
                label.style.display = optionText.includes(searchTerm) ? '' : 'none';
            });
        });

        // Pfeiltasten Navigation und Enter zum reibungslosen Übernehmen
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                const visibleInputs = Array.from(optionsContainer.children)
                    .filter(label => label.style.display !== 'none')
                    .map(label => label.querySelector('input'));
                if (visibleInputs.length > 0) visibleInputs[0].focus();
            }
            if (e.key === 'Enter') {
                e.preventDefault();
                saveButton.click();
            }
        });

        optionsContainer.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                e.preventDefault();
                const visibleInputs = Array.from(optionsContainer.children)
                    .filter(label => label.style.display !== 'none')
                    .map(label => label.querySelector('input'));
                const currentIndex = visibleInputs.indexOf(document.activeElement);
                if (currentIndex > -1) {
                    let nextIndex = e.key === 'ArrowDown' ? currentIndex + 1 : currentIndex - 1;
                    if (nextIndex >= 0 && nextIndex < visibleInputs.length) {
                        visibleInputs[nextIndex].focus();
                    }
                }
            }
            if (e.key === 'Enter') {
                e.preventDefault();
                saveButton.click();
            }
        });

        optionsContainer.addEventListener('change', function(e) {
            if (e.target.tagName === 'INPUT') {
                const idx = parseInt(e.target.getAttribute('data-index'));
                if (!isMultiple) {
                    optionsData.forEach(opt => opt.selected = false);
                }
                optionsData[idx].selected = e.target.checked;
                updateModalUI();
            }
        });
        
        cancelButton.addEventListener('click', closeModal);

        if (deselectAllButton) {
            deselectAllButton.addEventListener('click', function(e) {
                e.preventDefault();
                optionsData.forEach(opt => opt.selected = false);
                updateModalUI();
            });
        }

        saveButton.addEventListener('click', function() {
            Array.from(selectElement.options).forEach((option, index) => {
                option.selected = optionsData[index].selected;
            });
            selectElement.dispatchEvent(new Event('change', {bubbles: true}));
            updateButtonUI();
            closeModal();
        });

        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'disabled') {
                    updateButtonUI();
                }
            });
        });
        observer.observe(selectElement, { attributes: true });

        updateButtonUI();
    }

    document.querySelectorAll('select.oo-multi-select').forEach(initCustomSelect);
});
</script>

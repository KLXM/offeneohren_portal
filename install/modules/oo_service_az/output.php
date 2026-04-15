<?php
$sql = rex_sql::factory();

// Alle aktiven Services alphabetisch sortiert abrufen
$services = $sql->getArray('SELECT id, name, city, district_id FROM ' . rex::getTable('yf_service') . ' WHERE status = 1 ORDER BY name ASC');

// Beziehe die verknüpften Landkreise
$districts = rex_offeneohren_portal_service_finder::districtOptions();

// Detail Artikel ID (falls konfiguriert), ansonsten den aktuellen Artikel nutzen
$detailArticleId = rex_config::get('offeneohren_portal', 'detail_article_id', 0);
if ($detailArticleId == 0) {
    $detailArticleId = rex_article::getCurrentId();
}

$groupedServices = [];
$letters = [];

foreach ($services as $srv) {
    $name = trim((string)$srv['name']);
    if (empty($name)) continue;

    // Erster Buchstabe normalisiert
    $firstChar = mb_strtoupper(mb_substr($name, 0, 1));

    // Sonderzeichen und Umlaute als "#" behandeln (falls gewollt)
    // oder Umlaute speziell einordnen, hier der einfache Weg: Nur A-Z und ÄÖÜ erhalten
    if (!preg_match('/^[A-ZÄÖÜ]$/iu', $firstChar)) {
        $firstChar = '#';
    }

    // Landkreise formatieren
    $currentDistricts = array_filter(array_map('intval', explode(',', (string) $srv['district_id'])));
    $srvDistrictNames = [];
    foreach ($currentDistricts as $dId) {
        if (isset($districts[$dId])) {
            $srvDistrictNames[] = $districts[$dId];
        }
    }
    $srv['district_info'] = !empty($srvDistrictNames) ? implode(', ', $srvDistrictNames) : '';

    $groupedServices[$firstChar][] = $srv;
}

// Alphabetisch sortieren der Schlüssel
ksort($groupedServices);
// Falls '#' existiert, packen wir es ans Ende
if (isset($groupedServices['#'])) {
    $hashGrp = $groupedServices['#'];
    unset($groupedServices['#']);
    $groupedServices['#'] = $hashGrp;
}

$letters = array_keys($groupedServices);
?>

<section class="uk-section uk-section-default">
    <div class="uk-container uk-container-small">

        <?php if (!empty($letters)): ?>
            <!-- Quicklinks -->
            <div class="uk-margin-medium-bottom uk-card uk-card-body uk-card-muted uk-padding-small uk-text-center">
                <div class="uk-flex uk-flex-center uk-flex-wrap" style="gap: 5px;">
                    <?php foreach ($letters as $l): ?>
                        <a href="#buchstabe-<?= rex_escape($l === '#' ? 'hash' : $l) ?>" class="uk-button uk-button-default uk-button-small" style="min-width: 40px;"><?= rex_escape($l) ?></a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Live Search -->
            <div class="uk-margin-medium-bottom">
                <div class="uk-inline uk-width-1-1">
                    <span class="uk-form-icon" uk-icon="icon: search"></span>
                    <input class="uk-input uk-form-large" type="text" id="oo-az-livesearch" placeholder="Nach Angebot, Ort oder Landkreis suchen...">
                </div>
            </div>

            <!-- A-Z Liste -->
            <?php foreach ($groupedServices as $letter => $groupList): ?>
                <div class="uk-margin-large-bottom oo-letter-group">
                    <h2 id="buchstabe-<?= rex_escape($letter === '#' ? 'hash' : $letter) ?>" class="uk-heading-bullet oo-letter-header">
                        <span><?= rex_escape($letter) ?></span>
                    </h2>

                    <div class="uk-child-width-1-2@m uk-grid-small uk-grid-match uk-margin-top" uk-grid>
                        <?php foreach ($groupList as $srv): 
                            $detailUrl = '#';
                            if (class_exists('\Url\Url')) {
                                $detailUrl = rex_getUrl($detailArticleId, null, ['service_id' => $srv['id'], 'az_id' => rex_article::getCurrentId()]);
                            } else {
                                $detailUrl = rex_getUrl($detailArticleId, null, ['service_id' => $srv['id'], 'az_id' => rex_article::getCurrentId()]);
                            }
                        ?>
                            <div class="oo-service-entry" data-search="<?= rex_escape(mb_strtolower($srv['name'] . ' ' . $srv['city'] . ' ' . $srv['district_info'])) ?>">
                                <div class="uk-card uk-card-default uk-card-small uk-card-hover uk-transition-toggle">
                                    <div class="uk-card-body uk-flex uk-flex-column uk-height-1-1">
                                        <!-- Titel -->
                                        <h4 class="uk-card-title uk-margin-remove-bottom">
                                            <a href="<?= $detailUrl ?>" class="uk-link-heading" title="Details ansehen">
                                                <?= rex_escape($srv['name']) ?>
                                            </a>
                                        </h4>
                                        <!-- Footer / Landkreis Info -->
                                        <div class="uk-margin-auto-top uk-margin-small-top uk-text-meta">
                                            <?php if ($srv['city']): ?>
                                                <span uk-icon="location" class="uk-icon"></span> <strong><?= rex_escape($srv['city']) ?></strong>
                                            <?php endif; ?>
                                            <?php if (!empty($srv['district_info'])): ?>
                                                <span class="uk-text-muted uk-margin-small-left"><?= rex_escape($srv['district_info']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <a href="<?= $detailUrl ?>" class="uk-position-cover"></a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="uk-margin-medium-top uk-text-right">
                        <a href="#" uk-totop uk-scroll>Nach oben</a>
                    </div>
                </div>
            <?php endforeach; ?>

        <?php else: ?>
            <div class="uk-alert uk-alert-warning">Keine Angebote vorhanden.</div>
        <?php endif; ?>

    </div>
    
    <!-- Floating To-Top Button -->
    <a href="#" id="oo-totop-button" class="uk-icon-button uk-button-primary uk-position-fixed uk-position-z-index" uk-icon="chevron-up" uk-scroll style="bottom: 30px; right: 30px; display: none; width: 50px; height: 50px; box-shadow: 0 5px 15px rgba(0,0,0,0.2);"></a>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('oo-az-livesearch');
    const totopBtn = document.getElementById('oo-totop-button');
    
    // To-Top Button visibility
    if (totopBtn) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                totopBtn.style.display = 'inline-flex';
            } else {
                totopBtn.style.display = 'none';
            }
        });
    }

    if (!searchInput) return;

    const letterGroups = document.querySelectorAll('.oo-letter-group');
    const allEntries = document.querySelectorAll('.oo-service-entry');

    searchInput.addEventListener('input', function(e) {
        const term = e.target.value.toLowerCase().trim();

        if (term === '') {
            letterGroups.forEach(function(g) { g.style.display = ''; });
            allEntries.forEach(function(el) { el.style.display = ''; });
            return;
        }

        letterGroups.forEach(function(group) {
            let hasVisibleCards = false;
            const cards = group.querySelectorAll('.oo-service-entry');
            
            cards.forEach(function(card) {
                const searchable = card.getAttribute('data-search');
                if (searchable && searchable.includes(term)) {
                    card.style.display = '';
                    hasVisibleCards = true;
                } else {
                    card.style.display = 'none';
                }
            });

            if (hasVisibleCards) {
                group.style.display = '';
            } else {
                group.style.display = 'none';
            }
        });
    });
});
</script>

<?php
$overviewArticleId = (int) 'REX_LINK[1]';
$serviceId = (int) rex_request('service_id', 'int', rex_request('id', 'int', 0));

if (class_exists('\Url\Url')) {
    $resolved = \Url\Url::resolveCurrent();
    if ($resolved) {
        $serviceId = (int) $resolved->getDatasetId();
    }
}

if ($serviceId <= 0) {
    echo '<div class="uk-alert uk-alert-warning">Kein Eintrag gefunden oder ausgewählt.</div>';
    return;
}

$service = rex_offeneohren_portal_service::get($serviceId);
if (!$service || $service->getValue('status') != 1) {
    echo '<div class="uk-alert uk-alert-danger">Dieser Eintrag ist nicht mehr verfügbar.</div>';
    return;
}

$changeArticleId = rex_config::get('offeneohren_portal', 'change_article_id', 0);
$districts = rex_offeneohren_portal_service_finder::districtOptions();
$groups = rex_offeneohren_portal_service_finder::groupOptions();
$languages = rex_offeneohren_portal_service_finder::languageOptions();

$currentDistricts = array_filter(array_map('intval', explode(',', (string) $service->getValue('district_id'))));
$districtNames = [];
foreach ($currentDistricts as $dId) {
    if (isset($districts[$dId])) $districtNames[] = $districts[$dId];
}

$currentLangs = [];
$col = $service->getRelatedCollection('language_ids');
if ($col) {
    $currentLangs = array_map('intval', $col->getIds());
}
$langNames = [];
foreach ($currentLangs as $lId) {
    if (isset($languages[$lId])) $langNames[] = $languages[$lId];
}

// Finde verwandte Einträge basierend auf Gruppen und Zuständigkeitsbereich
$relatedServices = [];

// ==========================================
// Schema.org / JSON-LD Data für SEO
// (Telefonnummern ausgenommen, da Freitext)
// ==========================================
$schemaData = [
    '@context' => 'https://schema.org',
    '@type' => 'NGO', // Non-Governmental Organization / Hilfsorganisation
    'name' => $service->getValue('name'),
];

$desc = trim(strip_tags((string)$service->getValue('description')));
if (!empty($desc)) {
    $schemaData['description'] = $desc;
}

$url = trim((string)$service->getValue('url'));
if (!empty($url)) {
    // Falls kein Protokoll davor steht, https anhängen für Schema
    if (!preg_match('~^(?:f|ht)tps?://~i', $url)) {
        $url = 'https://' . $url;
    }
    $schemaData['url'] = $url;
}

$city = trim((string)$service->getValue('city'));
if (!empty($city)) {
    $schemaData['address'] = [
        '@type' => 'PostalAddress',
        'addressLocality' => $city,
        'addressRegion' => 'Hessen',
        'addressCountry' => 'DE'
    ];
}

// JSON-LD direkt ins Frontend schreiben
echo '<script type="application/ld+json">' . json_encode($schemaData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";


$groupIds = $service->getRelatedCollection('group_ids') ? $service->getRelatedCollection('group_ids')->getIds() : [];
if (!empty($groupIds) && !empty($currentDistricts)) {
    $sqlFilter = rex_sql::factory();
    $grpFilter = implode(',', array_map('intval', $groupIds));
    
    $districtWhere = [];
    foreach ($currentDistricts as $dId) {
        $districtWhere[] = 'FIND_IN_SET(' . (int)$dId . ', district_id)';
    }
    // Zusätzlich Hessenweit (31) und Bundesweit (32) einschließen
    $districtWhere[] = 'FIND_IN_SET(31, district_id)';
    $districtWhere[] = 'FIND_IN_SET(32, district_id)';
    
    $districtSql = '(' . implode(' OR ', $districtWhere) . ')';

    $rows = $sqlFilter->getArray('SELECT id FROM ' . rex::getTable('yf_service') . ' s 
                                    WHERE status = 1 
                                    AND id != ? 
                                    AND ' . $districtSql . ' 
                                    AND id IN (SELECT service_id FROM ' . rex::getTable('yf_relation_service_group') . ' WHERE group_id IN ('.$grpFilter.')) 
                                    ORDER BY RAND() LIMIT 4',
                                    [(int)$service->getId()]);
    foreach ($rows as $row) {
        $relatedServices[] = rex_offeneohren_portal_service::get((int)$row['id']);
    }
}

// URLs im Freitext erkennen, kürzen (max. 40 Zeichen Anzeige) und verlinken
$linkifyText = static function(string $text): string {
    return preg_replace_callback(
        '#https?://\S+#i',
        static function(array $m): string {
            $href = htmlspecialchars($m[0], ENT_QUOTES, 'UTF-8');
            $display = preg_replace('#^https?://(www\.)?#i', '', $m[0]);
            if (mb_strlen($display) > 40) {
                $display = mb_substr($display, 0, 40) . '\u2026';
            }
            $display = htmlspecialchars($display, ENT_QUOTES, 'UTF-8');
            return '<a href="' . $href . '" target="_blank" rel="noopener">' . $display . '</a>';
        },
        nl2br(rex_escape($text))
    );
};
?>
<section class="uk-section uk-section-small">
    <div class="uk-container uk-container-small">
        
        <?php
        $azArticleId = rex_request('az_id', 'int', 0);
        // Filterparameter wiederherstellen, falls über Suchergebnisse aufgerufen
        $backParams = array_filter([
            'district_id' => rex_request('back_district', 'int', 0),
            'group_id'    => rex_request('back_group',    'int', 0),
            'language_id' => rex_request('back_lang',     'int', 0),
            'q'           => rex_request('back_q',        'string', ''),
        ], function($v){ return $v !== 0 && $v !== ''; });
        ?>
        <?php if ($azArticleId > 0): ?>
            <a href="<?= rex_getUrl($azArticleId) ?>" class="uk-button uk-button-text uk-margin-bottom"><span uk-icon="arrow-left"></span> Zurück zur A-Z Liste</a>
        <?php elseif ($overviewArticleId > 0): ?>
            <a href="<?= rex_escape(rex_getUrl($overviewArticleId, null, $backParams)) ?>" class="uk-button uk-button-text uk-margin-bottom"><span uk-icon="arrow-left"></span> Zurück zur Übersicht</a>
        <?php endif; ?>

        <div class="uk-card uk-card-default uk-overflow-hidden">
            <div class="uk-card-header uk-background-primary uk-light">
                <h1 class="uk-h2 uk-margin-small-bottom uk-light" style="color:#fff;"><?= rex_escape((string) $service->getValue('name')) ?></h1>
                <p class="uk-text-meta uk-margin-remove-top uk-light" style="color:rgba(255,255,255,0.7);">
                    <span uk-icon="location"></span> <?= rex_escape((string) $service->getValue('city')) ?>
                    <?php if (!empty($districtNames)) echo ' | ' . rex_escape(implode(', ', $districtNames)); ?>
                </p>
            </div>

            <div class="uk-card-body">
                <div class="uk-text-lead oo-description">
                    <?= nl2br(rex_escape((string) $service->getValue('description'))) ?>
                </div>

                <hr class="uk-margin-medium">

                <div class="uk-grid-divider uk-child-width-1-2@m" uk-grid>
                    <div>
                        <h3 class="uk-card-title uk-margin-small-bottom">Kontaktdaten</h3>
                        <ul class="uk-list">
                            <?php if ($phone = trim((string) $service->getValue('phone'))): ?>
                                <li><span uk-icon="receiver" class="uk-margin-small-right"></span> <strong>Telefon:</strong><br><?= rex_escape($phone) ?></li>
                            <?php endif; ?>
                            
                            <?php if ($email = trim((string) $service->getValue('email'))): ?>
                                <li class="uk-margin-small-top"><span uk-icon="mail" class="uk-margin-small-right"></span> <strong>E-Mail:</strong><br><a href="mailto:<?= rex_escape($email) ?>"><?= rex_escape($email) ?></a></li>
                            <?php endif; ?>
                            
                            <?php if ($url = trim((string) $service->getValue('url'))): ?>
                                <li class="uk-margin-small-top"><span uk-icon="world" class="uk-margin-small-right"></span> <strong>Website:</strong><br><a href="<?= rex_escape($url) ?>" target="_blank" rel="noopener"><?= preg_replace('#^https?://#i', '', $url) ?></a></li>
                            <?php endif; ?>
                            
                            <?php if ($urlChat = trim((string) $service->getValue('url_chat'))): ?>
                                <li class="uk-margin-small-top"><span uk-icon="comments" class="uk-margin-small-right"></span> <strong>Chat-URL:</strong><br><a href="<?= rex_escape($urlChat) ?>" target="_blank" rel="noopener">Zum Chat</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div>
                        <h3 class="uk-card-title uk-margin-small-bottom">Informationen</h3>
                        <?php if ($hours = trim((string) $service->getValue('office_hours'))): ?>
                            <div class="uk-margin-small-bottom">
                                <span uk-icon="clock" class="uk-margin-small-right"></span> <strong>Sprechzeiten:</strong><br>
                                <?= $linkifyText($hours) ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($focus = trim((string) $service->getValue('focus'))): ?>
                            <div class="uk-margin-small-bottom">
                                <span uk-icon="info" class="uk-margin-small-right"></span> <strong>Schwerpunkte:</strong><br>
                                <?= $linkifyText($focus) ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($qual = trim((string) $service->getValue('carer_qualification'))): ?>
                            <div class="uk-margin-small-bottom">
                                <span uk-icon="star" class="uk-margin-small-right"></span> <strong>Qualifikation:</strong><br>
                                <?= $linkifyText($qual) ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($langNames)): ?>
                            <div class="uk-margin-top">
                                <span uk-icon="comment" class="uk-margin-small-right"></span> <strong>Sprachen:</strong> 
                                <div class="uk-margin-small-top">
                                    <?php foreach ($langNames as $l): ?>
                                        <span class="uk-label uk-label-success uk-margin-small-right uk-margin-small-bottom"><?= rex_escape($l) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <hr>
                <div class="uk-flex uk-flex-between uk-flex-middle">
                    <div>
                        <button class="uk-button uk-button-default uk-button-small" onclick="navigator.clipboard.writeText(window.location.href); UIkit.notification({message: 'Link in die Zwischenablage kopiert!', status: 'primary', timeout: 2000});"><span uk-icon="copy"></span> Link kopieren</button>
                    </div>
                    <?php if ($changeArticleId > 0): ?>
                    <div class="uk-text-right">
                        <?php 
                        $changeUrlBase = rex_getUrl($changeArticleId);
                        $changeUrl = $changeUrlBase . (strpos($changeUrlBase, '?') !== false ? '&' : '?') . 'service_id=' . $service->getId();
                        ?>
                        <a href="<?= $changeUrl ?>" class="uk-link-muted" uk-tooltip="Änderung vorschlagen" aria-label="Änderung vorschlagen"><span uk-icon="icon: pencil" class="uk-margin-small-right"></span>Ändern / Problem melden</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if (!empty($relatedServices)): ?>
        <div class="uk-margin-large-top">
            <h3 class="uk-h3">Ähnliche Angebote</h3>
            <div class="uk-child-width-1-2@m uk-grid-small" uk-grid>
                <?php foreach ($relatedServices as $rService): 
                    // Let's rely on the URL Addon to get the actual permalink.
                    // If not configured, fall back to ?service_id=
                    $detailUrl = '#';
                    if (class_exists('\Url\Url')) {
                        // In URL v2+, URLs per Dataset generieren. Problem: Welches Profil? 
                        // The URL Addon usually overrides native rex_getUrl behavior if configured.
                        $detailUrl = rex_getUrl(rex_article::getCurrentId(), null, ['service_id' => $rService->getId()]); 
                    } else {
                        $detailUrl = rex_getUrl(rex_article::getCurrentId(), null, ['service_id' => $rService->getId()]); 
                    }
                ?>
                <div>
                    <div class="uk-card uk-card-default uk-card-small uk-card-body">
                        <a href="<?= $detailUrl ?>" class="uk-link-reset">
                            <h4 class="uk-card-title uk-margin-remove-bottom"><?= rex_escape((string) $rService->getValue('name')) ?></h4>
                            <p class="uk-text-meta uk-margin-remove-top"><span uk-icon="location"></span> <?= rex_escape((string) $rService->getValue('city')) ?></p>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

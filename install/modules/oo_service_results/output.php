<?php
$isPdfExport = (rex_request('pdfout', 'int') === 1);
if ($isPdfExport) {
    ob_start();
}

$title = trim('REX_VALUE[1]');
$limit = (int) 'REX_VALUE[2]';
if ($limit <= 0) {
    $limit = 100;
}

$filters = rex_offeneohren_portal_service_finder::getRequestFilters();
$isFiltered = ($filters['q'] !== '' || $filters['district_id'] > 0 || $filters['group_id'] > 0 || $filters['language_id'] > 0);

// View Mode Logic
$viewMode = 'grid';
if (isset($_COOKIE['oo_service_view']) && in_array((string)$_COOKIE['oo_service_view'], ['grid', 'list'])) {
    $viewMode = (string)$_COOKIE['oo_service_view'];
}

$services = [];
if ($isFiltered) {
    $services = rex_offeneohren_portal_service_finder::find($filters, $limit);
}

$changeArticleId = rex_config::get('offeneohren_portal', 'change_article_id', 0);
$newArticleId = rex_config::get('offeneohren_portal', 'new_article_id', 0);

// =========================================================================
// PDF EXPORT
// =========================================================================
if ($isPdfExport) {
    ob_end_clean(); // Discard frontend
    ob_start();
    ?>
    <!DOCTYPE html>
    <html lang="de">
    <head>
        <meta charset="utf-8">
        <title><?= rex_escape($title ?: rex_article::getCurrent()->getName()) ?></title>
        <style>
            @page { margin: 40px 50px; }
            body { font-family: Helvetica, Arial, sans-serif; font-size: 10pt; color: #333; line-height: 1.4; margin: 0; padding: 0; }
            
            .pdf-header { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #005a9e; }
            .pdf-header img { max-height: 80px; margin-bottom: 15px; }
            .pdf-header h1 { font-size: 18pt; margin: 0 0 5px 0; color: #005a9e; font-weight: normal; }
            .pdf-header p { font-size: 9pt; color: #666; margin: 0; }
            
            .pdf-service { margin-bottom: 25px; padding: 0; page-break-inside: avoid; }
            .pdf-service-title { font-size: 13pt; font-weight: bold; color: #005a9e; margin-top: 0; margin-bottom: 8px; border-bottom: 1px solid #ccc; padding-bottom: 4px; }
            
            .pdf-details-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
            .pdf-details-table td { padding: 3px 0; vertical-align: top; }
            .pdf-details-table td.label { width: 160px; font-weight: bold; color: #444; }
            .pdf-details-table td.value { color: #222; }
            
            .pdf-desc { margin-top: 8px; padding: 8px 12px; background-color: #f4f8fb; border-left: 3px solid #005a9e; font-size: 9.5pt; color: #444; }
            
            .pdf-links-table { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 9.5pt; }
            .pdf-links-table td { padding: 2px 0; }
            
            a { text-decoration: none; color: #005a9e; }
        </style>
    </head>
    <body>
        <div class="pdf-header">
            <?php 
            $logoPath = '';
            if (file_exists(rex_path::media('brand.png'))) {
                $logoPath = rex_path::media('brand.png');
            } elseif (file_exists(rex_path::media('logo.png'))) {
                $logoPath = rex_path::media('logo.png');
            }
            
            if ($logoPath) {
                $type = pathinfo($logoPath, PATHINFO_EXTENSION);
                $data = @file_get_contents($logoPath);
                if ($data) {
                    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    echo '<img src="'.$base64.'" alt="Logo"><br>';
                }
            }
            ?>
            <h1><?= rex_escape($title ?: rex_article::getCurrent()->getName()) ?></h1>
            <p>Generiert am <?= date('d.m.Y H:i') ?> | <?= count($services) ?> Ergebnisse</p>
        </div>

        <?php if (!$services): ?>
            <p>Keine passenden Einträge gefunden.</p>
        <?php else: ?>
            <?php foreach ($services as $service): ?>
                <?php 
                $districts = $service->getRelatedCollection('district_id');
                $districtNames = [];
                if ($districts) {
                    foreach ($districts as $d) {
                        $districtNames[] = $d->getValue('name');
                    }
                }
                
                $languages = $service->getRelatedCollection('language_ids');
                $langNames = [];
                if ($languages) {
                    foreach($languages as $l) {
                        $langNames[] = $l->getValue('name');
                    }
                }
                ?>
                <div class="pdf-service">
                    <h3 class="pdf-service-title"><?= rex_escape((string) $service->getValue('name')) ?></h3>
                    <table class="pdf-details-table">
                        <?php if (!empty($districtNames)): ?>
                        <tr><td class="label">Zuständigkeitsbereiche:</td><td class="value"><?= rex_escape(implode(', ', $districtNames)) ?></td></tr>
                        <?php endif; ?>
                        
                        <?php if ($city = trim((string) $service->getValue('city'))): ?>
                        <tr><td class="label">Ort:</td><td class="value"><?= rex_escape($city) ?></td></tr>
                        <?php endif; ?>
                        
                        <?php if ($phone = trim((string) $service->getValue('phone'))): ?>
                        <tr><td class="label">Telefon:</td><td class="value"><?= rex_escape($phone) ?></td></tr>
                        <?php endif; ?>
                        
                        <?php if ($email = trim((string) $service->getValue('email'))): ?>
                        <tr><td class="label">E-Mail:</td><td class="value"><a href="mailto:<?= rex_escape($email) ?>"><?= rex_escape($email) ?></a></td></tr>
                        <?php endif; ?>
                        
                        <?php if ($hours = trim((string) $service->getValue('office_hours'))): ?>
                        <tr><td class="label">Sprechzeiten:</td><td class="value"><?= nl2br(rex_escape($hours)) ?></td></tr>
                        <?php endif; ?>
                        
                        <?php if ($focus = trim((string) $service->getValue('focus'))): ?>
                        <tr><td class="label">Schwerpunkte:</td><td class="value"><?= nl2br(rex_escape($focus)) ?></td></tr>
                        <?php endif; ?>

                        <?php if ($qual = trim((string) $service->getValue('carer_qualification'))): ?>
                        <tr><td class="label">Qualifikation:</td><td class="value"><?= nl2br(rex_escape($qual)) ?></td></tr>
                        <?php endif; ?>

                        <?php if (!empty($langNames)): ?>
                        <tr><td class="label">Sprachen:</td><td class="value"><?= rex_escape(implode(', ', $langNames)) ?></td></tr>
                        <?php endif; ?>
                    </table>

                    <?php $desc = trim((string) $service->getValue('description')); ?>
                    <?php if ('' !== $desc): ?>
                        <div class="pdf-desc"><?= nl2br(rex_escape(mb_strimwidth($desc, 0, 360, '...'))) ?></div>
                    <?php endif ?>

                    <?php 
                    $url = trim((string) $service->getValue('url'));
                    $urlChat = trim((string) $service->getValue('url_chat'));
                    ?>
                    <?php if ('' !== $url || '' !== $urlChat): ?>
                        <table class="pdf-links-table">
                        <?php if ('' !== $url): ?>
                            <?php $displayUrl = preg_replace('#^https?://(www\.)?#', '', $url); ?>
                            <tr><td><strong>Website:</strong> <a href="<?= rex_escape($url) ?>"><?= rex_escape(mb_strimwidth($displayUrl, 0, 45, '...')) ?></a></td></tr>
                        <?php endif ?>
                        <?php if ('' !== $urlChat): ?>
                            <?php $displayChat = preg_replace('#^https?://(www\.)?#', '', $urlChat); ?>
                            <tr><td><strong>Chat:</strong> <a href="<?= rex_escape($urlChat) ?>"><?= rex_escape(mb_strimwidth($displayChat, 0, 45, '...')) ?></a></td></tr>
                        <?php endif ?>
                        </table>
                    <?php endif ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </body>
    </html>
    <?php
    $html = ob_get_clean();

    if (class_exists(\FriendsOfRedaxo\PdfOut\PdfOut::class)) {
        $pdf = new \FriendsOfRedaxo\PdfOut\PdfOut();
        $pdf->setName('Angebote-' . date('Y-m-d'));
        $pdf->setPaperSize('A4', 'portrait');
        $pdf->setAttachment(false);
        $pdf->setRemoteFiles(true);
        $pdf->setHtml($html);
        $pdf->run();
        exit;
    } else {
        echo '<div style="color:red;font-weight:bold;">Fehler: PdfOut AddOn nicht aktiv.</div>';
        exit;
    }
}

// =========================================================================
// REGULAR FRONTEND EXPORT 
// =========================================================================

// URLs im Freitext erkennen, kürzen (max. 40 Zeichen Anzeige) und verlinken
$linkifyText = static function(string $text): string {
    return preg_replace_callback(
        '#https?://\S+#i',
        static function(array $m): string {
            $href = htmlspecialchars($m[0], ENT_QUOTES, 'UTF-8');
            $display = preg_replace('#^https?://(www\.)?#i', '', $m[0]);
            if (mb_strlen($display) > 40) {
                $display = mb_substr($display, 0, 40) . '…';
            }
            $display = htmlspecialchars($display, ENT_QUOTES, 'UTF-8');
            return '<a href="' . $href . '" target="_blank" rel="noopener">' . $display . '</a>';
        },
        nl2br(rex_escape($text))
    );
};

$renderDetails = function($service, $districtNames, $langNames) use ($changeArticleId, $linkifyText) {
    ob_start();
    ?>
    <dl class="uk-text-small" style="display:grid; grid-template-columns: max-content 1fr; gap: 0.25rem 0.75rem; align-items: start; margin: 0 0 0.5rem;">
        <?php if (!empty($districtNames)): ?>
        <dt style="white-space:nowrap;"><span uk-icon="icon: location; ratio:0.8" class="uk-margin-small-right"></span><strong>Zuständigkeitsbereiche</strong></dt>
        <dd style="margin:0;"><?= rex_escape(implode(', ', $districtNames)) ?></dd>
        <?php endif; ?>

        <dt style="white-space:nowrap;"><span uk-icon="icon: home; ratio:0.8" class="uk-margin-small-right"></span><strong>Ort</strong></dt>
        <dd style="margin:0;"><?= rex_escape((string) $service->getValue('city')) ?></dd>

        <?php $phone = trim((string) $service->getValue('phone')); if ($phone): ?>
        <dt style="white-space:nowrap;"><span uk-icon="icon: receiver; ratio:0.8" class="uk-margin-small-right"></span><strong>Telefon</strong></dt>
        <dd style="margin:0;"><?= rex_escape($phone) ?></dd>
        <?php endif; ?>

        <?php if ($email = trim((string) $service->getValue('email'))): ?>
        <dt style="white-space:nowrap;"><span uk-icon="icon: mail; ratio:0.8" class="uk-margin-small-right"></span><strong>E-Mail</strong></dt>
        <dd style="margin:0;"><a href="mailto:<?= rex_escape($email) ?>"><?= rex_escape($email) ?></a></dd>
        <?php endif; ?>

        <?php if ($hours = trim((string) $service->getValue('office_hours'))): ?>
        <dt style="white-space:nowrap;"><span uk-icon="icon: clock; ratio:0.8" class="uk-margin-small-right"></span><strong>Sprechzeiten</strong></dt>
        <dd style="margin:0;"><?= $linkifyText($hours) ?></dd>
        <?php endif; ?>

        <?php if ($focus = trim((string) $service->getValue('focus'))): ?>
        <dt style="white-space:nowrap;"><span uk-icon="icon: info; ratio:0.8" class="uk-margin-small-right"></span><strong>Schwerpunkte</strong></dt>
        <dd style="margin:0;"><?= $linkifyText($focus) ?></dd>
        <?php endif; ?>

        <?php if ($qual = trim((string) $service->getValue('carer_qualification'))): ?>
        <dt style="white-space:nowrap;"><span uk-icon="icon: star; ratio:0.8" class="uk-margin-small-right"></span><strong>Qualifikation</strong></dt>
        <dd style="margin:0;"><?= $linkifyText($qual) ?></dd>
        <?php endif; ?>

        <?php if (!empty($langNames)): ?>
        <dt style="white-space:nowrap;"><span uk-icon="icon: comment; ratio:0.8" class="uk-margin-small-right"></span><strong>Sprachen</strong></dt>
        <dd style="margin:0;"><?= rex_escape(implode(', ', $langNames)) ?></dd>
        <?php endif; ?>
    </dl>

    <?php $desc = trim((string) $service->getValue('description')); ?>
    <?php if ('' !== $desc): ?>
        <p class="uk-margin-small-bottom uk-text-small"><?= nl2br(rex_escape(mb_strimwidth($desc, 0, 360, '...'))) ?></p>
    <?php endif ?>

    <?php 
    $url = trim((string) $service->getValue('url'));
    $urlChat = trim((string) $service->getValue('url_chat'));
    ?>
    <?php if ('' !== $url || '' !== $urlChat): ?>
        <ul class="uk-list uk-text-small uk-margin-top">
        <?php if ('' !== $url): ?>
            <?php $displayUrl = preg_replace('#^https?://(www\.)?#', '', $url); ?>
            <li><span uk-icon="world" class="uk-margin-small-right" aria-label="Website"></span> <a href="<?= rex_escape($url) ?>" target="_blank" rel="noopener"><?= rex_escape(mb_strimwidth($displayUrl, 0, 45, '...')) ?></a></li>
        <?php endif ?>
        <?php if ('' !== $urlChat): ?>
            <?php $displayChat = preg_replace('#^https?://(www\.)?#', '', $urlChat); ?>
            <li><span uk-icon="comment" class="uk-margin-small-right" aria-label="Chat"></span> <a href="<?= rex_escape($urlChat) ?>" target="_blank" rel="noopener">Chat: <?= rex_escape(mb_strimwidth($displayChat, 0, 45, '...')) ?></a></li>
        <?php endif ?>
        </ul>
    <?php endif ?>
    
    <div class="uk-margin-top uk-flex uk-flex-between uk-flex-middle">
        <div>
            <button class="uk-button uk-button-text uk-text-small uk-text-muted" onclick="navigator.clipboard.writeText(window.location.origin + '<?= rex_getUrl(rex_article::getCurrentId(), '', ['service_id' => $service->getId()]) ?>'); UIkit.notification({message: 'Link kopiert!', pos: 'bottom-center', timeout: 2000});"><span uk-icon="icon: copy" class="uk-margin-small-right"></span>Link kopieren</button>
        </div>
        <?php if ($changeArticleId > 0): ?>
        <div class="uk-text-right">
            <?php 
            $changeUrlBase = rex_getUrl($changeArticleId);
            $changeUrl = $changeUrlBase . (strpos($changeUrlBase, '?') !== false ? '&' : '?') . 'service_id=' . $service->getId();
            ?>
            <a href="<?= $changeUrl ?>" class="uk-link-muted uk-text-small" uk-tooltip="Änderung vorschlagen" aria-label="Änderung vorschlagen"><span uk-icon="icon: pencil" class="uk-margin-small-right"></span>Ändern / Problem</a>
        </div>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
};
?>
<section id="oo-service-results" class="oo-service-results uk-section uk-section-small uk-background-muted">
    <div class="uk-container uk-container-large">
        
        <?php if (!$isFiltered): ?>
            <div class="uk-panel oo-intro-text uk-margin-bottom">
                REX_VALUE[id=3 output=html]
            </div>
        <?php else: ?>

        <?php if ('' !== $title): ?>
            <h2 class="uk-heading-small"><?= rex_escape($title) ?></h2>
        <?php endif ?>

        <div class="uk-flex uk-flex-between uk-flex-middle uk-margin-bottom">
            <p class="uk-text-meta uk-margin-remove" aria-live="polite"><?= count($services) ?> Treffer</p>
            
            <div class="uk-flex uk-flex-middle">
                <div class="uk-button-group uk-margin-small-right">
                    <button type="button" onclick="window.ooSetView('grid');" class="uk-button uk-button-small <?= $viewMode === 'grid' ? 'uk-button-primary' : 'uk-button-default' ?> oo-view-toggle" data-view="grid" uk-tooltip="Kachel-Ansicht" aria-label="Kachel-Ansicht"><span uk-icon="grid"></span></button>
                    <button type="button" onclick="window.ooSetView('list');" class="uk-button uk-button-small <?= $viewMode === 'list' ? 'uk-button-primary' : 'uk-button-default' ?> oo-view-toggle" data-view="list" uk-tooltip="Listen-Ansicht" aria-label="Listen-Ansicht"><span uk-icon="list"></span></button>
                </div>
                
                <?php if ($newArticleId > 0): ?>
                    <a href="<?= rex_getUrl($newArticleId) ?>" class="uk-button uk-button-primary uk-button-small">Neuen Eintrag vorschlagen</a>
                <?php endif; ?>

                <?php
                $currentParams = $_GET;
                $currentParams['pdfout'] = 1;
                $pdfUrl = rex_getUrl(rex_article::getCurrentId(), rex_clang::getCurrentId(), $currentParams);
                ?>
                <a href="<?= $pdfUrl ?>" class="uk-button uk-button-default uk-button-small uk-margin-small-left" target="_blank" uk-tooltip="Als PDF exportieren"><span uk-icon="download"></span> PDF</a>
            </div>
        </div>

        <?php if (!$services): ?>
            <div class="uk-alert-warning" uk-alert>
                <p>Keine passenden Einträge gefunden.</p>
            </div>
        <?php else: ?>
            
            <!-- GRID VIEW -->
            <div id="oo-view-grid" class="oo-view-container uk-grid-medium uk-child-width-1-1 uk-child-width-1-2@m" uk-grid="masonry: true" <?= $viewMode !== 'grid' ? 'style="display:none;"' : '' ?>>
                <?php foreach ($services as $service): ?>
                    <?php 
                    $districts = $service->getRelatedCollection('district_id');
                    $districtNames = [];
                    if ($districts) {
                        foreach ($districts as $d) {
                            $districtNames[] = $d->getValue('name');
                        }
                    }
                    $languages = $service->getRelatedCollection('language_ids');
                    $langNames = [];
                    if ($languages) {
                        foreach($languages as $l) {
                            $langNames[] = $l->getValue('name');
                        }
                    }
                    ?>
                    <div>
                        <article class="uk-card uk-card-default uk-card-small uk-height-1-1">
                            <header class="uk-card-header uk-background-primary uk-light">
                                <h3 class="uk-card-title uk-light uk-margin-remove-bottom"><?= rex_escape((string) $service->getValue('name')) ?></h3>
                            </header>
                            <div class="uk-card-body">
                                <?= $renderDetails($service, $districtNames, $langNames) ?>
                            </div>
                        </article>
                    </div>
                <?php endforeach ?>
            </div>

            <!-- LIST VIEW -->
            <div id="oo-view-list" class="oo-view-container" <?= $viewMode !== 'list' ? 'style="display:none;"' : '' ?>>
                <ul uk-accordion="collapsible: false">
                <?php foreach ($services as $service): ?>
                    <?php 
                    $districts = $service->getRelatedCollection('district_id');
                    $districtNames = [];
                    if ($districts) {
                        foreach ($districts as $d) {
                            $districtNames[] = $d->getValue('name');
                        }
                    }
                    $languages = $service->getRelatedCollection('language_ids');
                    $langNames = [];
                    if ($languages) {
                        foreach($languages as $l) {
                            $langNames[] = $l->getValue('name');
                        }
                    }
                    ?>
                    <li class="uk-card uk-card-default uk-card-small uk-margin-bottom uk-overflow-hidden">
                        <a class="uk-accordion-title uk-card-header uk-background-primary uk-light uk-display-block" href="#">
                            <h3 class="uk-card-title uk-light uk-margin-remove-bottom" style="font-size: 1.1rem; line-height: 1.4;"><?= rex_escape((string) $service->getValue('name')) ?></h3>
                        </a>
                        <div class="uk-accordion-content uk-card-body uk-margin-remove-top">
                            <?= $renderDetails($service, $districtNames, $langNames) ?>
                        </div>
                    </li>
                <?php endforeach ?>
                </ul>
            </div>

        <?php endif ?>
        <?php endif; ?>
    </div>
    <script>
    if (!window.ooSetView) {
        window.ooSetView = function(mode) {
            document.cookie = 'oo_service_view=' + mode + '; path=/; max-age=31536000';
            
            var viewGrid = document.getElementById('oo-view-grid');
            var viewList = document.getElementById('oo-view-list');
            var btnsGrid = document.querySelectorAll('.oo-view-toggle[data-view="grid"]');
            var btnsList = document.querySelectorAll('.oo-view-toggle[data-view="list"]');
            
            if (viewGrid && viewList) {
                viewGrid.style.display = (mode === 'grid') ? '' : 'none';
                viewList.style.display = (mode === 'list') ? '' : 'none';
                
                if (mode === 'grid' && window.UIkit) {
                    UIkit.update(viewGrid, 'update');
                }
            }
            
            btnsGrid.forEach(function(btn) {
                btn.classList.remove('uk-button-primary', 'uk-button-default');
                btn.classList.add(mode === 'grid' ? 'uk-button-primary' : 'uk-button-default');
            });
            
            btnsList.forEach(function(btn) {
                btn.classList.remove('uk-button-primary', 'uk-button-default');
                btn.classList.add(mode === 'list' ? 'uk-button-primary' : 'uk-button-default');
            });
        };
    }
    </script>
</section>

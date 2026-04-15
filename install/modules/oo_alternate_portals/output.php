<?php
$sql = rex_sql::factory();
$portals = $sql->getArray('SELECT id, name, url FROM ' . rex::getTable('yf_alternate') . ' ORDER BY name ASC');
?>

<section class="uk-section uk-section-default oo-alternate-portals">
    <div class="uk-container uk-container-small">
        
        <?php if (!empty($portals)): ?>
            <div class="uk-child-width-1-2@m uk-grid-small uk-grid-match" uk-grid>
                <?php foreach ($portals as $portal): 
                    $url = trim((string)$portal['url']);
                    $name = trim((string)$portal['name']);
                    if (empty($url) || empty($name)) continue;
                    
                    // Simple URL cleaning for display
                    $displayUrl = preg_replace('#^https?://(www\.)?#', '', $url);
                ?>
                    <div>
                        <div class="uk-card uk-card-default uk-card-small uk-card-hover uk-transition-toggle">
                            <div class="uk-card-body uk-flex uk-flex-middle">
                                <div class="uk-margin-small-right">
                                    <span class="uk-icon-button uk-button-default uk-text-primary" uk-icon="link" style="width: 40px; height: 40px;"></span>
                                </div>
                                <div class="uk-width-expand">
                                    <h4 class="uk-card-title uk-margin-remove-bottom uk-text-truncate" title="<?= rex_escape($name) ?>">
                                        <a href="<?= rex_escape($url) ?>" target="_blank" rel="noopener" class="uk-link-heading">
                                            <?= rex_escape($name) ?>
                                        </a>
                                    </h4>
                                    <div class="uk-text-meta uk-text-truncate uk-margin-small-top" title="<?= rex_escape($displayUrl) ?>">
                                        <?= rex_escape($displayUrl) ?>
                                    </div>
                                </div>
                            </div>
                            <a href="<?= rex_escape($url) ?>" target="_blank" rel="noopener" class="uk-position-cover"></a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="uk-alert uk-alert-warning">Keine alternativen Portale vorhanden.</div>
        <?php endif; ?>
        
    </div>
</section>
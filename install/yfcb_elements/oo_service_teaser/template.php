<?php
$headline = $slice['headline'] ?? '';
$text = $slice['text'] ?? '';
$buttonLabel = $slice['button_label'] ?? '';
$buttonUrl = $slice['button_url'] ?? '';
?>
<div class="uk-card uk-card-default uk-card-body">
    <?php if ($headline): ?>
        <h3 class="uk-card-title"><?= rex_escape($headline) ?></h3>
    <?php endif ?>

    <?php if ($text): ?>
        <p><?= nl2br(rex_escape($text)) ?></p>
    <?php endif ?>

    <?php if ($buttonLabel && $buttonUrl): ?>
        <p><a class="uk-button uk-button-primary" href="<?= rex_escape($buttonUrl) ?>"><?= rex_escape($buttonLabel) ?></a></p>
    <?php endif ?>
</div>

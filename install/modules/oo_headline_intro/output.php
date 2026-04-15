<?php
$headline = trim('REX_VALUE[1]');
$intro = trim('REX_VALUE[2]');

if ('' === $headline && '' === $intro) {
    return;
}
?>
<section class="oo-intro uk-section uk-section-small">
    <div class="uk-container uk-container-large">
        <?php if ('' !== $headline): ?>
            <h1 class="uk-heading-medium"><?= rex_escape($headline) ?></h1>
        <?php endif ?>

        <?php if ('' !== $intro): ?>
            <div class="uk-text-lead"><?= $intro ?></div>
        <?php endif ?>
    </div>
</section>

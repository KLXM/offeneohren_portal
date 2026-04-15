<?php
$headline = 'REX_VALUE[1]';
$subline = 'REX_VALUE[2]';
?>

<div class="uk-form-stacked uk-margin">
    <div class="uk-margin-small-bottom">
        <label class="uk-form-label" for="rx-headline">Hauptüberschrift</label>
        <div class="uk-form-controls">
            <input class="uk-input" type="text" id="rx-headline" name="REX_INPUT_VALUE[1]" value="<?= htmlspecialchars((string) $headline) ?>">
        </div>
    </div>
    
    <div class="uk-margin-small-bottom">
        <label class="uk-form-label" for="rx-subline">Untertext</label>
        <div class="uk-form-controls">
            <textarea class="uk-textarea" id="rx-subline" name="REX_INPUT_VALUE[2]" rows="3"><?= htmlspecialchars((string) $subline) ?></textarea>
        </div>
    </div>
    
    <div class="uk-alert uk-alert-primary" uk-alert>
        <p>Das Modul zieht automatisch <strong>logo.svg</strong> und <strong>ohr.svg</strong> aus dem Media-Pool. Das nötige CSS und JS für die Animationen ist integriert.</p>
    </div>
</div>

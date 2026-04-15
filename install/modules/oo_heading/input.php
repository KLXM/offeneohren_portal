<fieldset class="form-horizontal">
    <legend>Überschrift</legend>

    <div class="form-group">
        <label class="col-sm-2 control-label" for="headline_text">Text</label>
        <div class="col-sm-10">
            <input class="form-control" type="text" id="headline_text" name="REX_INPUT_VALUE[1]" value="REX_VALUE[1]" />
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label" for="headline_tag">HTML Tag</label>
        <div class="col-sm-10">
            <select class="form-control" id="headline_tag" name="REX_INPUT_VALUE[2]">
                <?php
                foreach (['h1', 'h2', 'h3', 'h4', 'h5', 'h6'] as $tag) {
                    $selected = ('REX_VALUE[2]' == $tag) ? ' selected="selected"' : '';
                    if ('REX_VALUE[2]' == '' && $tag == 'h2') $selected = ' selected="selected"';
                    echo '<option value="'.$tag.'"'.$selected.'>'.strtoupper($tag).'</option>';
                }
                ?>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label" for="headline_class">UIkit Größe</label>
        <div class="col-sm-10">
            <select class="form-control" id="headline_class" name="REX_INPUT_VALUE[3]">
                <?php
                $sizes = [
                    '' => 'Standard (wie Tag)',
                    'uk-h1' => 'uk-h1',
                    'uk-h2' => 'uk-h2',
                    'uk-h3' => 'uk-h3',
                    'uk-h4' => 'uk-h4',
                    'uk-h5' => 'uk-h5',
                    'uk-h6' => 'uk-h6',
                    'uk-heading-primary' => 'Primary (uk-heading-primary)',
                    'uk-heading-medium' => 'Medium (uk-heading-medium)',
                    'uk-heading-large' => 'Large (uk-heading-large)',
                    'uk-heading-xlarge' => 'X-Large (uk-heading-xlarge)',
                    'uk-heading-2xlarge' => '2X-Large (uk-heading-2xlarge)',
                ];
                foreach ($sizes as $val => $label) {
                    $selected = ('REX_VALUE[3]' == $val) ? ' selected="selected"' : '';
                    echo '<option value="'.$val.'"'.$selected.'>'.$label.'</option>';
                }
                ?>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label" for="headline_align">Ausrichtung</label>
        <div class="col-sm-10">
            <select class="form-control" id="headline_align" name="REX_INPUT_VALUE[4]">
                <?php
                $aligns = [
                    '' => 'Links (Standard)',
                    'uk-text-center' => 'Zentriert',
                    'uk-text-right' => 'Rechts'
                ];
                foreach ($aligns as $val => $label) {
                    $selected = ('REX_VALUE[4]' == $val) ? ' selected="selected"' : '';
                    echo '<option value="'.$val.'"'.$selected.'>'.$label.'</option>';
                }
                ?>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label" for="headline_style">Besonderer Stil</label>
        <div class="col-sm-10">
            <select class="form-control" id="headline_style" name="REX_INPUT_VALUE[5]">
                <?php
                $styles = [
                    '' => 'Kein Stil',
                    'uk-heading-divider' => 'Divider (uk-heading-divider)',
                    'uk-heading-bullet' => 'Bullet (uk-heading-bullet)',
                    'uk-heading-line' => 'Linie (uk-heading-line)'
                ];
                foreach ($styles as $val => $label) {
                    $selected = ('REX_VALUE[5]' == $val) ? ' selected="selected"' : '';
                    echo '<option value="'.$val.'"'.$selected.'>'.$label.'</option>';
                }
                ?>
            </select>
        </div>
    </div>
</fieldset>

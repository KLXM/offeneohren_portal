<?php
$text = 'REX_VALUE[id=1 output="html"]';
$tag = 'REX_VALUE[id=2]';
$class = 'REX_VALUE[id=3]';
$align = 'REX_VALUE[id=4]';
$style = 'REX_VALUE[id=5]';

// Fallback für HTML Tag
if (empty($tag)) {
    $tag = 'h2';
}

$classes = [];

// UIkit Größen-/Typografie-Klasse hinzufügen
if (!empty($class)) {
    $classes[] = $class;
}

// Ausrichtung hinzufügen
if (!empty($align)) {
    $classes[] = $align;
}

// Besondere Styles hinzufügen (z.B. uk-heading-divider oder uk-heading-line)
if (!empty($style)) {
    $classes[] = $style;
}

// Spezielles Handling für uk-heading-line (braucht in UIkit ein <span> Element)
if ($style === 'uk-heading-line') {
    $text = '<span>' . $text . '</span>';
}

// Classes formatieren
$classAttr = '';
if (count($classes) > 0) {
    $classAttr = ' class="' . implode(' ', array_unique($classes)) . '"';
}

// Nur ausgeben, wenn tatsächlich Text vorhanden ist
if (!empty(trim(strip_tags($text)))) {
    echo '<' . $tag . $classAttr . '>' . $text . '</' . $tag . '>';
} elseif (rex::isBackend()) {
    echo '<div class="alert alert-info">Bitte geben Sie einen Überschriftentext ein.</div>';
}
?>

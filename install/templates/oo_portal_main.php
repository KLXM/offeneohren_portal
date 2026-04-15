<?php
/**
 * UIKit Default Template
 *
 * @name UIKit Default Template
 * @description Flexibles, responsive Template mit Logo und Navigation in einer Leiste.
 *
 * DOMAIN_SETTINGS:
 *
 * --- Layout & Struktur [fa-solid fa-layer-group] ---
 * tm_navbar_container: select|Navbar Breite|uk-container uk-container-large-large:Weit,uk-container uk-container-large:Standard,uk-container uk-container-large-small:Eng,uk-container uk-container-large-xlarge:Extra Weit,uk-container uk-container-large-expand:Volle Breite|Container-Breite für die Navbar
 * tm_navbar_style: select|Navbar Stil|uk-navbar-container:Standard (mit Schatten),uk-navbar-transparent:Transparent (ohne Schatten)|Stil der Navigationsleiste
 * tm_navbar_color: select|Navbar Hintergrund|uk-background-default:Standard,uk-background-muted:Hell,uk-background-primary uk-light:Primärfarbe,uk-background-secondary uk-light:Sekundärfarbe,:Transparent|Hintergrundfarbe der Navbar
 * tm_navbar_sticky: select|Navbar Sticky|default:Immer sichtbar,show-on-up:Nur beim Hochscrollen,:Nicht fixiert|Soll die Navigation beim Scrollen fixiert werden?
 *
 * --- Barrierefreiheit [fa-solid fa-universal-access] [admin,developer] ---
 * tm_sa11y_enabled: checkbox|Sa11y Accessibility Checker||Aktiviert den Sa11y Accessibility Checker im Frontend
 *
 * --- Branding & Logo [fa-solid fa-palette] ---
 * tm_logo: media|Logo|logo.svg|Logo für Header und Footer
 * tm_logo_text: text|Logo Alt-Text|Meine Firma|Alternativer Text für das Logo
 * tm_logo_white: media|Logo Weiß|logo_white.svg|Weißes Logo für dunkle Hintergründe
 * tm_mobile_logo_enabled: checkbox|Separates Mobillogo|1|Soll ein separates Logo für mobile Geräte verwendet werden?
 * tm_mobile_logo_variant: select|Mobile Logo Variante|standard:Standard Logo,white:Weißes Logo,custom:Eigenes Mobillogo|Welches Logo soll auf mobilen Geräten verwendet werden?
 * tm_mobile_logo: media|Eigenes Mobillogo|logo.svg|Eigenes Logo für mobile Geräte (nur bei Variante "Eigenes Mobillogo")
 * tm_firma: text|Firmenname|Meine Firma|Firmenname für Copyright
 * tm_slogan: text|Slogan||Slogan im Footer
 *
 * --- Responsive Breakpoints [fa-solid fa-mobile-screen] ---
 * tm_logo_breakpoint: select|Desktop-Logo Breakpoint|s:Small (640px),m:Medium (960px),l:Large (1200px),xl:Extra Large (1600px)|Ab welcher Bildschirmbreite soll das Desktop-Logo angezeigt werden?
 * tm_nav_breakpoint: select|Navigation Breakpoint|s:Small (640px),m:Medium (960px),l:Large (1200px),xl:Extra Large (1600px)|Ab welcher Bildschirmbreite soll die Desktop-Navigation angezeigt werden?
 * tm_toggle_breakpoint: select|Mobile-Toggle Breakpoint|s:Small (640px),m:Medium (960px),l:Large (1200px),xl:Extra Large (1600px)|Bis zu welcher Bildschirmbreite soll der Mobile-Toggle angezeigt werden?
 * tm_search_breakpoint: select|Suchbutton Breakpoint|s:Small (640px),m:Medium (960px),l:Large (1200px),xl:Extra Large (1600px)|Ab welcher Bildschirmbreite soll der Suchbutton angezeigt werden?
 *
 * --- Navigation [fa-solid fa-bars] [admin,developer] ---
 * tm_nav_style: select|Navigationsstil|default:Links,centered:Zentriert,right:Rechts|Wähle den Stil der Hauptnavigation
 * tm_mobile_nav_icon: text|Mobile Nav Toggle Icon|plus|UIKit Icon für Mobile Navigation Toggle (plus, chevron-down, triangle-down)
 * tm_breadcrumb_enabled: checkbox|Breadcrumb Navigation|1|Soll die Breadcrumb-Navigation angezeigt werden?
 * tm_dropdown_rounded: checkbox|Dropdown Ecken abrunden|1|Soll das Dropdown-Menü abgerundete Ecken haben?
 * tm_dropdown_mode: select|Dropdown Modus|auto:Automatisch (basierend auf Anzahl),manual:Manuelle Einstellung|Soll die Dropdown-Breite automatisch oder manuell gesteuert werden?
 * tm_dropdown_width: select|Dropdown Breite|4:Breit (4),2:Schmal (2),3:Mittel (3),5:Extra Breit (5)|Breite des Dropdown-Menüs (uk-navbar-dropdown-width-X)
 * tm_dropdown_columns: select|Dropdown Spalten|2:2 Spalten,1:1 Spalte,3:3 Spalten|Anzahl der Spalten im Dropdown-Menü
 * tm_dropdown_auto_threshold: text|Auto-Schwellenwert|2|Ab wie vielen Einträgen wird im Auto-Modus 2-spaltig gewechselt?
 *
 * --- CTA Button [fa-solid fa-bullhorn] ---
 * tm_cta_enabled: checkbox|CTA Button anzeigen|1|Soll der Call-to-Action Button angezeigt werden?
 * tm_cta_text: text|CTA Button Text|Kontakt|Text des CTA Buttons
 * tm_cta_link: text|CTA Button Link|#kontakt|Link-Ziel des CTA Buttons
 * tm_cta_icon: text|CTA Button Icon|mail|UIKit Icon-Name (ohne "icon:" Präfix)
 * tm_cta_button_style: select|CTA Button Stil|uk-button-primary:Primär,uk-button-secondary:Sekundär,uk-button-default:Standard,uk-button-text:Textlink|Uikit-Button-Stile
 *
 * --- Suche [fa-solid fa-magnifying-glass] [admin,developer] ---
 * tm_search_enabled: checkbox|Suchfeld anzeigen|0|Soll ein Suchfeld in der Navbar angezeigt werden?
 * tm_search_article: article|Suchergebnisseite||Artikel-ID der Search It Ergebnisseite
 * tm_search_placeholder: text|Suchfeld Platzhaltertext|Suchen...|Platzhaltertext für das Suchfeld
 *
 * --- Social Media [fa-brands fa-facebook] ---
 * tm_social_links: social_links|Social Media Links|uk|Social-Media-Profile (UIKit Icons)
 *
 * --- Formulare [fa-solid fa-envelope] ---
 * tm_privacy_notice: textarea|Datenschutzhinweis Formular|Ihre Kontaktdaten werden nur zum Zwecke der Bearbeitung dieser Anfrage gespeichert. Keine Weitergabe an Dritte.|Der Hinweis, der mit der Checkbox beim Absenden akzeptiert werden muss (Link /datenschutz/ ist fix hinterlegt).
 *
 * --- Footer [fa-solid fa-sitemap] ---
 * tm_footer_color: select|Footer-Farbe|uk-background-secondary uk-light:Sekundärfarbe,uk-background-primary uk-light:Primärfarbe,uk-background-default:Standard,uk-background-muted:Hell|Uikit-Farben
 * tm_footer_contact: cke5|Kontaktblock|<p>HAGE - Hessische Arbeitsgemeinschaft für Gesundheitsförderung e.V.<br>Wildunger Straße 6/6a<br>60487 Frankfurt</p><p>Telefon: +49 69/ 713 76 78-0<br>Fax: +49 69/ 713 76 78-11<br><a href="mailto:info@hage.de">info@hage.de</a></p>|Adresse und Kontakt
 * tm_footer_hage_text: textarea|HAGE Text|Diese Homepage ist ein Projekt der Hessischen Arbeitsgemeinschaft für Gesundheitsförderung e.V. (HAGE). Die HAGE ist eine landesweit arbeitende Vereinigung und verbindet Akteure und Aktivitäten aus allen Bereichen und Arbeitsfeldern, die sich mit Gesundheitsförderung und Prävention beschäftigen.|Text für HAGE Projekt
 * tm_footer_hage_logo: media|HAGE Logo|brand-hage.svg|HAGE Logo
 * tm_footer_hmsi_text: textarea|HMSI Text|Das Hessische Ministerium für Soziales und Integration unterstützt das Projekt und bedankt sich im Namen der Landesregierung bei allen Akteuren für ihr Engagement und die Einstellung ihrer Angebote.|Text für HMSI Unterstützung
 * tm_footer_hmsi_logo: media|HMSI Logo|brand-hessen-subline.png|HMSI Logo
 * tm_footer_text: textarea|Danksagung / Text|Die HAGE und das Hessische Ministerium für Soziales und Integration danken der Fa. KLXM Crossmedia GmbH für die Unterstützung bei der Erstellung der aktuellen Homepage. Dank geht auch an die Firma YAKAMARA- Media für die initiale Umsetzung.|Unterstützer-Text (Agenturen)
 * tm_footer_logo_1: media|Logo 1 (KLXM)|brand_klxm.svg|Erstes Sponsoren-Logo
 * tm_footer_logo_1_url: text|Logo 1 URL|https://www.klxm.de/|Link für Logo 1
 * tm_footer_logo_2: media|Logo 2 (Yakamara)|brand-yakamara.svg|Zweites Sponsoren-Logo
 * tm_footer_logo_2_url: text|Logo 2 URL|https://www.yakamara.de/|Link für Logo 2
 * tm_footer_show_privacy_settings: checkbox|Datenschutzeinstellungen Link|1|Link zu Cookie-Einstellungen im Footer anzeigen
 * tm_footer_links: linklist|Rechtliche Links||Hier können Sie Artikel verlinken, z.B. Impressum, Datenschutz (Kommaseparierte IDs oder über Link-Picker)
 */

use FriendsOfRedaxo\NavigationArray\BuildArray;
use FriendsOfRedaxo\Sa11y\Sa11y;
use FriendsOfRedaxo\TemplateManager\TemplateManager;
use UikitThemeBuilder\DomainContext;
use UikitThemeBuilder\TemplateHelper;

setlocale(LC_TIME, 'de_DE.UTF8');
$ctype1 = 'REX_ARTICLE[ctype=1]';

if (array_key_exists('Modal', apache_request_headers()) && 'on' == apache_request_headers()['Modal']) {
    echo rex_extension::registerPoint(new rex_extension_point('OUTPUT_FILTER', $ctype1 . '<script>UIkit.scroll().scrollTo("#ajax-dialog");</script>'));
    exit;
}

// Hilfsfunktion zum Zählen aller Kinder (inkl. Enkel)
if (!function_exists('countAllChildren')) {
    function countAllChildren($categories) {
        $count = 0;
        foreach ($categories as $child) {
            $count++;
            if (!empty($child['children'])) {
                $count += countAllChildren($child['children']);
            }
        }
        return $count;
    }
}

// Navigation-Funktion für Desktop - UIKit Navbar kompatibel mit erweitertem Dropdown
if (!function_exists('buildNavigation')) {
    function buildNavigation($categories, $level = 0, $config = [])
    {
        // Config mit Defaults
        $defaults = [
            'rounded' => true,
            'mode' => 'auto',
            'width' => '4',
            'columns' => '2',
            'autoThreshold' => 2
        ];
        $config = array_merge($defaults, is_array($config) ? $config : ['rounded' => $config]);
        
        $output = [];

        foreach ($categories as $cat) {
            $liClass = '';
            $subnavi = '';
            $parentIcon = '';
            $aClass = '';

            // Styling für verschiedene Levels
            if ($level == 1) {
                $aClass = ' class="uk-text-bolder uk-margin-small-left"';
            } elseif ($level >= 2) {
                $aClass = ' class="uk-margin-small-left"';
            }

            // Parent-Klasse für Dropdown-Menüs - NUR auf Level 0
            if (!empty($cat['children']) && $level === 0) {
                $liClass = 'uk-parent';
                $parentIcon = '<span uk-navbar-parent-icon></span>';

                // Dropdown-Breite und Spalten bestimmen
                if ($config['mode'] === 'auto') {
                    // Automatischer Modus: Basierend auf Anzahl der Kinder
                    $totalChildren = countAllChildren($cat['children']);
                    
                    if ($totalChildren > $config['autoThreshold']) {
                        $columns = '2';
                        $width = '4';
                    } else {
                        $columns = '1';
                        $width = '2';
                    }
                } else {
                    // Manueller Modus: Feste Werte aus Config
                    $columns = $config['columns'];
                    $width = $config['width'];
                }
                
                // Grid und Dropdown-Klassen zusammenbauen
                $roundedClass = $config['rounded'] ? ' uk-border-rounded' : '';
                $dividerClass = $columns > 1 ? 'uk-grid-divider ' : '';
                $ulClass = $dividerClass . 'uk-grid-small uk-nav uk-navbar-dropdown-nav uk-child-width-1-' . $columns;
                // uk-background-default entfernt damit Customizer-Farben greifen
                $dropdownClass = 'uk-navbar-dropdown uk-navbar-dropdown-width-' . $width . $roundedClass . ' uk-margin-remove uk-padding-small';
                
                $gridAttr = ' uk-grid="masonry: true"';
                $scrollspyAttr = ' uk-scrollspy="target: > .uk-active; cls: uk-animation-fade; delay: 50; repeat: true"';

                // Dropdown-Menü erstellen
                $subnavi .= '<div tabindex="0" class="' . $dropdownClass . '">';
                $subnavi .= '<ul class="' . $ulClass . '"' . $gridAttr . $scrollspyAttr . '>';
                $subnavi .= buildNavigation($cat['children'], $level + 1, $config);
                $subnavi .= '</ul>';
                $subnavi .= '</div>';
            } elseif (!empty($cat['children']) && $level >= 1) {
                // Ab Level 1: Kinder als uk-nav-sub darunter anzeigen
                $subnavi .= '<ul class="uk-nav-sub">';
                $subnavi .= buildNavigation($cat['children'], $level + 1, $config);
                $subnavi .= '</ul>';
            }

            // Aktiver Punkt
            if ($cat['current']) {
                $liClass .= ($liClass ? ' ' : '') . 'uk-active';
            }

            // Link erstellen
            $link = '<a' . $aClass . ' href="' . $cat['url'] . '">' . htmlspecialchars($cat['catName']) . $parentIcon . '</a>';

            // Li-Element zusammenbauen
            $li = '<li' . ($liClass ? ' class="' . $liClass . '"' : '') . '>';
            $li .= $link . $subnavi;
            $li .= '</li>';

            $output[] = $li;
        }

        return implode("\n", $output);
    }
}

// Funktion zum Generieren von Link-Listen aus komma-getrennten Artikel-IDs
if (!function_exists('generateFooterSection')) {
    function generateFooterSection($linkString)
    {
        if (empty($linkString)) {
            return '';
        }

        $output = '';
        $linkIds = array_map('trim', explode(',', $linkString));

        foreach ($linkIds as $linkId) {
            if (empty($linkId) || !is_numeric($linkId)) {
                continue;
            }
            
            $article = rex_article::get($linkId);
            
            if ($article) {
                $articleName = $article->getValue('name');
                $output .= '<li><a href="' . rex_getUrl($linkId) . '">' . htmlspecialchars($articleName) . '</a></li>';
            }
        }

        return $output;
    }
}

// Mobile Navigation-Funktion - UIKit Nav mit separatem Toggle-Button
if (!function_exists('buildMobileNavigation')) {
    function buildMobileNavigation($categories, $level = 0, $toggleIcon = 'plus')
    {
        $output = [];

        foreach ($categories as $cat) {
            $liClass = 'level-' . $level;
            $subnavi = '';
            $toggleBtn = '';
            $isOpen = '';

            // Parent-Klasse mit Sub-Navigation
            if (!empty($cat['children'])) {
                $liClass .= ' uk-parent';

                // Wenn aktiv, uk-open hinzufügen
                if ($cat['current']) {
                    $isOpen = ' uk-open';
                }

                // Toggle-Button mit Icon
                $toggleBtn = '<a href="#" tabindex="0" class="nav-toggle-btn"><span uk-icon="icon: ' . htmlspecialchars($toggleIcon) . '; ratio: 1"></span></a>';

                // Sub-Navigation erstellen
                $subnavi .= '<ul class="uk-nav-sub">';
                $subnavi .= buildMobileNavigation($cat['children'], $level + 1, $toggleIcon);
                $subnavi .= '</ul>';
            }

            // Aktiver Punkt
            if ($cat['current']) {
                $liClass .= ' uk-active';
            }

            // Link erstellen - immer mit URL
            $catname = htmlspecialchars($cat['catName']);
            if ($cat['current']) {
                $catname = '<strong>' . $catname . '</strong>';
            }
            $link = '<a href="' . $cat['url'] . '">' . $catname . '</a>';

            // Li-Element zusammenbauen: Toggle-Button DANN Link (barrierefreie DOM-Reihenfolge, visuell rechts durch float)
            $li = '<li class="' . $liClass . '"' . $isOpen . '>';
            $li .= $toggleBtn . $link . $subnavi;
            $li .= '</li>';

            $output[] = $li;
        }

        return implode("\n", $output);
    }
}

// Navigation generieren
$nav_start = rex_yrewrite::getDomainByArticleId(rex_article::getCurrentId(), rex_clang::getCurrentId())->getMountId();

// Mobile Navigation mit Toggle-Button (Icon aus Template Manager, default: plus)
$mobileToggleIcon = TemplateManager::get('tm_mobile_nav_icon', 'plus');

// Template Manager Settings laden
$navbarContainer = TemplateManager::get('tm_navbar_container', 'uk-container uk-container-large-large');
$navbarStyle = TemplateManager::get('tm_navbar_style', 'uk-navbar-container uk-padding-remove');
$navbarColor = TemplateManager::get('tm_navbar_color', 'uk-background-default');
$sa11yEnabled = TemplateManager::get('tm_sa11y_enabled', '0');
$logoMedia = TemplateManager::get('tm_logo', 'logo.svg');
$logoWhite = TemplateManager::get('tm_logo_white', 'logo_white.svg');
$logoAltText = TemplateManager::get('tm_logo_text', 'Meine Firma');
$slogan = TemplateManager::get('tm_slogan', '');
$firma = TemplateManager::get('tm_firma', 'Meine Firma');
$navbarSticky = TemplateManager::get('tm_navbar_sticky', 'default');
$footerLogoEnabled = TemplateManager::get('tm_footer_logo_enabled', '1');
$mobileLogoEnabled = TemplateManager::get('tm_mobile_logo_enabled', '1');
$mobileLogoVariant = TemplateManager::get('tm_mobile_logo_variant', 'standard');
$mobileLogo = TemplateManager::get('tm_mobile_logo', $logoMedia);

// Responsive Breakpoints für einzelne Elemente
$logoBreakpoint = TemplateManager::get('tm_logo_breakpoint', 'l');
$navBreakpoint = TemplateManager::get('tm_nav_breakpoint', 'l');
$toggleBreakpoint = TemplateManager::get('tm_toggle_breakpoint', 'l');
$searchBreakpoint = TemplateManager::get('tm_search_breakpoint', 'l');

// CTA Button Einstellungen
$ctaEnabled = TemplateManager::get('tm_cta_enabled', '1');
$ctaText = TemplateManager::get('tm_cta_text', 'Kontakt');
$ctaLink = TemplateManager::get('tm_cta_link', '#kontakt');
$ctaIcon = TemplateManager::get('tm_cta_icon', 'mail');
$ctaButtonStyle = TemplateManager::get('tm_cta_button_style', 'uk-button-primary');

// Breadcrumb Einstellung
$breadcrumbEnabled = TemplateManager::get('tm_breadcrumb_enabled', '1');

// Dropdown Einstellungen
$dropdownRounded = TemplateManager::get('tm_dropdown_rounded', '1');
$dropdownMode = TemplateManager::get('tm_dropdown_mode', 'auto');
$dropdownWidth = TemplateManager::get('tm_dropdown_width', '4');
$dropdownColumns = TemplateManager::get('tm_dropdown_columns', '2');
$dropdownAutoThreshold = TemplateManager::get('tm_dropdown_auto_threshold', '2');

// Suchfeld Einstellungen
$searchEnabled = TemplateManager::get('tm_search_enabled', '0');
$searchArticle = TemplateManager::get('tm_search_article', '');
$searchPlaceholder = TemplateManager::get('tm_search_placeholder', 'Suchen...');

// Navigation Stil
$navStyle = TemplateManager::get('tm_nav_style', 'default');

// UIKit Theme aus Settings
$uikitTheme = TemplateManager::get('tm_uikit_theme', 'default');

// Navigation Array erstellen
$nav_start = rex_yrewrite::getDomainByArticleId(rex_article::getCurrentId(), rex_clang::getCurrentId())->getMountId();

$navigationArray = BuildArray::create()
    ->setStart(-1)
    ->setDepth(2)
    ->setIgnore(true)
    ->generate();

// Dropdown-Konfiguration zusammenstellen
$dropdownConfig = [
    'rounded' => '1' == $dropdownRounded,
    'mode' => $dropdownMode,
    'width' => $dropdownWidth,
    'columns' => $dropdownColumns,
    'autoThreshold' => (int)$dropdownAutoThreshold
];

$navigation = buildNavigation($navigationArray, 0, $dropdownConfig);
$mobileNavigation = buildMobileNavigation($navigationArray);

// Footer-Settings aus Template Manager laden
$footerColor = TemplateManager::get('tm_footer_color', 'uk-background-default');
$footerMobileLogo = TemplateManager::get('tm_footer_mobile_logo', $logoMedia);
$footerAlternativeLayout = TemplateManager::get('tm_footer_alternative_layout', '0');
$footerImpressumLink = TemplateManager::get('tm_footer_impressum_link', '');
$footerDatenschutzLink = TemplateManager::get('tm_footer_datenschutz_link', '');
$footerKontaktLink = TemplateManager::get('tm_footer_kontakt_link', '');
$footerShowPrivacySettings = TemplateManager::get('tm_footer_show_privacy_settings', '0');
$footerInnerShadow = TemplateManager::get('tm_footer_inner_shadow', '0');
$section1Title = TemplateManager::get('tm_footer_section1_title', 'Unternehmen');
$section1Links = generateFooterSection(TemplateManager::get('tm_footer_section1_links', ''));

$section2Title = TemplateManager::get('tm_footer_section2_title', 'Service');
$section2Links = generateFooterSection(TemplateManager::get('tm_footer_section2_links', ''));

$section3Title = TemplateManager::get('tm_footer_section3_title', 'Kontakt');
$section3Text = TemplateManager::get('tm_footer_section3_text', '');

// Breadcrumb-Funktion mit navigation_array
if (!function_exists('buildBreadcrumb')) {
    function buildBreadcrumb()
    {
        // Nicht auf Startseite anzeigen
        if (rex_article::getCurrentId() == rex_article::getSiteStartArticleId()) {
            return '';
        }

        $navarray = BuildArray::create()->setDepth(10);
        $breadcrumbItems = [];

        $navarray->walk(static function ($item, $level) use (&$breadcrumbItems) {
            if ($item['active']) {
                // Aktueller Artikel wird als aktiv markiert aber nicht verlinkt
                if (rex_article::getCurrentId() == $item['catId']) {
                    $breadcrumbItems[] = '<li class="uk-disabled"><span>' . htmlspecialchars($item['catName']) . '</span></li>';
                } else {
                    $breadcrumbItems[] = '<li><a href="' . $item['url'] . '">' . htmlspecialchars($item['catName']) . '</a></li>';
                }
            }
        });

        if (empty($breadcrumbItems)) {
            return '';
        }

        $output = '<nav aria-label="Breadcrumb" class="uk-margin-medium-bottom">';
        $output .= '<ul class="uk-breadcrumb">';
        $output .= '<li><a href="/" title="Startseite"><span uk-icon="home"></span></a></li>';
        $output .= implode("\n", $breadcrumbItems);
        $output .= '</ul>';
        $output .= '</nav>';

        return $output;
    }
}
?>
<!DOCTYPE html>
<html lang="<?= rex_clang::getCurrent()->getCode() ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <?php
        $seo = new rex_yrewrite_seo();
        echo $seo->getTags();
    ?>
    
    <!-- UIKit Theme Builder - CSS und Fonts -->
    <?php
    // Theme aus DomainContext oder Template Manager
    $themeContext = DomainContext::getContext();
    $themeName = $themeContext['theme'] ?? $uikitTheme;
    echo TemplateHelper::includeAllStyles($themeContext['theme']);
    ?>
    <?php
        echo '<link rel="stylesheet" href="' . rex_url::addonAssets('vidstack', 'vidstack.css') . '">';
        echo '<link rel="stylesheet" href="' . rex_url::addonAssets('vidstack', 'vidstack_helper.css') . '">';
        echo '<link rel="stylesheet" href="' . rex_url::addonAssets('uikit_theme_builder', 'templates.css') . '">';
    ?>
    
    <!-- Accessibility -->
    <?php if ('1' == $sa11yEnabled): ?>
    <?= Sa11y::get()?>
    <?php endif ?>
    
    <style>
        /* Focus visible for better keyboard navigation */
        *:focus-visible {
            outline: 2px solid var(--global-primary-background, #1e87f0);
            outline-offset: 2px;
        }
    </style>
   <link rel="stylesheet" href="<?= rex_url::addonAssets("offeneohren_portal", "vendor/tom-select/tom-select.css") ?>">
</head>
<body>

<!-- Skip to main content link for keyboard users -->
<a href="#main-content" class="uk-position-top-left uk-position-fixed uk-position-z-index uk-transition-slide-top-small" style="transform: translateY(-100%);" onfocus="this.style.transform='translateY(0)'" onblur="this.style.transform='translateY(-100%)'" tabindex="0">
    <span class="uk-button uk-button-primary">Zum Hauptinhalt springen</span>
</a>

<!-- Header mit Navigation -->
<header role="banner">
    <!-- Navbar mit optionalem Sticky -->
    <?php if (!empty($navbarSticky) && '' !== $navbarSticky): ?>
        <?php
        $stickyAttr = 'sel-target: .uk-navbar-container; cls-active: uk-navbar-sticky';
        if ('show-on-up' === $navbarSticky) {
            $stickyAttr = 'show-on-up: true; animation: uk-animation-slide-top; sel-target: .uk-navbar-container; cls-active: uk-navbar-sticky';
        }
        ?>
    <div uk-sticky="<?= htmlspecialchars($stickyAttr) ?>">
    <?php endif ?>
        <nav class="<?= htmlspecialchars($navbarStyle) ?> <?= htmlspecialchars($navbarColor) ?>" aria-label="Hauptnavigation">
            <div class="uk-container uk-container-large <?= htmlspecialchars($navbarContainer) ?>">
                <div uk-navbar>
                    
                <!-- Logo -->
                <div class="uk-navbar-left">
                      <?php if (rex_article::getCurrentId() != rex_article::getSiteStartArticleId()): ?>
                      <a class="uk-navbar-item uk-logo uk-padding-remove-right" href="/" aria-label="Zur Startseite">
                          <!-- Desktop Logo -->
                          <img height="60px" style="height: 60px" src="<?= rex_url::media($logoMedia) ?>" 
                               alt="<?= htmlspecialchars($logoAltText) ?>" 
                               class="uk-responsive-height uk-visible@<?= htmlspecialchars($logoBreakpoint) ?>" />
                          <!-- Mobile Logo -->
                          <?php
                          if ('custom' === $mobileLogoVariant) {
                              $navbarMobileLogo = $mobileLogo;
                          } elseif ('white' === $mobileLogoVariant) {
                              $navbarMobileLogo = $logoWhite;
                          } else {
                              $navbarMobileLogo = $logoMedia;
                          }
                          ?>
                          <img height="60px" style="height: 60px" src="<?= rex_url::media($navbarMobileLogo) ?>" 
                               alt="<?= htmlspecialchars($logoAltText) ?>" 
                               class="uk-responsive-height uk-hidden@<?= htmlspecialchars($logoBreakpoint) ?>" />
                      </a>
                      <?php endif; ?>
                <!-- Navigation (nur Desktop) -->
                <?php
                // Dynamische Positionierung basierend auf Navigation Style
                $navbarPosition = 'uk-navbar-left';
                if ('centered' === $navStyle) {
                    $navbarPosition = 'uk-navbar-center';
                } elseif ('right' === $navStyle) {
                    $navbarPosition = 'uk-navbar-right';
                }
                ?>
                <div class="<?= htmlspecialchars($navbarPosition) ?> uk-visible@<?= htmlspecialchars($navBreakpoint) ?>">
                    <ul class="uk-navbar-nav">
                        <?= $navigation ?>
                    </ul>
                </div>
                
                <!-- Rechte Seite: Search Icon + CTA + Mobile Toggle -->
                <div class="uk-navbar-right">
                    <?php if ('1' == $searchEnabled && !empty($searchArticle)): ?>
                    <a class="uk-navbar-toggle uk-visible@<?= htmlspecialchars($searchBreakpoint) ?>" uk-search-icon="ratio: 1.2" uk-toggle="target: .nav-overlay; animation: uk-animation-fade" href="#"></a>
                    <?php endif ?>
                    
                    <?php if ('1' == $ctaEnabled): ?>
                    <div class="uk-navbar-item uk-visible@<?= htmlspecialchars($logoBreakpoint) ?>">
                        <a href="<?= htmlspecialchars($ctaLink) ?>" class="uk-button <?= htmlspecialchars($ctaButtonStyle) ?> uk-button-small" title="<?= htmlspecialchars($ctaText) ?>">
                            <?php if (!empty($ctaIcon)): ?>
                            <span uk-icon="<?= htmlspecialchars($ctaIcon) ?>" aria-hidden="true"></span>
                            <?php endif ?>
                            <span><?= htmlspecialchars($ctaText) ?></span>
                        </a>
                    </div>
                    <?php endif ?>
                    
                    <!-- Mobile-Toggle (nur unterhalb Breakpoint) -->
                    <a class="uk-navbar-toggle uk-hidden@<?= htmlspecialchars($toggleBreakpoint) ?> uk-margin-right" 
                       uk-toggle="target: #mobile-nav"
                       uk-navbar-toggle-icon
                       aria-label="Mobile Navigation öffnen"
                       aria-expanded="false"
                       aria-controls="mobile-nav">
                    </a>
                </div>
            
                </div>
            </div>
        </nav>
    <?php if (!empty($navbarSticky) && '' !== $navbarSticky): ?>
    </div>
    <?php endif ?>
    
    <!-- Suchfeld Fullwidth Overlay (außerhalb nav für korrektes z-index) -->
    <?php if ('1' == $searchEnabled && !empty($searchArticle)): ?>
    <div class="nav-overlay uk-position-fixed uk-width-1-1 uk-visible@<?= htmlspecialchars($searchBreakpoint) ?> <?= htmlspecialchars($navbarStyle) ?> <?= htmlspecialchars($navbarColor) ?>" hidden style="top: 0; left: 0; z-index: 992;">
        <div class="uk-flex uk-flex-middle uk-flex-center uk-height-1-1">
            <div class="uk-width-1-1 uk-padding uk-padding-remove-vertical">
                <div class="uk-flex uk-flex-middle">
                    <div class="uk-width-expand">
                        <form class="uk-search uk-search-default uk-width-1-1" method="get" action="<?= rex_getUrl($searchArticle) ?>">
                            <input class="uk-search-input uk-text-center" type="search" name="search" placeholder="<?= htmlspecialchars($searchPlaceholder) ?>" aria-label="<?= htmlspecialchars($searchPlaceholder) ?>" autofocus>
                        </form>
                    </div>
                    <a class="uk-navbar-toggle uk-margin-left" uk-close uk-toggle="target: .nav-overlay; animation: uk-animation-fade" href="#"></a>
                </div>
            </div>
        </div>
    </div>
    <?php endif ?>
</header>

<!-- Mobile Navigation Offcanvas -->
<div id="mobile-nav" 
     uk-offcanvas="mode: slide; overlay: true; flip: true;"
     aria-labelledby="mobile-nav-title">
    <div class="uk-offcanvas-bar" role="dialog" aria-modal="true">
        
        <!-- Close Button -->
        <button class="uk-offcanvas-close" 
                type="button" 
                uk-close
                aria-label="Mobile Navigation schließen"></button>
        
        <!-- Mobile Logo -->
        <?php if ('1' == $mobileLogoEnabled): ?>
            <?php
            if ('custom' === $mobileLogoVariant) {
                $offcanvasMobileLogo = $mobileLogo;
            } elseif ('white' === $mobileLogoVariant) {
                $offcanvasMobileLogo = $logoWhite;
            } else {
                $offcanvasMobileLogo = $logoMedia;
            }
            ?>
        <div class="uk-text-center uk-margin-medium-top">
            <img src="<?= rex_url::media($offcanvasMobileLogo) ?>" 
                 alt="<?= htmlspecialchars($logoAltText) ?>" 
                 class="uk-responsive-height"
                 height="50" />
        </div>
        <?php endif ?>
        
        <!-- Mobile Navigation Menu -->
        <nav aria-label="Mobile Navigation">
            <h2 id="mobile-nav-title" class="uk-hidden-visually">Mobile Navigation</h2>
            <ul class="uk-nav-default" data-uk-nav="toggle:>.nav-toggle-btn">
                <?= $mobileNavigation ?>
            </ul>
        </nav>
        
        <!-- Mobile Suchfeld -->
        <?php if ('1' == $searchEnabled && !empty($searchArticle)): ?>
        <hr class="uk-margin-medium">
        <div class="uk-margin-medium">
            <form class="uk-search uk-search-default uk-width-1-1" method="get" action="<?= rex_getUrl($searchArticle) ?>">
                <input class="uk-search-input uk-width-1-1" type="search" name="query" placeholder="<?= htmlspecialchars($searchPlaceholder) ?>" aria-label="<?= htmlspecialchars($searchPlaceholder) ?>">
                <button type="submit" uk-search-icon aria-label="Suche absenden"></button>
            </form>
        </div>
        <?php endif ?>
        
        <!-- Mobile CTA Button mit konfigurierbarem Stil -->
        <?php if ('1' == $ctaEnabled): ?>
        <div class="uk-margin-large-top uk-text-center">
            <a href="<?= htmlspecialchars($ctaLink) ?>" class="uk-button <?= htmlspecialchars($ctaButtonStyle) ?> uk-width-1-1" title="<?= htmlspecialchars($ctaText) ?>">
                <?php if (!empty($ctaIcon)): ?>
                <span uk-icon="<?= htmlspecialchars($ctaIcon) ?>" aria-hidden="true"></span>
                <?php endif ?>
                <?= htmlspecialchars($ctaText) ?>
            </a>
        </div>
        <?php endif ?>
    </div>
</div>

<!-- Main Content -->
<main id="main-content" role="main" tabindex="-1">
    <article class="ck-content">
        <?php 
        if ('1' == $breadcrumbEnabled) {
            $bcHTML = buildBreadcrumb();
            if ($bcHTML !== '') {
                echo '<div class="uk-container uk-container-large uk-margin-top">' . $bcHTML . '</div>';
            }
        }
        ?>
        
        <div class="uk-section uk-margin-remove uk-padding-remove" uk-height-viewport="expand: true">
            <?= $ctype1?>
        </div>
    </article>
</main>

            <footer class="uk-section <?= TemplateManager::get('tm_footer_color', 'uk-background-secondary uk-light') ?> uk-padding-remove-bottom">
        <div class="uk-container uk-container-expand uk-padding">
            <div class="uk-grid-divider uk-child-width-1-1 uk-child-width-1-2@m uk-child-width-1-4@l" uk-grid>
                <!-- Kontakt -->
                <div>
                    <h4 class="uk-h4">Kontakt & Information</h4>
                    <div class="uk-text-small uk-margin-bottom">
                        <?= TemplateManager::get('tm_footer_contact', '<p>HAGE - Hessische Arbeitsgemeinschaft für Gesundheitsförderung e.V.<br>Wildunger Straße 6/6a<br>60487 Frankfurt</p><p>Telefon: +49 69/ 713 76 78-0<br>Fax: +49 69/ 713 76 78-11<br><a href="mailto:info@hage.de">info@hage.de</a></p>') ?>
                    </div>
                    <!-- Links & Impressum -->
                    <ul class="uk-list uk-text-small">
                        <?php
                        $links = TemplateManager::get('tm_footer_links', '');
                        if ($links) {
                            $linkIds = explode(',', $links);
                            foreach ($linkIds as $linkId) {
                                $art = rex_article::get((int)$linkId);
                                if ($art) {
                                    echo '<li><a class="uk-link-text" href="' . rex_getUrl($linkId) . '">' . htmlspecialchars($art->getName()) . '</a></li>';
                                }
                            }
                        } else {
                            echo '<li><a class="uk-link-text" href="#">Impressum / Datenschutz bitte verlinken</a></li>';
                        }
                        
                        if ('1' == TemplateManager::get('tm_footer_show_privacy_settings', '1')): ?>
                            <li><a href="#" class="consent_manager-show-box">Datenschutzeinstellungen</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <!-- HAGE -->
                <div>
                    <?php if ($logoHage = TemplateManager::get('tm_footer_hage_logo', 'brand-hage.svg')): ?>
                    <div class="uk-margin-bottom">
                        <img src="<?= rex_url::media($logoHage) ?>" alt="HAGE Logo" style="width: 180px; height: 45px; display: block; object-fit: contain; object-position: left;">
                    </div>
                    <?php endif; ?>
                    <p class="uk-text-small">
                        <?= nl2br(rex_escape(TemplateManager::get('tm_footer_hage_text', 'Diese Homepage ist ein Projekt der Hessischen Arbeitsgemeinschaft für Gesundheitsförderung e.V. (HAGE). Die HAGE ist eine landesweit arbeitende Vereinigung und verbindet Akteure und Aktivitäten aus allen Bereichen und Arbeitsfeldern, die sich mit Gesundheitsförderung und Prävention beschäftigen.'))) ?>
                    </p>
                </div>
                
                <!-- HMSI -->
                <div>
                    <?php if ($logoHmsi = TemplateManager::get('tm_footer_hmsi_logo', 'brand-hessen-subline.png')): ?>
                    <div class="uk-margin-bottom">
                        <img src="<?= rex_url::media($logoHmsi) ?>" alt="Hessisches Ministerium für Soziales und Integration" style="max-height: 55px; width: auto; display: block;">
                    </div>
                    <?php endif; ?>
                    <p class="uk-text-small">
                        <?= nl2br(rex_escape(TemplateManager::get('tm_footer_hmsi_text', 'Das Hessische Ministerium für Soziales und Integration unterstützt das Projekt und bedankt sich im Namen der Landesregierung bei allen Akteuren für ihr Engagement und die Einstellung ihrer Angebote.'))) ?>
                    </p>
                </div>
                
                <!-- Agenturen/Unterstützer -->
                <div>
                    <h4 class="uk-h4">Umsetzung der Webseite</h4>
                    <p class="uk-text-small">
                        <?= nl2br(rex_escape(TemplateManager::get('tm_footer_text', 'Die HAGE und das Hessische Ministerium für Soziales und Integration danken der Fa. KLXM Crossmedia GmbH für die Unterstützung bei der Erstellung der aktuellen Homepage. Dank geht auch an die Firma YAKAMARA- Media für die initiale Umsetzung.'))) ?>
                    </p>
                    <div class="uk-flex uk-flex-middle uk-margin-top" style="gap: 30px;">
                        <?php if ($logo1 = TemplateManager::get('tm_footer_logo_1', 'brand_klxm.svg')): ?>
                        <a href="<?= rex_escape(TemplateManager::get('tm_footer_logo_1_url', 'https://www.klxm.de/')) ?>" target="_blank" rel="noopener">
                            <img src="<?= rex_url::media($logo1) ?>" alt="Sponsor 1" style="max-height: 28px; width: 130px; height: 28px; display: block; object-fit: contain;">
                        </a>
                        <?php endif; ?>
                        
                        <?php if ($logo2 = TemplateManager::get('tm_footer_logo_2', 'brand-yakamara.svg')): ?>
                        <a href="<?= rex_escape(TemplateManager::get('tm_footer_logo_2_url', 'https://www.yakamara.de/')) ?>" target="_blank" rel="noopener">
                            <img src="<?= rex_url::media($logo2) ?>" alt="Sponsor 2" style="max-height: 42px; width: 105px; height: 42px; display: block; object-fit: contain;">
                        </a>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
            
            <div class="uk-text-center uk-margin-large-top uk-padding-small uk-text-small uk-text-muted" style="border-top: 1px solid rgba(128,128,128,0.2);">
                &copy; <?= date('Y') ?> | <?= rex_escape(TemplateManager::get('tm_firma', 'HAGE – Hessische Arbeitsgemeinschaft für Gesundheitsförderung e.V.')) ?>
            </div>
        </div>
    </footer>

<!-- UIKit Theme Builder - JavaScript -->
<?= TemplateHelper::includeAllJS() ?>
<?php
// JavaScript einbinden
echo '<script src="' . rex_url::addonAssets('vidstack', 'vidstack.js') . '"></script>';
echo '<script src="' . rex_url::addonAssets('vidstack', 'vidstack_helper.js') . '"></script>';
?>                     
<script src="<?= rex_url::addonAssets("offeneohren_portal", "vendor/tom-select/tom-select.complete.min.js") ?>"></script>
<script src="<?= rex_url::addonAssets("offeneohren_portal", "vendor/tom-select/tom-select.complete.min.js") ?>"></script>
</body>
</html>

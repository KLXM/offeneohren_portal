<?php
$headline = trim('REX_VALUE[1]');
$subline = trim('REX_VALUE[2]');
$logoMedia = rex_url::media('logo.svg');
$ohrMedia = rex_url::media('ohr.svg');

if (rex::isBackend()) {
    echo '<p><strong>Hero Section</strong></p>';
    echo '<p>' . htmlspecialchars($headline) . '</p>';
} else {
?>

<div class="oo-hero-wrapper">
    <div class="uk-container uk-container-large oo-hero-inner" uk-parallax="opacity: 1,0.8; y: 0,-30; target: !.oo-hero-wrapper; easing: 1">
        <div class="oo-hero-grid uk-grid-margin uk-grid" uk-grid>
            <!-- Text Spalte -->
            <div class="uk-width-1-1 uk-width-3-5@m uk-flex uk-flex-column uk-flex-center ui-mobile-center">
                <!-- Logo (link oben in der Spalte) -->
                <div class="oo-hero-logo-box oo-anim-bounce">
                    <img src="<?= $logoMedia ?>" alt="Offene Ohren Logo" class="oo-hero-logo">
                </div>

                <div class="oo-hero-text">
                    <h1 class="oo-hero-title"><?= htmlspecialchars($headline) ?></h1>
                    <?php if ($subline !== ''): ?>
                        <p class="oo-hero-subtitle"><?= nl2br(htmlspecialchars($subline)) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Ohr Grafik Spalte -->
            <div class="uk-width-1-1 uk-width-2-5@m uk-flex uk-flex-middle uk-flex-center uk-visible@m">
                <img src="<?= $ohrMedia ?>" alt="Ohr Illustration" class="oo-hero-graphic" uk-parallax="scale: 1,0.95; opacity: 1,0.8; y: 0,-15; target: !.oo-hero-wrapper; easing: 1">
            </div>
        </div>
    </div>
</div>

<style>
/* === HERO CSS === */
.oo-hero-wrapper {
    position: relative;
    width: 100vw;
    left: 50%;
    right: 50%;
    margin-left: -50vw;
    margin-right: -50vw;
    background-color: #1b103e; /* Passend anpassen */
    color: #ffffff;
    padding: 30px 0;
    overflow: hidden; /* Parallax Overflow verstecken */
}

.oo-hero-inner {
    padding-top: 10px;
    padding-bottom: 20px;
}

.oo-hero-title {
    color: #ffffff;
    font-size: 2.2rem;
    font-weight: bold;
    margin-bottom: 10px;
}

@media (min-width: 960px) {
    .oo-hero-title {
        font-size: 3.5rem;
    }
}

.oo-hero-subtitle {
    font-size: 1.1rem;
    opacity: 0.85;
}

.oo-hero-logo-box {
    margin-bottom: 30px;
}

.oo-hero-logo {
    width: 140px;
    height: auto;
}

@media (min-width: 960px) {
    .oo-hero-logo {
        width: 180px;
    }
}

.oo-hero-graphic {
    max-width: 100%;
    width: 250px;
    transform-origin: center center;
}

@media (min-width: 960px) {
    .oo-hero-graphic {
        width: 360px;
    }
}

/* === Animation Pop-In === */
@media (max-width: 959px) {
    .ui-mobile-center {
        text-align: center;
        align-items: center;
    }
    .oo-hero-logo-box {
        margin-left: auto;
        margin-right: auto;
    }
}

@keyframes oo_bounce_in {
    0% { transform: scale(0.5); opacity: 0; }
    60% { transform: scale(1.08); opacity: 1; }
    100% { transform: scale(1); opacity: 1; }
}

.oo-anim-bounce {
    animation: oo_bounce_in 0.8s cubic-bezier(0.18, 0.89, 0.32, 1.28) forwards;
}

/* Reduced Motion (Barrierefreiheit) */
@media (prefers-reduced-motion: reduce) {
    .oo-anim-bounce {
        animation: none !important;
        transform: none !important;
        opacity: 1 !important;
    }
}
</style>

<?php } ?>
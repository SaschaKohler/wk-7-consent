# WK7 Consent Manager

**Version:** 1.1.0  
**Entwickelt von:** [Sascha Kohler](https://sascha-kohler.at) – Kongruenz-Mentor & Web-Entwickler  
**Kompatibel mit:** WordPress 6.0+, Divi 5  
**Requires:** PHP 7.4+  
**Lizenz:** GPL v2 or later

## Beschreibung

GDPR-konformer Cookie-Banner mit Google Consent Mode v2 Integration für GTM/GA4. Erkennt automatisch Theme-Farben für nahtlose Design-Integration.

Professionell entwickelt für maximale GDPR-Compliance und beste User Experience.

## Features

- 🎨 **Auto Theme Detection** - Automatically adapts to active theme colors
- 🍪 **GDPR Compliant** - Full cookie consent management
- 📊 **Google Consent Mode v2** - Modern analytics integration
- 🎯 **GTM & GA4 Support** - Seamless tracking integration
- 🎭 **4 Banner Templates** - Multiple design options
- 🔘 **Floating Action Button** - Always accessible settings
- 🌐 **Fully Customizable** - All texts and colors adjustable
- ⚡ **Performance Optimized** - Minimal footprint (~5KB total)
- 📱 **Mobile Responsive** - Works on all devices

## Installation

1. Upload the plugin folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure settings under Settings → Wine K7 Consent

## Quick Start

### 1. Basic Setup

```
Settings → Wine K7 Consent

Google Tag Manager ID: GTM-XXXXXXX
OR
GA4 Measurement ID: G-XXXXXXXXXX

Privacy Policy URL: /datenschutz
Imprint URL: /impressum
```

### 2. Enable Theme Colors

```
✓ Theme-Farben automatisch erkennen
```

### 3. Choose Template

```
Banner-Template: Template 1 (klassisch)
```

### 4. Save & Test

Visit your website and verify the consent banner appears.

## Configuration

### Theme Color Detection

The plugin automatically detects colors from:

- Wine K7 Theme (primary: #ff6b35, text: #ededed, bg: #0a0a0a)
- Theme Customizer settings
- Custom theme filters

### Manual Color Override

Disable auto-detection and set custom colors:

```php
// In functions.php
add_filter( 'wine_k7_consent_theme_colors', function( $colors ) {
    return array(
        'primary' => '#your-color',
        'text' => '#your-color',
        'background' => '#your-color',
    );
});
```

### Constants

Define in `wp-config.php`:

```php
define( 'WINE_K7_GTM_ID', 'GTM-XXXXXXX' );
define( 'WINE_K7_GA4_ID', 'G-XXXXXXXXXX' );
```

## Usage

### Shortcode

Add a consent preferences button anywhere:

```
[wine_k7_consent_preferences label="Cookie Settings"]
```

### JavaScript API

```javascript
// Open preferences dialog
window.WineK7Consent.openPreferences();
```

### Custom Button

```html
<button onclick="window.WineK7Consent.openPreferences()">
  Cookie Settings
</button>
```

## Files Structure

```
wine-k7-consent/
├── wine-k7-consent.php      # Main plugin file
├── README.md                 # This file
├── assets/
│   ├── consent.css          # Frontend styles
│   ├── consent.js           # Frontend logic
│   └── admin.js             # Admin preview
└── includes/
    ├── admin-settings.php   # Settings page
    ├── admin-fields.php     # Field renderers
    └── admin-sanitize.php   # Input sanitization
```

## Hooks & Filters

### Filters

```php
// Override GTM ID
add_filter( 'wine_k7_consent_gtm_id', function( $id ) {
    return 'GTM-CUSTOM';
});

// Override GA4 ID
add_filter( 'wine_k7_consent_ga4_id', function( $id ) {
    return 'G-CUSTOM';
});

// Override theme colors
add_filter( 'wine_k7_consent_theme_colors', function( $colors ) {
    return array(
        'primary' => '#custom',
        'text' => '#custom',
        'background' => '#custom',
    );
});

// Override FAB visibility
add_filter( 'wine_k7_consent_show_fab', '__return_false' );

// Override FAB label
add_filter( 'wine_k7_consent_fab_label', function( $label ) {
    return 'Custom Label';
});
```

## Google Consent Mode v2

### Consent Categories Mapping

| Cookie Category | Consent Mode Keys                                  |
| --------------- | -------------------------------------------------- |
| Necessary       | `functionality_storage`, `security_storage`        |
| Analytics       | `analytics_storage`                                |
| Marketing       | `ad_storage`, `ad_user_data`, `ad_personalization` |
| Functional      | `functionality_storage`                            |

### Default State (Denied)

```javascript
gtag("consent", "default", {
  ad_storage: "denied",
  analytics_storage: "denied",
  ad_user_data: "denied",
  ad_personalization: "denied",
  functionality_storage: "granted",
  security_storage: "granted",
  wait_for_update: 500,
});
```

### Update on Consent

```javascript
gtag("consent", "update", {
  analytics_storage: "granted", // if analytics accepted
  ad_storage: "granted", // if marketing accepted
  // ...
});
```

## Customization

### CSS Variables

```css
:root {
  --wine-k7-primary: #ff6b35;
  --wine-k7-text: #ededed;
  --wine-k7-bg: #0a0a0a;
}
```

### Custom Styling

```css
/* Override banner styles */
.wine-k7-consent-container {
  max-width: 900px;
  border-radius: 20px;
}

/* Override button styles */
.wine-k7-btn-primary {
  background: linear-gradient(135deg, #ff6b35, #ff8c61);
}
```

## Debugging

### Enable Debug Mode

```javascript
localStorage.setItem("wine_k7_consent_debug", "true");
```

### Check Consent Status

```javascript
const consent = JSON.parse(localStorage.getItem("wine_k7_consent"));
console.log(consent);
```

### Reset Consent

```javascript
localStorage.removeItem("wine_k7_consent");
location.reload();
```

## Performance

- **CSS**: ~2KB gzipped
- **JS**: ~3KB gzipped
- **Total**: ~5KB gzipped
- **Lazy Loaded**: Main scripts load in footer
- **Cache Friendly**: Compatible with all major caching plugins

## Browser Support

- Chrome/Edge (latest 2 versions)
- Firefox (latest 2 versions)
- Safari (latest 2 versions)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Requirements

- WordPress 5.0+
- PHP 7.4+
- Modern browser with JavaScript enabled

## Changelog

### 1.1.0 (2025-11-08)

- Plugin auf WK7 Consent Manager umbenannt
- Vollständiges Sascha Kohler Branding
- Deutsche Texte und Dokumentation
- Verbesserte Konstanten-Struktur
- Optimierte Code-Dokumentation

### 1.0.0 (2024)

- Initial release
- Theme color auto-detection
- Google Consent Mode v2 support
- 4 banner templates
- Floating Action Button
- GTM & GA4 integration
- Live admin preview
- Fully customizable texts

## Support & Kontakt

**Entwickelt von Sascha Kohler**  
Kongruenz-Mentor & Web-Entwickler

- 📧 **Email:** <support@sascha-kohler.at>
- 🌐 **Website:** <https://vision.sascha-kohler.at>

## WordPress / Divi Umgebung

Der parallel gepflegte WordPress/Divi-Teil des Projekts läuft lokal in einer DDEV-Umgebung. Nutze dazu die gewohnten DDEV-Kommandos (z. B. `ddev start`, `ddev wp ...`), um die Instanz zu starten und Administrationsaufgaben auszuführen.

### Git Workflow

Commits und Pushes erfolgen bewusst nur sporadisch, sobald abgeschlossene Arbeitspakete vorliegen. Plane lokale Änderungen entsprechend und prüfe den Stand vor einem Deployment.

### Über den Entwickler

Sascha Kohler verbindet Mentaltraining mit Web-Entwicklung und begleitet Gründer:innen von der inneren Klarheit bis zur digitalen Sichtbarkeit. Als Ironman-Finisher und diplomierter Lebensberater entwickelt er maßgeschneiderte digitale Lösungen mit Fokus auf Authentizität und Kongruenz.

### Services

- WordPress & Divi 5 Entwicklung
- GDPR-konforme Cookie-Lösungen
- Plugin-Entwicklung
- Digitale Strategieberatung
- Mentoring für Gründer:innen

## Lizenz

GPL v2 or later - <https://www.gnu.org/licenses/gpl-2.0.html>

Copyright (C) 2025 Sascha Kohler

Dieses Plugin ist freie Software und kann unter den Bedingungen der GNU General Public License weiterverbreitet und/oder modifiziert werden.

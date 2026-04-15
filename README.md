# Offene Ohren Portal

Das **Offene Ohren Portal** ist ein zentrales AddOn für eine REDAXO 5 Instanz, das speziell zur Verwaltung und Darstellung von Beratungsstellen, Notdiensten und ähnlichen Angeboten entwickelt wurde.

Durch die Nutzung moderner Technologien wie **YForm & YORM** sowie ein vollständig integriertes Ausgabesystem ersetzt dieses AddOn bisherige externe Rendering-Lösungen (wie *yakme*) durch ein stabiles, performantes REDAXO-natives Konstrukt.

## Architektur & Technik

### 1. Datenverwaltung über YForm (YORM)
Die gesamte Datenspeicherung läuft über `yform`. Das AddOn initialisiert bei der Installation eigene YORM-Modelle (`src/lib/*.php`), um Anfragen objektorientiert durchzuführen. 

Wichtige Tabellen:
- `rex_yf_service`: Die Haupttabelle der Einrichtungen und Dienste.
- `rex_yf_district`: Zuständigkeitsbereiche (z.B. Hessenweit, Regionale Kreise).
- `rex_yf_group` & `rex_yf_language`: Themen- und Sprachzuordnungen (verknüpft über Relationstabellen).
- `rex_yf_alternate`: Alternative Portale.

*Achtung:* Direkte SQL-Queries via `rex_sql` auf diese Tabellen sollen zugunsten von YORM (`rex_offeneohren_portal_service::create()`, `::query()`) weitestgehend vermieden werden.

### 2. Rendering über dedizierte Module
Das AddOn liefert bei der Installation alle notwendigen REDAXO-Module gleich mit. Ein eingebauter Setup-Scraper scannt den Ordner `install/modules/` und installiert oder aktualisiert die Module im System (siehe Navigation unter *Setup*).
- **Suchergebnisse / Listen:** Ein flexibel schaltbares Modul (Kacheln vs. Akkordeon-Listen), inkl. Live-AJAX Suche.
- **Formulare:** Formular für Neuanmeldungen oder Änderungswünsche (mit Anti-Spam-Maßnahmen).
- **PDF Export:** Eigenständige, saubere HTML-Struktur zur Kompatibilität mit dem AddOn *PdfOut* (DOMPDF getrieben).

### 3. Redaktioneller Workflow & Sicherheit
Alle Einträge, die Besucher über die Formulare auf der Website vorschlagen oder ändern wollen, landen **niemals sofort live** auf der Seite.
Sie werden mit dem Status `In Prüfung (2)` in der Datenbank abgelegt und müssen über die AddOn-Seite **Moderation** von Redakteuren explizit kontrolliert und live geschaltet werden (Status `Online (1)`).

### 4. Link-Checker
Eine weitere Besonderheit des AddOns ist der integrierte **Link-Checker**. Dieser prüft alle URLs (Websites & Chat-Links) der Online-Dienste live per AJAX-Requests auf Erreichbarkeit (Status 200). Redakteure können so "tote" Datensätze identifizieren und unkompliziert bereinigen.

---

## Entwicklung

- **UI & Frontend:** Wir verzichten auf serverseitige CSS-Pakete und orientieren uns an **UIkit**, das fest in den Modul-Ausgaben (`output.php`) integriert ist.
- **Code Quality:** Das AddOn unterliegt den aktuellen Standards (PSR-12, Typisierung via PHP 8).
- **JavaScript & Assets:** Skripte, die beispielsweise für den Link-Checker (`check.php`) relevant sind, liegen direkt in der entsprechenden PHP-Datei oder in den Modulen (AJAX-Filter-Script im Filter-Modul).
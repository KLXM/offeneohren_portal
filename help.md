# Redakteurs-Hilfe: Offene Ohren Portal

Willkommen im AddOn „Offene Ohren Portal“. Mit diesem AddOn verwalten Sie das Kernstück der Plattform: Beratungsstellen, Notdienste, Kontaktgruppen und weitere Hilfsangebote.

## Generelle Verwaltung der Angebote

Die eigentliche Eingabe der Angebote in die Datenbank nehmen Sie im Hauptmenü unter **YForm > Tabellen-Übersicht > Einrichtung (Services)** vor.

In einem Eintrag finden Sie Felder für wichtige Kenndaten wie Namen, Telefonnummern, Kontakt-URLs, sowie Zuordnungen zu Landkreisen und Sprachen.

**Wichtig - Der Veröffentlichungs-Status:**
- `Offline (0)`: Die Einrichtung ist systemintern deaktiviert und auf der Website unsichtbar.
- `Online (1)`: Die Einrichtung ist im Live-Betrieb! Sie taucht in Listen, Suchergebnissen und auf Detailseiten umgehend auf.
- `In Prüfung (2)`: Ein neuer Vorschlag oder eine eingereichte Änderung durch Website-Besucher. **Dieser Eintrag ist noch auf der Seite unsichtbar und erfordert ihre redaktionelle Freigabe.**

---

## Das Menü „Moderation“

Ihre Besucher und externe Beratungsstellen können über das integrierte Portal-Formular neue Dienstleistungen eintragen oder Änderungen an bestehenden Angeboten vorschlagen. Aus Sicherheitsgründen und um Fehlinformationen zu vermeiden, werden diese Eintragungen **niemals sofort live gestellt**!

Im Menü **„Moderation“** landen alle Neuanfragen (Status `2` – "In Prüfung") sowie gemeldete Probleme („Problemmeldungen“).
Hier können Sie auf einen Blick erkennen, wer was vorgeschlagen hat. Prüfen Sie diese Angaben kritisch, bearbeiten Sie sie bei Bedarf im YForm-Editor und schalten Sie sie bei Korrektheit auf den Status `Online (1)`.  

---

## Das Menü „Link-Checker“

Anlaufstellen ändern mit der Zeit häufig ihre Websites oder Chat-Systeme. Veraltete oder tote Internet-Adressen verschlechtern die Qualität dieses Portals drastisch.

Der **Link-Checker** ist ein Werkzeug für Sie, um bequem alle aktuell eingetragenen Links (Website und Chat-URL) im System maschinell abfragen zu lassen:
1. Klicken Sie auf **"Link-Check starten / fortsetzen"**.
2. Das System arbeitet alle Datensätze hintereinander ab und meldet sich mit einem grünen Flag (`OK`), wenn die Website erreichbar ist.
3. Meldet der Check **„Fehler“** (rotes Flag) oder einen **„Timeout“**, sollten Sie den entsprechenden Eintrag kontrollieren. Möglicherweise existiert die Seite nicht mehr.
4. *Sonderfall Cloudflare / Bot-Schutz (gelbes Flag):* Manche modern gesicherten Server blockieren automatisierte Checks wie diesen hier. In solchen Fällen ist die Website oft noch da. Sie sollten den betroffenen Link manuell anklicken und prüfen. Ist er in Ordnung, können Sie diesen über den grünen Haken-Knopf in der Tabelle ("Manuell als OK markieren") bestätigen.

Sie können tote Einträge direkt in der Check-Tabelle über den roten Papierkorb unwiderruflich von der Website entfernen.

---

## Das Menü „Benachrichtigungen“

Hier finden Sie Systemeinstellungen, Vorlagen und Logs für ausgehende Benachrichtigungen (E-Mails), wenn neue Angebote eintreffen oder Änderungen stattfinden. So bleiben Sie als Redaktionsteam stets auf dem Laufenden.

---

## Wichtig beim Verwenden der Portal-Module (Artikel-Pflege)

Auf den Struktur-Seiten von REDAXO fügen Sie die Module in Seiten ein (z. B. auf der Start- oder Übersichtsseite). 
- Die Module wie „OO | Suchergebnis“ reagieren automatisch auf vom Benutzer gesetzte Filter aus dem Modul „OO | Filter-Formular“.
- Achten Sie darauf, in den Modul-Einstellungen stets die korrekten **Ziel-Artikel** auszuwählen. Ein Detail-Modul muss z. B. wissen, auf welche Seite es für „Änderungsvorschläge“ oder für einen Neuantrag verlinken soll.
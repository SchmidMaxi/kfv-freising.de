# TYPO3 Database Sync Tool

Ein wiederverwendbares Tool zum Synchronisieren von TYPO3-Datenbanken von Remote-Servern (Staging/Production) in deine lokale DDEV-Installation.

## Features

- ✅ Synchronisierung von Staging- und Production-Datenbanken
- ✅ Sichere Konfiguration über `.env.db-sync` Datei
- ✅ Automatische Dump-Erstellung auf Remote-Server
- ✅ Komprimierung mit gzip für schnellere Übertragung
- ✅ Automatischer Import in lokale DDEV-Datenbank
- ✅ TYPO3-spezifische Post-Import-Tasks (Schema-Update, Cache-Flush)
- ✅ Wiederverwendbar für mehrere Projekte
- ✅ Farbige Konsolen-Ausgabe für bessere Übersicht

## Installation

### 1. Konfigurationsdatei erstellen

Kopiere die Beispiel-Konfiguration und passe sie an:

```bash
cp .env.db-sync.example .env.db-sync
```

### 2. Zugangsdaten eintragen

Öffne `.env.db-sync` und trage deine Zugangsdaten ein:

```bash
# STAGING Environment
STAGING_SSH_HOST=staging.deine-domain.de
STAGING_SSH_USER=dein-username
STAGING_SSH_PORT=22
STAGING_DB_NAME=deine_staging_db
STAGING_DB_USER=db_user
STAGING_DB_PASSWORD=db_passwort
STAGING_DB_HOST=localhost

# PRODUCTION Environment
PRODUCTION_SSH_HOST=production.deine-domain.de
PRODUCTION_SSH_USER=dein-username
PRODUCTION_SSH_PORT=22
PRODUCTION_DB_NAME=deine_production_db
PRODUCTION_DB_USER=db_user
PRODUCTION_DB_PASSWORD=db_passwort
PRODUCTION_DB_HOST=localhost
```

### 3. SSH-Key Setup (empfohlen)

Für eine passwortlose Verbindung richte SSH-Keys ein:

```bash
# SSH-Key generieren (falls noch nicht vorhanden)
ssh-keygen -t ed25519 -C "dein@email.de"

# Public Key auf Server kopieren
ssh-copy-id -p 22 username@staging.deine-domain.de
ssh-copy-id -p 22 username@production.deine-domain.de
```

DDEV kann auch mit dem SSH-Agent arbeiten:

```bash
ddev auth ssh
```

## Verwendung

### Von Staging synchronisieren

```bash
ddev sync --staging
```

### Von Production synchronisieren

```bash
ddev sync --production
```

### Hilfe anzeigen

```bash
ddev sync --help
```

## Was passiert beim Sync?

1. **Dump erstellen**: Ein MySQL-Dump wird auf dem Remote-Server erstellt
2. **Download**: Der Dump wird komprimiert heruntergeladen
3. **Cleanup**: Der Remote-Dump wird gelöscht
4. **Import**: Der Dump wird in die lokale DDEV-Datenbank importiert
5. **Post-Tasks**: TYPO3-spezifische Aufgaben werden ausgeführt:
   - Database Schema Update
   - Development Admin User erstellen
   - Cache Flush

## Dump-Verwaltung

Alle heruntergeladenen Dumps werden im `.dumps/` Verzeichnis gespeichert:

```
.dumps/
  ├── db_staging_20260114_143022.sql.gz
  ├── db_production_20260114_150533.sql.gz
  └── ...
```

Du kannst alte Dumps manuell löschen, um Speicherplatz zu sparen:

```bash
rm -rf .dumps/db_staging_*.sql.gz
```

## Wiederverwendung für andere Projekte

Dieses Tool ist so konzipiert, dass es in jedem TYPO3-Projekt verwendet werden kann:

1. Kopiere das DDEV-Command:
   ```bash
   cp .ddev/commands/host/sync /pfad/zu/anderem/projekt/.ddev/commands/host/
   ```

2. Kopiere die Beispiel-Konfiguration:
   ```bash
   cp .env.db-sync.example /pfad/zu/anderem/projekt/
   ```

3. Erstelle die Konfiguration:
   ```bash
   cd /pfad/zu/anderem/projekt
   cp .env.db-sync.example .env.db-sync
   # Zugangsdaten anpassen
   ```

4. Füge zur `.gitignore` hinzu:
   ```
   .env.db-sync
   .dumps/
   ```

## Konfigurationsoptionen

### Lokale Einstellungen

In `.env.db-sync` kannst du auch lokale Einstellungen anpassen:

```bash
# Lokales Verzeichnis für Dumps (Standard: .dumps)
LOCAL_DUMP_DIR=.dumps

# Komprimierung (gzip oder none)
DUMP_COMPRESSION=gzip
```

### Remote Dump-Pfad

Standardmäßig werden Dumps in `/tmp` auf dem Remote-Server erstellt. Du kannst dies ändern:

```bash
STAGING_DUMP_PATH=/var/www/tmp
PRODUCTION_DUMP_PATH=/home/username/dumps
```

## Sicherheit

⚠️ **Wichtig**: Die `.env.db-sync` Datei enthält sensible Zugangsdaten!

- ✅ Die Datei ist bereits in `.gitignore` eingetragen
- ✅ Committe niemals die `.env.db-sync` Datei
- ✅ Verwende SSH-Keys statt Passwörtern wo möglich
- ✅ Beschränke SSH-User auf notwendige Berechtigungen

## Fehlerbehebung

### "Configuration file not found"

```bash
# Erstelle die Konfigurationsdatei
cp .env.db-sync.example .env.db-sync
```

### "Permission denied (publickey)"

```bash
# SSH-Key auf Server kopieren
ddev auth ssh
ssh-copy-id -p 22 username@host
```

### "Access denied for user"

Prüfe die Datenbank-Zugangsdaten in `.env.db-sync`

### Import schlägt fehl

```bash
# Prüfe ob DDEV läuft
ddev describe

# Starte DDEV falls nötig
ddev start
```

## Erweiterte Verwendung

### Post-Import-Tasks anpassen

Du kannst das Script `.ddev/commands/host/sync` bearbeiten und im Abschnitt "TYPO3-specific post-import tasks" eigene Befehle hinzufügen, z.B.:

```bash
# Eigene Extension-Commands
ddev typo3 my_extension:setup

# Zusätzliche Datenbank-Änderungen
echo 'UPDATE be_users SET password="" WHERE username="admin";' | ddev typo3 database:import
```

## Technische Details

- **Dump-Methode**: `mysqldump` mit `--single-transaction` für konsistente Backups
- **Transfer**: `scp` über SSH
- **Komprimierung**: gzip (optional)
- **Import**: `ddev import-db` Command

## Support

Bei Problemen oder Fragen:

1. Prüfe die Fehlermeldung
2. Kontrolliere die Konfiguration in `.env.db-sync`
3. Teste die SSH-Verbindung manuell: `ssh username@host`
4. Teste die Datenbankverbindung auf dem Server

## Lizenz

Dieses Tool ist frei verwendbar und kann nach Belieben angepasst werden.

# ERP Systém

Ekonomický systém pro české podniky vyvíjený po krocích. První etapa: **základ a CRM** –
evidence firem a kontaktních osob s ověřením údajů přes ARES a kontrolou plátce DPH,
webový instalátor a zálohování systému.

## Instalace na hosting (jako Joomla)

1. Stáhněte si nejnovější **instalační ZIP** ze záložky *Releases*.
2. Rozbalte a nahrajte obsah přes FTP do kořene domény (nebo do podadresáře).
3. Nastavte adresář `storage/` a `bootstrap/cache/` jako zapisovatelné (práva 775).
4. Otevřete doménu v prohlížeči – spustí se instalační průvodce:
   - kontrola prostředí,
   - připojení k databázi (MySQL/MariaDB na hostingu, SQLite pro testy),
   - vytvoření administrátorského účtu.
5. Po dokončení se přihlaste a systém je připraven.

Pokud hosting neumí nastavit document root na `public/`, kořenový `.htaccess`
automaticky přesměruje požadavky tam.

## Zálohy

V administraci v sekci **Zálohy** lze kdykoli vytvořit kompletní zálohu
(dump databáze + `.env` + data), stáhnout ji jako ZIP a uložit mimo server.

## Vývoj

```sh
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
php artisan serve
```

## Roadmapa

- [x] Základ a CRM: firmy, kontaktní osoby, ARES, kontrola DPH
- [x] Webový instalátor
- [x] Zálohovací modul
- [ ] Prodej a dokumenty: nabídky, objednávky, faktury, číselníky, číselné řady, PDF, QR platby
- [ ] Účetnictví: osnova, deník, salda, banka a pokladna, DPH přiznání
- [ ] Sklad a nákup
- [ ] Personalistika a mzdy
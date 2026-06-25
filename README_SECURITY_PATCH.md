# Patch di sicurezza - riepilogo fix e modifiche

Questo documento riassume i fix introdotti con la patch di hardening del template Joomla (`commit 8a0f6d5`).

## Obiettivo

Ridurre la superficie di attacco del template, con focus su:
- sanitizzazione input/output dinamici;
- riduzione rischio XSS negli override;
- gestione più sicura di URL esterni;
- include file più robusti e non dipendenti da path ricavati da `$_SERVER`.

## File modificati

- `JoomlaTheme\index.php`
- `JoomlaTheme\html\com_content\article\default.php`
- `JoomlaTheme\html\com_content\category\blog-firstlevel_item.php`
- `JoomlaTheme\html\com_content\category\blog_item.php`
- `JoomlaTheme\html\com_content\featured\default_item.php`
- `JoomlaTheme\html\com_content\featured\default_leading_item.php`
- `JoomlaTheme\html\com_users\login\default_login.php`

## Dettaglio interventi

### 1) Include più sicuri negli override content
Negli override `com_content` è stato sostituito l'uso di include costruiti da `$_SERVER` con:

- `require_once JPATH_THEMES . '/' . $template . '/functions.php';`

Beneficio:
- elimina dipendenze da host/path runtime potenzialmente manipolabili;
- aumenta portabilità e affidabilità dei path.

### 2) Escaping output dinamico (mitigazione XSS)
Sono stati aggiunti escaping espliciti per contenuti dinamici stampati a pagina, ad esempio:

- summary custom field;
- titoli articolo/menu;
- heading generati dinamicamente.

Metodi usati:
- `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`
- `$this->escape(...)` dove coerente con override Joomla.

### 3) Hardening in `index.php` su URL esterni e classi CSS
In `index.php` sono state introdotte funzioni di sanitizzazione:

- `sanitizeExternalUrl()`:
  - accetta URL relativi interni (`/...`) oppure URL assoluti validi;
  - blocca URL non validi;
  - consente solo schemi `http/https`;
  - applica escaping finale HTML.
- `sanitizeCssClasses()`:
  - rimuove caratteri non ammessi dalle classi CSS dinamiche;
  - normalizza gli spazi.

Applicazioni principali:
- link `parentcorporationUrl`;
- link social (`facebook`, `instagram`, `twitter`, `youtube`, `telegram`, `whatsapp`, `linkedin`, `newsletter`);
- classe icona FontAwesome (`siteFaIconClass`).

### 4) Link esterni social più sicuri
Per i link social con `target="_blank"` è stato aggiunto:

- `rel="noopener noreferrer"`

Beneficio:
- riduce rischi di reverse tabnabbing e isolamento migliore della tab esterna.

### 5) Rafforzamento attributi dinamici nel login (`com_users`)
Nel template di login sono stati sanificati gli attributi dinamici dei pulsanti:

- `class`, `title`, `id`, `onclick`, `icon`;
- chiavi/valori `data-*`.

Beneficio:
- riduzione rischio di injection via attributi HTML/JS dinamici.

## Impatto funzionale

- Nessuna modifica al comportamento applicativo atteso.
- Modifiche orientate a sicurezza e robustezza dell'output/rendering.

## Nota

Questa patch è focalizzata sull'hardening lato template/override e non sostituisce aggiornamenti core Joomla o policy server (WAF, CSP, header HTTP di sicurezza, ecc.).

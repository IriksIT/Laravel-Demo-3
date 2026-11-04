# QUA-8 — Refinement huisstijl (neon roze / paars / Comic Sans)

Status: **voorbereid, nog niet afgerond.** Dit document is de vastlegging die QUA-8 vraagt.
De onderdelen die een klantbesluit of een billingcode nodig hebben staan expliciet als OPEN gemarkeerd.

Repo: `IriksIT/Laravel-Demo-3` — default branch `production` (er is geen `main`).
Alle drie de uitvoerende tickets branchen van `production` en richten hun PR op `production`.

---

## 0. Bevindingen in de code (grond voor de schattingen)

Deze zijn geverifieerd in de repo, niet aangenomen. Ze veranderen de opzet van de tickets.

**0.1 De publieke pagina gebruikt de Vite-bundle helemaal niet.**
`resources/views/welcome.blade.php` is 24 regels en heeft een inline `<style>`-blok met
hardgecodeerde kleuren (`background:#0f172a`, `color:#e2e8f0`, `font-family: system-ui`).
Er staat **geen `@vite(...)`-directive** in de pagina. `resources/css/app.css` en
`resources/js/app.js` worden dus nergens geladen.

Gevolg: een font-stack die via `vite.config.js` → `bunny()` wordt geladen **bereikt de publieke
pagina niet** zoals de repo er nu staat. Het eerste uitvoerende ticket moet de pagina eerst aan de
bundle hangen (`@vite(['resources/css/app.css'])`) en de inline stijlen vervangen door tokens in
`resources/css/app.css`. Dit is extra werk dat in de oorspronkelijke ticketomschrijving niet zat.

**0.2 Het Orchid-adminpanel heeft een eigen, gescheiden styling-ingang.**
Orchid `14.53.0`, gemount op `/admin` (`config/platform.php`, `PLATFORM_PREFIX`).
Het panel laadt `public/vendor/orchid/css/orchid.css` (468 KB, Bootstrap 5) en **niet**
`resources/css/app.css`. In `config/platform.php` staan beide ingangen nu leeg:

```php
'resource' => [ 'stylesheets' => [], 'scripts' => [] ],
'vite'     => [],
```

Goed nieuws voor de schatting: Orchid 14 zet zijn kleuren als Bootstrap-custom-properties op
`:root,[data-bs-theme=light]`. Een override-stylesheet volstaat; er hoeft niets geforkt te worden.
Relevante tokens en hun huidige waarden:

| token | huidig |
|---|---|
| `--bs-body-bg` | `#edeef0` |
| `--bs-body-color` | `#212529` |
| `--bs-body-font-family` | `var(--bs-font-sans-serif)` |
| `--bs-primary` | `#007bff` |
| `--bs-link-color` | `#171b1d` |
| `--bs-emphasis-color` | `#15141a` |

**0.3 Orchid heeft een donkere variant.** `[data-bs-theme=dark]` komt in `orchid.css` voor.
Een rebrand die alleen `[data-bs-theme=light]` overschrijft, laat de donkere modus ongemoeid en
dus ongebrand. Scopevraag voor de klant — zie §4.

**0.4 `resources/css/app.css` is wél al token-gestuurd.** Tailwind v4 met een `@theme`-blok dat nu
`--font-sans: 'Instrument Sans', …` zet. Dat is de juiste plek voor de nieuwe font-stack en de
kleurtokens van de publieke kant.

---

## 1. Kleurwaarden — VOORSTEL, klantbevestiging OPEN

"Neon roze" en "paars" zijn geen tokens. Hieronder een volledig uitgewerkt voorstel, zodat het
gesprek met de klant over hexwaarden gaat en niet over bijvoeglijke naamwoorden.

Alle verhoudingen hieronder zijn berekend met de WCAG 2.1 relatieve-luminantieformule.

### Voorgestelde achtergrond

`--brand-pink: #FF2D95` — verzadigd neon roze.
Overwogen alternatieven: `#FF00FF` (zuiver magenta, harder), `#FF1493` (deeppink, iets doffer).
De keuze van de achtergrond bepaalt alle andere waarden; dit is de eerste vraag aan de klant.

### Palet bij optie (b) — haalt AA

| rol | token | hex | contrast op `#FF2D95` | eis | oordeel |
|---|---|---|---|---|---|
| achtergrond | `--brand-pink` | `#FF2D95` | — | — | — |
| bodytekst | `--brand-ink` | `#280952` | **4.84 : 1** | 4.5 | haalt |
| koppen (≥24px) | `--brand-purple` | `#4C1D95` | **3.16 : 1** | 3.0 (grote tekst) | haalt |
| knop achtergrond | `--brand-btn-bg` | `#280952` | **4.84 : 1** | 3.0 (UI-component) | haalt |
| knoplabel | `--brand-btn-fg` | `#FFFFFF` | 16.78 : 1 *(op de knop)* | 4.5 | haalt |
| links | `--brand-link` | `#280952` | **4.84 : 1** | 4.5 | haalt |
| accent / focusring | `--brand-accent` | `#FFFFFF` | **3.46 : 1** | 3.0 (UI) | haalt |

Twee kanttekeningen bij dit palet:

- **Koppen in `#4C1D95` mogen alleen op ≥24px, of ≥18.66px vet.** Zakt een kop daaronder, dan moet
  hij `--brand-ink` gebruiken. Zet dit als opmerking in het ticket, anders sluipt het er alsnog in.
- **Links hebben dezelfde kleur als bodytekst.** Dat is bewust: elke paarstint die *licht genoeg* is
  om van de bodytekst te verschillen, zakt door de 4.5:1 op neon roze. Links moeten daarom
  **onderstreept** zijn. Zonder onderstreping schendt dit WCAG 1.4.1 (kleur als enige onderscheid).

### Palet bij optie (a) — merkintentie, haalt AA niet

Als de klant de letterlijke wens aanhoudt (neon roze achtergrond, "gewoon paarse" tekst), dan is dit
wat we meten:

| tekstkleur | contrast op `#FF2D95` | eis bodytekst | oordeel |
|---|---|---|---|
| `#800080` (purple) | 2.72 : 1 | 4.5 | **zakt** |
| `#6B21A8` | 2.52 : 1 | 4.5 | **zakt** |
| `#7C3AED` (violet) | 1.64 : 1 | 4.5 | **zakt** |
| `#4C1D95` | 3.16 : 1 | 4.5 | **zakt** (haalt wél de 3.0 voor grote tekst) |

De schatting in de oorspronkelijke ticketomschrijving — "ongeveer 2:1 à 3:1" — klopt.

### OPEN

- [ ] Hexwaarde neon roze achtergrond bevestigd door klant
- [ ] Hexwaarde paarse bodytekst bevestigd door klant
- [ ] Knoppen, links en accenten bevestigd door klant

---

## 2. Comic Sans — licentie en beschikbaarheid: BESLOTEN

Dit onderdeel heeft geen klantbesluit nodig. De conclusie staat vast en twee van de drie
uitvoerende tickets hangen eraan.

**Comic Sans MS wordt niet meegeleverd en niet self-hosted.** Het is een propriëtair Microsoft-font
dat met Windows en Office meekomt. De licentie staat herdistributie als webfont niet toe. Het
ontbreekt bovendien standaard op Linux en Android, dus een flink deel van het bezoek zou hoe dan ook
de fallback zien.

**Vastgelegde font-stack:**

```css
--font-sans: 'Comic Sans MS', 'Comic Neue', cursive;
```

Bezoekers die Comic Sans MS lokaal hebben, zien het — dat is toegestaan, want we leveren niets mee.
Alle anderen krijgen Comic Neue.

**Comic Neue is geverifieerd beschikbaar** (niet aangenomen — opgevraagd bij de bron):

- Licentie **SIL Open Font License 1.1** — herdistributie en self-hosting toegestaan.
- Aanwezig op Bunny Fonts, de provider die `vite.config.js` al gebruikt.
  `https://fonts.bunny.net/css?family=comic-neue:400,700` → HTTP 200, levert woff2 + woff.
- Beschikbare gewichten 300 / 400 / 700, normaal en cursief, latin-subset.

**Wijziging in `app/vite.config.js`** — het bestaande `bunny()`-blok vervangen:

```js
fonts: [
    bunny('Comic Neue', { weights: [400, 700] }),
],
```

`'Instrument Sans'` vervalt daarmee, tenzij de klant hem als secundair font wil aanhouden.

Let op de staartjes: Comic Neue heeft een kleinere x-hoogte en smallere cijfers dan Instrument Sans.
Tabellen in het adminpanel en knoppen met vaste breedte kunnen anders uitvallen. Dat is
opgenomen in de schatting van het admin-ticket, niet als apart ticket.

---

## 3. Contrast — klantbesluit OPEN

Twee opties, zoals de ticketomschrijving voorschrijft. Dit is een vastlegging, geen blokkade: de
klant mág voor (a) kiezen.

**(a) De klant accepteert de afwijking bewust.**
We voeren de letterlijke merkwens uit en documenteren dat de publieke pagina en/of het adminpanel
niet aan WCAG 2.1 AA voldoen. Zie de meetwaarden in §1. Praktisch risico: Comic Neue op kleine
formaten in een adminpanel met dichte tabellen is de slechtst denkbare combinatie voor leesbaarheid,
en medewerkers kijken er de hele dag naar.
Als de klant onder de Europese toegankelijkheidsrichtlijn valt, of aan de overheid levert, is dit
geen vrije keuze — dat is een vraag die voorafgaat aan (a) of (b).

**(b) Neon roze blijft, de paarstint gaat donkerder.**
Achtergrond blijft `#FF2D95`, bodytekst wordt `#280952` (4.84:1). Het palet in §1 is compleet en
doorgemeten. Visueel blijft het onmiskenbaar neon roze en paars; alleen de tekst is donkerder dan
de klant zich misschien voorstelt.

Geen van beide opties kost meer bouwwerk dan de ander — het verschil zit alleen in de waarden.
De keuze heeft dus geen invloed op de schattingen hieronder.

### OPEN

- [ ] Besluit (a) of (b) genoteerd, met naam van wie het namens de klant bevestigd heeft
- [ ] Bij (a): afwijking vastgelegd en de vraag over wettelijke verplichting beantwoord

---

## 4. Scope adminpanel — klantbesluit OPEN

De rebranding raakt twee oppervlakken:

1. de publieke pagina (`/`, één Blade-view);
2. het Orchid-adminpanel op `/admin` — intern, alleen voor ingelogde medewerkers.

Te bevestigen bij de klant:

- [ ] Moet `/admin` mee in de rebranding? Zo nee, dan **vervalt het derde uitvoerende ticket.**
- [ ] Zo ja: geldt het ook voor Orchid's donkere modus (`[data-bs-theme=dark]`, §0.3), of alleen licht?

Aanbeveling om aan de klant voor te leggen: het panel is intern en wordt de hele werkdag gebruikt.
Als de klant voor optie (a) kiest, is het verdedigbaar om de rebranding tot de publieke kant te
beperken en het panel leesbaar te laten.

---

## 5. Opdeling in uitvoerende tickets

Drie tickets, geen van alle groter dan één werkdag, dus splitsen is niet nodig.
De uitvoerende tickets bestaan al en staan geblokkeerd op QUA-8.

### T1 — Publieke pagina: tokens, font-stack en Vite-koppeling

- **Schatting:** 0,5 dag
- **Prioriteit:** high — T2 en T3 leunen op de tokens die hier landen
- **Discipline:** front-end → **Timo's agent**
- **Repo/branch:** `IriksIT/Laravel-Demo-3`, branch vanaf `production`, PR naar `production`
- **Blokkeert op:** QUA-8 (kleurbesluit §1, contrastbesluit §3)

Acceptatiecriteria:

- [ ] `resources/css/app.css` bevat de bevestigde kleurtokens in het bestaande `@theme`-blok
- [ ] `@theme` zet `--font-sans: 'Comic Sans MS', 'Comic Neue', cursive`
- [ ] `resources/views/welcome.blade.php` laadt de bundle via `@vite(['resources/css/app.css'])`
- [ ] Het inline `<style>`-blok in `welcome.blade.php` is weg; alle kleuren komen uit tokens
- [ ] Achtergrond, bodytekst, koppen, knop (achtergrond + label), links en accenten gebruiken de tokens
- [ ] Links zijn onderstreept (WCAG 1.4.1 — zie §1)
- [ ] `npm run build` slaagt
- [ ] Bij besluit (b): een screenshot of meting toont bodytekst ≥ 4.5:1 en koppen ≥ 3:1
- [ ] Bij besluit (a): de afwijking staat in `docs/huisstijl-QUA-8.md` vastgelegd

### T2 — Comic Neue als webfont via Bunny

- **Schatting:** 0,25 dag
- **Prioriteit:** high — zonder dit ticket ziet ruim de helft van het bezoek `cursive`
- **Discipline:** tooling / build → **Matthijs's agent**
- **Erbij halen:** Timo's agent, omdat T1 dezelfde `@theme` aanraakt — stem de volgorde af of voeg
  T1 en T2 samen in één PR
- **Repo/branch:** `IriksIT/Laravel-Demo-3`, branch vanaf `production`, PR naar `production`
- **Blokkeert op:** QUA-8 (§2, al besloten — dit ticket kan direct na refinement door)

Acceptatiecriteria:

- [ ] `app/vite.config.js` laadt `bunny('Comic Neue', { weights: [400, 700] })`
- [ ] `'Instrument Sans'` is verwijderd, of de klant heeft bevestigd dat hij secundair blijft
- [ ] `npm run build` slaagt en de gebouwde CSS bevat `@font-face` voor Comic Neue
- [ ] Comic Sans MS wordt **niet** meegeleverd of self-hosted — alleen als eerste naam in de stack
- [ ] De licentieconclusie uit §2 staat in de PR-omschrijving

### T3 — Orchid-adminpanel rebranden

- **Schatting:** 0,5 dag (1 dag als de donkere modus meemoet — dan splitsen)
- **Prioriteit:** medium — intern oppervlak, geen klantzichtbaar risico
- **Discipline:** front-end → **Timo's agent**
- **Erbij halen:** Matthijs's agent voor `config/platform.php`
- **Repo/branch:** `IriksIT/Laravel-Demo-3`, branch vanaf `production`, PR naar `production`
- **Blokkeert op:** QUA-8 (§4 — **dit ticket vervalt als de klant `/admin` buiten scope zet**)

Acceptatiecriteria:

- [ ] Een override-stylesheet overschrijft minimaal `--bs-body-bg`, `--bs-body-color`,
      `--bs-body-font-family`, `--bs-primary` en `--bs-link-color`
- [ ] De stylesheet is gekoppeld via `config/platform.php` → `'resource' => ['stylesheets' => [...]]`
- [ ] `public/vendor/orchid/css/orchid.css` is **niet** aangepast — die wordt bij elke publish overschreven
- [ ] Tabellen, badges, formuliervelden en focusringen zijn visueel gecontroleerd op `/admin`
- [ ] Bij besluit (b): bodytekst in tabellen haalt 4.5:1, knoppen en randen 3:1
- [ ] Besloten en vastgelegd of `[data-bs-theme=dark]` meegaat
- [ ] Inloggen op `/admin` en één CRUD-scherm doorlopen zonder visuele regressie

---

## 6. Wat QUA-8 nog tegenhoudt

| # | Open punt | Eigenaar |
|---|---|---|
| 1 | Hexwaarden bevestigen (§1) | Jori — klantcontact |
| 2 | Contrastbesluit (a) of (b) + wie het bevestigde (§3) | Jori — klantcontact |
| 3 | Scope `/admin` wel/niet (§4) | Jori — klantcontact |
| 4 | **Billingcode van de klant** — staat nergens in QUA-7/QUA-8 en is niet uit de repo af te leiden | Jori |

Punt 2 uit §2 (Comic Sans-licentie) is afgerond en vraagt geen klantbesluit.
Punt 5 uit de eerdere versie (ontbrekende ticket-ID's) is opgelost — zie §7.
De sub-vraag over de donkere modus is eveneens gesloten — zie §8.

---

## 7. Ticket-ID's — aangeleverd via de standup van 4 november

De standup noemt de drie geblokkeerde tickets: **QUA-9**, **QUA-10** en **QUA-11**.
QUA-9 en QUA-10 hangen direct op QUA-8, QUA-11 daarachter.
Klant is **TEPNL**.

**Let op bij toepassen:** ik kan de titels van QUA-9/10/11 niet lezen — deze runtime heeft
geen Paperclip-API (apart geregistreerd op **QUA-14**). Ik weet dus wél dat dit de drie
tickets zijn, maar **niet** welk ID bij welk werkitem hoort. Wie dit toepast moet T1/T2/T3
uit §5 op titel matchen, niet op volgorde. Gok niet op nummervolgorde:

- **T1** — publieke pagina: tokens, font-stack en Vite-koppeling (0,5 dag, high, Timo's agent)
- **T2** — Comic Neue als webfont via Bunny (0,25 dag, high, Matthijs's agent)
- **T3** — Orchid-adminpanel rebranden (0,5 dag, medium, Timo's agent)

Dat QUA-11 "daarachter" hangt past bij T3: het adminpanel is het enige ticket dat kan
vervallen, afhankelijk van de scope-beslissing in §4. Dat is een aanwijzing, geen bevestiging.

---

## 8. Donkere modus — gesloten, geen klantvraag meer

De standup stelt dat deze sub-vraag nu al te beantwoorden is. Dat klopt, en het antwoord is
beter dan "voorleggen aan de klant".

Geverifieerd in `public/vendor/orchid/css/orchid.css`:

- `prefers-color-scheme` komt **0 keer** voor. De donkere modus volgt dus **niet** de
  OS-voorkeur en gaat nooit vanzelf aan.
- `[data-bs-theme=dark]` definieert wél een volledig tokenblok
  (`--bs-body-bg:#212529`, `--bs-body-color:#dee2e6`, `--bs-emphasis-color:#fff`, …).
  Zodra iets dat attribuut zet, krijgt de gebruiker de **ongebrande** donkere variant.

Of Orchid 14.53 een gebruikersschakelaar heeft die dat attribuut zet, kon ik hier niet
nagaan: `vendor/` is in deze workspace niet geïnstalleerd, dus de Blade-templates van Orchid
zijn er niet. Dat is precies waarom "weten we het zeker?" de verkeerde vraag is.

**Besluit (implementatiekeuze, geen merkbeslissing — valt binnen de refinement-gate):**
de override-stylesheet van T3 zet de merktokens op **beide** selectors:

```css
:root,
[data-bs-theme=light],
[data-bs-theme=dark] { /* merktokens */ }
```

Daarmee is het irrelevant of de schakelaar bestaat: geen enkele gebruiker kan op een
ongebrand panel uitkomen. De kosten zijn nul — het is dezelfde declaratie met een extra
selector. Het gevolg is dat de donkere modus in het panel feitelijk verdwijnt; wil TEPNL
later een echte donkere variant van de huisstijl, dan is dat een nieuw ticket, geen
uitbreiding van T3.

De AC van T3 in §5 vervangt daarmee "Besloten en vastgelegd of `[data-bs-theme=dark]`
meegaat" door:

- [ ] De merktokens staan op `:root`, `[data-bs-theme=light]` **en** `[data-bs-theme=dark]`
- [ ] Het panel is in beide thema's bekeken en is in geen van beide ongebrand

Dit haalt géén van de vier klantvragen weg — het haalt een sub-vraag weg die anders bij de
klant was beland.

# Dokumentace projektu – CineDB

Webová aplikace pro správu a prohlížení filmové databáze (filmy, žánry, osoby).
Postavena na frameworku **CodeIgniter 4** s autentizací **Ion Auth** a stylováním
pomocí **Tailwind CSS**.

**Autoři:** Ostap Hlushko a Matvii Kopylov.
**Full-stack senior advisor:** Claude (Anthropic) – konzultace architektury,
generování částí kódu a dokumentace (viz kap. 3 – Citace AI).

---

## 1. Rozdělení práce

Práce **nebyla dělána celá společně** – níže je rozdělení podle modulů. Každý člen
zodpovídá za uvedené části (návrh, implementaci i odladění).

| Oblast | Soubory | Odpovídá |
|--------|---------|----------|
| Databázové schéma + seedery (TMDB import) | `app/Database/Migrations/*`, `app/Database/Seeds/*` | **Ostap Hlushko** |
| Modely a databázové dotazy (JOIN, agregace) | `app/Models/Movie.php`, `People.php`, `Genres.php` | **Ostap Hlushko** |
| Veřejná část – výpis filmů, detail filmu, detail osoby, statistiky | `Home`, `Stats`, `app/Views/home/*`, `app/Views/stats/*` | **Matvii Kopylov** |
| Administrace (CRUD, vyhledávání, správa obsazení) | `Admin`, `app/Views/admin/*` | **Matvii Kopylov** |
| Autentizace (přihlášení, registrace, zapomenuté heslo) | `Auth`, `IdentityResolver`, `app/Views/auth/*` | **Ostap Hlushko** |
| Frontend infrastruktura (Tailwind build, helpery, šablony, JS) | `app/Helpers/form_ext_helper.php`, `assets/js/app.js`, `layouts/main.php`, `partials/*` | **Matvii Kopylov** |
| Konfigurace, lokalizace hlášek, filtry | `app/Config/Messages.php`, `Validation.php`, `Filters.php`, `app/Filters/AdminFilter.php` | **Ostap Hlushko** |
| Dokumentace | `docs/*` | **Claude (Anthropic)** |

---

## 2. Použité externí nástroje (knihovny a frameworky)

Pro každou knihovnu je uveden **název, verze, autor, licence a odkaz**.

### 2.1 Produkční závislosti

| Knihovna | Verze | Autor | Licence | Odkaz |
|----------|-------|-------|---------|-------|
| PHP | ^8.2 | The PHP Group | PHP License v3.01 | https://www.php.net |
| CodeIgniter 4 (framework) | v4.7.2 | CodeIgniter Foundation | MIT | https://codeigniter.com |
| CodeIgniter Ion Auth | 4.x-dev | Ben Edmunds, Benoît Vrignaud | MIT | https://github.com/benedmunds/CodeIgniter-Ion-Auth |
| Tailwind CSS | 3.4.19 | Tailwind Labs Inc. (Adam Wathan a kol.) | MIT | https://tailwindcss.com |
| @tailwindcss/forms | 0.5.11 | Tailwind Labs Inc. | MIT | https://github.com/tailwindlabs/tailwindcss-forms |
| @tailwindcss/line-clamp | 0.4.4 | Tailwind Labs Inc. | MIT | https://github.com/tailwindlabs/tailwindcss-line-clamp |
| Inter (webfont) | 4.x | Rasmus Andersson | SIL Open Font License 1.1 | https://rsms.me/inter/ |

### 2.2 Vývojové (dev) závislosti

| Knihovna | Verze | Autor | Licence | Odkaz |
|----------|-------|-------|---------|-------|
| FakerPHP/Faker | 1.24.x-dev | FakerPHP (François Zaninotto a kol.) | MIT | https://github.com/FakerPHP/Faker |
| mikey179/vfsStream | v1.x-dev | Frank Kleine | BSD-3-Clause | https://github.com/bovigo/vfsStream |
| PHPUnit | 10.5.x-dev | Sebastian Bergmann | BSD-3-Clause | https://phpunit.de |
| Node.js + npm | (build) | OpenJS Foundation | MIT | https://nodejs.org |


### 2.3 Externí data

| Zdroj dat | Účel | Licence / podmínky | Odkaz |
|-----------|------|--------------------|-------|
| The Movie Database (TMDB) API | Naplnění databáze filmy, žánry a osobami (seeder) | TMDB Terms of Use (nekomerční) | https://www.themoviedb.org |

---

## 3. Citace umělé inteligence (AI)

Při tvorbě aplikace byl využit AI asistent **Claude (Anthropic)** v roli
**full-stack senior advisora** – konkrétně k návrhu architektury, generování částí
kódu (kontrolery, šablony, helper, dokumentace) a k ladění chyb.

V souladu se zadáním je konverzace doložena **screenshotem chatu v příloze**:

* **Přílohy** – `docs/prilohy/` 


---

## 4. Seznam zdrojů (dokumentace, fóra, Stack Overflow)

Při řešení byly použity primárně oficiální dokumentace:

1. CodeIgniter 4 User Guide – https://codeigniter4.github.io/userguide/
2. Ion Auth dokumentace – https://github.com/benedmunds/CodeIgniter-Ion-Auth/blob/3/USERGUIDE.md
3. Tailwind CSS dokumentace – https://tailwindcss.com/docs
4. PHP manuál (DateTime, filter_var) – https://www.php.net/manual/


---

## 5. Popis metod v kontrolerech

Popisováno je *co metoda obecně dělá*, nikoli jednotlivé řádky.

### 5.1 `App\Controllers\BaseController`
Společný předek všech kontrolerů. V property `$helpers` načítá pro celou aplikaci
helpery `form`, `url` a vlastní `form_ext`, takže jsou dostupné ve všech šablonách.

### 5.2 `App\Controllers\Home` (veřejná část)
| Metoda | Co dělá |
|--------|---------|
| `index()` | Vypíše stránkovaný seznam filmů (20 / stránka) seřazený podle popularity; podporuje vyhledávání podle názvu (`?q=`). Předává data a pager do šablony `home/index`. |
| `show(int $id)` | Detail jednoho filmu. Načte film, jeho žánry a osoby (JOIN), rozdělí osoby na herce a režiséry a zobrazí šablonu `home/show`. Pokud film neexistuje, vyhodí 404. |
| `genres()` | Výpis všech žánrů s počtem filmů u každého (agregace). |
| `genre(int $id)` | Stránkovaný výpis filmů jednoho žánru (JOIN), jako hlavní výpis filmů. 404 při neexistenci. |
| `people()` | Stránkovaný a prohledávatelný výpis všech osob. |
| `person(int $id)` | Detail osoby (herec/režisér). Zobrazí profil z DB a filmografii (všechny filmy, na kterých je osoba uvedena). Při neexistenci vyhodí 404. |

### 5.3 `App\Controllers\Auth` (autentizace)
| Metoda | Co dělá |
|--------|---------|
| `__construct()` | Inicializuje knihovnu Ion Auth a konfiguraci hlášek (`Config\Messages`). |
| `login()` | GET zobrazí přihlašovací formulář, POST ověří přihlášení. Umožňuje přihlášení **e-mailem i uživatelským jménem** (přes `IdentityResolver`). Při úspěchu / chybě nastaví flash hlášku. |
| `logout()` | Odhlásí uživatele a přesměruje na úvod. |
| `register()` | GET zobrazí registrační formulář, POST validuje vstup (skupina pravidel `register`) a založí účet. Reálné uživatelské jméno doplní ihned po registraci. |
| `forgot_password()` | GET formulář pro zadání identity, POST odešle odkaz pro obnovu hesla. Odpovídá vždy stejně (neprozrazuje existenci účtu). |
| `reset_password(string $code)` | Ověří platnost kódu z odkazu, POST nastaví nové heslo (skupina pravidel `reset_password`). |

### 5.4 `App\Controllers\Admin` (administrace, chráněno filtrem `admin`)
| Metoda | Co dělá |
|--------|---------|
| `__construct()` | Načte konfiguraci hlášek. |
| `index()` | Rozcestník administrace + počty záznamů (agregace `COUNT`). |
| `add()` | Zobrazí formulář pro přidání s přepínačem typu (film / žánr / osoba). |
| `store()` | Zvaliduje a uloží nový záznam podle zvoleného typu; nastaví flash hlášku. |
| `list_entries()` | Stránkovaný výpis záznamů daného typu s **vyhledáváním** podle názvu; udržuje `q` a `entity` ve stránkování. |
| `edit(string $entity, int $id)` | Formulář pro úpravu záznamu. U filmu navíc načte připojené osoby a seznam všech osob (správa obsazení). |
| `update(string $entity, int $id)` | Uloží změny existujícího záznamu. |
| `delete(string $entity, int $id)` | Smaže záznam a vrátí flash hlášku. |
| `attach_person(int $movie_id)` | Přiřadí osobu k filmu v dané roli (případně změní roli). |
| `detach_person(int $movie_id, int $person_id)` | Odebere osobu z filmu. |
| `users()` | Stránkovaný a prohledávatelný (username/email) seznam uživatelských účtů; označí administrátory. |
| `edit_user(int $id)` | Formulář pro úpravu uživatele (profil, aktivace, role admin, nové heslo). |
| `update_user(int $id)` | Zvaliduje a uloží změny uživatele přes Ion Auth; synchronizuje členství ve skupině admin a volitelně mění heslo. |
| `delete_user(int $id)` | Smaže uživatele (s pojistkou – nelze smazat sám sebe). |

Privátní pomocné metody (drží kontroler malý a bez duplicit):
`resolve_entity()` (validace typu entity), `model_for()` (vrátí model podle typu),
`movie_roles()` (číselník rolí), `not_found_msg()/updated_msg()/deleted_msg()` (výběr
hlášky podle typu), `movie_rules()/genre_rules()/person_rules()/user_rules()` (sady
validačních pravidel), `movie_payload()/person_payload()` (sestavení dat k uložení),
`admin_group_id()` (vyhledá id skupiny administrátorů).

### 5.5 `App\Controllers\Stats`
| Metoda | Co dělá |
|--------|---------|
| `index()` | Stránka statistik. Zobrazuje souhrnné agregace katalogu a rozpad podle žánrů (JOIN + GROUP BY) – viz `Movie::genre_stats()` a `Movie::catalogue_totals()`. |

---

## 6. Vlastní vytvořené knihovny, helpery a modely

### 6.1 Knihovna `App\Libraries\IdentityResolver`
Převádí přihlašovací vstup (e-mail **nebo** uživatelské jméno) na identitu, kterou
očekává Ion Auth.

| Metoda | Vstup | Výstup | Popis |
|--------|-------|--------|-------|
| `to_identity(string $login)` | `$login` – e-mail nebo username | `string\|null` – hodnota identity, nebo `null` když účet neexistuje | Je-li vstup platný e-mail, vrátí jej beze změny; jinak jej považuje za username a v tabulce uživatelů dohledá odpovídající identitu. |

### 6.2 Helper `app/Helpers/form_ext_helper.php`
Sada funkcí, které vykreslují formulářové prvky naformátované pro Tailwind (label +
ovládací prvek + inline chybová hláška). Odstraňuje opakování dlouhých class řetězců
ve šablonách.

| Funkce | Vstup | Výstup | Popis |
|--------|-------|--------|-------|
| `field_text($name,$label,$opts)` | název, popisek, volby (`value,type,placeholder,required,help,attrs,wrapper,id`) | HTML | Textové (a obdobné) vstupní pole jako celá skupina. |
| `field_password($name,$label,$opts)` | název, popisek, volby | HTML | Pole pro heslo s tlačítkem pro **zobrazení/skrytí** (váže se na `app.js`). |
| `field_textarea($name,$label,$opts)` | název, popisek, volby (`rows,…`) | HTML | Víceřádkové textové pole. |
| `field_select($name,$label,$options,$opts)` | název, popisek, mapa `hodnota=>popisek`, volby (`selected,multiple,required,…`) | HTML | Rozbalovací seznam (i vícenásobný). |
| `field_checkbox($name,$label,$opts)` | název, popisek, volby (`value,checked,…`) | HTML | Jeden zaškrtávací prvek s popiskem. |
| `field_submit($text,$opts)` | text tlačítka, volby (`name,class`) | HTML | Odesílací tlačítko. |
| `field_errors($name)` | název pole | HTML / `''` | Vykreslí chybovou hlášku validace pro dané pole. |

Interní pomocné funkce: `field_shell()` (obal label + prvek + chyba),
`field_input_classes()` (sdílené třídy, červený rámeček při chybě),
`field_has_error()` (zjištění chyby pole).

### 6.3 Modely a jejich vlastní metody
Modely jsou generovány přes `php spark make:model` (návratový typ **object**,
pojmenované podle tabulek). Vlastní (nad rámec CRUD) metody:

**`App\Models\Movie`**
| Metoda | Vstup | Výstup | Popis |
|--------|-------|--------|-------|
| `get_genres(int $movie_id)` | ID filmu | `array<object>` | Žánry filmu (JOIN `movie_genres`+`genres`). |
| `get_people(int $movie_id)` | ID filmu | `array<object>` | Osoby filmu + role (JOIN `movie_people`+`people`). |
| `attach_person(int $movie_id,int $person_id,string $role)` | ID filmu, ID osoby, role | `void` | Přiřadí/přeřadí osobu k filmu (smaž-a-vlož kvůli složenému PK). |
| `detach_person(int $movie_id,int $person_id)` | ID filmu, ID osoby | `void` | Odebere vazbu osoby na film. |
| `for_genre(int $genre_id)` | ID žánru | `self` | Omezí dotaz na filmy daného žánru (JOIN) pro řetězení `paginate()`. |
| `genre_stats()` | – | `array<object>` | Statistika po žánrech: `COUNT/AVG/SUM` + `GROUP BY` (JOIN). |
| `catalogue_totals()` | – | `object` | Souhrnné agregace celého katalogu. |

**`App\Models\People`**
| Metoda | Vstup | Výstup | Popis |
|--------|-------|--------|-------|
| `get_movies(int $person_id)` | ID osoby | `array<object>` | Filmografie osoby + role (JOIN `movie_people`+`movie`). |

**`App\Models\Genres`**
| Metoda | Vstup | Výstup | Popis |
|--------|-------|--------|-------|
| `with_movie_counts()` | – | `array<object>` | Žánry + počet filmů (LEFT JOIN + `COUNT` + `GROUP BY`). |

**`App\Models\Users`** (read-only nad tabulkou Ion Auth `users`; mutace jdou přes knihovnu Ion Auth)
| Metoda | Vstup | Výstup | Popis |
|--------|-------|--------|-------|
| `is_admin(int $user_id, string $admin_name='admin')` | ID uživatele, název admin skupiny | `bool` | Zjistí, zda je uživatel ve skupině administrátorů (JOIN `users_groups`+`groups`). |

### 6.4 Filtr `App\Filters\AdminFilter`
| Metoda | Popis |
|--------|-------|
| `before()` | Před vstupem na `admin/*` ověří přihlášení a roli administrátora; jinak přesměruje s chybovou hláškou. |
| `after()` | Nepoužito. |

### 6.5 JavaScript `assets/js/app.js`
Bez frameworku, načítán s `defer`. Funkce: přepínání zobrazení hesla
(`[data-toggle-password]`), přidávání/odebírání řádků v dávkových formulářích,
hromadný výběr v tabulce a inicializace Select2 (je-li přítomen).

---

## 7. Popis konfiguračních proměnných

### 7.1 `Config\Messages` (vlastní)
Centrální úložiště všech textů hlášek (flash zprávy). Každá proměnná je řetězec;
proměnné s `%s` používají `sprintf()` pro doplnění názvu.

| Proměnná | K čemu slouží | Možné hodnoty |
|----------|---------------|---------------|
| `loginSuccess`, `loginFailed`, `logoutSuccess` | Hlášky přihlášení/odhlášení | text |
| `loginRequired`, `adminRequired` | Hlášky při odepření přístupu | text |
| `registerSuccess`, `registerFailed` | Hlášky registrace | text |
| `resetSent`, `resetSuccess`, `resetInvalidCode` | Hlášky obnovy hesla | text |
| `movieAdded/Updated/Deleted`, `movieNotFound` | Hlášky CRUD filmů (`%s` = název) | text se `%s` |
| `genreAdded/Updated/Deleted`, `genreNotFound`, `genreIdTaken` | Hlášky CRUD žánrů | text |
| `personAdded/Updated/Deleted`, `personNotFound` | Hlášky CRUD osob | text |
| `personAttached`, `personDetached`, `personAttachInvalid` | Hlášky správy obsazení (`%1$s`,`%2$s`) | text se zástupnými symboly |
| `userUpdated`, `userDeleted`, `userNotFound`, `userSelfDelete`, `userUpdateFailed` | Hlášky správy uživatelů (`%s` = jméno) | text |
| `unknownEntity`, `validationFailed`, `unexpectedError` | Obecné/záložní hlášky | text |

### 7.2 `Config\IonAuth` (naše přepsání)
Přepisuje výchozí chování Ion Auth, aby při vývoji nedocházelo k uzamčení účtu.

| Proměnná | Význam | Možné hodnoty |
|----------|--------|---------------|
| `trackLoginAttempts` | Zda počítat neúspěšné pokusy o přihlášení | `true` / `false` (u nás `false` = bez uzamčení) |
| `maximumLoginAttempts` | Max. počet neúspěšných pokusů před uzamčením | celé číslo (`0` = neomezeně) |
| `lockoutTime` | Délka uzamčení účtu v sekundách | celé číslo v sekundách (`0` = bez uzamčení) |

Klíčové převzaté proměnné Ion Auth, na kterých aplikace stojí:
`identity` (sloupec identity, výchozí `email`), `defaultGroup` (výchozí skupina
nově registrovaných, `members`), `tables` (názvy tabulek Ion Auth).

### 7.3 `Config\Validation` (vlastní skupiny pravidel)
| Proměnná | Význam | Hodnoty |
|----------|--------|---------|
| `$register` | Pravidla registrace (unikátní username/email, heslo min. 8 znaků, shoda potvrzení) | pole pravidel |
| `$reset_password` | Pravidla obnovy hesla (heslo min. 8 znaků, shoda potvrzení) | pole pravidel |

### 7.4 `Config\Pager` (rozšíření)
| Proměnná | Význam | Hodnoty |
|----------|--------|---------|
| `$templates['movies_pager']` | Vlastní Tailwind šablona stránkování | cesta k view (`App\Views\pager\tailwind_pager`) |
| `$perPage` | Výchozí počet položek na stránku | celé číslo (výchozí `20`) |

### 7.5 `Config\Filters` (registrace filtru)
| Proměnná | Význam | Hodnoty |
|----------|--------|---------|
| `aliases['admin']` | Alias filtru pro ochranu administrace | třída `App\Filters\AdminFilter` |

---

## 8. Splnění programátorské složitosti

| Požadavek | Kde je splněno |
|-----------|----------------|
| Stránka s **JOINem** | Detail filmu (`Movie::get_genres/get_people`), detail osoby (`People::get_movies`), statistiky (`Movie::genre_stats`) |
| **Agregační funkce** | `Movie::genre_stats()` a `catalogue_totals()` – `COUNT/AVG/SUM`, dále `COUNT` v `Admin::index()` |
| **Routa se 2+ parametry** | `admin/edit/(:segment)/(:num)` → `Admin::edit/$1/$2`; `admin/movie/(:num)/people/(:num)/detach` → `Admin::detach_person/$1/$2` |

---

## 9. Spuštění projektu

```bash
# PHP závislosti
composer install

# JS / CSS závislosti a build Tailwindu
npm install
npm run build:css        # jednorázový build do assets/css/app.css
# npm run watch:css       # průběžný build při vývoji

# Databáze
php spark migrate
php spark db:seed IonAuthSeeder    # vytvoří admin účet (admin@admin.com / password)
php spark db:seed TMDBSeeder       # naplní filmy z TMDB (vyžaduje TMDB_API_KEY v .env)
```

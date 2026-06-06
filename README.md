# Stuff I'd Watch

Webová aplikace sloužící jako filmová databáze inspirovaná IMDb. Uživatelé mohou procházet filmy, filtrovat podle žánru, roku vydání nebo hodnocení a zobrazovat detaily jednotlivých filmů včetně popisu, obsazení a plakátu. Přihlášení administrátoři mohou záznamy přidávat, editovat a mazat.

Data jsou importována z volně dostupného datasetu TMDB/IMDb (CSV/JSON), čímž je dosaženo požadovaných 5 000+ záznamů.

---

## Tým

| Jméno | Role |
|---|---|
| Matvii Kopylov | Big Boss |
| Ostap Hlushko | Indian Recruit |

---

## Dokumentace

- 📄 [Dokumentace projektu](docs/DOKUMENTACE.md) — rozdělení práce, externí nástroje, citace AI, popis kontrolerů, knihoven a konfiguračních proměnných.

---

## Názvosloví — databáze

| Věc | Konvence | Příklad |
|---|---|---|
| Jazyk tabulek | angličtina | `movies`, `genres` |
| Notace tabulek | snake_case | `movie_genres` |
| Primární klíč | `id` | `id INT AUTO_INCREMENT` |
| Cizí klíč | `tabulka_id` | `genre_id`, `director_id` |
| Víceslovné názvy sloupců | snake_case | `release_date`, `poster_path` |

---

## Názvosloví — PHP / CodeIgniter 4

| Věc | Konvence | Příklad |
|---|---|---|
| Proměnné | camelCase | `$movieList`, `$currentPage` |
| Třídy (Controllers) | PascalCase + přípona | `MoviesController` |
| Třídy (Models) | PascalCase + přípona | `MovieModel` |
| Třídy (Libraries) | PascalCase + přípona | `UploadLib`, `FlashLib` |
| Složky Views | malá písmena dle controlleru | `views/movies/index.php` |
| Layout složka | `views/layout/` | `header.php`, `footer.php` |

---

## Názvosloví — metody v controllerech

Všechny controllery používají jednotné názvy metod:

| Metoda | HTTP | Popis |
|---|---|---|
| `index()` | GET | výpis všech záznamů |
| `show($id)` | GET | detail záznamu |
| `create()` | GET | formulář pro přidání |
| `store()` | POST | uložení nového záznamu |
| `edit($id)` | GET | formulář pro editaci |
| `update($id)` | POST | uložení editace |
| `delete($id)` | POST | smazání záznamu (soft delete) |

---

## Struktura databáze

```
movie          — hlavní tabulka filmů (název, popis, datum vydání, runtime, hodnocení, rozpočet…)
genres         — žánry (název)
people         — osoby (herci, režiséři…)
movie_genres   — vazební tabulka m:n (movie ↔ genres)
movie_people   — vazební tabulka m:n (movie ↔ people, včetně role)
tabulky pro Ion Auth
```

### Splněné podmínky zadání

| Podmínka                  | Řešení                                |
| ------------------------- | ------------------------------------- |
| Sloupec s obrázkem        | `pic VARCHAR` v tabulce `movie`       |
| Sloupec s datem           | `release_date DATE` v tabulce `movie` |
| Dlouhý text (>1000 znaků) | `description TEXT` v tabulce `movie`  |
| Tabulky z m:n relací      | `movie_genres`, `movie_people`        |
| Minimálně 15 sloupců      | tabulka `movie` obsahuje 15+ atributů |
| Reálná data               | data načítána z TMDB API              |


---

## Použité technologie

| Nástroj | Verze |
|---|---|
| CodeIgniter | 4.x |
| Bootstrap | 5.x |
| TinyMCE | 6.x |
| MySQL | 8.x |
| PHP | 8.x |
| IonAUTH | 4.x |

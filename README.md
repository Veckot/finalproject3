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
movies        — hlavní tabulka filmů (název, popis, rok, plakát, hodnocení...)
genres        — žánry (název)
directors     — režiséři (jméno, foto, bio)
actors        — herci (jméno, foto)
movie_genres  — vazební tabulka m:n (movies ↔ genres)
movie_actors  — vazební tabulka m:n (movies ↔ actors)
tabulký pro Ion Auth
```

### Splněné podmínky zadání

| Podmínka | Řešení |
|---|---|
| Sloupec s obrázkem | `poster_path VARCHAR` v tabulce `movies` |
| Sloupec s datem | `release_date DATE` v tabulce `movies` |
| Dlouhý text (>1000 znaků) | `description TEXT` v tabulce `movies` |
| Tabulky z m:n relací | `movie_genres`, `movie_actors` |
| Soft deletes | `deleted_at DATETIME NULL` v každé tabulce |

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

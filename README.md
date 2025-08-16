# My Library - System Zarządzania Osobistą Biblioteką

My Library to nowoczesna aplikacja webowa służąca do zarządzania osobistą biblioteką książek. Aplikacja pozwala użytkownikom dodawać, kategoryzować i śledzić swoje książki, integrując się z Google Books API w celu łatwego wyszukiwania i importowania metadanych książek.

## Funkcjonalności

### Zarządzanie książkami
- **Dodawanie książek** - ręczne wprowadzanie lub import z Google Books API
- **Kategorie statusów** - Do przeczytania, Czytam teraz, Przeczytane
- **Oceny i notatki** - System ocen 1-5 oraz osobiste notatki
- **Daty czytania** - Śledzenie dat rozpoczęcia i zakończenia czytania
- **Okładki książek** - Automatyczne pobieranie okładek z Google Books

### Wyszukiwanie i filtrowanie
- **Wyszukiwanie lokalne** - po tytule i autorze w własnej bibliotece
- **Filtry statusów** - szybkie filtrowanie według statusu czytania
- **Sortowanie** - według tytułu, autora, daty dodania, oceny
- **Paginacja** - efektywne przeglądanie dużych kolekcji

### Integracja z Google Books API
- **Wyszukiwanie książek** - po tytule, autorze lub ISBN
- **Import metadanych** - automatyczne wypełnianie informacji o książce
- **Podgląd szczegółów** - wyświetlanie dodatkowych informacji z Google Books
- **Wsparcie wielu języków** - polski i angielski

### Statystyki i panel główny
- **Dashboard** - przegląd statystyk biblioteki
- **Liczniki** - łączna liczba książek w różnych statusach
- **Ostatnio dodane** - lista najnowszych pozycji
- **Cel roczny** - śledzenie postępów w czytaniu (domyślnie 12 książek/rok)

### Zarządzanie użytkownikami
- **Rejestracja i logowanie** - pełny system uwierzytelniania (Laravel Breeze)
- **Profile użytkowników** - edycja danych osobowych
- **Bezpieczeństwo** - hash haseł, reset hasła, weryfikacja email
- **Izolacja danych** - każdy użytkownik widzi tylko swoje książki

## Technologie

### Backend
- **Laravel 12** (PHP 8.2+) - framework aplikacji webowej
- **Inertia.js** - połączenie backend/frontend bez API
- **SQLite** - baza danych (konfigurowana dla development)
- **Laravel Breeze** - system uwierzytelniania
- **Google Books API** - źródło metadanych książek

### Frontend
- **Vue.js 3** - framework JavaScript
- **Tailwind CSS** - framework CSS z komponenty @tailwindcss/forms
- **Vite** - bundler i dev server z SSR
- **Headless UI** - dostępne komponenty interfejsu

### Development Tools
- **Laravel Pint** - formatowanie kodu PHP
- **Laravel Pail** - viewer logów
- **PHPUnit** - testowanie
- **Concurrently** - równoczesne uruchamianie procesów dev

## Wymagania systemowe

- **PHP** >= 8.2
- **Composer** - menedżer zależności PHP
- **Node.js** >= 18 z npm - do frontend assets
- **SQLite** - baza danych (lub MySQL/PostgreSQL)
- **Opcjonalnie**: Klucz Google Books API (dla wyższych limitów)

## Instalacja

### 1. Klonowanie repozytorium
```bash
git clone https://github.com/your-username/my-library.git
cd my-library
```

### 2. Instalacja zależności Backend
```bash
composer install
```

### 3. Instalacja zależności Frontend
```bash
npm install
```

### 4. Konfiguracja środowiska
```bash
# Skopiuj plik konfiguracyjny
cp .env.example .env

# Wygeneruj klucz aplikacji
php artisan key:generate

# Utwórz bazę danych SQLite
touch database/database.sqlite
```

### 5. Konfiguracja bazy danych
```bash
# Uruchom migracje
php artisan migrate

# Opcjonalnie: załaduj przykładowe dane
php artisan db:seed
```

### 6. Konfiguracja Google Books API (opcjonalne)
1. Uzyskaj klucz API w [Google Cloud Console](https://console.cloud.google.com/)
2. Włącz Books API dla swojego projektu
3. Dodaj klucz do pliku `.env`:
```env
GOOGLE_BOOKS_API_KEY=your_api_key_here
```

## Uruchamianie aplikacji

### Środowisko deweloperskie (zalecane)
```bash
# Uruchomienie wszystkich serwisów jednocześnie
composer dev
```

Ta komenda uruchamia:
- **Laravel server** (http://localhost:8000) - niebieski
- **Queue worker** - przetwarzanie zadań - fioletowy
- **Log viewer** - podgląd logów - różowy  
- **Vite dev server** - hot reload - pomarańczowy

### Lub uruchamianie serwisów oddzielnie

#### Backend
```bash
# Serwer aplikacji
php artisan serve

# Worker kolejek (w osobnym terminalu)
php artisan queue:listen --tries=1

# Viewer logów (w osobnym terminalu)  
php artisan pail --timeout=0
```

#### Frontend
```bash
# Development server z hot reload
npm run dev
```

### Produkcja
```bash
# Build frontend assets
npm run build

# Konfiguracja serwera web (Apache/Nginx) na folder public/
```

## Testowanie

### Uruchamianie wszystkich testów
```bash
composer test
# lub
php artisan test
```

### Testowanie konkretnej klasy
```bash
php artisan test --filter=BookTest
```

### Typy testów
- **Feature tests** - testowanie funkcjonalności aplikacji
- **Unit tests** - testowanie pojedynczych klas/metod
- **Google Books Service** - testowanie integracji z API
- **Authentication** - testowanie Laravel Breeze

## Struktura projektu

```
my-library/
├── app/
│   ├── Http/Controllers/          # Kontrolery
│   │   ├── BookController.php     # CRUD książek
│   │   ├── SearchController.php   # Wyszukiwanie Google Books
│   │   └── ProfileController.php  # Profile użytkowników
│   ├── Models/                    # Modele Eloquent
│   │   ├── Book.php              # Model książki
│   │   └── User.php              # Model użytkownika
│   ├── Services/                  # Serwisy biznesowe
│   │   └── GoogleBooksService.php # Integracja Google Books API
│   └── Policies/                  # Polityki autoryzacji
│       └── BookPolicy.php        # Autoryzacja książek
├── database/
│   ├── migrations/               # Migracje bazy danych
│   ├── seeders/                  # Seedery testowych danych
│   └── factories/                # Fabryki modeli do testów
├── resources/
│   ├── js/
│   │   ├── Components/           # Komponenty Vue.js
│   │   ├── Layouts/              # Layouty aplikacji
│   │   └── Pages/                # Strony aplikacji
│   │       ├── Books/            # Strony zarządzania książkami
│   │       ├── Search/           # Wyszukiwanie książek
│   │       └── Auth/             # Strony uwierzytelniania
│   └── css/
│       └── app.css               # Style Tailwind CSS
├── routes/
│   ├── web.php                   # Route aplikacji webowej
│   └── auth.php                  # Route uwierzytelniania
└── tests/                        # Testy automatyczne
    ├── Feature/                  # Testy funkcjonalności
    └── Unit/                     # Testy jednostkowe
```

## Główne Encje

- **User** - użytkownicy systemu z rolami i relacjami do książek
- **Book** - książki z metadanymi, statusami i ocenami
- Statusy książek: `to_read`, `reading`, `read`
- Relacje: User hasMany Books, Book belongsTo User

## Kluczowe komponenty

### Model Book (app/Models/Book.php)
```php
// Statusy książek
const STATUS_TO_READ = 'to_read';
const STATUS_READING = 'reading'; 
const STATUS_READ = 'read';

// Pomocne metody
$book->markAsStarted();           // Oznacz jako rozpoczętą
$book->markAsFinished($rating);   // Oznacz jako przeczytaną
$book->getReadingDuration();      // Liczba dni czytania
```

### GoogleBooksService (app/Services/GoogleBooksService.php)
```php
// Wyszukiwanie książek
$service->searchBooks($query);                    // Ogólne wyszukiwanie
$service->searchByTitleAndAuthor($title, $author); // Wyszukiwanie po tytule/autorze
$service->searchByIsbn($isbn);                     // Wyszukiwanie po ISBN
$service->getBookById($googleBooksId);             // Szczegóły książki
```

## API Endpointy

### Główne trasy publiczne:
- `GET /` - strona główna
- `GET /login` - logowanie
- `GET /register` - rejestracja

### Trasy dla zalogowanych użytkowników:
- `GET /books` - lista książek użytkownika
- `POST /books` - dodaj nową książkę
- `GET /books/{id}` - szczegóły książki
- `PUT /books/{id}` - edytuj książkę
- `DELETE /books/{id}` - usuń książkę
- `PATCH /books/{id}/status` - zmień status książki
- `GET /search` - strona wyszukiwania
- `POST /api/search/books` - wyszukaj w Google Books
- `GET /api/books/stats` - statystyki biblioteki

## Konfiguracja

### Zmienne środowiskowe (.env)
```env
# Baza danych
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite

# Google Books API (opcjonalne)
GOOGLE_BOOKS_API_KEY=your_api_key

# Inne ustawienia Laravel...
```

### Konfiguracja Google Books (config/services.php)
```php
'google_books' => [
    'api_key' => env('GOOGLE_BOOKS_API_KEY'),
],
```

## Komendy deweloperskie

### Formatowanie kodu
```bash
./vendor/bin/pint              # Formatuj kod PHP
```

### Baza danych
```bash
php artisan migrate:fresh --seed    # Odśwież bazę z danymi testowymi
php artisan migrate:rollback         # Cofnij ostatnie migracje
```

### Cache i optymalizacja
```bash
php artisan config:cache       # Cache konfiguracji
php artisan view:cache         # Cache widoków
php artisan route:cache        # Cache route'ów
```

## Wdrożenie produkcyjne

### 1. Przygotowanie środowiska produkcyjnego
```bash
# Ustawienie środowiska produkcyjnego
APP_ENV=prod

# Budowa zasobów produkcyjnych
npm run build

# Czyszczenie i warming cache
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod
```

### 2. Optymalizacja
```bash
# Autoloader Composer dla produkcji
composer install --no-dev --optimize-autoloader

# Preload klas PHP (opcjonalnie)
composer dump-autoload --optimize --classmap-authoritative
```

## Rozwiązywanie problemów

### Błędy instalacji
1. **Błąd Composer**: Sprawdź wersję PHP (>=8.2)
2. **Błąd NPM**: Sprawdź wersję Node.js (>=18)
3. **Błąd bazy danych**: Sprawdź ścieżkę do pliku SQLite w `.env`

### Problemy z Google Books API
1. **Limit requestów**: Bez klucza API limit to 1000/dzień
2. **Brak wyników**: Sprawdź połączenie internetowe
3. **Błędy API**: Sprawdź logi w `storage/logs/laravel.log`

### Problemy z frontend
1. **Assets nie ładują się**: Uruchom `npm run dev`
2. **Błędy Vue**: Sprawdź console przeglądarki
3. **Stylowanie**: Sprawdź czy Tailwind CSS jest skompilowany

## Współpraca

1. Fork projektu
2. Stwórz branch dla nowej funkcjonalności (`git checkout -b feature/AmazingFeature`)
3. Commit zmian (`git commit -m 'Add some AmazingFeature'`)
4. Push do brancha (`git push origin feature/AmazingFeature`)
5. Otwórz Pull Request

### Standardy kodowania
- Używaj Laravel Pint do formatowania PHP
- Follow Vue 3 Composition API
- Dodaj testy dla nowych funkcjonalności
- Aktualizuj dokumentację

## Licencja

Ten projekt jest licencjonowany na licencji MIT - sprawdź plik [LICENSE](LICENSE) po szczegóły.

## Zgłaszanie błędów

Błędy i propozycje ulepszeń można zgłaszać przez system issue w repozytorium.

## Kontakt

W przypadku pytań technicznych skontaktuj się z zespołem deweloperskim.

## Przydatne linki

- [Laravel Documentation](https://laravel.com/docs)
- [Vue.js 3 Guide](https://vuejs.org/guide/)
- [Inertia.js Documentation](https://inertiajs.com/)
- [Tailwind CSS](https://tailwindcss.com/)
- [Google Books API](https://developers.google.com/books)

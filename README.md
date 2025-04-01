# Sample
## Wymagania
- PHP 8.2
- Postgresql 16
- Composer

## Instalacja
```bash
  composer install
```

## JWT
Generowanie kluczy dla JWT:
```bash
php bin/console lexik:jwt:generate-keypair
```

## Baza Danych
.env
```text
DATABASE_URL="postgresql://postgres:postgres@127.0.0.1:5432/capitalService?serverVersion=16&charset=utf8"
```
Generowanie bazy danych
```bash
  php bin/console doctrine:database:create
```
Migracje
```bash
  php bin/console doctrine:migrations:migrate
```

## Fixtury
```bash
  php bin/console doctrine:fixtures:load
```

## Start Servera
```bash
  symfony server:start
```

## Logowanie
Na start dostępny jest użytkownik administracyjny:
- login: admin
- hasło: admin

## API

### 1. Logowanie(pobranie tokenu JWT)

```bash
  POST /api/login
```

#### Parametr body

```bash
  {
    "login": "admin",
    "password": "admin"
  }
```

#### Odpowiedzi błędów

| Kod | Opis                |
| :-------- | :------------------------- |
| `400` | Brak parametru poświadczenia |
| `401` | Nieprawidłowe poświadczenie |
| `429` | Przekroczona liczba zapytań. Max 20 na minutę |

### 2. Kalkulacja

```bash
  POST /api/calculation
```

#### Parametry żądania

| Parametr | Typ     | Opis                |
| :-------- | :------- | :------------------------- |
| `amount` | `int` | **Wymagane**. Kwota pożyczki. Musi mieścić się w przedziale od 1000 do 12000 i być podzielna przez 500. |
| `installments` | `int` | **Wymagane**. Liczba rat. Musi mieścić się w przedziale od 3 do 18 i być podzielna przez 3. |

#### Odpowiedzi błędów

| Kod | Opis                |
| :-------- | :------------------------- |
| `400` | Nieprawidłowa wartość amount lub installments |
| `429` | Przekroczona liczba zapytań. Max 20 na minutę |

### 3. Wykluczenie Obliczenia

```bash
  PUT /api/exclude
```

#### Wymagana autoryzacja

```bash
  Authorization: Bearer <token_jwt>
```

#### Parametry żądania

| Parametr | Typ     | Opis                |
| :-------- | :------- | :------------------------- |
| `id` | `int` | **Wymagane**. ID obliczenia, które ma zostać wykluczone. |

#### Odpowiedzi błędów

| Kod | Opis                |
| :-------- | :------------------------- |
| `400` | Brak parametru `id` |
| `404` | Obliczenie o podanym `id` nie zostało znalezione |
| `401` | Brak lub nieprawidłowy token JWT |
| `429` | Przekroczona liczba zapytań. Max 20 na minutę |

### 4. Pobierz ostatnie 4 obliczenia

```bash
  GET /api/calculations
```

#### Wymagana autoryzacja

```bash
  Authorization: Bearer <token_jwt>
```

#### Parametry żądania

| Parametr | Typ     | Opis                |
| :-------- | :------- | :------------------------- |
| `filter` | `string` | **Opcjonalne**. Filtruje obliczenia według statusu excluded. Możliwe wartości: excluded (tylko obliczenia wykluczone) lub brak filtra (wszystkie obliczenia). |

#### Odpowiedzi błędów

| Kod | Opis                |
| :-------- | :------------------------- |
| `400` | Brak parametru `id` |
| `404` | Obliczenie o podanym `id` nie zostało znalezione |
| `401` | Brak lub nieprawidłowy token JWT |
| `429` | Przekroczona liczba zapytań. Max 20 na minutę |


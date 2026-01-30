# price-competitor

A small Laravel application that streams and aggregates competitor product prices, extracts vendor pricing from multiple API versions, and stores the lowest observed price per product.

## Key Concepts

- Streams JSON responses from configured competitor endpoints to avoid loading large payloads into memory.
- Supports multiple competitor API shapes via versioned extractors in `app/Http/Services/PriceSync/PriceExtractors`.
- Persists the lowest vendor price per product in the `ProductLowestPrice` model.

## Features

- Config-driven competitor endpoints: `config/competitor-apis.php`.
- Streaming HTTP client with retry/timeout: `app/Http/Clients/HttpStreamClient.php`.
- Versioned product price extractors for different JSON structures.
- PHPUnit feature tests under `tests/Feature`.

## Prerequisites

- PHP 8.3+
- Composer
- Node.js and npm (for frontend assets / Vite)
- A database supported by Laravel (SQLite is used for local quick setup in the scripts)

## Quick Setup (local)

1. Install PHP dependencies:

```bash
composer install
```

2. Install frontend dependencies and build assets:

```bash
npm install
npm run build
```

3. Copy the example environment and generate app key:

```bash
cp .env.example .env
php artisan key:generate
```

4. (Optional) Create an SQLite file for local development and run migrations:

```bash
touch database/database.sqlite
php artisan migrate
```

5. Start the app (development):

```bash
php artisan serve
```

## Configuration

- Competitor endpoints and parsing hints are defined in `config/competitor-apis.php`. Each entry currently supports:
  - `url` — the competitor API endpoint to stream from.
  - `json_pointer` — a JSON pointer path to the array/object containing pricing data.
  - `product_id` — the internal product id to match when extracting prices.

- HTTP streaming options (timeout/retries) are configured via `config/services.php` under the `http_stream` key.

## How it works (overview)

1. The app streams the JSON response from a competitor `url` using `HttpStreamClient`.
2. The streaming parser (e.g., `halaxa/json-machine`) walks the stream and yields product entries.
3. A version-specific extractor converts each entry into a `SyncProductPriceDTO`.
4. The service layer persists prices and updates the `ProductLowestPrice` model when a lower price is found.

## Tests

Run the test suite with:

```bash
php artisan test
```

See `tests/Feature/ProductControllerTest.php` for examples of expected behavior.

## Developer Notes

- Add new competitor endpoints to `config/competitor-apis.php` and, if necessary, implement a new extractor under `app/Http/Services/PriceSync/PriceExtractors` implementing `App\\Http\\Contracts\\ExtractProductPriceContract`.
- Use the streaming client for large responses to avoid memory spikes.

## Useful Files

- `config/competitor-apis.php` — configured competitor endpoints.
- `app/Http/Clients/HttpStreamClient.php` — streaming HTTP client with retry/timeout.
- `app/Http/Services/PriceSync/PriceExtractors` — versioned extractors.
- `app/Models/ProductLowestPrice.php` — model storing the lowest price record.

## License

MIT


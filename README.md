# Fleet CMS

Small multi-tenant fleet manager for an interview exercise. PHP, MySQL, [bramus/router](https://github.com/bramus/router), jQuery, and Bootstrap.

The important rule: **tenant identity (`level` + `level_id`) always comes from the signed-in user**. It is never taken from a form, query string, or JSON body. Changing a vehicle or inspection id in the URL cannot cross from Company A (`level_id` 10) to Company B (`level_id` 20).

Learning notes and docs: [LEARNING.md](LEARNING.md). Continuing on a Mac: [HANDOFF.md](HANDOFF.md).

## Run with Docker (recommended for the employer)

From this folder:

```bash
docker compose up --build
```

App: http://localhost:8080

MySQL is on host port **3307** (container 3306) so it is less likely to clash with a local MySQL install.

The first start imports `schema.sql` and hashes demo passwords.

If the database was created on an older compose run, reset it with:

```bash
docker compose down -v
docker compose up --build
```

## Run with XAMPP / WAMP

1. Copy `config/local.php.example` to `config/local.php` and set your MySQL user/password.
2. Import `schema.sql` in phpMyAdmin (or `mysql < schema.sql`).
3. Point the site document root at the `public/` folder (not the project root).
4. Install PHP dependencies: `composer install`
5. Hash demo passwords: `php bin/seed.php`
6. Open the site in the browser.

`public/` is the only web root. `src/`, `views/`, `config/`, and `vendor/` must not be served.

## Demo logins

Password for every demo user: `Password123!`

| Email | Role | Tenant |
|---|---|---|
| admin@companya.test | Admin | Company A (`level_id` 10) |
| manager@companya.test | Manager | Company A |
| driver@companya.test | Driver | Company A |
| admin@companyb.test | Admin | Company B (`level_id` 20) |

Try signing in as Company A, then open `/vehicles/3` (Company B’s van). You should get **Not found**, not Company B’s data.

## Roles

- **Admin** — add/edit/delete vehicles and inspections (own tenant only)
- **Manager** — add/edit/delete inspections; view vehicles
- **Driver** — view vehicles and inspections only

## What to look at in a review

- `schema.sql` — `level` / `level_id` on users, vehicles, inspections
- `src/Security/Auth.php` — session tenant
- `src/Repositories/*` — every query includes `level = ? AND level_id = ?`
- `src/Security/Gate.php` — role checks
- `src/routes.php` — auth + CSRF on POST

# Learning notes

This file is a map of what the app is doing, with official docs. Read it alongside the code rather than as a substitute.

## Routing

The front controller is `public/index.php`. Almost every URL is rewritten there (Apache `.htaccess` or Docker vhost).

We use **bramus/router**: define HTTP method + path, then run a PHP callable.

- Repo and README: https://github.com/bramus/router
- Packagist: https://packagist.org/packages/bramus/router
- Apache rewrite gist they recommend: https://gist.github.com/bramus/5332525

Patterns used here:

- Static: `/login`, `/vehicles`
- Numeric ids: `/vehicles/(\d+)` — only digits, so `/vehicles/abc` is 404
- `mount('/vehicles', …)` prefixes a group of routes

## PHP and Composer

- PHP language: https://www.php.net/manual/en/index.php
- `declare(strict_types=1)`: https://www.php.net/manual/en/language.types.declarations.php#language.types.declarations.strict
- Composer autoload (PSR-4): https://getcomposer.org/doc/04-schema.md#psr-4
- Front controller idea: one `index.php` entry, the rest of PHP stays off the web root

## Database and SQL injection

All queries use **PDO prepared statements**. User input is bound as parameters, never concatenated into SQL.

- PDO: https://www.php.net/manual/en/book.pdo.php
- Prepared statements: https://www.php.net/manual/en/pdo.prepared-statements.php
- `ATTR_EMULATE_PREPARES => false` so MySQL sees real prepares
- OWASP SQL injection: https://owasp.org/www-community/attacks/SQL_Injection
- OWASP SQL injection prevention: https://cheatsheetseries.owasp.org/cheatsheets/Query_Parameterization_Cheat_Sheet.html

## Authentication

- `password_hash` / `password_verify`: https://www.php.net/manual/en/function.password-hash.php
- Sessions: https://www.php.net/manual/en/book.session.php
- `session_regenerate_id(true)` on login to stop session fixation: https://owasp.org/www-community/attacks/Session_fixation
- HttpOnly cookies so JavaScript cannot read the session id: https://owasp.org/www-community/HttpOnly
- OWASP authentication cheat sheet: https://cheatsheetseries.owasp.org/cheatsheets/Authentication_Cheat_Sheet.html
- OWASP session cheat sheet: https://cheatsheetseries.owasp.org/cheatsheets/Session_Management_Cheat_Sheet.html

## Authorisation and tenancy (`level` / `levelId`)

This is the core of the brief.

1. User logs in. We store `role`, `level`, and `level_id` in the **session**.
2. Repositories take those values from `Auth::tenant()`.
3. Every SELECT/UPDATE/DELETE includes `id = ? AND level = ? AND level_id = ?`.
4. If someone edits the URL to another company’s vehicle id, the row does not match → **404**. We do not return 403 with “this belongs to company 20”, which would leak that the record exists.
5. `level_id` from POST/GET is ignored for tenancy. New vehicles get the session tenant, not a hidden field.

Related reading:

- Broken access control (OWASP Top 10): https://owasp.org/Top10/A01_2021-Broken_Access_Control/
- Insecure Direct Object Reference (IDOR): https://cheatsheetseries.owasp.org/cheatsheets/Insecure_Direct_Object_Reference_Prevention_Cheat_Sheet.html
- Access control cheat sheet: https://cheatsheetseries.owasp.org/cheatsheets/Access_Control_Cheat_Sheet.html

Roles (RBAC, kept small):

- Admin: `vehicles.manage` + `inspections.manage`
- Manager: `inspections.manage` only
- Driver: view only

Same-tenant role failure → **403**. Cross-tenant id guessing → **404**.

## CSRF and XSS

- CSRF token in session, hidden field `_csrf`, compared with `hash_equals`
- OWASP CSRF: https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html
- Output encoding with `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')` in views
- OWASP XSS: https://cheatsheetseries.owasp.org/cheatsheets/Cross_Site_Scripting_Prevention_Cheat_Sheet.html

## Validation

Controllers validate types, ranges, and allowed enums before hitting the database. Inspecting a vehicle still loads that vehicle **through the tenant-scoped repository**, so you cannot attach an inspection to another company’s vehicle by posting their `vehicle_id`.

- Filter/validate: https://www.php.net/manual/en/book.filter.php

## Frontend

- Bootstrap 5 docs: https://getbootstrap.com/docs/5.3/getting-started/introduction/
- jQuery: https://api.jquery.com/
- Confirm-on-delete is a small jQuery handler in `public/assets/js/app.js`. It is UX only. Real protection is the server.

## Docker (virtual copy for the employer)

- Compose file reference: https://docs.docker.com/compose/compose-file/
- Official PHP Apache image: https://hub.docker.com/_/php
- Official MySQL image: https://hub.docker.com/_/mysql

Document root is `public/`. Apache is told to deny `src/`, `config/`, and `views/` even if someone mis-points the vhost.

## How to practise the security story

1. Log in as `admin@companya.test`.
2. Note a Company A vehicle id (1 or 2).
3. Log out, log in as `admin@companyb.test`.
4. Visit `/vehicles/1` — should be not found.
5. As Company A driver, visit `/vehicles/create` — should be 403.
6. As Company A manager, you can create inspections but not add a vehicle.

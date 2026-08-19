# Handoff — continue on macOS

Saved from the Windows Cursor session so Declan can pick this up on a Mac. Open this folder in Cursor and start a new chat with: *“Read HANDOFF.md and keep going.”*

## What this is

Interview take-home: a **small Fleet Management System**. The company cares about structure, database, security, and permissions — not a huge feature set.

**Stack (match their house tools, learn as we go):** PHP, MySQL, [bramus/router](https://github.com/bramus/router), jQuery, Bootstrap. One repo, not separate frontend/backend repos.

**Project folder:** `fleet-cms` (currently `C:\Users\Declan\fleet-cms` on Windows).

## Brief (what they asked for)

- User login
- Users belong to a business via **`level` + `levelId`**
- Vehicles: registration, make, model, year, mileage, MOT expiry, tax expiry, status — add / edit / view
- Inspections: date, mileage, damage reported, notes, status
- Roles:
  - **Admin** — manage vehicles and inspections
  - **Manager** — manage inspections (vehicles read-only)
  - **Driver** — view only
- **Hard requirement:** Company A (`levelId` 10) must never see/edit/delete Company B (`levelId` 20), even if someone tampers with vehicle id, inspection id, or the request body
- Provide **database SQL** so they can run it locally
- Prefer a small system done properly over lots of features

They said we are welcome to use PHP, MySQL, JavaScript, jQuery, and Bootstrap.

## Decisions already made

- **One repository** (`client`/`server` split is unnecessary for this size)
- Stay close to **PHP + jQuery + Bootstrap + MySQL**, not React/Node
- **Docker Compose** is the “virtual copy” for the employer (`docker compose up --build` → http://localhost:8080)
- Web root is **`public/` only** so `src/`, `views/`, `config/`, `vendor/` are not HTTP-accessible
- Tenant (`level`, `level_id`) is taken from the **session**, never from the browser
- Cross-tenant id guessing → **404** (do not leak that the other company’s row exists)
- Wrong role, same tenant → **403**
- Demo password is hashed on first run by `php bin/seed.php` (Docker runs this automatically)

## Demo logins

Password for every demo user: `Password123!`

| Email | Role | Tenant |
|---|---|---|
| admin@companya.test | Admin | Company A (`level_id` 10) |
| manager@companya.test | Manager | Company A |
| driver@companya.test | Driver | Company A |
| admin@companyb.test | Admin | Company B (`level_id` 20) |

Sanity check: as Company A, open `/vehicles/3` (Company B’s van) → Not found.

## Code map

| Path | Why it matters |
|---|---|
| `schema.sql` | Tables + seed; `level` / `level_id` on users, vehicles, inspections |
| `src/routes.php` | Bramus Router; login required; CSRF on POST |
| `src/Security/Auth.php` | Login, session, `Auth::tenant()` |
| `src/Security/Gate.php` | Admin / manager / driver |
| `src/Repositories/` | Every query: `level = ? AND level_id = ?` |
| `LEARNING.md` | Docs links (PDO, OWASP, Bramus, Bootstrap, Docker) |
| `README.md` | How to run |

Git was **not** initialised on Windows (`git` was not on PATH). Initialise on the Mac if you want version control.

## Get this folder onto the Mac

This chat will not automatically appear in Cursor on another computer. Copy the **project folder**.

Pick one:

1. **USB / AirDrop / shared drive** — copy `fleet-cms` (the whole directory).
2. **iCloud / Dropbox** — copy the folder into a synced location, wait until it finishes.
3. **GitHub private repo** — once Git works on the Mac: `git init`, commit, push, clone on the Mac.

Then on the Mac: Cursor → **File → Open Folder** → `fleet-cms`.

## macOS setup (Git, Docker, optional PHP)

You were a Mac/Linux-style developer; Homebrew is the usual path.

### 1. Homebrew (if missing)

https://brew.sh

```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

Apple Silicon: follow the “Next steps” it prints to add `brew` to your PATH (`/opt/homebrew/bin`).

### 2. Git

```bash
brew install git
git --version
```

Xcode Command Line Tools (`xcode-select --install`) also provide Git if you prefer that.

### 3. Docker Desktop (recommended to run this app)

https://docs.docker.com/desktop/setup/install/mac-install/

Or:

```bash
brew install --cask docker
```

Open **Docker.app**, wait until it says Docker is running, then:

```bash
cd /path/to/fleet-cms
docker compose up --build
```

App: http://localhost:8080

MySQL is published on host port **3307**.

### 4. PHP + Composer (optional)

Only if you want to run without Docker (MAMP/Herd/local MySQL):

```bash
brew install php composer
```

Then: import `schema.sql`, copy `config/local.php.example` → `config/local.php`, `composer install`, `php bin/seed.php`, point the web server at `public/`.

## Next work (when the Mac is ready)

1. Confirm Docker login + vehicle list works.
2. Walk the security story using `LEARNING.md`.
3. `git init` and a first commit (only if Declan wants Git).
4. Polish UI / README for the employer; do not add big extra features.

## Prompt to paste in a new Cursor chat on the Mac

```
Open fleet-cms. Read HANDOFF.md, README.md, and LEARNING.md.
This is the interview Fleet CMS (PHP, MySQL, Bramus Router, jQuery, Bootstrap).
Tenant isolation via session level + levelId is the main review point.
Help me run it with Docker on macOS and keep going from there.
```

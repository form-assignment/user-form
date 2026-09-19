# User Registration System (Supabase)

A complete user registration form with Supabase PostgreSQL storage, server-side validation, and styled success/error messages. Includes a PHP backend as an alternative.

## Quick Start (Supabase — Recommended)

1. Go to [supabase.com](https://supabase.com) and create a project
2. Open **SQL Editor** → paste `db_setup.sql` → **Run**
3. Open `index.html` through a local server (see below)
4. Fill out the form and submit — data goes directly into your Supabase database

### Local Server Options

**Python:**
```bash
cd C:\Users\Nedu\Desktop\assignment
python -m http.server 8000
# Open http://localhost:8000/index.html
```

**Node.js:**
```bash
npx serve .
```

**XAMPP:** Place folder in `htdocs/` and open `http://localhost/assignment/`

## Files

| File | Description |
|------|-------------|
| `index.html` | Registration form with Supabase client (vanilla JS, no Edge Function needed) |
| `submit-registration.php` | PHP backend using PDO_PGSQL (alternative) |
| `db_setup.sql` | PostgreSQL schema |
| `.env.example` | Environment variable template |
| `.htaccess` | URL rewrite for PHP backend |
| `.gitignore` | Ignores .env, logs, etc. |

## Supabase Details

Your credentials are already configured in `index.html`:
- **Project URL:** `https://rwlmzeqiniruoqcwpyim.supabase.co`
- **Anon Key:** Configured in the `<script type="module">` section
- **Table:** `users` (created by `db_setup.sql`)

To view submitted data: Supabase Dashboard → Table Editor → `users`

## PHP Backend (Alternative)

If you prefer PHP, configure `submit-registration.php`:

```php
define('DB_HOST', 'aws-0-eu-west-2.pooler.supabase.com');
define('DB_PORT', '5432');
define('DB_NAME', 'postgres');
define('DB_USER', 'postgres.rwlmzeqiniruoqcwpyim');
define('DB_PASS', 'your-password'); // Set your Supabase DB password
```

Then:
1. Ensure `php-pdo_pgsql` extension is enabled
2. Serve via PHP server: `php -S localhost:8000`
3. Change `BACKEND` in `index.html` to `'php'`
4. Open `http://localhost:8000/index.html`

## Form Fields

| Field | Type | Validation |
|-------|------|------------|
| Full Name | text | Required |
| Email Address | email | Required, valid format |
| Age | number | Min 18, max 120 |
| Gender | radio | Required (Male, Female, Prefer not to say) |
| Country | select | Required (5 countries) |
| Interests | checkbox | Multi-select (Sports, Music, Technology, Art) |
| Bio | textarea | Max 500 characters |

## How It Works

1. User fills out the form and clicks **Submit**.
2. JavaScript uses the **Supabase client** (`@supabase/supabase-js` from CDN) to insert data directly into the `users` table.
3. On success, displays a green success message.
4. On error (validation or database), displays a red error message with details.
5. A debug panel shows technical details for troubleshooting.

## Troubleshooting

- **CORS issues:** Make sure you open `index.html` via `http://localhost` (not `file://`).
- **405 on PHP:** Ensure `.htaccess` is active and `mod_rewrite` is enabled.
- **Table not found:** Run `db_setup.sql` in Supabase SQL Editor.
- **RLS blocking insert:** In Supabase SQL Editor, run `ALTER TABLE users DISABLE ROW LEVEL SECURITY;` (the anon key bypasses RLS for direct client usage if RLS policies allow it).
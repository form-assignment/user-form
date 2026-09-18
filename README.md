# User Registration System

A complete user registration form with server-side validation, database storage via PDO, and user feedback with styled success/error messages.

## Files

| File | Description |
|------|-------------|
| `index.html` | Registration form with 8 fields |
| `submit-registration.php` | Server-side handler (validation, PDO insert, redirect with feedback) |
| `db_setup.sql` | Database schema |
| `ca-cert.pem` | SSL CA certificate for Aiven Cloud MySQL |
| `.htaccess` | Apache URL rewrite rule for `/submit-registration` |
| `nginx.conf` | Nginx configuration example |
| `Dockerfile` | Docker image with Apache + PHP 8.2 + PDO MySQL |
| `apache-vhost.conf` | Apache virtual host config (used in Docker) |

## Aiven Deployment

Since you are deploying to Aiven Runtime (containerized), build and deploy using the included Dockerfile:

1. Build the Docker image:
   ```bash
   docker build -t registration-app .
   ```
2. Run locally to test:
   ```bash
   docker run -p 8080:80 registration-app
   ```
3. Push to your Git repo and deploy via Aiven Runtime.

The Dockerfile includes PHP 8.2 with Apache and the PDO MySQL extension. The `apache-vhost.conf` enables URL rewriting so `/submit-registration` resolves to `submit-registration.php`.

## Database Setup

Import `db_setup.sql` into your Aiven MySQL database:

```bash
mysql -u avnadmin -p -h mysql-3b5bf10a-ifediorahsamuels-63da.e.aivencloud.com -P 17298 --ssl-ca=ca-cert.pem < db_setup.sql
```

Ensure `ca-cert.pem` is in the same directory as `submit-registration.php`.

## Local Development

Serve the files through a PHP-enabled web server (e.g., XAMPP, WAMP, MAMP) and navigate to `index.html`. For clean URLs locally, configure URL rewriting for `/submit-registration` to `submit-registration.php` using `.htaccess` or `nginx.conf`.

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
2. Form sends a **POST** request to `/submit-registration`.
3. `submit-registration.php` validates all inputs server-side.
4. On validation errors, redirects back to `index.html` with error messages.
5. On success, inserts data into `users` table via **PDO prepared statements**, then redirects with a success message.
6. JavaScript on `index.html` reads URL query params and displays styled success/error messages.

## Troubleshooting 405 Errors

A 405 (Method Not Allowed) on form submission means the server cannot route `/submit-registration` to the PHP handler:

- **Aiven Runtime**: Ensure the `Dockerfile` builds with Apache and `mod_rewrite` enabled. The `apache-vhost.conf` must set `AllowOverride All`.
- **Local Apache**: Enable `mod_rewrite` and set `AllowOverride All` in your virtual host.
- **Nginx**: Add the `location = /submit-registration` block from `nginx.conf` to your server config.
- **Any platform**: Verify PHP POST requests are supported and the web server routes POST to the PHP handler.# user-form

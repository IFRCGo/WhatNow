# Laravel 13 migration

## Status

Application source and container configuration have been adapted from Laravel
5.8 to Laravel 13, targeting PHP 8.4. **This migration is not validated or ready
for deployment yet.** The checked-in `composer.lock` still describes Laravel
5.8 because the local environment cannot resolve Packagist/GitHub and its cache
lacks several required packages. No lockfile contents have been fabricated.
Docker is also unavailable locally: its daemon is stopped and the installed
Docker.app cannot launch because its executable is missing. The container has
not been built.

The deployment workflows require the Laravel 13 tests and reject the old lock.
Do not bypass these checks or run `composer update` inside a production image.

## Local verification

- PHP 8.4.19 syntax and compilation deprecation checks: 254 source/config/test
  files passed.
- Workflow and Docker Compose YAML, PHPUnit XML and shell script syntax passed.
- `npm run production` completed successfully using the existing local Node and
  installed npm dependencies. The Node 22 container stage has not been run.
- Token comparison confirmed that the OpenAPI conversion retained controller
  implementation code, apart from explicit nullable type updates.
- Composer metadata is syntactically valid; strict lock validation fails as
  expected because the Laravel 13 dependencies could not be resolved.
- Application boot, PHPUnit 12 execution, Swagger generation, Composer audit and
  Docker builds have not passed validation and remain required.

## Changes

- Laravel 13, PHP >= 8.3 with dependency resolution targeting PHP 8.4.
- Guzzle 7, Socialite 5, Tinker 3, JWT Auth 2.3, Laravel UI 4.6.3 and
  Laravel Excel 3.1.70 or later in the 3.1 series. Laravel UI supplies the
  authentication traits that were removed from the framework.
- Framework CORS and trusted proxy middleware replace the old packages;
  JWT signing uses Lcobucci instead of the removed Namshi implementation.
- Throwable-compatible exception handler, JsonResource classes, modern mail/logging configuration,
  Laravel 13 cache serialization policy and JSON session serialization.
- Eloquent datetime casts replace `$dates`. Explicit serializers preserve the
  previous `Y-m-d H:i:s` JSON format for User, Terms and History.
- Existing controller namespaces, `resources/lang` and the legacy application
  layout are retained. Database seeders now use the `Database\Seeders` namespace.
- Class-based factories and PHPUnit 12 attributes replace removed testing APIs.
- L5-Swagger 11 configuration and PHP OpenAPI attributes replace annotations.
- PHP 8.4 Apache image, required extensions and a separate Node 22 asset build.
  Production dependencies exclude development tools. Build uses the committed
  lockfile and checks platform requirements.
- Runtime smoke tests cover Apache, PHP sessions and multipart uploads. CI also
  checks application boot, route/config/view compilation, Swagger and the suite.

Existing `APP_KEY` and `JWT_SECRET` must be retained. Do not generate new secrets
for an existing deployment. Symfony mail uses automatic STARTTLS for SMTP; an
existing `MAIL_ENCRYPTION=ssl` selects SMTPS. Existing environment names for cache,
queue and filesystem defaults remain accepted.

## Finish dependency resolution and validation

Use PHP 8.4 and Composer 2 with network access. On Herd, invoke its PHP 8.4 binary
explicitly if the project's default PHP still points to 7.4.

```bash
composer update --with-all-dependencies --no-scripts --no-interaction
composer dump-autoload --optimize
composer validate --strict --no-check-publish
composer check-platform-reqs
composer audit
php artisan --version
php artisan config:cache
php artisan config:clear
php artisan route:list --except-vendor
php artisan route:cache
php artisan route:clear
php artisan view:cache
php artisan view:clear
php artisan l5-swagger:generate
composer test
```

Resolve dependency conflicts and test failures, then commit `composer.lock` with
the migration. The old `vendor` tree must be replaced using Composer; it cannot
run this migrated source. Do not use `--ignore-platform-reqs` or disable audit
blocking to force a build.

With Docker running:

```bash
docker build --pull --target php-runtime -f docker/Dockerfile -t whatnow-runtime:laravel13 .
bash docker/test-php-runtime.sh whatnow-runtime:laravel13
docker build --pull -f docker/Dockerfile -t whatnow:laravel13 .
bash docker/test-php-runtime.sh whatnow:laravel13
```

Test against a disposable restored database in staging, including login, inactive
and unconfirmed accounts, OAuth, password reset/setup, roles, messages,
translations, file uploads, XLSX import/export, mail delivery and audit history.
Some existing tests and data migrations reference roles removed by later
migrations; investigate failures against the current intended permissions rather
than weakening assertions. Never point the test suite at a production database.
The default test configuration forces SQLite in memory and array mail/cache.

## Release and rollback

Before release, record the currently deployed image digest and take a verified
database backup. Retain existing secrets and external service settings. Run the
staging pipeline with the validated committed lock and review its results before
using the manual production workflow. This source change does not itself add a
new database migration; inspect the pending migration list before running any.
Never run sample database seeders on production as part of this upgrade.

A rollback must restore the known-good image and its configuration together;
clear/rebuild caches for the restored version. If migrations were applied,
review their reversibility and the database backup before reverting data. During
the PHP vulnerability incident, do not reopen an unpatched PHP 7.4 image.
The older `docker/build-patched-php.sh` is historical and is no longer used by
this Dockerfile or the current runtime checks.

## References

- https://laravel.com/docs/13.x/upgrade
- https://laravel.com/docs/13.x/releases
- https://packagist.org/packages/tymon/jwt-auth
- https://packagist.org/packages/laravel/ui
- https://packagist.org/packages/maatwebsite/excel
- https://packagist.org/packages/darkaonline/l5-swagger

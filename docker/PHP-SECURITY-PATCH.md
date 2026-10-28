> Historical emergency approach, superseded by the Laravel 13 / PHP 8.4 migration.
> The current Dockerfile no longer builds the PHP 7.4 backport or uses its stages.
> See [migration status](../docs/LARAVEL-13-UPGRADE.md) before building or deploying.

# PHP 7.4 emergency backport — 22 October 2026

## Scope and status

The Docker build now recompiles PHP 7.4.33 and Apache's PHP module with the
official minimal EOL backport for CVE-2026-109618 and CVE-2026-109619:

- Advisory: https://www.php.net/archive/2026.php#2026-10-21-5
- Source branch: https://github.com/php/php-src/tree/PHP-7.4-CVE-2026-109618-109619
- Pinned commit: https://github.com/php/php-src/commit/712e60f9edfd03f0960fc4fc3c2c375f42b90aa2

This is a source backport, not a new PHP release. `php -v` will still report
7.4.33. It does not restore general security support to PHP 7.4 or Laravel 5.8.
It only affects images built from `docker/Dockerfile`. The older Bitbucket/SSH
deployment scripts, separate API containers and the host's local PHP installation
are not patched by these changes.
The upstream change disables session upload-progress tracking and terminates
workers on allocator OOM. Ordinary file uploads still need smoke testing.

The changes are prepared locally. Shell/PHP syntax, workflow YAML and whitespace
checks passed. The local unpatched PHP fails the new OOM regression condition
(exit 255 and shutdown callback runs); the backport is expected to exit 1 without
running that callback, as specified in its allocator change.
Compilation and container tests have NOT run in the preparation environment:
Docker socket access is denied. Production has NOT been changed; the cluster's
hostname could not be resolved from that environment.

## Build and test

First run the runtime-only build, which does not require application secrets,
Node dependencies, a database, or access to the registry:

```sh
docker build --pull --target patched-runtime -f docker/Dockerfile -t whatnow-php-security:local .
bash docker/test-php-runtime.sh whatnow-php-security:local
```

The build fetches the immutable upstream commit through Git over HTTPS, applies
its exact diff to the official PHP 7.4.33 source with no fuzz, and compares all
changed files with the upstream tree. Compilation fails if any step fails.
It rebuilds CLI, Apache's module and bundled shared extensions, preserves patched
headers/source for subsequent extension builds, checks the new allocator symbol
in both binaries and records SHA-256 checksums in `/usr/local/share/php-security`.

The smoke test starts a disposable container without external networking or
published ports. It checks recorded binary/source hashes, allocator OOM behavior,
real Apache/mod_php execution, session persistence and multipart upload contents.
It removes the container on exit. The PHP test fixture is not an application route.
These checks are not an exhaustive exploit assessment or a full application test.

Then build and test the complete application:

```sh
docker build --pull -f docker/Dockerfile -t whatnow:php-security-20261022 .
bash docker/test-php-runtime.sh whatnow:php-security-20261022
docker run --rm --network none --entrypoint composer whatnow:php-security-20261022 check-platform-reqs --no-dev
```

Application dependencies remain on the existing Composer lockfile. The final
build also verifies the runtime and production platform requirements. `.env`,
local dependencies and certificate/secret files are excluded from the build
context. Swagger's public URL is supplied with `--build-arg SWAGGER_URL=...`.

The full application build retains the existing Node 16/package installation
steps. Failures in those legacy dependencies must be resolved before deployment;
do not substitute the original unpatched PHP image to bypass a failed build.

## Deployment

1. Keep public traffic blocked upstream of PHP while patching. A Laravel
   maintenance response does not prevent request parsing inside PHP.
2. Preserve the current deployment image digest, Helm revision, logs and existing
   database/storage backups before replacing the workload. Review exposure and
   possible signs of compromise; patching does not remove an existing backdoor.
3. Commit and push only the reviewed patch changes. Existing unrelated working
   tree changes must be reviewed separately. Run **PHP security runtime check**
   in GitHub Actions. Deploy to staging and validate login, permissions, message
   editing, imports/exports and the backend API before production.
4. Run the production workflow for that reviewed commit. Both staging and
   production workflows run the container smoke test BEFORE pushing images and
   select the exact commit tag for Helm, rather than deploying `latest`. Helm
   waits up to ten minutes for the rollout; it does not automatically roll back
   to an older potentially vulnerable runtime.
5. Verify the actual running pods, not only the CI image. Replace the namespace
   and pod placeholders below with the inspected deployment values:

```sh
kubectl -n NAMESPACE get deployment whatnow-backoffice -o wide
kubectl -n NAMESPACE rollout status deployment/whatnow-backoffice --timeout=10m
kubectl -n NAMESPACE get pods -o wide
kubectl -n NAMESPACE exec POD -c backoffice -- /usr/local/bin/verify-php-runtime
```

Repeat the last check for EVERY pod serving the application. Confirm their image
IDs/digests and the absence of older serving replicas. Check the separate API
deployment and any other PHP endpoints as well; this repository does not prove
that the separate API runtime is patched. Reopen traffic only after deployment
verification and functional checks succeed.

## If validation or rollout fails

Keep public traffic blocked, preserve logs and correct the failure. A Helm
rollback to the previous image can reintroduce the vulnerability; it is only a
recovery option behind continued infrastructure isolation. Do not treat a
successful rollback as security remediation. No database migration is part of
this patch.

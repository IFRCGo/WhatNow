#!/usr/bin/env bash
set -euo pipefail

if [[ $# != 1 ]]; then
    printf 'Usage: bash docker/test-php-runtime.sh IMAGE\n' >&2
    exit 2
fi
image=$1
root=$(cd "$(dirname "$0")/.." && pwd)
container=
cleanup() {
    if [[ -n "$container" ]]; then docker rm -f "$container" >/dev/null; fi
}
trap cleanup EXIT

# No published ports or external connectivity. Exercise real Apache/mod_php,
# not only the CLI version string. This works for runtime and app images.
container=$(docker run --detach --network none \
    --mount "type=bind,source=$root/docker/vhost.conf,target=/etc/apache2/sites-available/000-default.conf,readonly" \
    --mount "type=bind,source=$root/docker/tests/runtime-probe.php,target=/var/www/html/public/__php_security_probe.php,readonly" \
    "$image")
docker exec "$container" /usr/local/bin/verify-php-runtime

for expected in 1 2; do
    response=$(docker exec "$container" curl --fail --silent --show-error \
        --retry 10 --retry-connrefused --retry-delay 1 --max-time 5 \
        --cookie /tmp/php-security-cookies --cookie-jar /tmp/php-security-cookies \
        http://127.0.0.1/__php_security_probe.php)
    printf '%s' "$response" | docker exec -i "$container" php -r '
        $data = json_decode(stream_get_contents(STDIN), true);
        if (!$data || $data["sapi"] !== "apache2handler" ||
            version_compare($data["version"], "8.4.0", "<") || version_compare($data["version"], "8.5.0", ">=") || $data["requests"] !== (int)$argv[1]) {
            fwrite(STDERR, "Apache/session smoke test failed\n"); exit(1);
        }
    ' "$expected"
done

docker exec "$container" sh -c 'printf %s php-runtime-upload > /tmp/php-security-upload.txt'
response=$(docker exec "$container" curl --fail --silent --show-error --max-time 10 \
    --cookie /tmp/php-security-cookies \
    --form file=@/tmp/php-security-upload.txt \
    http://127.0.0.1/__php_security_probe.php)
printf '%s' "$response" | docker exec -i "$container" php -r '
    $data = json_decode(stream_get_contents(STDIN), true);
    if (!$data || $data["requests"] !== 3 ||
        $data["upload_sha256"] !== hash("sha256", "php-runtime-upload")) {
        fwrite(STDERR, "Multipart smoke test failed\n"); exit(1);
    }
'
printf 'PASS: PHP 8.4, Apache, sessions and multipart upload.\n'

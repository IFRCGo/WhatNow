#!/usr/bin/env bash
set -euo pipefail

# Official minimal EOL backport linked from the PHP security advisory:
# https://www.php.net/archive/2026.php#2026-10-21-5
# https://github.com/php/php-src/tree/PHP-7.4-CVE-2026-109618-109619
readonly commit=712e60f9edfd03f0960fc4fc3c2c375f42b90aa2
readonly module=/usr/lib/apache2/modules/libphp7.so
readonly evidence=/usr/local/share/php-security

[[ "$(php -r 'echo PHP_VERSION;')" == 7.4.33 ]]
configure_options=$(php-config --configure-options)

git init /tmp/php-security-upstream
git -C /tmp/php-security-upstream remote add origin https://github.com/php/php-src.git
# Include the parent so the diff contains only the reviewed security commit.
git -C /tmp/php-security-upstream fetch --depth=2 origin "$commit"
[[ "$(git -C /tmp/php-security-upstream rev-parse FETCH_HEAD)" == "$commit" ]]
git -C /tmp/php-security-upstream diff "${commit}^" "$commit" > /tmp/php-security.patch
test -s /tmp/php-security.patch

docker-php-source extract
cd /usr/src/php
patch --batch --forward --fuzz=0 -p1 < /tmp/php-security.patch

# Compare every affected file byte-for-byte with the pinned upstream tree.
while IFS= read -r file; do
    git -C /tmp/php-security-upstream show "${commit}:${file}" > /tmp/php-security-expected
    cmp "$file" /tmp/php-security-expected
done < <(git -C /tmp/php-security-upstream diff --name-only "${commit}^" "$commit")

# Save patched source BEFORE configure adds generated build artifacts.
tar -cJf /usr/src/php.tar.xz --exclude=php/.docker-extracted -C /usr/src php

export CFLAGS="$PHP_CFLAGS" CPPFLAGS="$PHP_CPPFLAGS" LDFLAGS="$PHP_LDFLAGS"
# php-config belongs to the pinned official base image, not application input.
# Its shell-quoted options preserve the original CLI/Apache build configuration.
eval "./configure $configure_options"
make -j"$(nproc)"
make install

# Confirm the new allocator code exists in BOTH executables before recording them.
nm -D --defined-only /usr/local/bin/php > /tmp/php-cli-symbols
nm -D --defined-only "$module" > /tmp/php-apache-symbols
grep -Eq '[[:space:]]zend_mm_safe_error_ex$' /tmp/php-cli-symbols
grep -Eq '[[:space:]]zend_mm_safe_error_ex$' /tmp/php-apache-symbols
php -r 'if (PHP_VERSION !== "7.4.33") { exit(1); }'
apache2ctl -t

mkdir -p "$evidence"
printf '%s\n' "$commit" > "$evidence/upstream-commit"
sha256sum /usr/local/bin/php "$module" > "$evidence/binaries.sha256"
sha256sum /usr/src/php.tar.xz > "$evidence/source.sha256"
cp /tmp/php-security.patch "$evidence/upstream.patch"
rm -rf /tmp/php-security-upstream /tmp/php-security.patch /tmp/php-security-expected
rm -f /tmp/php-cli-symbols /tmp/php-apache-symbols
docker-php-source delete

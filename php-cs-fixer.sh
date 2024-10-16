#!/usr/bin/env bash

ROOT="/"

echo "php-cs-fixer pre commit hook start"

PHP_CS_FIXER="vendor/bin/php-cs-fixer"
HAS_PHP_CS_FIXER=false

if [ -x vendor/bin/php-cs-fixer ]; then
    HAS_PHP_CS_FIXER=true
fi

if $HAS_PHP_CS_FIXER; then
    if git rev-parse --verify HEAD > /dev/null
    then
        against=HEAD
    else
        # Initial commit: diff against an empty tree object
        against=45a31c910ccedb6e9b794062f0fd255fedfccbc5
    fi

    # this is the magic:
    # retrieve all files in staging area that are added, modified or renamed
    # but no deletions etc
    FILES=$(git diff-index --name-only --cached --diff-filter=ACMR $against -- | grep php$)

    if [ "$FILES" == "" ]; then
        exit 0
    fi

    echo -n "PHP CS Fixer "

    for FILE in $FILES
    do
        php $PHP_CS_FIXER fix $FILE --rules=@PSR2,-psr0 --quiet
        echo -n .
    done

    echo " Done"
else
    echo ""
    echo "Please install php-cs-fixer"
fi

echo "php-cs-fixer pre commit hook finish"

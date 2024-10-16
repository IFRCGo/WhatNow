# Note: this needs tabs, not spaces
.PHONY:

git-hooks:
	cp php-cs-fixer.sh .git/hooks/pre-commit && chmod a+x .git/hooks/pre-commit

composer-install:
	docker-compose exec portal composer install

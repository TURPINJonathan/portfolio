## LIST COMMANDS ##
.PHONY: list
list: ## List all make commands
	@grep -E '(^##)|(^[a-zA-Z_-]+:.*?## .*$$)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; \
    { \
        if (NF == 1) { \
            cmd = "tput cols"; \
            cmd | getline width; \
            close(cmd); \
            title = $$1; \
            len = length(title); \
            padding = int((width - len) / 2); \
            printf "\n\033[1;34m\033[44m%" padding "s%s%" padding "s\033[0m\n", "", title, "" \
        } else { \
            printf "\033[36m%-30s\033[0m %s\n", $$1, $$2 \
        } \
    }'

## SYMFONY ##
cc: ## Clear cache
	@sudo rm -rf var/cache
	@php bin/console cache:clear
	@sudo chmod -R 777 var/cache

migration: ## Create migration
	@php bin/console make:migration

migrate: ## Migrate database
	@php bin/console doctrine:migrations:migrate

router: ## Show all routes
	@php bin/console debug:router

test: ## Run PHP Unit
	@composer test

test-unit: ## Run PHP Unit with filter
	@composer test-class

test-no-dox: ## Run PHP Unit without dox
	@composer test-no-dox

fixtures: ## Run fixtures
	@composer load-fixtures

## CODING STANDARDS ##
stan: ## Run PHPStan
	@vendor/bin/phpstan analyse -c phpstan.dist.neon

cs: ## Run PHP CS Fixer
	@vendor/bin/php-cs-fixer fix --dry-run

cs-fix: ## Run PHP CS Fixer with fix
	@vendor/bin/php-cs-fixer fix

add: ## Run PHP CS Fixer with fix
	@$(MAKE) cs-fix
	@if [ $$? -ne 0 ]; then echo "PHP CS Fixer failed"; exit 1; fi
	@$(MAKE) stan
	@if [ $$? -ne 0 ]; then echo "PHPStan failed"; exit 1; fi
	@$(MAKE) test
	@if [ $$? -ne 0 ]; then echo "Tests failed"; exit 1; fi
	@echo "All checks passed and files added to git"
	@git add -A
	@if [ -z "$(filter-out $@,$(MAKECMDGOALS))" ]; then echo "Commit message is required. Use 'make add \"Your commit message\"'"; exit 1; fi
	@git commit -m "$(filter-out $@,$(MAKECMDGOALS))"

%:
	@:
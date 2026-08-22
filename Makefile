#!/bin/bash

# --- Packaging --------------------------------------------------------------
NAME                = secretary
SECRETARY           = com_$(NAME)
SECRETARY_DASHBOARD = mod_$(NAME)_dashboard
# Source of truth for com_secretary (the Joomla 4+/6 line). Mirrors a Joomla
# root (administrator/, components/, media/, plugins/, language/) so the same
# bind-mount layout as the joomla/ tree applies
SOURCE              = ./dev
BUILDDIR            = ./
RELEASE             = ./_package
VERSION            := $(shell date +%Y%m%d)
ZIPFILE_COMPONENT   = $(RELEASE)/$(SECRETARY)_$(VERSION).zip
ZIPFILE_MODULE      = $(RELEASE)/$(SECRETARY_DASHBOARD)_$(VERSION).zip
# Packages are zipped from *inside* the build dir so the manifest ends up at the
# archive root - Joomla only looks for it there or one level down. Excluding
# ".*" is deliberately avoided: it would drop the uploads/.htaccess.
ZIP_EXCLUDES        = -x "*.svn*" -x "*.DS_Store" -x "README.md"

JOOMLA6_VERSION = 6.1.3

# --- Credentials --------------------------------------------------------------
# Defaults live in .env (also read directly by `docker compose`'s built-in
# .env support). Override per-invocation with `make up DEV_DB_PASSWORD=secret`
# rather than editing .env for a one-off change.
# Note: MYSQL_ROOT_PASSWORD and DEV_DB_PASSWORD only take effect on a fresh
# mysql_data volume (MySQL only runs its init env vars once); bootstrap-dev.sh
# does force the 'dev' user's password to match on every run regardless.
include .env
export

# --- Stack ------------------------------------------------------------------
all: up

up:
	docker compose -f deploy/compose.yml up -d
	./deploy/scripts/bootstrap-dev.sh

stop:
	docker compose -f deploy/compose.yml down

# --- Image builds -------------------------------------------------------------
build/base:
	docker build --target base --build-arg JOOMLA_VERSION=$(JOOMLA6_VERSION) -t joomla-base:$(JOOMLA6_VERSION) -f Dockerfile .

build/joomla:
	docker build --target joomla --build-arg JOOMLA_VERSION=$(JOOMLA6_VERSION) -t joomla:$(JOOMLA6_VERSION) -f Dockerfile .

build: build/base build/joomla

# --- Joomla extension packaging --------------------------------------------
# Zips straight from the source tree (com_secretary/, mod_secretary_dashboard/
# at repo root). The uploads cleanup below runs directly against the real
# checkout, not a staged copy - fine today since admin/uploads only ever
# tracks its .htaccess placeholder.
secretary:
	@echo "Deleting all files and directories in 'uploads' except .htaccess..."
	@find $(BUILDDIR)/$(SECRETARY)/admin/uploads -type f ! -name ".htaccess" -delete
	@find $(BUILDDIR)/$(SECRETARY)/admin/uploads -type d -empty -delete || true
	@mkdir -p $(BUILDDIR)/$(SECRETARY)/admin/uploads

	@echo "Creating ZIP package..."
	@mkdir -p $(RELEASE)
	@rm -f $(ZIPFILE_COMPONENT)
	@cd $(BUILDDIR)/$(SECRETARY) && zip -rq $(CURDIR)/$(ZIPFILE_COMPONENT) . $(ZIP_EXCLUDES)
	@echo "Package created at $(ZIPFILE_COMPONENT)"

secretary/module:
	@echo "Creating ZIP package..."
	@mkdir -p $(RELEASE)
	@rm -f $(ZIPFILE_MODULE)
	@cd $(BUILDDIR)/$(SECRETARY_DASHBOARD) && zip -rq $(CURDIR)/$(ZIPFILE_MODULE) . $(ZIP_EXCLUDES)
	@echo "Package created at $(ZIPFILE_MODULE)"

# --- Linting ------------------------------------------------------------------
# Runs via the joomla-base image so contributors don't need PHP/composer
# installed locally. Config lives in phpcs.xml (Joomla coding standard).
lint: build/base
	docker run --rm -v $(CURDIR):/work -w /work joomla-base:$(JOOMLA6_VERSION) \
		sh -c "composer install --quiet && vendor/bin/phpcs"

lint/fix: build/base
	docker run --rm -v $(CURDIR):/work -w /work joomla-base:$(JOOMLA6_VERSION) \
		sh -c "composer install --quiet && vendor/bin/phpcbf -d memory_limit=512M"

# --- Testing --------------------------------------------------------------
# PHPUnit tests for com_secretary's server-side logic. Runs via the
# joomla-base image, same as lint. Config lives in com_secretary/tests/.
test: build/base
	docker run --rm -v $(CURDIR):/work -w /work/com_secretary/tests joomla-base:$(JOOMLA6_VERSION) \
		sh -c "composer install --quiet && vendor/bin/phpunit"

.PHONY: all up stop \
	build/base build/joomla6 build/secretary \
	secretary secretary/module schefa/template \
	lint lint/fix test

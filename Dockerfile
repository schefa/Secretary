# syntax=docker/dockerfile:1
#
# Single multi-stage Dockerfile for every image in this repo.
# Build a specific image by selecting its stage with --target, e.g.
#   docker build --target joomla --build-arg JOOMLA_VERSION=6.1.3 \
#     -t joomla:6.1.3 .
# The Joomla stages have no default version - `make build/joomla` owns it.
#
# Stages / targets:
#   base           Joomla 6 on PHP 8.3, installation/ intact (joomla6 dev site)
#   joomla         base + installer removed (tgc2, sfburg - already-provisioned sites)

# ─────────────────────────────────────────────────────────────────────────────
# joomla6 — Joomla 6 base
# ─────────────────────────────────────────────────────────────────────────────
FROM php:8.3-apache AS base

# Required - no default. The build fails fast without it. Dotted release
# number (e.g. 6.1.3); it doubles as the image tag, see `make build/joomla`.
ARG JOOMLA_VERSION

RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        wget bzip2 libpng-dev libjpeg-dev libfreetype6-dev libzip-dev unzip && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd mysqli calendar zip && \
    rm -rf /var/lib/apt/lists/*

# Downloads use the dashed form of the release (6-1-3), so derive it here rather
# than making callers pass the version twice in two spellings.
RUN : "${JOOMLA_VERSION:?required build arg, e.g. --build-arg JOOMLA_VERSION=6.1.3}" && \
    JOOMLA_MAJOR="${JOOMLA_VERSION%%.*}" && \
    JOOMLA_RELEASE="$(echo "$JOOMLA_VERSION" | tr . -)" && \
    wget -O /tmp/joomla.tar.gz "https://downloads.joomla.org/cms/joomla${JOOMLA_MAJOR}/${JOOMLA_RELEASE}/Joomla_${JOOMLA_RELEASE}-Stable-Full_Package.tar.gz" && \
    rm -rf /var/www/html/* && \
    tar -xzf /tmp/joomla.tar.gz -C /var/www/html && \
    rm /tmp/joomla.tar.gz

RUN a2enmod rewrite

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

COPY ./deploy/joomla-dev/apache.conf /etc/apache2/sites-available/000-default.conf

# German (de-DE) language pack from the official J!German team, matching
# JOOMLA_VERSION - downloaded rather than vendored so the repo doesn't need a
# full Joomla checkout and a fresh clone can build this image standalone.
# J!German tags releases "<joomla-version>v<n>" (v2, v3, ... for hotfixes to
# the same Joomla version) - the API lookup picks the latest such tag rather
# than assuming v1, since that suffix isn't predictable ahead of time.
RUN JOOMLA_LANG_TAG="$(wget -qO- "https://api.github.com/repos/joomlagerman/joomla/tags" | \
        grep -o "\"name\": *\"${JOOMLA_VERSION}v[0-9]*\"" | head -1 | grep -o "${JOOMLA_VERSION}v[0-9]*")" && \
    : "${JOOMLA_LANG_TAG:?no joomlagerman/joomla release tag found for JOOMLA_VERSION=$JOOMLA_VERSION}" && \
    wget -O /tmp/de-DE.zip "https://github.com/joomlagerman/joomla/releases/download/${JOOMLA_LANG_TAG}/de-DE_joomla_lang_full_${JOOMLA_LANG_TAG}.zip" && \
    mkdir -p /var/www/html/administrator/language/de-DE /var/www/html/language/de-DE /var/www/html/api/language/de-DE && \
    unzip -p /tmp/de-DE.zip admin_de-DE.zip > /tmp/admin_de-DE.zip && unzip -q /tmp/admin_de-DE.zip -d /var/www/html/administrator/language/de-DE && \
    unzip -p /tmp/de-DE.zip site_de-DE.zip > /tmp/site_de-DE.zip && unzip -q /tmp/site_de-DE.zip -d /var/www/html/language/de-DE && \
    unzip -p /tmp/de-DE.zip api_de-DE.zip > /tmp/api_de-DE.zip && unzip -q /tmp/api_de-DE.zip -d /var/www/html/api/language/de-DE && \
    rm -f /tmp/de-DE.zip /tmp/admin_de-DE.zip /tmp/site_de-DE.zip /tmp/api_de-DE.zip

# logs/ is not in the Joomla tarball but configuration.php points $log_path
# there, and compose mounts a volume over it. Docker seeds a fresh volume from
# the image, so the directory has to exist here with the right owner - otherwise
# the mount point is created root-owned and Joomla cannot write its log.
RUN mkdir -p /var/www/html/logs && \
    groupadd -r -g 1000 schefa && \
    useradd -r -g schefa -u 1000 -d /var/www/html -s /bin/bash schefa && \
    chown -R schefa:schefa /var/www/html && \
    echo "schefa ALL=(ALL) NOPASSWD: /usr/local/bin/apache2-foreground" >> /etc/sudoers

EXPOSE 80

CMD ["apache2-foreground"]

# ─────────────────────────────────────────────────────────────────────────────
# joomla — joomla6 with the installer stripped out at build time
# ─────────────────────────────────────────────────────────────────────────────
FROM base AS joomla

ARG MPDF_VERSION=v8.2.4
USER schefa
RUN mkdir -p /var/www/html/libraries/mpdf-lib && \
    cd /var/www/html/libraries/mpdf-lib && \
    composer require mpdf/mpdf:${MPDF_VERSION} --no-interaction --optimize-autoloader

RUN rm -rf /var/www/html/installation

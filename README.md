# Secretary ![Static Badge](https://img.shields.io/badge/remastered-559953)


Secretary is a Joomla CMS component that allows you to manage your digital office locally or from anywhere. Create invoices, offers, manage master data of your clients, observe products trends, manage events and project and much more.

If Secretary is useful to you, consider supporting its development:

[![Sponsor](https://img.shields.io/badge/Sponsor-%E2%9D%A4-db61a2?logo=github-sponsors&logoColor=white)](https://github.com/sponsors/schefa)
[![Donate](https://img.shields.io/badge/Donate-PayPal-00457C?logo=paypal&logoColor=white)](https://www.paypal.com/donate/?hosted_button_id=VKN76VER5RSWL)

## Sections
- **Documents (Invoicing & Accounting)**
-- Create bills, invoices, offers, contracts, etc.
- **Contacts**
-- Manage the master data of customers, suppliers, employees, members or other contact types
- **Products**
-- Warehouse & Inventory Management
- **Time and Project Management**
-- Seconds-accurate planning of events, projects, courses, occupated times, warehousing
- **Reports**
-- Visualised Statistics and Charts

## Installation

Download the latest <a href="https://github.com/schefa/Secretary/releases">release</a> and install the zip-file via Joomla Backend

## Documentation

See [docs/index.md](docs/index.md) for how to use each section of the component (Documents, Contacts, Products, Time Management, Reports, Settings).

## Development

The repo ships a Docker Compose stack (Traefik + MySQL + two Joomla sites) for local development.

```
make up      # start the stack
make stop    # stop it
```

This brings up two sites behind Traefik:
- **joomla-dev.localhost** - a working Joomla install with com_secretary and mod_secretary_dashboard already installed (bind-mounted from `com_secretary/` and `mod_secretary_dashboard/`, so edits are picked up live). `make up` bootstraps the database and installs both extensions automatically on first run.
- **joomla.localhost** - a blank Joomla install, useful for testing the installer/upgrade flow with a packaged zip.

Credentials (MySQL and the dev site's Joomla admin account) live in `.env` at the repo root and can be overridden per-invocation, e.g. `make up SECRETARY_DEV_ADMIN_PASSWORD=...`.

The `joomla` and `dev` images are built from the repo's own [Dockerfile](Dockerfile) (`docker compose -f deploy/compose.yml build`, or `make build` / `make build/base` / `make build/joomla`) rather than pulled from a registry, so a fresh clone works standalone.

## Building a Release

```
make secretary          # builds _package/com_secretary_<date>.zip
make secretary/module   # builds _package/mod_secretary_dashboard_<date>.zip
```

Pushing any tag (e.g. `4.0.3`) runs both of these in CI and publishes the resulting zips to the [Releases page](https://github.com/schefa/Secretary/releases) automatically, once lint and tests pass.

## Linting

```
make lint       # check com_secretary and mod_secretary_dashboard against the Joomla coding standard
make lint/fix   # auto-fix what's fixable
```

Runs as a gate in the release workflow (a tag push only builds/publishes if lint passes). Style warnings about inline control structures missing braces are reported but don't fail the build - fixing those means restructuring code, not a safe blanket auto-fix.

## Formatting

Formatting is enforced via the [pre-commit](https://pre-commit.com) framework: Prettier for `.html`/`.js`/`.css`, `phpcbf` (same standard as `make lint`) for `.php`. Install the tool once (`brew install pre-commit`, or `pip install pre-commit`), then wire up the hook:

```
pre-commit install
```

From then on, every `git commit` runs the hooks against your staged files. If a file needs reformatting, the hook rewrites it and aborts the commit so you can review the diff - `git add` the result and commit again. Run `pre-commit run --all-files` to format the whole tree in one pass.

## Testing

```
make test   # PHPUnit tests for com_secretary's server-side logic (tests)
```

Also runs as a gate in the release workflow, alongside lint.

## Support

We use Secretary for our own purpose and make it available free of charge to everyone in hope that it will be useful.
**Support is not included.** You are free to consult the documentation or share your wisdom in the forum.

If you are a developer you are free to submit a pull request with your code fix, as long as there is a clear description of what was not working for you, why and how you fixed it.

## License

Secretary for Joomla! Copyright (c) 2014 - 2026 Fjodor Schäfer, vonfio.de

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but **WITHOUT ANY WARRANTY**; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program. If not, see https://www.gnu.org/licenses/.

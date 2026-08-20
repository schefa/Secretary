# Secretary Documentation

Secretary is a Joomla 6 component for running a small business's back office: invoicing, contacts, products, time and project tracking, and reporting. This document covers how the component is organized and how to use each section, from the admin backend at `/administrator/index.php?option=com_secretary`.

For installing the extension, local development, packaging, and linting, see the [project README](../README.md).

## Contents

- [Concepts](#concepts)
- [Dashboard](#dashboard)
- [Documents](#documents)
- [Contacts](#contacts)
- [Products](#products)
- [Time Management](#time-management)
- [Reports](#reports)
- [Categories (Folders)](#categories-folders)
- [Settings](#settings)
- [PDF generation](#pdf-generation)

## Concepts

- **Business** - the company/entity Secretary is managing. A Joomla install can hold more than one (*Business* in the sidebar), each with its own address, tax details, and documents. The active business is shown in the top bar.
- **Folders (Categories)** - Documents, Contacts, Products, and Time entries are all organized into folders. A folder controls what *kind* of record it holds - e.g. the Documents folders "Invoices", "Quotes", and "Reminders" each produce a different document type from the same underlying table.
- **Contacts, Products, Documents, Time** are the four core record types; most other sections (Reports, Settings) operate across all of them.

## Dashboard

`view=dashboard` - the landing page after login. Gives an overview across the active business (recent documents, open items) before you drill into a specific section.

## Documents

`view=documents&catid=<folder id>` - invoicing and accounting. This is the most-used part of the component.

**Folders**: by default, Invoices, Quotes, and Reminders (a business can add more). Switch between them with the folder dropdown at the top of the list, or view everything at once via *All Documents*.

**List toolbar**:
| Action | What it does |
|---|---|
| New: *\<Folder\>* | Creates a new document in the current folder (e.g. "New: Invoice") |
| Batch | Bulk-edit selected documents (e.g. move folder, change status) via a dialog |
| Update product data | Refreshes selected documents' line items from the current product prices/descriptions |
| Delete | Removes the selected documents |
| Check-In | Releases a document that's stuck "checked out" (e.g. after a crashed edit session) |

**List columns**: Number, per-row quick actions, Date / Folder, Contact, Total, Status.

**Per-row quick actions** (icons next to the document number):
- **Show** - opens the document detail view
- **Preview** - renders the document as PDF in an inline popup
- **Email** - opens a dialog to send the document (as PDF) to the contact

**Status**: each document has a toggleable state shown in the Status column (e.g. *Open* for an unpaid invoice); click it to mark as done/paid.

Search filters by document number, title, or contact name. PDF preview/email requires a PDF library to be installed and selected — see [PDF generation](#pdf-generation); without one, the list shows a warning banner instead of failing silently.

## Contacts

`view=subjects&catid=<group id>` - customers, employees, and suppliers, grouped into Customer/Employee/Supplier categories by default.

**List toolbar**:
| Action | What it does |
|---|---|
| New: Contact | Creates a new contact |
| Import Users as Contacts | Creates contacts from existing Joomla user accounts (name, email, registration date only - re-running it after users change names can create duplicates) |
| Create Document | Bulk-creates a document (invoice/quote/etc.) for each selected contact |
| Batch / Delete / Check-In | Same as Documents |

An A-Z bar filters the list by last-name initial. The list's columns are user-configurable via *Custom columns* (Category, Street, Zip, Location, Country, Phone, Email, Number, ID) - pick what's relevant and save.

Contact detail pages can show a map of the contact's address; this needs a Google Maps API key configured under Settings, or the map is simply omitted.

## Products

`view=products&catid=<category id>` - inventory/catalog items, split into **buy** and **sell** categories (purchasing vs. sales items).

Toolbar: *New: Product*, *Batch*, *Delete*, *Check-In*, and *Create Document* (bulk-add selected products as line items to a new document - pick the target folder, e.g. Invoices/Quotes/Reminders, from the dropdown shown).

## Time Management

`view=times&extension=<events|projects|locations>` - scheduling and time tracking, split into three areas:
- **Events** - single dated appointments
- **Projects** - longer-running tracked work
- **Locations** - where time/events happen

The list view (`section=list`) supports switching to **Week**, **Month**, or **Year** calendar views for the same data.

## Reports

`view=reports` - aggregate views across **Documents**, **Contacts**, and **Products** (revenue over time, top contacts/products, etc.), selected via tabs.

## Categories (Folders)

`view=folders&extension=<documents|subjects|products|...>` - manage the folders/categories that Documents, Contacts, and Products are organized into. Each extension (Documents, Contacts, Products, ...) has its own independent folder tree, reachable via the *Categories* link on that section's list page.

## Settings

`view=item&extension=settings&layout=edit` - component-wide configuration, organized into tabs: **Component**, **Businesses**, **Documents**, **Contacts**, **Products**, **Time Management**, **Folders**, **Locations**, **Templates**, **Reports**.

Also under the sidebar's *Database* group (each backed by the generic `view=items&extension=<name>` list):
- **Data Fields** (`extension=fields`) - custom fields available on records
- **Status** (`extension=status`) - the set of statuses a document/contact can be in
- **Units** (`extension=entities`) - quantity units for products/time (e.g. hours, pieces, kg)
- **Currencies** (`extension=currencies`)
- **Translations** (`view=language`)
- **Files** (`extension=uploads`)
- **Database** (`view=database`) - maintenance tools for the component's own tables

## PDF generation

Document preview, email, and PDF export need a PDF rendering library, configured under **Settings → Component → PDF Library**. Neither ships bundled (they're large and GPL/MIT-mixed), so pick one and install it manually into `libraries/`:

- **mPDF** - install with Composer into `libraries/mpdf-lib` (see [mpdf/mpdf](https://github.com/mpdf/mpdf))
- **Dompdf** - download [dompdf](https://github.com/dompdf/dompdf) into `libraries/dompdf`, then [php-font-lib](https://github.com/PhenX/php-font-lib) into `libraries/dompdf/lib/php-font-lib` and [php-svg-lib](https://github.com/PhenX/php-svg-lib) into `libraries/dompdf/lib/php-svg-lib`

Until one is installed and selected, the Documents list shows a warning banner and PDF-dependent actions (Preview, Email) won't produce output.

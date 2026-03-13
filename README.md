# Laravel SPA CMS

A production-ready CMS boilerplate built with Laravel 12, Inertia.js v2, Vue 3, and Tailwind CSS v4. Clone it, restyle the public frontend, ship.

## Features

- **Page Builder** — 10 drag-and-drop block types (hero, rich text, image, columns, CTA, features, testimonials, checklist, spacer)
- **Blog** — posts with categories, tags, featured images, SEO metadata
- **Services** — service directory with full CRUD
- **Dynamic Forms** — drag-and-drop form builder with email notifications on submission
- **Media Library** — upload, browse, auto-generated thumbnails and previews
- **Navigation** — hierarchical menu builder with drag-and-drop ordering
- **Contact Enquiries** — status tracking, admin notes, CSV export
- **User Management** — role-based access control (admin, editor, author)
- **Settings** — site name, logo, favicon, SEO defaults, social links, schema markup, maintenance mode
- **Revisions** — version history for pages, posts, and services
- **Scheduled Publishing** — set a future publish date, content goes live automatically
- **URL Redirects** — manage 301/302 redirects for SEO
- **Search** — full-text search across all content types
- **XML Sitemap** — auto-generated
- **Dark Mode** — per-user toggle in admin panel
- **Mobile Responsive** — admin and public frontend
- **Scroll Animations** — subtle entrance animations on public pages

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 12, PHP 8.4+ |
| Frontend | Vue 3, Inertia.js v2 |
| Styling | Tailwind CSS v4 |
| Database | MySQL 8.4 |
| Dev Environment | Laravel Sail (Docker) |
| Build | Vite 7 |
| Testing | Pest (220 tests, 1163 assertions) |
| CI | GitHub Actions (Pint, Larastan, vue-tsc, Pest, Vite) |
| Media | Spatie Media Library |
| Auth | Spatie Permission |

## Quick Start

```bash
# Clone the repo
git clone <repo-url> my-project
cd my-project

# Install dependencies
composer install
npm install

# Start Docker containers
vendor/bin/sail up -d

# Setup environment
cp .env.example .env
vendor/bin/sail artisan key:generate

# Run migrations and seed demo content
vendor/bin/sail artisan migrate --seed

# Build frontend assets
vendor/bin/sail npm run build

# Open in browser
vendor/bin/sail open
```

Default admin login is created by the seeder — check `database/seeders/AdminUserSeeder.php` for credentials.

## Development

```bash
# Start dev server with hot reload
vendor/bin/sail npm run dev

# Run tests
vendor/bin/sail artisan test --compact

# Code style (auto-fix)
vendor/bin/sail bin pint

# Static analysis
vendor/bin/sail bin phpstan analyse --memory-limit=512M

# Vue/TS type check
vendor/bin/sail npm run type-check
```

## Architecture

```
Controller -> FormRequest -> Service -> Repository -> Model
```

- **Controllers** handle HTTP, delegate to services
- **Form Requests** validate all input (52 classes)
- **Services** contain business logic (12 services)
- **Repositories** handle data access (12 repositories)
- **Models** define relationships and casts (15 models)

## Page Builder

Pages can use a block-based editor with 10 block types:

| Block | Description |
|-------|-------------|
| Hero | Full-width banner with heading, subheading, CTA |
| Rich Text | TipTap WYSIWYG editor |
| Image | Single image with alt text and caption |
| Two Column | Side-by-side rich text columns |
| Three Column | Three rich text columns |
| Call to Action | CTA banner with button and colour variants |
| Feature Grid | Grid of feature cards with icons |
| Testimonial | Quote with author, role, and avatar |
| Checklist | List of checkable items |
| Spacer | Vertical spacing (small/medium/large) |

Content is stored as a flat JSON array of blocks. Legacy TipTap content is auto-detected and rendered with the old renderer — no migration needed.

## Customising Per Client

1. **Restyle the public frontend** — edit components in `resources/js/Pages/Public/` and `resources/js/Components/Public/`
2. **Update branding** — site name, logo, favicon, colours via admin Settings panel
3. **Add block types** — register in `config/cms.php`, create editor + renderer components
4. **Configure mail** — update `.env` with SMTP credentials for form notifications

## Configuration

All CMS settings live in `config/cms.php`:
- Page templates
- Block types
- Pagination defaults
- Media upload limits and allowed MIME types

## CI Pipeline

GitHub Actions runs on push to `main`/`dev` and PRs to `main`:

1. **Pint** — PHP code style
2. **Larastan** — PHP static analysis (level 4)
3. **vue-tsc** — Vue/TypeScript type checking
4. **Pest** — Full test suite with MySQL
5. **Vite** — Frontend build verification

## License

Proprietary. All rights reserved.

# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Development Commands

Use the `just` command runner for all development tasks:

- `just build` - Build the Docker image
- `just start` - Start Docker services with docker-compose
- `just stop` - Stop Docker services
- `just restart` - Restart Docker services
- `just logs` - View container logs
- `just status` - Check container status
- `just superlint` - Run GitHub Super-Linter for code quality checks
- `just clean` - Remove Docker images
- `just reset` - Full reset: stop, clean, rebuild, and start

For baremetal development:
- PHP 8.2+ with modules: curl, dom, gd, imagick, intl, openssl, sqlite3, zip
- Database migrations: Visit `/endpoints/db/migrate.php` in browser
- Cronjobs must be configured for notifications and updates (see cronjobs file)

## Architecture Overview

Wallos is a self-hosted subscription management application built with:

### Core Technologies
- **Backend**: PHP 8.2 with SQLite3 database
- **Frontend**: Vanilla JavaScript with no frameworks
- **Infrastructure**: Docker + Nginx + PHP-FPM
- **Database**: SQLite with automatic migrations

### Application Structure

**Database Layer**:
- Single SQLite file at `db/wallos.db`
- Migration system in `migrations/` (numbered 000001.php to 000039.php)
- Connection handled by `includes/connect.php` with 5-second busy timeout

**API Architecture**:
- RESTful endpoints in `endpoints/` directory organized by feature
- Each endpoint is a separate PHP file with input validation
- Authentication handled via session management
- JSON responses for AJAX calls

**Frontend Organization**:
- Main pages: `index.php` (dashboard), `subscriptions.php`, `stats.php`, `calendar.php`
- JavaScript modules in `scripts/` (dashboard.js, subscriptions.js, etc.)
- Styling in `styles/` with theme support (light/dark/automatic)
- Multi-language support in `includes/i18n/` and `scripts/i18n/`

**Key Architectural Patterns**:
- Settings system with user preferences and admin settings
- Currency conversion with external API integration (Fixer.io)
- Theme engine supporting custom colors and CSS
- Logo management with automatic web search functionality
- Notification system (email, Discord, Telegram, webhooks, etc.)
- OIDC authentication support

### Data Flow
1. User requests → PHP page → includes header/session checks
2. Database queries via prepared statements (SQLite3)
3. Settings loaded from multiple tables (settings, custom_colors, admin)
4. Frontend JavaScript handles dynamic interactions
5. AJAX calls to `endpoints/` for data operations
6. Cronjobs handle background tasks (notifications, updates, exchange rates)

### Security Features
- Input validation in `includes/inputvalidation.php`
- Session management with user authentication
- File upload restrictions for logos/avatars
- IP validation for external URL requests
- CSRF protection patterns

### Internationalization
- Language files in PHP (`includes/i18n/`) and JavaScript (`scripts/i18n/`)
- 20+ supported languages with fallback to English
- Right-to-left (RTL) text direction support

## Database Schema
The application uses SQLite with these key tables:
- `users` - User accounts and authentication
- `subscriptions` - Subscription data with categories and payment methods
- `settings` - User preferences and configuration
- `currencies` - Supported currencies with exchange rates
- `categories` - Subscription categorization
- `household` - Family/shared subscription management
- `migrations` - Database version tracking

## Testing and Quality
- Use `just superlint` to run comprehensive linting on entire codebase
- Use `just superlint-pr` to run fast linting on only PR changes  
- Use `just superlint-github` to test GitHub Actions linting configuration locally
- Automated linting via GitHub Actions on all PRs and main branch pushes
- No automated test suite - manual testing required
- Demo environment available at demo.wallosapp.com

### Linting Strategy
**Approach**: **Exclude-only** configuration - all linters enabled by default except those explicitly disabled

**Auto-Enabled Linters (Essential Quality Checks):**
- ✅ **PHP_BUILTIN** - Catches syntax errors and fatal issues that prevent code execution
- ✅ **CSS** - CSS syntax and basic style validation  
- ✅ **JAVASCRIPT_ES** - JavaScript syntax and ES standards compliance
- ✅ **YAML** - YAML syntax validation for Docker configs
- ✅ **JSON** - JSON syntax validation for manifests and configs
- ✅ **MARKDOWN** - Documentation quality and formatting
- ✅ **BASH** - Shell script syntax validation
- ✅ **DOCKERFILE_HADOLINT** - Dockerfile best practices
- ✅ **And many others** - Super-Linter enables all relevant linters by default

**Explicitly Disabled Linters (Too Strict for Legacy Codebase):**
- ❌ **VALIDATE_PHP_PHPCS=false** - Coding standards (would require massive formatting changes)
- ❌ **VALIDATE_PHP_PHPSTAN=false** - Static analysis requiring type hints (legacy code uses dynamic patterns)
- ❌ **VALIDATE_PHP_PSALM=false** - Strict type safety (incompatible with SQLite result handling patterns)
- ❌ **VALIDATE_JSCPD=false** - Duplicate code detection (legacy codebase has acceptable duplication)

**Philosophy**: Focus on **functionality and security** over **style preferences**. Catch real bugs and syntax errors while avoiding bikeshedding on formatting in a working legacy codebase.

## Deployment
- Primarily Docker-based deployment via `docker-compose.yaml`
- Supports baremetal installation with web server
- Automatic database creation and migration on startup
- Volume mounts for persistence: `./db` and `./logos`
- Cronjob management handled by container startup script
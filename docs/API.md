# Wallos API Documentation

## Overview

The Wallos API provides programmatic access to subscription management data and settings. All endpoints require authentication via an API key.

## Authentication

All endpoints require an `api_key` parameter. The API key can be passed as either:
- `api_key` (lowercase)
- `apiKey` (camelCase)

## Base URL

```
/api/
```

## Common Response Format

All endpoints return JSON with the following structure:

```json
{
  "success": boolean,
  "title": string,
  "data": object/array,
  "notes": array
}
```

---

## Admin Endpoints

### GET/POST `/api/admin/get_admin_settings.php`

Retrieves admin settings. **Requires admin user (ID=1)**.

**Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| api_key | string | Yes | User API key |

**Example Response:**
```json
{
  "success": true,
  "title": "admin_settings",
  "admin_settings": {
    "registrations_open": 1,
    "max_users": 100,
    "require_email_verification": 1,
    "server_url": "http://example.com",
    "smtp_address": "smtp.example.com",
    "smtp_port": 587,
    "smtp_username": "admin@example.com",
    "smtp_password": "********",
    "from_email": "no-reply@example.com",
    "encryption": "tls",
    "login_disabled": 0,
    "latest_version": "v1.0.0",
    "update_notification": 1
  },
  "notes": []
}
```

### GET/POST `/api/admin/get_oidc_settings.php`

Retrieves OIDC/OAuth settings. **Requires admin user (ID=1)**.

**Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| api_key | string | Yes | User API key |

**Example Response:**
```json
{
  "success": true,
  "title": "oidc_settings",
  "oidc_settings": {
    "name": "Authentik",
    "client_id": "CJMLcyyS94cUMXkitNZuokayArnn23TXxpeUv48E",
    "client_secret": "SzfQBIibfN0gEAgCORrKnGnrYe9yqASWAYUuu1byelVosCHlnoqAdWlMDppblyuByb38Zw78AAlgMmdK6SWpGjOU4IiqaoltkAEh52trcqCB8briP1TqqXZdar4xfhVw",
    "authorization_url": "https://auth.bellamylab.com/application/o/authorize/",
    "token_url": "https://auth.bellamylab.com/application/o/token/",
    "user_info_url": "https://auth.bellamylab.com/application/o/userinfo/",
    "redirect_url": "http://localhost:80/wallos",
    "logout_url": "https://auth.bellamylab.com/application/o/wallos/end-session/",
    "user_identifier_field": "sub",
    "scopes": "openid email profile",
    "auth_style": "auto",
    "created_at": "2025-07-20 20:31:50",
    "updated_at": "2025-07-20 20:31:50",
    "auto_create_user": 0,
    "password_login_disabled": 0
  },
  "notes": []
}
```

### POST `/api/admin/set_disable_password_login.php`

Enable or disable password login. **Requires admin user (ID=1)**.

**Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| api_key | string | Yes | User API key |
| disable | string | Yes | '1' to disable, '0' to enable |

**Example Response:**
```json
{
  "success": true,
  "title": "Updated",
  "message": "Password login has been disabled."
}
```

---

## Categories

### GET/POST `/api/categories/get_categories.php`

Retrieves user's subscription categories.

**Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| api_key | string | Yes | User API key |

**Example Response:**
```json
{
  "success": true,
  "title": "categories",
  "categories": [
    {
      "id": 1,
      "name": "General",
      "order": 1,
      "in_use": true
    },
    {
      "id": 2,
      "name": "Entertainment",
      "order": 2,
      "in_use": true
    },
    {
      "id": 3,
      "name": "Music",
      "order": 3,
      "in_use": false
    }
  ],
  "notes": []
}
```

---

## Currencies

### GET/POST `/api/currencies/get_currencies.php`

Retrieves user's currencies and main currency.

**Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| api_key | string | Yes | User API key |

**Example Response:**
```json
{
  "success": true,
  "title": "currencies",
  "main_currency": 3,
  "currencies": [
    {
      "id": 1,
      "name": "US Dollar",
      "symbol": "$",
      "code": "USD",
      "rate": "1.1000",
      "in_use": true
    },
    {
      "id": 2,
      "name": "Japanese Yen",
      "symbol": "¥",
      "code": "JPY",
      "rate": "150.0000",
      "in_use": false
    },
    {
      "id": 3,
      "name": "Euro",
      "symbol": "€",
      "code": "EUR",
      "rate": "1.0000",
      "in_use": true
    }
  ],
  "notes": []
}
```

---

## Fixer API Settings

### GET/POST `/api/fixer/get_fixer.php`

Retrieves currency exchange API settings.

**Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| api_key | string | Yes | User API key |

**Example Response:**
```json
{
  "success": true,
  "title": "fixer",
  "fixer": {
    "api_key": "********",
    "provider": 0,
    "provider_name": "Fixer.io"
  },
  "notes": []
}
```

---

## Household Members

### GET/POST `/api/household/get_household.php`

Retrieves household/family members.

**Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| api_key | string | Yes | User API key |

**Example Response:**
```json
{
  "success": true,
  "title": "household",
  "household": [
    {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "in_use": true
    },
    {
      "id": 2,
      "name": "Jane Doe",
      "email": "jane@example.com",
      "in_use": false
    }
  ],
  "notes": []
}
```

---

## Notification Settings

### GET/POST `/api/notifications/get_notification_settings.php`

Retrieves notification settings for all enabled methods.

**Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| api_key | string | Yes | User API key |

**Example Response:**
```json
{
  "success": true,
  "title": "notification_settings",
  "notification_settings": {
    "email_notifications": {
      "enabled": 1,
      "smtp_address": "smtp.example.com",
      "smtp_port": 587,
      "smtp_username": "user@example.com",
      "smtp_password": "********",
      "from_email": "no-reply@example.com",
      "encryption": "tls",
      "other_emails": "other@example.com"
    },
    "discord_notifications": {
      "enabled": 1,
      "webhook": "https://discord.com/api/webhooks/..."
    },
    "telegram_notifications": {
      "enabled": 0,
      "bot_token": "********",
      "chat_id": "123456789"
    },
    "gotify_notifications": {
      "enabled": 0,
      "url": "https://gotify.example.com",
      "token": "********"
    },
    "ntfy_notifications": {
      "enabled": 0,
      "host": "http://notify.example.com",
      "topic": "example_topic",
      "headers": "********"
    },
    "pushover_notifications": {
      "enabled": 0,
      "token": "********",
      "user": "user123"
    },
    "webhook_notifications": {
      "enabled": 0,
      "url": "https://webhook.example.com",
      "headers": "********"
    }
  },
  "notes": []
}
```

---

## Payment Methods

### GET/POST `/api/payment_methods/get_payment_methods.php`

Retrieves user's payment methods.

**Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| api_key | string | Yes | User API key |

**Example Response:**
```json
{
  "success": true,
  "title": "payment_methods",
  "payment_methods": [
    {
      "id": 1,
      "name": "PayPal",
      "icon": "images/uploads/icons/paypal.png",
      "enabled": 1,
      "order": 1,
      "in_use": true
    },
    {
      "id": 2,
      "name": "Credit Card",
      "icon": "images/uploads/icons/creditcard.png",
      "enabled": 1,
      "order": 2,
      "in_use": true
    },
    {
      "id": 3,
      "name": "Bank Transfer",
      "icon": "images/uploads/icons/banktransfer.png",
      "enabled": 1,
      "order": 3,
      "in_use": false
    }
  ],
  "notes": []
}
```

---

## User Settings

### GET/POST `/api/settings/get_settings.php`

Retrieves user display and theme settings.

**Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| api_key | string | Yes | User API key |

**Example Response:**
```json
{
  "success": true,
  "title": "settings",
  "settings": {
    "dark_theme": 0,
    "monthly_price": 1,
    "convert_currency": 1,
    "remove_background": 1,
    "color_theme": "red",
    "hide_disabled": 0,
    "disabled_to_bottom": 1,
    "show_original_price": 0,
    "mobile_nav": 1,
    "custom_colors": {
      "main_color": "#FF0000",
      "accent_color": "#00FF00",
      "hover_color": "#0000FF"
    },
    "custom_css": {
      "css": ".custom-class { color: red; }"
    }
  },
  "notes": []
}
```

---

## Application Status

### GET/POST `/api/status/version.php`

Retrieves application version information.

**Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| api_key | string | Yes | User API key |

**Example Response:**
```json
{
  "success": true,
  "title": "version",
  "version": "v2.42.1",
  "version_number": "2.42.1",
  "notes": []
}
```

---

## Subscriptions

### GET/POST `/api/subscriptions/get_subscriptions.php`

Retrieves user's subscriptions with optional filtering and sorting.

**Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| api_key | string | Yes | User API key |
| member | string | No | Comma-separated member IDs to filter |
| category | integer | No | Category ID to filter |
| payment_method | integer | No | Payment method ID to filter |
| state | integer | No | 0 for active, 1 for inactive |
| disabled_to_bottom | boolean | No | Sort inactive to bottom (default: false) |
| sort | string | No | Sort by: 'name', 'id', 'next_payment', 'price', 'payer_user_id', 'category_id', 'payment_method_id', 'inactive', 'alphanumeric' (default: 'next_payment') |
| convert_currency | boolean | No | Convert to main currency (default: false) |

**Example Response:**
```json
{
  "success": true,
  "title": "subscriptions",
  "subscriptions": [
    {
      "id": 1,
      "name": "Netflix",
      "logo": "netflix.png",
      "price": 15.99,
      "currency_id": 1,
      "start_date": "2024-01-01",
      "next_payment": "2025-02-01",
      "cycle": 3,
      "frequency": 1,
      "auto_renew": 1,
      "notes": "Family plan",
      "payment_method_id": 1,
      "payer_user_id": 1,
      "category_id": 2,
      "notify": 1,
      "url": "https://netflix.com",
      "inactive": 0,
      "notify_days_before": 3,
      "user_id": 1,
      "cancellation_date": "",
      "category_name": "Entertainment",
      "payer_user_name": "John Doe",
      "payment_method_name": "Credit Card"
    },
    {
      "id": 2,
      "name": "Spotify",
      "logo": "spotify.png",
      "price": 9.99,
      "currency_id": 1,
      "start_date": "2024-06-15",
      "next_payment": "2025-01-15",
      "cycle": 3,
      "frequency": 1,
      "auto_renew": 0,
      "notes": "",
      "payment_method_id": 1,
      "payer_user_id": 2,
      "category_id": 3,
      "notify": 1,
      "url": "https://spotify.com",
      "inactive": 1,
      "notify_days_before": null,
      "user_id": 1,
      "cancellation_date": "2025-01-15",
      "category_name": "Music",
      "payer_user_name": "Jane Doe",
      "payment_method_name": "PayPal",
      "replacement_subscription_id": 1
    }
  ],
  "notes": []
}
```

**Cycle Values:**
- 1 = Days
- 2 = Weeks  
- 3 = Months
- 4 = Years

### GET/POST `/api/subscriptions/get_monthly_cost.php`

Calculates total subscription cost for a specific month.

**Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| api_key | string | Yes | User API key |
| month | integer | Yes | Month (1-12) |
| year | integer | Yes | Year (e.g., 2025) |

**Example Response:**
```json
{
  "success": true,
  "title": "March 2025",
  "monthly_cost": "120.24",
  "localized_monthly_cost": "€120.24",
  "currency_code": "EUR",
  "currency_symbol": "€",
  "notes": []
}
```

**Notes:**
- Automatically converts multiple currencies to main currency if exchange rates are available
- Returns warning in notes if using multiple currencies without exchange rates

### GET/POST `/api/subscriptions/get_ical_feed.php`

Downloads an iCalendar (.ics) file with active subscriptions.

**Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| api_key | string | Yes | User API key |
| convert_currency | boolean | No | Convert to main currency (default: false) |

**Response:**
- Content-Type: `text/calendar`
- Content-Disposition: `attachment; filename="subscriptions.ics"`
- Returns VCALENDAR format with subscription payment reminders

**Example iCal Entry:**
```
BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Wallos//iCalendar//EN
NAME:Wallos
X-WR-CALNAME:Wallos
BEGIN:VEVENT
UID:abc123
SUMMARY:Netflix
DESCRIPTION:Price: $15.99\nCategory: Entertainment\nPayment Method: Credit Card\nPayer: John Doe\nNotes: Family plan
DTSTART:20250201
DTEND:20250201
LOCATION:https://netflix.com
STATUS:CONFIRMED
TRANSP:OPAQUE
BEGIN:VALARM
ACTION:DISPLAY
DESCRIPTION:Reminder
TRIGGER:-P3D
END:VALARM
END:VEVENT
END:VCALENDAR
```

---

## User Profile

### GET/POST `/api/users/get_user.php`

Retrieves authenticated user's profile information.

**Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| api_key | string | Yes | User API key |

**Example Response:**
```json
{
  "success": true,
  "title": "user",
  "user": {
    "id": 1,
    "username": "johndoe",
    "email": "john.doe@example.com",
    "password": "********",
    "main_currency": 1,
    "avatar": "images/uploads/logos/avatars/default-avatar.jpg",
    "language": "en",
    "budget": 100,
    "totp_enabled": 0,
    "api_key": "********"
  },
  "notes": []
}
```

---

## Error Responses

All endpoints return standardized error responses:

### Missing Parameters
```json
{
  "success": false,
  "title": "Missing parameters"
}
```

### Invalid API Key
```json
{
  "success": false,
  "title": "Invalid API key"
}
```

### Invalid Request Method
```json
{
  "success": false,
  "title": "Invalid request method"
}
```

### Unauthorized (Admin endpoints)
```json
{
  "success": false,
  "title": "Invalid user"
}
```

---

## Notes

- All endpoints support both GET and POST methods unless specified otherwise
- Passwords and sensitive tokens are masked with "********" in responses
- The `in_use` field indicates whether an item (category, currency, payment method, household member) is currently referenced by any subscription
- Currency conversion requires exchange rates to be configured via Fixer API
- Admin endpoints require the authenticated user to have ID=1 (admin user)

---

## Interactive Endpoints

The following endpoints are used by the web interface for interactive operations. Unlike the API endpoints above, these require session-based authentication and return various response formats.

### Subscriptions Management

#### POST `/endpoints/subscription/add.php`

Creates a new subscription or updates an existing one. Handles file uploads for logos.

**Authentication**: Session-based (web interface)

**Parameters** (form data):
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | integer | No | Subscription ID (for editing) |
| name | string | Yes | Subscription name |
| price | float | Yes | Subscription price |
| currency_id | integer | Yes | Currency ID |
| frequency | integer | Yes | Payment frequency |
| cycle | integer | Yes | Payment cycle (1=days, 2=weeks, 3=months, 4=years) |
| next_payment | date | Yes | Next payment date |
| auto_renew | boolean | No | Auto-renewal enabled |
| start_date | date | Yes | Start date |
| payment_method_id | integer | Yes | Payment method ID |
| payer_user_id | integer | Yes | Payer user ID |
| category_id | integer | Yes | Category ID |
| notes | string | No | Notes |
| url | string | No | Subscription URL |
| logo-url | string | No | Logo URL (alternative to file upload) |
| logo | file | No | Logo file upload |
| notifications | boolean | No | Enable notifications |
| notify_days_before | integer | No | Days before to notify |
| inactive | boolean | No | Mark as inactive |
| cancellation_date | date | No | Cancellation date |
| replacement_subscription_id | integer | No | Replacement subscription ID |

**Example Response** (success):
```json
{
  "success": true,
  "message": "Subscription added successfully"
}
```

**Example Response** (error):
```json
{
  "success": false,
  "errorMessage": "Invalid URL format."
}
```

#### DELETE `/endpoints/subscription/delete.php`

Deletes a subscription by ID.

**Authentication**: Session-based (web interface)

**Parameters** (query string):
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | integer | Yes | Subscription ID to delete |

**Response**: HTTP 204 (success) or HTTP 500 (error)

### Categories Management

#### GET/POST `/endpoints/categories/category.php`

Manages subscription categories (add, edit, delete operations).

**Authentication**: Session-based (web interface)

**Add Category** (`?action=add`):
- Creates a new category with default name "Category"
- Response: `{"success": true, "categoryId": 123}` or error

**Edit Category** (`?action=edit&categoryId=X&name=Y`):
- Updates category name
- Response: `{"success": true, "message": "Category saved"}` or error

**Delete Category** (`?action=delete&categoryId=X`):
- Deletes category if not in use
- Cannot delete category ID 1 (default category)
- Response: `{"success": true}` or error if category is in use

### Payment Methods Management

#### GET `/endpoints/payments/payment.php`

Enables or disables a payment method.

**Authentication**: Session-based (web interface)

**Parameters** (query string):
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| paymentId | integer | Yes | Payment method ID |
| enabled | boolean | Yes | Enable (1) or disable (0) |

**Example Response**:
```json
{
  "success": true,
  "message": "enabled"
}
```

**Note**: Cannot disable payment methods that are currently in use by subscriptions.

### User Profile Management

#### POST `/endpoints/user/save_user.php`

Updates user profile information and handles avatar uploads.

**Authentication**: Session-based (web interface)

**Parameters** (form data):
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| username | string | Yes | Username |
| email | string | Yes | Email address |
| avatar | file | No | Avatar image upload |
| main_currency | integer | Yes | Main currency ID |
| language | string | Yes | Language code |
| budget | float | No | Monthly budget |

**Features**:
- Automatically resizes and optimizes uploaded avatars
- Updates currency exchange rates when main currency changes
- Validates email format and username uniqueness

### Notification Settings

#### POST `/endpoints/notifications/saveemailnotifications.php`

Configures email notification settings.

**Authentication**: Session-based (web interface)

**Parameters** (JSON body):
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| enabled | boolean | Yes | Enable email notifications |
| smtpaddress | string | Yes* | SMTP server address |
| smtpport | integer | Yes* | SMTP port |
| encryption | string | No | Encryption type (default: "tls") |
| smtpusername | string | No | SMTP username |
| smtppassword | string | No | SMTP password |
| fromemail | string | No | From email address |
| otheremails | string | No | Additional recipient emails |

*Required when enabled = true

**Example Request**:
```json
{
  "enabled": true,
  "smtpaddress": "smtp.gmail.com",
  "smtpport": 587,
  "encryption": "tls",
  "smtpusername": "user@gmail.com",
  "smtppassword": "password",
  "fromemail": "notifications@example.com"
}
```

**Example Response**:
```json
{
  "success": true,
  "message": "Email notifications updated successfully"
}
```

#### POST `/endpoints/notifications/savediscordnotifications.php`

Configures Discord webhook notifications.

**Authentication**: Session-based (web interface)

**Parameters** (JSON body):
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| enabled | boolean | Yes | Enable Discord notifications |
| webhook | string | Yes* | Discord webhook URL |

*Required when enabled = true

#### POST `/endpoints/notifications/savetelegramnotifications.php`

Configures Telegram bot notifications.

**Authentication**: Session-based (web interface)

**Parameters** (JSON body):
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| enabled | boolean | Yes | Enable Telegram notifications |
| bot_token | string | Yes* | Telegram bot token |
| chat_id | string | Yes* | Chat ID |

*Required when enabled = true

#### POST `/endpoints/notifications/savegotifynotifications.php`

Configures Gotify push notifications.

**Authentication**: Session-based (web interface)

**Parameters** (JSON body):
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| enabled | boolean | Yes | Enable Gotify notifications |
| url | string | Yes* | Gotify server URL |
| token | string | Yes* | Application token |

*Required when enabled = true

#### POST `/endpoints/notifications/savepushovernotifications.php`

Configures Pushover notifications.

**Authentication**: Session-based (web interface)

**Parameters** (JSON body):
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| enabled | boolean | Yes | Enable Pushover notifications |
| token | string | Yes* | Application token |
| user | string | Yes* | User key |

*Required when enabled = true

#### POST `/endpoints/notifications/savewebhooknotifications.php`

Configures custom webhook notifications.

**Authentication**: Session-based (web interface)

**Parameters** (JSON body):
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| enabled | boolean | Yes | Enable webhook notifications |
| url | string | Yes* | Webhook URL |
| headers | string | No | Custom headers (JSON format) |

*Required when enabled = true

### Currency Management

#### POST `/endpoints/currency/fixer_api_key.php`

Updates currency exchange API settings.

**Authentication**: Session-based (web interface)

**Parameters** (form data):
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| api_key | string | Yes | API key |
| provider | integer | Yes | Provider (0=Fixer.io, 1=APILayer) |

#### POST `/endpoints/currency/update_exchange.php`

Manually updates exchange rates for all currencies.

**Authentication**: Session-based (web interface)

**Response**: Redirects to referrer page after updating rates.

### Admin Endpoints (Session-based)

#### POST `/endpoints/admin/savesmtpsettings.php`

Updates global SMTP settings (admin only).

**Authentication**: Session-based (admin user)

**Parameters** (form data):
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| smtp_address | string | Yes | SMTP server |
| smtp_port | integer | Yes | SMTP port |
| smtp_username | string | Yes | Username |
| smtp_password | string | Yes | Password |
| from_email | string | Yes | From email |
| encryption | string | Yes | Encryption type |

#### POST `/endpoints/admin/saveoidcsettings.php`

Configures OIDC/OAuth authentication settings (admin only).

**Authentication**: Session-based (admin user)

**Parameters** (form data):
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| name | string | Yes | Provider name |
| client_id | string | Yes | OAuth client ID |
| client_secret | string | Yes | OAuth client secret |
| authorization_url | string | Yes | Authorization endpoint |
| token_url | string | Yes | Token endpoint |
| user_info_url | string | Yes | User info endpoint |
| redirect_url | string | Yes | Redirect URL |
| logout_url | string | No | Logout URL |
| scopes | string | Yes | OAuth scopes |
| user_identifier_field | string | Yes | User ID field name |
| auto_create_user | boolean | No | Auto-create users |

---

## Key Differences

### API Endpoints vs Interactive Endpoints

**API Endpoints (`/api/`):**
- Designed for programmatic access
- Require API key authentication
- Return standardized JSON responses
- Read-only operations (GET data)
- Consistent response format with `success`, `title`, `data`, `notes`

**Interactive Endpoints (`/endpoints/`):**
- Designed for web interface interaction
- Require session-based authentication
- Handle file uploads and complex form data
- Support create, update, delete operations
- Variable response formats depending on operation
- Include business logic like logo processing, email validation

**Common Features:**
- Input validation and sanitization
- User-specific data isolation
- Internationalization support
- Error handling with descriptive messages
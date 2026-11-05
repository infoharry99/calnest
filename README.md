# CalNest Backend & API

Official Laravel REST backend implementation for the **CalNest Flutter App** (`com.calnest.calnest`, version 1.0.0) based on the **CalNest API Reference** specification.

---

## 🚀 Features

- **Full Document Coverage**: Implements all 27 remote calls and services across all 7 sections of the CalNest specification:
  1. **Google OAuth & Authorization**: Silent sign-in and token exchange with calendar, tasks, and contacts scopes.
  2. **Google Calendar API (`calendar/v3`)**: Full CRUD (`calendarList`, `events`, date-range filtering, recurrence handling, singleEvents).
  3. **Google Tasks API (`tasks/v1`)**: Task lists, tasks querying, creation, updating status (`needsAction`, `completed`), deletion.
  4. **Google People API (`people/v1`)**: Reads contacts that have a birthday, pagination with `nextPageToken`, photo, and phone formatting.
  5. **Microsoft Graph API (`graph.microsoft.com/v1.0`)**: MSAL OAuth 2.0 (`/consumers/oauth2/v2.0/token`), user profile (`/me`), Outlook calendars, primary and secondary `calendarView` with `$top` and `$select`, Outlook events CRUD, and Microsoft To-Do lists and tasks CRUD.
  6. **Device Links (WhatsApp & SMS)**: Birthday wish link generators for `https://wa.me/{phone}?text={message}` and `sms:{phone}?body={message}`.
  7. **CalNest Local Storage & Cloud Sync**: Backend endpoints for persisting user preferences, manual birthdays, selected calendars, and reminder settings (described in section 7 of the document).
- **Dual Routing Architecture**:
  - Direct root paths for zero-config drop-in compatibility (e.g. `http://localhost:8000/calendar/v3/...`, `http://localhost:8000/v1.0/me`, `http://localhost:8000/consumers/oauth2/v2.0/...`).
  - Standard REST prefix paths (e.g. `http://localhost:8000/api/google/...`, `http://localhost:8000/api/microsoft/...`, `http://localhost:8000/api/calnest/...`).
- **Pre-seeded with Document Sample Data**:
  - Real database seeders populating `user@gmail.com`, "Team standup", "Priya's birthday", "Send invoice", "Ravi Patel", "Dentist", "Project review", "Pay rent", etc.
- **Interactive Web API Console**:
  - Live API testing UI accessible directly in the browser at `http://127.0.0.1:8000/`.
- **Postman Collection Included**:
  - Importable Postman collection file: `calnest_api_collection.json`.

---

## 🛠 Tech Stack

- **Framework**: Laravel 12 / PHP 8.2+
- **Database**: SQLite (Zero configuration out-of-the-box, can be switched to MySQL or PostgreSQL in `.env`)
- **Authentication**: Laravel Sanctum API Tokens
- **Testing**: PHPUnit / Pest Feature & Unit tests

---

## 📦 Quick Start & Setup

### 1. Requirements
- PHP >= 8.2 (with pdo_sqlite, curl, mbstring extensions)
- Composer

### 2. Installation & Database Setup
Clone or navigate to the project directory:
```bash
cd "d:\All Project\Calnest"
```

If needed, copy environment file and generate key:
```bash
php -r "file_exists('.env') || copy('.env.example', '.env');"
php artisan key:generate
```

Run migrations and seed the document's sample records:
```bash
php artisan migrate:fresh --seed
```

### 3. Start the Development Server
```bash
php artisan serve
```
By default, the server runs at **`http://127.0.0.1:8000`**.

Visit **`http://127.0.0.1:8000`** in your browser to view the interactive CalNest API Reference and Test Console!

---

## 🧪 Running Automated Tests

Run the full automated test suite covering all 27 API operations:
```bash
php artisan test
```
All 14 feature test scenarios (72 assertions) will run against the test database.

---

## 📋 API Endpoints Summary

### 1. Authentication & OAuth
| Method | Endpoint | Description |
|---|---|---|
| `POST` | `/api/oauth/google/token` | Exchange Google token with calendar, tasks, contacts scopes |
| `GET` | `/consumers/oauth2/v2.0/authorize` | Microsoft MSAL authorize parameters info |
| `POST` | `/consumers/oauth2/v2.0/token` | Microsoft MSAL token exchange (`authorization_code`, `refresh_token`) |

### 2. Google Calendar API (`calendar/v3`)
| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/calendar/v3/users/me/calendarList` | List user's calendars |
| `GET` | `/calendar/v3/calendars/{calendarId}/events` | Load events for visible date range |
| `POST` | `/calendar/v3/calendars/{calendarId}/events` | Create a calendar event |
| `PATCH`| `/calendar/v3/calendars/{calendarId}/events/{eventId}` | Edit an existing event |
| `DELETE`| `/calendar/v3/calendars/{calendarId}/events/{eventId}` | Delete an event (204 No Content) |

### 3. Google Tasks API (`tasks/v1`)
| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/tasks/v1/users/@me/lists` | List task lists |
| `GET` | `/tasks/v1/lists/{tasklistId}/tasks` | Load tasks in a list |
| `POST` | `/tasks/v1/lists/{tasklistId}/tasks` | Create a task |
| `PATCH`| `/tasks/v1/lists/{tasklistId}/tasks/{taskId}` | Edit a task / mark complete (`completed`, `needsAction`) |
| `DELETE`| `/tasks/v1/lists/{tasklistId}/tasks/{taskId}` | Delete a task (204 No Content) |

### 4. Google People API (`people/v1`)
| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/people/v1/people/me/connections` | Reads contacts with birthdays for the Birthdays screen |

### 5. Microsoft Graph API (`v1.0`)
| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/v1.0/me` | Read signed-in user's profile |
| `GET` | `/v1.0/me/calendars` | List Outlook calendars |
| `GET` | `/v1.0/me/calendarView` | Primary Outlook calendarView |
| `GET` | `/v1.0/me/calendars/{calendarId}/calendarView` | Secondary calendarView |
| `POST` | `/v1.0/me/calendars/{calendarId}/events` | Create an Outlook event (201 Created) |
| `PATCH`| `/v1.0/me/calendars/{calendarId}/events/{eventId}` | Edit an Outlook event |
| `DELETE`| `/v1.0/me/calendars/{calendarId}/events/{eventId}` | Delete an Outlook event (204 No Content) |
| `GET` | `/v1.0/me/todo/lists` | List Microsoft To Do lists |
| `GET` | `/v1.0/me/todo/lists/{listId}/tasks` | Load tasks in a list |
| `POST` | `/v1.0/me/todo/lists/{listId}/tasks` | Create a To Do task (201 Created) |
| `PATCH`| `/v1.0/me/todo/lists/{listId}/tasks/{taskId}` | Edit a To Do task / mark complete |
| `DELETE`| `/v1.0/me/todo/lists/{listId}/tasks/{taskId}` | Delete a To Do task (204 No Content) |

### 6. Device Links (WhatsApp & SMS)
| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/api/device-links/whatsapp?phone={phone}&message={text}` | Generates WhatsApp deep link `https://wa.me/{phone}?text=...` |
| `GET` | `/api/device-links/sms?phone={phone}&message={text}` | Generates SMS deep link `sms:{phone}?body=...` |

### 7. CalNest Backend (SharedPreferences Cloud Sync)
| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/api/calnest/overview` | Health check & summary counts |
| `GET/POST` | `/api/calnest/preferences` | Get or update app preferences |
| `GET/POST` | `/api/calnest/manual-birthdays` | Get or create manual birthdays |
| `DELETE` | `/api/calnest/manual-birthdays/{id}` | Delete manual birthday |
| `GET/POST` | `/api/calnest/selected-calendars` | Get or update active selected calendars |
| `GET/POST` | `/api/calnest/reminder-settings` | Get or update reminder notifications settings |

---

## 📱 Connecting to the Flutter App

To connect the CalNest Flutter app (`com.calnest.calnest`) to this backend:

1. Update the base URL constants in the Flutter service classes (e.g., `google_auth_service.dart` and `microsoft_auth_service.dart`) from Google/Microsoft domains to your local or deployed Laravel host:
   ```dart
   // For local development on Android emulator:
   static const String baseUrl = 'http://10.0.2.2:8000';

   // For iOS simulator or web:
   static const String baseUrl = 'http://127.0.0.1:8000';
   ```
2. The routes are mapped with both standard Google/Microsoft paths (`/calendar/v3/...`, `/v1.0/...`, etc.) and `/api/...` prefix so no path remodeling is required in the mobile client.

# calnest

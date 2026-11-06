# CALNEST · BACKEND REQUIREMENTS (V2 - Corrected & Complete)
## APIs for the CalNest Flutter App (Server-Backed Architecture)

**Base URL**: `https://calnest.arinine.com` (or local `http://127.0.0.1:8000`)  
**Prefix**: All endpoints are routed under `/api/calnest/...`  
**Authentication**: Laravel Sanctum (`Authorization: Bearer <token>`)  
**Format**: `Content-Type: application/json` & `Accept: application/json`  

---

## 📌 Architecture Overview & State
* Google, Microsoft, and Yahoo OAuth accounts have been removed.
* The Flutter app uses its own native email + password authentication and Sanctum Bearer tokens.
* All data (Events, Tasks, Birthdays, Preferences, and Reminder Settings) is persisted on the server, scoped to `$request->user()->id`.

---

## 📋 Complete Endpoint Summary

| # | Method | Path | Purpose | Auth | Status |
|---|---|---|---|---|---|
| 1 | `POST` | `/api/calnest/register` | Create account & get token | Public | MUST |
| 2 | `POST` | `/api/calnest/login` | Sign in & get token | Public | MUST |
| 3 | `POST` | `/api/calnest/logout` | Revoke current token | Bearer | MUST |
| 4 | `GET` | `/api/calnest/me` | Current user profile | Bearer | MUST |
| 5 | `PUT` | `/api/calnest/me` | Update user profile | Bearer | SHOULD |
| 6 | `DELETE` | `/api/calnest/me` | **Delete user account & all data** *(App Store mandatory)* | Bearer | MUST |
| 7 | `POST` | `/api/calnest/forgot-password` | Send 6-digit reset code | Public | OPTIONAL |
| 8 | `POST` | `/api/calnest/reset-password` | Verify code & set new password | Public | OPTIONAL |
| 9 | `POST` | `/api/calnest/verify-email` | Confirm 6-digit email code | Public | OPTIONAL |
| 10 | `GET` | `/api/calnest/events` | List events (with `from` and `to` range) | Bearer | MUST |
| 11 | `POST` | `/api/calnest/events` | Create new event | Bearer | MUST |
| 12 | `PUT/PATCH` | `/api/calnest/events/{id}` | Update event | Bearer | MUST |
| 13 | `DELETE` | `/api/calnest/events/{id}` | Delete event | Bearer | MUST |
| 14 | `GET` | `/api/calnest/tasks` | List all tasks (open and completed) | Bearer | MUST |
| 15 | `POST` | `/api/calnest/tasks` | Create task | Bearer | MUST |
| 16 | `PUT/PATCH` | `/api/calnest/tasks/{id}` | Update task / mark complete (`{"completed": true}`) | Bearer | MUST |
| 17 | `DELETE` | `/api/calnest/tasks/{id}` | Delete task | Bearer | MUST |
| 18 | `GET` | `/api/calnest/manual-birthdays` | List all birthdays for user | Bearer | MUST |
| 19 | `POST` | `/api/calnest/manual-birthdays` | Add birthday | Bearer | MUST |
| 20 | `PUT/PATCH` | `/api/calnest/manual-birthdays/{id}` | Update birthday | Bearer | SHOULD |
| 21 | `DELETE` | `/api/calnest/manual-birthdays/{id}` | Delete birthday | Bearer | SHOULD |
| 22 | `GET` | `/api/calnest/preferences` | Read user theme and calendar preferences | Bearer | MUST |
| 23 | `POST` | `/api/calnest/preferences` | Save user preferences | Bearer | MUST |
| 24 | `GET` | `/api/calnest/reminder-settings` | Read notification & reminder settings | Bearer | MUST |
| 25 | `POST` | `/api/calnest/reminder-settings` | Save notification & reminder settings | Bearer | MUST |
| 26 | `GET` | `/api/calnest/overview` | Health check & summary counts | Public | MUST |

---

## 🛠 API Conventions & Rules

1. **Headers**:
   ```http
   Content-Type: application/json
   Accept: application/json
   Authorization: Bearer <token>
   ```
2. **Timestamps & Dates**:
   * Timestamps are ISO 8601 with offset: `2026-11-06T10:00:00+05:30`.
   * Date-only values are `YYYY-MM-DD`: `2026-11-06`.
   * For unknown birth years, use year 1900 convention: `1900-10-18`.
3. **HTTP Status Codes**:
   * `200 OK`: Successful retrieval, update, or deletion.
   * `201 Created`: Successful creation.
   * `401 Unauthorized`: Missing, expired, or invalid token; wrong login credentials.
   * `404 Not Found`: Record not found or belongs to another user.
   * `422 Unprocessable Entity`: Validation failure. Format:
     ```json
     {
       "message": "The given data was invalid.",
       "errors": { "field": ["Error message..."] }
     }
     ```
4. **All Deletions**: Return HTTP 200 with `{"status": "deleted"}`.

---

## 1. Authentication & Account Management

### 1.1 Register
`POST /api/calnest/register`
* **Request Body**:
  ```json
  {
    "name": "Ganesh Patel",
    "email": "ganesh@example.com",
    "password": "secret123"
  }
  ```
* **Validation**: `name` (required, string), `email` (required, email, unique:users), `password` (required, min 6).
* **Response (201 Created)**:
  ```json
  {
    "id": 12,
    "name": "Ganesh Patel",
    "email": "ganesh@example.com",
    "photo_url": null,
    "token": "1|xYz..."
  }
  ```

### 1.2 Login
`POST /api/calnest/login`
* **Request Body**:
  ```json
  {
    "email": "ganesh@example.com",
    "password": "secret123"
  }
  ```
* **Response (200 OK)**:
  ```json
  {
    "id": 12,
    "name": "Ganesh Patel",
    "email": "ganesh@example.com",
    "photo_url": null,
    "token": "1|xYz..."
  }
  ```
* **Failure (401 Unauthorized)**:
  ```json
  {
    "message": "Invalid email or password"
  }
  ```

### 1.3 Logout
`POST /api/calnest/logout`  
*Header: `Authorization: Bearer <token>`*
* **Response (200 OK)**:
  ```json
  {
    "status": "ok"
  }
  ```

### 1.4 Get Profile (Me)
`GET /api/calnest/me`  
*Header: `Authorization: Bearer <token>`*
* **Response (200 OK)**:
  ```json
  {
    "id": 12,
    "name": "Ganesh Patel",
    "email": "ganesh@example.com",
    "photo_url": null
  }
  ```

### 1.5 Update Profile
`PUT /api/calnest/me`  
*Header: `Authorization: Bearer <token>`*
* **Request Body**:
  ```json
  {
    "name": "Ganesh Patel Updated"
  }
  ```
* **Response (200 OK)**: Updated user profile object.

### 1.6 Delete Account (Apple App Store / Google Play Compliance)
`DELETE /api/calnest/me`  
*Header: `Authorization: Bearer <token>`*
* **Response (200 OK)**:
  ```json
  {
    "status": "deleted"
  }
  ```

### 1.7 Forgot Password
`POST /api/calnest/forgot-password`
* **Request Body**:
  ```json
  {
    "email": "ganesh@example.com"
  }
  ```
* **Response (200 OK)**:
  ```json
  {
    "status": "sent"
  }
  ```
  *(Always returns 200 even if email is unknown to prevent user enumeration attacks).*

### 1.8 Reset Password
`POST /api/calnest/reset-password`
* **Request Body**:
  ```json
  {
    "email": "ganesh@example.com",
    "code": "482913",
    "password": "newpass123"
  }
  ```
* **Response (200 OK)**:
  ```json
  {
    "status": "ok"
  }
  ```

### 1.9 Verify Email
`POST /api/calnest/verify-email`
* **Request Body**:
  ```json
  {
    "email": "ganesh@example.com",
    "code": "482913"
  }
  ```
* **Response (200 OK)**:
  ```json
  {
    "status": "verified"
  }
  ```

---

## 2. Events API

### Event JSON Schema
```json
{
  "id": 301,
  "title": "Site visit – Vaishnodevi",
  "description": "Carry the approved drawings",
  "location": "Vaishnodevi Circle, Ahmedabad",
  "start": "2026-11-06T10:00:00+05:30",
  "end": "2026-11-06T11:30:00+05:30",
  "is_all_day": false,
  "color": "#3B82F6",
  "updated_at": "2026-11-05T13:40:12+05:30"
}
```

### 2.1 List Events (with Date Range)
`GET /api/calnest/events?from=2026-10-01&to=2026-12-31`
* **Behavior**:
  * Correctly includes events spanning up to `to 23:59:59` and starting after `from 00:00:00`.
  * If `from` or `to` are omitted, returns all user events.
* **Response (200 OK)**:
  ```json
  {
    "items": [
      {
        "id": 301,
        "title": "Site visit – Vaishnodevi",
        "description": "Carry the approved drawings",
        "location": "Vaishnodevi Circle, Ahmedabad",
        "start": "2026-11-06T10:00:00+05:30",
        "end": "2026-11-06T11:30:00+05:30",
        "is_all_day": false,
        "color": "#3B82F6",
        "updated_at": "2026-11-05T13:40:12+05:30"
      }
    ]
  }
  ```

### 2.2 Create Event
`POST /api/calnest/events`
* **Request Body**:
  ```json
  {
    "title": "Site visit – Vaishnodevi",
    "description": "Carry the approved drawings",
    "location": "Vaishnodevi Circle, Ahmedabad",
    "start": "2026-11-06T10:00:00+05:30",
    "end": "2026-11-06T11:30:00+05:30",
    "is_all_day": false,
    "color": "#3B82F6"
  }
  ```
* **Response (201 Created)**: Returns created event object.

### 2.3 Update Event
`PUT/PATCH /api/calnest/events/{id}`
* **Request Body**: Any subset of fields (or full object).
* **Response (200 OK)**: Returns updated event object.

### 2.4 Delete Event
`DELETE /api/calnest/events/{id}`
* **Response (200 OK)**: `{"status": "deleted"}`

---

## 3. Tasks API

### Task JSON Schema
```json
{
  "id": 77,
  "title": "Send RERA documents",
  "description": "PDF set to Surbhi",
  "priority": "high",
  "due": "2026-11-08",
  "completed": false,
  "updated_at": "2026-11-05T13:40:12+05:30"
}
```
* `priority`: `'low'`, `'medium'`, `'high'`.
* `due`: `'YYYY-MM-DD'` or `null`.
* `completed`: boolean `true`/`false`.

### 3.1 List Tasks
`GET /api/calnest/tasks`
* Query parameters (optional): `?completed=false&priority=high`
* **Response (200 OK)**:
  ```json
  {
    "items": [
      {
        "id": 77,
        "title": "Send RERA documents",
        "description": "PDF set to Surbhi",
        "priority": "high",
        "due": "2026-11-08",
        "completed": false,
        "updated_at": "2026-11-05T13:40:12+05:30"
      }
    ]
  }
  ```

### 3.2 Create Task
`POST /api/calnest/tasks`
* **Request Body**:
  ```json
  {
    "title": "Send RERA documents",
    "description": "PDF set to Surbhi",
    "priority": "high",
    "due": "2026-11-08"
  }
  ```
* **Response (201 Created)**: Returns created task object.

### 3.3 Update Task / Mark Complete
`PUT/PATCH /api/calnest/tasks/{id}`
* **Request Body (Partial or Full)**:
  ```json
  {
    "completed": true
  }
  ```
* **Response (200 OK)**: Returns updated task object.

### 3.4 Delete Task
`DELETE /api/calnest/tasks/{id}`
* **Response (200 OK)**: `{"status": "deleted"}`

---

## 4. Manual Birthdays API

### Birthday JSON Schema
```json
{
  "id": 2,
  "name": "Priya Shah",
  "date": "1994-10-18",
  "phone": "+919876543210",
  "relationship": "Friend",
  "notes": null
}
```

### 4.1 List Birthdays
`GET /api/calnest/manual-birthdays`
* **Response (200 OK)**:
  ```json
  {
    "items": [
      {
        "id": 2,
        "name": "Priya Shah",
        "date": "1994-10-18",
        "phone": "+919876543210",
        "relationship": "Friend",
        "notes": null
      }
    ],
    "total": 1
  }
  ```

### 4.2 Create Birthday
`POST /api/calnest/manual-birthdays`
* **Response (201 Created)**: Returns created birthday object.

### 4.3 Update Birthday
`PUT/PATCH /api/calnest/manual-birthdays/{id}`
* **Response (200 OK)**: Returns updated birthday object.

### 4.4 Delete Birthday
`DELETE /api/calnest/manual-birthdays/{id}`
* **Response (200 OK)**: `{"status": "deleted"}`

---

## 5. Preferences & Settings API

### 5.1 Preferences
* `GET /api/calnest/preferences` -> Returns key-value dictionary (e.g. `{"theme": "dark", "calendar_view": "month"}`).
* `POST /api/calnest/preferences` -> Saves user key-value dictionary.

### 5.2 Reminder Settings
* `GET /api/calnest/reminder-settings`
  * **Response (200 OK)**:
    ```json
    {
      "event_reminder_minutes": 15,
      "birthday_reminder_enabled": true,
      "birthday_reminder_time": "09:00"
    }
    ```
* `POST /api/calnest/reminder-settings`
  * **Request Body**:
    ```json
    {
      "event_reminder_minutes": 15,
      "birthday_reminder_enabled": true,
      "birthday_reminder_time": "09:00"
    }
    ```
  * **Response (200 OK)**: Same typed JSON object.

---

## 6. Health & Overview
`GET /api/calnest/overview`
* **Response (200 OK)**:
  ```json
  {
    "status": "healthy",
    "app": "CalNest Backend API",
    "version": "1.0.0",
    "counts": {
      "greetings": 31,
      "events": 12,
      "tasks": 4,
      "manual_birthdays": 8
    }
  }
  ```

---

## 7. Greetings Library API

### Greeting JSON Schema
```json
{
  "id": 1,
  "title": "Warm Birthday Wishes",
  "message": "Wishing you a very Happy Birthday! May this special day bring you lots of joy...",
  "category": "birthday",
  "emoji": "🎂",
  "language": "English",
  "author": "CalNest",
  "image_url": null,
  "tags": ["birthday", "wishes", "celebration"],
  "created_at": "2026-11-06T10:00:00+05:30"
}
```

### 7.1 List Greetings Library
`GET /api/calnest/greetings`
* **Authentication**: Read-only, public content (token accepted if provided).
* **Query Parameters (optional)**:
  * `category`: Supports camelCase or snake_case (e.g. `birthday`, `goodMorning`, `good_morning`, `newJob`, `new_job`, `teachersDay`, `valentinesDay`, etc.).
  * `language`: Filter by language (e.g. `English`).
  * `q`: Search keyword across title and message text.
* **Response (200 OK)**:
  ```json
  {
    "items": [
      {
        "id": 1,
        "title": "Warm Birthday Wishes",
        "message": "Wishing you a very Happy Birthday! May this special day bring you lots of joy, laughter, and unforgettable moments.",
        "category": "birthday",
        "emoji": "🎂",
        "language": "English",
        "author": "CalNest",
        "image_url": null,
        "tags": ["birthday", "wishes", "celebration"],
        "created_at": "2026-11-06T10:00:00+05:30"
      }
    ]
  }
  ```


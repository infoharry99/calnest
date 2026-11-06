<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BirthdayController;
use App\Http\Controllers\Api\CalnestBackendController;
use App\Http\Controllers\Api\DeviceLinksController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\GoogleCalendarController;
use App\Http\Controllers\Api\GooglePeopleController;
use App\Http\Controllers\Api\GoogleTasksController;
use App\Http\Controllers\Api\MicrosoftGraphController;
use App\Http\Controllers\Api\OverviewController;
use App\Http\Controllers\Api\PreferenceController;
use App\Http\Controllers\Api\ReminderSettingController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes for CalNest Flutter App
|--------------------------------------------------------------------------
| Base URL: https://calnest.arinine.com/api/calnest
| All routes in this file are automatically prefixed with /api
|--------------------------------------------------------------------------
*/

// CalNest Native API Specification (5 November 2026)
Route::prefix('calnest')->group(function () {
    // Health check & overview
    Route::get('overview', [OverviewController::class, 'index']);

    // Public Auth & Password Reset
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'forgot']);
    Route::post('reset-password', [AuthController::class, 'reset']);
    Route::post('verify-email', [AuthController::class, 'verify']);

    // Authenticated routes
    Route::middleware('auth:sanctum')->group(function () {
        // User Profile & Account Management
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::put('me', [AuthController::class, 'updateProfile']);
        Route::delete('me', [AuthController::class, 'deleteAccount']);

        // Events CRUD
        Route::get('events', [EventController::class, 'index']);
        Route::post('events', [EventController::class, 'store']);
        Route::match(['put', 'patch'], 'events/{id}', [EventController::class, 'update']);
        Route::delete('events/{id}', [EventController::class, 'destroy']);

        // Tasks CRUD
        Route::get('tasks', [TaskController::class, 'index']);
        Route::post('tasks', [TaskController::class, 'store']);
        Route::match(['put', 'patch'], 'tasks/{id}', [TaskController::class, 'update']);
        Route::delete('tasks/{id}', [TaskController::class, 'destroy']);

        // Preferences & Settings
        Route::get('preferences', [PreferenceController::class, 'show']);
        Route::post('preferences', [PreferenceController::class, 'store']);
        Route::get('reminder-settings', [ReminderSettingController::class, 'show']);
        Route::post('reminder-settings', [ReminderSettingController::class, 'store']);

        // Manual Birthdays CRUD
        Route::get('manual-birthdays', [BirthdayController::class, 'index']);
        Route::post('manual-birthdays', [BirthdayController::class, 'store']);
        Route::match(['put', 'patch'], 'manual-birthdays/{id}', [BirthdayController::class, 'update']);
        Route::delete('manual-birthdays/{id}', [BirthdayController::class, 'destroy']);

        // Selected Calendars (optional legacy sync)
        Route::get('selected-calendars', [CalnestBackendController::class, 'listSelectedCalendars']);
        Route::post('selected-calendars', [CalnestBackendController::class, 'saveSelectedCalendars']);
    });
});

// Legacy / unauthenticated fallback for manual birthdays & preferences if needed
Route::prefix('device-links')->group(function () {
    Route::match(['get', 'post'], '/whatsapp', [DeviceLinksController::class, 'whatsApp']);
    Route::match(['get', 'post'], '/sms', [DeviceLinksController::class, 'sms']);
});

// Legacy OAuth Mock / Proxy Endpoints
Route::prefix('oauth')->group(function () {
    Route::post('/google/token', [AuthController::class, 'googleSignIn']);
    Route::get('/microsoft/authorize', [AuthController::class, 'microsoftAuthorize']);
    Route::post('/microsoft/token', [AuthController::class, 'microsoftToken']);
});

// User info (Sanctum)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Google Calendar API routes
Route::prefix('google/calendar/v3')->group(function () {
    Route::get('/users/me/calendarList', [GoogleCalendarController::class, 'listCalendars']);
    Route::get('/calendars/{calendarId}/events', [GoogleCalendarController::class, 'listEvents']);
    Route::post('/calendars/{calendarId}/events', [GoogleCalendarController::class, 'createEvent']);
    Route::patch('/calendars/{calendarId}/events/{eventId}', [GoogleCalendarController::class, 'updateEvent']);
    Route::delete('/calendars/{calendarId}/events/{eventId}', [GoogleCalendarController::class, 'deleteEvent']);
});

// Google Tasks API routes
Route::prefix('google/tasks/v1')->group(function () {
    Route::get('/users/@me/lists', [GoogleTasksController::class, 'listTaskLists']);
    Route::get('/lists/{tasklistId}/tasks', [GoogleTasksController::class, 'listTasks']);
    Route::post('/lists/{tasklistId}/tasks', [GoogleTasksController::class, 'createTask']);
    Route::patch('/lists/{tasklistId}/tasks/{taskId}', [GoogleTasksController::class, 'updateTask']);
    Route::delete('/lists/{tasklistId}/tasks/{taskId}', [GoogleTasksController::class, 'deleteTask']);
});

// Google People API routes
Route::prefix('google/people/v1')->group(function () {
    Route::get('/people/me/connections', [GooglePeopleController::class, 'listConnections']);
});

// Microsoft Graph API routes
Route::prefix('microsoft/v1.0')->group(function () {
    Route::get('/me', [MicrosoftGraphController::class, 'getProfile']);
    Route::get('/me/calendars', [MicrosoftGraphController::class, 'listCalendars']);
    Route::get('/me/calendarView', [MicrosoftGraphController::class, 'getPrimaryCalendarView']);
    Route::get('/me/calendars/{calendarId}/calendarView', [MicrosoftGraphController::class, 'getCalendarView']);
    Route::post('/me/calendars/{calendarId}/events', [MicrosoftGraphController::class, 'createEvent']);
    Route::patch('/me/calendars/{calendarId}/events/{eventId}', [MicrosoftGraphController::class, 'updateEvent']);
    Route::delete('/me/calendars/{calendarId}/events/{eventId}', [MicrosoftGraphController::class, 'deleteEvent']);

    Route::get('/me/todo/lists', [MicrosoftGraphController::class, 'listTodoLists']);
    Route::get('/me/todo/lists/{listId}/tasks', [MicrosoftGraphController::class, 'listTodoTasks']);
    Route::post('/me/todo/lists/{listId}/tasks', [MicrosoftGraphController::class, 'createTodoTask']);
    Route::patch('/me/todo/lists/{listId}/tasks/{taskId}', [MicrosoftGraphController::class, 'updateTodoTask']);
    Route::delete('/me/todo/lists/{listId}/tasks/{taskId}', [MicrosoftGraphController::class, 'deleteTodoTask']);
});

// Direct root paths for zero-config drop-in compatibility
Route::prefix('calendar/v3')->group(function () {
    Route::get('/users/me/calendarList', [GoogleCalendarController::class, 'listCalendars']);
    Route::get('/calendars/{calendarId}/events', [GoogleCalendarController::class, 'listEvents']);
    Route::post('/calendars/{calendarId}/events', [GoogleCalendarController::class, 'createEvent']);
    Route::patch('/calendars/{calendarId}/events/{eventId}', [GoogleCalendarController::class, 'updateEvent']);
    Route::delete('/calendars/{calendarId}/events/{eventId}', [GoogleCalendarController::class, 'deleteEvent']);
});

Route::prefix('tasks/v1')->group(function () {
    Route::get('/users/@me/lists', [GoogleTasksController::class, 'listTaskLists']);
    Route::get('/lists/{tasklistId}/tasks', [GoogleTasksController::class, 'listTasks']);
    Route::post('/lists/{tasklistId}/tasks', [GoogleTasksController::class, 'createTask']);
    Route::patch('/lists/{tasklistId}/tasks/{taskId}', [GoogleTasksController::class, 'updateTask']);
    Route::delete('/lists/{tasklistId}/tasks/{taskId}', [GoogleTasksController::class, 'deleteTask']);
});

Route::prefix('people/v1')->group(function () {
    Route::get('/people/me/connections', [GooglePeopleController::class, 'listConnections']);
});

Route::prefix('v1.0')->group(function () {
    Route::get('/me', [MicrosoftGraphController::class, 'getProfile']);
    Route::get('/me/calendars', [MicrosoftGraphController::class, 'listCalendars']);
    Route::get('/me/calendarView', [MicrosoftGraphController::class, 'getPrimaryCalendarView']);
    Route::get('/me/calendars/{calendarId}/calendarView', [MicrosoftGraphController::class, 'getCalendarView']);
    Route::post('/me/calendars/{calendarId}/events', [MicrosoftGraphController::class, 'createEvent']);
    Route::patch('/me/calendars/{calendarId}/events/{eventId}', [MicrosoftGraphController::class, 'updateEvent']);
    Route::delete('/me/calendars/{calendarId}/events/{eventId}', [MicrosoftGraphController::class, 'deleteEvent']);

    Route::get('/me/todo/lists', [MicrosoftGraphController::class, 'listTodoLists']);
    Route::get('/me/todo/lists/{listId}/tasks', [MicrosoftGraphController::class, 'listTodoTasks']);
    Route::post('/me/todo/lists/{listId}/tasks', [MicrosoftGraphController::class, 'createTodoTask']);
    Route::patch('/me/todo/lists/{listId}/tasks/{taskId}', [MicrosoftGraphController::class, 'updateTodoTask']);
    Route::delete('/me/todo/lists/{listId}/tasks/{taskId}', [MicrosoftGraphController::class, 'deleteTodoTask']);
});

<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CalnestBackendController;
use App\Http\Controllers\Api\DeviceLinksController;
use App\Http\Controllers\Api\GoogleCalendarController;
use App\Http\Controllers\Api\GooglePeopleController;
use App\Http\Controllers\Api\GoogleTasksController;
use App\Http\Controllers\Api\MicrosoftGraphController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes for CalNest Flutter App
|--------------------------------------------------------------------------
| Covers Google OAuth, Google Calendar, Google Tasks, Google People,
| Microsoft Graph (OAuth, Calendars, Events, To-Do), Device Links,
| and CalNest SharedPreferences synchronization.
*/

// User info (Sanctum protected)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// CalNest Native Backend Services
Route::prefix('calnest')->group(function () {
    Route::get('/overview', [CalnestBackendController::class, 'overview']);
    Route::get('/preferences', [CalnestBackendController::class, 'getPreferences']);
    Route::post('/preferences', [CalnestBackendController::class, 'savePreferences']);

    Route::get('/manual-birthdays', [CalnestBackendController::class, 'listManualBirthdays']);
    Route::post('/manual-birthdays', [CalnestBackendController::class, 'createManualBirthday']);
    Route::delete('/manual-birthdays/{id}', [CalnestBackendController::class, 'deleteManualBirthday']);

    Route::get('/selected-calendars', [CalnestBackendController::class, 'listSelectedCalendars']);
    Route::post('/selected-calendars', [CalnestBackendController::class, 'saveSelectedCalendars']);

    Route::get('/reminder-settings', [CalnestBackendController::class, 'getReminderSettings']);
    Route::post('/reminder-settings', [CalnestBackendController::class, 'saveReminderSettings']);
});

// Device Links (WhatsApp & SMS)
Route::prefix('device-links')->group(function () {
    Route::match(['get', 'post'], '/whatsapp', [DeviceLinksController::class, 'whatsApp']);
    Route::match(['get', 'post'], '/sms', [DeviceLinksController::class, 'sms']);
});

// OAuth Mock / Proxy Endpoints
Route::prefix('oauth')->group(function () {
    Route::post('/google/token', [AuthController::class, 'googleSignIn']);
    Route::get('/microsoft/authorize', [AuthController::class, 'microsoftAuthorize']);
    Route::post('/microsoft/token', [AuthController::class, 'microsoftToken']);
});

// Google Calendar API routes (namespaced)
Route::prefix('google/calendar/v3')->group(function () {
    Route::get('/users/me/calendarList', [GoogleCalendarController::class, 'listCalendars']);
    Route::get('/calendars/{calendarId}/events', [GoogleCalendarController::class, 'listEvents']);
    Route::post('/calendars/{calendarId}/events', [GoogleCalendarController::class, 'createEvent']);
    Route::patch('/calendars/{calendarId}/events/{eventId}', [GoogleCalendarController::class, 'updateEvent']);
    Route::delete('/calendars/{calendarId}/events/{eventId}', [GoogleCalendarController::class, 'deleteEvent']);
});

// Google Tasks API routes (namespaced)
Route::prefix('google/tasks/v1')->group(function () {
    Route::get('/users/@me/lists', [GoogleTasksController::class, 'listTaskLists']);
    Route::get('/lists/{tasklistId}/tasks', [GoogleTasksController::class, 'listTasks']);
    Route::post('/lists/{tasklistId}/tasks', [GoogleTasksController::class, 'createTask']);
    Route::patch('/lists/{tasklistId}/tasks/{taskId}', [GoogleTasksController::class, 'updateTask']);
    Route::delete('/lists/{tasklistId}/tasks/{taskId}', [GoogleTasksController::class, 'deleteTask']);
});

// Google People API routes (namespaced)
Route::prefix('google/people/v1')->group(function () {
    Route::get('/people/me/connections', [GooglePeopleController::class, 'listConnections']);
});

// Microsoft Graph API routes (namespaced)
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

/*
|--------------------------------------------------------------------------
| Also support direct /api/calendar/v3, /api/tasks/v1, /api/v1.0, etc.
|--------------------------------------------------------------------------
*/
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

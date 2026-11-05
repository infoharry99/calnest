<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DeviceLinksController;
use App\Http\Controllers\Api\GoogleCalendarController;
use App\Http\Controllers\Api\GooglePeopleController;
use App\Http\Controllers\Api\GoogleTasksController;
use App\Http\Controllers\Api\MicrosoftGraphController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes / Direct Root Service Endpoints
|--------------------------------------------------------------------------
| Allows the CalNest app to connect either via root URL or standard /api prefix.
*/

Route::get('/', function () {
    return view('welcome');
});

// Direct Microsoft OAuth 2.0 Endpoints
Route::prefix('consumers/oauth2/v2.0')->group(function () {
    Route::get('/authorize', [AuthController::class, 'microsoftAuthorize']);
    Route::post('/token', [AuthController::class, 'microsoftToken']);
});

// Direct Google Calendar API Endpoints
Route::prefix('calendar/v3')->group(function () {
    Route::get('/users/me/calendarList', [GoogleCalendarController::class, 'listCalendars']);
    Route::get('/calendars/{calendarId}/events', [GoogleCalendarController::class, 'listEvents']);
    Route::post('/calendars/{calendarId}/events', [GoogleCalendarController::class, 'createEvent']);
    Route::patch('/calendars/{calendarId}/events/{eventId}', [GoogleCalendarController::class, 'updateEvent']);
    Route::delete('/calendars/{calendarId}/events/{eventId}', [GoogleCalendarController::class, 'deleteEvent']);
});

// Direct Google Tasks API Endpoints
Route::prefix('tasks/v1')->group(function () {
    Route::get('/users/@me/lists', [GoogleTasksController::class, 'listTaskLists']);
    Route::get('/lists/{tasklistId}/tasks', [GoogleTasksController::class, 'listTasks']);
    Route::post('/lists/{tasklistId}/tasks', [GoogleTasksController::class, 'createTask']);
    Route::patch('/lists/{tasklistId}/tasks/{taskId}', [GoogleTasksController::class, 'updateTask']);
    Route::delete('/lists/{tasklistId}/tasks/{taskId}', [GoogleTasksController::class, 'deleteTask']);
});

// Direct Google People API Endpoints
Route::prefix('people/v1')->group(function () {
    Route::get('/people/me/connections', [GooglePeopleController::class, 'listConnections']);
});
Route::prefix('v1')->group(function () {
    Route::get('/people/me/connections', [GooglePeopleController::class, 'listConnections']);
});

// Direct Microsoft Graph API Endpoints (v1.0)
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

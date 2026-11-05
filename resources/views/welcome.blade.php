<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CalNest API Reference & Backend</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        code, pre { font-family: 'Fira Code', monospace; }
        .method-get { background-color: #0284c7; color: white; }
        .method-post { background-color: #16a34a; color: white; }
        .method-patch { background-color: #d97706; color: white; }
        .method-delete { background-color: #dc2626; color: white; }
        .method-open { background-color: #8b5cf6; color: white; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen">
    <!-- Header -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-sky-600 to-indigo-600 flex items-center justify-center text-white font-bold text-xl shadow-md">
                    CN
                </div>
                <div>
                    <h1 class="text-lg font-bold text-slate-900 leading-tight">CalNest API Reference</h1>
                    <p class="text-xs text-slate-500">Flutter App Companion Backend (v1.0.0)</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <a href="#quick-test" class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-sky-50 text-sky-700 hover:bg-sky-100 transition">
                    Quick Test Console
                </a>
                <a href="/api/calnest/overview" target="_blank" class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition">
                    View Health & Stats JSON
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Overview Banner -->
        <div class="bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 text-white p-6 sm:p-8 rounded-2xl shadow-lg mb-8">
            <div class="max-w-3xl">
                <span class="inline-block bg-sky-500/20 text-sky-300 text-xs px-2.5 py-1 rounded-full font-semibold mb-3 border border-sky-400/30">
                    REST API Documentation & Mock Engine
                </span>
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight mb-2">
                    CalNest Remote Services & Backend Sync
                </h2>
                <p class="text-slate-300 text-sm leading-relaxed mb-4">
                    Complete Laravel backend implementation supporting all 27 API endpoints defined in the CalNest specification: Google OAuth, Calendar, Tasks, People, Microsoft Graph (MSAL, Outlook, To-Do), WhatsApp/SMS links, and CalNest device storage synchronization.
                </p>
                <div class="flex flex-wrap gap-4 text-xs font-medium text-slate-300">
                    <div class="flex items-center gap-1.5 bg-white/10 px-3 py-1.5 rounded-lg">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        Status: Active & Seeded with Spec Data
                    </div>
                    <div class="flex items-center gap-1.5 bg-white/10 px-3 py-1.5 rounded-lg">
                        <span class="w-2 h-2 rounded-full bg-sky-400"></span>
                        Supports <code>/api/...</code> & Direct Root Endpoints
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left 2 Cols: Endpoint Documentation -->
            <div class="lg:col-span-2 space-y-8">

                <!-- 1. Sign-in and Authorisation -->
                <section class="bg-white rounded-xl shadow-xs border border-slate-200 overflow-hidden">
                    <div class="bg-slate-100 px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                        <h3 class="font-bold text-slate-800 text-base">1 · Sign-in and Authorisation</h3>
                        <span class="text-xs bg-slate-200 text-slate-700 px-2 py-0.5 rounded font-mono">OAuth 2.0</span>
                    </div>
                    <div class="divide-y divide-slate-100">
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded text-xs font-bold method-post">POST</span>
                                <code class="text-sm font-semibold text-slate-900">/api/oauth/google/token</code>
                            </div>
                            <p class="text-xs text-slate-600 mb-2">Google Sign-in token exchange / refresh for calendar, tasks & contacts scopes.</p>
                            <button onclick="testApi('POST', '/api/oauth/google/token', {email: 'user@gmail.com'})" class="text-xs text-sky-600 hover:text-sky-800 font-medium">Try Endpoint →</button>
                        </div>
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded text-xs font-bold method-get">GET</span>
                                <code class="text-sm font-semibold text-slate-900">/consumers/oauth2/v2.0/authorize</code>
                            </div>
                            <p class="text-xs text-slate-600 mb-2">Microsoft MSAL OAuth 2.0 authorization endpoint.</p>
                            <button onclick="testApi('GET', '/consumers/oauth2/v2.0/authorize?client_id=demo_app&redirect_uri=msauth://calnest')" class="text-xs text-sky-600 hover:text-sky-800 font-medium">Try Endpoint →</button>
                        </div>
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded text-xs font-bold method-post">POST</span>
                                <code class="text-sm font-semibold text-slate-900">/consumers/oauth2/v2.0/token</code>
                            </div>
                            <p class="text-xs text-slate-600 mb-2">Microsoft token exchange with authorization_code or refresh_token.</p>
                            <button onclick="testApi('POST', '/consumers/oauth2/v2.0/token', {grant_type: 'authorization_code', code: 'sample'})" class="text-xs text-sky-600 hover:text-sky-800 font-medium">Try Endpoint →</button>
                        </div>
                    </div>
                </section>

                <!-- 2. Google Calendar API -->
                <section class="bg-white rounded-xl shadow-xs border border-slate-200 overflow-hidden">
                    <div class="bg-slate-100 px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                        <h3 class="font-bold text-slate-800 text-base">2 · Google Calendar API</h3>
                        <span class="text-xs bg-sky-100 text-sky-700 px-2 py-0.5 rounded font-mono">calendar/v3</span>
                    </div>
                    <div class="divide-y divide-slate-100">
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded text-xs font-bold method-get">GET</span>
                                <code class="text-sm font-semibold text-slate-900">/calendar/v3/users/me/calendarList</code>
                            </div>
                            <p class="text-xs text-slate-600 mb-2">Lists every calendar the user has.</p>
                            <button onclick="testApi('GET', '/calendar/v3/users/me/calendarList')" class="text-xs text-sky-600 hover:text-sky-800 font-medium">Try Endpoint →</button>
                        </div>
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded text-xs font-bold method-get">GET</span>
                                <code class="text-sm font-semibold text-slate-900">/calendar/v3/calendars/{calendarId}/events</code>
                            </div>
                            <p class="text-xs text-slate-600 mb-2">Loads events for visible date range (timeMin, timeMax, singleEvents, orderBy, maxResults).</p>
                            <button onclick="testApi('GET', '/calendar/v3/calendars/user@gmail.com/events?orderBy=startTime&maxResults=250')" class="text-xs text-sky-600 hover:text-sky-800 font-medium">Try Endpoint →</button>
                        </div>
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded text-xs font-bold method-post">POST</span>
                                <code class="text-sm font-semibold text-slate-900">/calendar/v3/calendars/{calendarId}/events</code>
                            </div>
                            <p class="text-xs text-slate-600 mb-2">Creates an event from the Add Event form.</p>
                            <button onclick="testApi('POST', '/calendar/v3/calendars/user@gmail.com/events', {summary: 'Client call', description: 'Discuss renewal', location: 'Zoom', start: {dateTime: '2026-10-20T15:00:00+05:30'}, end: {dateTime: '2026-10-20T16:00:00+05:30'}})" class="text-xs text-sky-600 hover:text-sky-800 font-medium">Try Endpoint →</button>
                        </div>
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded text-xs font-bold method-patch">PATCH</span>
                                <code class="text-sm font-semibold text-slate-900">/calendar/v3/calendars/{calendarId}/events/{eventId}</code>
                            </div>
                            <p class="text-xs text-slate-600 mb-2">Saves edits to an event. Only the changed fields are sent.</p>
                            <button onclick="testApi('PATCH', '/calendar/v3/calendars/user@gmail.com/events/7k3c9qa1b2', {summary: 'Team standup (rescheduled)'})" class="text-xs text-sky-600 hover:text-sky-800 font-medium">Try Endpoint →</button>
                        </div>
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded text-xs font-bold method-delete">DELETE</span>
                                <code class="text-sm font-semibold text-slate-900">/calendar/v3/calendars/{calendarId}/events/{eventId}</code>
                            </div>
                            <p class="text-xs text-slate-600 mb-2">Deletes an event (returns 204 No Content).</p>
                            <button onclick="testApi('DELETE', '/calendar/v3/calendars/user@gmail.com/events/temp_sample_id')" class="text-xs text-sky-600 hover:text-sky-800 font-medium">Try Endpoint →</button>
                        </div>
                    </div>
                </section>

                <!-- 3. Google Tasks API -->
                <section class="bg-white rounded-xl shadow-xs border border-slate-200 overflow-hidden">
                    <div class="bg-slate-100 px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                        <h3 class="font-bold text-slate-800 text-base">3 · Google Tasks API</h3>
                        <span class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded font-mono">tasks/v1</span>
                    </div>
                    <div class="divide-y divide-slate-100">
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded text-xs font-bold method-get">GET</span>
                                <code class="text-sm font-semibold text-slate-900">/tasks/v1/users/@me/lists</code>
                            </div>
                            <p class="text-xs text-slate-600 mb-2">Lists the user’s task lists.</p>
                            <button onclick="testApi('GET', '/tasks/v1/users/@me/lists')" class="text-xs text-sky-600 hover:text-sky-800 font-medium">Try Endpoint →</button>
                        </div>
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded text-xs font-bold method-get">GET</span>
                                <code class="text-sm font-semibold text-slate-900">/tasks/v1/lists/{tasklistId}/tasks</code>
                            </div>
                            <p class="text-xs text-slate-600 mb-2">Loads tasks in a list (e.g. maxResults=200).</p>
                            <button onclick="testApi('GET', '/tasks/v1/lists/MTIzNDU2/tasks?maxResults=200')" class="text-xs text-sky-600 hover:text-sky-800 font-medium">Try Endpoint →</button>
                        </div>
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded text-xs font-bold method-post">POST</span>
                                <code class="text-sm font-semibold text-slate-900">/tasks/v1/lists/{tasklistId}/tasks</code>
                            </div>
                            <p class="text-xs text-slate-600 mb-2">Creates a task.</p>
                            <button onclick="testApi('POST', '/tasks/v1/lists/MTIzNDU2/tasks', {title: 'Renew domain', notes: 'btotmail.com', due: '2026-10-25T00:00:00.000Z', status: 'needsAction'})" class="text-xs text-sky-600 hover:text-sky-800 font-medium">Try Endpoint →</button>
                        </div>
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded text-xs font-bold method-patch">PATCH</span>
                                <code class="text-sm font-semibold text-slate-900">/tasks/v1/lists/{tasklistId}/tasks/{taskId}</code>
                            </div>
                            <p class="text-xs text-slate-600 mb-2">Edits a task, or marks it complete or open.</p>
                            <button onclick="testApi('PATCH', '/tasks/v1/lists/MTIzNDU2/tasks/dGFzay0x', {status: 'completed'})" class="text-xs text-sky-600 hover:text-sky-800 font-medium">Try Endpoint →</button>
                        </div>
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded text-xs font-bold method-delete">DELETE</span>
                                <code class="text-sm font-semibold text-slate-900">/tasks/v1/lists/{tasklistId}/tasks/{taskId}</code>
                            </div>
                            <p class="text-xs text-slate-600 mb-2">Deletes a task.</p>
                            <button onclick="testApi('DELETE', '/tasks/v1/lists/MTIzNDU2/tasks/temp_task_id')" class="text-xs text-sky-600 hover:text-sky-800 font-medium">Try Endpoint →</button>
                        </div>
                    </div>
                </section>

                <!-- 4. Google People API -->
                <section class="bg-white rounded-xl shadow-xs border border-slate-200 overflow-hidden">
                    <div class="bg-slate-100 px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                        <h3 class="font-bold text-slate-800 text-base">4 · Google People API</h3>
                        <span class="text-xs bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded font-mono">people/v1</span>
                    </div>
                    <div class="p-5">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2 py-0.5 rounded text-xs font-bold method-get">GET</span>
                            <code class="text-sm font-semibold text-slate-900">/people/v1/people/me/connections</code>
                        </div>
                        <p class="text-xs text-slate-600 mb-2">Reads contacts with birthdays for the Birthdays screen (personFields, pageSize, pageToken).</p>
                        <button onclick="testApi('GET', '/people/v1/people/me/connections?pageSize=200')" class="text-xs text-sky-600 hover:text-sky-800 font-medium">Try Endpoint →</button>
                    </div>
                </section>

                <!-- 5. Microsoft Graph API -->
                <section class="bg-white rounded-xl shadow-xs border border-slate-200 overflow-hidden">
                    <div class="bg-slate-100 px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                        <h3 class="font-bold text-slate-800 text-base">5 · Microsoft Graph API</h3>
                        <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded font-mono">graph/v1.0</span>
                    </div>
                    <div class="divide-y divide-slate-100">
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded text-xs font-bold method-get">GET</span>
                                <code class="text-sm font-semibold text-slate-900">/v1.0/me</code>
                            </div>
                            <p class="text-xs text-slate-600 mb-2">Reads signed-in user's profile (name & email).</p>
                            <button onclick="testApi('GET', '/v1.0/me')" class="text-xs text-sky-600 hover:text-sky-800 font-medium">Try Endpoint →</button>
                        </div>
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded text-xs font-bold method-get">GET</span>
                                <code class="text-sm font-semibold text-slate-900">/v1.0/me/calendars</code>
                            </div>
                            <p class="text-xs text-slate-600 mb-2">Lists Outlook calendars ($select=id,name,isDefaultCalendar,color).</p>
                            <button onclick="testApi('GET', '/v1.0/me/calendars')" class="text-xs text-sky-600 hover:text-sky-800 font-medium">Try Endpoint →</button>
                        </div>
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded text-xs font-bold method-get">GET</span>
                                <code class="text-sm font-semibold text-slate-900">/v1.0/me/calendarView</code>
                            </div>
                            <p class="text-xs text-slate-600 mb-2">Loads events for the primary calendar in visible range ($top=500).</p>
                            <button onclick="testApi('GET', '/v1.0/me/calendarView')" class="text-xs text-sky-600 hover:text-sky-800 font-medium">Try Endpoint →</button>
                        </div>
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded text-xs font-bold method-get">GET</span>
                                <code class="text-sm font-semibold text-slate-900">/v1.0/me/calendars/{id}/calendarView</code>
                            </div>
                            <p class="text-xs text-slate-600 mb-2">Loads events for a specific Outlook calendar.</p>
                            <button onclick="testApi('GET', '/v1.0/me/calendars/AAMkAGI2/calendarView')" class="text-xs text-sky-600 hover:text-sky-800 font-medium">Try Endpoint →</button>
                        </div>
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded text-xs font-bold method-post">POST</span>
                                <code class="text-sm font-semibold text-slate-900">/v1.0/me/calendars/{id}/events</code>
                            </div>
                            <p class="text-xs text-slate-600 mb-2">Creates an Outlook event.</p>
                            <button onclick="testApi('POST', '/v1.0/me/calendars/AAMkAGI2/events', {subject: 'Client call', body: {contentType: 'text', content: 'Discuss renewal'}, location: {displayName: 'Zoom'}, start: {dateTime: '2026-10-20T10:00:00', timeZone: 'UTC'}, end: {dateTime: '2026-10-20T11:00:00', timeZone: 'UTC'}, isAllDay: false})" class="text-xs text-sky-600 hover:text-sky-800 font-medium">Try Endpoint →</button>
                        </div>
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded text-xs font-bold method-patch">PATCH</span>
                                <code class="text-sm font-semibold text-slate-900">/v1.0/me/calendars/{id}/events/{eventId}</code>
                            </div>
                            <p class="text-xs text-slate-600 mb-2">Saves edits to an Outlook event.</p>
                            <button onclick="testApi('PATCH', '/v1.0/me/calendars/AAMkAGI2/events/AAMkAGI2-evt1', {subject: 'Dentist (rescheduled)'})" class="text-xs text-sky-600 hover:text-sky-800 font-medium">Try Endpoint →</button>
                        </div>
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded text-xs font-bold method-delete">DELETE</span>
                                <code class="text-sm font-semibold text-slate-900">/v1.0/me/calendars/{id}/events/{eventId}</code>
                            </div>
                            <p class="text-xs text-slate-600 mb-2">Deletes an Outlook event (204 No Content).</p>
                            <button onclick="testApi('DELETE', '/v1.0/me/calendars/AAMkAGI2/events/temp_evt')" class="text-xs text-sky-600 hover:text-sky-800 font-medium">Try Endpoint →</button>
                        </div>
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded text-xs font-bold method-get">GET</span>
                                <code class="text-sm font-semibold text-slate-900">/v1.0/me/todo/lists</code>
                            </div>
                            <p class="text-xs text-slate-600 mb-2">Lists Microsoft To Do lists.</p>
                            <button onclick="testApi('GET', '/v1.0/me/todo/lists')" class="text-xs text-sky-600 hover:text-sky-800 font-medium">Try Endpoint →</button>
                        </div>
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded text-xs font-bold method-get">GET</span>
                                <code class="text-sm font-semibold text-slate-900">/v1.0/me/todo/lists/{listId}/tasks</code>
                            </div>
                            <p class="text-xs text-slate-600 mb-2">Loads tasks in a To Do list.</p>
                            <button onclick="testApi('GET', '/v1.0/me/todo/lists/AQMkADAw/tasks')" class="text-xs text-sky-600 hover:text-sky-800 font-medium">Try Endpoint →</button>
                        </div>
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded text-xs font-bold method-post">POST</span>
                                <code class="text-sm font-semibold text-slate-900">/v1.0/me/todo/lists/{listId}/tasks</code>
                            </div>
                            <p class="text-xs text-slate-600 mb-2">Creates a To Do task (201 Created).</p>
                            <button onclick="testApi('POST', '/v1.0/me/todo/lists/AQMkADAw/tasks', {title: 'Renew domain', body: {contentType: 'text', content: 'btotmail.com'}, dueDateTime: {dateTime: '2026-10-25T00:00:00', timeZone: 'UTC'}, status: 'notStarted'})" class="text-xs text-sky-600 hover:text-sky-800 font-medium">Try Endpoint →</button>
                        </div>
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded text-xs font-bold method-patch">PATCH</span>
                                <code class="text-sm font-semibold text-slate-900">/v1.0/me/todo/lists/{listId}/tasks/{taskId}</code>
                            </div>
                            <p class="text-xs text-slate-600 mb-2">Edits a To Do task, or sets status only when marking complete.</p>
                            <button onclick="testApi('PATCH', '/v1.0/me/todo/lists/AQMkADAw/tasks/AQMkADAw-t1', {status: 'completed'})" class="text-xs text-sky-600 hover:text-sky-800 font-medium">Try Endpoint →</button>
                        </div>
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded text-xs font-bold method-delete">DELETE</span>
                                <code class="text-sm font-semibold text-slate-900">/v1.0/me/todo/lists/{listId}/tasks/{taskId}</code>
                            </div>
                            <p class="text-xs text-slate-600 mb-2">Deletes a To Do task (204 No Content).</p>
                            <button onclick="testApi('DELETE', '/v1.0/me/todo/lists/AQMkADAw/tasks/temp_task')" class="text-xs text-sky-600 hover:text-sky-800 font-medium">Try Endpoint →</button>
                        </div>
                    </div>
                </section>

                <!-- 6. Device Links -->
                <section class="bg-white rounded-xl shadow-xs border border-slate-200 overflow-hidden">
                    <div class="bg-slate-100 px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                        <h3 class="font-bold text-slate-800 text-base">6 · Device Links (WhatsApp & SMS)</h3>
                        <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded font-mono">device</span>
                    </div>
                    <div class="divide-y divide-slate-100">
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded text-xs font-bold method-open">OPEN</span>
                                <code class="text-sm font-semibold text-slate-900">https://wa.me/{phone}?text={urlencoded message}</code>
                            </div>
                            <p class="text-xs text-slate-600 mb-2">Opens WhatsApp with the birthday wish filled in. API helper: <code>/api/device-links/whatsapp</code>.</p>
                            <button onclick="testApi('GET', '/api/device-links/whatsapp?phone=+91 98765 43210&message=Happy Birthday Priya!')" class="text-xs text-sky-600 hover:text-sky-800 font-medium">Try Helper →</button>
                        </div>
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded text-xs font-bold method-open">OPEN</span>
                                <code class="text-sm font-semibold text-slate-900">sms:{phone}?body={urlencoded message}</code>
                            </div>
                            <p class="text-xs text-slate-600 mb-2">Opens default SMS app with wish filled in. API helper: <code>/api/device-links/sms</code>.</p>
                            <button onclick="testApi('GET', '/api/device-links/sms?phone=+91 98765 43210&message=Happy Birthday Priya!')" class="text-xs text-sky-600 hover:text-sky-800 font-medium">Try Helper →</button>
                        </div>
                    </div>
                </section>

                <!-- 7. CalNest Backend (Developer Notes) -->
                <section class="bg-white rounded-xl shadow-xs border border-slate-200 overflow-hidden">
                    <div class="bg-slate-100 px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                        <h3 class="font-bold text-slate-800 text-base">7 · CalNest Backend Storage Sync</h3>
                        <span class="text-xs bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded font-mono">calnest/backend</span>
                    </div>
                    <div class="divide-y divide-slate-100">
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded text-xs font-bold method-get">GET</span>
                                <span class="px-2 py-0.5 rounded text-xs font-bold method-post">POST</span>
                                <code class="text-sm font-semibold text-slate-900">/api/calnest/preferences</code>
                            </div>
                            <p class="text-xs text-slate-600 mb-2">Cloud sync for SharedPreferences (theme, view mode, user settings).</p>
                            <button onclick="testApi('GET', '/api/calnest/preferences')" class="text-xs text-sky-600 hover:text-sky-800 font-medium">Try Endpoint →</button>
                        </div>
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded text-xs font-bold method-get">GET</span>
                                <span class="px-2 py-0.5 rounded text-xs font-bold method-post">POST</span>
                                <code class="text-sm font-semibold text-slate-900">/api/calnest/manual-birthdays</code>
                            </div>
                            <p class="text-xs text-slate-600 mb-2">Cloud sync for manual birthdays added inside the app.</p>
                            <button onclick="testApi('GET', '/api/calnest/manual-birthdays')" class="text-xs text-sky-600 hover:text-sky-800 font-medium">Try Endpoint →</button>
                        </div>
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded text-xs font-bold method-get">GET</span>
                                <span class="px-2 py-0.5 rounded text-xs font-bold method-post">POST</span>
                                <code class="text-sm font-semibold text-slate-900">/api/calnest/selected-calendars</code>
                            </div>
                            <p class="text-xs text-slate-600 mb-2">Saves which calendars are toggled active for sync.</p>
                            <button onclick="testApi('GET', '/api/calnest/selected-calendars')" class="text-xs text-sky-600 hover:text-sky-800 font-medium">Try Endpoint →</button>
                        </div>
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded text-xs font-bold method-get">GET</span>
                                <span class="px-2 py-0.5 rounded text-xs font-bold method-post">POST</span>
                                <code class="text-sm font-semibold text-slate-900">/api/calnest/reminder-settings</code>
                            </div>
                            <p class="text-xs text-slate-600 mb-2">Reminder times and notification preferences sync.</p>
                            <button onclick="testApi('GET', '/api/calnest/reminder-settings')" class="text-xs text-sky-600 hover:text-sky-800 font-medium">Try Endpoint →</button>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Right Col: Interactive Test Console & Live Responses -->
            <div class="space-y-6">
                <div id="quick-test" class="bg-white rounded-xl shadow-xs border border-slate-200 p-6 sticky top-24">
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                        <h4 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Live API Test Console
                        </h4>
                        <span id="responseStatus" class="text-xs font-semibold px-2 py-0.5 rounded bg-slate-100 text-slate-600 font-mono">
                            Ready
                        </span>
                    </div>

                    <div class="space-y-3 mb-4">
                        <div class="flex gap-2">
                            <select id="requestMethod" class="text-xs font-bold border border-slate-300 rounded-lg px-2 py-2 bg-slate-50 focus:outline-none">
                                <option value="GET">GET</option>
                                <option value="POST">POST</option>
                                <option value="PATCH">PATCH</option>
                                <option value="DELETE">DELETE</option>
                            </select>
                            <input id="requestUrl" type="text" value="/api/calnest/overview" class="flex-1 text-xs border border-slate-300 rounded-lg px-3 py-2 font-mono focus:outline-none focus:ring-1 focus:ring-sky-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Request Body (JSON for POST/PATCH)</label>
                            <textarea id="requestBody" rows="4" class="w-full text-xs font-mono border border-slate-300 rounded-lg p-2.5 bg-slate-50 focus:outline-none" placeholder="{}"></textarea>
                        </div>
                        <button onclick="executeConsole()" class="w-full py-2 bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs rounded-lg transition shadow-xs flex items-center justify-center gap-1.5">
                            <span>Execute Request</span>
                            <span>⚡</span>
                        </button>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs font-semibold text-slate-600">Response</span>
                            <span id="responseTime" class="text-[11px] text-slate-400 font-mono"></span>
                        </div>
                        <pre id="responseJson" class="text-xs bg-slate-900 text-emerald-400 p-3.5 rounded-lg overflow-x-auto max-h-96 whitespace-pre-wrap">{ "message": "Click any 'Try Endpoint' button or click Execute Request" }</pre>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="mt-16 border-t border-slate-200 py-6 text-center text-xs text-slate-500">
        CalNest Backend API • Compatible with CalNest Flutter app (com.calnest.calnest, version 1.0.0)
    </footer>

    <script>
        function testApi(method, url, body = null) {
            document.getElementById('requestMethod').value = method;
            document.getElementById('requestUrl').value = url;
            document.getElementById('requestBody').value = body ? JSON.stringify(body, null, 2) : '';
            executeConsole();
        }

        async function executeConsole() {
            const method = document.getElementById('requestMethod').value;
            const url = document.getElementById('requestUrl').value;
            const bodyText = document.getElementById('requestBody').value.trim();
            const statusEl = document.getElementById('responseStatus');
            const jsonEl = document.getElementById('responseJson');
            const timeEl = document.getElementById('responseTime');

            statusEl.textContent = 'Sending...';
            statusEl.className = 'text-xs font-semibold px-2 py-0.5 rounded bg-amber-100 text-amber-700 font-mono';
            const startTime = performance.now();

            try {
                const options = {
                    method: method,
                    headers: {
                        'Accept': 'application/json',
                    }
                };

                if ((method === 'POST' || method === 'PATCH') && bodyText) {
                    options.headers['Content-Type'] = 'application/json';
                    options.body = bodyText;
                }

                const res = await fetch(url, options);
                const duration = Math.round(performance.now() - startTime);
                timeEl.textContent = `${duration} ms`;

                statusEl.textContent = `${res.status} ${res.statusText}`;
                if (res.ok) {
                    statusEl.className = 'text-xs font-semibold px-2 py-0.5 rounded bg-emerald-100 text-emerald-700 font-mono';
                } else {
                    statusEl.className = 'text-xs font-semibold px-2 py-0.5 rounded bg-rose-100 text-rose-700 font-mono';
                }

                if (res.status === 204) {
                    jsonEl.textContent = '204 No Content (Empty Response Body)';
                    return;
                }

                const contentType = res.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    const data = await res.json();
                    jsonEl.textContent = JSON.stringify(data, null, 2);
                } else {
                    const text = await res.text();
                    jsonEl.textContent = text;
                }
            } catch (err) {
                statusEl.textContent = 'Network Error';
                statusEl.className = 'text-xs font-semibold px-2 py-0.5 rounded bg-rose-100 text-rose-700 font-mono';
                jsonEl.textContent = err.toString();
            }
        }

        // Run overview on load
        window.addEventListener('DOMContentLoaded', () => {
            executeConsole();
        });
    </script>
</body>
</html>

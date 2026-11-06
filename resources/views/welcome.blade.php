<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CalNest API Reference & Services</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; background: #0f172a; color: #f8fafc; margin: 0; padding: 2rem; }
        .container { max-width: 800px; margin: 0 auto; background: #1e293b; padding: 2rem; border-radius: 12px; border: 1px solid #334155; }
        h1 { color: #38bdf8; margin-top: 0; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 9999px; font-size: 0.8rem; background: #0284c7; color: white; margin-bottom: 1rem; }
        ul { line-height: 1.8; }
        a { color: #38bdf8; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <span class="badge">API Status: Online</span>
        <h1>CalNest API Reference</h1>
        <p>Production backend for CalNest Flutter App (Events, Tasks, Birthdays, Preferences, and Auth).</p>
        <h2>Available Endpoints</h2>
        <ul>
            <li><strong>Health Check:</strong> <code>GET /api/calnest/overview</code></li>
            <li><strong>Authentication:</strong> <code>POST /api/calnest/register</code>, <code>POST /api/calnest/login</code>, <code>POST /api/calnest/logout</code></li>
            <li><strong>User Profile:</strong> <code>GET /api/calnest/me</code></li>
            <li><strong>Events:</strong> <code>GET /api/calnest/events</code>, <code>POST /api/calnest/events</code></li>
            <li><strong>Tasks:</strong> <code>GET /api/calnest/tasks</code>, <code>POST /api/calnest/tasks</code></li>
            <li><strong>Birthdays:</strong> <code>GET /api/calnest/manual-birthdays</code>, <code>POST /api/calnest/manual-birthdays</code></li>
            <li><strong>Preferences:</strong> <code>GET /api/calnest/preferences</code>, <code>POST /api/calnest/preferences</code></li>
            <li><strong>Reminder Settings:</strong> <code>GET /api/calnest/reminder-settings</code>, <code>POST /api/calnest/reminder-settings</code></li>
        </ul>
    </div>
</body>
</html>

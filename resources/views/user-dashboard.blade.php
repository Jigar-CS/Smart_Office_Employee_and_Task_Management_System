<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>User Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f4f9ff;
            --panel: #ffffff;
            --panel-2: #ffffff;
            --line: rgba(31,111,224,0.08);
            --text: #0b2540;
            --muted: #567089;
            --brand: #1f6fe0;
            --brand-2: #0f57c6;
            --warning: #f0ad4e;
            --success: #28a745;
            --danger: #d9534f;
            --shadow: 0 8px 20px rgba(31,111,224,0.06);
        }

        * { box-sizing: border-box; }
        html, body { height: 100%; margin: 0; }

        body {
            font-family: Inter, sans-serif;
            color: var(--text);
            color-scheme: light;
            background: var(--bg);
        }

            body.drawer-open {
                overflow: hidden;
            }

        /* Minimal UI: no decorative overlay */

        .shell {
            min-height: 100vh;
            padding: 26px;
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 18px;
        }

        .sidebar,
        .card,
        .panel,
        .overview-card {
            border: 1px solid var(--line);
            border-radius: 12px;
            background: var(--panel);
            box-shadow: var(--shadow);
        }

        .sidebar {
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            position: sticky;
            top: 26px;
            height: calc(100vh - 52px);
            background: var(--panel);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--line);
        }

        .brand img { width: 44px; height: 44px; object-fit: contain; }
        .brand strong { display: block; font-family: 'Space Grotesk', sans-serif; letter-spacing: 0.08em; text-transform: uppercase; }
        .brand span { display: block; color: var(--muted); font-size: 12px; margin-top: 3px; }

        .profile {
            padding: 14px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: var(--panel);
        }

        .profile-label { color: var(--muted); font-size: 12px; text-transform: uppercase; letter-spacing: 0.12em; }
        .profile-name { margin-top: 8px; font-weight: 800; }
        .profile-email { margin-top: 4px; color: var(--muted); font-size: 13px; word-break: break-all; }

        .nav {
            display: grid;
            gap: 8px;
        }

        .nav button,
        .logout-btn {
            width: 100%;
            border: 1px solid transparent;
            background: #f4f8ff;
            color: var(--text);
            padding: 12px 14px;
            border-radius: 12px;
            cursor: pointer;
            text-align: left;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: 0.12s ease;
        }

        .nav button:hover,
        .nav button.active {
            background: rgba(43,125,233,0.08);
            border-color: rgba(43,125,233,0.12);
            color: var(--brand);
        }

        .nav small { color: var(--muted); font-size: 11px; }

        .logout-btn {
            margin-top: auto;
            justify-content: center;
            background: rgba(217,83,79,0.06);
            border-color: rgba(217,83,79,0.12);
            color: var(--danger);
            font-weight: 700;
        }

        .content {
            display: grid;
            gap: 18px;
            align-content: start;
        }

        .drawer-backdrop {
            position: fixed;
            inset: 0;
            z-index: 40;
            display: none;
            background: rgba(4, 12, 20, 0.35);
            backdrop-filter: blur(14px) saturate(110%);
            -webkit-backdrop-filter: blur(14px) saturate(110%);
        }

        .drawer-backdrop.visible {
            display: block;
        }

        .hero,
        .panel,
        .overview-card,
        .stat,
        .task-card {
            padding: 20px;
        }

        .hero {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
        }

        .hero h1 {
            margin: 0;
            font-family: 'Space Grotesk', sans-serif;
            font-size: clamp(28px, 4vw, 42px);
            line-height: 1.05;
        }

        .hero p { margin: 8px 0 0; color: var(--muted); line-height: 1.6; }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 12px;
            border: 1px solid var(--line);
            border-radius: 999px;
            background: rgba(255,255,255,0.03);
            color: var(--muted);
            font-size: 12px;
        }

        .grid { display: grid; gap: 18px; }
        .stats { grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 18px; }

        .stat .label {
            color: var(--muted);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.12em;
        }

        .stat .value {
            margin-top: 8px;
            font-size: 32px;
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 800;
        }

        .stat .hint { margin-top: 6px; color: var(--muted); font-size: 13px; }

        .layout {
            display: grid;
            grid-template-columns: 1fr;
            gap: 18px;
        }

        .profile-panel {
            display: grid;
            gap: 18px;
        }

        .profile-hero {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .profile-avatar {
            width: 72px;
            height: 72px;
            border-radius: 22px;
            overflow: hidden;
            display: grid;
            place-items: center;
            background: var(--brand);
            border: 1px solid rgba(43,125,233,0.12);
            color: #fff;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 24px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none;
        }

        .profile-actions {
            display: flex;
            gap: 10px;
            margin-left: auto;
            flex-wrap: wrap;
        }

        .profile-title h2 {
            margin: 0;
            font-family: 'Space Grotesk', sans-serif;
            font-size: clamp(24px, 3vw, 34px);
        }

        .profile-title p {
            margin: 6px 0 0;
            color: var(--muted);
            line-height: 1.6;
        }

        .profile-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .profile-item {
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 14px 16px;
            background: rgba(255,255,255,0.03);
        }

        .profile-item .label {
            color: var(--muted);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.12em;
        }

        .profile-item .value {
            margin-top: 8px;
            font-size: 15px;
            font-weight: 700;
            color: var(--text);
            word-break: break-word;
        }

        .profile-wide {
            grid-column: 1 / -1;
        }

        .profile-status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            width: fit-content;
            margin-top: 8px;
        }

        .profile-status.active {
            background: rgba(94,227,139,0.16);
            color: #a2f5ba;
        }

        .profile-status.inactive {
            background: rgba(255,107,107,0.16);
            color: #ffb2b2;
        }

        .profile-edit-form {
            display: none;
            border-top: 1px solid var(--line);
            padding-top: 18px;
        }

        .profile-edit-form.open {
            display: block;
        }

        .profile-form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .profile-form-grid .field.full {
            grid-column: 1 / -1;
        }

        .profile-image-preview {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border: 1px dashed var(--line);
            border-radius: 16px;
            background: rgba(255,255,255,0.03);
        }

        .profile-image-preview img {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            object-fit: cover;
        }

        .profile-image-preview span {
            color: var(--muted);
            font-size: 13px;
            line-height: 1.5;
        }

        .editor-drawer {
            display: none;
            position: fixed;
            top: 26px;
            right: 26px;
            width: min(500px, calc(100vw - 52px));
            max-height: calc(100vh - 52px);
            overflow: auto;
            z-index: 50;
        }

        .editor-drawer.open {
            display: block;
        }

        .editor-drawer-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 18px;
        }

        .editor-drawer-title {
            margin: 0;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 24px;
        }

        .editor-drawer-note {
            margin: 6px 0 0;
            color: var(--muted);
            line-height: 1.6;
            font-size: 13px;
        }

        .editor-drawer-empty {
            min-height: 240px;
            display: grid;
            place-items: center;
            text-align: center;
            border: 1px dashed var(--line);
            border-radius: 18px;
            padding: 18px;
            color: var(--muted);
            background: rgba(255,255,255,0.03);
        }

        .editor-drawer .form-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .editor-drawer .field.full {
            grid-column: 1 / -1;
        }

        .editor-drawer .btn {
            min-height: 48px;
        }

        .panel-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 18px;
        }

        .panel-title {
            margin: 0 0 6px 0;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 24px;
            color: var(--text);
        }
        .panel-title::after { content: ''; display: block; margin-top: 10px; width: 48px; height: 3px; background: var(--brand); border-radius: 3px; }

        .panel-desc { margin: 6px 0 0; color: var(--muted); line-height: 1.6; }

        .toolbar {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .search,
        .input,
        .select,
        .textarea {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: #f4f8ff;
            color: var(--text);
            padding: 10px 12px;
            outline: none;
            transition: box-shadow 0.12s ease, border-color 0.12s ease;
        }
        .search:focus, .input:focus, .select:focus, .textarea:focus { box-shadow: 0 6px 18px rgba(43,125,233,0.06); border-color: var(--brand); }

        .select option {
            color: var(--text);
            background: #fff;
        }

        .select option:checked,
        .select option:focus {
            color: #fff;
            background: var(--brand);
        }

        .select option:hover {
            color: var(--text);
            background: rgba(43,125,233,0.06);
        }

        .textarea { min-height: 110px; resize: vertical; }

        .btn {
            border: 0;
            border-radius: 10px;
            padding: 10px 14px;
            cursor: pointer;
            font-weight: 700;
            transition: transform 0.12s ease, box-shadow 0.12s ease;
        }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 8px 22px rgba(43,125,233,0.08); }
        .btn-primary { color: #fff; background: var(--brand); box-shadow: 0 8px 22px rgba(43,125,233,0.08); }
.btn-secondary { color: var(--text); background: #fff; border: 1px solid var(--line); }

        .icon-mini {
            width: 40px;
            height: 36px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            border: 1px solid var(--line);
            background: #ffffff;
            cursor: pointer;
        }

        .icon-mini svg {
            width: 18px;
            height: 18px;
            display: block;
        }

        .icon-mini svg * {
            stroke: #0b2540;
            fill: none;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .task-list { display: grid; gap: 14px; }

        /* Strong, forced task card styles to ensure visibility */
        .task-card {
            border: 1px solid rgba(11,37,64,0.08) !important;
            border-radius: 12px !important;
            background: #ffffff !important;
            cursor: pointer;
            transition: 0.12s ease !important;
            box-shadow: 0 16px 40px rgba(11,37,64,0.10) !important;
            position: relative;
            overflow: hidden;
        }
        .task-card::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 6px; background: var(--brand); }
        .task-card:hover, .task-card.active { transform: translateY(-10px) !important; box-shadow: 0 22px 56px rgba(11,37,64,0.14) !important; }

        .task-top {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: flex-start;
        }

        .task-title {
            margin: 0;
            font-size: 18px;
            font-weight: 800;
        }

        .task-meta {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }

        .badge.ok { background: rgba(40,163,91,0.08); color: #1f7a45; }
        .badge.warn { background: rgba(240,173,78,0.08); color: #a15e1a; }
        .badge.danger { background: rgba(217,83,79,0.08); color: #a93b36; }
        .badge.muted { background: rgba(31,111,224,0.04); color: var(--text); }

        .task-desc { margin: 12px 0 0; color: var(--muted); line-height: 1.65; }

        .task-title { color: var(--text); }

        .task-footer {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
            margin-top: 14px;
            color: var(--muted);
            font-size: 13px;
            flex-wrap: wrap;
        }

        .notice {
            min-height: 20px;
            color: var(--muted);
            font-size: 13px;
        }

        .notice.success { color: var(--success); }
        .notice.error { color: #ff8787; }

        .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
        .field { display: grid; gap: 8px; }
        .field.full { grid-column: 1 / -1; }
        .field label { color: #c9d7e7; font-size: 13px; font-weight: 700; }

        .section {
            display: grid;
            gap: 14px;
        }

        .history {
            display: grid;
            gap: 12px;
        }

        .history-item {
            padding: 14px;
            border-radius: 16px;
            border: 1px solid var(--line);
            background: rgba(255,255,255,0.03);
        }

        .history-item strong { display: block; }
        .history-item span { display: block; color: var(--muted); margin-top: 4px; font-size: 13px; line-height: 1.5; }

        .loader {
            position: fixed;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            background: rgba(4, 12, 20, 0.62);
            z-index: 9999;
        }

        .loader .spinner {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            border: 4px solid rgba(255,255,255,0.18);
            border-top-color: #ffffff;
            animation: spin 0.9s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        @media (max-width: 1180px) {
            .shell { grid-template-columns: 1fr; }
            .sidebar { position: relative; top: 0; height: auto; }
            .layout { grid-template-columns: 1fr; }
            .editor-drawer { right: 18px; left: 18px; width: auto; top: 18px; max-height: calc(100vh - 36px); }
        }

        @media (max-width: 900px) {
            .stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .form-grid { grid-template-columns: 1fr; }
            .hero, .panel-head { flex-direction: column; align-items: flex-start; }
            .toolbar { justify-content: flex-start; }
            .editor-drawer .form-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 560px) {
            .shell { padding: 16px; }
            .stats { grid-template-columns: 1fr; }
            .hero, .panel, .overview-card, .task-card, .stat { padding: 16px; }
        }
    </style>
</head>
<body>
    <div id="hoot-loader" class="loader"><div class="spinner" aria-label="Loading"></div></div>

    <div class="shell">
        <aside class="sidebar">
            <div class="brand">
                <img src="{{ asset('New_logo.png') }}" alt="Smart Office">
                <div>
                    <strong>Smart Office</strong>
                    <span>My Workspace</span>
                </div>
            </div>

            <div class="profile">
                <div class="profile-label">Signed in</div>
                <div id="profileName" class="profile-name">Loading...</div>
                <div id="profileEmail" class="profile-email"></div>
            </div>

            <nav class="nav">
                <button type="button" class="active" data-view="tasks"><span>My Tasks</span><small id="taskCountLabel">0</small></button>
                <button type="button" data-view="profile"><span>Profile</span><small>Local</small></button>
            </nav>

            <button id="logoutButton" class="logout-btn" type="button">Logout</button>
        </aside>

        <main class="content">
            <header class="hero">
                <div>
                    <h1>My Tasks</h1>
                 
                </div>
                <div id="sessionPill" class="pill">Session ready</div>
            </header>

            <section id="statsPanel" class="grid stats">
                <div class="stat"><div class="label">Assigned</div><div id="assignedStat" class="value">0</div><div class="hint">Tasks currently assigned to you</div></div>
                <div class="stat"><div class="label">Due Soon</div><div id="dueSoonStat" class="value">0</div><div class="hint">Due within the next 3 days</div></div>
                <div class="stat"><div class="label">Overdue</div><div id="overdueStat" class="value">0</div><div class="hint">Past due date</div></div>
                <div class="stat"><div class="label">Updated</div><div id="updatedStat" class="value">0</div><div class="hint">Recently refreshed records</div></div>
            </section>

            <section id="dashboardLayout" class="layout">
                <section id="tasksPanel" class="panel">
                    <div class="panel-head">
                        <div>
                            <h2 class="panel-title">Assigned Tasks</h2>
                            <p class="panel-desc">Search and filter the tasks assigned to your account.</p>
                        </div>
                        <div class="toolbar" style="flex-wrap:nowrap;">
                            <input id="searchInput" class="search" type="search" placeholder="Search tasks by title..." style="flex:1; min-width:180px;">
                            <input id="fromDate" class="input" type="date" style="width:150px;">
                            <input id="toDate" class="input" type="date" style="width:150px;">
                            <button id="refreshButton" type="button" class="btn btn-secondary" aria-label="Refresh" title="Refresh" style="white-space:nowrap;"><span style="font-size:16px; line-height:1;">⟳</span></button>
                        </div>
                    </div>

                    <div id="taskNotice" class="notice" aria-live="polite"></div>
                    <div id="taskList" class="task-list"></div>
                </section>

                <section id="profileCard" class="panel profile-panel" style="display:none;">
                    <div class="profile-hero">
                        <div id="profileAvatar" class="profile-avatar">
                            <img id="profileAvatarImage" alt="Profile photo">
                            <span id="profileAvatarFallback">U</span>
                        </div>
                        <div class="profile-title">
                            <h2 id="profileFullName">User Profile</h2>
                            <p id="profileSummary">Complete account details will appear here.</p>
                        </div>
                        <div class="profile-actions">
                            <button id="profileEditButton" class="btn btn-secondary" type="button">Edit Profile</button>
                        </div>
                    </div>

                    <div id="profileDisplaySection" class="profile-grid">
                        <div class="profile-item profile-wide">
                            <div class="label">Account Status</div>
                            <div id="profileStatus" class="profile-status active">Active</div>
                        </div>
                        <div class="profile-item">
                            <div class="label">User ID</div>
                            <div id="profileUserId" class="value">-</div>
                        </div>
                        <div class="profile-item">
                            <div class="label">Full Name</div>
                            <div id="profileNameValue" class="value">-</div>
                        </div>
                        <div class="profile-item">
                            <div class="label">Email Address</div>
                            <div id="profileEmailValue" class="value">-</div>
                        </div>
                        <div class="profile-item">
                            <div class="label">Mobile Number</div>
                            <div id="profileMobile" class="value">-</div>
                        </div>
                        <div class="profile-item">
                            <div class="label">Role</div>
                            <div id="profileRole" class="value">-</div>
                        </div>
                        <div class="profile-item">
                            <div class="label">Department</div>
                            <div id="profileDepartment" class="value">-</div>
                        </div>
                        <div class="profile-item">
                            <div class="label">Last Login</div>
                            <div id="profileLastLogin" class="value">-</div>
                        </div>
                        <div class="profile-item">
                            <div class="label">Email Verified</div>
                            <div id="profileVerified" class="value">-</div>
                        </div>
                        <div class="profile-item">
                            <div class="label">Created At</div>
                            <div id="profileCreatedAt" class="value">-</div>
                        </div>
                        <div class="profile-item">
                            <div class="label">Updated At</div>
                            <div id="profileUpdatedAt" class="value">-</div>
                        </div>
                        <div class="profile-item profile-wide">
                            <div class="label">Access Notes</div>
                            <div id="profileNotes" class="value">-</div>
                        </div>
                    </div>

                    <form id="profileEditSection" class="profile-edit-form" autocomplete="off">
                        <div class="panel-head" style="margin-bottom:14px;">
                            <div>
                                <h2 class="panel-title" style="font-size:20px;">Edit Profile</h2>
                                <p class="panel-desc">Update your personal information and profile image.</p>
                            </div>
                        </div>

                        <div class="profile-form-grid">
                            <div class="field full">
                                <label for="profileNameInput">Full Name</label>
                                <input id="profileNameInput" class="input" name="name" type="text" placeholder="Full name">
                            </div>
                            <div class="field full">
                                <label for="profileEmailInput">Email Address</label>
                                <input id="profileEmailInput" class="input" name="email" type="email" placeholder="Email address">
                            </div>
                            <div class="field full">
                                <label for="profileMobileInput">Mobile Number</label>
                                <input id="profileMobileInput" class="input" name="mobile" type="text" placeholder="Mobile number">
                            </div>
                            <div class="field full">
                                <label for="profileImageInput">Profile Image</label>
                                <div style="display:flex; gap:10px; align-items:center;">
                                    <input id="profileImageInput" class="input" name="image_file" type="file" accept="image/*" style="flex:1;">
                                    <button id="uploadImageButton" type="button" class="btn btn-primary">Upload</button>
                                </div>
                                <small class="field-help">Choose a file and click Upload to store it on the server.</small>
                            </div>
                            <div class="field full">
                                <label for="profileImageUrlInput">Image URL</label>
                                <div style="display:flex; gap:10px; align-items:center;">
                                    <input id="profileImageUrlInput" class="input" name="image" type="text" placeholder="https://... or stored image path" style="flex:1;">
                                    <button id="pastePreviewButton" type="button" class="btn btn-secondary">Preview</button>
                                </div>
                                <small class="field-help">Or paste an image URL and click Preview, or leave blank to keep current image.</small>
                            </div>
                            <div class="field full">
                                <div class="profile-image-preview">
                                    <img id="profileImagePreview" alt="Selected profile preview" style="display:none;">
                                    <span id="profileImagePreviewText">Upload a file or paste an image URL to update your profile photo.</span>
                                </div>
                            </div>
                        </div>

                        <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top:16px;">
                            <button id="profileSaveButton" class="btn btn-primary" type="submit">Save Profile</button>
                            <button id="profileCancelButton" class="btn btn-secondary" type="button">Cancel</button>
                        </div>
                    </form>
                </section>
            </section>
        </main>
    </div>

    <div id="editorBackdrop" class="drawer-backdrop" aria-hidden="true"></div>

    <aside id="taskDrawer" class="panel editor-drawer" aria-hidden="true">
        <div class="editor-drawer-header">
            <div>
                <h2 id="drawerTitle" class="editor-drawer-title">Editing task</h2>
              
            </div>
            <button id="closeDrawerButton" class="btn btn-secondary" type="button">Close</button>
        </div>

        <form id="taskForm" autocomplete="off">
            <input type="hidden" id="recordId" name="id">
            <div class="form-grid">
                <div class="field full">
                    <label for="taskTitle">Title</label>
                    <input id="taskTitle" class="input" name="title" type="text" placeholder="Task title">
                </div>
                <div class="field full">
                    <label for="taskDescription">Description</label>
                    <textarea id="taskDescription" class="textarea" name="description" placeholder="Task description"></textarea>
                </div>
                <div class="field"><label for="startDate">Start Date</label><input id="startDate" class="input" name="start_date" type="date"></div>
                <div class="field"><label for="dueDate">Due Date</label><input id="dueDate" class="input" name="due_date" type="date"></div>
                <div class="field"><label for="priorityId">Priority</label><select id="priorityId" class="select" name="priority_id"></select></div>
                <div class="field"><label for="statusId">Task Status</label><select id="statusId" class="select" name="task_status_id"></select></div>
                <div class="field full"><label for="departmentId">Department</label><select id="departmentId" class="select" name="department_id"></select></div>
            </div>
            <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top:16px;">
                <button class="btn btn-primary" type="submit">Save Changes</button>
                <button id="resetButton" class="btn btn-secondary" type="button">Reset</button>
            </div>
        </form>
    </aside>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        const API_BASE = '{{ url('/api') }}';
        const state = {
            token: localStorage.getItem('smart-office-token') || '',
            user: safeParse(localStorage.getItem('smart-office-user')),
            tasks: [],
            priorities: [],
            statuses: [],
            departments: [],
            activeTaskId: null,
            view: 'tasks',
            drawerOpen: false,
            profileEditMode: false,
            searchTimer: null
        };

        function safeParse(value) {
            try { return value ? JSON.parse(value) : null; } catch (error) { return null; }
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function formatDate(value) {
            if (!value) return '-';
            const date = new Date(value);
            if (Number.isNaN(date.getTime())) return escapeHtml(value);
            return date.toLocaleDateString();
        }

        function formatDateTime(value) {
            if (!value) return '-';
            const date = new Date(value);
            if (Number.isNaN(date.getTime())) return escapeHtml(value);
            return date.toLocaleString();
        }

        function daysUntil(value) {
            if (!value) return null;
            const target = new Date(value);
            if (Number.isNaN(target.getTime())) return null;
            const diff = target.setHours(0,0,0,0) - new Date().setHours(0,0,0,0);
            return Math.round(diff / 86400000);
        }

        function setLoader(show) {
            $('#hoot-loader').css('display', show ? 'flex' : 'none');
        }

        function setNotice(text, type = '') {
            $('#taskNotice').removeClass('success error').addClass(type).text(text || '');
        }

        function apiHeaders() {
            const headers = { Accept: 'application/json' };
            if (state.token) headers.Authorization = `Bearer ${state.token}`;
            return headers;
        }

        async function apiRequest(endpoint, payload = {}) {
            try {
                const response = await $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: `${API_BASE}/${endpoint}`,
                    data: payload,
                    headers: apiHeaders()
                });

                if (response && response.status === 401) {
                    handleUnauthorized();
                }

                return response;
            } catch (xhr) {
                const response = xhr.responseJSON || {};
                if (xhr.status === 401) handleUnauthorized();
                throw new Error(extractError(response));
            }
        }

        async function apiFormRequest(endpoint, formData) {
            try {
                const response = await $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: `${API_BASE}/${endpoint}`,
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: apiHeaders()
                });

                if (response && response.status === 401) {
                    handleUnauthorized();
                }

                return response;
            } catch (xhr) {
                const response = xhr.responseJSON || {};
                if (xhr.status === 401) handleUnauthorized();
                throw new Error(extractError(response));
            }
        }

        function extractError(payload) {
            if (!payload) return 'Request failed. Please try again.';
            if (typeof payload === 'string') return payload;
            if (payload.error) {
                if (typeof payload.error === 'string') return payload.error;
                if (typeof payload.error === 'object') {
                    return Object.values(payload.error).flat().filter(Boolean).join(' ');
                }
            }
            if (payload.message) return payload.message;
            return 'Request failed. Please try again.';
        }

        function handleUnauthorized() {
            localStorage.removeItem('smart-office-token');
            localStorage.removeItem('smart-office-user');
            window.location.href = '/login';
        }

        function lookupLabel(list, keyField, labelField, id) {
            const found = (list || []).find(item => String(item[keyField]) === String(id));
            return found ? found[labelField] : id || '-';
        }

        function getProfileImageSource(value) {
            const source = String(value || '').trim();
            if (!source) return '';
            if (/^(https?:|data:|blob:)/i.test(source)) return source;
            if (source.startsWith('/')) return source;
            return `{{ asset('storage') }}/${source}`;
        }

        function setProfileEditMode(enabled) {
            state.profileEditMode = Boolean(enabled);
            $('#profileDisplaySection').toggle(!enabled);
            $('#profileEditSection').toggleClass('open', enabled);
            $('#profileEditButton').text(enabled ? 'View Profile' : 'Edit Profile');
            if (enabled) {
                $('#profileNameInput').trigger('focus');
            }
        }

        function isAdminSide() {
            const roleId = Number(state.user?.role_id || 0);
            return roleId === 1 || roleId === 2;
        }

        function renderProfile() {
            const user = state.user || {};
            const departmentName = lookupLabel(state.departments, 'department_id', 'name', user.department_id);
            const roleName = user.role_name || `Role #${user.role_id || '-'}`;
            const initials = String(user.name || 'U')
                .trim()
                .split(/\s+/)
                .filter(Boolean)
                .slice(0, 2)
                .map(part => part.charAt(0))
                .join('')
                .toUpperCase() || 'U';
            const active = Number(user.status ?? 1) === 1;
            const imageSource = getProfileImageSource(user.image);

            $('#profileName').text(user.name || 'User');
            $('#profileEmail').text(user.email || '');
            $('#sessionPill').text(isAdminSide() ? 'Admin access' : 'User access');

            if (imageSource) {
                $('#profileAvatarImage').attr('src', imageSource).show();
                $('#profileAvatarFallback').hide();
            } else {
                $('#profileAvatarImage').attr('src', '').hide();
                $('#profileAvatarFallback').text(initials).show();
            }
            $('#profileFullName').text(user.name || 'User Profile');
            $('#profileSummary').text(`${roleName} • ${departmentName || 'Department not assigned'} • ${user.email || 'No email available'}`);
            $('#profileUserId').text(user.user_id || '-');
            $('#profileNameValue').text(user.name || '-');
            $('#profileEmailValue').text(user.email || '-');
            $('#profileMobile').text(user.mobile || '-');
            $('#profileRole').text(roleName);
            $('#profileDepartment').text(departmentName || user.department_id || '-');
            $('#profileLastLogin').text(formatDateTime(user.last_login_at));
            $('#profileVerified').text(user.email_verified_at ? formatDateTime(user.email_verified_at) : 'Not verified');
            $('#profileCreatedAt').text(formatDateTime(user.created_at));
            $('#profileUpdatedAt').text(formatDateTime(user.updated_at));
            $('#profileNotes').text(active
                ? 'This account is active and can access the dashboard according to its assigned role.'
                : 'This account is currently inactive and may have limited or no access to dashboard features.');
            $('#profileStatus').removeClass('active inactive').addClass(active ? 'active' : 'inactive').text(active ? 'Active' : 'Inactive');

            $('#profileNameInput').val(user.name || '');
            $('#profileEmailInput').val(user.email || '');
            $('#profileMobileInput').val(user.mobile || '');
            $('#profileImageUrlInput').val(user.image || '');
            if (imageSource) {
                $('#profileImagePreview').attr('src', imageSource).show();
                $('#profileImagePreviewText').text('Current profile image preview. Choose a new file to replace it.');
            } else {
                $('#profileImagePreview').attr('src', '').hide();
                $('#profileImagePreviewText').text('No profile image is set yet. Upload a file or paste an image URL.');
            }
        }

        function populateSelect($select, items, keyField, labelField, includePlaceholder = true) {
            const placeholder = includePlaceholder ? '<option value="">Select...</option>' : '';
            const options = items.map(item => `<option value="${escapeHtml(item[keyField])}">${escapeHtml(item[labelField])}</option>`).join('');
            $select.html(placeholder + options);
        }

        async function loadLookups() {
            const [priorityResponse, statusResponse, departmentResponse] = await Promise.all([
                apiRequest('getallpriority', { limit: 500, offset: 0 }),
                apiRequest('getalltaskstatus', { limit: 500, offset: 0 }),
                apiRequest('getalldepartment', { limit: 500, offset: 0 })
            ]);

            state.priorities = priorityResponse.data || [];
            state.statuses = statusResponse.data || [];
            state.departments = departmentResponse.data || [];

            populateSelect($('#priorityId'), state.priorities, 'priority_id', 'title');
            populateSelect($('#statusId'), state.statuses, 'task_status_id', 'title');
            populateSelect($('#departmentId'), state.departments, 'department_id', 'name');
        }

        async function loadTasks() {
            const search = $('#searchInput').val().trim();
            const fromDate = $('#fromDate').val();
            const toDate = $('#toDate').val();

            const payload = { limit: 100, offset: 0 };
            if (search) payload.search = search;
            if (fromDate) payload.from_date = fromDate;
            if (toDate) payload.to_date = toDate;

            const response = await apiRequest('getalltask', payload);
            state.tasks = response.data || [];
            renderTasks();
            updateTaskStats();
        }

        function updateTaskStats() {
            const assigned = state.tasks.length;
            const dueSoon = state.tasks.filter(task => {
                const days = daysUntil(task.due_date);
                return days !== null && days >= 0 && days <= 3;
            }).length;
            const overdue = state.tasks.filter(task => {
                const days = daysUntil(task.due_date);
                return days !== null && days < 0;
            }).length;
            const updated = state.tasks.filter(task => task.updated_at || task.created_at).length;

            $('#assignedStat').text(assigned);
            $('#dueSoonStat').text(dueSoon);
            $('#overdueStat').text(overdue);
            $('#updatedStat').text(updated);
            $('#taskCountLabel').text(String(assigned));
        }

        async function submitProfile() {
            const formData = new FormData();
            formData.append('name', $('#profileNameInput').val().trim());
            formData.append('email', $('#profileEmailInput').val().trim());
            formData.append('mobile', $('#profileMobileInput').val().trim());

            const imageFile = $('#profileImageInput')[0]?.files?.[0];
            const imageUrl = ($('#profileImageUrlInput').val() || '').trim();

            // Only send image URL if it's non-empty; empty string can trigger server-side validation issues.
            if (imageFile) {
                formData.append('image_file', imageFile);
                // If user selected a file, don't also send image URL.
            } else if (imageUrl) {
                formData.append('image', imageUrl);
            }


            try {
                setLoader(true);
                $('#profileSaveButton').prop('disabled', true).text('Saving...');
                const response = await apiFormRequest('updateprofile', formData);
                if (response.status !== 200) {
                    throw new Error(extractError(response));
                }

                if (response.data) {
                    state.user = response.data;
                    localStorage.setItem('smart-office-user', JSON.stringify(response.data));
                }

                renderProfile();
                setProfileEditMode(false);
                setNotice('Profile updated successfully.', 'success');
            } catch (error) {
                setNotice(error.message, 'error');
            } finally {
                $('#profileSaveButton').prop('disabled', false).text('Save Profile');
                setLoader(false);
            }
        }

        function getStatusBadge(task) {
            const statusLabel = lookupLabel(state.statuses, 'task_status_id', 'title', task.task_status_id);
            const statusText = String(statusLabel || '').toLowerCase();

            if (statusText.includes('complete') || statusText.includes('done') || statusText.includes('closed')) {
                return `<span class="badge ok">${escapeHtml(statusLabel)}</span>`;
            }

            if (statusText.includes('progress') || statusText.includes('doing') || statusText.includes('review')) {
                return `<span class="badge warn">${escapeHtml(statusLabel)}</span>`;
            }

            return `<span class="badge muted">${escapeHtml(statusLabel)}</span>`;
        }

        function renderTasks() {
            if (!state.tasks.length) {
                $('#taskList').html('<div class="task-card"><div class="task-desc">No assigned tasks found.</div></div>');
                return;
            }

            const html = state.tasks.map(task => {
                const days = daysUntil(task.due_date);
                let deadlineBadge = '<span class="badge muted">No deadline</span>';
                if (days !== null) {
                    if (days < 0) deadlineBadge = `<span class="badge danger">Overdue by ${Math.abs(days)} day(s)</span>`;
                    else if (days === 0) deadlineBadge = '<span class="badge warn">Due today</span>';
                    else if (days <= 3) deadlineBadge = `<span class="badge warn">Due in ${days} day(s)</span>`;
                    else deadlineBadge = `<span class="badge muted">Due in ${days} day(s)</span>`;
                }

                const active = String(state.activeTaskId) === String(task.task_id) ? 'active' : '';
                return `
                    <article class="task-card ${active}" data-task-id="${escapeHtml(task.task_id)}">
                        <div class="task-top">
                            <div>
                                <h3 class="task-title">${escapeHtml(task.title)}</h3>
                                <div class="task-meta">
                                    ${getStatusBadge(task)}
                                    ${deadlineBadge}
                                    <span class="badge muted">${escapeHtml(lookupLabel(state.priorities, 'priority_id', 'title', task.priority_id))}</span>
                                </div>
                            </div>
                            <div style="display:grid; gap:8px; justify-items:end;">
                                <span class="badge muted">#${escapeHtml(task.task_id)}</span>
<button type="button" class="btn btn-secondary icon-mini task-edit-button" aria-label="Edit" data-edit-task="${escapeHtml(task.task_id)}">
                                    <svg viewBox="0 0 24 24" role="img" aria-hidden="true">
                                        <path d="M3 17.25V21h3.75L19.81 7.94l-3.75-3.75L3 17.25z" />
                                        <path d="M14.06 4.19l3.75 3.75" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <p class="task-desc">${escapeHtml(task.description)}</p>
                        <div class="task-footer">
                            <span>Start: ${escapeHtml(formatDate(task.start_date))}</span>
                            <span>Due: ${escapeHtml(formatDate(task.due_date))}</span>
                            <span>${escapeHtml(lookupLabel(state.departments, 'department_id', 'name', task.department_id))}</span>
                            <span>Assigned by: ${escapeHtml(task.assigned_by_name || task.assigned_by || '-')}</span>
                        </div>
                    </article>
                `;
            }).join('');

            $('#taskList').html(html);

            $('#taskList .task-card').off('click').on('click', function () {
                const taskId = $(this).data('task-id');
                selectTask(taskId);
            });

            $('#taskList .task-edit-button').off('click').on('click', function (event) {
                event.stopPropagation();
                const taskId = $(this).data('edit-task');
                selectTask(taskId, true);
            });
        }

        function openTaskDrawer(taskId) {
            const task = state.tasks.find(item => String(item.task_id) === String(taskId));
            if (!task) return;

            state.drawerOpen = true;
        // Runtime enforcement: ensure stat/task cards are visibly styled
        document.addEventListener('DOMContentLoaded', function () {
            const apply = (els) => {
                els.forEach(el => {
                    try {
                        el.style.setProperty('background', '#ffffff', 'important');
                        el.style.setProperty('border', '1px solid rgba(11,37,64,0.08)', 'important');
                        el.style.setProperty('box-shadow', '0 18px 48px rgba(11,37,64,0.12)', 'important');
                        el.style.setProperty('padding', '18px', 'important');
                        el.style.setProperty('min-height', '96px', 'important');
                    } catch(e){}
                });
            };

            apply(Array.from(document.querySelectorAll('#statsPanel .stat')));
            apply(Array.from(document.querySelectorAll('.task-card')));
        });
            $('#drawerTitle').text(`Editing task #${task.task_id}`);
           // $('#drawerNote').text('Update the selected task in the side panel, then save the changes.');
            $('#taskDrawer').addClass('open').attr('aria-hidden', 'false');
            $('#editorBackdrop').addClass('visible').attr('aria-hidden', 'false');
            $('body').addClass('drawer-open');
            // If non-admin, focus the status select instead
            if (!isAdminSide()) {
                $('#statusId').trigger('focus');
            } else {
                $('#taskTitle').trigger('focus');
            }
        }

        function closeTaskDrawer() {
            state.drawerOpen = false;
            $('#taskDrawer').removeClass('open').attr('aria-hidden', 'true');
            $('#editorBackdrop').removeClass('visible').attr('aria-hidden', 'true');
            $('body').removeClass('drawer-open');
        }

        function selectTask(taskId, shouldFocusForm = false) {
            const task = state.tasks.find(item => String(item.task_id) === String(taskId));
            if (!task) return;
            state.activeTaskId = task.task_id;
            $('#recordId').val(task.task_id);
            $('#taskTitle').val(task.title || '');
            $('#taskDescription').val(task.description || '');
            $('#startDate').val((task.start_date || '').split(' ')[0] || '');
            $('#dueDate').val((task.due_date || '').split(' ')[0] || '');
            $('#priorityId').val(task.priority_id || '');
            $('#statusId').val(task.task_status_id || '');
            $('#departmentId').val(task.department_id || '');
            $('.task-card').removeClass('active');
            $(`.task-card[data-task-id="${task.task_id}"]`).addClass('active');
            setNotice(`Editing task #${task.task_id}.`, '');

            // Make fields view-only for non-admin users; they may only change task status
            const admin = isAdminSide();
            $('#taskTitle').prop('readonly', !admin);
            $('#taskDescription').prop('readonly', !admin);
            $('#startDate').prop('disabled', !admin);
            $('#dueDate').prop('disabled', !admin);
            $('#priorityId').prop('disabled', !admin);
            // status select should always be enabled for assigned users
            $('#statusId').prop('disabled', false);
            $('#departmentId').prop('disabled', !admin);

            // Update drawer note for non-admins
           // if (!admin) {
             //   $('#drawerNote').text('You can only update the task status. Other fields are view-only.');
            //} else {
                //$('#drawerNote').text('Update the selected task in the side panel, then save the changes.');
           // }

            openTaskDrawer(task.task_id);
        }

        function resetForm() {
            $('#taskForm')[0].reset();
            $('#recordId').val('');
            state.activeTaskId = null;
            $('.task-card').removeClass('active');
            setNotice('', '');
            closeTaskDrawer();
        }

        async function submitTask() {
            const id = $('#recordId').val();
            if (!id) {
                setNotice('Select a task first.', 'error');
                return;
            }

            // Non-admin users are allowed to change only task_status_id. Admins may send full payload.
            let payload = { id, task_status_id: $('#statusId').val() };
            if (isAdminSide()) {
                payload = {
                    id,
                    title: $('#taskTitle').val().trim(),
                    description: $('#taskDescription').val().trim(),
                    start_date: $('#startDate').val(),
                    due_date: $('#dueDate').val(),
                    priority_id: $('#priorityId').val(),
                    task_status_id: $('#statusId').val(),
                    department_id: $('#departmentId').val()
                };
            }

            try {
                setLoader(true);
                setNotice('Saving task...', '');
                const response = await apiRequest('updatetask', payload);
                if (response.status !== 200) {
                    throw new Error(extractError(response));
                }

                setNotice('Task updated successfully.', 'success');
                await loadTasks();
                await loadHistory();
            } catch (error) {
                setNotice(error.message, 'error');
            } finally {
                setLoader(false);
            }
        }

        function activateView(view) {
            state.view = view;
            $('.nav button').removeClass('active');
            $(`.nav button[data-view="${view}"]`).addClass('active');

            const isProfileView = view === 'profile';
            $('#statsPanel').toggle(!isProfileView);
            $('#tasksPanel').toggle(view === 'tasks');
            $('#profileCard').toggle(isProfileView);

            if (view !== 'tasks') {
                closeTaskDrawer();
            }

            if (isProfileView) {
                setProfileEditMode(false);
                $('#profileCard')[0]?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }

        function extractError(payload) {
            if (!payload) return 'Request failed. Please try again.';
            if (typeof payload === 'string') return payload;
            if (payload.error) {
                if (typeof payload.error === 'string') return payload.error;
                if (typeof payload.error === 'object') {
                    return Object.values(payload.error).flat().filter(Boolean).join(' ');
                }
            }
            if (payload.message) return payload.message;
            return 'Request failed. Please try again.';
        }

        function handleUnauthorized() {
            localStorage.removeItem('smart-office-token');
            localStorage.removeItem('smart-office-user');
            window.location.href = '/login';
        }

        async function init() {
            if (!state.token) {
                window.location.href = '/login';
                return;
            }

            renderProfile();

            if (isAdminSide()) {
                window.location.href = '/dashboard';
                return;
            }

            $('.nav button[data-view]').on('click', function () {
                activateView($(this).data('view'));
            });
            $('#profileEditButton').on('click', function () {
                setProfileEditMode(!state.profileEditMode);
            });
            $('#profileCancelButton').on('click', function () {
                renderProfile();
                setProfileEditMode(false);
            });
            $('#logoutButton').on('click', async function () {
                try {
                    setLoader(true);
                    await apiRequest('logout', {});
                } catch (error) {
                    // ignore logout errors
                } finally {
                    localStorage.removeItem('smart-office-token');
                    localStorage.removeItem('smart-office-user');
                    window.location.href = '/login';
                }
            });

            try {
                setLoader(true);
                await loadLookups();
                renderProfile();
                await loadTasks();
                activateView('tasks');
                $('#searchInput').on('input', function () {
                    clearTimeout(state.searchTimer);
                    state.searchTimer = setTimeout(loadTasks, 250);
                });
                $('#fromDate, #toDate').on('change', loadTasks);
                $('#refreshButton').on('click', async function () {
                    setLoader(true);
                    try {
                        await loadTasks();
                    } finally {
                        setLoader(false);
                    }
                });
                $('#taskForm').on('submit', function (event) {
                    event.preventDefault();
                    submitTask();
                });
                $('#resetButton').on('click', resetForm);
                $('#closeDrawerButton, #editorBackdrop').on('click', function () {
                    resetForm();
                });
                $('#profileImageInput').on('change', function () {
                    const file = this.files && this.files[0];
                    if (!file) {
                        renderProfile();
                        return;
                    }

                    const previewUrl = URL.createObjectURL(file);
                    $('#profileImagePreview').attr('src', previewUrl).show();
                    $('#profileImagePreviewText').text(file.name);
                    $('#profileAvatarImage').attr('src', previewUrl).show();
                    $('#profileAvatarFallback').hide();
                });
                $('#profileImageUrlInput').on('input', function () {
                    const source = getProfileImageSource($(this).val().trim());
                    if (source) {
                        $('#profileImagePreview').attr('src', source).show();
                        $('#profileImagePreviewText').text('Image preview from URL.');
                        $('#profileAvatarImage').attr('src', source).show();
                        $('#profileAvatarFallback').hide();
                    } else {
                        renderProfile();
                    }
                });
                $('#profileEditSection').on('submit', function (event) {
                    event.preventDefault();
                    submitProfile();
                });
            } catch (error) {
                setNotice(error.message, 'error');
                if (error.message.toLowerCase().includes('unauthorized')) {
                    handleUnauthorized();
                }
            } finally {
                setLoader(false);
            }
        }

        $(document).ready(function () {
            init();
        });
    </script>
</body>
</html>
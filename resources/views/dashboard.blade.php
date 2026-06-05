<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Smart Office Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f4f9ff;
            --bg-soft: #ffffff;
            --panel: #ffffff;
            --panel-strong: #ffffff;
            --line: rgba(31,111,224,0.08);
            --text: #0b2540;
            --muted: #567089;
            --brand: #1f6fe0;
            --brand-2: #0f57c6;
            --brand-variant: rgba(31,111,224,0.12);
            --danger: #d9534f;
            --warning: #f0ad4e;
            --success: #28a745;
            --shadow: 0 10px 30px rgba(16,40,80,0.06);
            --soft-shadow: 0 6px 18px rgba(16,40,80,0.04);
        }

        * { box-sizing: border-box; }

        html, body { height: 100%; margin: 0; }

        body {
            font-family: Inter, sans-serif;
            color: var(--text);
            color-scheme: light;
            background: var(--bg);
            overflow-x: hidden;
        }

        body.drawer-open {
            overflow: hidden;
        }

        /* No decorative overlays for minimal professional look */

        button, input, select, textarea { font: inherit; }

        .shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 290px 1fr;
        }

        .sidebar {
            position: sticky;
            top: 0;
            height: 100vh;
            padding: 26px 18px;
            border-right: 1px solid var(--line);
            background: var(--panel);
            box-shadow: var(--shadow);
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 10px 18px;
            border-bottom: 1px solid var(--line);
            color: var(--text);
        }

        .brand img {
            width: 44px;
            height: 44px;
            object-fit: contain;
        }

        .brand strong {
            display: block;
            font-family: 'Space Grotesk', sans-serif;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            font-size: 15px;
        }

        .brand span {
            display: block;
            color: var(--muted);
            font-size: 12px;
            margin-top: 3px;
        }

        .profile {
            padding: 16px;
            border: 1px solid var(--line);
            border-radius: 14px;
            background: var(--panel);
            box-shadow: var(--soft-shadow);
        }

        .profile-label { color: var(--muted); font-size: 12px; text-transform: uppercase; letter-spacing: 0.12em; }
        .profile-name { margin-top: 8px; font-weight: 700; }
        .profile-email { margin-top: 4px; color: var(--muted); font-size: 13px; word-break: break-all; }

        .nav {
            display: grid;
            gap: 8px;
            overflow-y: auto;
            padding-right: 4px;
        }

        .nav button,
        .logout-btn {
            width: 100%;
            border: 1px solid transparent;
            background: transparent;
            color: var(--text);
            text-align: left;
            padding: 12px 14px;
            border-radius: 14px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: 0.2s ease;
        }

        .nav button:hover,
        .nav button.active {
            background: var(--brand-variant);
            border-color: rgba(43,125,233,0.18);
            color: var(--brand);
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(43,125,233,0.06);
        }

        .nav small {
            color: var(--muted);
            font-size: 11px;
        }

        .logout-btn {
            margin-top: auto;
            justify-content: center;
            background: #fff;
            border-color: rgba(217,83,79,0.06);
            color: var(--danger);
            font-weight: 700;
            box-shadow: 0 6px 16px rgba(217,83,79,0.04);
        }

        .content {
            padding: 28px;
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

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 18px;
            margin-bottom: 20px;
        }

        .hero h1 {
            margin: 0;
            font-family: 'Space Grotesk', sans-serif;
            font-size: clamp(28px, 4vw, 42px);
            line-height: 1.05;
        }

        .hero p {
            margin: 8px 0 0;
            color: var(--muted);
        }

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

        /* build-badge removed */

        .grid {
            display: grid;
            gap: 18px;
        }

        .stats {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        /* Strong, forced card styles to ensure visibility */
        .stat {
            padding: 18px !important;
            border: 1px solid rgba(11,37,64,0.08) !important;
            border-radius: 14px !important;
            background: #ffffff !important;
            box-shadow: 0 18px 48px rgba(11,37,64,0.12) !important;
            transition: transform 0.12s ease, box-shadow 0.12s ease !important;
            min-height: 96px !important;
        }
        .stat:hover { transform: translateY(-6px) !important; box-shadow: 0 22px 56px rgba(11,37,64,0.16) !important; }

        .stat .label { color: var(--muted); font-size: 12px; letter-spacing: 0.1em; text-transform: uppercase; }
        .stat .value { margin-top: 8px; font-size: 30px; font-weight: 800; font-family: 'Space Grotesk', sans-serif; }
        .stat .hint { margin-top: 6px; color: var(--muted); font-size: 13px; }

        .notice {
            min-height: 22px;
            margin: 10px 0 0;
            color: var(--muted);
            font-size: 13px;
        }

        .notice.success { color: var(--success); }
        .notice.error { color: #ff8787; }

        .panel {
            padding: 22px;
            border: 1px solid var(--line);
            border-radius: 14px;
            background: var(--panel);
            box-shadow: var(--soft-shadow);
            transition: box-shadow 0.12s ease, transform 0.12s ease;
        }
        .panel:hover { box-shadow: var(--shadow); transform: translateY(-4px); }

        .panel-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 18px;
            margin-bottom: 18px;
        }

        .panel-title {
            margin: 0 0 6px 0;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 26px;
            color: var(--text);
        }
        .panel-title::after { content: ''; display: block; margin-top: 10px; width: 56px; height: 3px; background: var(--brand); border-radius: 3px; }

        .panel-desc { margin: 6px 0 0; color: var(--muted); line-height: 1.6; }

        .toolbar {
            display: flex;
            gap: 10px;
            flex-wrap: nowrap;
            align-items: center;
        }

        .search,
        .control,
        .textarea,
        .select {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: #f4f8ff;
            color: var(--text);
            padding: 10px 12px;
            outline: none;
            transition: box-shadow 0.12s ease, border-color 0.12s ease;
        }
        .search:focus, .control:focus, .textarea:focus, .select:focus { box-shadow: 0 6px 18px rgba(43,125,233,0.06); border-color: var(--brand); }

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

        .search { min-width: 250px; }
        .textarea { min-height: 110px; resize: vertical; }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 18px;
        }

        .field { display: grid; gap: 8px; }
        .field label { color: var(--muted); font-size: 13px; font-weight: 700; letter-spacing: 0.02em; }
        .field.full { grid-column: 1 / -1; }

        .actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 4px;
        }

        .btn {
            border: 0;
            border-radius: 10px;
            padding: 10px 14px;
            cursor: pointer;
            font-weight: 700;
            transition: transform 0.12s ease, box-shadow 0.12s ease;
        }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 8px 22px rgba(43,125,233,0.08); }
.btn-primary { color: var(--text); background: #fff; border: 1px solid var(--line); box-shadow: 0 8px 22px rgba(43,125,233,0.08); }
        .btn-primary:active { transform: translateY(0); }
        .btn-secondary { color: var(--text); background: #fff; border: 1px solid var(--line); }
        .btn-danger { color: #fff; background: var(--danger); }

        .table-wrap { overflow: auto; border-radius: 18px; border: 1px solid var(--line); }
        table { width: 100%; border-collapse: collapse; min-width: 920px; }
        thead th {
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted);
            background: #f4f8ff;
            padding: 12px;
        }

        tbody td {
            padding: 12px;
            border-top: 1px solid rgba(43,125,233,0.06);
            vertical-align: top;
            color: var(--text);
        }

        .empty {
            padding: 24px;
            color: var(--muted);
            text-align: center;
        }

        .row-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

.mini {
            padding: 8px 10px;
            border-radius: 8px;
            border: 1px solid var(--line);
            background: #f4f8ff;
            color: var(--text);
            cursor: pointer;
            font-size: 12px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .icon-mini {
            width: 36px;
            height: 32px;
            padding: 0;
        }

        .icon-mini svg {
            width: 16px;
            height: 16px;
            display: block;
        }

        .icon-mini svg * {
            stroke: #0b2540;
            fill: none;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
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
        .badge.muted { background: rgba(31,111,224,0.04); color: var(--text); }

        .pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-top: 16px;
            flex-wrap: wrap;
        }

        .pagination .meta { color: var(--muted); font-size: 13px; }

        .pagination .buttons { display: flex; gap: 10px; flex-wrap: wrap; }

        .editor-layout {
            display: block;
        }

        .editor-drawer {
            display: none;
            position: fixed;
            top: 24px;
            right: 24px;
            width: min(500px, calc(100vw - 48px));
            max-height: calc(100vh - 48px);
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
            font-size: 22px;
        }

        .editor-drawer-note {
            margin: 6px 0 0;
            color: var(--muted);
            line-height: 1.6;
            font-size: 13px;
        }

        .editor-drawer-empty {
            min-height: 220px;
            display: grid;
            place-items: center;
            text-align: center;
            border: 1px dashed var(--line);
            border-radius: 18px;
            padding: 18px;
            color: var(--muted);
            background: rgba(255,255,255,0.03);
        }

        .field-help {
            color: var(--muted);
            font-size: 12px;
            line-height: 1.4;
            margin-top: -2px;
        }

        .editor-drawer .form-grid {
            gap: 16px;
            margin-bottom: 16px;
        }

        .editor-drawer .field {
            align-content: start;
        }

        .editor-drawer .field label {
            margin-bottom: 0;
        }

        .editor-drawer .control,
        .editor-drawer .select,
        .editor-drawer .textarea {
            min-height: 52px;
            font-size: 15px;
            line-height: 1.3;
        }

        .editor-drawer .select {
            padding-right: 40px;
        }

        .editor-drawer .textarea {
            min-height: 128px;
        }

        .editor-drawer .field-help {
            margin-top: 0;
        }

        .editor-drawer .actions {
            margin-top: 10px;
        }

        .editor-drawer .btn {
            min-height: 48px;
            padding: 12px 18px;
        }

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

        .module-view { display: none; }
        .module-view.active { display: block; }

        .overview-card {
            padding: 20px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: var(--panel);
            box-shadow: var(--shadow);
        }

        .overview-card h2 {
            margin: 0;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 24px;
        }

        .overview-card p { margin: 8px 0 0; color: var(--muted); line-height: 1.6; }

        @media (max-width: 1120px) {
            .shell { grid-template-columns: 1fr; }
            .sidebar {
                height: auto;
                position: relative;
                border-right: 0;
                border-bottom: 1px solid var(--line);
            }
        }

        @media (max-width: 900px) {
            .stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .form-grid { grid-template-columns: 1fr; }
            .topbar, .panel-head { flex-direction: column; align-items: flex-start; }
            .search { min-width: 0; width: 100%; }
            .editor-drawer { right: 18px; left: 18px; width: auto; top: 18px; max-height: calc(100vh - 36px); }
            .editor-drawer .form-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 560px) {
            .content { padding: 18px; }
            .stats { grid-template-columns: 1fr; }
            .panel, .overview-card, .stat { border-radius: 18px; }
        }
    </style>
</head>
<body>
    <div id="hoot-loader" class="loader" aria-hidden="true"><div class="spinner" aria-label="Loading"></div></div>

    <div class="shell">
        <aside class="sidebar">
            <div class="brand">
                <img src="{{ asset('New_logo.png') }}" alt="Smart Office">
                <div>
                    <strong>Smart Office</strong>
                    <span>Operations Console</span>
                </div>
            </div>

            <div class="profile">
                <div class="profile-label">Signed in</div>
                <div id="profileName" class="profile-name">Loading...</div>
                <div id="profileEmail" class="profile-email"></div>
            </div>

            <nav id="sidebarNav" class="nav"></nav>

            <button id="logoutButton" class="logout-btn" type="button">Logout</button>
        </aside>

        <main class="content">
            <header class="topbar">
                <div class="hero">
                    <h1 id="pageTitle">Dashboard</h1>
                   
                </div>
                <div id="sessionPill" class="pill">Session ready</div>
            </header>

            <section id="notice" class="notice" aria-live="polite"></section>

            <section id="moduleRoot"></section>
        </main>
    </div>

    <div id="editorBackdrop" class="drawer-backdrop" aria-hidden="true"></div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        const API_BASE = '{{ url('/api') }}';
        const state = {
            token: localStorage.getItem('smart-office-token') || '',
            user: safeParse(localStorage.getItem('smart-office-user')),
            activeModule: 'overview',
            lookups: {},
            taskAssignees: [],
            editingId: {},
            drawerModule: null,
            drawerMode: null,
            counts: {},
            pages: {},
            search: {},
            records: {}
        };

        const lookupSources = {
            departments: 'getalldepartment',
            roles: 'getallrole',
            priorities: 'getallpriority',
            taskStatuses: 'getalltaskstatus',
            users: 'getalluser',
            tasks: 'getalltask'
        };

        const moduleOrder = ['overview', 'departments', 'roles', 'priorities', 'taskStatuses', 'users', 'tasks', 'taskStatusLogs', 'documents'];

        function isSuperAdmin() {
            const roleId = Number(state.user?.role_id || 0);
            const roleName = String(state.user?.role_name || '').toLowerCase().trim();
            return roleId === 1 || roleId === 2 || roleName.includes('admin');
        }

        function getVisibleModuleOrder() {
            if (isSuperAdmin()) {
                return moduleOrder;
            }

            return ['overview', 'tasks', 'taskStatusLogs', 'documents'];
        }

        const moduleLookupMap = {
            users: ['roles', 'departments'],
            tasks: ['priorities', 'taskStatuses', 'departments'],
            taskStatusLogs: ['tasks', 'users', 'taskStatuses', 'departments'],
            documents: ['tasks']
        };

        const modules = {
            overview: { title: 'Dashboard', subtitle: 'System overview and quick entry points.' },
            departments: {
                title: 'Departments', endpoint: 'department', list: 'getalldepartment', create: 'adddepartment', update: 'updatedepartment', destroy: 'deletedepartment', pageSize: 10,

                fields: [
                    { name: 'name', label: 'Name', type: 'text', required: true },
                    { name: 'description', label: 'Description', type: 'textarea' }
                ],
                columns: [
                    { key: 'department_id', label: 'ID' },
                    { key: 'name', label: 'Name' },
                    { key: 'description', label: 'Description', fallback: '-' }
                ]
            },
            
            roles: {
                title: 'Roles', endpoint: 'role', list: 'getallrole', create: 'addrole', update: 'updaterole', destroy: 'deleterole', pageSize: 5,
                searchable: false,
                fields: [
                    { name: 'name', label: 'Name', type: 'text', required: true },
                    { name: 'description', label: 'Description', type: 'textarea' }
                ],
                columns: [
                    { key: 'role_id', label: 'ID' },
                    { key: 'name', label: 'Name' },
                    { key: 'description', label: 'Description', fallback: '-' }
                ]
            },
            priorities: {
                title: 'Priorities',endpoint: 'priority', list: 'getallpriority', create: 'addpriority', update: 'updatepriority', destroy: 'deletepriority', pageSize: 10,
                searchable: false,
                fields: [
                    { name: 'title', label: 'Title', type: 'text', required: true },
                    { name: 'level', label: 'Level', type: 'text', required: true }
                ],
                columns: [
                    { key: 'priority_id', label: 'ID' },
                    { key: 'title', label: 'Title' },
                    { key: 'level', label: 'Level' }
                ]
            },
            taskStatuses: {
                title: 'Task Statuses',  endpoint: 'taskstatus', list: 'getalltaskstatus', create: 'addtaskstatus', update: 'updatetaskstatus', destroy: 'deletetaskstatus', pageSize: 5,
                searchable: false,
                fields: [
                    { name: 'title', label: 'Title', type: 'text', required: true },
                    { name: 'description', label: 'Description', type: 'textarea' }
                ],
                columns: [
                    { key: 'task_status_id', label: 'ID' },
                    { key: 'title', label: 'Title' },
                    { key: 'description', label: 'Description', fallback: '-' }
                ]
            },
            users: {
                title: 'Users',  endpoint: 'user', list: 'getalluser', create: 'adduser', update: 'updateuser', destroy: 'deleteuser', pageSize: 5,
                searchable: true,
                fields: [
                    { name: 'name', label: 'Name', type: 'text', required: true },
                    { name: 'email', label: 'Email', type: 'email', required: true },
                    { name: 'password', label: 'Password', type: 'password', required: true, placeholder: 'Leave blank to keep current password' },
                    { name: 'role_id', label: 'Role', type: 'select', lookup: 'roles', required: true },
                    { name: 'department_id', label: 'Department', type: 'select', lookup: 'departments', required: true },
                    { name: 'mobile', label: 'Mobile', type: 'text' },
                    { name: 'image', label: 'Image URL', type: 'text' }
                ],
                columns: [
                    { key: 'user_id', label: 'ID' },
                    { key: 'name', label: 'Name' },
                    { key: 'email', label: 'Email' },
                    { key: 'role_name', label: 'Role', fallback: '-' },
                    { key: 'department_name', label: 'Department', fallback: '-' },
                    { key: 'mobile', label: 'Mobile', fallback: '-' }
                ]
            },
            tasks: {
                title: 'Tasks',  endpoint: 'task', list: 'getalltask', create: 'createtask', update: 'updatetask', destroy: 'deletetask', pageSize: 5,
                searchable: true,
                fields: [
                    { name: 'title', label: 'Title', type: 'text', required: true },
                    { name: 'description', label: 'Description', type: 'textarea', required: true },
                    { name: 'start_date', label: 'Start Date', type: 'date', required: true },
                    { name: 'due_date', label: 'Due Date', type: 'date', required: true },
                    { name: 'priority_id', label: 'Priority', type: 'select', lookup: 'priorities', required: true },
                    { name: 'task_status_id', label: 'Status', type: 'select', lookup: 'taskStatuses', required: true },
                    { name: 'department_id', label: 'Department', type: 'select', lookup: 'departments', required: true },
                    { name: 'assigned_user_ids', label: 'Assigned Users', type: 'multi-select', createOnly: true, required: true }
                ],
                columns: [
                    { key: 'task_id', label: 'ID' },
                    { key: 'title', label: 'Title' },
                    { key: 'start_date', label: 'Start', render: row => formatDate(row.start_date) },
                    { key: 'due_date', label: 'Due', render: row => formatDate(row.due_date) },
                    { key: 'priority_id', label: 'Priority', lookup: 'priorities' },
                    { key: 'task_status_id', label: 'Status', lookup: 'taskStatuses' },
                    { key: 'department_id', label: 'Department', lookup: 'departments' }
                ]
            },
            taskStatusLogs: {
                title: 'Task Status Logs', endpoint: 'taskstatuslog', list: 'getalltaskstatuslog', create: null, update: 'updatetaskstatuslog', destroy: 'deletetaskstatuslog', pageSize: 5,
                searchable: false,
                fields: [
                    { name: 'task_id', label: 'Task', type: 'select', lookup: 'tasks', required: true, lockOnEdit: true },
                    { name: 'assigned_by', label: 'Assigned By', type: 'select', lookup: 'users' },
                    { name: 'from_status_id', label: 'From Status', type: 'select', lookup: 'taskStatuses' },
                    { name: 'to_status_id', label: 'To Status', type: 'select', lookup: 'taskStatuses' },
                    { name: 'changed_by', label: 'Changed By', type: 'select', lookup: 'users', required: true },
                    { name: 'department_id', label: 'Department', type: 'select', lookup: 'departments', required: true },
                    { name: 'remarks', label: 'Remarks', type: 'textarea' }
                ],
                columns: [
                    { key: 'status_log_id', label: 'Log ID' },
                    { key: 'task_id', label: 'Task', lookup: 'tasks' },
                    { key: 'department_name', label: 'Department' },
                    { key: 'from_status_name', label: 'From Status', fallback: '-' },
                    { key: 'to_status_name', label: 'To Status', fallback: '-' },
                    { key: 'changed_by_name', label: 'Changed By', fallback: '-' }
                ]
            },
            documents: {
                title: 'Documents', endpoint: 'document', list: 'getalldocument', create: 'adddocument', update: 'updatedocument', destroy: 'deletedocument', pageSize: 5,
                searchable: false,
                fields: [
                    { name: 'task_id', label: 'Task', type: 'select', lookup: 'tasks', required: true },
                    { name: 'title', label: 'Title', type: 'text', required: true },
                    { name: 'file_name', label: 'File Name', type: 'text', required: true },
                    { name: 'file_path', label: 'File Path', type: 'text', required: true },
                    { name: 'mime_type', label: 'Mime Type', type: 'text', required: true },
                    { name: 'file_size', label: 'File Size', type: 'number' }
                ],
                columns: [
                    { key: 'document_id', label: 'ID' },
                    { key: 'task_id', label: 'Task', lookup: 'tasks' },
                    { key: 'title', label: 'Title' },
                    { key: 'file_name', label: 'File Name' },
                    { key: 'mime_type', label: 'Mime Type' },
                    { key: 'file_size', label: 'Size', render: row => row.file_size ? `${row.file_size}` : '-' }
                ]
            }
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

        function setNotice(text, type = '') {
            $('#notice').removeClass('success error').addClass(type).text(text || '');
        }

        function setLoader(show) {
            $('#hoot-loader').css('display', show ? 'flex' : 'none');
        }

        function apiHeaders() {
            const headers = { Accept: 'application/json' };
            if (state.token) {
                headers.Authorization = `Bearer ${state.token}`;
            }
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
                if (xhr.status === 401) {
                    handleUnauthorized();
                }
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

        function isAdminSide() {
            const roleId = Number(state.user?.role_id || 0);
            const roleName = String(state.user?.role_name || '').toLowerCase().trim();
            return roleId === 1 || roleId === 2 || roleName.includes('admin') || roleName.includes('manager');
        }

        function lookupLabel(name, id) {
            const items = state.lookups[name] || [];
            const key = getLookupKey(name);
            const labelKey = getLookupLabelKey(name);
            const found = items.find(item => String(item[key]) === String(id));
            return found ? found[labelKey] : id || '-';
        }

        function getLookupKey(name) {
            switch (name) {
                case 'departments': return 'department_id';
                case 'roles': return 'role_id';
                case 'priorities': return 'priority_id';
                case 'taskStatuses': return 'task_status_id';
                case 'users': return 'user_id';
                case 'tasks': return 'task_id';
                default: return 'id';
            }
        }

        function getLookupLabelKey(name) {
            switch (name) {
                case 'departments':
                case 'roles': return 'name';
                case 'priorities':
                case 'taskStatuses': return 'title';
                case 'users': return 'name';
                case 'tasks': return 'title';
                default: return 'name';
            }
        }

        function getVisibleModuleConfig() {
            const visibleOrder = getVisibleModuleOrder();
            return visibleOrder.map((key) => ({ key, cfg: modules[key] })).filter(item => !!item.cfg);
        }

        function renderSidebar() {
            const navHtml = getVisibleModuleConfig().map(({ key, cfg }) => {
                const label = key === 'overview' ? 'Overview' : cfg.title;
                return `<button type="button" class="${key === state.activeModule ? 'active' : ''}" data-module="${key}"><span>${escapeHtml(label)}</span><small>${key === 'overview' ? 'Summary' : 'Manage'}</small></button>`;
            }).join('');

            if (!getVisibleModuleOrder().includes(state.activeModule)) {
                state.activeModule = 'overview';
            }

            $('#sidebarNav').html(navHtml);
            $('#profileName').text(state.user?.name || 'Authenticated User');
            $('#profileEmail').text(state.user?.email || '');
            $('#sessionPill').text(state.token ? 'Token active' : 'No token');
        }

        function renderOverview() {
            const visibleKeys = getVisibleModuleOrder();

            const cards = [
                { key: 'departments', label: 'Departments', value: state.counts.departments ?? 0, hint: 'Department master records' },
                { key: 'roles', label: 'Roles', value: state.counts.roles ?? 0, hint: 'Access roles and permissions' },
                { key: 'users', label: 'Users', value: state.counts.users ?? 0, hint: 'Active team members' },
                { key: 'tasks', label: 'Tasks', value: state.counts.tasks ?? 0, hint: 'Task records in the system' },
                { key: 'taskStatuses', label: 'Statuses', value: state.counts.taskStatuses ?? 0, hint: 'Workflow status options' },
                { key: 'priorities', label: 'Priorities', value: state.counts.priorities ?? 0, hint: 'Priority levels' },
                { key: 'documents', label: 'Documents', value: state.counts.documents ?? 0, hint: 'Document metadata entries' }
            ].filter(card => visibleKeys.includes(card.key));

            const html = `
                <section class="grid stats">
                    ${cards.map((card) => `
                        <div class="stat">
                            <div class="label">${escapeHtml(card.label)}</div>
                            <div class="value">${escapeHtml(card.value)}</div>
                            <div class="hint">${escapeHtml(card.hint)}</div>
                        </div>
                    `).join('')}
                </section>

            `;

            $('#moduleRoot').html(html);
            $('#pageTitle').text('Dashboard');
          
        }

        function getDrawerText(moduleName, mode) {
            const cfg = modules[moduleName];
            const title = cfg ? cfg.title : 'Record';

            if (mode === 'create') {
                return {
                    title: `Create ${title}`,
                    //note: 'Fill out the fields in the side panel, then save the new record.'
                };
            }

            return {
                title: `Editing ${title}`,
               // note: 'Update the selected record in the side panel, then save the changes.'
            };
        }

        function syncEditorBackdrop(isOpen) {
            $('body').toggleClass('drawer-open', isOpen);
            $('#editorBackdrop').toggleClass('visible', isOpen);
        }

        function openEditorDrawer(moduleName, mode = 'edit') {
            state.drawerModule = moduleName;
            state.drawerMode = mode;
            const drawerText = getDrawerText(moduleName, mode);
            $('#moduleDrawerTitle').text(drawerText.title);
            $('#moduleDrawerNote').text(drawerText.note);
            $('#moduleDrawer').addClass('open');
            $('#moduleDrawerEmpty').hide();
            syncEditorBackdrop(true);
        }

        function closeEditorDrawer() {
            state.drawerModule = null;
            state.drawerMode = null;
            $('#moduleDrawer').removeClass('open');
            $('#moduleDrawerEmpty').show();
            syncEditorBackdrop(false);
        }

        function prepareNewRecord(moduleName) {
            state.records[moduleName] = state.records[moduleName] || {};
            state.records[moduleName].editing = null;
            state.editingId[moduleName] = null;
            if ($('#moduleForm').length) {
                $('#moduleForm')[0].reset();
                $('#recordId').val('');
            }

            if (moduleName === 'tasks') {
                state.taskAssignees = [];
                $('[name="assigned_user_ids[]"]').html('<option value="">Select a department first</option>').val([]);
                $('[data-multi-filter-for="field_tasks_assigned_user_ids"]').val('').prop('disabled', true);
            }

            openEditorDrawer(moduleName, 'create');
            setNotice(`Creating a new ${modules[moduleName].title.toLowerCase()} record.`, '');
        }

        async function renderModule(moduleName) {
            const cfg = modules[moduleName];
            await ensureModuleLookups(moduleName);
            const canSearch = true;
            const canCreate = !!cfg.create;
            const searchHtml = `<input id="moduleSearch" class="search" type="search" placeholder="Search ${escapeHtml(cfg.title.toLowerCase())}..." value="${escapeHtml(state.search[moduleName] || '')}">`;

            const formFields = cfg.fields.map(field => renderField(moduleName, field)).join('');
            const drawerOpen = state.drawerModule === moduleName;
            const drawerText = getDrawerText(moduleName, state.drawerMode || 'edit');

            const html = `
                <section class="panel">
                    <div class="panel-head">
                        <div>
                            <h2 class="panel-title">${escapeHtml(cfg.title)}</h2>
                            <p class="panel-desc">${escapeHtml(cfg.subtitle || '')}</p>
                        </div>
                        <div class="toolbar">
                            ${searchHtml}
${canCreate ? '<button id="newButton" type="button" class="btn btn-primary" aria-label="New" title="New"><span style="font-size:18px; line-height:1;">+</span></button>' : ''}
<button id="refreshButton" type="button" class="btn btn-secondary" aria-label="Refresh" title="Refresh"><span style="font-size:16px; line-height:1;">⟳</span></button>
                        </div>
                    </div>

                    <div class="table-wrap">
                        <table>
                            <thead><tr>${cfg.columns.map(column => `<th>${escapeHtml(column.label)}</th>`).join('')}<th>Actions</th></tr></thead>
                            <tbody id="moduleTableBody"><tr><td colspan="${cfg.columns.length + 1}" class="empty">Loading...</td></tr></tbody>
                        </table>
                    </div>

                    <div class="pagination">
                        <div id="moduleMeta" class="meta">Ready</div>
                        <div id="moduleButtons" class="buttons"></div>
                    </div>
                </section>

                <aside id="moduleDrawer" class="panel editor-drawer ${drawerOpen ? 'open' : ''}">
                    <div class="editor-drawer-header">
                        <div>
                            <h3 id="moduleDrawerTitle" class="editor-drawer-title">${escapeHtml(drawerText.title)}</h3>
                            <p id="moduleDrawerNote" class="editor-drawer-note">${escapeHtml(drawerText.note)}</p>
                        </div>
                        <button type="button" id="closeModuleDrawer" class="btn btn-secondary">Close</button>
                    </div>

                    <form id="moduleForm" autocomplete="off">
                        <input type="hidden" name="id" id="recordId" value="">
                        <div class="form-grid">${formFields}</div>
                        <div class="actions">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                            <button type="button" id="resetButton" class="btn btn-secondary">Reset</button>
                        </div>
                    </form>

                    <div id="moduleDrawerEmpty" class="editor-drawer-empty" style="${drawerOpen ? 'display:none;' : ''}">
                        Choose Edit on any row, or New if this module supports creating records.
                    </div>
                </aside>
            `;

            $('#moduleRoot').html(html);
            $('#pageTitle').text(cfg.title);
            $('#pageSubtitle').text(cfg.subtitle || '');

            $('#moduleForm').on('submit', function (event) {
                event.preventDefault();
                submitModule(moduleName);
            });

            $('#resetButton').on('click', function () {
                clearForm(moduleName);
            });

            $('#closeModuleDrawer').on('click', function () {
                clearForm(moduleName);
            });

            $('#editorBackdrop').off('click').on('click', function () {
                clearForm(moduleName);
            });

            if (canCreate) {
                $('#newButton').on('click', function () {
                    prepareNewRecord(moduleName);
                });
            }

            $('#refreshButton').on('click', function () {
                loadModule(moduleName);
            });

            $('#moduleSearch').off('input.moduleSearch').on('input.moduleSearch', function () {
                state.search[moduleName] = $(this).val().trim();
                clearTimeout(state.searchTimer);
                state.searchTimer = setTimeout(() => loadModule(moduleName, 1), 250);
            });


            if (moduleName === 'tasks') {
                $('#field_tasks_department_id').off('change.taskAssignees').on('change.taskAssignees', function () {
                    const departmentId = $(this).val();
                    const assigneeFilter = $('[data-multi-filter-for="field_tasks_assigned_user_ids"]');
                    assigneeFilter.val('');
                    void loadTaskAssignees(departmentId).then(() => {
                        syncTaskAssigneeFilter();
                    });
                });

                $(document).off('input.taskAssigneeFilter').on('input.taskAssigneeFilter', '[data-multi-filter-for="field_tasks_assigned_user_ids"]', function () {
                    filterMultiSelectOptions($('[data-task-assignee-select]'), $(this).val());
                });
            }

            // Preview/upload helper for image file inputs in user form
            $(document).off('click.moduleImagePreview').on('click.moduleImagePreview', 'button[id$="_preview"]', function () {
                const btn = $(this);
                const fileInput = btn.closest('.field').find('input[type="file"]')[0];
                if (!fileInput || !fileInput.files || !fileInput.files.length) {
                    setNotice('Please choose an image file first.', 'error');
                    return;
                }
                const file = fileInput.files[0];
                const reader = new FileReader();
                reader.onload = function (e) {
                    const win = window.open('', '_blank');
                    const img = win.document.createElement('img');
                    img.src = e.target.result;
                    img.style.maxWidth = '100%';
                    win.document.body.appendChild(img);
                };
                reader.readAsDataURL(file);
            });

            loadModule(moduleName);
        }

        function renderField(moduleName, field) {
            if (field.createOnly && state.editingId[moduleName]) {
                return '';
            }

            const value = getFieldValue(moduleName, field.name);
            const id = `field_${moduleName}_${field.name}`;
            const required = field.required && !(field.name === 'password' && state.editingId[moduleName]);
            const label = `${escapeHtml(field.label)}${required ? ' *' : ''}`;

            if (field.lockOnEdit && state.editingId[moduleName]) {
                const selectedLabel = lookupLabel(field.lookup, value);

                return `
                    <div class="field ${field.full ? 'full' : ''}">
                        <label for="${id}">${label}</label>
                        <input type="hidden" name="${field.name}" value="${escapeHtml(value)}">
                        <input id="${id}" class="control" type="text" value="${escapeHtml(selectedLabel)}" readonly>
                        <small class="field-help">This task is locked for this log entry.</small>
                    </div>
                `;
            }

            if (moduleName === 'tasks' && field.name === 'assigned_user_ids') {
                const selectedValues = Array.isArray(value) ? value.map(String) : [];
                const currentDepartmentId = $('#field_tasks_department_id').val() || state.records.tasks?.editing?.department_id || '';
                const helperText = currentDepartmentId
                    ? 'Choose one or more users from the selected department.'
                    : 'Select a department first to load the user list.';

                return `
                    <div class="field ${field.full ? 'full' : ''}">
                        <label for="${id}">${label}</label>
                        <small class="field-help">${escapeHtml(helperText)}</small>
                        <input type="search" class="control" data-multi-filter-for="${id}" placeholder="Filter assignees..." ${currentDepartmentId ? '' : 'disabled'}>
                        <select id="${id}" name="${field.name}[]" class="select" multiple size="6" ${required ? 'required' : ''} data-task-assignee-select>
                            ${renderTaskAssigneeOptions(selectedValues)}
                        </select>
                    </div>
                `;
            }

            if (field.type === 'textarea') {
                return `
                    <div class="field ${field.full ? 'full' : ''}">
                        <label for="${id}">${label}</label>
                        <textarea id="${id}" name="${field.name}" class="textarea" ${required ? 'required' : ''} placeholder="${escapeHtml(field.placeholder || '')}">${escapeHtml(value)}</textarea>
                    </div>
                `;
            }

            if (field.type === 'select' || field.type === 'multi-select') {
                const items = state.lookups[field.lookup] || [];
                const key = getLookupKey(field.lookup);
                const labelKey = getLookupLabelKey(field.lookup);
                const multiple = field.type === 'multi-select';
                const selectedValues = multiple ? (Array.isArray(value) ? value.map(String) : []) : [String(value || '')];

                const options = [`<option value="">Select ${escapeHtml(field.label)}</option>`].concat(items.map(item => {
                    const itemValue = String(item[key]);
                    const selected = selectedValues.includes(itemValue) ? 'selected' : '';
                    return `<option value="${escapeHtml(itemValue)}" ${selected}>${escapeHtml(item[labelKey])}</option>`;
                })).join('');

                const helper = multiple
                    ? `<small class="field-help">Use the filter below to search users, then hold Ctrl or Cmd to select multiple names.</small>`
                    : '';
                const filter = multiple && field.lookup === 'users'
                    ? `<input type="search" class="control" data-multi-filter-for="field_${moduleName}_${field.name}" placeholder="Filter users...">`
                    : '';

                return `
                    <div class="field ${field.full ? 'full' : ''}">
                        <label for="${id}">${label}</label>
                        ${helper}
                        ${filter}
                        <select id="${id}" name="${field.name}${multiple ? '[]' : ''}" class="select" ${multiple ? 'multiple size="5"' : ''} ${required ? 'required' : ''}>${options}</select>
                    </div>
                `;
            }

            const type = field.type || 'text';
            const inputValue = value || '';
            const placeholder = escapeHtml(field.placeholder || `Enter ${field.label.toLowerCase()}`);
            // special-case image field for admin UI: provide file picker plus URL fallback
            if (moduleName === 'users' && field.name === 'image') {
                return `
                    <div class="field ${field.full ? 'full' : ''}">
                        <label for="${id}">${label}</label>
                        <input id="${id}" name="${field.name}" class="control" type="${type}" value="${escapeHtml(inputValue)}" placeholder="${placeholder}" ${required ? 'required' : ''}>
                        <div style="margin-top:8px; display:flex; gap:8px; align-items:center;">
                            <input id="${id}_file" name="image_file" type="file" accept="image/*" style="flex:1;" />
                            <button type="button" id="${id}_preview" class="btn btn-secondary">Preview</button>
                        </div>
                        <small class="field-help">Upload an image file or paste an image URL.</small>
                    </div>
                `;
            }

            return `
                <div class="field ${field.full ? 'full' : ''}">
                    <label for="${id}">${label}</label>
                    <input id="${id}" name="${field.name}" class="control" type="${type}" value="${escapeHtml(inputValue)}" placeholder="${placeholder}" ${required ? 'required' : ''}>
                </div>
            `;
        }

        function getFieldValue(moduleName, fieldName) {
            const current = state.records[moduleName]?.editing || null;
            if (!current) return '';
            if (fieldName === 'assigned_user_ids') {
                return current.assigned_user_ids || [];
            }
            if (fieldName === 'user_ids') {
                return current.user_ids || [];
            }
            return current[fieldName] ?? '';
        }

        function clearForm(moduleName) {
            state.records[moduleName] = state.records[moduleName] || {};
            state.records[moduleName].editing = null;
            state.editingId[moduleName] = null;
            if ($('#moduleForm').length) {
                $('#moduleForm')[0].reset();
                $('#recordId').val('');
            }
            if (moduleName === 'tasks') {
                state.taskAssignees = [];
                $('[name="assigned_user_ids[]"]').html('<option value="">Select a department first</option>').val([]);
                $('[data-multi-filter-for="field_tasks_assigned_user_ids"]').val('').prop('disabled', true);
            }

            closeEditorDrawer();
        }

        function setEditing(moduleName, item) {
            state.records[moduleName] = state.records[moduleName] || {};
            state.records[moduleName].editing = item;
            state.editingId[moduleName] = item ? item[modules[moduleName].columns[0].key] || item.id || item[Object.keys(item)[0]] : null;
        }

        async function loadLookup(name) {
            const endpoint = lookupSources[name];
            if (!endpoint || state.lookups[name]) {
                return;
            }

            const response = await apiRequest(endpoint, { limit: 500, offset: 0 });
            state.lookups[name] = response.data || [];
            state.counts[name] = response.count ?? (response.data || []).length;
        }

        async function ensureModuleLookups(moduleName) {
            const requiredLookups = moduleLookupMap[moduleName] || [];
            await Promise.allSettled(requiredLookups.map(loadLookup));
        }

        async function loadOverviewCounts() {
            const totals = {};
            const visible = new Set(getVisibleModuleOrder());

            const requests = Object.entries(modules)
                .filter(([name]) => name !== 'overview' && visible.has(name))
                .map(async ([name, cfg]) => {
                    const response = await apiRequest(cfg.list, { limit: 1, offset: 0, search: state.search[name] || '' });
                    totals[name] = response.count ?? (response.data || []).length;
                });

            await Promise.all(requests);

            state.counts = { ...state.counts, ...totals };

            // Update overview cards after counts are fetched.
            // Use the *current* visibility rules (admin vs non-admin) to avoid stale/incorrect rendering.
            if ($('#moduleRoot').length) {
                // Only re-render when currently on dashboard.
                if (state.activeModule === 'overview') {
                    renderOverview();
                }
            }
        }

        async function loadModule(moduleName, page = 1) {
            const cfg = modules[moduleName];
            if (!cfg || moduleName === 'overview') return;

            await ensureModuleLookups(moduleName);
            state.pages[moduleName] = page;
            setLoader(true);

            try {
                const offset = (page - 1) * cfg.pageSize;
                const payload = { limit: cfg.pageSize, offset };
                if (state.search[moduleName]) {
                    payload.search = state.search[moduleName];
                }


                const response = await apiRequest(cfg.list, payload);
                const rows = response.data || [];
                state.counts[moduleName] = response.count ?? rows.length;
                renderTable(moduleName, rows);
                renderPagination(moduleName);
                $('#moduleMeta').text(`${rows.length} of ${state.counts[moduleName]} records shown`);
            } catch (error) {
                setNotice(error.message, 'error');
                $('#moduleTableBody').html(`<tr><td colspan="${cfg.columns.length + 1}" class="empty">${escapeHtml(error.message)}</td></tr>`);
            } finally {
                setLoader(false);
            }
        }

        function renderTable(moduleName, rows) {
            const cfg = modules[moduleName];
            if (!rows.length) {
                $('#moduleTableBody').html(`<tr><td colspan="${cfg.columns.length + 1}" class="empty">No records found.</td></tr>`);
                return;
            }

            const html = rows.map((row) => {
                const cells = cfg.columns.map((column) => {
                    const raw = column.render ? column.render(row) : (column.lookup ? lookupLabel(column.lookup, row[column.key]) : row[column.key]);
                    return `<td>${renderCell(raw, column)}</td>`;
                }).join('');

                return `
                    <tr>
                        ${cells}
                        <td>
                            <div class="row-actions">
<button type="button" class="mini icon-mini" aria-label="Edit" data-action="edit" data-id="${escapeHtml(getRowId(row, moduleName))}">
                                    <svg viewBox="0 0 24 24" role="img" aria-hidden="true">
                                        <path d="M3 17.25V21h3.75L19.81 7.94l-3.75-3.75L3 17.25z" />
                                        <path d="M14.06 4.19l3.75 3.75" />
                                    </svg>
                                </button>
                                <button type="button" class="mini icon-mini" aria-label="Delete" data-action="delete" data-id="${escapeHtml(getRowId(row, moduleName))}">
                                    <svg viewBox="0 0 24 24" role="img" aria-hidden="true">
                                        <path d="M3 6h18" />
                                        <path d="M8 6V4h8v2" />
                                        <path d="M19 6l-1 16H6L5 6" />
                                        <path d="M10 11v6" />
                                        <path d="M14 11v6" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');

            $('#moduleTableBody').html(html);

            $('#moduleTableBody button[data-action="edit"]').off('click').on('click', function () {
                const id = $(this).data('id');
                const item = rows.find(row => String(getRowId(row, moduleName)) === String(id));
                if (item) {
                    openRowEditor(moduleName, item);
                }
            });

            $('#moduleTableBody button[data-action="delete"]').off('click').on('click', function () {
                const id = $(this).data('id');
                deleteRecord(moduleName, id);
            });
        }

        function renderCell(raw, column) {
            if (column.render || column.lookup) {
                return raw === null || raw === undefined || raw === '' ? '-' : raw;
            }
            return escapeHtml(raw ?? column.fallback ?? '-');
        }

        function openRowEditor(moduleName, item) {
            fillForm(moduleName, item);
        }

        function renderTaskAssigneeOptions(selectedValues = []) {
            const items = state.taskAssignees || [];
            if (!items.length) {
                return '<option value="">Select a department first</option>';
            }

            return items.map(item => {
                const itemValue = String(item.user_id);
                const selected = selectedValues.includes(itemValue) ? 'selected' : '';
                return `<option value="${escapeHtml(itemValue)}" ${selected}>${escapeHtml(item.name)}</option>`;
            }).join('');
        }

        async function loadTaskAssignees(departmentId, selectedValues = []) {
            const normalizedDepartmentId = String(departmentId || '').trim();
            state.taskAssignees = [];
            const filterInput = $('[data-multi-filter-for="field_tasks_assigned_user_ids"]');

            if (!normalizedDepartmentId) {
                const select = $('[name="assigned_user_ids[]"]');
                if (select.length) {
                    select.html('<option value="">Select a department first</option>');
                    select.val([]);
                }
                if (filterInput.length) {
                    filterInput.val('').prop('disabled', true);
                }
                return [];
            }

            const response = await apiRequest('getalluser', {
                limit: 500,
                offset: 0,
                department_id: normalizedDepartmentId
            });

            state.taskAssignees = response.data || [];

            const select = $('[name="assigned_user_ids[]"]');
            if (select.length) {
                select.html(renderTaskAssigneeOptions(selectedValues.map(String)));
            }

            if (filterInput.length) {
                filterInput.prop('disabled', false);
            }

            return state.taskAssignees;
        }

        function syncTaskAssigneeFilter() {
            const filterValue = $('[data-multi-filter-for="field_tasks_assigned_user_ids"]').val() || '';
            filterMultiSelectOptions($('[name="assigned_user_ids[]"]'), filterValue);
        }

        function filterMultiSelectOptions(selectElement, query) {
            const normalizedQuery = String(query || '').toLowerCase().trim();
            $(selectElement).find('option').each(function () {
                const option = $(this);
                if (!option.val()) {
                    option.prop('hidden', false);
                    return;
                }

                const label = option.text().toLowerCase();
                option.prop('hidden', normalizedQuery ? !label.includes(normalizedQuery) : false);
            });
        }

        function getRowId(row, moduleName) {
            switch (moduleName) {
                case 'departments': return row.department_id;
                case 'roles': return row.role_id;
                case 'priorities': return row.priority_id;
                case 'taskStatuses': return row.task_status_id;
                case 'users': return row.user_id;
                case 'tasks': return row.task_id;
                case 'taskStatusLogs': return row.status_log_id;
                case 'documents': return row.document_id;
                default: return row.id;
            }
        }

        function renderPagination(moduleName) {
            const cfg = modules[moduleName];
            const total = state.counts[moduleName] || 0;
            const current = state.pages[moduleName] || 1;
            const pages = Math.max(1, Math.ceil(total / cfg.pageSize));
            const buttons = [];

            buttons.push(`<button type="button" class="btn btn-secondary" ${current <= 1 ? 'disabled' : ''} data-page="${current - 1}">Previous</button>`);
            buttons.push(`<button type="button" class="btn btn-secondary" ${current >= pages ? 'disabled' : ''} data-page="${current + 1}">Next</button>`);

            $('#moduleButtons').html(buttons.join(''));
            $('#moduleButtons button[data-page]').off('click').on('click', function () {
                const nextPage = Number($(this).data('page'));
                loadModule(moduleName, nextPage);
            });

            $('#moduleMeta').text(`Page ${current} of ${pages} · ${total} total records`);
        }

        function fillForm(moduleName, item) {
            setEditing(moduleName, item);
            $('#recordId').val(getRowId(item, moduleName));

            modules[moduleName].fields.forEach((field) => {
                if (field.createOnly) return;
                const selector = `[name="${field.name}${field.type === 'multi-select' ? '[]' : ''}"]`;
                const element = $(selector);
                if (!element.length) return;

                if (field.type === 'multi-select') {
                    const values = Array.isArray(item[field.name]) ? item[field.name] : (item[field.name] ? [item[field.name]] : []);
                    element.val(values.map(String));
                } else {
                    element.val(item[field.name] ?? '');
                }
            });

            if (moduleName === 'tasks') {
                const assignedValues = item.assigned_user_ids || [];
                const departmentId = item.department_id || $('#field_tasks_department_id').val() || '';
                void loadTaskAssignees(departmentId, assignedValues.map(String)).then(() => {
                    $('[name="assigned_user_ids[]"]').val(assignedValues.map(String));
                    syncTaskAssigneeFilter();
                });
            }

            if (moduleName === 'taskStatusLogs') {
                const taskField = $('[name="task_id"]');
                if (taskField.length) {
                    const wrapper = taskField.closest('.field');
                    const taskId = item.task_id ?? '';
                    const taskLabel = lookupLabel('tasks', taskId);
                    wrapper.html(`
                        <label for="field_taskStatusLogs_task_id">Task *</label>
                        <input type="hidden" name="task_id" value="${escapeHtml(taskId)}">
                        <input id="field_taskStatusLogs_task_id" class="control" type="text" value="${escapeHtml(taskLabel)}" readonly>
                        <small class="field-help">This log stays linked to its original task.</small>
                    `);
                }
            }

            openEditorDrawer(moduleName, 'edit');

            setNotice(`Editing ${modules[moduleName].title.toLowerCase()} record #${getRowId(item, moduleName)}.`, '');
        }

        async function submitModule(moduleName) {
            const cfg = modules[moduleName];
            const form = $('#moduleForm')[0];
            const formData = new FormData(form);
            const payload = {};

            for (const [key, value] of formData.entries()) {
                if (key.endsWith('[]')) {
                    const baseKey = key.slice(0, -2);
                    payload[baseKey] = payload[baseKey] || [];
                    payload[baseKey].push(value);
                } else if (payload[key] !== undefined) {
                    if (!Array.isArray(payload[key])) payload[key] = [payload[key]];
                    payload[key].push(value);
                } else {
                    payload[key] = value;
                }
            }

            if (payload.password === '') delete payload.password;
            if (payload.file_size === '') delete payload.file_size;

            const isEdit = !!payload.id;
            if (moduleName === 'taskStatusLogs' && !isEdit) {
                setNotice('Select a log entry from the list first.', 'error');
                return;
            }
            const endpoint = isEdit ? cfg.update : cfg.create;

            try {
                setLoader(true);
                setNotice('Saving record...', '');
                let response;
                // If the form includes a file input, send as multipart FormData
                const fileInput = form.querySelector('input[type="file"][name="image_file"]');
                if (fileInput && fileInput.files && fileInput.files.length) {
                    const fd = new FormData();
                    // append all original form entries from the FormData object
                    for (const pair of formData.entries()) {
                        fd.append(pair[0], pair[1]);
                    }

                    response = await (async () => {
                        try {
                            const jq = await $.ajax({
                                type: 'POST',
                                url: `${API_BASE}/${endpoint}`,
                                data: fd,
                                processData: false,
                                contentType: false,
                                headers: apiHeaders()
                            });
                            return jq;
                        } catch (xhr) {
                            throw new Error(extractError(xhr.responseJSON || {}));
                        }
                    })();
                } else {
                    response = await apiRequest(endpoint, payload);
                    if (response.status !== 200) {
                        throw new Error(extractError(response));
                    }
                }

                setNotice('Record saved successfully.', 'success');
                clearForm(moduleName);
                await refreshLookupsIfNeeded(moduleName);
                await loadModule(moduleName, state.pages[moduleName] || 1);
            } catch (error) {
                setNotice(error.message, 'error');
            } finally {
                setLoader(false);
            }
        }

        async function deleteRecord(moduleName, id) {
            const cfg = modules[moduleName];
            if (!confirm(`Delete this ${cfg.title.slice(0, -1).toLowerCase()} record?`)) return;

            try {
                setLoader(true);
                const response = await apiRequest(cfg.destroy, { id });
                if (response.status !== 200) {
                    throw new Error(extractError(response));
                }
                setNotice('Record deleted successfully.', 'success');
                clearForm(moduleName);
                await loadModule(moduleName, state.pages[moduleName] || 1);
            } catch (error) {
                setNotice(error.message, 'error');
            } finally {
                setLoader(false);
            }
        }

        async function refreshLookupsIfNeeded(moduleName) {
            await ensureModuleLookups(moduleName);
        }

        function activateModule(moduleName) {
            state.activeModule = moduleName;
            $('#sidebarNav button').removeClass('active');
            $(`#sidebarNav button[data-module="${moduleName}"]`).addClass('active');
            setNotice('', '');

            if (moduleName === 'overview') {
                renderOverview();
                void loadOverviewCounts();
                return;
            }

            void renderModule(moduleName);
        }

        function bindEvents() {
            $('#sidebarNav').on('click', 'button[data-module]', function () {
                activateModule($(this).data('module'));
            });

            $('#logoutButton').on('click', async function () {
                try {
                    setLoader(true);
                    await apiRequest('logout', {});
                } catch (error) {
                    // ignore logout errors and clear local session anyway
                } finally {
                    localStorage.removeItem('smart-office-token');
                    localStorage.removeItem('smart-office-user');
                    window.location.href = '/login';
                }
            });
        }

        async function init() {
            if (!state.token) {
                window.location.href = '/login';
                return;
            }

            if (!isAdminSide()) {
                window.location.href = '/user-dashboard';
                return;
            }

            renderSidebar();
            bindEvents();

            try {
                renderSidebar();
                renderOverview();
                setTimeout(function () {
                    void loadOverviewCounts();
                }, 0);
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

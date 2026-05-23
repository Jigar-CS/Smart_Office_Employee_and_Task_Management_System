**Masters & Tables (Formatted & Project-Adapted Reference)**

Summary
- This file is a compact, human-friendly reference of master (lookup) tables and core project tables adapted from your attached reference sheet and the project `tasks.md`/migrations.

1) Reference (from your attachment — mapped concepts)
- Masters in reference: Spaces, Amenity, Artist, FAQ, Age, Genres, Banner, Leads, Tags
- Pages in reference: Events, Blog, Gallery, Sitemap, Setting, Menu
- Common fields in reference: `created_at`, `updated_at`, `created_by`, `updated_by`, `status`

2) Project Masters (adapted for this API)

| Master     | Fields                                  | Notes / Example |
|------------|-----------------------------------------|-----------------|
| Priorities | id, title, level, status                | High            |
| Roles      | id, name, description, status           | Admin           |
| TaskStatus | id, title, description, status          | In Progress     |
| Departments| id, name, description, status           | HR              |

3) Common Fields (applied across tables)
- `created_at` (timestamp)
- `updated_at` (timestamp)
- `created_by` (user id)
- `updated_by` (user id)
- `status` (tinyint: 1=active,0=inactive)
- `deleted_at` (nullable timestamp for soft deletes)

4) Core Tables (summary + key fields)

| Table                | Key Fields (summary)                                                                 | Example / Notes |
|----------------------|--------------------------------------------------------------------------------------|-----------------|
| users                | id, name, email(unique), password, department_id, role_id, mobile, accesstoken, status | admin@example.com |
| roles                | id, name, description, status                                                       | Admin           |
| role_permissions     | id, role_id, permission_key (string) / permission_json, status                     |                 |
| departments          | id, name, description, status                                                      | HR              |
| priorities           | id, title, level, status                                                           | High            |
| task_statuses        | id, title, description, status                                                     | In Progress     |
| tasks                | id, title, description, start_date, due_date, priority_id, task_status_id, estimated_hours, attachments(JSON), created_by, status |                 |
| task_assignments     | id, task_id, user_id (assignee), assigned_by, assigned_at, role, status          |                 |
| task_status_logs     | id, task_id, from_status_id, to_status_id, changed_by, remarks, created_at        |                 |
| documents            | id, model_type, model_id, filename, original_name, mime_type, size, path, storage, uploaded_by, status | store in S3/local |
| activity_logs        | id, user_id, action, model_type, model_id, old_value(json), new_value(json), ip_address, user_agent, created_at |                 |
| request_logs         | id, route, method, request_body(json), response_code, response_body(json), ip_address, created_at |                 |

5) Mapping notes (how the reference maps to this project)
- `Spaces`, `Events`, `Gallery` in the reference map conceptually to content modules — for this project the primary content model is `tasks` and `documents`/`activity_logs` rather than event-driven content.
- Reference array fields (images[], genres[], amenity[]) are implemented here either as normalized pivot tables or via `documents` (polymorphic) + JSON metadata depending on query needs.

6) Implementation recommendations (follow `tasks.md` rules)
- Use Laravel migrations with `softDeletes()` for entities that require restore (users, tasks, documents).
- Normalize many-to-many relationships with pivot tables when you need to query/filter (e.g., `task_tag`, `task_genre`) otherwise use JSON for opaque metadata.
- Add indexes: `email`, `role_id`, `department_id`, `task_status_id`, `priority_id`, `created_by`.
- Store files via Laravel `Storage` (S3 if enabled) and save metadata in `documents` table.
- Seed `roles` and an Admin user in `DatabaseSeeder` per `tasks.md`.

7) Where to find more details
- Full schema and relationship notes: see `SCHEMA.md`
- Project tasks, rules and seeding policy: see `tasks.md`

If you want, I can now:
- (A) Convert these summaries into Laravel migration stubs, or
- (B) Create pivot-table migrations for normalized arrays from your reference (images[], genres[], amenity[]), or
- (C) Produce a one-page ER diagram (SVG).



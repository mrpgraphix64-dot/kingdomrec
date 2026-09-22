<?php

/*
|--------------------------------------------------------------------------
| Admin navigation groups
|--------------------------------------------------------------------------
|
| Single source of truth for the admin nav, shared by the desktop sidebar
| (components/admin/sidebar.blade.php) and the mobile drawer
| (components/admin/mobile-sidebar.blade.php) so they can't drift apart.
|
| Item shape: ['label', 'route', 'icon']
| Optional 'children' on an item renders it as an expandable group on desktop
| (and a flat indented sub-list on mobile) — see 'params' for query-string links.
|
| Optional 'permission' gates the item's *visibility* using the same Spatie
| permission the route itself is protected by (see routes/web.php's admin
| group) — this is a UX layer only, so an item with no 'permission' key is
| shown to every admin, matching the fact that its route(s) carry no
| permission middleware. Route-level middleware remains the actual security
| boundary; removing or mislabeling a key here cannot grant access.
|
*/

return [
    'groups' => [
        'Overview' => [
            ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'layout-dashboard'],
        ],
        'Management' => [
            ['label' => 'Job Post', 'route' => 'admin.job-post', 'icon' => 'file-plus', 'permission' => 'manage_jobs', 'children' => [
                ['label' => 'All Job Posts', 'route' => 'admin.job-post', 'icon' => 'list'],
                ['label' => 'Categories', 'route' => 'admin.job-category', 'icon' => 'tags'],
            ]],
            ['label' => 'Rate Card', 'route' => 'admin.rate-card', 'icon' => 'credit-card', 'permission' => 'manage_operations'],
            ['label' => 'Venue', 'route' => 'admin.venue', 'icon' => 'map-pin', 'permission' => 'manage_operations'],
        ],
        'People' => [
            ['label' => 'Admins', 'route' => 'admin.admins.index', 'icon' => 'shield-check', 'permission' => 'manage_roles', 'children' => [
                ['label' => 'Admin Users', 'route' => 'admin.admins.index', 'icon' => 'shield-check'],
                ['label' => 'Roles & Permissions', 'route' => 'admin.roles.index', 'icon' => 'key-round'],
            ]],
            ['label' => 'Users', 'route' => 'admin.users', 'icon' => 'user-circle', 'permission' => 'manage_users'],
            ['label' => 'Team', 'route' => 'admin.team', 'icon' => 'user-cog', 'permission' => 'manage_team'],
            ['label' => 'Applicants', 'route' => 'admin.applicant', 'icon' => 'users', 'permission' => 'manage_applicants'],
            ['label' => 'Partners', 'route' => 'admin.partners', 'icon' => 'handshake'],
            ['label' => 'Applications', 'route' => 'admin.applications', 'icon' => 'file-check'],
        ],
        'Operations' => [
            ['label' => 'Staff Quotation', 'route' => 'admin.staff-quotation', 'icon' => 'clipboard-list', 'permission' => 'manage_operations'],
            ['label' => 'Events', 'route' => 'admin.events', 'icon' => 'calendar', 'permission' => 'manage_operations'],
            ['label' => 'Staff Assignments', 'route' => 'admin.assignments', 'icon' => 'user-check'],
            ['label' => 'Timesheets', 'route' => 'admin.time-shifting', 'icon' => 'file-spreadsheet', 'permission' => 'manage_operations'],
            ['label' => 'Billing', 'route' => 'admin.finance', 'icon' => 'banknote'],
            ['label' => 'Ratings & Feedback', 'route' => 'admin.ratings', 'icon' => 'star'],
        ],
        'Website Management' => [
            ['label' => 'Gallery', 'route' => 'admin.gallery.index', 'icon' => 'image'],
        ],
        'Support' => [
            ['label' => 'Chat Support', 'route' => 'admin.chat-support', 'icon' => 'message-circle'],
        ],
        'System' => [
            ['label' => 'Featured Candidates', 'route' => 'admin.featured-candidates.index', 'icon' => 'star'],
            ['label' => 'Settings', 'route' => 'admin.settings', 'icon' => 'settings', 'permission' => 'manage_settings'],
        ],
    ],
];

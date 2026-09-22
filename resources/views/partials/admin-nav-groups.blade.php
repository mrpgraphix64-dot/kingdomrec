{{--
    Single source of truth for the admin nav, shared by the desktop sidebar
    (components/admin/sidebar.blade.php) and the mobile drawer
    (components/admin/mobile-sidebar.blade.php) so they can't drift apart.

    Item shape: ['label', 'route', 'icon']
    Optional 'children' on an item renders it as an expandable group on desktop
    (and a flat indented sub-list on mobile) — see 'params' for query-string links.
--}}
@php
    $navGroups = [
        'Overview' => [
            ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'layout-dashboard'],
        ],
        'Management' => [
            ['label' => 'Job Post', 'route' => 'admin.job-post', 'icon' => 'file-plus', 'children' => [
                ['label' => 'All Job Posts', 'route' => 'admin.job-post', 'icon' => 'list'],
                ['label' => 'Categories', 'route' => 'admin.job-post', 'params' => ['manage_categories' => 1], 'icon' => 'tags'],
            ]],
            ['label' => 'Rate Card', 'route' => 'admin.rate-card', 'icon' => 'credit-card'],
            ['label' => 'Venue', 'route' => 'admin.venue', 'icon' => 'map-pin'],
        ],
        'People' => [
            ['label' => 'Admins', 'route' => 'admin.admins.index', 'icon' => 'shield-check'],
            ['label' => 'Users', 'route' => 'admin.users', 'icon' => 'user-circle'],
            ['label' => 'Team', 'route' => 'admin.team', 'icon' => 'user-cog'],
            ['label' => 'Applicants', 'route' => 'admin.applicant', 'icon' => 'users'],
            ['label' => 'Partners', 'route' => 'admin.partners', 'icon' => 'handshake'],
            ['label' => 'Applications', 'route' => 'admin.applications', 'icon' => 'file-check'],
        ],
        'Operations' => [
            ['label' => 'Staff Quotation', 'route' => 'admin.staff-quotation', 'icon' => 'clipboard-list'],
            ['label' => 'Events', 'route' => 'admin.events', 'icon' => 'calendar'],
            ['label' => 'Staff Assignments', 'route' => 'admin.assignments', 'icon' => 'user-check'],
            ['label' => 'Timesheets', 'route' => 'admin.time-shifting', 'icon' => 'file-spreadsheet'],
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
            ['label' => 'Settings', 'route' => 'admin.settings', 'icon' => 'settings'],
        ],
    ];
@endphp

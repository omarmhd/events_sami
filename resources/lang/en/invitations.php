<?php

return [
    'title' => 'Invitation Management',
    'event_fallback' => 'All Events',

    'create' => [
        'page_title' => 'Send Invitation',
        'page_subtitle' => 'Create and link an employee invitation to an event using the unified platform form.',
        'validation_title' => 'Some fields need attention',
        'validation_hint' => 'Please fix highlighted fields and submit again.',
        'fields' => [
            'event' => 'Event',
            'event_placeholder' => 'Select an event',
            'full_name' => 'Full Name',
            'full_name_placeholder' => 'e.g. Ahmed Ali',
            'email' => 'Email Address',
            'email_placeholder' => 'name@company.com',
            'position' => 'Job Title / Employee ID',
            'position_placeholder' => 'e.g. HR Specialist',
            'nationality' => 'Nationality',
            'allowed_guests' => 'Allowed Guests',
            'allowed_guests_hint' => 'How many extra companions are allowed for this invitation.',
        ],
        'actions' => [
            'cancel' => 'Cancel',
            'submit' => 'Send Invitation',
        ],
        'messages' => [
            'create_event_first' => 'Create an event first before sending invitations.',
            'email_already_invited' => 'This email is already invited for the selected event.',
            'invitation_sent' => 'Invitation sent successfully.',
        ],
    ],

    'actions' => [
        'new' => 'New Invitation',
        'export_csv' => 'Export CSV',
        'import_csv' => 'Import CSV',
        'bulk_resend_selected' => 'Resend Selected',
        'bulk_resend_all' => 'Resend All',
        'import' => 'Import',
        'close' => 'Close',
        'resend' => 'Resend',
        'copy_link' => 'Copy Link',
    ],

    'index' => [
        'search_placeholder' => 'Search by name, email, phone, position, status',
        'search' => 'Search',
        'overview' => 'Overview',
        'plan_gate_note' => 'Advanced tools are plan-gated: CSV import and bulk resend require Professional or higher.',
        'upgrade_plan' => 'Upgrade plan',
        'edit_invitation' => 'Edit Invitation',
        'copy_invite_message' => 'Copy Invite Message',
        'more_options' => 'More Options',
        'copy_tickets_link' => 'Copy Tickets Link',
        'confirm_resend_email' => 'Resend this invitation now?',
        'confirm_delete' => 'Are you sure you want to delete this invitation? Related data will be removed.',
        'delete_invitation' => 'Delete Invitation',
    ],

    'kpi' => [
        'total' => 'Total',
        'accepted' => 'Accepted',
        'pending' => 'Pending',
        'declined' => 'Declined',
        'maybe' => 'Maybe',
    ],

    'filters' => [
        'all' => 'All',
        'pending' => 'Pending',
        'accepted' => 'Accepted',
        'declined' => 'Declined',
        'maybe' => 'Maybe',
    ],

    'table' => [
        'name' => 'Name',
        'event' => 'Event',
        'position' => 'Position',
        'guests' => 'Guests',
        'email' => 'Email',
        'phone' => 'Phone',
        'status' => 'Status',
        'sent_date' => 'Sent Date',
        'responded' => 'Responded',
        'actions' => 'Actions',
        'no_phone' => '—',
        'no_event' => 'No event assigned',
        'no_position' => '—',
        'no_response' => '—',
        'empty_filtered' => 'No invitations found for the selected filters',
    ],

    'status' => [
        'accepted' => 'Accepted',
        'pending' => 'Pending',
        'maybe' => 'Maybe',
        'sent' => 'Sent',
        'declined' => 'Declined',
        'rejected' => 'Rejected',
    ],

    'import' => [
        'label' => 'CSV File',
        'hint' => '(name, email, phone, nationality)',
    ],

    'js' => [
        'resend_success' => 'Resent successfully',
        'resend_failed' => 'Failed to resend',
        'link_unavailable' => 'Link is unavailable',
        'link_copied' => 'Link copied',
        'copy_failed' => 'Unable to copy automatically',
        'copy_fetch_error' => 'Error while fetching link',
        'select_one' => 'Select at least one invitation',
        'confirm_bulk_resend' => 'Resend :count selected invitations?',
        'confirm_resend_all_filtered' => 'Resend all invitations for the current filter?',
    ],

    'api' => [
        'feature_pro_only' => 'This feature is available for professional plans only',
        'import_success' => 'Imported :imported invitations successfully (:skipped skipped)',
        'import_failed' => 'Import failed: :error',
        'resend_success' => 'Invitation resent successfully',
        'resend_failed' => 'Failed to resend invitation',
        'bulk_resend_success' => 'Sent :count invitations',
        'copied' => 'Link copied',
        'link_text' => 'Please open: :url',
    ],
];

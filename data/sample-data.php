<?php
// Sample data - replace with actual database queries

// Stats data
$facultyCount = 124;
$facultyIncrease = 5;
$departmentCount = 8;
$evaluationCount = 42;
$pendingEvaluations = 12;
$developmentCount = 15;
$upcomingDevelopment = 3;

// Recent activities
$recentActivities = [
    [
        'title' => 'Dr. Sarah Johnson added to Computer Science',
        'time' => 'Today at 10:30 AM',
        'color' => 'blue',
        'icon' => '<path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path>'
    ],
    [
        'title' => 'Evaluation completed for Dr. Michael Brown',
        'time' => 'Yesterday at 2:15 PM',
        'color' => 'purple',
        'icon' => '<path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>'
    ],
    [
        'title' => 'Development program \'Teaching Excellence\' created',
        'time' => 'Apr 2, 2025',
        'color' => 'green',
        'icon' => '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>'
    ]
];

// Upcoming events
$upcomingEvents = [
    [
        'month' => 'APR',
        'day' => '05',
        'title' => 'Faculty Evaluation Meeting',
        'time' => '10:00 AM - Computer Science'
    ],
    [
        'month' => 'APR',
        'day' => '08',
        'title' => 'Teaching Workshop',
        'time' => '1:00 PM - Conference Room A'
    ],
    [
        'month' => 'APR',
        'day' => '12',
        'title' => 'Department Heads Meeting',
        'time' => '9:30 AM - Board Room'
    ]
];

// Pending tasks
$pendingTasks = [
    [
        'id' => 1,
        'description' => 'Complete Dr. Thompson\'s evaluation',
        'completed' => false
    ],
    [
        'id' => 2,
        'description' => 'Review department budget proposal',
        'completed' => false
    ],
    [
        'id' => 3,
        'description' => 'Schedule meeting with new faculty members',
        'completed' => false
    ]
];
?>
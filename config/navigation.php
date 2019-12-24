<?php

return [
    'sections' => [
        'manage' => [
            'title' => 'Администрирование',
            'items' => [
                [
                    'is_tree' => false,
                    'title' => 'Администраторы',
                    'route_name' => 'admin.admins',
                    'item_active_on' => 'admin/admins*',
                    'icon' => 'la la-users',
                    'roles' => [
                        'admin'
                    ]
                ]
            ],
            'roles' => [
                'admin'
            ]
        ],

        'reports' => [
            'title' => 'Отчеты',
            'items' => [
                [
                    'is_tree' => false,
                    'title' => 'План/факт закупа',
                    'route_name' => 'admin.reports.sales_plan.index',
                    'item_active_on' => 'admin/reports/sales-plan*',
                    'icon' => 'la la-bar-chart',
                    'roles' => [
                        'admin'
                    ]
                ]
            ],
            'roles' => [
                'admin'
            ]
        ],
    ],
];

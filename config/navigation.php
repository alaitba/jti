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
                    'item_active_on' => 'admins*',
                    'icon' => 'la la-users',
                    'roles' => [
                        'admin'
                    ]
                ],
                [
                    'is_tree' => false,
                    'title' => 'Призы',
                    'route_name' => 'admin.rewards.index',
                    'item_active_on' => 'rewards*',
                    'icon' => 'la la-gift',
                    'roles' => [
                        'admin'
                    ]
                ],
                [
                    'is_tree' => false,
                    'title' => 'Новости',
                    'route_name' => 'admin.news.index',
                    'item_active_on' => 'news*',
                    'icon' => 'la la-newspaper-o',
                    'roles' => [
                        'admin'
                    ]
                ],
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
                    'item_active_on' => 'reports/sales-plan*',
                    'icon' => 'la la-bar-chart',
                    'roles' => [
                        'admin'
                    ]
                ],
                [
                    'is_tree' => false,
                    'title' => 'Зарегистрированные продавцы',
                    'route_name' => 'admin.reports.partners.index',
                    'item_active_on' => 'reports/partners*',
                    'icon' => 'la la-users',
                    'roles' => [
                        'admin'
                    ]
                ],
            ],
            'roles' => [
                'admin'
            ]
        ],

        'notifications' => [
            'title' => 'Уведомления',
            'items' => [
                [
                    'is_tree' => false,
                    'title' => 'Уведомление для всех пользователей ВП',
                    'route_name' => 'admin.notifications.all.index',
                    'item_active_on' => 'notifications/all*',
                    'icon' => 'la la-bell',
                    'roles' => [
                        'admin'
                    ]
                ],
                [
                    'is_tree' => false,
                    'title' => 'Уведомление для пользователей ВП по списку',
                    'route_name' => 'admin.notifications.bylist.index',
                    'item_active_on' => 'notifications/bylist*',
                    'icon' => 'la la-bell',
                    'roles' => [
                        'admin'
                    ]
                ],
            ],
            'roles' => [
                'admin'
            ]
        ],
    ],
];

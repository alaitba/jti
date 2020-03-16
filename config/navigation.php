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
                    'title' => 'Бренды',
                    'route_name' => 'admin.brands.index',
                    'item_active_on' => 'brands*',
                    'icon' => 'la la-trademark',
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
                [
                    'is_tree' => false,
                    'title' => 'Викторины и опросы',
                    'route_name' => 'admin.quizzes.index',
                    'item_active_on' => 'quizzes*',
                    'icon' => 'la la-question-circle',
                    'roles' => [
                        'admin'
                    ]
                ],
                [
                    'is_tree' => false,
                    'title' => 'Выходные и праздники',
                    'route_name' => 'admin.holidays.index',
                    'item_active_on' => 'holidays*',
                    'icon' => 'la la-calendar',
                    'roles' => [
                        'admin'
                    ]
                ],
                [
                    'is_tree' => false,
                    'title' => 'Обратная связь',
                    'route_name' => 'admin.feedback.index',
                    'item_active_on' => 'feedback*',
                    'icon' => 'la la-comment',
                    'roles' => [
                        'admin'
                    ]
                ],
                [
                    'is_tree' => false,
                    'title' => 'Слайдер на главной',
                    'route_name' => 'admin.slider.index',
                    'item_active_on' => 'slider*',
                    'icon' => 'la la-image',
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
                [
                    'is_tree' => false,
                    'title' => 'Авторизация продавцов',
                    'route_name' => 'admin.reports.partner-auth.index',
                    'item_active_on' => 'reports/partner-auth*',
                    'icon' => 'la la-sign-in',
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
                    'title' => 'Уведомления для пользователей ВП',
                    'route_name' => 'admin.notifications.index',
                    'item_active_on' => 'notifications/*',
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

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Contracts\NotifyTemplate;

/**
 * Class EmailTemplateCommand
 * @package App\Console\Commands
 */
class EmailTemplateCommand extends Command
{
    //private $admin;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'core:create-email-templates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создание шаблонов писем';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $data = [
            /**
             * Письмо для подтверждения регистрации
             */
            [
                'type' => 'email',
                'name' => 'CustomerRegistrationConfirmMail',
                'display_name' => 'Подтверждение регистрации пользователем',
                'params' => json_encode([
                    'variables' => [
                        'first_name' => [
                            'title' => 'Имя'
                        ],
                        'last_name' => [
                            'title' => 'Фамилия'
                        ],
                        'email' => [
                            'title' => 'E-mail клиента'
                        ],
                        'confirm_code' => [
                            'title' => 'Код подтверждения'
                        ],
                        'confirm_url' => [
                            'title' => 'Ссылка подтверждения'
                        ]
                    ],

                    'fields' => [
                        'subject' => [
                            'type' => 'text',
                            'label' => 'Тема письма',

                        ],
                        'body' => [
                            'type' => 'textarea',
                            'label' => 'Текст письма',
                        ]
                    ]
                ])
            ],


            /**
             * Письмо, которое отправляется после подтверждения с сгенерированным паролем
             */
            [
                'type' => 'email',
                'name' => 'CustomerPasswordAfterConfirmMail',
                'display_name' => 'Сгенерированный пароль после подтверждения регистрации',
                'params' => json_encode([
                    'variables' => [
                        'first_name' => [
                            'title' => 'Имя'
                        ],
                        'last_name' => [
                            'title' => 'Фамилия'
                        ],
                        'email' => [
                            'title' => 'E-mail клиента'
                        ],
                        'password' => [
                            'title' => 'Сгенерированный пароль'
                        ],
                        'confirm_code' => [
                            'title' => 'Код подтверждения'
                        ],
                        'confirm_url' => [
                            'title' => 'Ссылка подтверждения'
                        ]
                    ],

                    'fields' => [
                        'subject' => [
                            'type' => 'text',
                            'label' => 'Тема письма',

                        ],
                        'body' => [
                            'type' => 'textarea',
                            'label' => 'Текст письма',
                        ]
                    ]
                ])
            ],

            /**
             * Письмо восстановления пароля
             */
            [
                'type' => 'email',
                'name' => 'CustomerPasswordRemindMail',
                'display_name' => 'Письмо с ссылкой на восстановление пароля',
                'params' => json_encode([
                    'variables' => [
                        'first_name' => [
                            'title' => 'Имя'
                        ],
                        'last_name' => [
                            'title' => 'Фамилия'
                        ],
                        'email' => [
                            'title' => 'E-mail клиента'
                        ],

                        'confirm_code' => [
                            'title' => 'Код подтверждения'
                        ],
                        'confirm_url' => [
                            'title' => 'Ссылка подтверждения'
                        ]

                    ],



                    'fields' => [
                        'subject' => [
                            'type' => 'text',
                            'label' => 'Тема письма',

                        ],
                        'body' => [
                            'type' => 'textarea',
                            'label' => 'Текст письма',
                        ]
                    ]
                ])
            ],

            /**
             * Уведомление администратора о новом сообщение с сайта
             */
            [
                'type' => 'email',
                'name' => 'AdminFeedbackNotify',
                'display_name' => 'Сообщение с сайта (feedback)',
                'params' => json_encode([
                    'variables' => [
                        'full_name' => [
                            'title' => 'ФИО клиента'
                        ],
                        'email' => [
                            'title' => 'E-mail клиента'
                        ],
                        'message' => [
                            'title' => 'Сообщение клиента'
                        ],

                    ],

                    'fields' => [
                        'subject' => [
                            'type' => 'text',
                            'label' => 'Тема письма',

                        ],
                        'body' => [
                            'type' => 'textarea',
                            'label' => 'Текст письма',
                        ]
                    ]
                ])
            ],
        ];

        app(NotifyTemplate::class)->truncate();

        foreach ($data as $item)
        {
            if (!app(NotifyTemplate::class)->where('name', $item['name'])->count())
            {
                app(NotifyTemplate::class)->create($item);
            }
        }

        $this->info('Шаблоны писем успешно созданы');
    }
}

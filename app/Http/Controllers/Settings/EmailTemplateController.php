<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Contracts\NotifyTemplate as NotifyTemplateContract;

class EmailTemplateController extends Controller
{
    public function index()
    {
        $templates = app(NotifyTemplateContract::class)->where('type', 'email')->get();

        return view('email_template.index', [
            'title' => 'Шаблоны писем',
            'templates' => $templates
        ]);
    }

    public function create()
    {
        return response()->json([
            'functions' => [
                'updateModal' => [
                    'params' => [
                        'modal' => 'largeModal',
                        'title' => 'Создание шаблона',
                        'content' => view('email_template.form', [
                            'formAction' => route('admin.email_templates.store'),
                            'buttonText' => 'Создать'
                        ])->render(),
                    ]
                ]
            ]
        ]);
    }

    public function store(Request $request)
    {
        $params = explode(',', $request->params);
        $variables = [];
        $array = [];

        foreach ($params as $key => $value) {
//            $b = explode(', ', $value);
            $name = explode(':', $value);
            $variables[$name[0]] = ['title' => $name[1]];
        }

        $array['variables'] = $variables;

        $data = json_encode([
            'fields' => [
                'body' => ['type' => 'textarea', 'label' => 'Текст письма'],
                'subject' => ['type' => 'text', 'label' => 'Тема письма'],
            ],
            'variables' => $array['variables']]);
//        dd($data);

        $template = app(NotifyTemplateContract::class)->create([
            'type' => 'email',
            'name' => $request->get('name'),
            'display_name' => $request->get('display_name'),
            'params' => $data,
        ]);

        $templates = app(NotifyTemplateContract::class)->where('type', 'email')->get();

        return response()->json([
            'functions' => [
                'closeModal' => [
                    'params' => [
                        'modal' => 'largeModal',
                    ]
                ],
                'redirect' => [
                    'params' => [
                        'url' => route('admin.email_templates.index'),
                    ]
                ],
            ]
        ]);
    }


    public function edit(int $id)
    {
        $item = app(NotifyTemplateContract::class)->find($id);

        $params = json_decode($item->params, true);

        asort($params['fields']);
        $fields = $params['fields'];
        $variables = $params['variables'];
        $data = json_decode($item->data, true);


        return response()->json([
            'functions' => [
                'updateModal' => [
                    'params' => [
                        'modal' => 'largeModal',
                        'title' => 'Редактирование шаблона',
                        'content' => view('email_template.template', [
                            'fields' => $fields,
                            'variables' => $variables,
                            'data' => $data,
                            'formAction' => route('admin.email_templates.update', ['id' => $id]),
                            'buttonText' => 'Сохранить'
                        ])->render(),
                    ]
                ]
            ]
        ]);
    }

    public function update(int $id, Request $request)
    {
        $item = app(NotifyTemplateContract::class)->find($id);

        if ($item) {
            $item->data = json_encode($request->input('data'));

            $item->save();
        }

        $templates = app(NotifyTemplateContract::class)->where('type', 'email')->get();

        return response()->json([
            'functions' => [
                'closeModal' => [
                    'params' => [
                        'modal' => 'largeModal',
                    ]
                ],
                'updateTableRow' => [
                    'params' => [
                        'selector' => '.ajax-content',
                        'row' => '.row-' . $id,
                        'content' => view('email_template.index', [
                            'title' => 'Шаблоны писем',
                            'templates' => $templates
                        ])->render()
                    ]
                ]
            ]
        ]);
    }
}

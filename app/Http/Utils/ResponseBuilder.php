<?php namespace App\Http\Utils;

use App\Ui\Components\Table\TableContent;
use App\Ui\LayoutBuilder;
use App\Ui\Components\Modal\Modal;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\MessageBag;
use Illuminate\View\View;
use Throwable;

/**
 * Class ResponseBuilder
 * @package App\Http\Utils
 */
class ResponseBuilder
{
    /**
     * @var array
     */
    protected $functions = [];


    /**
     * Обновление данных таблицы
     * @param string $selector
     * @param TableContent $content
     * @param array $filters
     * @throws Throwable
     */
    public function updateTableContent(string $selector, TableContent $content, array $filters = [])
    {
        $data = [

            'params' => [
                'selector' => $selector,
                'content' => $content->renderHtml(),
                'pagination' => $content->renderPagination($filters),
            ],
        ];

        $this->functions['updateTableContent'] = $data;
    }

    /**
     * Добавление строки в таблицу
     * @param TableContent $content
     * @param string $selector
     * @throws Throwable
     */
    public function prependTableRow(TableContent $content, string $selector)
    {
        $data = [
            'params' => [
                'selector' => $selector,
                'content' => $content->renderHtml()
            ]
        ];

        $this->functions['prependTableRow'] = $data;
    }

    /**
     * Обновление строки таблицы
     * @param TableContent $content
     * @param string $selector
     * @param int $rowNumber
     * @throws Throwable
     */
    public function updateTableRow(TableContent $content, string $selector, int $rowNumber)
    {
        $data = [
            'params' => [
                'row' => '.row-' . $rowNumber,
                'selector' => $selector,
                'content' => $content->renderHtml()
            ]
        ];

        $this->functions['updateTableRow'] = $data;
    }


    /**
     * Обновление контента модального окна
     * @param Modal $modal
     * @throws Throwable
     */
    public function updateModal(Modal $modal)
    {

        $data = [

            'params' => [
                'modal' => $modal->getModal(),
                'title' => $modal->getTitle(),
                'content' => $modal->renderContent(),
            ],
        ];

        $this->functions['updateModal'] = $data ;

    }

    /**
     * Закрытие модального окна
     * @param string $modal
     */
    public function closeModal(string $modal)
    {
        $data = [
            'params' => [
                'modal' => $modal
            ]
        ];

        $this->functions['closeModal'] = $data ;
    }

    /**
     * Инициализация редактора
     */
    public function initEditor()
    {
        $data = ['params' => []];

        $this->functions['initEditor'] = $data ;

    }

    /**
     * Инициализация дейт пикера
     */
    public function initDatePicker()
    {
        $data = ['params' => []];
        $this->functions['initDatePicker'] = $data ;
    }

    /**
     * Инициализация дейт тайм пикера
     */
    public function initDateTimePicker()
    {
        $data = ['params' => []];
        $this->functions['initDateTimePicker'] = $data ;
    }

    public function initSelect2()
    {
        $data = ['params' => []];
        $this->functions['initSelect2'] = $data ;
    }


    /**
     * Ответ отрендеренной вьюхой фронту
     * @param LayoutBuilder $builder
     * @return View
     */
    public function makeLayout(LayoutBuilder $builder): View
    {
        return $builder->render();
    }

    /**
     * Ответ json-om фронту
     * @return JsonResponse
     */
    public function makeJson(): JsonResponse
    {
        $funcs = [];
        foreach ($this->functions as $key => $function)
        {

            if ($function)
            {
                $funcs[$key] = $function;
            }
        }

        return response()->json([
            'functions' => $funcs,
        ]);
    }

    /**
     * @param $data
     * @return JsonResponse
     */
    public function apiSuccess($data)
    {
        return response()->json([
            'error' => 0,
            'data' => $data,
        ], 200);
    }

    /**
     * @param string $error
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    public function apiError(string $error, string $message, int $code = 500)
    {
        return response()->json([
            'error' => $error,
            'message' => $message,
        ], $code);
    }

    /**
     * @param string $error
     * @param MessageBag $validationErrors
     * @return JsonResponse
     */
    public function apiRequestValidationError(string $error, MessageBag $validationErrors)
    {
        return response()->json([
            'error' => $error,
            'message' => $validationErrors,
        ], 422);
    }

    /**
     * Обновление данных в элементе
     * @param string $selector
     * @param string $content
     */
    public function updateElementContent(string $selector, string $content)
    {
        $data = [
            'params' => [
                'selector' => $selector,
                'content' => $content
            ]
        ];

        $this->functions['updateElementContent'] = $data ;
    }

    /**
     * Замена данных в элементе
     * @param string $selector
     * @param string $content
     */
    public function replaceElementContent(string $selector, string $content)
    {
        $data = [
            'params' => [
                'selector' => $selector,
                'content' => $content
            ]
        ];

        $this->functions['replaceElementContent'] = $data ;
    }

    /**
     * Показ нотификации
     * @param string $message
     * @param string $type
     */
    public function showNotify(string $message, string $type = 'success')
    {
        $data = [
            'params' => [
                'message' => $message,
                'type' => $type
            ]
        ];

        $this->functions['showNotify'] = $data ;
    }

    /**
     * @param string $url
     */
    public function redirect(string $url)
    {
        $data = [
            'params' => [
                'url' => $url,

            ]
        ];

        $this->functions['redirect'] = $data ;
    }

    /**
     * @param string $header
     * @param string $message
     * @param string $type
     */
    public function showAlert(string $header, string $message, string $type = 'error')
    {
        $data = [
            'params' => [
                'alert_type' => $type,
                'alert_header' => $header,
                'alert_message' => $message

            ]
        ];

        $this->functions['showAlert'] = $data ;
    }

    /**
     * Отдаем API ответ для Select 2 поиска данных
     *
     * @param $data
     * @return JsonResponse
     * @author Rishat Sultanov
     */
    public function apiSelectTwo($data)
    {
        return response()->json($data, 200);
    }

    /**
     * @param string $selector
     * @param $content
     * @param $pagination
     */
    public function updateTableContentHtml(string $selector, string $content, string $pagination)
    {
        $data = [

            'params' => [
                'selector' => $selector,
                'content' => $content,
                'pagination' => $pagination,
            ],
        ];

        $this->functions['updateTableContent'] = $data;
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Dictionaries",
    description: "API для справочников системы"
)]
class DictionariesController extends Controller
{
    #[OA\Get(
        path: "/v1/dictionaries/pause-reasons",
        summary: "Получить причины приостановки обучения",
        tags: ["Dictionaries"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Список причин",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "items",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "id", type: "string"),
                                    new OA\Property(property: "label", type: "string"),
                                    new OA\Property(property: "icon", type: "string")
                                ],
                                type: "object"
                            )
                        )
                    ]
                )
            )
        ]
    )]
    public function getPauseReasons(): JsonResponse
    {
        $items = [
            ['id' => 'vacation', 'label' => 'Отпуск', 'icon' => '🏖️'],
            ['id' => 'illness', 'label' => 'Болезнь', 'icon' => '🤒'],
            ['id' => 'travel', 'label' => 'Поездка', 'icon' => '✈️'],
            ['id' => 'family', 'label' => 'Семейные обстоятельства', 'icon' => '👨‍👩‍👧'],
            ['id' => 'school', 'label' => 'Школьная нагрузка', 'icon' => '📚'],
            ['id' => 'financial', 'label' => 'Финансовые трудности', 'icon' => '💰'],
            ['id' => 'other', 'label' => 'Другое', 'icon' => '📝'],
        ];

        return response()->json(['items' => $items]);
    }

    #[OA\Get(
        path: "/v1/dictionaries/payment-methods",
        summary: "Получить методы оплаты",
        tags: ["Dictionaries"],
        responses: [
            new OA\Response(response: 200, description: "Список методов оплаты")
        ]
    )]
    public function getPaymentMethods(): JsonResponse
    {
        $items = [
            ['id' => 'card', 'label' => 'Карта', 'icon' => '💳'],
            ['id' => 'transfer', 'label' => 'Банковский перевод', 'icon' => '🏦'],
            ['id' => 'cash', 'label' => 'Наличные', 'icon' => '💵'],
            ['id' => 'blik', 'label' => 'BLIK', 'icon' => '📱'],
            ['id' => 'paypal', 'label' => 'PayPal', 'icon' => '🅿️'],
        ];

        return response()->json(['items' => $items]);
    }

    #[OA\Get(
        path: "/v1/dictionaries/discount-types",
        summary: "Получить типы скидок",
        tags: ["Dictionaries"],
        responses: [
            new OA\Response(response: 200, description: "Список типов скидок")
        ]
    )]
    public function getDiscountTypes(): JsonResponse
    {
        $items = [
            ['id' => 'family', 'label' => 'Семейная скидка', 'maxValue' => 20, 'icon' => '👨‍👩‍👧‍👦'],
            ['id' => 'referral', 'label' => 'Реферальная программа', 'maxValue' => 15, 'icon' => '🤝'],
            ['id' => 'loyalty', 'label' => 'За лояльность', 'maxValue' => 10, 'icon' => '⭐'],
            ['id' => 'complaint', 'label' => 'Компенсация жалобы', 'maxValue' => 50, 'icon' => '🔧'],
            ['id' => 'promo', 'label' => 'Промо-акция', 'maxValue' => 30, 'icon' => '🎁'],
            ['id' => 'early_bird', 'label' => 'Ранняя регистрация', 'maxValue' => 25, 'icon' => '🐦'],
            ['id' => 'sibling', 'label' => 'Второй ребенок', 'maxValue' => 15, 'icon' => '👧👦'],
        ];

        return response()->json(['items' => $items]);
    }

    #[OA\Get(
        path: "/v1/dictionaries/refund-reasons",
        summary: "Получить причины возврата средств",
        tags: ["Dictionaries"],
        responses: [
            new OA\Response(response: 200, description: "Список причин возврата")
        ]
    )]
    public function getRefundReasons(): JsonResponse
    {
        $items = [
            ['id' => 'cancel', 'label' => 'Отмена программы', 'icon' => '❌'],
            ['id' => 'dissatisfaction', 'label' => 'Недовольство качеством', 'icon' => '😞'],
            ['id' => 'health', 'label' => 'Проблемы со здоровьем', 'icon' => '🏥'],
            ['id' => 'relocation', 'label' => 'Переезд', 'icon' => '🚚'],
            ['id' => 'schedule', 'label' => 'Не подходит расписание', 'icon' => '📅'],
            ['id' => 'duplicate', 'label' => 'Дублирование платежа', 'icon' => '🔄'],
            ['id' => 'overpayment', 'label' => 'Переплата', 'icon' => '💸'],
            ['id' => 'other', 'label' => 'Другое', 'icon' => '📝'],
        ];

        return response()->json(['items' => $items]);
    }

    #[OA\Get(
        path: "/v1/dictionaries/tariffs",
        summary: "Получить тарифные планы",
        tags: ["Dictionaries"],
        responses: [
            new OA\Response(response: 200, description: "Список тарифов")
        ]
    )]
    public function getTariffs(): JsonResponse
    {
        $items = [
            [
                'id' => 'basic_4',
                'label' => 'Базовый (4 занятия)',
                'price' => 300,
                'lessons' => 4,
                'pricePerLesson' => 75,
                'icon' => '📘',
            ],
            [
                'id' => 'standard_8',
                'label' => 'Стандарт (8 занятий)',
                'price' => 550,
                'lessons' => 8,
                'pricePerLesson' => 68.75,
                'icon' => '📗',
                'popular' => true,
            ],
            [
                'id' => 'premium_12',
                'label' => 'Премиум (12 занятий)',
                'price' => 780,
                'lessons' => 12,
                'pricePerLesson' => 65,
                'icon' => '📙',
            ],
            [
                'id' => 'premium_16',
                'label' => 'Премиум+ (16 занятий)',
                'price' => 1000,
                'lessons' => 16,
                'pricePerLesson' => 62.5,
                'icon' => '📕',
            ],
            [
                'id' => 'individual',
                'label' => 'Индивидуальный',
                'price' => 150,
                'lessons' => 1,
                'pricePerLesson' => 150,
                'icon' => '👤',
            ],
        ];

        return response()->json(['items' => $items]);
    }
}


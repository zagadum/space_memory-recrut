<?php
return [
    'menu'=>[
        'title'=>'Олімпіади',
        'practicians'=>'Практиканти',

        'index'=>'Активні олімпіади',
        'finish'=>'Завершені олімпіади',
        'ratting'=>'Рейтинг олімпіад',
        'ratting_result'=>'Результати Олімпіади',
        'subscribe'=>'Список учасників олімпіади',
        'ratting_details'=>'Результати проходження олімпіади :name',
        'payments'=>'Платежі олімпіад',
        'create'=>'Створення Олімпіади Space',

    ],
    'actions'=>[
        'index'=>'Олімпіади',
        'exit'=>'Вийти',
        'confirm_exit'=>'Вийти з олімпіади?',
        'confirm_exit_text'=>'Ти справді хочеш вийти?',
        'open_result'=>'Після підтвердження всі результати будуть доступними для всіх. Відкрити ?',
        'close_result'=>'Після підтвердження все результаты будут закриті для всіх. Сховати ?',


    ],
    'errors'=>[
        'date_activate'=>'Дата активації має бути сьогодні чи пізніше, а дата закінчення не раніше дати початку.',
        'announcement_period'=>'Дата початку анонсу має бути меншою за дату старту активації.',
        'announcement_and_start'=>'Дата початку анонсу має бути меншою за дату старту активації.',
        'need_params'=>'Додайте рівень складності, щоб продовжити',
    ],

    'btn'=>[
        'title'=>'Олимпиада',
        'subscribe'=>'Брати участь',
        'prev'=>'Попередній',
        'apply'=>'Застосувати',
        'next'=>'Далі',
        'preview'=>'Перегляд',
        'finish'=>'Завершить',
        'edit'=>'Редагувати',
        'edit_list'=>'Взяти участь',
        'save'=>'Зберегти',
        'add_params'=>'Додати дисципліну',
        'all_countries'=>'Всі країни',
        'create_olympiad'=>'Створити олімпіаду',
        'create_olympiad_space'=>'Олимпіада SpaceM',
        'create_olympiad_ads'=>'Оголошення про олімпіаду',
        'add_discipline'=>'Додати дисципліну',
        'remove_result'=>'Видалити результати',
        'remove_result_practica'=>'Видалити тест-результати',
        'open_result'=>'Відкрити результати олімпіади'
    ],
    'lang'=>['ua'=>'Українська',
            'en'=>'English',
            'pl'=>'Polski',
            'ru'=>'Русский',
            'es'=>'Español',
            'fr'=>'Français',
            'de'=>'Deutsch',
            'it'=>'Italiano',
            'pt'=>'Português'],
    'pages'=>[
        'total'=>'Всього',
    ],

        'step1'=>[
            'title'=>'1 этап',
            'international'=>'Міжнародна, для всіх країн',
            'region'=>'Область',
            'add_city'=>'Додати місто',
            'hide_add_city'=>'Сховати додавання міста',

            'name'=> ['uk'=>'Назва','pl'=>'Tytyl','en'=>'Title'],
            'describe'=> ['uk'=>'Короткий опис','pl'=>'Krótki opis','en'=>'Short Description'],

            'cover'=> ['uk'=>'Обкладинка олімпіади','pl'=>'Okładka olimpiady','en'=>'Cover of the olympiad'],
            'cover_info'=> ['uk'=>'*Роздільна здатність 800x500','pl'=>'*Rozdzielczość obrazka 800x500','en'=>'*Image resolution 800x500'],
            'show_result'=>'Відображення результатів',
            'show_result_yes'=>'Автоматичне відображення на наступний день',
            'show_result_no'=>'Відображення результатів на кнопку у списку учасників',
        ],
        'step2'=>[
            'title'=>'2 этап',
            'full_describe'=> ['uk'=>'Детальний oпис олімпіади','pl'=>'Szczegółowy opis Olimpiady','en'=>'Detailed description of the Olympiad'],
        ],
        'step3'=>[
            'title'=>'3 этап',
            'price'=>'Ціна',
            'international_currency'=>'Міжнародна валюта',
            'local_currency'=>'Валюта в Україні',
            'announcement_period'=>'Дата анонаса  (Інтервал від-до)',
            'activation_period'=>'Дата активації (Інтервал від-до)',
        ],
        'step4'=>[
            'title'=>'4 этап',
        ],
    'step5'=>[
        'title'=>'5 этап',
    ],

    'main'=>[
        'promotion'=>[
            'ads'=>'Оголошення',
            'olympiad'=>'Олімпіада',
            'locale'=>'місцева',
            'international'=>'міжнародна',
        ],
        'title'=>'Олімпіади',
        'active'=>['title'=>'Активные олимпиады'],
        'finish'=>['title'=>'Завершені'],

        'description'=>'Участие в олимпиадах',
        'start'=>'Начать олимпиаду',
        'params'=>'Параметры олимпиады',
        'tasks'=>'Задания олимпиады',
        'results'=>'Результаты олимпиады',
        'rating'=>'Рейтинг олимпиады',
        'payments'=>'Платежи олимпиады',
    ],
    'practicians'=>[
        'action'=>[
            'print-list'=>'Роздрукувати список учасників',
            'print-ratting'=>'Роздрукувати рейтинг учасників',
            'bnt-result'=>'Результати'
        ],
        'title'=>'Практиканты',
        'total'=>'Всього',
        'list'=>[
            'title'=>'Список учасників олімпіади',
            'title_tab'=>'Список учасників',
            'result_tab'=>'Результати Олімпіади'

        ],
        'columns'=>[
                    'place' => 'Місце',
                    'full_name' => 'Ім\'я Прізвище',
                    'age' => 'Вік',
                    'teacher' => 'Вчитель',
                    'country' => 'Країна',
                    'category' => 'Категорія',

                    'total_score' => 'Загальний бал',
                    'good_answear' => 'Відповідей',
                    'proc_answear' => '% вірних',
                    'id' => 'ID',
                    'name' => 'Имя',
                    'surname' => 'Прізвище',
                    'lastname' => 'По батькові',
                    'email' => 'Ел. пошта',
                    'phone' => 'Телефон',
                    'school' => 'Школа',
                    'is_pay' => 'Оплачено',
                    'language' => 'Мова Олімпіади',
                    'subscribe_date' => 'Дата',
                    'last_login_at' => 'Останній вхід'
                ]

        ],
    'result'=>[
        'title'=>'Результати олімпіади',
        'title_for'=>'Тестові проби',
        'not_have_result'=>'Результати відсутні',
    ],
    'ratting'=>
        [
            'title'=>'Рейтинг',
            'filter'=>['category'=>'Фільтр за категорією','age'=>'Фільтр за віком'],
            'columns'=>[
                'data' => 'Дата',
                'name' => 'Название олимпиады',
                'country' => 'Страна',
                'practicans_total' => 'Участники',
                'creator' => 'Создатель',

                ]
        ],
    'payments'=>
        [
            'title'=>'Платежи',
            'columns'=>[
                    'id' => 'ID',
                    'payment_date' => 'Дата платежа',
                    'full_name' => 'Имя Фамилия',
                    'participant_id' => 'ID участника',
                    'amount' => 'Сумма',
                    'status' => 'Статус',

            ]
        ],

];

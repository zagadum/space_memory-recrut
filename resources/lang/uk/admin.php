<?php

return [
    'page_title_suffix'=>'Авторська система тренування пам`яті',
    'auth'=>[
        'password'=>'Пароль',
        'email'=>'Ваш E-MAIL',
        'login'=> ['title'=>'Вхід', 'button'=>'Увійти','forgot_password'=>'Забули пароль?']],
    'index'=>[
        'no_items'=>'Не вдалося знайти нічого',
        'try_changing_items'=>'Спробуйте змінити фільтри або додати новий',
        'total'=>'Усього'
    ],
    'forms'=>
        ['select_options'=>'Виберіть',
         'select_time'=>'Виберіть',
         'select_a_date'=>'Виберіть',
         'olimpiad_capacity'=>'без поділу',
         'Olimiad'=>'Олімпіада',

        ],
    'phone'=>[
        'code'=>'Код',
        'nubmer'=>'Телефонний номер',
        'EnterNumber'=>'Введіть номер телефону',
        'example'=>'Приклад'
    ],
    'role'=>[
        'admin'=>'СуперАдмин',
        'teacher'=>'Вчитель',
        'franchisee'=>'Франчази',
    ],
    'operation'=>[
        'succeeded'=>'Операція удачно виконана!',
        'existed'=>'Користувач з даним email вже існує'
    ],

    'training-images-task' => [
        'columns' => [
            'id' => 'ID',
            'name' => 'Ім`я',
            'number' => 'Номер',
            'file_path' => 'Шлях до файлу',
            'lang' => 'Мова',
            'part_word' => 'Частина мови',
        ],
        'actions' => [
            'index' => 'Індекс',
            'create' => 'Створити',
        ],
    ],

    'training-words-task' => [
        'actions' => [
            'index' => 'Індекс',
            'create' => 'Створити',
        ],
        'columns' => [
            'id' => 'ID',
            'name' => 'Слово',
            'part_word' => 'Частина мови',
            'lang' => 'Мова',
        ],
    ],
    'placeholder'=>['search'=>'Пошук'],
    'btn'=>[
        'save'=>'Зберігти',
        'search'=>'Знайти',
        'cancel'=>'Відмінити',
        'logout'=>'Вихід',
        'delete'=>'Видалити',
        'edit'=>'Редагувати',
        'lock'=>'Заблокувати',
        'unlock'=>'Розблокувати',
        'change'=>'Змінити',
        'hide'=>'Сховати',
        'add'=>'Додати',
        'pay_offline'=>'Оплата офлайн',
        'add_settlement'=>'подразделение (населенный пункт)',
        'ok'=>'OK',
        'yes'=>'Так',
        'block'=>'Заблокувати',
        'unblock'=>'Розблокувати',
        'coins'=>'Coin',
        'diams'=>'Diams',
        'print'=>'Роздрукувати',
        ],
    'modal'=>
        [
            'duplicate'=>['title'=>'Дублювати домашне завдання', 'date_move'=>'Дата переносу', 'date_move_help'=>'на дату'],
            'private_home'=>['title'=>'Зробити домашне завдання приватними?'],
            'blocking_franchisee'=>['title'=>'Блокувати Франчайзі', 'reason'=>'Причина блокування', 'reason_help'=>'Причина'],
            'unblocking_franchisee'=>['title'=>'Розблокувати Франчайзі'],
            'bloking_teacher'=>['title'=>'Блокувати Вчителя'],
            'bloking_student'=>['title'=>'Блокувати Учня'],
            'unbloking_teacher'=>['title'=>'Розблокувати Вчителя'],
            'unbloking_student'=>['title'=>'Розблокувати Учня'],
            'disablebloking_teacher'=>['title'=>'Неможливо Блокувати Вчителя'],
            'disablebloking_franchisee'=>['title'=>'Неможливо Блокувати Франчайзі'],
            'disabledelete_teacher'=>['title'=>'Неможливо Видалити Вчителя'],
            'delete'=>['title'=>'Ви точно хочете видалити?'],
            'change_group'=>['title'=>'Ви дійсно бажаєте змінити групу?'],
        ],
    'admin-user' => [
        'title' => 'Users',

        'actions' => [
            'index' => 'Users',
            'create' => 'New User',
            'edit' => 'Edit :name',
            'edit_profile' => 'Редагувати Профіль',
            'edit_password' => 'Редагувати Пароль',
            'change_email'=>'Ваш email змінено',
            'new_email'=>'Новий email',
            'change_password'=>'Ваш пароль змінено',
            'new_password'=>'Новий пароль',

        ],

        'columns' => [
            'id' => 'ID',
            'last_login_at' => 'Last login',
            'first_name' => 'Ім`я',
            'last_name' => 'Last name',
            'email' => 'Email',
            'password' => 'Пароль',
            'surname'=>'Прізвище',
            'patronymic'=>'По батькові',
            'password_repeat' => 'Password Confirmation',
            'activated' => 'Activated',
            'forbidden' => 'Forbidden',
            'language' => 'Мова',

            //Belongs to many relations
            'roles' => 'Roles',

        ],
    ],





    'user' => [
        'title' => 'Users',

        'actions' => [
            'index' => 'Users',
            'create' => 'New User',
            'edit' => 'Edit :name',
            'edit_profile' => 'Edit Profile',
            'edit_password' => 'Edit Password',
        ],

        'columns' => [
            'id' => "ID",
            'name' => "Name",
            'email' => "Email",
            'email_verified_at' => "Email verified at",
            'password' => "Password",
            'password_repeat' => "Password Confirmation",

            //Belongs to many relations
            'roles' => "Roles",

        ],
    ],

    'teacher-group' => [
        'title' => 'Список Груп',
        'title2' => 'Групи',
        'title3' => 'Групи Франчазу',
        'title4' => 'Групa',
        'zoom' => 'Посилання на урок',
        'franchisee'=>'Франчайзі',
        'general_info'=>'Общая информация',
        'group_location' => 'Локация',
        'pages' => ['total'=>'Усього груп'],
        'actions' => [
            'index' => 'Список Груп',
            'create' => 'Додати нову групу',
            'edit' => 'Редагувати :name',
        ],

        'columns' => [
            'id' => 'ID',
            'franchisee_id' => 'Франшиза',
            'teacher_name' => 'Вчитель групи',
            'surname' => 'Прізвище',
            'group_id' => 'Груп',
            'locations' => 'Розташування',

            'workday'=>'День уроку',
            'start_time'=>'Час початок занять',
            'workday1'=>'Пн',
            'workday2'=>'Вт',
            'workday3'=>'Ср',
            'workday4'=>'Чт',
            'workday5'=>'Пт',
            'workday6'=>'Сб',
            'workday7'=>'Вс',
            'name' => 'Назва групи',
            'name_group' => 'Назва групи',
            'age_id' => 'Вік групи',
            'address_group' => 'Адреса групи',
            'name_loaction' => 'Назва локації',
            'start_day' => 'Початок навчання',
            'total_student' => 'Учнів',
            'total_task' => 'Статистика ДЗ',
            'zoom_url' => 'Конференція (URL)',
            'zoom_text' => 'Текс для конференції',
            'zoom_img' => 'Картинка Url',
            'enabled' => 'Enabled',

        ],
    ],

    'teacher' => [
        'title' => 'Список Вчителів',
        'title2' => 'Вчитель',
        'title3' => 'Групи Вчителя',
        'titleCalendar' => 'Календар Вчителя',
        'pages' => [
            'total'=>'Усього вчителів',
            'total_block'=>'Усього заблоковано учнів',
        ],

        'actions' => [
            'index' => 'Список Вчителів',
            'create' => 'Додати Вчителя',
            'create_new' => 'Додати нового вчителя',
            'edit' => 'Редагувати :name',
            'btn_edit'=>'Редагувати вчителя',
            'btn_create'=>'Додати вчителя',
        ],

        'columns' => [
            'id' => 'ID',
            'franchisee_id' => 'Франшиза',

            'surname' => 'Прізвище',
            'first_name' => 'Ім`я',
            'patronymic' => 'По Батькові',
            'phone' => 'Телефон',
            'dob' => 'Дата народження',
            'email' => 'Email',
            'password' => 'Пароль',
            'passport' => 'Паспорт',
            'iin' => 'Іденфікаційний номер',
            'subscibe_email' => 'Email для розсилки',
            'fin_cabinet' => 'Кабінет фінансів',
            'total_group' => 'Групи',
            'total_people' => 'Учні',
            'total_home_task' => '% виконання ДЗ',
            'language' => 'Мова',
            'enabled' => 'Активно',

        ],
    ],


    'student' => [
        'title' => 'Учні',
        'title2' => 'Учень',
        'title_block' => 'Заблоковані учні',
        'title_group' => 'групи',
        'title_groupa' => 'група',


        'actions' => [
            'index' => 'Учні',
            'create' => 'Додати учня',
            'edit' => 'Редагувати :name',
            'print' => 'Друкувати',
            'show_block_student' => 'Відобразити заблокованих учнів',
            'select_first_child' => 'Вибрати першу дитину',
            'parent_btn' => 'батьків',
            'add_parent_btn' => 'Додати',
            'hide_parent_btn' => 'Приховати',
            'history_btn' => 'Історія',
        ],
        'pages' => [
            'total'=>'Усього учнів',
            'total_block'=>'Усього заблоковано учнів',
            ],
        'block' => [
            'franchise'=>'Франчайзі',
            'general'=>'Загальна інформація',
            'parent'=>'Батьки',
            'finance'=>'Фінансова інформація',
        ],

        'changeBalance' => [
            'number'=>'Кiлькiсть',
            'tittle'=>'Змiнити Баланс',
            'description'=>'Коментар',
            'current_balance'=>'Поточний баланс',
            'current_balance_diams'=>'Поточний баланс Diams',
        ],
        'finance'=>[
            'price'=>'Вартість підписки',
            'discount'=>'разом зі скідками',
            'pay'=>'Оплата',
            'pay_online'=>'Учнем онлайн',
            'pay_offline'=>'Оплата пішла офлайн',
            'period'=>'Період',
            'date_payment'=>'Дата оплати',
            'sum_subscription'=>'Сума абонементу',
            'add_comment'=>'Додати коментар',
        ],
        'columns' => [
            'id' => 'ID',
            'fio' => 'ФІО',
            'statistic_hw' => 'Статистика ДЗ',
            'franchisee_id' => 'Франчайзі',
            'group_id' => 'Група',

            'teacher_id' => 'Вчитель',
            'teacher' => 'Вчитель групи',
            'email' => 'Email',
            'subcribe_email' => 'Email для розсилки',
            'password' => 'Пароль',
            'surname' => 'Прізвище',
            'lastname' => 'Ім`я',
            'patronymic' => 'По батькові',

            'parent1_surname' => 'Прізвище',
            'parent1_lastname'=> 'Ім`я',
            'parent1_patronymic'=> 'По батькові',
            'parent1_phone'=>'Телефон',
            'parent2_surname' => 'Прізвище',
            'parent2_first_name'=> 'Ім`я',
            'parent2_patronymic'=> 'По батькові',
            'parent2_phone'=> 'Телефон',
            'parent3_surname' => 'Прізвище',
            'parent3_first_name'=> 'Ім`я',
            'parent3_patronymic'=> 'По батькові',
            'parent3_phone'=>'Телефон',
            'is_twochildren'=>'Вчиться друга дитина (знижка 10%)',


            'dob' => 'Дата народження',
            'phone' => 'Телефон',
            'start_day' => 'Початок навчання',
            'date_finish' => 'Date finish',
            'sum_aboniment' => 'Абонемент',
            'discount' => 'Знижка',
            'balance' => 'Coin',
            'diams' => 'Diams',
            'language' => 'Мова',
            'blocking_reason' => 'Причина блокування',
            'blocking_date' => 'Дата блокування',
            'waiting_date' => 'Дата очікувана',
            'email_verified_at' => 'Email verified at',
            'enabled' => 'Enabled',
            'rang_level' => 'Rang level',
            'blocked' => 'Заблокований',
            'last_login_at' => 'Вхід в портал',

        ],
    ],

    'student-user' => [
        'title' => 'Student-User',

        'actions' => [
            'index' => 'Student-User',
            'create' => 'New Student-User',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',

        ],
    ],

    'homework' => [
        'title' => 'Домашнi завдання',
        'private_title' => 'Індивідуальне завдання',
        'trening' => 'Тренування',
        'second'=>'сек.',
        'minute'=>'хв.',
        'is_done'=>'зроблене',
        'is_start'=>'стартуємо',
        'data_from'=>'Дата на',
        'error_copy_date'=>'На минулі дати копіювати не можна!',
        'data_create_home'=>'Дата створення ДЗ',
        'actions' => [
            'index' => 'Створення тренування',
            'create'=>'Створити',
            'add'=>'Додати',
            'clone'=>'Дублювати',
            'delete_all'=>'Видалити усе',
            'add_traning'=>'Додати тренування'
        ],
        'form_elements'=>[
          'even'=>'Чiткi',
          'incremental'=>'Довiльнi',
          'range_type'=>'Дiапазон чисел',
          'digit_number_list'=>'Кiлькiсть цифр',
          'digit_word_list'=>'Кiлькiсть слів',
          'digit_picture_list'=>'Кiлькiсть картинок',
          'capacity'=>'Розряднiсть',
          'training_type'=>'Тип тренування',
          'olympiad_type'=>'Олімпіада',
          'learn'=>'Знайомство',
          'training'=>'Тренування',
          'prof'=>'Профi',
            'easy'=>'Учень',
            'learn2'=>'Юніор',
          'profi'=>'Профi',
          'practice'=>'Тренування',
          'interval'=>'Швидкiсть',
          'interval2'=>'Швидкiсть/число',
          'timer'=>'Час',
          'repeat_number'=>'Кiлькiсть повторень',
          'repeat_number2'=>'К-сть завдань',
          'qty_expressions'=>'Кількість прикладів',
          'language'=>'Мова',

        ],
    ],

    'training' => [
        'title' => 'Тренування',

        'actions' => [
            'index' => 'Training',
            'create' => 'Створення Тренування',
            'edit' => 'Edit :name',
            'start'=>'Старт',
        ],

        'columns' => [
            'id' => 'ID',
            'training_id' => 'Training',
            'type_training' => 'Type training',
            't_digital' => 'T digital',
            't_bitness' => 'T bitness',
            't_repetitions' => 'T repetitions',
            't_interlval' => 'T interlval',
            'enabled' => 'Enabled',

        ],
    ],

    'training-statistic' => [
        'title' => 'Training Statistics',

        'actions' => [
            'index' => 'Training Statistics',
            'create' => 'New Training Statistic',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'training_id' => 'Training',
            'name' => 'Name',
            'dates' => 'Dates',
            'tolal_good' => 'Total good',
            'tolal_bad' => 'Total bad',
            'total_today' => 'Total today',

        ],
    ],

    'parent' => [
        'title' => 'Parents',

        'actions' => [
            'index' => 'Parents',
            'create' => 'New Parent',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'student_id' => 'Student',
            'surname' => 'Surname',
            'name' => 'Name',
            'phone' => 'Phone',
            'enabled' => 'Enabled',

        ],
    ],

    'student-payment' => [
        'title' => 'Календар оплат',

        'actions' => [
            'index' => 'Календар оплат',
            'create' => 'Нова оплата',
            'edit' => 'Редагувати :name',
        ],

        'columns' => [
            'id' => 'ID',
            'student_id' => 'Студент',
            'date_pay' => 'Дата оплаты',
            'date_finish' => 'Дата закінчення',
            'sum_aboniment' => 'Сума абонемента',
            'type_aboniment' => 'Тип абонемента',
            'type_pay' => 'Тип оплаты',
            'enabled' => 'Enabled',

        ],
    ],
    'ads' => [
        'title' => 'Реклама на головній',
        'actions' => [
            'index' => 'Реклама',
            'create' => 'Додати Рекламу',
            'create_new' => 'Додати нового франчайзі',
            'edit' => 'Редагувати :name',
            'btn_create'=>'Додати Рекламу',
            'btn_unblock'=>'Додати Рекламу',
            'btn_block'=>'Заблокувати'
        ],
        'columns' => [
            'id' => 'ID',
            'url' => 'Посилання',
            'img' => 'Малюнок реклами',

            'enabled' => 'Вкл.',
            'total_group'=>'Груп',
            'total_people'=>'Учнів',
            'total_teacher' => 'Вчителів',
        ],
        ],
    'managers' => [
        'title' => 'Список Менеджерів',
        'title2' => 'Менеджері',
        'actions' => [
            'index' => 'Менеджери',
            'create' => 'Додати Менеджера',
            'create_new' => 'Додати нового менеджера',
            'edit' => 'Редагувати :name',
            'change_email' => 'Змінити email',
            'change_password' => 'Змінити пароль',
            'btn_create'=>'Додати менеджера',
            'btn_unblock'=>'Розблокувати менеджера',
            'btn_block'=>'Заблокувати'
            ],
        'columns' => [
            'id' => 'ID',
            'surname' => 'Прізвище',
            'first_name' => 'Ім`я',
            'patronymic' => 'По батькові',
            'country_id' => 'Країна',
            'region_id' => 'Регіон',
            'city_id' => 'Місто',
            'locality' => 'Населений пункт',

            'phone' => 'Телефон',
            'email' => 'Email',
            'password' => 'Пароль',

            'fin_royalty' => 'Роялті',
            'fin_pr' => 'PR відрахування',
            'fin_legal' => 'Номер контракта',
            'fin_address' => 'Юридична адресса',
            'fin_regno' => 'Реєстраційний номер',
            'fin_vid' => 'Вид юр. діяльності',

            'fin_price_aboniment' => 'Ціна абонемента',
            'fin_currency' => 'Валюта',

            'passport' => 'Паспорт',
            'iin' => 'Ідентифікаційний номер',
            'subscibe_email' => 'Email для розсилки',
            'language' => 'Мова',
            'enabled' => 'Вкл.',
            'total_group'=>'Груп',
            'total_people'=>'Учнів',
            'total_teacher' => 'Вчителів',
        ],
        'pages' => ['total'=>'Усього менеджерів'],
        ],
    'franchisee' => [
        'title' => 'Список Франчайзі',
        'title2' => 'Франчайзі',
        'titleCalendar' => 'Календар Франчайзі',
        'password_help' => 'Пароль 7 символів,1 велика буква и цифри',
        'title_general'=>'Загальна інформація',
        'title_fin_info'=>'Фінансова Інформація',
        'title_addininal_info'=>'Додаткова Інформація',
        'actions' => [
            'index' => 'Франчайзі',
            'create' => 'Додати Франчайзі',
            'create_new' => 'Додати нового франчайзі',
            'edit' => 'Редагувати :name',
            'change_email' => 'Змінити email',
            'change_password' => 'Змінити пароль',
            'btn_create'=>'Додати Франчайзі',
            'btn_unblock'=>'Додати Франчайзі',
            'btn_block'=>'Заблокувати'
        ],

        
        'pages' => ['total'=>'Усього Франчайзі'],
        'columns' => [
            'id' => 'ID',
            'surname' => 'Прізвище',
            'first_name' => 'Ім`я',
            'patronymic' => 'По батькові',
            'country_id' => 'Країна',
            'region_id' => 'Регіон',
            'city_id' => 'Місто',
            'locality' => 'Населений пункт',

            'phone' => 'Телефон',
            'email' => 'Email',
            'password' => 'Пароль',

            'fin_royalty' => 'Роялті',
            'fin_pr' => 'PR відрахування',
            'fin_legal' => 'Номер контракта',
            'fin_address' => 'Юридична адресса',
            'fin_regno' => 'Реєстраційний номер',
            'fin_vid' => 'Вид юр. діяльності',

            'fin_price_aboniment' => 'Ціна абонемента',
            'fin_currency' => 'Валюта',

            'passport' => 'Паспорт',
            'iin' => 'Ідентифікаційний номер',
            'subscibe_email' => 'Email для розсилки',
            'language' => 'Мова',
            'enabled' => 'Вкл.',
            'total_group'=>'Груп',
            'total_people'=>'Учнів',
            'total_teacher' => 'Вчителів',
        ],
    ],

    'shop' => [
        'title' => 'магазин',
        'title2' => 'магазин',
 

        'filtr_all' => 'Усі',
        'filtr_aktive' => 'Активні',
        'filtr_inaktive' => 'Неопубліковані',
        'filtr_deleted' =>  'Удалены',
        'productdata' => 'Дані продукту',
        'loadimg' => 'Додати зображення продукту',
        'removeimg' => 'Видалити зображення продукту',
        'deliverytype1' => 'Немає',

        'actions' => [
            'index' => 'Магазин',
            'create' => 'Створити продукт',
            'create_new' => 'Add new продукт',
            'edit' => 'Редагувати продукт :name',
            'btn_create'=>'Зберегти',
            'btn_unblock'=>'Add продукт',
            'btn_block'=>'Block'
 
        ],
        'pages' => ['total'=>'Кількість'],
        'columns' => [
            'name' => 'Назва продукту',
            'status' => 'Статус',
            'price' => 'Ціна (SD)',
            'sold' => 'Загальна кількість куплених',
            'description' => 'Опис продукту',
            'productdata' => 'Дані продукту',
            'count' => 'Кількість',
            'weight' => 'Вага (кг)',
            'size' => 'Розміри (см)',
            'size_width' => 'шир',
            'size_length' => 'длин',
            'size_height' => 'выс',
            'sizeselect' => 'Вибрати',
            'deliveryclass' => 'Клас доставки',
            'aktive' => 'Активні',
            'inaktive' => 'Неопубліковані',
            'deleted' => 'Удален',
            'delivery' => 'Доставка',
            'deliveryprice' => 'Ціна доставки',
        ],
    ],
    'order' => [
            'title' => 'Замовлення',
            'title2' => 'Замовлення',
            'filtr_all' => 'Усі',
            'filtr_1' => 'Виконані',
            'filtr_2' => 'Відправлено INPOST',
            'filtr_3' =>  'Відправлено DHL',
             
            'loadimg' => 'Додати зображення продукту',
            'removeimg' => 'Видалити зображення продукту',
            'deliverytype1' => 'Немає',

            'actions' => [
                'index' => 'Замовлення',
                'create' => 'Створити замовлення',
                'create_new' => 'Add new продукт',
                'edit' => 'Edit :name',
                'btn_create'=>'Опублікувати',
                'btn_unblock'=>'Add продукт',
                'btn_block'=>'Block'
    
            ],
            'pages' => ['total'=>'Кількість замовлень'],
            'columns' => [
                'id' => 'ID',
                'created_at' => 'Дата',
                'name' => 'Продукт',
                'status' => 'Статус',
                'price' => 'Сума',
                'child_name' => 'Учень',
                'delivery_method' => 'Доставка',
                'aktive' => 'Активні',
                'inaktive' => 'Неопубліковані',
                'kwota' => 'Kwota',
            ],
            'statuses' => [
                'new' => 'Новый',
                'send_DHL' => 'Відправлено DHL',
                'send_INPOST' => 'Відправлено INPOST',
                'failed' => 'Не вдалося',
                'completed' => 'Виконані',
                'waiting_for_payment' => 'Очікують на оплату',
            ],
    ],
    'calendars' =>[
       'franchisee'=> ['calendar_franchisee' => 'Календар франчайзі', 'total_teacher' => 'Усього вчителів'],
      'groups'=> ['calendar_group' => 'Календар групи', 'total_teacher' => 'Усього вчителів'],
      'teachers'=> ['calendar_teachers' => 'Календар вчителя', 'total_teacher' => 'Усього вчителів','back'=>'Назад'],
      'students'=> ['student' => 'Календар студента','student_name'=>'студент']
    ],//end calendars

    'menu' => [
        'franchisee' => ['title' => 'Список франчайзі'],
        'managers' => ['title' => 'Список менеджерів','title2'=>'Менеджер'],
        'groups' => ['title' => 'Список груп'],
        'teacher' => ['title' => 'Список вчителів'],
        'student' => ['title' => 'Список учнів'],
        'training' => ['title' => 'Тренування','present'=>'Пробний урок','olympiad'=>'Олімпіада'],
        'student-payment' => ['title' => 'Платіжні дані'],
        'restore' => ['title' => 'Відновлення'],
        'calendar' => ['title' => 'Календар'],
        'homework' => ['title' => 'Домашне завдання'],

        'video_material' => ['title' => 'Відеоматеріали'],
        'shop' => ['title' => 'Магазин'],
        'admin_main' => ['title' => 'Адмінка профі'],
        'admin_home' => ['title' => 'Адмінка франчизи'],
        'cabinet' => ['title' => 'Кабінет'],
        'training-images-task' => ['title' => 'Запам\'ятовування картинок'],
        'training-words-task' => ['title' => 'Запам\'ятовування слів'],
        'finance' => ['title' => 'Фінанси'],
        'faq' => ['title' => 'Часті питання'],
        'feedback' => ['title' => 'Зв\'яжіться з нами'],
        'training-contract' => ['title' => 'Договір навчання'],
        'about' => ['title' => 'Про нас'],
        'privacy-policy' => ['title' => 'Політика конфіденційності'],
        'term-use' => ['title' => 'Умови користування'],
        'order' => ['title' => 'Замовлення'],
    ],

    'maths'=>[
        'category'=>['random' => 'Випадкові', 'addition' => 'Додавання', 'subtraction' => 'Віднімання', 'multiplication' => 'Множення', 'division' => 'Ділення', 'fractions' => 'Дроби', 'percentage' => 'Відсотки'],
        'random'=>'Випадкові',
        'all'=>'Усі',
        'action_fraction'=>['all'=>'Усі','plus'=>'Додати','minus'=>'Відняти'],
        'action_div'=>['all'=>'Усі','plus'=>'Збільшити на %','minus'=>'Зменьшити на %','procent'=>'% від числа'],
    ],
    'validation'=> [
        'accepted'             => 'Ви повинні прийняти :attribute.',
        'active_url'           => 'Поле :attribute не є правильним URL.',
        'after'                => 'Поле :attribute має містити дату не раніше :date.',
        'after_or_equal'       => 'Поле :attribute має містити дату не раніше, або дорівнюватися :date.',
        'alpha'                => 'Поле :attribute має містити лише літери.',
        'alpha_dash'           => 'Поле :attribute має містити лише літери, цифри, тире та підкреслення.',
        'alpha_num'            => 'Поле :attribute має містити лише літери та цифри.',
        'array'                => 'Поле :attribute має бути масивом.',
        'attached'             => 'Цей :attribute вже прикріплений.',
        'before'               => 'Поле :attribute має містити дату не пізніше :date.',
        'before_or_equal'      => 'Поле :attribute має містити дату не пізніше, або дорівнюватися :date.',
        'between'              => [
            'array'   => 'Поле :attribute має містити від :min до :max елементів.',
            'file'    => 'Розмір файлу у полі :attribute має бути не менше :min та не більше :max кілобайт.',
            'numeric' => 'Поле :attribute має бути між :min та :max.',
            'string'  => 'Текст у полі :attribute має бути не менше :min та не більше :max символів.',
        ],
        'boolean'              => 'Поле :attribute повинне містити логічний тип.',
        'confirmed'            => 'Поле :attribute не збігається з підтвердженням.',
        'date'                 => 'Поле :attribute не є датою.',
        'date_equals'          => 'Поле :attribute має бути датою рівною :date.',
        'date_format'          => 'Поле :attribute не відповідає формату :format.',
        'different'            => 'Поля :attribute та :other повинні бути різними.',
        'digits'               => 'Довжина цифрового поля :attribute повинна дорівнювати :digits.',
        'digits_between'       => 'Довжина цифрового поля :attribute повинна бути від :min до :max.',
        'dimensions'           => 'Поле :attribute містить неприпустимі розміри зображення.',
        'distinct'             => 'Поле :attribute містить значення, яке дублюється.',
        'email'                => 'Поле :attribute повинне містити коректну електронну адресу.',
        'ends_with'            => 'Поле :attribute має закінчуватися одним з наступних значень: :values',
        'exists'               => 'Вибране для :attribute значення не коректне.',
        'file'                 => 'Поле :attribute має містити файл.',
        'filled'               => 'Поле :attribute є обов\'язковим для заповнення.',
        'gt'                   => [
            'array'   => 'Поле :attribute має містити більше ніж :value елементів.',
            'file'    => 'Поле :attribute має бути більше ніж :value кілобайт.',
            'numeric' => 'Поле :attribute має бути більше ніж :value.',
            'string'  => 'Поле :attribute має бути більше ніж :value символів.',
        ],
        'gte'                  => [
            'array'   => 'Поле :attribute має містити :value чи більше елементів.',
            'file'    => 'Поле :attribute має дорівнювати чи бути більше ніж :value кілобайт.',
            'numeric' => 'Поле :attribute має дорівнювати чи бути більше ніж :value.',
            'string'  => 'Поле :attribute має дорівнювати чи бути більше ніж :value символів.',
        ],
        'image'                => 'Поле :attribute має містити зображення.',
        'in'                   => 'Вибране для :attribute значення не коректне.',
        'in_array'             => 'Значення поля :attribute не міститься в :other.',
        'integer'              => 'Поле :attribute має містити ціле число.',
        'ip'                   => 'Поле :attribute має містити IP адресу.',
        'ipv4'                 => 'Поле :attribute має містити IPv4 адресу.',
        'ipv6'                 => 'Поле :attribute має містити IPv6 адресу.',
        'json'                 => 'Дані поля :attribute мають бути у форматі JSON.',
        'lt'                   => [
            'array'   => 'Поле :attribute має містити менше ніж :value items.',
            'file'    => 'Поле :attribute має бути менше ніж :value кілобайт.',
            'numeric' => 'Поле :attribute має бути менше ніж :value.',
            'string'  => 'Поле :attribute має бути менше ніж :value символів.',
        ],
        'lte'                  => [
            'array'   => 'Поле :attribute має містити не більше ніж :value елементів.',
            'file'    => 'Поле :attribute має дорівнювати чи бути менше ніж :value кілобайт.',
            'numeric' => 'Поле :attribute має дорівнювати чи бути менше ніж :value.',
            'string'  => 'Поле :attribute має дорівнювати чи бути менше ніж :value символів.',
        ],
        'max'                  => [
            'array'   => 'Поле :attribute повинне містити не більше :max елементів.',
            'file'    => 'Файл в полі :attribute має бути не більше :max кілобайт.',
            'numeric' => 'Поле :attribute має бути не більше :max.',
            'string'  => 'Текст в полі :attribute повинен мати довжину не більшу за :max.',
        ],
        'mimes'                => 'Поле :attribute повинне містити файл одного з типів: :values.',
        'mimetypes'            => 'Поле :attribute повинне містити файл одного з типів: :values.',
        'min'                  => [
            'array'   => 'Поле :attribute повинне містити не менше :min елементів.',
            'file'    => 'Розмір файлу у полі :attribute має бути не меншим :min кілобайт.',
            'numeric' => 'Поле :attribute повинне бути не менше :min.',
            'string'  => 'Текст у полі :attribute повинен містити не менше :min символів.',
        ],
        'multiple_of'          => 'Поле :attribute повинно містити декілька :value',
        'not_in'               => 'Вибране для :attribute значення не коректне.',
        'not_regex'            => 'Формат поля :attribute не вірний.',
        'numeric'              => 'Поле :attribute повинно містити число.',
        'password'             => 'Неправильний пароль.',
        'present'              => 'Поле :attribute повинне бути присутнє.',
        'prohibited'           => 'Поле :attribute заборонено.',
        'prohibited_if'        => 'Поле :attribute заборонено, коли :other дорівнює :value.',
        'prohibited_unless'    => 'Поле :attribute заборонено, якщо тільки :other не знаходиться в :values.',
        'regex'                => 'Поле :attribute має хибний формат.',
        'relatable'            => 'Цей :attribute може бути не пов\'язаний з цим ресурсом.',
        'required'             => 'Поле :attribute є обов\'язковим для заповнення.',
        'required_if'          => 'Поле :attribute є обов\'язковим для заповнення, коли :other є рівним :value.',
        'required_unless'      => 'Поле :attribute є обов\'язковим для заповнення, коли :other відрізняється від :values',
        'required_with'        => 'Поле :attribute є обов\'язковим для заповнення, коли :values вказано.',
        'required_with_all'    => 'Поле :attribute є обов\'язковим для заповнення, коли :values вказано.',
        'required_without'     => 'Поле :attribute є обов\'язковим для заповнення, коли :values не вказано.',
        'required_without_all' => 'Поле :attribute є обов\'язковим для заповнення, коли :values не вказано.',
        'same'                 => 'Поля :attribute та :other мають збігатися.',
        'size'                 => [
            'array'   => 'Поле :attribute повинне містити :size елементів.',
            'file'    => 'Файл у полі :attribute має бути розміром :size кілобайт.',
            'numeric' => 'Поле :attribute має бути довжини :size.',
            'string'  => 'Текст у полі :attribute повинен містити :size символів.',
        ],
        'starts_with'          => 'Поле :attribute повинне починатися з одного з наступних значень: :values',
        'string'               => 'Поле :attribute повинне містити текст.',
        'timezone'             => 'Поле :attribute повинне містити коректну часову зону.',
        'unique'               => 'Вказане значення поля :attribute вже існує.',
        'uploaded'             => 'Завантаження :attribute не вдалося.',
        'url'                  => 'Формат поля :attribute хибний.',
        'uuid'                 => 'Поле :attribute має бути коректним UUID ідентифікатором.',
        'custom'               => [
            'attribute-name' => [
                'rule-name' => 'custom-message',

            ],
        ],
        'attributes'           => [
            'address'               => 'Адреса',
            'age'                   => 'Вік',
            'available'             => 'Доступно',
            'city'                  => 'Місто',
            'content'               => 'Контент',
            'country'               => 'Країна',
            'date'                  => 'Дата',
            'day'                   => 'День',
            'description'           => 'Опис',
            'email'                 => 'E-mail адреса',
            'excerpt'               => 'Уривок',
            'first_name'            => 'Ім\'я',
            'gender'                => 'Стать',
            'hour'                  => 'Година',
            'last_name'             => 'Прізвище',
            'minute'                => 'Хвилина',
            'mobile'                => 'Моб. номер',
            'month'                 => 'Місяць',
            'name'                  => 'Ім\'я',
            'password'              => 'Пароль',
            'password_confirmation' => 'Повторіть пароль',
            'phone'                 => 'Телефон',
            'second'                => 'Секунда',
            'sex'                   => 'Стать',
            'size'                  => 'Розмір',
            'time'                  => 'Час',
            'title'                 => 'Назва',
            'username'              => 'Нікнейм',
            'year'                  => 'Рік',
        ],
    ]
    // Do not delete me :) I'm used for auto-generation
];

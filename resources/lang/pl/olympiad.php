<?php
return [
    'menu'=>[
        'title'=>'Olimpiady',
        'index'=>'Aktywne Olimpiady',
        'practicians'=>'Lista uczestników',
        'finish'=>'Zakończone olimpiady',
        'ratting'=>'Ranking olimpiad',
        'ratting_result'=>'Wyniki olimpiady',
        'subscribe'=>'Lista uczestników',
        'ratting_details'=>'Wyniki olimpiady :name',
        'payments'=>'Płatności olimpiad',
        'create'=>'Stwórz olimpiadę SpaceM',


    ],
    'actions'=>[
        'index'=>'Olimpiady',
        'exit'=>'Wyjście',
        'confirm_exit'=>'Czy na pewno chcesz opuścić olimpiadę?',
        'confirm_exit_text'=>'Czy na pewno chcesz opuścić?',
        'open_result'=>'Po potwierdzeniu wszystkie wyniki będą dostępne dla wszystkich. Otwarte?',
        'close_result'=>'Po zamknięciu cytaty będą niewidoczne dla wszystkich. Ukrywać?',

    ],
    'errors'=>[
        'date_activate'=>'Data aktywacji musi przypadać dziś lub później, a data zakończenia nie może być wcześniejsza niż data rozpoczęcia.',
        'announcement_period'=>'Data rozpoczęcia ogłoszenia musi przypadać wcześniej niż data rozpoczęcia aktywacji.',
        'announcement_and_start'=>'Data rozpoczęcia ogłoszenia musi przypadać wcześniej niż data rozpoczęcia aktywacji.',
        'need_params'=>'Dodaj poziom trudności, aby kontynuować',
    ],
    'btn'=>[
        'title'=>'Olimpiada',
        'prev'=>'Wstecz',
        'next'=>'Dalej',
        'apply'=>'Zastosuj',
        'subscribe'=>'Weź udział',
        'preview'=>'Podgląd',
        'finish'=>'Zakończ',
        'edit'=>'Edytuj',
        'edit_list'=>'Weź udział',
        'save'=>'Zapisz',
        'add_params'=>'Dodaj zadanie',
        'all_countries'=>'Wszystkie kraje',
        'create_olympiad'=>'Utwórz olimpiadę',
        'create_olympiad_space'=>'Olimpiada SpaceM',
        'create_olympiad_ads'=>'Ogłoszenie o olimpiadzie',
        'add_discipline'=>'Dodaj zadanie',
        'remove_result'=>'Usuń wyniki olimpiady',
        'remove_result_practica'=>'Usuń wyniki olimpiady',
        'open_result'=>'Otwórz wyniki Olimpiady',
        'close_result'=>'Zamknij wyniki Olimpiady',
    ],
    'lang'=>[
        'ua'=>'Ukraiński',
        'en'=>'Angielski',
        'pl'=>'Polski',
        'ru'=>'Rosyjski',
        'es'=>'Hiszpański',
        'fr'=>'Francuski',
        'de'=>'Niemiecki',
        'it'=>'Włoski',
        'pt'=>'Portugalski'
    ],
    'pages'=>[
        'total'=>'Razem',
    ],

    'step1'=>[
        'title'=>'1 etap',
        'international'=>'Międzynarodowa, dla wszystkich krajów',
        'region'=>'Województwo',
        'add_city'=>'Dodaj miasto',
        'hide_add_city'=>'Ukryj dodawanie miasta',
        'name'=> ['uk'=>'Nazwa','pl'=>'Tytuł','en'=>'Title'],
        'describe'=> ['uk'=>'Krótki opis','pl'=>'Krótki opis','en'=>'Short Description'],
        'cover'=> ['uk'=>'Okładka olimpiady','pl'=>'Okładka olimpiady','en'=>'Cover of the olympiad'],
        'cover_info'=> ['uk'=>'*Rozdzielczość obrazka 800x500','pl'=>'*Rozdzielczość obrazka 800x500','en'=>'*Image resolution 800x500'],
        'show_result'=>'Wyświetlanie wyników',
        'show_result_yes'=>'Automatycznie wyświetla następny dzień',
        'show_result_no'=>'ВWyświetlanie wyników na przycisku na liście uczestników',
    ],
    'step2'=>[
        'title'=>'2 etap',
        'full_describe'=> ['uk'=>'Szczegółowy opis olimpiady','pl'=>'Szczegółowy opis olimpiady','en'=>'Detailed description of the Olympiad'],
    ],
    'step3'=>[
        'title'=>'3 etap',
         'price'=>'Cena',
        'international_currency'=>'Waluta międzynarodowa',
        'local_currency'=>'Waluta Ukraińska',
        'announcement_period'=>'Data ogłoszenia Olimpiady (Przedział od-do)', //Дата анонса олимпиады (Інтервал от-до)
        'activation_period'=>'Data aktywacji olimpiady (odstęp od-do)', //Дата активации олимпиады (Інтервал от-до

    ],
    'step4'=>[
        'title'=>'4 etap',

    ],
    'step5'=>[
        'title'=>'5 etap',
    ],

    'main'=>[
        'promotion'=>[
            'ads'=>'Ogłoszenie',
            'olympiad'=>'Olimpiady',
            'locale'=>'lokalna',
            'international'=>'międzynarodowa',
        ],

        'title'=>'Olimpiady',
        'active'=>['title'=>'Aktywne Olimpiady'],
        'finish'=>['title'=>'Zakończone'],

        'description'=>'Udział w olimpiadach',
        'start'=>'Rozpocznij olimpiadę',
        'params'=>'Parametry olimpiady',
        'tasks'=>'Zadania olimpiady',
        'results'=>'Wyniki olimpiady',
        'rating'=>'Ranking olimpiady',
        'payments'=>'Płatności olimpiady',
    ],
    'practicians'=>[
        'action'=>[
            'print-list'=>'Drukuj listę uczestników',
            'print-ratting'=>'Drukuj ranking uczestników',
            'bnt-result'=>'Wyniki'
        ],
        'title'=>'Praktykanci',
        'total'=>'Razem',
        'list'=>[
            'title'=>'Lista uczestników olimpiady',
            'title_tab'=>'Lista uczestników',
            'result_tab'=>'Wyniki olimpiady'
        ],
        'columns'=>[
            'place' => 'Miejsce',
            'full_name' => 'Imię i Nazwisko',
            'age' => 'Wiek',
            'teacher' => 'Nauczyciel',
            'country' => 'Kraj',
            'category' => 'Kategoria',
            'total_score' => 'Łączny wynik',
            'good_answear' => 'Odpowiedzi',
            'proc_answear' => '% poprawnych',
            'id' => 'ID',
            'name' => 'Imię',
            'surname' => 'Nazwisko',
            'lastname' => 'Drugie imię',
            'email' => 'E-mail',
            'phone' => 'Telefon',
            'school' => 'Szkoła',
            'is_pay' => 'Opłacone',
            'language' => 'Język olimpiady',
            'subscribe_date' => 'Data',
            'last_login_at' => 'Ostatnie logowanie'
        ]
    ],
    'result'=>[
        'title'=>'Wyniki olimpiady',
        'title_for'=>'Próbki testowe',
        'not_have_result'=>'Nie znaleziono wyników.',

    ],
    'ratting'=>[
        'title'=>'Ranking',
        'filter'=>['category'=>'Filtr według kategorii','age'=>'Według wieku'],
        'columns'=>[
            'data' => 'Data',
            'name' => 'Nazwa Olimpiady',
            'country' => 'Kraj',
            'practicans_total' => 'Uczestnicy',
            'creator' => 'Twórca',
        ]
    ],
    'payments'=>[
        'title'=>'Płatności',
        'columns'=>[
            'olympiad_title'=> 'Tytuł Olimpiady',
            'id' => 'ID',
            'payment_date' => 'Data płatności',
            'full_name' => 'Imię i Nazwisko',
            'participant_id' => 'ID uczestnika',
            'amount' => 'Kwota',
            'status' => 'Status',
        ]
    ],
];

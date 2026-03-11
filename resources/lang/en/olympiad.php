<?php
return [
    'menu'=>[
        'title'=>'Olympiads',
  'index'=>'Active Olympiads',
        'practicians'=>'Interns',
        'finish'=>'Finished Olympiads',
        'ratting'=>'Olympiad Rating',
        'ratting_result'=>'Olympiad Results',
        'subscribe'=>'List of Olympiad Participants',
        'ratting_details'=>'Olympiad Results :name',
        'payments'=>'Olympiad Payments',
        'create'=>'Create Olympiad Space',

    ],
    'actions'=>[
        'index'=>'Olympiads',
        'exit'=>'Exit',
        'confirm_exit'=>'Leave the Olympiad?',
        'confirm_exit_text'=>'If you leave now, your score in this section will be 0 points, and you`ll only be able to attempt the next section. Are you sure you want to exit?',
        'open_result'=>'After confirmation, all results will be available for all. Open?',
        'close_result'=>'After confirmation, all results will be hidden for all. Hide?',

    ],
    'errors'=>[
        'date_activate'=>'Activation date must be today or later, and the end date not earlier than the start date.',
        'announcement_period'=>'Announcement start date must be earlier than activation start date.',
        'announcement_and_start'=>'Announcement start date must be earlier than activation start date.',
        'need_params'=>'Add a difficulty level to continue',
    ],

    'btn'=>[
        'title'=>'Olympiad',
         'prev'=>'Previous',
         'next'=>'Next',
         'apply'=>'Apply',
        'subscribe'=>'Participate',

        'preview'=>'Preview',
        'finish'=>'Finish',
        'edit'=>'Edit',
        'edit_list'=>'Participate',
        'save'=>'Save',
        'add_params'=>'Add Discipline',
        'all_countries'=>'All Countries',
        'create_olympiad'=>'Create Olympiad',
        'create_olympiad_space'=>'Olympiad SpaceM',
        'create_olympiad_ads'=>'Olympiad Announcement',
        'add_discipline'=>'Add Discipline',
        'remove_result'=>'Delete Results',
        'remove_result_practica'=>'Delete Test Results',
        'open_result'=>'Open the results of the Olympiad',
        'close_result'=>'Close the results',
    ],
    'lang'=>[
        'ua'=>'Ukrainian',
        'en'=>'English',
        'pl'=>'Polish',
        'ru'=>'Russian',
        'es'=>'Spanish',
        'fr'=>'French',
        'de'=>'German',
        'it'=>'Italian',
        'pt'=>'Portuguese'
    ],
    'pages'=>[
        'total'=>'Total',
    ],

    'step1'=>[
        'title'=>'Stage 1',
        'international'=>'International, for all countries',
        'region'=>'Region',
        'add_city'=>'Add City',
        'hide_add_city'=>'Hide City Addition',
        'name'=> ['uk'=>'Name','pl'=>'Title','en'=>'Title'],
        'describe'=> ['uk'=>'Short Description','pl'=>'Short Description','en'=>'Short Description'],

        'cover'=> ['uk'=>'Olympiad Cover','pl'=>'Olympiad Cover','en'=>'Cover of the Olympiad'],
        'cover_info'=> ['uk'=>'*Resolution 800x500','pl'=>'*Image resolution 800x500','en'=>'*Image resolution 800x500'],
        'show_result'=>'Displaying results',
        'show_result_yes'=>'Automatically displays the next day',
        'show_result_no'=>'Displaying results on a button in the list of participants',
    ],
    'step2'=>[
        'title'=>'Stage 2',
        'full_describe'=> ['uk'=>'Detailed Description of the Olympiad','pl'=>'Detailed Description of the Olympiad','en'=>'Detailed description of the Olympiad'],
    ],
    'step3'=>[
        'title'=>'Stage 3',
        'price'=>'Price',
        'international_currency'=>'International Currency',
        'local_currency'=>'Currency in Ukraine',
        'announcement_period'=>'Announcement Date (Interval from-to)',
        'activation_period'=>'Activation Date (Interval from-to)',
    ],
    'step4'=>[
        'title'=>'Stage 4',
    ],
    'step5'=>[
        'title'=>'Stage 5',
    ],

    'main'=>[
        'promotion'=>[
            'ads'=>'Announcement',
            'olympiad'=>'Olympiad',
            'locale'=>'local',
            'international'=>'international',
        ],
        'title'=>'Olympiads',
        'active'=>['title'=>'Active Olympiads'],
        'finish'=>['title'=>'Finished'],

        'description'=>'Participation in Olympiads',
        'start'=>'Start Olympiad',
        'params'=>'Olympiad Parameters',
        'tasks'=>'Olympiad Tasks',
        'results'=>'Olympiad Results',
        'rating'=>'Olympiad Rating',
        'payments'=>'Olympiad Payments',
    ],
    'practicians'=>[
        'action'=>[
            'print-list'=>'Print Participant List',
            'print-ratting'=>'Print Participant Rating',
            'bnt-result'=>'Results'
        ],
        'title'=>'Interns',
        'total'=>'Total',
        'list'=>[
            'title'=>'Olympiad Participant List',
            'title_tab'=>'Participant List',
            'result_tab'=>'Olympiad Results'

        ],
        'columns'=>[
            'place' => 'Place',
            'full_name' => 'Full Name',
            'age' => 'Age',
            'teacher' => 'Teacher',
            'country' => 'Country',
            'category' => 'Category',

            'total_score' => 'Total Score',
            'good_answear' => 'Answers',
            'proc_answear' => '% Correct',
            'id' => 'ID',
            'name' => 'Name',
            'surname' => 'Surname',
            'lastname' => 'Patronymic',
            'email' => 'Email',
            'phone' => 'Phone',
            'school' => 'School',
            'is_pay' => 'Paid',
            'language' => 'Olympiad Language',
            'subscribe_date' => 'Date',
            'last_login_at' => 'Last Login'
        ]

    ],
    'result'=>[
        'title'=>'Olympiad Results',
        'title_for'=>'Test Trials',
        'not_have_result'=>'No Results',
    ],
    'ratting'=>[
        'title'=>'Rating',
        'filter'=>['category'=>'Filter by Category','age'=>'Filter by Age'],
        'columns'=>[
            'data' => 'Date',
            'name' => 'Olympiad Name',
            'country' => 'Country',
            'practicans_total' => 'Participants',
            'creator' => 'Creator',

        ]
    ],
    'payments'=>[
        'title'=>'Payments',
        'columns'=>[
 'olympiad_title'=> 'Olympic Title',
            'id' => 'ID',
            'payment_date' => 'Payment Date',
            'full_name' => 'Full Name',
            'participant_id' => 'Participant ID',
            'amount' => 'Amount',
            'status' => 'Status',

        ]
    ],

];

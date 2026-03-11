<?php

return [
    'exception_message' => 'Komunikat o błędzie: :message',
    'exception_trace' => 'Informacje o błędzie: :trace',
    'exception_message_title' => 'Komunikat o błędzie',
    'exception_trace_title' => 'Informacje o błędzie',

    'backup_failed_subject' => 'Tworzenie kopii zapasowej nie powiodło się :application_name',
    'backup_failed_body' => 'Uwaga: Wystąpił błąd podczas tworzenia kopii zapasowej:application_name',

    'backup_successful_subject' => 'Pomyślnie utworzono nową kopię zapasową :application_name',
    'backup_successful_subject_title' => 'Pomyślnie utworzono nową kopię zapasową!',
    'backup_successful_body' => 'Wspaniała wiadomość, nowa kopia zapasowa :application_name została pomyślnie utworzona i zapisana na dysku :disk_name.',

    'cleanup_failed_subject' => 'Nie udało się wyczyścić kopii zapasowych :application_name',
    'cleanup_failed_body' => 'Wystąpił błąd podczas czyszczenia kopii zapasowych :application_name',

    'cleanup_successful_subject' => 'Czyszczenie z kopii zapasowych :application_name powiodło się',
    'cleanup_successful_subject_title' => 'Czyszczenie kopii zapasowych powiodło się!',
    'cleanup_successful_body' => 'Czyszczenie ze starych kopii zapasowych :application_name na dysku :disk_name powiodło się.',

    'healthy_backup_found_subject' => 'Kopia zapasowa :application_name z dysku :disk_name została zainstalowana',
    'healthy_backup_found_subject_title' => 'Kopia zapasowa :application_name została zainstalowana',
    'healthy_backup_found_body' => 'Kopia zapasowa :application_name została pomyślnie zainstalowana. Dobra robota!',

    'unhealthy_backup_found_subject' => 'Uwaga: kopia zapasowa :application_name nie została zainstalowana',
    'unhealthy_backup_found_subject_title' => 'Uwaga: kopia zapasowa dla :application_name nie została zainstalowana. :problem',
    'unhealthy_backup_found_body' => 'Kopia zapasowa dla:application_name na dysku :disk_name nie została zainstalowana.',
    'unhealthy_backup_found_not_reachable' => 'Nie można zainstalować kopii zapasowej. :error',
    'unhealthy_backup_found_empty' => 'Brak kopii zapasowych dla tej aplikacji.',
    'unhealthy_backup_found_old' => 'Ostatnia utworzona kopia zapasowa :date jest nieaktualna',
    'unhealthy_backup_found_unknown' => 'Przepraszamy, nie można ustalić dokładnej przyczyny.',
    'unhealthy_backup_found_full' => 'Kopie zapasowe zużywają zbyt dużo pamięci. Używany :disk_usage przekracza limit: :disk_limit.',
];

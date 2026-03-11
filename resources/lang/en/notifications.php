<?php

return [
    'exception_message' => 'Error message: :message',
    'exception_trace' => 'Error informations: :trace',
    'exception_message_title' => 'Error message',
    'exception_trace_title' => 'Error informations',

    'backup_failed_subject' => 'Backup failed :application_name',
    'backup_failed_body' => 'Note: An error occurred while backing up:application_name',

    'backup_successful_subject' => 'Successfully created a new backup :application_name',
    'backup_successful_subject_title' => 'Successfully created a new backup!',
    'backup_successful_body' => 'Great news! New backup :application_name was successfully created and saved to disk :disk_name.',

    'cleanup_failed_subject' => 'Failed to clear backups :application_name',
    'cleanup_failed_body' => 'An error occurred while cleaning backups :application_name',

    'cleanup_successful_subject' => 'Backups cleaning :application_name succeed',
    'cleanup_successful_subject_title' => 'Backups cleaning succeed!',
    'cleanup_successful_body' => 'Cleaning old backups :application_name on disc :disk_name failed.',

    'healthy_backup_found_subject' => 'Backup :application_name from disc :disk_name has been installed',
    'healthy_backup_found_subject_title' => 'Backup :application_name has been installed',
    'healthy_backup_found_body' => 'Backup :application_name has been installed. Good work!',

    'unhealthy_backup_found_subject' => 'Backup :application_name has not been installed',
    'unhealthy_backup_found_subject_title' => 'Backup for :application_name has not been installed :problem',
    'unhealthy_backup_found_body' => 'Backup for :application_name on disk :disk_name has not been installed.',
    'unhealthy_backup_found_not_reachable' => 'Backup could not be installed. :error',
    'unhealthy_backup_found_empty' => 'There are no backups for this application.',
    'unhealthy_backup_found_old' => 'The last backup you created :date is out of date ',
    'unhealthy_backup_found_unknown' => 'Sorry, the exact reason cannot be determined.',
    'unhealthy_backup_found_full' => 'Backups use too much memory. The disk in use :disk_usage exceeds the limit: :disk_limit.',
];

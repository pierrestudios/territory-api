<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('db-backups', function () {
    $db = config('app.database');
    $user = config('app.username');
    $psw = config('app.password');
    $path = base_path() . '/database/backups/';
    $sql_file_name = $path . 'territory-db-backup-' . date('m-d-Y', time()) . '.sql';
    $sql_command = 'mysqldump -u ' . $user . ' -p' . $psw . ' ' . $db . ' > ' . $sql_file_name . ' --skip-add-drop-table';

    try {
        exec($sql_command, $output);
        $this->comment( implode( PHP_EOL, $output ) );

        $content = 'Database backup for '. date('m-d-Y', time());
        $subject = 'Territory Api Database Backup';
        $sent = Mail::send('translation-all/emails/notice', compact('content', 'subject'), function ($message) use ($subject, $sql_file_name) {
            $message->to(config('app.mailToEmail'), config('app.mailToName'));
            $message->from(config('app.mailFromEmail'));
            $message->subject($subject);
            $message->attach($sql_file_name);
        });
    } catch (Exception $ex) {
        Log::debug('Database backup failed: ' . $ex->getMessage());
    }

})->purpose('Perform DB Backup');
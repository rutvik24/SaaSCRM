<?php

namespace App\Console\Traits;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

trait ClearElements {

    protected function clearFiles(){
        $this->clearThumbnails();
        $this->clearUploadedFiles();
        $this->info('Files cleared');
    }

    protected function clearLogs(){
        exec('echo "" > ' . storage_path('logs/laravel.log'));

        $this->info('Logs cleared');
    }

    protected function clearUploadedFiles(){
        //this will clear unused files
        $dirs = [ 'master', 'files', 'avatar'];

        // Clear all the files in above folders
        foreach ($dirs as $dir){
            $files = Storage::disk('public')->files($dir);

            foreach ($files as $file){
                Storage::disk('public')->delete($file);
            }
        }
    }

    protected function clearThumbnails(){
        $dirs = [ 'announcementAttachments', 'files', 'avatar'];

        // Clear all the files in above folders
        foreach ($dirs as $dir){
            $files = Storage::disk('thumbnail')->files($dir);

            foreach ($files as $file){
                Storage::disk('thumbnail')->delete($file);
            }
        }
    }

    protected function clearDatabase(){
        //disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $tables = DB::select('SHOW TABLES');
        foreach($tables as $table) {
            $table_array = get_object_vars($table);
            Schema::drop($table_array[key($table_array)]);
        }

        Artisan::call('cloint:install');

        //enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->info('Database reset successful');
    }

    protected function clearConfiguration(){
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('clear-compiled');
        $this->info('Config cleared and cached again');
    }
}

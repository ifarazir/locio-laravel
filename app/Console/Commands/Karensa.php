<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Karensa extends Command
{
    protected $signature = 'make:karensa {name}';

    protected $description = 'generate files for develope';
    public function handle()
    {
        $photo = $this->confirm('Your Model Has Photo?', false);

        $possession = $this->confirm('Is Your Model Belongs To The User??', false);

        $parent = $this->confirm('Is Your Model Has Parent Relationship??', false);

        $api = $this->confirm('Is Your Response Priority API?', false);

        $blade = $this->confirm('Do You Want Generate Blade Files?', false);

        $admin = $this->confirm('Do You Want Generate Admin Controller?', false);

        $bar = $this->output->createProgressBar(8);
        $time = microtime(true);
        $bar->start();

        $this->newLine(2);
        $this->call('karensa:model', [
            'name' => $this->argument('name'),
            '--photo' => $photo,
            '--possession' => $possession,
            '--parent' => $parent,
        ]);
        $bar->advance();
        sleep(3);

        $this->newLine(2);
        $this->call('karensa:migration', [
            'name' => $this->argument('name'),
        ]);
        $bar->advance();
        sleep(3);

        if ($api) {
            $this->newLine(2);
            $this->call('karensa:resource', [
                'name' => $this->argument('name'),
            ]);
            $bar->advance();
            sleep(3);
        }

        $this->newLine(2);
        $this->call('karensa:service', [
            'name' => $this->argument('name'),
            '--photo' => $photo,
        ]);
        $bar->advance();
        sleep(3);


        $this->newLine(2);
        $this->call('karensa:response', [
            'name' => $this->argument('name'),
            '--api' => $api,
            '--admin' => $admin,
        ]);
        $bar->advance();
        sleep(3);

        $this->newLine(2);
        $this->call('karensa:protection', [
            'name' => $this->argument('name'),
            '--possession' => $possession,
        ]);
        $bar->advance();
        sleep(3);

        $this->newLine(2);
        $this->call('karensa:controller', [
            'name' => $this->argument('name'),
            '--possession' => $possession,
            '--api' => $api,
            '--admin' => $admin,
        ]);
        $bar->advance();
        sleep(3);


        if ($blade) {
            $this->newLine(2);
            $this->call('karensa:blade', [
                'name' => $this->argument('name'),
            ]);
            sleep(3);
        }
        $bar->advance();

        $bar->finish();
        $time = round(microtime(true) - $time, 5);
        $this->newLine();
        $this->info($time);
    }

}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Pluralizer;

class MakeCustomBladeCommand extends Command
{
    protected $signature = 'karensa:blade {name}';

    protected $description = 'generate custom blade file for develope';

    protected $files;
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    public function handle()
    {
        $this->generateBlade();
    }

    protected function generateBlade()
    {
        $path = base_path('resources/views/admin') . '/' . Pluralizer::plural($this->argument('name')) . "/index.blade.php";
        $stubPath = app_path() . '/Console/Commands/stubs/blade/index.stub';

        $class = new GenerateFile($this->files);
        $result = $class->generateFile($path, $stubPath, $this->argument('name'));

        $path = base_path('resources/views/admin') . '/' . Pluralizer::plural($this->argument('name')) . "/edit.blade.php";
        $stubPath = app_path() . '/Console/Commands/stubs/blade/edit.stub';

        $class = new GenerateFile($this->files);
        $result = $class->generateFile($path, $stubPath, $this->argument('name'));

        return ($result) ? $this->info("Blade created") : $this->error("Blade already exits");
    }
}

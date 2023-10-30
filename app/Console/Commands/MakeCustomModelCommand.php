<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Pluralizer;

class MakeCustomModelCommand extends Command
{
    protected $signature = 'karensa:model {name} {--photo} {--possession} {--parent}';

    protected $description = 'generate custom model file for develope';

    protected $files;
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    public function handle()
    {

        $this->generateModel();
    }

    protected function generateModel()
    {
        $path = base_path('App/Models') . '/' . ucwords(Pluralizer::singular($this->argument('name'))) . '.php';

        $stubPath = app_path() . '/Console/Commands/stubs/model.stub';

        $class = new GenerateFile($this->files);

        $stubVar = [
            'Possession' => $this->option('possession'),
            'Parent' => $this->option('parent'),
            'Photo' => $this->option('photo'),
        ];

        $result = $class->generateFile($path, $stubPath, $this->argument('name'), $stubVar);

        return ($result) ? $this->info("Model created") : $this->error("Model already exits");
    }
}

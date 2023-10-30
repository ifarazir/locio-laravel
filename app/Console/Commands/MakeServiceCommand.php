<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Pluralizer;

class MakeServiceCommand extends Command
{
    protected $signature = 'karensa:service {name} {--photo}';

    protected $description = 'generate custom service file for develope';

    protected $files;
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    public function handle()
    {
        $this->generateModelService();
        $this->generateService();
    }

    protected function generateModelService()
    {
        $path = base_path('App/Services') . '/' . $this->getSingularClassName($this->argument('name')) . '/' . $this->getSingularClassName($this->argument('name')) . 'Service.php';
        $stubPath = app_path() . '/Console/Commands/stubs/service.stub';

        $class = new GenerateFile($this->files);

        $stubVar = [
            'Photo' => $this->option('photo'),
        ];

        $result = $class->generateFile($path, $stubPath, $this->argument('name'), $stubVar);

        return ($result) ? $this->info("Model Service created") : $this->error("Model Service already exits");
    }

    protected function generateService()
    {
        $path = base_path('App/Services') . '/' . 'ModelService.php';
        $stubPath = app_path() . '/Console/Commands/stubs/modelService.stub';

        $class = new GenerateFile($this->files);
        $result = $class->generateFile($path, $stubPath, $this->argument('name'));

        return ($result) ? $this->info("Model Service Basic created") : $this->error("Model Service Basic already exits");
    }
    protected function getSingularClassName($name)
    {
        return ucwords(Pluralizer::singular($name));
    }

}

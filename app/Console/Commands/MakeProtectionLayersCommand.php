<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Pluralizer;

class MakeProtectionLayersCommand extends Command
{
    protected $signature = 'karensa:protection {name} {--possession}';

    protected $description = 'generate custom protection layers file for develope';

    protected $files;
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    public function handle()
    {
        $this->ensureModelIdExists();
        if ($this->option('possession')) {
            $this->preventTamperingOtherModel();
        }
    }

    protected function ensureModelIdExists()
    {
        $path = base_path('App/ProtectionLayers') . '/Ensure' . $this->getSingularClassName($this->argument('name')) . 'IdExists.php';
        $stubPath = app_path() . '/Console/Commands/stubs/ensureModelIdExists.stub';

        $class = new GenerateFile($this->files);
        $result = $class->generateFile($path, $stubPath, $this->argument('name'));

        return ($result) ? $this->info("Ensure Model Id created") : $this->error("Ensure Model Id already exits");
    }

    protected function preventTamperingOtherModel()
    {
        $path = base_path('App/ProtectionLayers') . '/PreventTamperingOther' . $this->getSingularClassName($this->argument('name')) . '.php';
        $stubPath = app_path() . '/Console/Commands/stubs/preventTamperingOtherModel.stub';

        $class = new GenerateFile($this->files);
        $result = $class->generateFile($path, $stubPath, $this->argument('name'));

        return ($result) ? $this->info("Prevent Tampering Other Model created") : $this->error("Prevent Tampering Other Model already exits");
    }

    protected function getSingularClassName($name)
    {
        return ucwords(Pluralizer::singular($name));
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Pluralizer;

class MakeResourceCommand extends Command
{
    protected $signature = 'karensa:resource {name}';

    protected $description = 'generate custom resource file for develope';

    protected $files;
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    public function handle()
    {
        $this->generateResource();
    }

    protected function generateResource()
    {
        $namespace = "App\Models\\" . $this->argument('name');
        $model = new $namespace;
        $fillables = array_merge($model->getFillable(), $model->getAppends());
        $fillables = array_diff($fillables, $model->getHidden());
        $datas = $this->generateData($fillables);

        $path = base_path('App/Http/Resources') . '/' . $this->getSingularClassName($this->argument('name')) . '/' . $this->getSingularClassName($this->argument('name')) . 'WithoutRelationResource.php';
        $stubPath = app_path() . '/Console/Commands/stubs/withoutRelationResource.stub';

        $class = new GenerateFile($this->files);

        $stubVar = [
            'Data' => $datas,
        ];

        $result = $class->generateFile($path, $stubPath, $this->argument('name'), $stubVar);

        return ($result) ? $this->info("Resource created") : $this->error("Resource already exits");
    }

    protected function generateData($fillables) : string
    {
        $datas = "['id' => \$this->id,";
        foreach ($fillables as $fillable) {
            $datas = $datas . "'" . $fillable . "' => \$this->" . $fillable . ",";
        }
        return $datas . "'created_at' => \$this->created_at,]";
    }
    protected function getSingularClassName($name)
    {
        return ucwords(Pluralizer::singular($name));
    }
}

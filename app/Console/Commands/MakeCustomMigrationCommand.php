<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Pluralizer;
use Illuminate\Support\Str;

class MakeCustomMigrationCommand extends Command
{
    protected $signature = 'karensa:migration {name}';

    protected $description = 'generate custom migration file for develope';

    protected $files;
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    public function handle()
    {
        $this->generateMigration();
    }

    protected function generateMigration()
    {
        $path = base_path('database/migrations') . '/' . Str::snake(now()->format('Y_m_d_His') . "_create" . Pluralizer::plural($this->argument('name')) . "_table.php");
        $stubPath = app_path() . '/Console/Commands/stubs/migration.stub';

        $class = new GenerateFile($this->files);
        $result = $class->generateFile($path, $stubPath, $this->argument('name'));

        return ($result) ? $this->info("Migration created") : $this->error("Migration already exits");
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeUploaderCommand extends Command
{
    protected $signature = 'karensa:uploader {--api}';

    protected $description = 'generate custom uploader file for develope';

    protected $files;
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    public function handle()
    {
        $this->generateServices();
        $this->generateResponses();
        $this->generateController();
        $this->generateModel();
        $this->generateDatabase();
        $this->generateService();
    }

    protected function generateServices()
    {
        $path = base_path('App/Services') . '/Uploader/Uploader.php';
        $stubPath = app_path() . '/Console/Commands/stubs/uploader/Services/Uploader/Uploader.stub';

        $class = new GenerateFile($this->files);
        $result = $class->generateFile($path, $stubPath, 'File uploader');

        $path = base_path('App/Services') . '/Uploader/StorageManager.php';
        $stubPath = app_path() . '/Console/Commands/stubs/uploader/Services/Uploader/StorageManager.stub';
        $class = new GenerateFile($this->files);
        $result = $class->generateFile($path, $stubPath, 'File uploader');

        return ($result) ? $this->info("Uploader Service created") : $this->error("Uploader Service already exits");
    }

    protected function generateResponses()
    {
        $path = base_path('App/Http') . '/Responses/Admin/File/HtmlyResponses.php';
        $stubPath = app_path() . '/Console/Commands/stubs/uploader/Responses/File/HtmlyResponses.stub';

        $class = new GenerateFile($this->files);
        $result = $class->generateFile($path, $stubPath, 'File uploader');

        $path = base_path('App/Http') . '/Responses/Admin/File/JsonResponses.php';
        $stubPath = app_path() . '/Console/Commands/stubs/uploader/Responses/File/JsonResponses.stub';

        $class = new GenerateFile($this->files);
        $result = $class->generateFile($path, $stubPath, 'File uploader');

        ($result) ? $this->info("Responses created") : $this->error("Responses already exits");

        $stubVar = [
            'Api' => $this->option('api'),
            'Html' => ! $this->option('api'),
        ];

        $path = base_path('App/Http') . '/Responses/Admin/FileResponse.php';
        $stubPath = app_path() . '/Console/Commands/stubs/uploader/Responses/FileResponse.stub';

        $class = new GenerateFile($this->files);
        $result = $class->generateFile($path, $stubPath, 'File uploader', $stubVar);
        ($result) ? $this->info("Responses created") : $this->error("Responses already exits");
    }

    protected function generateController()
    {
        $path = base_path('App/Http') . '/Controllers/Admin/FileController.php';
        $stubPath = app_path() . '/Console/Commands/stubs/uploader/Controller/FileController.stub';

        $class = new GenerateFile($this->files);
        $result = $class->generateFile($path, $stubPath, 'File uploader');

        return ($result) ? $this->info("Uploader Controller created") : $this->error("Uploader Controller already exits");
    }
    protected function generateModel()
    {
        $path = base_path('App/Models') . '/File.php';
        $stubPath = app_path() . '/Console/Commands/stubs/uploader/Models/File.stub';

        $class = new GenerateFile($this->files);
        $result = $class->generateFile($path, $stubPath, 'File uploader');

        return ($result) ? $this->info("Uploader Models created") : $this->error("Uploader Models already exits");
    }

    protected function generateDatabase()
    {
        $this->call('karensa:migration', [
            'name' => 'File',
        ]);
    }

    protected function generateService()
    {
        $this->call('karensa:service', [
            'name' => 'File',
        ]);
    }

}

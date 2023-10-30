<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Pluralizer;

class MakeResponseCommand extends Command
{
    protected $signature = 'karensa:response {name} {--api} {--admin}';

    protected $description = 'generate custom response file for develope';

    protected $files;
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    public function handle()
    {
        $bar = $this->output->createProgressBar(5);
        $time = microtime(true);
        $bar->start();

        if ($this->option('admin')) {
            $this->generateAdminJson();
            $bar->advance();
            sleep(2);

            $this->generateAdminHtml();
            $bar->advance();
            sleep(2);
        }

        $this->generateUserJson();
        $bar->advance();
        sleep(2);

        $this->generateUserHtml();
        $bar->advance();
        sleep(2);

        $this->generateResponse();
        $bar->advance();
        sleep(2);

        $bar->finish();
        $time = round(microtime(true) - $time, 5);
        $this->newLine();
        $this->info($time);
    }

    protected function generateAdminJson()
    {
        $this->newLine(2);
        $this->info('Started Generate Admin Json Responses ...');

        $path = base_path('App/Http/Responses/Admin') . '/' . $this->getSingularClassName($this->argument('name')) . '/' . 'JsonResponses.php';
        $stubPath = app_path() . '/Console/Commands/stubs/adminJsonResponses.stub';

        $class = new GenerateFile($this->files);
        $result = $class->generateFile($path, $stubPath, $this->argument('name'));

        ($result) ? $this->info("Admin Json Responses created") : $this->error("Admin Json Responses already exits");
    }
    protected function generateAdminHtml()
    {
        $this->newLine(2);
        $this->info('Started Generate Admin Html Responses ...');

        $path = base_path('App/Http/Responses/Admin') . '/' . $this->getSingularClassName($this->argument('name')) . '/' . 'HtmlyResponses.php';
        $stubPath = app_path() . '/Console/Commands/stubs/adminHtmlyResponses.stub';

        $class = new GenerateFile($this->files);
        $result = $class->generateFile($path, $stubPath, $this->argument('name'));

        return ($result) ? $this->info("Admin Html Responses created") : $this->error("Admin Html Responses already exits");
    }

    protected function generateUserJson()
    {
        $this->newLine(2);
        $this->info('Started Generate User Json Responses ...');

        $path = base_path('App/Http/Responses/User') . '/' . $this->getSingularClassName($this->argument('name')) . '/' . 'JsonResponses.php';
        $stubPath = app_path() . '/Console/Commands/stubs/userJsonResponses.stub';

        $class = new GenerateFile($this->files);
        $result = $class->generateFile($path, $stubPath, $this->argument('name'));

        return ($result) ? $this->info("User Json Responses created") : $this->error("User Json Responses already exits");
    }
    protected function generateUserHtml()
    {
        $this->newLine(2);
        $this->info('Started Generate User Html Responses ...');

        $path = base_path('App/Http/Responses/User') . '/' . $this->getSingularClassName($this->argument('name')) . '/' . 'HtmlyResponses.php';
        $stubPath = app_path() . '/Console/Commands/stubs/userHtmlyResponses.stub';

        $class = new GenerateFile($this->files);
        $result = $class->generateFile($path, $stubPath, $this->argument('name'));

        return ($result) ? $this->info("User Html Responses created") : $this->error("User Html Responses already exits");
    }
    protected function generateResponse()
    {
        $this->newLine(2);
        $this->info('Started Generate Responses Facades ...');

        $stubVar = [
            'Api' => $this->option('api'),
            'Html' => !$this->option('api'),
        ];

        $path = base_path('App/Http/Responses/User') . '/' . $this->getSingularClassName($this->argument('name')) . 'Response.php';
        $stubPath = app_path() . '/Console/Commands/stubs/userResponse.stub';

        $class = new GenerateFile($this->files);
        $result = $class->generateFile($path, $stubPath, $this->argument('name'), $stubVar);

        ($result) ? $this->info("User Responses Facade created") : $this->error("User Responses Facade already exits");

        if ($this->option('admin')) {
            $path = base_path('App/Http/Responses/Admin') . '/' . $this->getSingularClassName($this->argument('name')) . 'Response.php';
            $stubPath = app_path() . '/Console/Commands/stubs/adminResponse.stub';

            $class = new GenerateFile($this->files);
            $result = $class->generateFile($path, $stubPath, $this->argument('name'), $stubVar);

            ($result) ? $this->info("Admin Responses Facade created") : $this->error("Admin Responses Facade already exits");
        }
    }

    protected function getSingularClassName($name)
    {
        return ucwords(Pluralizer::singular($name));
    }
}

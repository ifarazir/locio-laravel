<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Pluralizer;
use Illuminate\Support\Str;

class MakeCustomControllerCommand extends Command
{
    protected $signature = 'karensa:controller {name} {--possession} {--api} {--admin}';

    protected $description = 'generate custom controllers file for develope';

    protected $files;
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    public function handle()
    {
        if ($this->option('admin')) {
            $this->generateAdminController();
        }
        // $this->generateUserController();
    }

    protected function generateAdminController()
    {
        $namespace = "App\Models\\" . $this->argument('name');
        $model = new $namespace;

        [$validation, $uniqueInputs] = $this->generateValidation($this->argument('name'), $model->getTable());
        $fillable = "[\n'" . implode("' ,'", $model->getFillable()) . "'\n]";

        $updateValidation = $this->generateUpdateValidation($uniqueInputs, $validation, $model->getTable());

        $updateValidation = $this->generateValidationFromArray($updateValidation);
        $validation = $this->generateValidationFromArray($validation);

        $path = base_path('App/Http/Controllers/Admin') . '/' . $this->getSingularClassName($this->argument('name')) . 'Controller.php';
        $stubPath = app_path() . '/Console/Commands/stubs/adminController.stub';

        $class = new GenerateFile($this->files);

        $stubVar = [
            'Api' => $this->option('api'),
            'Html' => !$this->option('api'),
            'UniqueInput' => !empty($uniqueInputs),
            'updateValidation' => $updateValidation,
            'fillable' => $fillable,
            'validation' => $validation,
        ];

        $result = $class->generateFile($path, $stubPath, $this->argument('name'), $stubVar);

        return ($result) ? $this->info("Admin Controller created") : $this->error("Admin Controller already exits");
    }

    protected function generateUpdateValidation($uniqueInputs, $validation, $tableName)
    {
        $updateValidation = [];

        if (!empty($uniqueInputs)) {
            $updateValidation = $validation;
            foreach ($uniqueInputs as $input => $validate) {
                $index = array_search($validate, $validation[$input]);
                $updateValidation[$input][$index] = "Rule::unique('" . $tableName . "')->ignore(\$id)";
            }
        }
        return $updateValidation;
    }
    protected function generateValidationFromArray($validations)
    {
        $str = '';
        foreach ($validations as $key => $value) {
            if (str_starts_with($value[0], 'Rule::unique')) {
                $rule = array_shift($value);
                $str = $str . "'" . $key . "' => " . "[" . $rule . ", '" . implode("' ,'", $value) . "'],\n";
                continue;
            }
            $str = $str . "'" . $key . "' => " . "['" . implode("' ,'", $value) . "'],\n";
        }
        return "[\n" . $str . ']';
    }
    protected function generateValidation($name, $table)
    {
        \Artisan::call('model:show ' . $name . ' --json');
        $res = json_decode(\Artisan::output());
        $attributes = $res->attributes;
        $relations = $res->relations;

        $validation = [];
        $uniqueInputs = [];

        foreach ($attributes as $attribute) {
            if (!$attribute->fillable) {
                continue;
            }

            $validate = [];

            if ($attribute->unique) {
                array_push($validate, "unique:" . $table . "," . str_replace("'", '', $attribute->name));
                $uniqueInputs += array($attribute->name => end($validate));
            }

            $attribute->nullable ? array_push($validate, "nullable") : array_push($validate, "required");
            if ($attribute->name == 'parent_id') {
                array_push($validate, "integer");
                array_push($validate, "exists:" . $res->table . ",id");
                $validation += array($attribute->name => $validate);
                continue;
            }
            switch ($attribute->type) {
                case 'string(255)':
                    array_push($validate, "string");
                    array_push($validate, "min:3");
                    break;
                case 'text':
                    array_push($validate, "string");
                    array_push($validate, "min:3");
                    break;
                case 'integer':
                    array_push($validate, "integer");
                    break;
                case 'char':
                    array_push($validate, "numeric");
                    break;
                case 'boolean':
                    array_push($validate, "boolean");
                    break;
                case 'bigint unsigned':
                    $name = str_replace('_id', '', $attribute->name);
                    $tableName = Str::pluralStudly(class_basename($name));
                    array_push($validate, "integer");
                    array_push($validate, "exists:$tableName,id");
                    break;

                default:

                    break;
            }
            $validation += array($attribute->name => $validate);
        }

        return [$validation, $uniqueInputs];
    }
    protected function generateUserController()
    {
        $path = base_path('App/Http/Controllers/User') . '/' . $this->getSingularClassName($this->argument('name')) . 'Controller.php';
        $stubPath = app_path() . '/Console/Commands/stubs/userController.stub';

        $class = new GenerateFile($this->files);

        $stubVar = [
            'Possession' => $this->option('possession'),
        ];

        $result = $class->generateFile($path, $stubPath, $this->argument('name'), $stubVar);

        return ($result) ? $this->info("User Controller created") : $this->error("User Controller already exits");
    }

    protected function getSingularClassName($name)
    {
        return ucwords(Pluralizer::singular($name));
    }
}

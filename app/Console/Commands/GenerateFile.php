<?php

namespace App\Console\Commands;

use Illuminate\Support\Pluralizer;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class GenerateFile
{
    public $name;
    protected $files;
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }
    public function generateFile($path, $stubPath, $name, $stubVar = [])
    {
        $this->makeDirectory(dirname($path));

        $contents = $this->getSourceFile($stubPath, $name, $stubVar);

        if (! $this->files->exists($path)) {
            $this->files->put($path, $contents);
            return true;
        } else {
            return false;
        }
    }

    protected function getSourceFile($stubPath, $name, $stubVar)
    {
        return $this->getStubContents($stubPath, $this->getStubVariables($name, $stubVar));
    }

    protected function getStubVariables($name, $stubVar)
    {
        $data = [
            'name' => strtolower($name),
            'class' => $this->getSingularClassName($name),
            'table' => Str::snake(Str::pluralStudly(class_basename($name))),
        ];
        return array_merge($data, $stubVar);
    }

    protected function getStubContents($stub, $stubVariables = [])
    {
        $contents = file_get_contents($stub);

        foreach ($stubVariables as $search => $replace) {
            if (is_bool($replace)) {
                $contents = $this->handleIssetInStub($search, $replace, $contents);
            } else {
                $contents = str_replace('{{' . $search . '}}', $replace, $contents);
            }
        }
        $contents = preg_replace('/^[ \t]*[\r\n]+/m', "", $contents);
        return $contents;
    }
    protected function getSingularClassName($name)
    {
        return ucwords(Pluralizer::singular($name));
    }

    protected function makeDirectory($path)
    {
        if (! $this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0777, true, true);
        }

        return $path;
    }

    protected function handleIssetInStub($name, $status, $contents)
    {
        if ($status) {
            $contents = str_replace("{{isset${name}}}", '', $contents);
            $contents = str_replace("{{endisset${name}}}", '', $contents);

            $contents = preg_replace("/{{isnotset${name}[\s\S]+?endisnotset${name}}}/", '', $contents);
        } else {
            $contents = preg_replace("/{{isset${name}[\s\S]+?endisset${name}}}/", '', $contents);

            $contents = str_replace("{{isnotset${name}}}", '', $contents);
            $contents = str_replace("{{endisnotset${name}}}", '', $contents);
        }

        return $contents;
    }
}

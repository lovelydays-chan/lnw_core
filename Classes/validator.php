<?php

namespace Lnw\Core;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation;
use Illuminate\Validation\Factory;

class validator
{
    public $lang;
    public $group;
    public $factory;
    public $namespace;
    // Translations root directory
    public $basePath;
    public static $translator;

    public function __construct($namespace = 'lang', $lang = 'en', $group = 'validation')
    {
        $this->lang = $lang;
        $this->group = $group;
        $this->namespace = $namespace;
        $this->basePath = $this->getTranslationsRootPath();
        $this->factory = new Factory($this->loadTranslator());
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->factory, $method], $args);
    }

    public function translationsRootPath(string $path = '')
    {
        if (!empty($path)) {
            $this->basePath = $path;
            $this->reloadValidatorFactory();
        }

        return $this;
    }

    public function getTranslationsRootPath(): string
    {
        return $_SERVER['DOCUMENT_ROOT'].(!empty(env('ROOT_PATH')) ? env('ROOT_PATH') : '');
    }

    public function loadTranslator(): Translator
    {
        $loader = new FileLoader(new Filesystem(), $this->basePath.$this->namespace);
        $loader->addNamespace($this->namespace, $this->basePath.$this->namespace);
        $loader->load($this->lang, $this->group, $this->namespace);

        return static::$translator = new Translator($loader, $this->lang);
    }

    private function reloadValidatorFactory()
    {
        $this->factory = new Factory($this->loadTranslator());

        return $this;
    }
}

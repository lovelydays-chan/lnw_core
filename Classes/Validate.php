<?php


namespace Lnw\Core;

use Lnw\Core\Validator;

abstract class Validate
{
    protected $validator;
    protected $locale = 'th';
    abstract protected function rules();
    abstract protected function messages();
    abstract protected function attributes();
    public function __construct($request)
    {
        $this->validator = (new Validator())
            ->make($request, $this->rules())
            ->setAttributeNames($this->attributes());

        $this->validator->getTranslator()->setLocale($this->locale);
    }

    public function __call($method, $args)
    {
        return call_user_func_array(
            [$this->validator, $method],
            $args
        );
    }
}

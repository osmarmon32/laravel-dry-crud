<?php

namespace Reddireccion\DryCrud\Validation;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\Validation\Validator as LaravelValidator;

class Validator extends LaravelValidator;
{
    /**
     * Create a new Validator instance.
     *
     * @param  \Illuminate\Contracts\Translation\Translator  $translator
     * @param  array  $data
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $customAttributes
     * @return void
     */
    public function __construct(Translator $translator, array $data, array $rules=[],
                                array $messages = [], array $customAttributes = [])
    {
        parent::__construct($translator,$data,$rules,$messages,$customAttributes);
    }
    /**
     * validates the model according to the rules for the given action
     *
     * @param  Reddireccion\DryCrud\Models\Model $model Model instance that will be validated 
     * @param  string $actiontype create or update constants from model class
     * @return boolean
     */
    public function isValid($model, $actionType){
        //Dynamic setup of rules accoring to the model and action type
        $this->setRules($model->validationRules($actionType));
        $this->after(function($validator){
            //validate duplicates
            if($model->isDuplicated()){
                $validator->errors()->add('id', 'This record is duplicated');
            }
        });
       	return $this->validate();
    }
}
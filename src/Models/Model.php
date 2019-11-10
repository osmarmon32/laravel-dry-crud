<?php

namespace Reddireccion\DryCrud\Models;

use Illuminate\Database\Eloquent\Model as LaravelModel;
use \Request;

class Model extends LaravelModel
{
	public const TYPE_TIMESTAMP='timestamp';
	public const TYPE_DATETIME='datetime';
	public const TYPE_TINYINT='tinyint';
	public const TYPE_BIGINT='bigint';
	public const TYPE_INT='int';
	public const ACTION_CREATE='Create';
	public const ACTION_UPDATE='Update';
	public const FIELD_PROPERTY_TYPE='type';
	public const FIELD_PROPERTY_LENGTH='length';
	public const FIELD_PROPERTY_NULLABLE='nullable';
	public const FIELD_PROPERTY_COMMENTS='comments';
	
	protected static $_fieldsDefinition = [];
	/**
     * get the default validation rules according to the field data type
     *
     * @return key value array of type->validation
     */
	protected static function getDefaultRulesByType(){
		return [
			static::TYPE_TIMESTAMP=>'date',
			static::TYPE_DATETIME=>'date',
			static::TYPE_BIGINT=>'integer',
			static::TYPE_INT=>'integer',
			static::TYPE_TINYINT=>'integer',
		];
	}
	/**
     * returns the model field definition
     *
     * @return array of fields
     */
	public static function getStaticFieldsDefinition(){
		return static::_fieldsDefinition;
	}
	/**
     * returns the model field definition
     *
     * @return array of fields
     */
	public function getFieldsDefinition(){
		return static::getFieldsDefinition();
	}
	/**
     * Dynamically creates the validation rules according to the fields definition for the given action type
     *
     * @param string action type
     * @return array of validation rules
     */
	public function staticValidationRules($actionType){
		$rules=[];
		foreach(static::getFieldsDefinition() as $fieldName=>$fieldDefinition){
			$rule = array_key_exists($fieldDefinition[FIELD_PROPERTY_TYPE], static::getDefaultRulesByType())? 
				static::getDefaultRulesByType()[$fieldDefinition[FIELD_PROPERTY_TYPE]] : '';
			if($fieldDefinition[FIELD_PROPERTY_LENGTH]!='0'){
				$rule=static::addRule($rule,'max:'+$fieldDefinition[FIELD_PROPERTY_LENGTH]);
			}
			if(!$fieldDefinition[FIELD_PROPERTY_NULLABLE]){
				$rule=static::addRule($rule,'required');	
			}
			if($fieldDefinition[FIELD_PROPERTY_TYPE]==TYPE_TINYINT && $fieldDefinition[FIELD_PROPERTY_LENGTH]=='1'){
				$rule=static::addRule($rule,'boolean');
			}
			if($fieldDefinition[FIELD_PROPERTY_TYPE]==TYPE_DATETIME || $fieldDefinition[FIELD_PROPERTY_TYPE]==TYPE_TIMESTAMP){
				if($fieldDefinition[FIELD_PROPERTY_NULLABLE]){
					$rule=static::addRule($rule,'nullable');	
				}	
			}
		}
		return $rules;

	}
	/**
     * Dynamically creates the validation rules according to the fields definition for the given action type
     *
     * @param string action type
     * @return array of validation rules
     */
	public function validationRules($actionType){
		return static::staticValidationRules($actionType);
	}
	/**
     * add a validation rule to the existing pipe separated string of rules
     *
     * @param string validation rules
     * @param string new validation rule to be added
     * @return string of concatenated validation rules
     */
	public static function addRule($string,$rule){
		if($string!='')
			$string.='|';
		$string.=$rule;
	}
	/**
     * get the array of field names allowed in the given action
     *
     * @param  string action type
     * @return array of field names
     */
	protected function getFieldsForAction($actionType){
		$data =[];
		foreach($this->getFieldsDefinition() as $field=>$definition){
			if(in_array($field,$this->guarded))
				continue;
			if(is_array($definition) && array_key_exists('can'.$actionType, $definition) && !$definition['can'.$actionType])
				continue;
			array_push(	$data, $field);
		}
		return $data;
	}
	/**
     * fill the given model record with the data from the request
     *
     * @param  \Reddireccion\DryCrud\Models\Model
     * @param  string action type
     * @return \Reddireccion\DryCrud\Models\Model
     */
	public static function fillFromRequest($model,$actionType){
		if($model===null)
			return null;
		$record==is_object($model)?$model:new $model();
        $record->fill(Request::only($record->getFieldsForAction($actionType)));
        return $record;
	}
	/**
     * place holder for validation of duplicated records
     *
     * @return boolesn
     */
	public function isDuplicated(){
		return false;
	}
	/**
     * place holder for default scopes applied to the model in lists
     *
     * @param  QueryBuilder 
     * @return QueryBuilder
     */
	public static function scopeDefault($query){
		return $query;
	}

}
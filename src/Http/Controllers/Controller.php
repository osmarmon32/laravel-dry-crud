<?php

namespace Reddireccion\DryCrud\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as LaravelController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Reddireccion\DryCrud\Util\DryHelper;
use \APIResponse, \Request;

class Controller extends LaravelController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $modelClass;
    protected $modelsNamespace;

    /**
     * constructor that gets the model corresponding to this controller.
     *
     * @return JsonResponse
     */
    public function __construct(){
    	$this->setModelClass();
    }
    /**
     * return a list of records
     *
     * @return JsonResponse
     */
    public function getRecords(){
        return $this->getModelRecords($this->modelClass);
    }
    /**
     * return a single record
     *
     * @param integer id of the record
     * @return JsonResponse
     */
    public function getRecord($id)
    {
        return $this->getModelRecord($this->modelClass,$id);
    }
    /**
     * Creates a new record
     *
     * @param  Request data to create the new record
     * @return JsonResponse with \Reddireccion\DryCryd\Model
     */
    public function postRecord(Request $request){
    	return $this->postModelRecord($this->modelClass,$request);
    }
    /**
     * Updates a single record
     *
     * @param integer id of the record
     * @param Request data to update the record
     * @return JsonResponse with \Reddireccion\DryCryd\Model
     */
    public function putRecord($id, Request $request){
        return $this->putModelRecord($this->modelClass,$id, $request);
    }
    /**
     * Deletes a single record
     *
     * @param integer id of the record
     * @return empty JsonResponse
     */
    public function deleteRecord($id){
        $modelClass = $this->modelClass;
        return $this->deleteModelRecord($modelClass,$id);
    }
    /**
     * return a list of records for the given model
     *
     * @param string model name
     * @return JsonResponse
     */
    protected function getModelRecords($modelClass){
        $records=$modelClass::default();
        return APIResponse::records($records);
    }
    /**
     * return a single record for the given model
     *
     * @param string model name
     * @param integer id of the record
     * @return JsonResponse
     */
    protected function getModelRecord($modelClass,$id){
        $record=$modelClass::find($id);
        if($record)
            return APIResponse::record($record);
        return APIResponse::notFound("Record with id: '{$id}' not found"); 
    }
    /**
     * Creates a new record for the given model
     *
     * @param string model name
     * @param  Request data to create the new record
     * @return JsonResponse with \Reddireccion\DryCryd\Model
     */
    protected function postModelRecord($modelClass,Request $request){
    	$validator = Validator::make($request->all());
    	$record = $modelClass::fillFromRequest($modelClass,$modelClass::ACTION_CREATE);
    	if($validator->isValid($record,$modelClass::ACTION_CREATE)){
    		if($record->save())
    		  APIResponse::recordCreated($record);
    	}
    	return APIResponse::recordValidationError($validator->errors());
    }
    /**
     * Updates a single record for the given model
     *
     * @param string model name
     * @param integer id of the record
     * @param Request data to update the record
     * @return JsonResponse with \Reddireccion\DryCryd\Model
     */
    protected function putModelRecord($modelClass,$id, Request $request){
        $record=$modelClass::find($id);
        if(!$record){
            return APIResponse::notFound("Record with id: '{$id}' not found"); 
        }
        $validator = Validator::make($request->all());
        $record = $modelClass::fillFromRequest($record,$modelClass::ACTION_UPDATE);
        if($validator->isValid($record,$modelClass::ACTION_UPDATE)){
            if($record->save())
              APIResponse::recordUpdated($record);
        }
        return APIResponse::recordValidationError($validator->errors());
    }
    /**
     * Deletes a single record for the given model
     *
     * @param string model name
     * @param integer id of the record
     * @return empty JsonResponse
     */
    protected function deleteModelRecord($modelClass,$id){
        $record=$modelClass::find($id);
        if($record && $record->delete()){
           return APIResponse::deleted($record);
        }
        return APIResponse::notFound();
    }
    /**
     * if not declares, sets the model class name from the controller name
     *
     * @return void
     */
    private function setModelClass(){
    	if(!isset($this->modelClass)){
	    	if(!isset($this->modelsNamespace)){
	    		$this->modelsNamespace= defined('MULTI_APP_NAME')?'\\'.studly_case(MULTI_APP_NAME).'\\Models\\':'\\App\\Models\\';
	    	}
    		$this->modelClass = DryHelper::modelClassNameFromController($this->modelsNamespace);
    	}
    }
}
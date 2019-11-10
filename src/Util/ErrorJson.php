<?php

namespace Reddireccion\DryCrud\Util;

public class ErrorJson{
    public $code;
    public $message;
    public $status;
    public $details;
    /**
     * Create a new Error instance.
     *
     * @param  string error code from Reddireccion\DryCrud\ResponseFactory
     * @param  string status phrase for the http code from Reddireccion\DryCrud\Factory
     * @param  string message custom user message
     * @param  string optional details for debug
     * @return Reddireccion\DryCryd\ErrorJson
     */
    public static create($code,$status,$message,$details=null){
        $error = new ErrorJson();
        $error->code = $code;
        $error->status = $status;
        $error->message = $message;
        $error->details = $details;
        return $error;
    }
    /**
     * Set the details message from fields errors
     *
     * @param  string field name
     * @param  array of field errors from Laravel validator
     * @return void
     */
    public function setFieldError($field,$fieldError){
        if(!$this->details)
            $this->details = new BadRequest();
        $this->details->setFieldError($field,$fieldError);
    }
    // Describes violations in a client request. This error type focuses on the
    // syntactic aspects of the request.
    class BadRequest {
      // A message type used to describe a single bad request field.
      class FieldViolation {
        // A path leading to a field in the request body. The value will be a
        // sequence of dot-separated identifiers that identify a protocol buffer
        // field. E.g., "field_violations.field" would identify this field.
        public $field;

        // A description of why the request element is bad.
        public  $description;
        /**
         * constructor for FieldViolation.
         *
         * @param string $field field name
         * @param string $description error description
         * @return FieldViolation
         */
        public function __construct($field,$description){
            $this->field=$field;
            $this->description=$description;
        }
      }

      // Describes all violations in a client request.
      public $field_violations;
      /**
     * add a new FieldViolation to the array and intializes it if has not ben initialized before.
     *
     * @param string $field field name
     * @param string $description error description
     * @return void
     */
      public function setFieldError($field,$message){
        if(!$this->field_violations)
            $this->field_violations=[];
        array_push($this->field_violations,new FieldViolation($field,$message));
      }
    }
}
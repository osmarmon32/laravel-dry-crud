<?php

namespace Reddireccion\DryCrud\Routing;

use Illuminate\Routing\ResponseFactory as LaravelResponseFactory;
use \Reddireccion\DryCrud\Util\ErrorJson;
use \Reddireccion\DryCrud\Routing\IResponse;
use \Response;

use \Reddireccion\DryCrud\Pagination\Paginator as DryCrudPaginator;

class ResponseFactory extends LaravelResponseFactory implements IResponse
{
    public const ERROR_OK=0; //Not an error; returned on success, STATUS 200
    public const ERROR_CANCELLED = 1; // The operation was cancelled, typically by the caller. STATUS 499
    public const ERROR_UNKNOWN = 2; // Unknown error. STATUS 500
    public const ERROR_INVALID_ARGUMENT = 3;// The client specified an invalid argument regardless of the state of the system. STATUS 400
    public const ERROR_DEADLINE_EXCEEDED = 4;// The deadline expired before the operation could complete. STATUS 504
    public const ERROR_NOT_FOUND = 5; // Some requested entity (e.g., file or directory) was not found. STATUS 404
    public const ERROR_ALREADY_EXISTS = 6;// The entity that a client attempted to create (e.g., file or directory) STATUS 409
    public const ERROR_PERMISSION_DENIED = 7;// The caller does not have permission to execute the specified operation. STATUS 403
    public const ERROR_RESOURCE_EXHAUSTED = 8;// Some resource has been exhausted, quota, file system is out of space, etc. STATUS 429
    public const ERROR_FAILED_PRECONDITION = 9;// The operation was rejected because the system is not in a state required for the operation's execution.  STATUS 400
        //Use `FAILED_PRECONDITION` if the client should not retry until the system state has been explicitly fixed
    public const ERROR_ABORTED = 10;// The operation was aborted, typically due to a concurrency issue such as a sequencer check failure or transaction abort. STATUS 409
        //Use `ABORTED` if the client should retry at a higher level
    public const ERROR_OUT_OF_RANGE = 11;// The operation was attempted past the valid range.  E.g., seeking or reading past end-of-file. STATUS 400
    public const ERROR_UNIMPLEMENTED = 12;// The operation is not implemented or is not supported/enabled in this service. STATUS 501
    public const ERROR_INTERNAL = 13;// Internal errors.  This means that some invariants expected by the underlying system have been broken.  STATUS 500
    public const ERROR_UNAVAILABLE = 14; // This is most likely a transient condition, which can be corrected by retrying STATUS 503
        //Use `UNAVAILABLE` if the client can retry just the failing call.
    public const ERROR_DATA_LOSS = 15;// Unrecoverable data loss or corruption. STATUS 500
    public const ERROR_UNAUTHENTICATED = 16; // The request does not have valid authentication credentials for the operation. STATUS 401

  
    /** @var array Map of standard HTTP status code/reason phrases */
    public static $phrases = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-status',
        208 => 'Already Reported',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        511 => 'Network Authentication Required',
    ];
    /**
     * return a json response for a list of records
     *
     * @param  QueryBuilder $query
     * @return \Illuminate\Http\JsonResponse
     */
    public static function records($query){
        return Response::json(DryCrudPaginator::createFromQuery($query));
    }
    /**
     * return a json response for a given record
     *
     * @param  \Reddireccion\DryCrud\Models\Model
     * @return \Illuminate\Http\JsonResponse
     */
    public static function record($record){
        return Response::json(['data'=>$record]);
    }
    /**
     * return an empty json response for a deleted record 
     *
     * @param  \Reddireccion\DryCrud\Models\Model
     * @return \Illuminate\Http\JsonResponse
     */
    public static function deleted($record){
        return Response::json();
    }
    /**
     * return a json response for a new record
     *
     * @param  \Reddireccion\DryCrud\Models\Model
     * @return \Illuminate\Http\JsonResponse
     */
    public static function recordCreated($record){
        return static::record($record);
    }
    /**
     * return a json response for a updated record
     *
     * @param  \Reddireccion\DryCrud\Models\Model
     * @return \Illuminate\Http\JsonResponse
     */
    public static function recordUpdated($record){
        return static::record($record);
    }
    /**
     * return a json response for invalid arguments (when validation failed)
     *
     * @param  array errors from laravel validator
     * @return \Illuminate\Http\JsonResponse
     */
    public static function recordValidationError($errors){
        $msg = static::getStatusMessage(400);
        $error = ErrorJson::create(ResponseFactory::ERROR_INVALID_ARGUMENT,array_key,$msg,'Request validation failed');
        foreach($errors as $field=>$fieldError){
            $error->setDetails($field->$fieldError);
        }
        return Response::withStatus(400)->json($error);
        //TODO return default creation respons when data has errors  base on google api best practices specification
    }
    /**
     * return a not found http reponse
     *
     * @param  string custom message
     * @return \Illuminate\Http\JsonResponse
     */
    public static function notFound($shortMessage){
        return ResponseHelper::errorResponse(404,$shortMessage);
    }
    /**
     * Return a http error reponse
     *
     * @param integer http error code
     * @param string custom error message
     * @param string details for debug
     * @return \Illuminate\Http\JsonResponse
     */
    public static function errorResponse($code,$shortMessage,$longMessage=null){
        $error = ErrorJson::create($code,static::getStatusMessage($code),$shortMessage,$longMessage);
        return Response::withStatus($code)->json($error);
    }
    /**
     * get the message corresponding to a given http code
     *
     * @param integer http code
     * @return string
     */
    public static function getStatusMessage($status){
        return array_key_exists($status, ResponseHelper::phrases)?ResponseHelper::phrases[$status]:'';
    }
}
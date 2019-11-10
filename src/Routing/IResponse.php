<?php

namespace Reddireccion\DryCrud\Routing;

use Illuminate\Contracts\Routing\ResponseFactory;

interface IResponse extends ResponseFactory
{
    /**
     * return a json response for a list of records
     *
     * @param  QueryBuilder $query
     * @return \Illuminate\Http\JsonResponse
     */
	public static function records($records);
    /**
     * return a json response for a given record
     *
     * @param  \Reddireccion\DryCrud\Models\Model
     * @return \Illuminate\Http\JsonResponse
     */
    public static function record($record);
    /**
     * return an empty json response for a deleted record 
     *
     * @param  \Reddireccion\DryCrud\Models\Model
     * @return \Illuminate\Http\JsonResponse
     */
    public static function deleted();
    /**
     * return a json response for a new record
     *
     * @param  \Reddireccion\DryCrud\Models\Model
     * @return \Illuminate\Http\JsonResponse
     */
    public static function recordCreated($record);
    /**
     * return a json response for a updated record
     *
     * @param  \Reddireccion\DryCrud\Models\Model
     * @return \Illuminate\Http\JsonResponse
     */
    public static function recordUpdated($record);
    /**
     * return a not found http reponse
     *
     * @param  string custom message
     * @return \Illuminate\Http\JsonResponse
     */
    public static function notFound($shortMessage);
    /**
     * Return a http error reponse
     *
     * @param integer http error code
     * @param string custom error message
     * @param string details for debug
     * @return \Illuminate\Http\JsonResponse
     */
    public static function errorResponse($code,$shortMessage,$longMessage=null);
}
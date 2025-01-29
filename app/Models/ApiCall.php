<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ApiCall extends Model
{
    protected $table = 'api_calls';

    protected $fillable = ['api','hash','request'];

    /**
     * Stores an API call to cache
     *
     * @param string $api
     * @param string $hash
     * @param array $requestParams
     * @return ApiCall
     */
    public static function cache($api,$hash,$requestParams)
    {
        $apiCall = new ApiCall([
            'api' => $api,
            'hash' => $hash,
            'request' => json_encode($requestParams)
        ]);
        $apiCall->save();
        return $apiCall;
    }

    /**
     * Returns cached result for the given API, hash and timeout in hours
     * or NULL if there is no acceptable cached results
     *
     * @param string $api
     * @param string $hash
     * @param int $timeoutHours
     * @return ApiCall|\Illuminate\Database\Eloquent\Builder|Model|\Illuminate\Database\Query\Builder|object|null
     */
    public static function fromCache($api, $hash, $timeoutHours)
    {
        $apiCall = ApiCall::where(['api' => $api, 'hash' => $hash])
            ->orderBy('updated_at','DESC')
            ->first();
        return ($apiCall && $apiCall->updated_at->isAfter(Carbon::now()->subHours($timeoutHours))) ? $apiCall : null;
    }

    /**
     * Returns JSON decoded response
     *
     * @param bool $assoc
     * @return object|null|array
     */
    public function getResponse($assoc = false)
    {
        return json_decode($this->response, $assoc);
    }

    /**
     * Checks if response is saved (not null)
     *
     * @return bool
     */
    public function hasResponse()
    {
        return $this->response !== null;
    }
}

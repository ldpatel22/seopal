<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use JavaScript;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Prepares JavaScript variables
     *
     * @param array $data
     */
    protected function putJavaScript($data)
    {
        JavaScript::put($data);
    }

    /**
     * Converts a URL slug to an ID
     *
     * @param string $slug
     * @return string
     */
    protected function getIdFromSlug($slug)
    {
        return explode('-',$slug)[0];
    }
}

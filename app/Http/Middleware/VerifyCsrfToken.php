<?php
/*
 * @Author: your name
 * @Date: 2020-01-14 19:54:02
 * @LastEditTime: 2020-01-15 18:11:26
 * @LastEditors: your name
 * @Description: In User Settings Edit
 * @FilePath: \dengru\app\Http\Middleware\VerifyCsrfToken.php
 */

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
        '*'
    ];
}

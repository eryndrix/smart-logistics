<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    /**
     * Handles authorization checks and policies.
     */
    use AuthorizesRequests;

    /**
     * Manages request validation and errors.
     */
    use ValidatesRequests;
}

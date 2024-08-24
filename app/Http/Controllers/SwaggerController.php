<?php

namespace App\Http\Controllers;


use OpenApi\Annotations as OA;

use Swagger\Annotations as SWG;

/**
 * @SWG\Swagger(
 *     basePath="/api",
 *     schemes={"http", "https"},
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="My API",
 *         description="API documentation for My API",
 *         @SWG\Contact(name="My Company"),
 *         @SWG\License(name="MIT")
 *     )
 * )
 */

class SwaggerController extends Controller
{

}
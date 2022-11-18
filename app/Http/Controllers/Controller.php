<?php

namespace App\Http\Controllers;

//use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="FASE 2 API",
 *      description="FASE 2 Swagger OpenApi",
 *      x={
 *          "logo": {
 *              "url": "https://via.placeholder.com/190x90.png?text=L5-Swagger"
 *          }
 *      },
 *      @OA\Contact(
 *          email="eaguilar.arrezola@gmail.com"
 *      ),
 *      @OA\License(
 *         name="Apache 2.0",
 *         url="https://www.apache.org/licenses/LICENSE-2.0.html"
 *     )
 * )
 */

 /*
  @OAS\SecurityScheme(
      securityScheme="bearerAuth",
      type="http",
      scheme="bearer"
  )
 */
class Controller extends BaseController
{
    use  DispatchesJobs, ValidatesRequests;
}

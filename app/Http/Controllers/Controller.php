<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Meat swagger documentation",
 *      description="Test project meat-facture",
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Enter JWT token in format: **Bearer {token}**"
 * )
 */
abstract class Controller {}

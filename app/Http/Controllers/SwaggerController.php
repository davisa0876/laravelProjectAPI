<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use L5Swagger\Http\Controllers\SwaggerController as BaseSwaggerController;

class SwaggerController extends BaseSwaggerController
{
    public function api(Request $request)
    {
        $documentation = $request->offsetGet('documentation');
        $config = config('l5-swagger.documentations.' . $documentation);

        return response()->view('l5-swagger::index', [
            'documentation' => $documentation,
            'secure' => $request->secure(),
            'urlToDocs' => route('l5-swagger.' . $documentation . '.docs', [], config('l5-swagger.documentations.' . $documentation . '.paths.use_absolute_path', true)),
            'operationsSorter' => $config['operations_sort'] ?? null,
            'configUrl' => $config['additional_config_url'] ?? null,
            'validatorUrl' => $config['validator_url'] ?? null,
            'useAbsolutePath' => $config['paths']['use_absolute_path'] ?? true,
        ]);
    }
} 
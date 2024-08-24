<?php
require($_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php');
//$openapi = \OpenApi\Generator::scan(['/path/to/project']);
$openapi = \OpenApi\Generator::scan($_SERVER['DOCUMENT_ROOT'].'/app/Http/Controllers');
header('Content-Type: application/x-yaml');
echo $openapi->toYaml();
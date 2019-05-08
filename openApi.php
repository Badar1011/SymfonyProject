<?php
require("vendor/autoload.php");
$openapi = \OpenApi\scan($this->get('kernel')->getProjectDir());
header('Content-Type: application/x-yaml');
echo $openapi->toYaml();
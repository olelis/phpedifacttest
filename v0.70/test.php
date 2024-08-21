<?php

use EDI\Analyser;
use EDI\Interpreter;
use EDI\Mapping\MappingProvider;
use EDI\Parser;

require 'vendor/autoload.php';



$data = parseString('editest.txt');

file_put_contents('editest.json', json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE | JSON_PRETTY_PRINT));

print_r($data);

function parseString(string $filename): array
{
    $parser = new Parser();
    $parser->load($filename);
    $parsed = $parser->get();

    $analyser = new Analyser();
    $mapping = new MappingProvider('D96A');
    $segs = $analyser->loadSegmentsXml($mapping->getSegments());
    $svc = $analyser->loadSegmentsXml($mapping->getServiceSegments());

    $interpreter = new Interpreter($mapping->getMessage('ORDERS'), $segs, $svc);
    $groups = $interpreter->prepare($parsed);


    return [
        'errors' => $interpreter->getErrors(),
        'groups' =>$groups,
    ];
}
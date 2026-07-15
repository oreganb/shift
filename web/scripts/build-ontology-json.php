#!/usr/bin/env php
<?php
/**
 * Parse SHIFT OWL/RDF-XML modules into data/json/ontology.json
 */
require_once dirname(__DIR__) . '/includes/config.php';

$ontologyDir = SHIFT_ONTOLOGY;

$classes = [];
$objectProperties = [];
$datatypeProperties = [];
$rules = [];

function localName(string $uri): string
{
    if (str_contains($uri, '#')) {
        return substr($uri, strrpos($uri, '#') + 1);
    }
    if (str_contains($uri, '/')) {
        return substr($uri, strrpos($uri, '/') + 1);
    }
    return $uri;
}

function parseOwlFile(string $path, array &$classes, array &$objectProperties, array &$datatypeProperties, array &$rules): void
{
    if (!is_readable($path)) {
        return;
    }
    $xml = @simplexml_load_file($path);
    if ($xml === false) {
        fwrite(STDERR, "WARN: could not parse $path\n");
        return;
    }
    $xml->registerXPathNamespace('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
    $xml->registerXPathNamespace('rdfs', 'http://www.w3.org/2000/01/rdf-schema#');
    $xml->registerXPathNamespace('owl', 'http://www.w3.org/2002/07/owl#');
    $xml->registerXPathNamespace('shift', 'http://shift-ontology.org/core#');

    foreach ($xml->xpath('//rdf:Description') ?: [] as $desc) {
        $about = (string) ($desc->attributes('rdf', true)->about ?? '');
        if ($about === '') {
            continue;
        }
        $name = localName($about);

        $types = [];
        foreach ($desc->children('rdf', true)->type as $t) {
            $types[] = (string) ($t->attributes('rdf', true)->resource ?? '');
        }

        $label = (string) ($desc->children('rdfs', true)->label ?? '');
        $comment = (string) ($desc->children('rdfs', true)->comment ?? '');

        if (in_array('http://www.w3.org/2002/07/owl#Class', $types, true)) {
            $subClassOf = [];
            foreach ($desc->children('rdfs', true)->subClassOf as $sc) {
                $subClassOf[] = localName((string) ($sc->attributes('rdf', true)->resource ?? ''));
            }
            $classes[$name] = [
                'iri'         => $about,
                'name'        => $name,
                'label'       => $label ?: $name,
                'comment'     => $comment,
                'subClassOf'  => array_values(array_filter($subClassOf)),
                'source'      => basename($path),
            ];
        } elseif (in_array('http://www.w3.org/2002/07/owl#ObjectProperty', $types, true)) {
            $domain = localName((string) ($desc->children('rdfs', true)->domain->attributes('rdf', true)->resource ?? ''));
            $range = localName((string) ($desc->children('rdfs', true)->range->attributes('rdf', true)->resource ?? ''));
            $objectProperties[$name] = [
                'iri'    => $about,
                'name'   => $name,
                'label'  => $label ?: $name,
                'comment'=> $comment,
                'domain' => $domain,
                'range'  => $range,
                'source' => basename($path),
            ];
        } elseif (in_array('http://www.w3.org/2002/07/owl#DatatypeProperty', $types, true)) {
            $domain = localName((string) ($desc->children('rdfs', true)->domain->attributes('rdf', true)->resource ?? ''));
            $range = (string) ($desc->children('rdfs', true)->range->attributes('rdf', true)->resource ?? '');
            $datatypeProperties[$name] = [
                'iri'    => $about,
                'name'   => $name,
                'label'  => $label ?: $name,
                'comment'=> $comment,
                'domain' => $domain,
                'range'  => localName($range) ?: $range,
                'source' => basename($path),
            ];
        } elseif (in_array('http://www.w3.org/2002/07/owl#Axiom', $types, true) || str_starts_with($name, 'SHIFT-RR-')) {
            $rule = [
                'id'      => $name,
                'iri'     => $about,
                'label'   => $label ?: $name,
                'comment' => $comment,
                'source'  => basename($path),
            ];
            foreach ($desc->children('shift', true) as $child) {
                $rule[$child->getName()] = (string) $child;
            }
            $rules[$name] = $rule;
        }
    }
}

// Parse all OWL files in ontology directory
foreach (glob($ontologyDir . '/*.owl') ?: [] as $file) {
    parseOwlFile($file, $classes, $objectProperties, $datatypeProperties, $rules);
}

// Build subclass index
$subclasses = [];
foreach ($classes as $name => $cls) {
    foreach ($cls['subClassOf'] as $parent) {
        $subclasses[$parent][] = $name;
    }
}
foreach ($subclasses as $parent => $kids) {
    sort($kids);
    $subclasses[$parent] = $kids;
}

// Property indexes by domain
$propsByDomain = ['object' => [], 'datatype' => []];
foreach ($objectProperties as $p) {
    if ($p['domain']) {
        $propsByDomain['object'][$p['domain']][] = $p['name'];
    }
}
foreach ($datatypeProperties as $p) {
    if ($p['domain']) {
        $propsByDomain['datatype'][$p['domain']][] = $p['name'];
    }
}

ksort($classes);
ksort($objectProperties);
ksort($datatypeProperties);
ksort($rules);

$output = [
    'version'           => SHIFT_VERSION,
    'iriBase'           => SHIFT_IRI_BASE,
    'generated'         => date('c'),
    'stats'             => [
        'classes'           => count($classes),
        'objectProperties'  => count($objectProperties),
        'datatypeProperties'=> count($datatypeProperties),
        'rules'             => count($rules),
    ],
    'classes'           => $classes,
    'objectProperties'  => $objectProperties,
    'datatypeProperties'=> $datatypeProperties,
    'subclasses'        => $subclasses,
    'propertiesByDomain'=> $propsByDomain,
    'rules'             => $rules,
];

if (!is_dir(SHIFT_JSON)) {
    mkdir(SHIFT_JSON, 0755, true);
}

$outFile = SHIFT_JSON . '/ontology.json';
file_put_contents($outFile, json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

echo "Wrote {$output['stats']['classes']} classes, {$output['stats']['objectProperties']} object properties, ";
echo "{$output['stats']['datatypeProperties']} datatype properties, {$output['stats']['rules']} rules to $outFile\n";

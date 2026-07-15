<?php
/**
 * Shared MySQL import logic (CLI + web install).
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

function shift_import_to_db(): array
{
    $ontologyFile = SHIFT_JSON . '/ontology.json';
    $vaultFile = SHIFT_JSON . '/vault.json';

    if (!file_exists($ontologyFile) || !file_exists($vaultFile)) {
        throw new RuntimeException('Run build scripts first — missing JSON data.');
    }

    $ontology = json_decode(file_get_contents($ontologyFile), true);
    $vault = json_decode(file_get_contents($vaultFile), true);
    $db = shift_db();
    $db->exec('SET NAMES utf8mb4');

    $db->exec('SET FOREIGN_KEY_CHECKS=0');
    foreach (['vault_links', 'vault_notes', 'reasoning_rules', 'ontology_properties', 'ontology_classes', 'site_metadata'] as $table) {
        $db->exec('DELETE FROM ' . $table);
    }
    $db->exec('SET FOREIGN_KEY_CHECKS=1');

    $stmtClass = $db->prepare('INSERT INTO ontology_classes (name, iri, label, comment, sub_class_of, source_file) VALUES (?,?,?,?,?,?) ON DUPLICATE KEY UPDATE iri=VALUES(iri), label=VALUES(label), comment=VALUES(comment), sub_class_of=VALUES(sub_class_of), source_file=VALUES(source_file)');
    foreach ($ontology['classes'] as $c) {
        $stmtClass->execute([
            $c['name'], $c['iri'], $c['label'], $c['comment'] ?? '',
            json_encode($c['subClassOf'] ?? []), $c['source'] ?? '',
        ]);
    }

    $stmtProp = $db->prepare('INSERT INTO ontology_properties (name, iri, label, prop_type, domain_class, range_value, comment, source_file) VALUES (?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE iri=VALUES(iri), label=VALUES(label), prop_type=VALUES(prop_type), domain_class=VALUES(domain_class), range_value=VALUES(range_value), comment=VALUES(comment), source_file=VALUES(source_file)');
    foreach ($ontology['objectProperties'] as $p) {
        $stmtProp->execute([$p['name'], $p['iri'], $p['label'], 'object', $p['domain'], $p['range'], $p['comment'] ?? '', $p['source'] ?? '']);
    }
    foreach ($ontology['datatypeProperties'] as $p) {
        $stmtProp->execute([$p['name'], $p['iri'], $p['label'], 'datatype', $p['domain'], $p['range'], $p['comment'] ?? '', $p['source'] ?? '']);
    }

    $stmtRule = $db->prepare('INSERT INTO reasoning_rules (rule_id, iri, label, comment, applies_to, infers, metadata, source_file) VALUES (?,?,?,?,?,?,?,?)');
    foreach ($ontology['rules'] as $r) {
        $meta = $r;
        unset($meta['id'], $meta['iri'], $meta['label'], $meta['comment'], $meta['source']);
        $stmtRule->execute([
            $r['id'], $r['iri'], $r['label'], $r['comment'] ?? '',
            $meta['appliesToClass'] ?? null, $meta['infersClass'] ?? null,
            json_encode($meta), $r['source'] ?? '',
        ]);
    }

    $stmtNote = $db->prepare('INSERT INTO vault_notes (slug, title, folder, body, meta, preview) VALUES (?,?,?,?,?,?)');
    $stmtLink = $db->prepare('INSERT INTO vault_links (source_slug, target_slug, target_title, alias, resolved) VALUES (?,?,?,?,?)');
    foreach ($vault['notes'] as $slug => $n) {
        $stmtNote->execute([$slug, $n['title'], $n['folder'], $n['body'], json_encode($n['meta']), $n['preview']]);
        foreach ($n['links'] as $link) {
            $stmtLink->execute([
                $slug, $link['resolved'], $link['target'], $link['alias'], $link['resolved'] ? 1 : 0,
            ]);
        }
    }

    $stmtMeta = $db->prepare('INSERT INTO site_metadata (meta_key, meta_value) VALUES (?,?) ON DUPLICATE KEY UPDATE meta_value=VALUES(meta_value)');
    $stmtMeta->execute(['ontology_version', SHIFT_VERSION]);
    $stmtMeta->execute(['last_import', date('c')]);
    $stmtMeta->execute(['note_count', (string) count($vault['notes'])]);

    return [
        'classes' => count($ontology['classes']),
        'notes'   => count($vault['notes']),
    ];
}

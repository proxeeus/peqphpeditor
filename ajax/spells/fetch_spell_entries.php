<?php
header('Content-Type: text/html; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/../../classes/mysqli.php');

$spell_types = [
  1 << 0 => 'Nuke',
  1 << 1 => 'Heal',
  1 << 2 => 'Root',
  1 << 3 => 'Buff',
  1 << 4 => 'Escape',
  1 << 5 => 'Pet',
  1 << 6 => 'Lifetap',
  1 << 7 => 'Snare',
  1 << 8 => 'DOT',
  1 << 9 => 'Dispel',
  1 << 10 => 'InCombatBuff',
  1 << 11 => 'Mez',
  1 << 12 => 'Charm',
  1 << 13 => 'Slow',
  1 << 14 => 'Debuff',
  1 << 15 => 'Cure',
  1 << 16 => 'Resurrect',
  1 << 17 => 'HateRedux',
  1 << 18 => 'InCombatBuffSong',
  1 << 19 => 'OutOfCombatBuffSong',
  1 << 20 => 'PreCombatBuff',
  1 << 21 => 'PreCombatBuffSong',
];

$npc_spells_id = $_GET['npc_spells_id'] ?? '';

if (!$npc_spells_id) {
  echo "<tr><td colspan='16'>Error: npc_spells_id is missing or invalid.</td></tr>";
  exit;
}

if (!isset($mysql_content_db)) {
  echo "<tr><td colspan='16'>Error: Database connection is not initialized.</td></tr>";
  exit;
}

$query = "SELECT bse.*, sn.name AS spell_name FROM bot_spells_entries bse LEFT JOIN spells_new sn ON bse.spell_id = sn.id WHERE bse.npc_spells_id = $npc_spells_id ORDER BY bse.minlevel ASC, bse.id";

$results = $mysql_content_db->query_mult_assoc($query);

if (!$results || count($results) === 0) {
  echo "<tr class='no-entries-row'><td colspan='16'>No spell entries found for this class.</td></tr>";
  return;
}

foreach ($results as $row) {
  echo "<tr>";
  // New first column: delete button
  echo "<td><button class='delete' data-id='{$row['id']}' style='background:none;border:none;padding:0;cursor:pointer;'><img src='images/remove3.gif' alt='Delete' title='Delete'></button></td>";
  echo "<td>{$row['id']}</td>";
  echo "<td>{$row['npc_spells_id']}</td>";
  // Spell name column, as a hyperlink to the spell editor page
  $spell_id = (int)($row['spell_id'] ?? 0);
  $spell_name_link = $spell_id ? "<a href='index.php?editor=spells&id={$spell_id}&action=2' target='_blank'>" . htmlspecialchars($row['spell_name']) . "</a>" : htmlspecialchars($row['spell_name']);
  echo "<td class='spell-name'>{$spell_name_link}</td>";
  echo "<td><select class='editable' data-id='{$row['id']}' data-column='type' style='width:90px;'>";
  foreach ($spell_types as $bit => $label) {
    $selected = ($row['type'] == $bit) ? 'selected' : '';
    echo "<option value='$bit' $selected>($bit) $label</option>";
  }
  echo "</select></td>";
  echo "<td><input type='text' class='editable' data-id='{$row['id']}' data-column='minlevel' value='{$row['minlevel']}'></td>";
  echo "<td><input type='text' class='editable' data-id='{$row['id']}' data-column='maxlevel' value='{$row['maxlevel']}'></td>";
  echo "<td><input type='text' class='editable' data-id='{$row['id']}' data-column='manacost' value='{$row['manacost']}'></td>";
  echo "<td><input type='text' class='editable' data-id='{$row['id']}' data-column='recast_delay' value='{$row['recast_delay']}'></td>";
  echo "<td><input type='text' class='editable' data-id='{$row['id']}' data-column='priority' value='{$row['priority']}'></td>";
  echo "<td><input type='text' class='editable' data-id='{$row['id']}' data-column='resist_adjust' value='{$row['resist_adjust']}'></td>";
  echo "<td><input type='text' class='editable' data-id='{$row['id']}' data-column='min_hp' value='{$row['min_hp']}'></td>";
  echo "<td><input type='text' class='editable' data-id='{$row['id']}' data-column='max_hp' value='{$row['max_hp']}'></td>";
  echo "<td><input type='text' class='editable' data-id='{$row['id']}' data-column='bucket_name' value='{$row['bucket_name']}'></td>";
  echo "<td><input type='text' class='editable' data-id='{$row['id']}' data-column='bucket_value' value='{$row['bucket_value']}'></td>";
  echo "<td><input type='text' class='editable' data-id='{$row['id']}' data-column='bucket_comparison' value='{$row['bucket_comparison']}'></td>";
  // Rightmost delete button
  echo "<td><button class='delete' data-id='{$row['id']}' style='background:none;border:none;padding:0;cursor:pointer;'><img src='images/remove3.gif' alt='Delete' title='Delete'></button></td>";
  echo "</tr>";
}
?>

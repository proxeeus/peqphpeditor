<?php
require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/../../classes/mysqli.php');

$search = $_GET['search'] ?? '';

if ($search) {
  $query = "SELECT id, name FROM spells_new WHERE name LIKE '%$search%' ORDER BY name";
  $results = $mysql_content_db->query_mult_assoc($query);

  foreach ($results as $row) {
    echo "<tr>";
    echo "<td>{$row['id']}</td>";
    echo "<td>{$row['name']}</td>";
    echo "<td style='text-align:center;vertical-align:middle;'><button class='add_spell' data-spell-id='{$row['id']}' style='background:none;border:none;padding:0;cursor:pointer;'><img src='images/add.gif' alt='Add' title='Add'></button></td>";
    echo "</tr>";
  }
} else {
  echo "<tr><td colspan='3'>No results found</td></tr>";
}
?>

<?php
header('Content-Type: text/html; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/../../classes/mysqli.php');
require_once(__DIR__ . '/../../lib/logging.php');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

if (!$action) {
  echo "Error: Action parameter is missing.";
  exit;
}

switch ($action) {
  case 'fetch':
    $query = "SELECT bse.*, sn.name AS spell_name FROM bot_spells_entries bse LEFT JOIN spells_new sn ON bse.spellid = sn.id ORDER BY bse.id";
    $results = $mysql_content_db->query_mult_assoc($query);

    foreach ($results as $row) {
      echo "<tr>";
      foreach ($row as $key => $value) {
        if ($key === 'id') {
          echo "<td>$value</td>";
        } elseif ($key === 'spell_name') {
          echo "<td>$value</td>";
        } else {
          echo "<td><input type='text' class='editable' data-id='{$row['id']}' data-column='$key' value='$value'></td>";
        }
      }
      echo "<td><button class='delete' data-id='{$row['id']}'>Delete</button></td>";
      echo "</tr>";
    }
    break;

  case 'add':
    $query = "INSERT INTO bot_spells_entries (npc_spells_id, spellid, type, minlevel, maxlevel, manacost, recast_delay, priority, resist_adjust, min_hp, max_hp, bucket_name, bucket_value, bucket_comparison) VALUES (0, 0, 0, 0, 255, -1, -1, 0, 0, 0, 0, '', '', 0)";
    $mysql_content_db->query_no_result($query);
    break;

  case 'add_spell':
    $npc_spells_id = intval($_POST['npc_spells_id'] ?? 0);
    $spell_id = intval($_POST['spell_id'] ?? 0);
    if ($npc_spells_id > 0 && $spell_id > 0) {
        // Insert with default values
        $query = "INSERT INTO bot_spells_entries (npc_spells_id, spell_id, type, minlevel, maxlevel, manacost, recast_delay, priority, resist_adjust, min_hp, max_hp, bucket_name, bucket_value, bucket_comparison) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $params = [
            $npc_spells_id,
            $spell_id,
            1 << 0, // Default type: Nuke
            1,      // minlevel
            255,    // maxlevel
            -1,     // manacost
            -1,     // recast_delay
            1,      // priority
            0,      // resist_adjust
            0,      // min_hp
            0,      // max_hp // Changed default value to 0
            '',     // bucket_name
            0,      // bucket_value
            0       // bucket_comparison
        ];
        $stmt = $mysql_content_db->prepare($query);
        $stmt->bind_param('iiiiiiiiiiiisi', ...$params);
        if ($stmt->execute()) {
            echo 'success';
            exit;
        } else {
            echo 'error';
            exit;
        }
        $stmt->close();
        exit;
    }
    echo 'error';
    exit;
    break;

  case 'update':
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $column = isset($_POST['column']) ? $_POST['column'] : '';
    $value = isset($_POST['value']) ? $_POST['value'] : '';
    $allowed_columns = [
      'type', 'minlevel', 'maxlevel', 'manacost', 'recast_delay', 'priority',
      'resist_adjust', 'min_hp', 'max_hp', 'bucket_name', 'bucket_value', 'bucket_comparison'
    ];
    if ($id > 0 && in_array($column, $allowed_columns, true)) {
      $numeric_columns = [
        'type', 'minlevel', 'maxlevel', 'manacost', 'recast_delay', 'priority',
        'resist_adjust', 'min_hp', 'max_hp', 'bucket_value'
      ];
      if (in_array($column, $numeric_columns, true)) {
        $value = is_numeric($value) ? (int)$value : 0;
      } else {
        $value = $mysql_content_db->real_escape_string($value);
        $value = "'" . $value . "'";
      }
      $query = "UPDATE bot_spells_entries SET `$column` = $value WHERE id = $id LIMIT 1";
      $result = $mysql_content_db->query_no_result($query);
      if ($result) {
        echo 'OK';
      } else {
        echo 'DB error: ' . $mysql_content_db->error . ' | Query: ' . $query;
      }
    } else {
      echo 'Invalid update';
    }
    exit;

  case 'delete':
    $id = $_POST['id'];

    $query = "DELETE FROM bot_spells_entries WHERE id = $id";
    $mysql_content_db->query_no_result($query);
    break;

  default:
    echo "Invalid action.";
    break;
}

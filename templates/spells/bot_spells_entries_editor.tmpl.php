<html>
<head>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link rel="stylesheet" href="/peqphpeditor/css/peq.css">
  <style>
    #bot_spells_table th, #bot_spells_table td {
      min-width: unset !important;
      max-width: unset !important;
      width: unset !important;
      padding: 2px 4px !important;
      font-size: 12px !important;
      text-align: center;
    }
    #bot_spells_table th[style], #bot_spells_table td[style] {
      width: unset !important;
    }
    #bot_spells_table th[style] {
      white-space: nowrap;
    }
    #bot_spells_table td input[type="text"] {
      width: 50px !important;
      font-size: 12px;
      padding: 1px 2px;
      box-sizing: border-box;
    }
    #bot_spells_table td.spell-name {
      max-width: 180px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }
    /* Spell search popup overlay and site-matching popup styling */
    #spell_popup_overlay {
      display: none;
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(0,0,0,0.35);
      z-index: 1000;
    }
    #spell_popup {
      display: none;
      position: fixed;
      left: 50%;
      top: 50%;
      transform: translate(-50%, -50%);
      background: #eaeaea; /* matches .table_content2 */
      border-radius: 8px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.18);
      border: 1px solid #b5b5b5; /* matches .table_content2 border */
      padding: 22px 28px 18px 28px;
      min-width: 420px;
      max-width: 98vw;
      z-index: 1001;
      font-size: 14px;
    }
    #spell_popup .table_header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      font-size: 16px;
      font-weight: bold;
      margin-bottom: 10px;
      background: #b5b5b5; /* matches .table_header */
      color: #222;
      border-radius: 6px 6px 0 0;
      padding: 6px 10px;
      min-height: 36px;
    }
    #spell_popup .table_header .popup-title {
      flex: 1 1 auto;
      text-align: left;
    }
    #spell_popup .table_header .popup-close {
      flex: 0 0 auto;
      margin-left: 16px;
      padding: 4px 14px;
      font-size: 13px;
      border-radius: 5px;
      border: 1px solid #b5b5b5;
      background: #d6d6d6; /* matches .table_content2 header */
      color: #222;
      cursor: pointer;
      transition: background 0.2s;
    }
    #spell_popup .table_header .popup-close:hover {
      background: #b5b5b5;
      color: #fff;
    }
    #spell_popup input[type="text"] {
      width: 70%;
      padding: 6px 8px;
      font-size: 14px;
      border: 1px solid #b5b5b5;
      border-radius: 5px;
      margin-right: 8px;
      box-sizing: border-box;
      background: #f8f8f8;
      color: #222;
    }
    #spell_popup button, #spell_popup .add_spell {
      padding: 5px 14px;
      font-size: 13px;
      border-radius: 5px;
      border: 1px solid #b5b5b5;
      background: #d6d6d6; /* matches .table_content2 header */
      color: #222;
      cursor: pointer;
      transition: background 0.2s;
    }
    #spell_popup button:hover, #spell_popup .add_spell:hover {
      background: #b5b5b5;
      color: #fff;
    }
    #spell_results {
      width: 100%;
      border-collapse: collapse;
      margin-top: 14px;
      font-size: 13px;
      background: #eaeaea;
    }
    #spell_results th, #spell_results td {
      border: 1px solid #b5b5b5;
      padding: 6px 8px;
      text-align: left;
    }
    #spell_results th {
      background: #b5b5b5;
      color: #222;
      font-weight: bold;
    }
    #spell_results tbody tr:nth-child(even) {
      background: #f8f8f8;
    }
    #spell_results tbody tr:hover {
      background: #d6e6f7;
    }
    #search_spell.disabled, #search_spell:disabled {
      opacity: 0.5;
      pointer-events: none;
      cursor: not-allowed;
    }
    #search_spell {
      padding: 3px 10px !important;
      font-size: 12px !important;
      border-radius: 4px;
      height: 26px;
      min-width: 0;
    }
    /* No entries popup */
    #no-entries-popup {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      background: rgba(0,0,0,0.18);
      z-index: 2000;
      align-items: center;
      justify-content: center;
    }
    #no-entries-popup > div {
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.18);
      padding: 32px 48px 28px 48px;
      min-width: 320px;
      max-width: 90vw;
      text-align: center;
      font-size: 16px;
      color: #444;
      border: 1px solid #e0e0e0;
    }
    #no-entries-popup .popup-title {
      font-size: 20px;
      display: block;
      margin-bottom: 10px;
    }
    #no-entries-popup button {
      margin-top: 18px;
      padding: 6px 22px;
      font-size: 14px;
      border-radius: 6px;
      border: 1px solid #bbb;
      background: #eaeaea;
      cursor: pointer;
    }
    /* Ensure tbody does not vertically center content when empty */
    #bot_spells_table tbody {
      vertical-align: top !important;
      min-height: 0 !important;
      height: auto !important;
      display: table-row-group;
    }
    .no-entries-row td {
      text-align: center;
      color: #666;
      background: #f8f8f8;
      font-style: italic;
      font-size: 15px;
      padding-top: 8px;
      padding-bottom: 8px;
    }
    /* Modal popup for empty field validation */
    #field-empty-modal {
      display: none;
      position: fixed;
      z-index: 3000;
      left: 0;
      top: 0;
      width: 100vw;
      height: 100vh;
      background: rgba(0,0,0,0.10);
      align-items: center;
      justify-content: center;
    }
    #field-empty-modal > div {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 24px rgba(0,0,0,0.13);
      padding: 22px 36px 18px 36px;
      min-width: 220px;
      max-width: 90vw;
      text-align: center;
      font-size: 15px;
      color: #b00;
      border: 1px solid #e0b4b4;
      display: inline-block;
      position: relative;
    }
    #field-empty-modal .popup-title {
      font-size: 17px;
      display: block;
      margin-bottom: 8px;
      font-weight: 500;
    }
    #field-empty-modal button {
      margin-top: 10px;
      padding: 5px 18px;
      font-size: 13px;
      border-radius: 5px;
      border: 1px solid #bbb;
      background: #f5f5f5;
      color: #333;
      cursor: pointer;
      transition: background 0.2s;
    }
    #field-empty-modal span#field-empty-x {
      position: absolute;
      top: 7px;
      right: 10px;
      font-size: 18px;
      color: #bbb;
      cursor: pointer;
    }
  </style>
</head>
<body>
<div class="table_container" style="width: 900px; margin: 40px auto;">
  <div class="table_header">
    <table width="100%" cellpadding="0" cellspacing="0">
      <tr>
        <td>Bot Spells Entries Editor</td>
        <td align="right">
          <button id="search_spell" style="background:none;border:none;padding:0;cursor:pointer;vertical-align:middle;line-height:0;"><img src="images/add.gif" alt="Search and Add Spell" title="Search and Add Spell" style="vertical-align:middle;margin-top:-8px;margin-left:8px;"></button>
        </td>
      </tr>
    </table>
  </div>
  <div class="table_content" style="padding: 20px;">
    <form>
      <table width="100%" cellpadding="6" cellspacing="0">
        <tr>
          <td style="width: 300px;">
            <label for="class-select"><strong>Select a Class:</strong></label><br>
            <select id="class-select" style="width: 100%;">
              <option value="">-- Select a Class --</option>
              <option value="3001">Warrior</option>
              <option value="3002">Cleric</option>
              <option value="3003">Paladin</option>
              <option value="3004">Ranger</option>
              <option value="3005">Shadow Knight</option>
              <option value="3006">Druid</option>
              <option value="3007">Monk</option>
              <option value="3008">Bard</option>
              <option value="3009">Rogue</option>
              <option value="3010">Shaman</option>
              <option value="3011">Necromancer</option>
              <option value="3012">Wizard</option>
              <option value="3013">Magician</option>
              <option value="3014">Enchanter</option>
              <option value="3015">Beastlord</option>
              <option value="3016">Berserker</option>
            </select>
          </td>
          <td style="vertical-align: top;">
            <div id="spell_popup_overlay"></div>
            <div id="spell_popup">
              <div class="table_header">
                <span class="popup-title">Search Spell</span>
                <button id="close_popup" type="button" class="popup-close">Close</button>
              </div>
              <div class="table_content">
                <input type="text" id="spell_search" placeholder="Enter spell name">
                <button id="spell_search_btn" type="button">Search</button>
                <table class="table_content2" id="spell_results">
                  <thead>
                    <tr>
                      <th>Spell ID</th>
                      <th>Spell Name</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <!-- Results will be populated via AJAX -->
                  </tbody>
                </table>
              </div>
            </div>
          </td>
        </tr>
      </table>
    </form>
    <div id="spells-table" style="display: none; margin-top: 30px; overflow-x: auto;">
      <div style="min-width: 1100px;">
        <table class="table_content2" id="bot_spells_table" style="width: 100%; font-size: 13px;">
          <thead>
            <tr>
              <th style="width:40px;"></th>
              <th style="width:32px;">ID</th>
              <th style="width:60px;">NPC Spells ID</th>
              <th style="min-width:120px;max-width:180px;">Spell Name</th>
              <th style="width:36px;">Type</th>
              <th style="width:44px;">Min Lvl</th>
              <th style="width:44px;">Max Lvl</th>
              <th style="width:54px;">Mana</th>
              <th style="width:60px;">Recast</th>
              <th style="width:44px;">Prio</th>
              <th style="width:60px;">Resist</th>
              <th style="width:44px;">Min HP</th>
              <th style="width:44px;">Max HP</th>
              <th style="width:70px;">Bucket</th>
              <th style="width:54px;">B. Val</th>
              <th style="width:80px;">B. Comp</th>
              <th style="width:60px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <!-- Rows will be populated via AJAX -->
          </tbody>
        </table>
      </div>
    </div>
    <div id="entry-error" style="display:none;color:#b00;background:#fff3f3;border:1px solid #e0b4b4;padding:6px 12px;margin-bottom:10px;border-radius:5px;text-align:center;font-size:13px;width:100%;max-width:1100px;box-sizing:border-box;"></div>
    <div id="no-entries-popup" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.18);z-index:2000;align-items:center;justify-content:center;">
      <div style="background:#fff;border-radius:10px;box-shadow:0 8px 32px rgba(0,0,0,0.18);padding:32px 48px 28px 48px;min-width:320px;max-width:90vw;text-align:center;font-size:16px;color:#444;border:1px solid #e0e0e0;">
        <span style="font-size:20px;display:block;margin-bottom:10px;">No spell entries found for this class.</span>
        <button id="close-no-entries" style="margin-top:18px;padding:6px 22px;font-size:14px;border-radius:6px;border:1px solid #bbb;background:#eaeaea;cursor:pointer;">OK</button>
      </div>
    </div>
    <!-- Modal popup for empty field validation -->
    <div id="field-empty-modal" style="display:none;position:fixed;z-index:3000;left:0;top:0;width:100vw;height:100vh;background:rgba(0,0,0,0.10);align-items:center;justify-content:center;">
      <div style="background:#fff;border-radius:12px;box-shadow:0 4px 24px rgba(0,0,0,0.13);padding:22px 36px 18px 36px;min-width:220px;max-width:90vw;text-align:center;font-size:15px;color:#b00;border:1px solid #e0b4b4;display:inline-block;position:relative;">
        <span style="font-size:17px;display:block;margin-bottom:8px;font-weight:500;">Field cannot be empty</span>
        <button id="close-field-empty-modal" style="margin-top:10px;padding:5px 18px;font-size:13px;border-radius:5px;border:1px solid #bbb;background:#f5f5f5;color:#333;cursor:pointer;transition:background 0.2s;">OK</button>
        <span style="position:absolute;top:7px;right:10px;font-size:18px;color:#bbb;cursor:pointer;" id="field-empty-x">&times;</span>
      </div>
    </div>
  </div>
</div>
<script>
$(document).ready(function() {
  // Fetch and display bot_spells_entries
  function fetchEntries() {
    $.ajax({
      url: 'ajax/spells/bot_spells_entries.php?action=fetch',
      method: 'GET',
      success: function(data) {
        $('#bot_spells_table tbody').html(data);
      }
    });
  }

  fetchEntries();

  // Add new entry
  $('#add_entry').click(function() {
    $.ajax({
      url: 'ajax/spells/bot_spells_entries.php?action=add',
      method: 'POST',
      data: { maxhp: 0 }, // Ensure maxhp defaults to 0
      success: function() {
        if ($('#class-select').val()) $('#class-select').trigger('change');
      }
    });
  });

  // Remove bucket_name and bucket_value columns from the table header
  $('#bot_spells_table thead tr th:contains("Bucket Name"), #bot_spells_table thead tr th:contains("Bucket Value")').remove();

  // Ensure bucket_name and bucket_value fields are editable
  $(document).on('change', '.editable', function() {
    const id = $(this).data('id');
    const column = $(this).data('column');
    let value = $(this).val();

    // Set default value for maxhp to 0 if empty
    if (column === 'maxhp' && value.trim() === '') {
        value = '0';
    }

    // If text input and empty, show error and prevent update
    if ($(this).is('input[type="text"]') && value.trim() === '') {
      showFieldEmptyModal();
      $(this).val($(this).attr('value'));
      setTimeout(function() { hideFieldEmptyModal(); }, 2000);
      return;
    }
    hideFieldEmptyModal();
    $.ajax({
      url: 'ajax/spells/bot_spells_entries.php?action=update',
      method: 'POST',
      data: { id, column, value },
      success: function() {
        if ($('#class-select').val()) $('#class-select').trigger('change');
      }
    });
  });

  // Delete entry
  $(document).on('click', '.delete', function() {
    const id = $(this).data('id');
    $.ajax({
      url: 'ajax/spells/bot_spells_entries.php?action=delete',
      method: 'POST',
      data: { id },
      success: function() {
        if ($('#class-select').val()) $('#class-select').trigger('change');
      }
    });
  });

  // Search and add spell
  $('#search_spell').click(function() {
    $('#spell_popup_overlay').show();
    $('#spell_popup').show();
  });
  $('#close_popup').click(function() {
    $('#spell_popup').hide();
    $('#spell_popup_overlay').hide();
  });
  $('#spell_popup_overlay').click(function() {
    $('#spell_popup').hide();
    $('#spell_popup_overlay').hide();
  });
  // Pressing Enter in spell search triggers search, not close
  $('#spell_search').on('keydown', function(e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      $('#spell_search_btn').click();
    }
  });
  $('#spell_search_btn').click(function() {
    const searchTerm = $('#spell_search').val();
    $.ajax({
      url: 'ajax/spells/spell_search.php',
      method: 'GET',
      data: { search: searchTerm },
      success: function(data) {
        $('#spell_results tbody').html(data);
      }
    });
  });
  // Fix spell search result button rendering
  // In spell_search.php, change button to:
  // echo "<td><button class='add_spell' data-spell-id='{$row['id']}'>Add</button></td>";
  $(document).off('click', '.add_spell');
  $(document).on('click', '.add_spell', function(e) {
    e.preventDefault();
    var spellId = $(this).data('spell-id');
    var npcSpellsId = $('#class-select').val();
    if (!npcSpellsId || !spellId) return;
    $.post('ajax/spells/bot_spells_entries.php', {
      action: 'add_spell',
      npc_spells_id: npcSpellsId,
      spell_id: spellId
    }, function(resp) {
      if (resp.trim() === 'success') {
        $('#spell_popup').hide();
        $('#spell_popup_overlay').hide();
        // Reset search input and results
        $('#spell_search').val('');
        $('#spell_results tbody').empty();
        // Reload table for the selected class
        $.get('ajax/spells/fetch_spell_entries.php', { npc_spells_id: npcSpellsId }, function(data) {
          $('#bot_spells_table tbody').html(data);
        });
      } else {
        alert('Failed to add spell.');
      }
    });
  });
  // Enable or disable the 'Search and Add Spell' button based on class selection and entries visibility
  function updateSearchSpellButton() {
    const tableVisible = $('#spells-table').is(':visible');
    const hasRows = $('#bot_spells_table tbody tr').length > 0;
    if (tableVisible && hasRows) {
      $('#search_spell').prop('disabled', false).removeClass('disabled');
    } else {
      $('#search_spell').prop('disabled', true).addClass('disabled');
    }
  }
  // Initial state
  updateSearchSpellButton();
  // Update on class change and after AJAX loads
  $('#class-select').change(function() {
    const npc_spells_id = $(this).val();
    if (npc_spells_id) {
      $.ajax({
      url: 'ajax/spells/fetch_spell_entries.php',
        method: 'GET',
        data: { npc_spells_id },
        success: function(data) {
          if (data.trim() === 'NO_ENTRIES_FOUND') {
            $('#bot_spells_table tbody').empty();
            showNoEntriesPopup();
          } else {
            $('#bot_spells_table tbody').html(data);
            hideNoEntriesPopup();
          }
          $('#spells-table').show();
          updateSearchSpellButton();
        },
        error: function(xhr, status, error) {
          $('#spells-table').hide();
          hideNoEntriesPopup();
          updateSearchSpellButton();
        }
      });
    } else {
      $('#spells-table').hide();
      hideNoEntriesPopup();
      updateSearchSpellButton();
    }
  });
  // Show no entries popup
  function showNoEntriesPopup() {
    $('#no-entries-popup').fadeIn(200);
  }
  // Hide no entries popup
  function hideNoEntriesPopup() {
    $('#no-entries-popup').fadeOut(200);
  }
  // Hide popup on OK click
  $('#no-entries-popup').on('click', '#close-no-entries', function() {
    hideNoEntriesPopup();
  });
  // Hide popup on class change
  $('#class-select').on('change', function() {
    hideNoEntriesPopup();
  });
  // Also update after add/delete
  $(document).on('ajaxComplete', function(e, xhr, settings) {
    if (settings.url && settings.url.indexOf('bot_spells_entries.php') !== -1) {
      updateSearchSpellButton();
    }
  });
  function showFieldEmptyModal() {
    $('#field-empty-modal').css('display','flex').hide().fadeIn(120);
  }
  function hideFieldEmptyModal() {
    $('#field-empty-modal').fadeOut(120);
  }
  $('#close-field-empty-modal, #field-empty-x').on('click', function() {
    hideFieldEmptyModal();
  });
  // Intercept save/update actions and validate fields
  $(document).off('blur change', '.editable');
  $(document).on('blur change', '.editable', function(e) {
    var $input = $(this);
    var column = $input.data('column');

    // Only validate non-bucket_name and non-bucket_value fields
    if (column !== 'bucket_name' && column !== 'bucket_value' && $input.val().trim() === '') {
      showFieldEmptyModal();
      $input.val($input.data('original') || '');
      $input.focus();
      e.preventDefault();
      return false;
    }
  });

  $(document).off('keydown', '.editable');
  $(document).on('keydown', '.editable', function(e) {
    var $input = $(this);
    var column = $input.data('column');

    // Only validate non-bucket_name and non-bucket_value fields
    if (column !== 'bucket_name' && column !== 'bucket_value' && e.key === 'Enter' && $input.val().trim() === '') {
      showFieldEmptyModal();
      $input.val($input.data('original') || '');
      $input.focus();
      e.preventDefault();
      return false;
    }
  });
  // Prevent form submission on Enter in spell search input
  $('#spell_search').closest('form').on('submit', function(e) {
    e.preventDefault();
    return false;
  });
});
</script>
</body>
</html>

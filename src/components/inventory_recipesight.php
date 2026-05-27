<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../styles/inventory_recipesight.css">
  <title>Inventory</title>
</head>
<body>

  <div class="body">

    <!-- Search -->
    <div class="search-wrap">
      <input type="text" id="search" placeholder="Search ingredients…" oninput="filterTable()">
    </div>

    <!-- Toolbar -->
    <div class="toolbar">
      <span id="count"><?= count($inventory_list) ?> item<?= count($inventory_list) !== 1 ? 's' : '' ?></span>
      <button class="btn-add" onclick="openAdd()">+ Add item</button>
    </div>

    <!-- Table -->
    <table id="inv-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Product Name</th>
          <th>Qty</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($inventory_list)): ?>
          <tr><td colspan="4">No items yet.</td></tr>
        <?php else: ?>
          <?php foreach ($inventory_list as $row): ?>
          <tr>
            <td><?= $row['inventory_id'] ?></td>
            <td><?= htmlspecialchars($row['ingredient_name']) ?></td>
            <td><?= $row['quantity'] ?> <?= htmlspecialchars($row['unit_name']) ?></td>
            <td>
              <button class="btn-edit" onclick="openEdit(
                <?= $row['inventory_id'] ?>,
                <?= $row['ingredient_id'] ?>,
                <?= $row['quantity'] ?>,
                <?= $row['unit_id'] ?>
              )">&#9998; Edit</button>
              <button class="btn-del" onclick="confirmDelete(
                <?= $row['inventory_id'] ?>,
                '<?= htmlspecialchars($row['ingredient_name'], ENT_QUOTES) ?>'
              )">&#128465; Del</button>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>

  </div>

  <!-- Add / Edit Modal -->
  <div class="modal-bg" id="modal-bg">
    <div class="modal">
      <h3 id="modal-title">Add item</h3>
      <form id="inv-form" method="POST" action="../database/add_inventory.php">
        <input type="hidden" name="inventory_id" id="f-id">
        <input type="hidden" name="user_id" value="<?= $user_id ?>">

        <div class="field">
          <label>Ingredient</label>
          <select name="ingredient_id" id="f-ingredient">
            <?php foreach ($ingredient_list as $ing): ?>
              <option value="<?= $ing['ingredient_id'] ?>">
                <?= htmlspecialchars($ing['ingredient_name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="field">
          <label>Quantity</label>
          <input type="number" name="quantity" id="f-qty" step="0.01" min="0" required placeholder="e.g. 2.5">
        </div>

        <div class="field">
          <label>Unit</label>
          <select name="unit_id" id="f-unit">
            <?php foreach ($unit_list as $u): ?>
              <option value="<?= $u['unit_id'] ?>">
                <?= htmlspecialchars($u['unit_name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="modal-btns">
          <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
          <button type="submit" class="btn-save">Save</button>
        </div>
      </form>
    </div>
  </div>

  <script src="../scripts/inventory_recipesight.js"></script>

</body>
</html>
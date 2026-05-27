function filterTable() {
  const q = document.getElementById('search').value.toLowerCase();
  const rows = document.querySelectorAll('#inv-table tbody tr');
  let count = 0;
  rows.forEach(r => {
    const match = r.innerText.toLowerCase().includes(q);
    r.style.display = match ? '' : 'none';
    if (match) count++;
  });
  document.getElementById('count').textContent =
    count + ' item' + (count !== 1 ? 's' : '');
}

function openAdd() {
  document.getElementById('modal-title').textContent = 'Add item';
  document.getElementById('inv-form').action = '../database/add_inventory.php';
  document.getElementById('f-id').value = '';
  document.getElementById('f-qty').value = '';
  document.getElementById('f-ingredient').selectedIndex = 0;
  document.getElementById('f-unit').selectedIndex = 0;
  document.getElementById('modal-bg').classList.add('open');
}

function openEdit(id, ingredient_id, qty, unit_id) {
  document.getElementById('modal-title').textContent = 'Edit item';
  document.getElementById('inv-form').action = '../database/edit_inventory.php';
  document.getElementById('f-id').value = id;
  document.getElementById('f-ingredient').value = ingredient_id;
  document.getElementById('f-qty').value = qty;
  document.getElementById('f-unit').value = unit_id;
  document.getElementById('modal-bg').classList.add('open');
}

function closeModal() {
  document.getElementById('modal-bg').classList.remove('open');
}

function confirmDelete(id, name) {
  if (confirm('Delete "' + name + '" from inventory?')) {
    window.location = '../database/delete_inventory.php?id=' + id;
  }
}

// Close modal when clicking outside
document.getElementById('modal-bg').addEventListener('click', function(e) {
  if (e.target === this) closeModal();
});
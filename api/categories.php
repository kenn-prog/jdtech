<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';

header('Content-Type: application/json');

// Only admins can manage categories
if (!isLoggedIn() || !isAdmin()) {
  http_response_code(403);
  echo json_encode(['ok' => false, 'error' => 'Unauthorized']);
  exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? null;

switch ($action) {
  case 'get_all':
    getAll();
    break;
  case 'add':
    add();
    break;
  case 'update':
    update();
    break;
  case 'delete':
    delete_category();
    break;
  default:
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid action']);
}

function getAll() {
  global $conn;
  
  $result = mysqli_query($conn, "SELECT id, name, slug, icon FROM categories ORDER BY id ASC");
  $categories = [];
  
  while ($row = mysqli_fetch_assoc($result)) {
    $categories[] = $row;
  }
  
  echo json_encode(['ok' => true, 'categories' => $categories]);
}

function add() {
  global $conn;
  
  $data = json_decode(file_get_contents('php://input'), true);
  
  $name = trim($data['name'] ?? '');
  $slug = trim($data['slug'] ?? '');
  $icon = trim($data['icon'] ?? '');
  
  if (!$name || !$slug || !$icon) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Missing required fields']);
    return;
  }
  
  // Check if slug already exists
  $check = mysqli_query($conn, "SELECT id FROM categories WHERE slug = '" . mysqli_real_escape_string($conn, $slug) . "'");
  if (mysqli_num_rows($check) > 0) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Slug already exists']);
    return;
  }
  
  $name_esc = mysqli_real_escape_string($conn, $name);
  $slug_esc = mysqli_real_escape_string($conn, $slug);
  $icon_esc = mysqli_real_escape_string($conn, $icon);
  
  $query = "INSERT INTO categories (name, slug, icon) VALUES ('$name_esc', '$slug_esc', '$icon_esc')";
  
  if (mysqli_query($conn, $query)) {
    echo json_encode(['ok' => true, 'id' => mysqli_insert_id($conn)]);
  } else {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Database error: ' . mysqli_error($conn)]);
  }
}

function update() {
  global $conn;
  
  $data = json_decode(file_get_contents('php://input'), true);
  
  $id = intval($data['id'] ?? 0);
  $name = trim($data['name'] ?? '');
  $slug = trim($data['slug'] ?? '');
  $icon = trim($data['icon'] ?? '');
  
  if (!$id || !$name || !$slug || !$icon) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Missing required fields']);
    return;
  }
  
  // Check if slug already exists (excluding current category)
  $check = mysqli_query($conn, "SELECT id FROM categories WHERE slug = '" . mysqli_real_escape_string($conn, $slug) . "' AND id != $id");
  if (mysqli_num_rows($check) > 0) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Slug already exists']);
    return;
  }
  
  $name_esc = mysqli_real_escape_string($conn, $name);
  $slug_esc = mysqli_real_escape_string($conn, $slug);
  $icon_esc = mysqli_real_escape_string($conn, $icon);
  
  $query = "UPDATE categories SET name = '$name_esc', slug = '$slug_esc', icon = '$icon_esc' WHERE id = $id";
  
  if (mysqli_query($conn, $query)) {
    echo json_encode(['ok' => true]);
  } else {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Database error: ' . mysqli_error($conn)]);
  }
}

function delete_category() {
  global $conn;
  
  $data = json_decode(file_get_contents('php://input'), true);
  
  $id = intval($data['id'] ?? 0);
  
  if (!$id) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Missing category ID']);
    return;
  }
  
  $query = "DELETE FROM categories WHERE id = $id";
  
  if (mysqli_query($conn, $query)) {
    echo json_encode(['ok' => true]);
  } else {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Database error: ' . mysqli_error($conn)]);
  }
}
?>

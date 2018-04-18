<?php

/**
 * Get all the stuff we need for category page
 */
function categoryInit(&$con) {
    try {
        $categories = $con->query('select * from category order by category_desc');

        if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
            $id = $_GET['edit'];
            $stmt = $con->prepare('select * from category where category_id = ?');
            $stmt->bind_param('i', $id);

            if (!$stmt->execute()) {
                throw new \Exception($stmt->error);
            }

            $result = $stmt->get_result();
            $editCat = $result->fetch_object();
        }

        return [
            $categories,
            isset($editCat) ? $editCat->category_desc : null,
            isset($editCat) ? $editCat->category_id : null,
        ];
    } catch (\Throwable $e) {
        echo "<p>Error: {$e->getMessage()}</p>";
    }
}


/**
 * handle category form submit
 */
function handleSubmit(&$con) {
    try {
        $id = $_POST['catid'];
        $name = $_POST['catname'];

        if (empty($name)) {
            throw new \Exception('Category name cannot be empty');
        }

        if (!empty($id) && is_numeric($id)) {
            $q = 'UPDATE category SET category_desc = ? WHERE category_id = ?';
            $stmt = $con->prepare($q);
            $stmt->bind_param('si', $name, $id);
        } else {
            $q = 'INSERT category (category_desc) VALUES (?)';
            $stmt = $con->prepare($q);
            $stmt->bind_param('s', $name);
        }

        if (!$stmt->execute()) {
            throw new \Exception($stmt->error);
        }

        header('Location: categories.php');
    } catch (\Throwable $e) {
        echo "<p>Error: {$e->getMessage()}</p>";
    }
}
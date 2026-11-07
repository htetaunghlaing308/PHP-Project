<?php

require_once "auth.php";
require_once "database.php";

$page_title = "Materials & Prices";

$message = "";
$message_type = "";

/* DELETE */
if (isset($_GET['delete'])) {

    $id = (int) $_GET['delete'];

    $st = $conn->prepare("DELETE FROM materials WHERE id = ?");
    $st->bind_param("i", $id);
    $st->execute();

    header("Location: materials.php");
    exit;
}


/* ADD / UPDATE */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = (int) ($_POST['id'] ?? 0);

    $name  = trim($_POST['name'] ?? '');
    $price = (float) ($_POST['price'] ?? 0);
    $unit  = trim($_POST['unit'] ?? 'sq.ft');

    if ($name === '') {

        $message = "Material name is required.";
        $message_type = "error";

    } else {

        /*
         * Check duplicate material name
         *
         * When editing:
         * ignore the current material itself.
         */
        if ($id > 0) {

            $check = $conn->prepare(
                "SELECT id FROM materials WHERE name = ? AND id != ? LIMIT 1"
            );

            $check->bind_param("si", $name, $id);
            $check->execute();

            $result = $check->get_result();

            if ($result->num_rows > 0) {

                $message = "Material '$name' already exists. Please use another name.";
                $message_type = "error";

            } else {

                $st = $conn->prepare(
                    "UPDATE materials 
                     SET name = ?, price = ?, unit = ?
                     WHERE id = ?"
                );

                $st->bind_param(
                    "sdsi",
                    $name,
                    $price,
                    $unit,
                    $id
                );

                $st->execute();

                header("Location: materials.php");
                exit;
            }

        } else {

            /*
             * Check if material already exists before INSERT
             */
            $check = $conn->prepare(
                "SELECT id FROM materials WHERE name = ? LIMIT 1"
            );

            $check->bind_param("s", $name);
            $check->execute();

            $result = $check->get_result();

            if ($result->num_rows > 0) {

                $message = "Material '$name' already exists.";
                $message_type = "error";

            } else {

                $st = $conn->prepare(
                    "INSERT INTO materials (name, price, unit)
                     VALUES (?, ?, ?)"
                );

                $st->bind_param(
                    "sds",
                    $name,
                    $price,
                    $unit
                );

                $st->execute();

                header("Location: materials.php");
                exit;
            }
        }
    }
}


/* EDIT */
$edit = null;

if (isset($_GET['edit'])) {

    $id = (int) $_GET['edit'];

    $st = $conn->prepare(
        "SELECT * FROM materials WHERE id = ? LIMIT 1"
    );

    $st->bind_param("i", $id);
    $st->execute();

    $edit = $st->get_result()->fetch_assoc();
}


/* GET ALL MATERIALS */
$rows = $conn->query(
    "SELECT * FROM materials ORDER BY name"
);


include "header.php";
?>


<h1 class="page-title">Materials & Prices</h1>

<p class="muted">
    Change your selling prices without editing the code.
</p>


<?php if ($message): ?>

    <div class="card" style="margin-bottom: 20px;">

        <p style="
            margin: 0;
            font-weight: 600;
            color: <?= $message_type === 'error' ? '#dc2626' : '#16a34a' ?>;
        ">
            <?= htmlspecialchars($message) ?>
        </p>

    </div>

<?php endif; ?>


<div class="grid2">


    <!-- ADD / EDIT MATERIAL -->

    <div class="card">

        <h2>
            <?= $edit ? 'Edit Material' : 'Add Material' ?>
        </h2>


        <form method="post">

            <input
                type="hidden"
                name="id"
                value="<?= $edit['id'] ?? 0 ?>"
            >


            <div class="field">

                <label>Material Name</label>

                <input
                    type="text"
                    name="name"
                    required
                    value="<?= htmlspecialchars($edit['name'] ?? '') ?>"
                >

            </div>


            <div class="field">

                <label>Price</label>

                <input
                    type="number"
                    name="price"
                    min="0"
                    step="0.01"
                    required
                    value="<?= htmlspecialchars($edit['price'] ?? 0) ?>"
                >

            </div>


            <div class="field">

                <label>Unit</label>

                <input
                    type="text"
                    name="unit"
                    value="<?= htmlspecialchars($edit['unit'] ?? 'sq.ft') ?>"
                >

            </div>


            <button
                type="submit"
                class="btn primary"
            >
                <?= $edit ? 'Update' : 'Save' ?>
            </button>


            <?php if ($edit): ?>

                <a
                    href="materials.php"
                    class="btn light"
                >
                    Cancel
                </a>

            <?php endif; ?>

        </form>

    </div>



    <!-- PRICE LIST -->

    <div class="card">

        <h2>Price List</h2>


        <table class="table">

            <tr>
                <th>Material</th>
                <th>Price</th>
                <th>Unit</th>
                <th></th>
            </tr>


            <?php while ($r = $rows->fetch_assoc()): ?>

                <tr>

                    <td>
                        <?= htmlspecialchars($r['name']) ?>
                    </td>


                    <td>
                        <?= number_format((float)$r['price']) ?>
                    </td>


                    <td>
                        <?= htmlspecialchars($r['unit']) ?>
                    </td>


                    <td>

                        <a
                            class="btn light"
                            href="materials.php?edit=<?= $r['id'] ?>"
                        >
                            Edit
                        </a>


                        <a
                            class="btn danger"
                            href="materials.php?delete=<?= $r['id'] ?>"
                            onclick="return confirm('Delete material?')"
                        >
                            Delete
                        </a>

                    </td>

                </tr>

            <?php endwhile; ?>

        </table>

    </div>

</div>


<?php include "footer.php"; ?>
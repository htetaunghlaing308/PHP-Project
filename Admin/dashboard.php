<?php

require_once "auth.php";
require_once "database.php";

$page_title = "Dashboard";


/* TOTAL QUOTATIONS + TOTAL VALUE */
$q = $conn->query("
    SELECT 
        COUNT(*) AS c,
        COALESCE(SUM(total), 0) AS total
    FROM quotations
")->fetch_assoc();


/* TODAY'S QUOTATIONS */
$today = $conn->query("
    SELECT COUNT(*) AS c
    FROM quotations
    WHERE DATE(created_at) = CURDATE()
")->fetch_assoc()['c'];


/* TOTAL CUSTOMERS */
$customers = $conn->query("
    SELECT COUNT(*) AS c
    FROM customers
")->fetch_assoc()['c'];


/* RECENT QUOTATIONS */
$recent = $conn->query("
    SELECT 
        q.*,
        c.name AS customer
    FROM quotations q
    LEFT JOIN customers c 
        ON q.customer_id = c.id
    ORDER BY q.id DESC
    LIMIT 8
");


include "header.php";
?>


<h1 class="page-title">Dashboard</h1>

<p class="muted">
    Manage your signage quotation business.
</p>


<!-- STAT CARDS -->

<div class="cards">

    <div class="stat">
        <small>Total Quotations</small>
        <strong>
            <?= $q['c'] ?>
        </strong>
    </div>


    <div class="stat">
        <small>Quotation Value</small>
        <strong>
            <?= number_format($q['total']) ?> MMK
        </strong>
    </div>


    <div class="stat">
        <small>Today's Quotations</small>
        <strong>
            <?= $today ?>
        </strong>
    </div>


    <div class="stat">
        <small>Customers</small>
        <strong>
            <?= $customers ?>
        </strong>
    </div>

</div>



<!-- RECENT QUOTATIONS -->

<div class="card">

    <div style="
        display:flex;
        justify-content:space-between;
        align-items:center;
    ">

        <h2>Recent Quotations</h2>

        <a
            class="btn primary"
            href="quotation_create.php"
        >
            + New Quotation
        </a>

    </div>


    <div class="table-wrap">

        <table class="table">

            <tr>
                <th>No.</th>
                <th>Customer</th>
                <th>Project</th>
                <th>Date</th>
                <th class="right">Total</th>
                <th></th>
            </tr>


            <?php while ($r = $recent->fetch_assoc()): ?>

                <tr>

                    <td>
                        <?= htmlspecialchars($r['quotation_no']) ?>
                    </td>


                    <td>
                        <?= htmlspecialchars($r['customer'] ?? 'Walk-in') ?>
                    </td>


                    <td>
                        <?= htmlspecialchars($r['project']) ?>
                    </td>


                    <td>
                        <?= date(
                            'd M Y',
                            strtotime($r['created_at'])
                        ) ?>
                    </td>


                    <td class="right">
                        <?= number_format($r['total']) ?> MMK
                    </td>


                    <td>

                        <!-- FIXED PATH -->
                        <a
                            class="btn light"
                            href="quotation_view.php?id=<?= $r['id'] ?>"
                        >
                            View
                        </a>

                    </td>

                </tr>

            <?php endwhile; ?>

        </table>

    </div>

</div>


<?php include "footer.php"; ?>
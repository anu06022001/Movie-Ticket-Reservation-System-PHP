
<?php
include '../database/db_connection.php';

// Fetch ticket data grouped by month
$query = "SELECT MONTH(t.date) AS month, COUNT(*) AS total_tickets, SUM(t.total_amount) AS total_revenue
          FROM ticket t
          GROUP BY MONTH(t.date)
          ORDER BY MONTH(t.date)";
$tickets = $conn->query($query);

$months = [
    "01" => "January", "02" => "February", "03" => "March", "04" => "April", "05" => "May", "06" => "June", 
    "07" => "July", "08" => "August", "09" => "September", "10" => "October", "11" => "November", "12" => "December"
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Reports</title>
    <style>

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        table, th, td {
            border: 1px solid #ddd;
            padding: 10px;
        }

        th {
            background-color:#59e681;
            color: white;
        }

        td {
            background-color: #f9f9f9;
        }

        tr:nth-child(even) td {
            background-color: #e9ecef;
        }

    </style>
</head>
<body>
    <h1>Ticket Reports</h1>
    <table>
        <thead>
            <tr>
                <th>Month</th>
                <th>Total Tickets</th>
                <th>Total Revenue (₹)</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $tickets->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $months[sprintf('%02d', $row['month'])]; ?></td>
                <td><?php echo $row['total_tickets']; ?></td>
                <td><?php echo '₹' . number_format($row['total_revenue'], 2); ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
   
    <?php
        echo "<a href='admin_dashboard.php'>Go to Admin dashboard</a>   "; 
        echo "<button onclick='printTicket()'>🖨️ Print Ticket</button>";
        echo "</div></center>";

        // JavaScript to print the ticket
        echo "<script>
            function printTicket() {
                window.print();
            }
        </script>";
        ?>
</body>
</html>


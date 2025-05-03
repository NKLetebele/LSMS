<?php include('../includes/header.php')?>

<?php
// Check if the user is logged in
if (!isset($_SESSION['slogin']) || !isset($_SESSION['srole'])) {
    header('Location: ../index.php');
    exit();
}

// Check if the user has the role of Manager or Admin
$userRole = $_SESSION['srole'];
if ($userRole !== 'Manager' && $userRole !== 'Admin') {
    header('Location: ../index.php');
    exit();
}

$stmt = $conn->prepare("SELECT COUNT(*) as total_leave FROM tblleave");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$total_leave = $row['total_leave'];

// Fetch the count of pending leaves
$stmt = $conn->prepare("SELECT COUNT(*) as pending_leave FROM tblleave WHERE leave_status = 0");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$pending_leave = $row['pending_leave'];

// Fetch the count of approved leaves
$stmt = $conn->prepare("SELECT COUNT(*) as approved_leave FROM tblleave WHERE leave_status = 1");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$approved_leave = $row['approved_leave'];

// Fetch the count of recalled leaves
$stmt = $conn->prepare("SELECT COUNT(*) as recalled_leave FROM tblleave WHERE leave_status = 3");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$recalled_leave = $row['recalled_leave'];

// Fetch the count of canceled leaves
$stmt = $conn->prepare("SELECT COUNT(*) as rejected_leave FROM tblleave WHERE leave_status = 4");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$rejected_leave = $row['rejected_leave'];

// Calculate the percentages
$pending_percentage = ($total_leave > 0) ? floor(($pending_leave / $total_leave) * 100) : 0;
$approved_percentage = ($total_leave > 0) ? floor(($approved_leave / $total_leave) * 100) : 0;
$recalled_percentage = ($total_leave > 0) ? floor(($recalled_leave / $total_leave) * 100) : 0;
$rejected_percentage = ($total_leave > 0) ? floor(($rejected_leave / $total_leave) * 100) : 0;
?>

<?php
$totalStaff = 0;

// Assuming you have a database connection, fetch all departments
$departmentQuery = $conn->prepare("SELECT * FROM tbldepartments");
$departmentQuery->execute();
$departmentResult = $departmentQuery->get_result();

$departments = [];

while ($departmentRow = $departmentResult->fetch_assoc()) {
    $departmentId = $departmentRow['id'];
    $departmentName = $departmentRow['department_name'];
    $departmentDesc = $departmentRow['department_desc'];

    // Fetch the count of staff in the department
    $staffQuery = $conn->prepare("SELECT COUNT(*) as staff_count FROM tblemployees WHERE department = ?");
    $staffQuery->bind_param("i", $departmentId);
    $staffQuery->execute();
    $staffResult = $staffQuery->get_result();
    $staffRow = $staffResult->fetch_assoc();
    $staffCount = $staffRow['staff_count'];

    $totalStaff += $staffCount;

    // Fetch the count of managers in the department
    $managerQuery = $conn->prepare("SELECT COUNT(*) as manager_count FROM tblemployees WHERE department = ? AND role = 'Manager'");
    $managerQuery->bind_param("i", $departmentId);
    $managerQuery->execute();
    $managerResult = $managerQuery->get_result();
    $managerRow = $managerResult->fetch_assoc();
    $managerCount = $managerRow['manager_count'];

    $departments[] = [
        'id' => $departmentId,
        'name' => $departmentName,
        'desc' => $departmentDesc,
        'staffCount' => $staffCount,
        'managerCount' => $managerCount,
    ];
}
?>


<body>
    <!-- Pre-loader start -->
    <?php include('../includes/loader.php')?>
    <!-- Pre-loader end -->
    <div id="pcoded" class="pcoded">
        <div class="pcoded-overlay-box"></div>
        <div class="pcoded-container navbar-wrapper">

           <?php include('../includes/topbar.php')?>

            <div class="pcoded-main-container">
                <div class="pcoded-wrapper">
                    <?php $page_name = "dashboard"; ?>
                    <?php include('../includes/sidebar.php')?>
                    <div class="pcoded-content">
                        <div class="pcoded-inner-content">
                            <!-- Main-body start -->
                            <div class="main-body">
                                <div class="page-wrapper">
                                    <!-- Page-body start -->
                                    <div class="page-body">
                                        <div class="row">
                                            <!-- user card  start -->
                                            <div class="col-md-6 col-xl-3">
                                                <div class="card widget-card-1">
                                                    <?php
                                                        $stmt = $conn->prepare("SELECT COUNT(*) as total_employee FROM tblemployees");
                                                        $stmt->execute();
                                                        $result = $stmt->get_result();
                                                        $row = $result->fetch_assoc();
                                                        $total_employee = $row['total_employee'];    
                                                    ?>
                                                    <div class="card-block-small">
                                                        <i class="feather icon-user bg-c-blue card1-icon"></i>
                                                        <span class="text-c-blue f-w-600">Active Staff</span>
                                                        <?php if ($total_employee == 0): ?>
                                                            <h4>No</h4>
                                                        <?php else: ?>
                                                            <h4><?= $total_employee ?></h4>
                                                        <?php endif; ?>
                                                        <div>
                                                            <span class="f-left m-t-10 text-muted">
                                                                <i class="text-c-blue f-16 feather icon-user m-r-10"></i>Registered Staff
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-xl-3">
                                                <div class="card widget-card-1">
                                                    <?php
                                                        $stmt = $conn->prepare("SELECT COUNT(*) as total_depart FROM tbldepartments");
                                                        $stmt->execute();
                                                        $result = $stmt->get_result();
                                                        $row = $result->fetch_assoc();
                                                        $total_depart = $row['total_depart'];    
                                                    ?>
                                                    <div class="card-block-small">
                                                        <i class="feather icon-home bg-c-pink card1-icon"></i>
                                                        <span class="text-c-pink f-w-600">Departments</span>
                                                        <?php if ($total_depart == 0): ?>
                                                            <h4>No</h4>
                                                        <?php else: ?>
                                                            <h4><?= $total_depart ?></h4>
                                                        <?php endif; ?>
                                                        <div>
                                                            <span class="f-left m-t-10 text-muted">
                                                                <i class="text-c-pink f-16 feather icon-home m-r-10"></i>Available Departments
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-xl-3">
                                                <div class="card widget-card-1">
                                                    <?php
                                                        $stmt = $conn->prepare("SELECT COUNT(*) as total_types FROM tblleavetype");
                                                        $stmt->execute();
                                                        $result = $stmt->get_result();
                                                        $row = $result->fetch_assoc();
                                                        $total_types = $row['total_types'];    
                                                    ?>
                                                    <div class="card-block-small">
                                                        <i class="feather icon-tag bg-c-green card1-icon"></i>
                                                        <span class="text-c-green f-w-600">Leave Types</span>
                                                        <?php if ($total_types == 0): ?>
                                                            <h4>No</h4>
                                                        <?php else: ?>
                                                            <h4><?= $total_types ?></h4>
                                                        <?php endif; ?>
                                                        <div>
                                                            <span class="f-left m-t-10 text-muted">
                                                                <i class="text-c-green f-16 feather icon-tag m-r-10"></i>Active Leave Types
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-xl-3">
                                                <div class="card widget-card-1">
                                                    <?php
                                                        $stmt = $conn->prepare("SELECT COUNT(*) as total_leave FROM tblleave");
                                                        $stmt->execute();
                                                        $result = $stmt->get_result();
                                                        $row = $result->fetch_assoc();
                                                        $total_leave = $row['total_leave'];    
                                                    ?>
                                                    <div class="card-block-small">
                                                        <i class="feather icon-list bg-c-yellow card1-icon"></i>
                                                        <span class="text-c-yellow f-w-600">Leave</span>
                                                        <?php if ($total_leave == 0): ?>
                                                            <h4>No</h4>
                                                        <?php else: ?>
                                                            <h4><?= $total_leave ?></h4>
                                                        <?php endif; ?>
                                                        <div>
                                                            <span class="f-left m-t-10 text-muted">
                                                                <i class="text-c-yellow f-16 feather icon-list m-r-10"></i>Leave Application
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- user card end -->

                                            <!-- Department table start -->
                                            <div class="col-sm-12">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h5>Departments Overview</h5>
                                                    </div>
                                                    <div class="card-block">
                                                        <div class="table-responsive dt-responsive">
                                                            <table id="department-table" class="table table-striped table-bordered nowrap">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Department Name</th>
                                                                        <th>Description</th>
                                                                        <th>Total Staff</th>
                                                                        <th>Total Managers</th>
                                                                        <th>Progress</th>
                                                                        <th>Actions</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php foreach ($departments as $department): ?>
                                                                    <tr>
                                                                        <td><?= htmlspecialchars($department['name']) ?></td>
                                                                        <td><?= htmlspecialchars($department['desc']) ?></td>
                                                                        <td><span class="badge badge-info"><?= $department['staffCount'] ?></span></td>
                                                                        <td><span class="badge badge-warning"><?= $department['managerCount'] ?></span></td>
                                                                        <td>
                                                                            <div class="progress">
                                                                                <?php
                                                                                $staffPercentage = $totalStaff > 0 ? round(($department['staffCount'] / $totalStaff) * 100) : 0;
                                                                                ?>
                                                                                <div class="progress-bar bg-c-blue" style="width:<?= $staffPercentage ?>%"><label><?= $staffPercentage ?>%</label></div>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <a href="staff_list.php?department=<?= urlencode($department['name']) ?>" class="btn btn-primary btn-mini waves-effect waves-light">View Staff</a>
                                                                        </td>
                                                                    </tr>
                                                                    <?php endforeach; ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Department table end -->
                                        </div>
                                    </div>
                                    <!-- Page-body end -->
                                    
                                </div>
                                <div id="styleSelector"> </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Required Jquery -->
    <?php include('../includes/scripts.php')?>
    <script>
        $(document).ready(function() {
            $('#department-table').DataTable({
                responsive: true,
                dom: '<"top"Bfl>rt<"bottom"ip>',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search departments...",
                    lengthMenu: "Show _MENU_ departments per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ departments",
                    infoEmpty: "No departments available",
                    infoFiltered: "(filtered from _MAX_ total departments)"
                },
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]]
            });
        });
        
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());
        gtag('config', 'UA-23581568-13');
    </script>
</body>

</html>
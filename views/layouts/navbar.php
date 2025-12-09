<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo SITE_URL; ?>/index.php">
            <i class="fas fa-university"></i> GWPC Employee Management
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo activeClass('index.php'); ?>" href="<?php echo SITE_URL; ?>/index.php">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>
                
                <?php if (isAdmin()): ?>
                    <!-- Admin Menu -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="employeesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-users"></i> Employees
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="employeesDropdown">
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/admin/employees.php"><i class="fas fa-list"></i> View All</a></li>
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/admin/employee_form.php"><i class="fas fa-plus"></i> Add New</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="supervisorsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-tie"></i> Supervisors
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="supervisorsDropdown">
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/admin/supervisors.php"><i class="fas fa-list"></i> View All</a></li>
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/admin/supervisor_form.php"><i class="fas fa-plus"></i> Add New</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="efficiencyDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-chart-line"></i> Efficiency Points
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="efficiencyDropdown">
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/admin/efficiency_points.php"><i class="fas fa-list"></i> View All</a></li>
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/admin/efficiency_point_form.php"><i class="fas fa-plus"></i> Add New</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="semestersDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-calendar"></i> Semesters
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="semestersDropdown">
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/admin/semesters.php"><i class="fas fa-list"></i> View All</a></li>
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/admin/semester_form.php"><i class="fas fa-plus"></i> Add New</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link <?php echo activeClass('admin/reports.php'); ?>" href="<?php echo SITE_URL; ?>/admin/reports.php">
                            <i class="fas fa-file-alt"></i> Reports
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php if (isSupervisor()): ?>
                    <!-- Supervisor Menu -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo activeClass('supervisor/my_employees.php'); ?>" href="<?php echo SITE_URL; ?>/supervisor/my_employees.php">
                            <i class="fas fa-users"></i> My Employees
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link <?php echo activeClass('supervisor/supervision.php'); ?>" href="<?php echo SITE_URL; ?>/supervisor/supervision.php">
                            <i class="fas fa-comments"></i> Supervision
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link <?php echo activeClass('supervisor/scoring.php'); ?>" href="<?php echo SITE_URL; ?>/supervisor/scoring.php">
                            <i class="fas fa-star"></i> Scoring
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link <?php echo activeClass('supervisor/reports.php'); ?>" href="<?php echo SITE_URL; ?>/supervisor/reports.php">
                            <i class="fas fa-file-alt"></i> Reports
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
            
            <!-- User Menu -->
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle"></i> <?php echo e(getCurrentUserName()); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/profile.php"><i class="fas fa-user"></i> Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

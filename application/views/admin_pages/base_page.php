<div class="container">
	<div class="row">
        <nav class="col-md-2 sidebar">

          <div class="sidebar-sticky">
            <ul class="nav flex-column">
              <li class="nav-item">
                <a class="nav-link <?php echo $admin_nav == 'dashboard' ? 'active':''; ?>" href="dashboard">
                  <i class="fas fa-tachometer-alt"></i>
                  Dashboard
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?php echo $admin_nav == 'explorer' ? 'active':''; ?>" href="explorer">
                  <i class="fa fa-binoculars"></i>
                  Explorer
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?php echo $admin_nav == 'stocks' ? 'active':''; ?>" href="stocks">
                  <i class="fa fa-chart-line"></i>
                  Stocks
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?php echo $admin_nav == 'prospecti' ? 'active':''; ?>" href="prospecti">
                  <i class="fa fa-comments-dollar"></i>
                  Prospecti
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?php echo $admin_nav == 'auto_updates' ? 'active':''; ?>" href="auto_updates">
                  <i class="fa fa-magic"></i>
                  Auto Updates
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?php echo $admin_nav == 'settings' ? 'active':''; ?>" href="settings">
                  <i class="fa fa-cogs"></i>
                  Settings
                </a>
              </li>
            </ul>
          </div>
    	</nav>

        <?php echo $pane; ?>
    </div>
</div>
<script>
	var activity = <?php echo json_encode($player_activity); ?>;
	var auto_updates_template = <?php echo json_encode($auto_updates_template); ?>;
</script>

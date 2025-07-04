      </div>
      <!-- END Page Content -->
    </main>
    <!-- END Main Container -->

    <!-- Footer -->
    <footer id="page-footer" class="bg-body-light">
      <div class="content py-3">
        <div class="row fs-sm">
          <div class="col-sm-6 order-sm-2 py-1 text-center text-sm-end">
            Crafted with <i class="fa fa-heart text-danger"></i> by <a class="fw-semibold" href="#" target="_blank">AK23STK</a>
          </div>
          <div class="col-sm-6 order-sm-1 py-1 text-center text-sm-start">
            <a class="fw-semibold" href="#" target="_blank">AK23STK</a> &copy; <span data-toggle="year-copy"><?php echo date('Y'); ?></span>
          </div>
        </div>
      </div>
    </footer>
    <!-- END Footer -->
  </div>
  <!-- END Page Container -->

  <!-- OneUI Core JS -->
  <script src="<?php echo ASSETS_URL; ?>js/oneui.app.min.js"></script>
  
  <!-- jQuery (required for Select2 + jQuery UI) -->
  <script src="<?php echo ASSETS_URL; ?>js/lib/jquery.min.js"></script>
  
  <!-- Page-specific JS -->
  <?php if (!empty($page_js)): ?>
    <?php foreach ((array)$page_js as $js): ?>
      <script src="<?php echo ASSETS_URL . 'js/' . $js; ?>"></script>
    <?php endforeach; ?>
  <?php endif; ?>
  
  <!-- Page-specific JS content -->
  <?php if (!empty($page_js_content)): ?>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        <?php echo $page_js_content; ?>
      });
    </script>
  <?php endif; ?>
  
  <!-- Custom JS -->
  <script src="<?php echo ASSETS_URL; ?>js/ak23-custom.js"></script>
  
  <!-- Page-specific footer content -->
  <?php if (!empty($page_footer_content)): ?>
    <?php echo $page_footer_content; ?>
  <?php endif; ?>
</body>
</html>
<?php
// End output buffering and flush the buffer
if (ob_get_level() > 0) {
    ob_end_flush();
}

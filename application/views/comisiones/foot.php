    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="<?=base_url()?>scripts/jquery.dataTables.min.js"></script>
    <script src="<?=base_url()?>scripts/bootstrap3.min.js"></script>
    <script src="<?=base_url()?>scripts/dataTables.bootstrap.js"></script>
    <script src="<?=base_url()?>scripts/moment.min.js"></script>
    <script src="<?=base_url()?>scripts/daterangepicker.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
    	$("#comisiones_table").dataTable({
            "language": {
               "url": "<?=base_url()?>scripts/ES_ar.txt"           
            },
            "order": [[ 0, "asc" ]],
            "paging": false
        });
    })
	</script>
</body>
</html>
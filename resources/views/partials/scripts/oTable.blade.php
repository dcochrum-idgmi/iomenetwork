<script type="text/javascript">
    var oTable;
    $(function () {
        var $table = $('#table'),
                cols = [];
        $('thead th[data-name]').each(function (i, e) {
            cols.push({'data': $(this).data('name')});
        });

        oTable = $table.dataTable({
            aoColumnDefs: [{
                bSortable: false,
                aTargets: ['no-sort']
            }],

            bProcessing: true,
            bServerSide: true,
            stateSave: true,
            ajax: $.fn.dataTable.pipeline(
                    {
                        url: '{{ $source }}',
                        pages: 5
                    }),
            columns: cols,
            fnDrawCallback: IOMEsetModals
        });
    });
</script>

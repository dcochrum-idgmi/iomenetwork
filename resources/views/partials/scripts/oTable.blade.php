<script type="text/javascript">
    var oTable;
    $(function () {
        var $table = $('#table'),
                cols = [];
        $('thead th[data-name]').each(function (i, e) {
            cols.push({'data': $(this).data('name')});
        });

        oTable = $table.dataTable({
            columnDefs: [{
                sortable: false,
                targets: 'no-sort'
            }, {
                searchable: false,
                targets: unsearchable($table)
            }],

            processing: true,
            serverSide: true,
            stateSave: true,
            ajax: $.fn.dataTable.pipeline(
                    {
                        url: '{{ $source }}',
                        pages: 5
                    }),
            columns: cols,
            fnDrawCallback: drewDataTable
        });
    });

    var unsearchable = function ($table) {
        var cols = [];
        $table.find('th').each(function (i, e) {
            if( ! $(this).is('.search') )
                cols.push(i);
        });

        return cols;
    }
</script>

<script type="text/javascript">
    var oTable;
    $( function() {
        oTable = $( '#table' ).dataTable( {
            aoColumnDefs: [ {
                bSortable: false,
                aTargets: [ 'no-sort' ]
            } ],

            bProcessing: true,
            bServerSide: true,
            stateSave: true,
            ajax: $.fn.dataTable.pipeline(
                    {
                        url: '{{ $source }}',
                        pages: 5
                    } ),
            fnDrawCallback: IOMEsetModals
        } );
    } );
</script>

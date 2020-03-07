
$(document).ready( function () {
    $('#table_id').DataTable({
        ajax:{
            url: 'apiEndPoint.php?table=true',
            dataSrc: '',
            
        }
    });
} );
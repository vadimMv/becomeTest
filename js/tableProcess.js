
$(document).ready( function () {
    $('#table_id').DataTable({
        ajax:{
            url: 'apiEndPoint.php',
            dataSrc: '',
            
        }
    });
} );
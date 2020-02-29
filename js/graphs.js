$(document).ready( function () {
    
    let ctx = document.getElementById('cites').getContext('2d');
    AjaxCall(ctx);
  
} );


function AjaxCall(ctx){
    
    $.get("apiStatEndPoint.php", function(data, status){
               
     const result = data.map(obj => obj[0]);
           
      new Chart(ctx, {      
                type: 'bar',
                data: {
                labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
                datasets: [{
                            label: 'Cities ',
                            backgroundColor: 'rgb(255, 99, 132)',
                            borderColor: 'rgb(255, 99, 132)',
                            data: result}]
        },});
    
    });
}
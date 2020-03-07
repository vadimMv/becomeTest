$(document).ready( function () {
    
    let ctx = document.getElementById('cites').getContext('2d');
    let ctx2 = document.getElementById('ages').getContext('2d');
    AjaxCall(ctx,ctx2);
  
} );


function AjaxCall(ctx ,ctx2){
    
    $.get("apiEndPoint.php?graphs=true", function(data, status){
     const result = data[0].map(obj => obj[0]);
     const result1 = Object.keys(data[1]).map(obj => data[1][obj]);    
     console.log(result1);          

      new Chart(ctx, {      
                type: 'bar',
                data: {
                labels: ['Jerusalem', 'Tlv', 'Melburn', 'Zurix', 'Berlin', 'Paris', 'Roma'],
                datasets: [{
                            label: 'Cities ',
                            backgroundColor: 'rgb(255, 99, 132)',
                            borderColor: 'rgb(255, 99, 132)',
                            data: result}]
        },});


        new Chart(ctx2, {      
            type: 'line',
            data: {
            labels: ['0-20', '20-40', '40-70', '70-95'],
            datasets: [{
                        label: 'Ages',
                        backgroundColor: 'green',
                        borderColor: 'green',
                        data: result1}]
    },});
    
    });
}
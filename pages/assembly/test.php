<html>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<style>
table{
    margin: 10px;
}

td{
   padding: 5px;
   background: lightblue;
}

button{
    float:right;
}

</style>
</head>
<body>
<button>TEST SELECTOR!</button>

<table id="one">
    <tbody>
        <tr>
            <td>Stupid</td>
            <td>Flanders</td>
            <td></td>
        </tr>
        <tr>
            <td>Hi!</td>
            <td>There!</td>
            <td><input type="checkbox" class="checkbox"><label>Check ME!</label></td>
        </tr>
        <tr>
            <td>Ho!</td>
            <td>There!</td>
            <td><input type="checkbox" class="checkbox"><label>Check ME!</label></td>
        </tr>
        <tr>
            <td>Neighbor</td>
            <td>eno!</td>
            <td><input type="checkbox" class="checkbox"><label>Check ME!</label></td>
        </tr>
        <tr>
            <td>Okaly</td>
            <td>Dokaly</td>
            <td><input type="checkbox" class="checkbox"><label>Check ME!</label></td>
        </tr>      
     </tbody>
</table>

<table id="two">
    <tbody>
        <tr>
            <td>Stupid</td>
            <td>Flanders</td>
            <td></td>
        </tr>
    </tbody>
</table>

<script>
$('body').on('click','#one tbody tr td input.checkbox', function(){
    if( $(this).is(':checked')){
    var row = $(this).closest('tr').clone();
     $('#two tbody').append(row);
         $(this).closest('tr').remove();
    }
     
});
</script>
<script>
$('body').on('click','#two tbody tr td input.checkbox', function(){
    var row = $(this).closest('tr').clone();
    $('#one tbody').append(row);
    $(this).closest('tr').remove();
});
</script>
<script>
$('button').on('click', function(){
    $('*').removeAttr('style');
    $('#one tbody tr td input.checkbox:not(:checked)').parent().css('border','red 1px dashed');
});
</script>
</body>
</html>
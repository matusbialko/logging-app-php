
<?php 
    $greeting = 'Ahoj!';
    $currentDate = date('H:i:s d.m.Y');

    print "<h2> $greeting </h2>";
    echo lineBreakValue('Aktuálny čas:');
    echo lineBreakValue($currentDate);
    echo '<br>';
?>
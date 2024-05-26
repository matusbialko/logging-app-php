<?php
    $greeting = 'Ahoj!';
    $currentDate = date('H:i:s j.m.Y');
    $isLate = date('H') >= 8;
    $tooLateToLog = date('H') >= 20 && date('H') <= 24;

    function lineBreakValue($value) {
        return $value . '<br>';
    }
    
    function writeLog($currentDate, $isLate, $tooLateToLog) {
        if ($tooLateToLog) return die('<br> Daný príchod sa nemôže zapísať.');
        if ($isLate) return file_put_contents('log.txt', lineBreakValue($currentDate . ' - meškanie'), FILE_APPEND);
        file_put_contents('log.txt', lineBreakValue($currentDate), FILE_APPEND);
    }

    function getLogs() {
        echo ('<br>');
        echo lineBreakValue('Log history:');
        echo lineBreakValue(file_get_contents('log.txt'));
    }

    print '<h2>' . $greeting . '</h2>';
    echo lineBreakValue('Current date:');
    echo lineBreakValue($currentDate);
    writeLog($currentDate, $isLate, $tooLateToLog);
    getLogs();
?>
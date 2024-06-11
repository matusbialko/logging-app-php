
<html>
    <body>
        <?php include 'header.php' ?>

        <form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>">
            Meno študenta: <input type="text" name="nameInput">
            <input type="submit" value="Poslať">
        </form>

        <form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>">
            Správa: <input type="text" name="customMessage">
            <input type="submit" value="Poslať">
        </form>

        <?php
            $isLate = date('H') >= 8;
            $tooLateToLog = date('H') >= 20 && date('H') <= 24;
            $newEntry = new Entry();

            function lineBreakValue($value): string {
                return "$value <br>";
            }
            
            function writeTimeLog($currentDate): string {
                echo '<br>';

                if ($GLOBALS['tooLateToLog']) return die('<br> Daný príchod sa nemôže zapísať.');

                if ($GLOBALS['isLate']) return file_put_contents('timeLogs.txt', lineBreakValue("$currentDate - meškanie"), FILE_APPEND);

                return file_put_contents('timeLogs.txt', lineBreakValue($currentDate), FILE_APPEND);
            }

            function getLogs(): void {
                echo '<br>';
                echo lineBreakValue('Časové záznamy:');
                if (!file_exists('timeLogs.txt')) die('Neexistujú žiadne časové záznamy');
                echo lineBreakValue(file_get_contents('timeLogs.txt'));
            }

            class Student {
                static $totalStudentEntries = 0;
                static $studentsArray = [];

                public static function logStudent($name): void {
                    if (file_exists('studenti.json')) self::$studentsArray = json_decode(file_get_contents('studenti.json'));
                
                    if (file_exists('totalStudentEntriesNumber.json')) self::$totalStudentEntries = json_decode(file_get_contents('totalStudentEntriesNumber.json'));
                    
                    if ($name) {
                        self::$studentsArray[] = array('name'=>$name);
                        ++self::$totalStudentEntries;
                        file_put_contents('totalStudentEntriesNumber.json', json_encode(self::$totalStudentEntries));
                    }
    
                    $jsonStudentsArray = json_encode(self::$studentsArray);
                    file_put_contents('studenti.json', $jsonStudentsArray);
                }

                public static function showStudentsArray(): void {
                    if (file_exists('totalStudentEntriesNumber.json')) self::$totalStudentEntries = json_decode(file_get_contents('totalStudentEntriesNumber.json'));
                    print_r(lineBreakValue("Celkový počet príchodov študentov: " . self::$totalStudentEntries));  
                    echo '<br>';

                    if (file_exists('studenti.json')) self::$studentsArray = json_decode(file_get_contents('studenti.json'));

                    print lineBreakValue('<strong>Študenti:</strong>');
                    if (self::$studentsArray) print_r(self::$studentsArray);
                }
            }

            class Entry {
                private $entriesArray = [];
    
                private function isEntryLate($date): bool {
                    $hour = substr($date, 0, 2);
                    return $hour >= 8;
                }

                private function checkForLateEntries(): void {
                    for ($i = 0; $i < count($this->entriesArray)-1; $i++) {
                        $this->entriesArray[$i]->meškanie = $this->isEntryLate($this->entriesArray[$i]?->date) ? 'áno' : 'nie';
                    }
                }
 
                public function logEntry($currentDate): void {
                    if (file_exists('prichody.json')) $this->entriesArray = json_decode(file_get_contents('prichody.json'));

                    $this->entriesArray[] = array(
                        'date'=>$currentDate,
                        'meškanie'=>'nie'
                    );

                    $this->checkForLateEntries();

                    file_put_contents('prichody.json', json_encode($this->entriesArray));
                }

                public function showEntriesArray() {
                    if (file_exists('prichody.json')) $this->entriesArray = json_decode(file_get_contents('prichody.json'));
                    print lineBreakValue('<strong>Príchody:</strong>');
                    if ($this->entriesArray) print_r($this->entriesArray);
                }
            }

            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nameInput'])) {
                $name = $_POST['nameInput'];
                Student::logStudent($name);
            }
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['customMessage'])) {
                echo lineBreakValue($_POST['customMessage']);
            }
            if (isset($_GET['meno'])) {
                $name = $_GET['meno'];
                Student::logStudent($name);
            }

            Student::showStudentsArray();

            echo '<br><br>';
            $newEntry->logEntry($currentDate);
            $newEntry->showEntriesArray();

            writeTimeLog($currentDate);
            getLogs();
        ?>
    </body>
</html>
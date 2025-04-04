<?php

class BoyerMooreSearch
{
    /**
     * Implementation of the Boyer-Moore algorithm for string searching.
     *
     * @param string $text    The complete text to search within.
     * @param string $pattern The pattern to search for.
     * @return int|false      Index of the first occurrence of the pattern in the text, or false if not found.
     */
    public function boyerMooreSearch(string $text, string $pattern)
    {
        $n = strlen($text);
        $m = strlen($pattern);

        if ($m == 0) {
            return 0; // Empty pattern is found at the beginning
        }

        $badCharTable = $this->createBadCharacterTable($pattern);
        $goodSuffixTable = $this->createGoodSuffixTable($pattern);

        $i = 0; // Index for the text
        while ($i <= ($n - $m)) {
            $j = $m - 1; // Index for the pattern, start from the end

            // Compare the pattern with the text from right to left
            while ($j >= 0 && $pattern[$j] == $text[$i + $j]) {
                $j--;
            }

            // If the pattern matches completely
            if ($j < 0) {
                return $i; // Pattern found at index i
            } else {
                // 'Bad' character in the text that does not match the pattern
                $badChar = $text[$i + $j];

                // Calculate the shift using the heuristic tables
                $badCharShift = isset($badCharTable[$badChar]) ? max(1, $j - $badCharTable[$badChar]) : $j + 1;
                $goodSuffixShift = $goodSuffixTable[$j + 1]; // j+1 because index 0 in the table represents the full suffix

                $shift = max($badCharShift, $goodSuffixShift);
                $i += $shift;
            }
        }

        return false; // Pattern not found
    }


    /**
     * Creates the "bad character" table for the Boyer-Moore algorithm.
     *
     * @param string $pattern The search pattern.
     * @return array           Bad character table (character => last position in the pattern).
     */
    private function createBadCharacterTable(string $pattern): array
    {
        $table = [];
        $m = strlen($pattern);
        for ($i = 0; $i < $m; $i++) {
            $table[$pattern[$i]] = $i;
        }
        return $table;
    }

    /**
     * Creates the "good suffix" table for the Boyer-Moore algorithm.
     *
     * @param string $pattern The search pattern.
     * @return array           Good suffix table (suffix length => shift).
     */
    private function createGoodSuffixTable(string $pattern): array
    {
        $m = strlen($pattern);
        $table = array_fill(0, $m + 1, 0); // Initialize with zeros

        if ($m <= 1) {
            return $table; // No significant good suffixes for short patterns
        }

        $lastPrefixPosition = $this->getLastPrefixPosition($pattern);
        $borderLength = $this->getBorderLength($pattern);


        for ($i = 0; $i <= $m; ++$i) {
            $table[$i] = $m - $borderLength[$m];
        }
        for ($i = 0; $i < $m; ++$i) {
            $suffixLength = $m - 1 - $i;
            $position = $lastPrefixPosition[$i];
            $table[$suffixLength] = min($table[$suffixLength], $m - 1 - $position);
        }
        return $table;
    }


    /**
     * Helper function for good suffix table.
     */
    private function getLastPrefixPosition(string $pattern): array {
        $m = strlen($pattern);
        $lastPrefixPosition = array_fill(0, $m, 0);
        $borderLength = $this->getBorderLength($pattern);

        for ($i = 0; $i < $m - 1; ++$i) {
            $suffixIndex = $m - 1 - $i;
            $lastPrefixPosition[$suffixIndex] = $borderLength[$suffixIndex];
        }
        return $lastPrefixPosition;
    }

    /**
     * Helper function for good suffix table.
     */
    private function getBorderLength(string $pattern): array {
        $m = strlen($pattern);
        $borderLength = array_fill(0, $m + 1, 0);
        if ($m <= 1) {
            return $borderLength;
        }

        $j = 0;
        for ($i = 1; $i < $m; ++$i) {
            while ($j > 0 && $pattern[$i] != $pattern[$j]) {
                $j = $borderLength[$j];
            }
            if ($pattern[$i] == $pattern[$j]) {
                ++$j;
            }
            $borderLength[$i + 1] = $j;
        }
        return $borderLength;
    }


    /**
     * Basic string search using PHP's strpos function.
     *
     * @param string $text    The complete text to search within.
     * @param string $pattern The pattern to search for.
     * @return int|false      Position of the first occurrence of the pattern, or false if not found.
     */
    public function basicSearch(string $text, string $pattern)
    {
        return strpos($text, $pattern);
    }

    /**
     * Reads the content of a text file.
     *
     * @param string $filePath Path to the text file.
     * @return string|false   Content of the file as a string, or false on error.
     */
    private function readFileContent(string $filePath)
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            return false; // File does not exist or cannot be read
        }
        return file_get_contents($filePath);
    }

    /**
     * Compares the execution time of the Boyer-Moore algorithm and basic search.
     *
     * @param string $filePath Path to the text file.
     * @param string $pattern  Search pattern.
     * @return array           Associative array with comparison results.
     */
    public function compareSearchMethods(string $filePath, string $pattern): array
    {
        $text = $this->readFileContent($filePath);
        if ($text === false) {
            return [
                'error' => 'Could not read the file.'
            ];
        }

        $startTimeBoyerMoore = microtime(true);
        $resultBoyerMoore = $this->boyerMooreSearch($text, $pattern);
        $endTimeBoyerMoore = microtime(true);
        $timeBoyerMoore = $endTimeBoyerMoore - $startTimeBoyerMoore;

        $startTimeBasic = microtime(true);
        $resultBasic = $this->basicSearch($text, $pattern);
        $endTimeBasic = microtime(true);
        $timeBasic = $endTimeBasic - $startTimeBasic;


        return [
            'pattern' => $pattern,
            'file' => $filePath,
            'boyerMoore' => [
                'found' => $resultBoyerMoore !== false,
                'index' => $resultBoyerMoore,
                'time' => $timeBoyerMoore
            ],
            'basicSearch' => [
                'found' => $resultBasic !== false,
                'index' => $resultBasic,
                'time' => $timeBasic
            ]
        ];
    }
}

// Usage example:
$searcher = new BoyerMooreSearch();
$filePath = 'large_text_file.txt'; // Replace with the path to your large text file
$patternToSearch = 'algoritmo'; // Pattern to search for

// Generate a large text file for testing (optional, if you don't have one)
if (!file_exists($filePath)) {
    $largeText = '';
    for ($i = 0; $i < 100000; $i++) { // 100,000 lines of example text
        $largeText .= "Este es un ejemplo de texto para probar el algoritmo de Boyer-Moore y compararlo con una búsqueda básica.  Aquí buscamos la palabra algoritmo varias veces algoritmo en el texto. algoritmo\n";
    }
    file_put_contents($filePath, $largeText);
    echo "Test file 'large_text_file.txt' generated.\n";
}


$comparisonResults = $searcher->compareSearchMethods($filePath, $patternToSearch);

if (isset($comparisonResults['error'])) {
    echo "Error: " . $comparisonResults['error'] . "<br/>";
} else {
    echo "Comparison Results:<br/>";
    echo "File: " . $comparisonResults['file'] . "<br/>";
    echo "Searched Pattern: " . $comparisonResults['pattern'] . "<br/><br/>";

    echo "<b>Boyer-Moore:</b><br/>";
    echo "  Found: " . ($comparisonResults['boyerMoore']['found'] ? 'Yes' : 'No') . "<br/>";
    echo "  Index: " . ($comparisonResults['boyerMoore']['found'] ? $comparisonResults['boyerMoore']['index'] : 'Not found') . "<br/>";
    echo "  Execution Time: " . $comparisonResults['boyerMoore']['time'] . " seconds<br/><br/>";

    echo "Basic Search (strpos):<br/>";
    echo "  Found: " . ($comparisonResults['basicSearch']['found'] ? 'Yes' : 'No') . "<br/>";
    echo "  Index: " . ($comparisonResults['basicSearch']['found'] ? $comparisonResults['basicSearch']['index'] : 'Not found') . "<br/>";
    echo "  Execution Time: " . $comparisonResults['basicSearch']['time'] . " seconds<br/>";
}

?>
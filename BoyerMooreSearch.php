<?php

class BoyerMooreSearch
{
    /**
     * Implementaci�n del algoritmo Boyer-Moore para b�squeda de cadenas.
     *
     * @param string $text      El texto completo donde buscar.
     * @param string $pattern   El patr�n a buscar.
     * @return int|false       �ndice de la primera ocurrencia del patr�n en el texto, o false si no se encuentra.
     */
    public function boyerMooreSearch(string $text, string $pattern)
    {
        $n = strlen($text);
        $m = strlen($pattern);

        if ($m == 0) {
            return 0; // Patr�n vac�o se encuentra al principio
        }

        $badCharTable = $this->createBadCharacterTable($pattern);
        $goodSuffixTable = $this->createGoodSuffixTable($pattern);

        $i = 0; // �ndice para el texto
        while ($i <= ($n - $m)) {
            $j = $m - 1; // �ndice para el patr�n, empezamos desde el final

            // Comparar el patr�n con el texto de derecha a izquierda
            while ($j >= 0 && $pattern[$j] == $text[$i + $j]) {
                $j--;
            }

            // Si el patr�n coincide completamente
            if ($j < 0) {
                return $i; // Patr�n encontrado en el �ndice i
            } else {
                // Car�cter 'malo' en el texto que no coincide con el patr�n
                $badChar = $text[$i + $j];

                // Calcular el salto usando las tablas heur�sticas
                $badCharShift = isset($badCharTable[$badChar]) ? max(1, $j - $badCharTable[$badChar]) : $j + 1;
                $goodSuffixShift = $goodSuffixTable[$j + 1]; // j+1 porque el �ndice 0 en la tabla representa el sufijo completo

                $shift = max($badCharShift, $goodSuffixShift);
                $i += $shift;
            }
        }

        return false; // Patr�n no encontrado
    }


    /**
     * Crea la tabla de "car�cter malo" para el algoritmo Boyer-Moore.
     *
     * @param string $pattern El patr�n de b�squeda.
     * @return array          Tabla de caracteres malos (car�cter => �ltima posici�n en el patr�n).
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
     * Crea la tabla de "buen sufijo" para el algoritmo Boyer-Moore.
     *
     * @param string $pattern El patr�n de b�squeda.
     * @return array          Tabla de buenos sufijos (longitud del sufijo => salto).
     */
    private function createGoodSuffixTable(string $pattern): array
    {
        $m = strlen($pattern);
        $table = array_fill(0, $m + 1, 0); // Inicializar con ceros

        if ($m <= 1) {
            return $table; // No hay buenos sufijos significativos para patrones cortos
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
     * Helper function para tabla de buen sufijo.
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
     * Helper function para tabla de buen sufijo.
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
     * B�squeda b�sica de cadenas usando la funci�n strpos de PHP.
     *
     * @param string $text      El texto completo donde buscar.
     * @param string $pattern   El patr�n a buscar.
     * @return int|false       Posici�n de la primera ocurrencia del patr�n, o false si no se encuentra.
     */
    public function basicSearch(string $text, string $pattern)
    {
        return strpos($text, $pattern);
    }

    /**
     * Lee el contenido de un archivo de texto.
     *
     * @param string $filePath Ruta al archivo de texto.
     * @return string|false   Contenido del archivo como cadena, o false en caso de error.
     */
    private function readFileContent(string $filePath)
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            return false; // Archivo no existe o no se puede leer
        }
        return file_get_contents($filePath);
    }

    /**
     * Compara el tiempo de ejecuci�n del algoritmo Boyer-Moore y la b�squeda b�sica.
     *
     * @param string $filePath  Ruta al archivo de texto.
     * @param string $pattern   Patr�n de b�squeda.
     * @return array            Array asociativo con resultados de la comparaci�n.
     */
    public function compareSearchMethods(string $filePath, string $pattern): array
    {
        $text = $this->readFileContent($filePath);
        if ($text === false) {
            return [
                'error' => 'No se pudo leer el archivo.'
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

// Ejemplo de uso:
$searcher = new BoyerMooreSearch();
$filePath = 'large_text_file.txt'; // Reemplaza con la ruta a tu archivo de texto grande
$patternToSearch = 'algoritmo'; // Patr�n a buscar

// Generar un archivo de texto grande para probar (opcional, si no tienes uno)
if (!file_exists($filePath)) {
    $largeText = '';
    for ($i = 0; $i < 100000; $i++) { // 100,000 l�neas de texto de ejemplo
        $largeText .= "Este es un ejemplo de texto para probar el algoritmo de Boyer-Moore y compararlo con una b�squeda b�sica.  Aqu� buscamos la palabra algoritmo varias veces algoritmo en el texto. algoritmo\n";
    }
    file_put_contents($filePath, $largeText);
    echo "Archivo de prueba 'large_text_file.txt' generado.\n";
}


$comparisonResults = $searcher->compareSearchMethods($filePath, $patternToSearch);

if (isset($comparisonResults['error'])) {
    echo "Error: " . $comparisonResults['error'] . "\n";
} else {
    echo "Resultados de la comparaci�n:\n";
    echo "Archivo: " . $comparisonResults['file'] . "\n";
    echo "Patr�n buscado: " . $comparisonResults['pattern'] . "\n\n";

    echo "Boyer-Moore:\n";
    echo "  Encontrado: " . ($comparisonResults['boyerMoore']['found'] ? 'S�' : 'No') . "\n";
    echo "  �ndice: " . ($comparisonResults['boyerMoore']['found'] ? $comparisonResults['boyerMoore']['index'] : 'No encontrado') . "\n";
    echo "  Tiempo de ejecuci�n: " . $comparisonResults['boyerMoore']['time'] . " segundos\n\n";

    echo "B�squeda B�sica (strpos):\n";
    echo "  Encontrado: " . ($comparisonResults['basicSearch']['found'] ? 'S�' : 'No') . "\n";
    echo "  �ndice: " . ($comparisonResults['basicSearch']['found'] ? $comparisonResults['basicSearch']['index'] : 'No encontrado') . "\n";
    echo "  Tiempo de ejecuci�n: " . $comparisonResults['basicSearch']['time'] . " segundos\n";
}

?>
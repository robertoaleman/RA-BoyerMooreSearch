# RA-BoyerMooreSearch
This PHP class implements the Boyer-Moore string searching algorithm and provides functionalities to compare its performance with PHP's basic string search function (`strpos`).

<p>Author: Roberto Aleman,<a href="https://ventics.com/">ventics.com</a>, license AGPL</p>

<li>If you require further explanation, I can assist you based on my availability and at an hourly rate.</li>

<li>If you need to implement this version or an advanced and/or customized version of my code in your system, I can assist you based on my availability and at an hourly rate.</li>

<li>Please write to me and we'll discuss.</li>

<li>Do you need advice to implement an IT project, develop an algorithm to solve a real-world problem in your business, factory, or company?
Write me right now and I'll advise you.</li>



# BoyerMooreSearch Class Documentation

This PHP class implements the Boyer-Moore string searching algorithm and provides functionalities to compare its performance with PHP's basic string search function (`strpos`).

## Overview

The `BoyerMooreSearch` class offers an efficient method for finding the first occurrence of a pattern within a text. The Boyer-Moore algorithm is known for its efficiency, especially in large texts, as it often makes larger jumps in the text compared to simpler search algorithms.

In addition to the Boyer-Moore algorithm implementation, the class includes helper methods for creating the "bad character" and "good suffix" tables, which are fundamental to the algorithm's operation. It also provides a function to compare the execution time of the Boyer-Moore algorithm with a basic search using `strpos`.

## Methods

### `boyerMooreSearch(string $text, string $pattern): int|false`

Main implementation of the Boyer-Moore string searching algorithm.

**Parameters:**

* `$text` (`string`): The complete text where the search will be performed.
* `$pattern` (`string`): The pattern to search for within the text.

**Return Value:**

* `int`: The index of the first occurrence of the `$pattern` in the `$text`.
* `false`: If the `$pattern` is not found in the `$text`.

**Example:**

<code>
$searcher = new BoyerMooreSearch();
$text = "This is an example text to search for a pattern.";
$pattern = "example";
$index = $searcher->boyerMooreSearch($text, $pattern);

if ($index !== false) {
    echo "Pattern found at index: " . $index; // Output: Pattern found at index: 21
} else {
    echo "Pattern not found.";
}
</code>

<p> Please read the full <a href="Documentation.html">documentation</a> </p>

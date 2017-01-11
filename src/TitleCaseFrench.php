<?php

/*
* (c) Jurihub <info@jurihub.fr>
*/

namespace Jurihub\Helpers;

/**
* A simple class cleaner for French strings
*
* @property      array $capitalizedSpecials
* @property      array $removeCapitalizedSpecials
* @property      array $lowerCaseAfterQuoteAnd
* @property      array $capitalizeAfterQuoteAnd
* @property      array $lowerCaseWordList
*/
class TitleCaseFrench
{
    /**
    * Capitalized specials char
    *
    * @var array
    */
    static private $capitalizedSpecials = [
        [ 'input' => 'À', 'output' => 'A'],
        [ 'input' => 'Â', 'output' => 'A'],
        [ 'input' => 'Ä', 'output' => 'A'],
        [ 'input' => 'É', 'output' => 'E'],
        [ 'input' => 'È', 'output' => 'E'],
        [ 'input' => 'Ê', 'output' => 'E'],
        [ 'input' => 'Ë', 'output' => 'E'],
        [ 'input' => 'Ç', 'output' => 'C'],
        [ 'input' => 'Î', 'output' => 'I'],
        [ 'input' => 'Ï', 'output' => 'I'],
        [ 'input' => 'Ô', 'output' => 'O'],
        [ 'input' => 'Ö', 'output' => 'O'],
        [ 'input' => 'Û', 'output' => 'U'],
        [ 'input' => 'Ü', 'output' => 'U'],
        [ 'input' => 'Ù', 'output' => 'U']
    ];

    /**
    * Remove capitalized specials char
    *
    * @var array
    */
    static private $removeCapitalizedSpecials = [];

    /**
    * Lower case after quote and char
    *
    * @var array
    */
    static private $lowerCaseAfterQuoteAnd = ['c', 'j', 'm', 'n', 's', 't'];

    /**
    * Capitalize after quote and char
    *
    * @var array
    */
    static private $capitalizeAfterQuoteAnd = ['l', 'd'];

    /**
    * list of lower case words
    *
    * @var array
    */
    static private $lowerCaseWordList = [
        // definite articles
        'le', 'la', 'les',
        // indefinite articles
        '', 'un', 'une', 'des',
        // partitive articles
        '', 'du', 'de', 'des',
        // contracted articles
        '', 'au', 'aux', 'du', 'des',
        // demonstrative adjectives
        '', 'ce', 'cet', 'cette', 'ces',
        // exclamative adjectives
        '', 'quel', 'quels', 'quelle', 'quelles',
        // possessive adjectives
        '', 'mon', 'ton', 'son', 'notre', 'votre', 'leur', 'ma', 'ta', 'sa', 'mes', 'tes', 'ses', 'nos', 'vos', 'leurs',
        // coordinating conjunctions
        '', 'mais', 'ou', 'et', 'donc', 'or', 'ni', 'car', 'voire',
        // subordinating conjunctions
        '', 'que', 'qu', 'quand', 'comme', 'si', 'lorsque', 'lorsqu', 'puisque', 'puisqu', 'quoique', 'quoiqu',
        // prepositions
        '', 'à', 'chez', 'dans', 'entre', 'jusque', 'jusqu', 'hors', 'par', 'pour', 'sans', 'vers', 'sur', 'pas', 'parmi', 'avec', 'sous', 'en',
        // personal pronouns
        '', 'je', 'tu', 'il', 'elle', 'on', 'nous', 'vous', 'ils', 'elles', 'me', 'te', 'se', 'y',
        // relative pronouns
        '', 'qui', 'que', 'quoi', 'dont', 'où',
        // others
        '', 'ne'
    ];

    /**
    * Replace Capitalized specials char
    *
    * @param string $text
    *
    * @return string
    */
    static public function replaceCapitalizedSpecials($text)
    {
        if ($text) {
            foreach (self::$capitalizedSpecials as $capitalizedSpecial) {
                if (!in_array($capitalizedSpecial['input'], self::$removeCapitalizedSpecials)) {
                    $text = str_replace($capitalizedSpecial['input'], $capitalizedSpecial['output'], $text);
                }
            }
        }

        return $text;
    }

    /**
    * Is lower case word
    *
    * @param string $text
    *
    * @return boolean
    */
    static public function isLowerCaseWord($text)
    {
        return !!in_array($text, self::$lowerCaseWordList);
    }

    /**
    * Capitalize first char
    *
    * @param string $text
    *
    * @return string
    */
    static public function capitalizeFirst($text)
    {
        return ucfirst($text);
    }

    /**
    * Capitalize first char if needed
    *
    * @param string $text
    *
    * @return string
    */
    static public function capitalizeFirstIfNeeded($text)
    {
        if (self::isLowerCaseWord($text)) {
            return self::lower($text);
        }

        return self::capitalizeFirst($text);
    }

    /**
    * Has quote
    *
    * @param string $text
    *
    * @return string
    */
    static public function hasQuote($text)
    {
        $txtWithQuote = explode("'",$text);

        return (count($txtWithQuote) === 2);
    }

    /**
    * Capitalize with quote
    *
    * @param string $text
    *
    * @return string
    */
    static public function capitalizeWithQuote($text)
    {
        $txtWithQuote = explode("'", $text);

        if (count($txtWithQuote) === 2) {
            // could be d' or l', if it is the first word (l'Autre)
            if (strlen($txtWithQuote[0]) === 1 && in_array($txtWithQuote[0],self::$capitalizeAfterQuoteAnd)) {
                $text = self::lower($txtWithQuote[0]) . "'" . self::capitalizeFirstIfNeeded($txtWithQuote[1]);
            }

            // could be c', m', t', j', n', s' if it is the first word (c'est)
            if (strlen($txtWithQuote[0]) === 1 && in_array($txtWithQuote[0],self::$lowerCaseAfterQuoteAnd)) {
                $text = self::lower($txtWithQuote[0]) . "'" . self::lower($txtWithQuote[1]);
            }

            // could be 's
            if (strlen($txtWithQuote[1]) === 1) {
                $text = self::capitalizeFirstIfNeeded($txtWithQuote[0]) . "'" . self::lower($txtWithQuote[1]);
            }

            // could be jusqu'au
            if (strlen($txtWithQuote[0]) > 1 && strlen($txtWithQuote[1]) > 1) {
                $text = self::capitalizeFirstIfNeeded($txtWithQuote[0]) . "'" . self::capitalizeFirstIfNeeded($txtWithQuote[1]);
            }

            return $text;
        }

        return self::capitalizeFirstIfNeeded($text);
    }

    /**
    * Capitalize each word
    *
    * @param string $text
    *
    * @return string
    */
    static public function capitalizeEachWord($text)
    {
        if ($text) {
            $textArray = explode(" ", $text);
            $words = array();

            foreach ($textArray as $index => $txt) {
                array_push($words, self::capitalizeWord($txt, $index));
            }

            $text = implode(" ", $words);
        }

        return $text;
    }

    /**
    * Capitalize word
    *
    * @param string $text
    *
    * @return string
    */
    static public function capitalizeWord($txt, $index)
    {
        $isComposedWord = false;

        // reset the word with lowercase
        $txt = self::lower($txt);

        // look for '
        if (self::hasQuote($txt)) {
            $isComposedWord = true;
            $txt = self::capitalizeWithQuote($txt);
        }

        // look for -
        $txtWithDash = explode("-", $txt);

        if (count($txtWithDash) === 2) {
            return self::capitalizeFirst($txtWithDash[0]) . "-" + self::capitalizeFirstIfNeeded($txtWithDash[1]);
        }

        // look for .
        $txtWithDot = explode(".",$txt);

        if (count($txtWithDot) > 1) {
            $letters = array();

            foreach ($txtWithDot as $letter) {
                array_push($letters, self::capitalizeFirst($letter));
            }

            return implode(".", $letters);
        }

        // look for known words to replace if it is not the first word of the sentence
        if ($index === 0) {
            return self::capitalizeFirst($txt);
        }

        if (self::isLowerCaseWord($txt)) {
            return self::lower($txt);
        }

        if ($isComposedWord) {
            return $txt;
        }

        return self::capitalizeFirst($txt);
    }

    /**
    * Convert the given string to lower-case.
    *
    * @param  string  $value
    * @return string
    */
    static public function lower($value)
    {
        return mb_strtolower($value, 'UTF-8');
    }

    /**
    * Transform to title
    *
    * @param  string  $text
    * @return string
    */
    static public function title($text)
    {
        return self::replaceCapitalizedSpecials(self::capitalizeEachWord($text));
    }

    /*module.exports.addLowerCaseWords = public function (words) {
    self::$lowerCaseWordList = self::$lowerCaseWordList + ',' + words.split(',').map(public function(word){
    return word.trim();
}).join(',');
};

module.exports.removeLowerCaseWords = public function (words) {
$wordsToRemove = words.split(',').map(public function(word) {
return word.trim();
});
self::$lowerCaseWordList = self::$lowerCaseWordList.split(',').filter(public function(word) {
if (!~wordsToRemove.indexOf(word)) {
return true;
}
}).join(',');
};

module.exports.keepCapitalizedSpecials = public function(letters) {
self::$removeCapitalizedSpecials = letters.split(',').map(public function(letter) {
return letter.trim();
});
};*/
}

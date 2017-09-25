<?php
namespace Wamania\Snowball;

/**
 *
 * @link http://snowball.tartarus.org/algorithms/german/stemmer.html
 * @author wamania
 *
 */
class German extends Stem
{
    /**
     * All German vowels
     */
    protected static $vowels = array('a', 'e', 'i', 'o', 'u', 'y', 'ä', 'ö', 'ü');

    protected static $sEndings = array('b', 'd', 'f', 'g', 'h', 'k', 'l', 'm', 'n', 'r' ,'t');

    protected static $stEndings = array('b', 'd', 'f', 'g', 'h', 'k', 'l', 'm', 'n', 't');

    /**
     * Main function to get the STEM of a word
     * The word in param MUST BE IN UTF-8
     *
     * @param string $word
     * @throws \Exception
     * @return NULL|string
     */
    public function stem($word)
    {
        // we do ALL in UTF-8
        if (! Utf8::check($word)) {
            throw new \Exception('Word must be in UTF-8');
            return null;
        }

        $this->plainVowels = implode('', self::$vowels);

        $this->word = Utf8::strtolower($word);

        // First, replace ß by ss
        $this->word = Utf8::str_replace('ß', 'ss', $this->word);

        // put u and y between vowels into upper case
        $this->word = preg_replace('#(['.$this->plainVowels.'])y(['.$this->plainVowels.'])#u', '$1Y$2', $this->word);
        $this->word = preg_replace('#(['.$this->plainVowels.'])u(['.$this->plainVowels.'])#u', '$1U$2', $this->word);

        //  R1 and R2 are first set up in the standard way
        $this->r1();
        $this->r2();

        // but then R1 is adjusted so that the region before it contains at least 3 letters.
        if ($this->r1Index < 3) {
            $this->r1Index = 3;
            $this->r1 = Utf8::substr($this->word, 3);
        }

        $this->step1();
        $this->step2();
        $this->step3();
        $this->finish();

        return $this->word;
    }

    /**
     * Step 1
     */
    public function step1()
    {
        // delete if in R1
        if ( ($position = $this->search(array('em', 'ern', 'er'))) !== false) {
            if ($this->inR1($position)) {
                $this->word = Utf8::substr($this->word, 0, $position);
            }
            return true;
        }

        // delete if in R1
        if ( ($position = $this->search(array('es', 'en', 'e'))) !== false) {
            if ($this->inR1($position)) {
                $this->word = Utf8::substr($this->word, 0, $position);

                //If an ending of group (b) is deleted, and the ending is preceded by niss, delete the final s
                if ($this->search(array('niss')) !== false) {
                    $this->word = Utf8::substr($this->word, 0, -1);
                }
            }
            return true;
        }

        // s (preceded by a valid s-ending)
        if ( ($position = $this->search(array('s'))) !== false) {
            if ($this->inR1($position)) {
                $before = $position - 1;
                $letter = Utf8::substr($this->word, $before, 1);

                if (in_array($letter, self::$sEndings)) {
                    $this->word = Utf8::substr($this->word, 0, $position);
                }
            }
            return true;
        }

        return false;
    }

    /**
     * Step 2
     */
    public function step2()
    {
        // en   er   est
        //      delete if in R1
        if ( ($position = $this->search(array('en', 'er', 'est'))) !== false) {
            if ($this->inR1($position)) {
                $this->word = Utf8::substr($this->word, 0, $position);
            }
            return true;
        }

        // st (preceded by a valid st-ending, itself preceded by at least 3 letters)
        //      delete if in R1
        if ( ($position = $this->search(array('st'))) !== false) {
            if ($this->inR1($position)) {
                $before = $position - 1;
                if ($before >= 3) {
                    $letter = Utf8::substr($this->word, $before, 1);

                    if (in_array($letter, self::$stEndings)) {
                        $this->word = Utf8::substr($this->word, 0, $position);
                    }
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Step 3: d-suffixes
     */
    public function step3()
    {
        // end   ung
        //      delete if in R2
        //      if preceded by ig, delete if in R2 and not preceded by e
        if ( ($position = $this->search(array('end', 'ung'))) !== false) {
            if ($this->inR2($position)) {
                $this->word = Utf8::substr($this->word, 0, $position);
            }

            if ( ($position2 = $this->search(array('ig'))) !== false) {
                $before = $position2 - 1;
                $letter = Utf8::substr($this->word, $before, 1);

                if ( ($this->inR2($position2)) && ($letter != 'e') ) {
                    $this->word = Utf8::substr($this->word, 0, $position2);
                }
            }
            return true;
        }

        // ig   ik   isch
        //      delete if in R2 and not preceded by e
        if ( ($position = $this->search(array('ig', 'ik', 'isch'))) !== false) {
            $before = $position - 1;
            $letter = Utf8::substr($this->word, $before, 1);

            if ( ($this->inR2($position)) && ($letter != 'e') ) {
                $this->word = Utf8::substr($this->word, 0, $position);
            }
            return true;
        }

        // lich   heit
        //      delete if in R2
        //      if preceded by er or en, delete if in R1
        if ( ($position = $this->search(array('lich', 'heit'))) != false) {
            if ($this->inR2($position)) {
                $this->word = Utf8::substr($this->word, 0, $position);
            }

            if ( ($position2 = $this->search(array('er', 'en'))) !== false) {
                if ($this->inR1($position2)) {
                    $this->word = Utf8::substr($this->word, 0, $position2);
                }
            }
            return true;
        }

        // keit
        //      delete if in R2
        //      if preceded by lich or ig, delete if in R2
        if ( ($position = $this->search(array('keit'))) != false) {
            if ($this->inR2($position)) {
                $this->word = Utf8::substr($this->word, 0, $position);
            }

            if ( ($position2 = $this->search(array('lich', 'ig'))) !== false) {
                if ($this->inR2($position2)) {
                    $this->word = Utf8::substr($this->word, 0, $position2);
                }
            }
            return true;
        }

        return false;
    }

    /**
     * Finally
     */
    public function finish()
    {
        // turn U and Y back into lower case, and remove the umlaut accent from a, o and u.
        $this->word = Utf8::str_replace(array('U', 'Y', 'ä', 'ü', 'ö'), array('u', 'y', 'a', 'u', 'o'), $this->word);
    }
}
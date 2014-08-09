<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Translation Catalogue Interface
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Library\Translator
 */
interface KTranslatorCatalogueInterface extends IteratorAggregate, ArrayAccess, Serializable
{
    /**
     * Get a string from the catalogue
     *
     * @param  string $string
     * @return  string  The translation of the string
     */
    public function get($string);

    /**
     * Set a string in the catalogue
     *
     * @param  string $string
     * @param  string $translation
     * @return KTranslatorCatalogueInterface
     */
    public function set($string, $translation);

    /**
     * Check if a string exists in the catalogue
     *
     * @param  string $string
     * @return boolean
     */
    public function has($string);

    /**
     * Remove a string from the catalogue
     *
     * @param  string $string
     * @return KTranslatorCatalogueInterface
     */
    public function remove($string);

    /**
     * Clears out all strings from the catalogue
     *
     * @return  KTranslatorCatalogueInterface
     */
    public function clear();

    /**
     * Load translations into the catalogue.
     *
     * @param array  $translations Associative array containing translations.
     * @param bool   $override     Whether or not existing translations can be overridden during import.
     * @return bool True on success, false otherwise.
     */
    public function load(array $translations, $override = false);

    /**
     * Get a list of all strings in the catalogue
     *
     * @return  array
     */
    public function toArray();
}

<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

/**
 * CountryTaxTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class CountryTaxTable extends Doctrine_Table {

  /**
   * Returns an instance of this class.
   *
   * @return CountryTaxTable The table instance
   */
  public static function getInstance() {
    return Doctrine_Core::getTable('CountryTax');
  }

  public function queryAll() {
    return $this->createQuery('ct')->orderBy('ct.country ASC');
  }

  public static function taxForCountryVat($country, $hasVat) {
    $entry = self::getInstance()->createQuery('ct')->where('ct.country = ?', $country)->fetchOne();
    if ($entry) {
      /* @var $entry CountryTax */
      return $hasVat ? $entry->getTaxVat() : $entry->getTaxNoVat();
    }

    return StoreTable::value(StoreTable::BILLING_TAX);
  }

  public static function noteForCountryVat($country, $hasVat) {
    $entry = self::getInstance()->createQuery('ct')->where('ct.country = ?', $country)->fetchOne();
    if ($entry) {
      /* @var $entry CountryTax */
      return $hasVat ? $entry->getVatNoteText() : $entry->getNoVatNoteText();
    }

    return '';
  }

}

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
 * CampaignStoreTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class CampaignStoreTable extends Doctrine_Table {

  const KEY_PRIVACY_POLICY = 'privacy_policy';

  /**
   * @return CampaignStoreTable
   */
  public static function getInstance() {
    return Doctrine_Core::getTable('CampaignStore');
  }

  /**
   *
   * @param Campaign $campaign
   * @param Language $language
   * @param string $key
   * @return CampaignStore
   */
  public function findByCampaignLanguageKey(Campaign $campaign, Language $language, $key) {
    return $this->createQuery('s')
        ->where('s.campaign_id = ?', $campaign->getId())
        ->andWhere('s.language_id = ?', $language->getId())
        ->andWhere('s.key = ?', $key)
        ->fetchOne();
  }

}
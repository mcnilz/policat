<?php

/**
 * ContactTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class ContactTable extends Doctrine_Table {

  const FILTER_SEARCH = 'search';

  /**
   * Returns an instance of this class.
   *
   * @return ContactTable
   */
  public static function getInstance() {
    return Doctrine_Core::getTable('Contact');
  }

  /**
   *
   * @param MailingList $target_list
   * @return Doctrine_Query
   */
  public function queryByTargetList(MailingList $target_list, Petition $petition_pledge = null) {
    $query = $this->createQuery('c')->where('c.mailing_list_id = ?', $target_list->getId())->orderBy('c.lastname ASC, c.firstname ASC, c.id ASC');
    
    if ($petition_pledge) {
      $query->leftJoin('c.Pledges p')->leftJoin('p.PledgeItem pi')->andWhere('pi.petition_id = ? OR pi.petition_id IS NULL', $petition_pledge->getId());
      $query->orderBy('p.status_at DESC, c.lastname ASC, c.firstname ASC, c.id ASC');
    }

    return $query;
  }

  /**
   *
   * @param Doctrine_Query $query
   * @param FilterContactForm $filter
   * @return Doctrine_Query
   */
  public function filter(Doctrine_Query $query, $filter) {
    if (!$filter)
      return $query;

    $alias = $query->getRootAlias();

    $search = trim($filter->getValue(self::FILTER_SEARCH));
    if ($search) {
      $query->andWhere('concat(' . $alias . '.email, " ", ' . $alias . '.firstname, " ", ' . $alias . '.lastname) LIKE ?', '%' . $search . '%');
    }

    return $query;
  }

//  public function queryByPledgeItem(MailingList $mailing_list, PledgeItem $pledge_item) {
//    return $this->createQuery('c')
//        ->where('c.mailing_list_id = ?', $mailing_list->getId())
//        ->orderBy('c.id')
//        ->leftJoin('c.Pledges p')
//        ->andWhere('p.pledge_item_id = ? OR p.pledge_item_id is NULL', $pledge_item->getId())
//    ;
//  }

  /**
   *
   * @param Petition $petition
   * @param string $ts_1
   * @param string $ts_2
   * @param PetitionSigning $existing_signing
   * @return Doctrine_Query
   */
  public function queryByTargetSelector(Petition $petition, $ts_1, $ts_2, $existing_signing = null) {
    $ts = $petition->getTargetSelectors();
    $query = $this
      ->createQuery('c')
      ->where('c.mailing_list_id = ?', $petition->getMailingListId());

    if ($existing_signing) {
      $query
        ->andWhere('c.id NOT IN (SELECT psc.contact_id FROM PetitionSigningContact psc WHERE psc.petition_signing_id = ?)', $existing_signing->getId());
    }

    if ($ts_1 && count($ts) > 0) {
      $sel = $ts[0]['id'];
      if (is_numeric($sel)) {
        $is_mapping = array_key_exists('kind', $ts[0]) && $ts[0]['kind'] == MailingListMeta::KIND_MAPPING;
        $is_choice = array_key_exists('kind', $ts[0]) && $ts[0]['kind'] == MailingListMeta::KIND_CHOICE;
        $query->leftJoin('c.ContactMeta cm1');
        if ($is_mapping) {
          $mapped_ts1 = MappingPairTable::getInstance()->getMapByIdAndA($ts[0]['mapping_id'], $ts_1);
          $query
            ->andWhere('cm1.mailing_list_meta_id = ?', $ts[0]['meta_id'])
            ->leftJoin('cm1.MailingListMetaChoice mlmc1')
            ->andWhereIn('mlmc1.choice', $mapped_ts1);
        } elseif ($is_choice && is_numeric($ts_1)) {
          $query
            ->andWhere('cm1.mailing_list_meta_id = ?', $sel)
            ->andWhere('cm1.mailing_list_meta_choice_id = ?', $ts_1);
        } else { // should not happen
          $query
            ->andWhere('cm1.mailing_list_meta_id = ?', $sel)
            ->andWhere('cm1.value = ?', $ts_1);
        }
      } else {
        if ($sel === 'contact') {
          if (is_numeric($ts_1)) {
            $query->andWhere("c.id = ?", $ts_1);
          }
        } else {
          if (is_string($ts_1) && $ts_1 != 'all') {
            $query->andWhere("c.$sel = ?", $ts_1);
          }
        }
      }

      if ($ts_2 && count($ts) > 1) {
        $sel = $ts[1]['id'];
        if (is_numeric($sel)) {
          if (is_numeric($ts_2))
            $query
              ->leftJoin('c.ContactMeta cm2')
              ->andWhere('cm2.mailing_list_meta_id = ?', $sel)
              ->andWhere('cm2.mailing_list_meta_choice_id = ?', $ts_2);
        }
        else {
          if (is_string($ts_2) && $ts_2 != 'all') {
            $query->andWhere("c.$sel = ?", $ts_2);
          }
        }
      } else {
        if (is_numeric($ts_2)) {
          $query->andWhere("c.id = ?", $ts_2);
        }
      }
    }

    return $query;
  }

  /**
   *
   * @param Petition $petition
   * @param string $ts_1
   * @param string $ts_2
   * @param PetitionSigning $existing_signing
   * @return array
   */
  public function fetchIdsByTargetSelector(Petition $petition, $ts_1, $ts_2, $existing_signing = null) {
    return $this->queryByTargetSelector($petition, $ts_1, $ts_2, $existing_signing)->select('DISTINCT c.id')->fetchArray();
  }

  public function fetchIdsByContactIds(Petition $petition, $contact_ids, $existing_signing = null) {
    $query = Doctrine_Core::getTable('Contact')
      ->createQuery('c')
      ->where('c.mailing_list_id = ?', $petition->getMailingListId())
      ->select('DISTINCT c.id');

    if ($existing_signing) {
      $query
        ->andWhere('c.id NOT IN (SELECT psc.contact_id FROM PetitionSigningContact psc WHERE psc.petition_signing_id = ?)', $existing_signing->getId());
    }

    if (is_string($contact_ids)) {
      $contact_ids = explode(',', $contact_ids);
    }

    $contact_ids = array_filter($contact_ids, 'ctype_digit');

    if (!$contact_ids) {
      return array();
    }

    $query->andWhereIn('c.id', $contact_ids);

    return $query->fetchArray();
  }

  /**
   *
   * @param MailingList $ml
   * @param Petition $pledges_by_petition
   * @return Doctrine_Query
   */
  public function queryByMailingList(MailingList $ml, $pledges_by_petition = null) {
    $query = $this->createQuery('c INDEXBY c.id')->where('c.mailing_list_id = ?', $ml->getId())->orderBy('c.id');

    if ($pledges_by_petition) {
      $query->leftJoin('c.Pledges p INDEXBY p.pledge_item_id');
      $query->select('c.*, p.*');
      $query->andWhere('p.contact_id IS NULL OR (p.pledge_item_id IN (SELECT pi.id FROM PledgeItem pi WHERE pi.petition_id = ?))', $pledges_by_petition->getId());
    } else {
      $query->select('c.*');
    }

    return $query;
  }

  public function getPledgeInfoColumns($contacts, $info_columns) {
    if (!$info_columns) {
      return array();
    }
    $with_country = false;
    $i = 0;
    $mailing_list_meta_ids = array();
    $pos = array();
    foreach ($info_columns as $info_name) {
      if ($info_name === 'country') {
        $with_country = $i;
      } elseif (is_numeric($info_name)) {
        $mailing_list_meta_ids[] = $info_name;
        $pos[$info_name] = $i;
      }
      $i++;
    }
    $infos = array();

    $contact_ids = array();
    foreach ($contacts as $contact) {
      /* @var $contact Contact */
      $contact_ids[] = $contact->getId();
      $infos[$contact->getId()] = array_fill(0, count($info_columns), '');

      if ($with_country !== null) {
        $infos[$contact->getId()][$with_country] = $contact->getCountryName();
      }
    }

    if (!$contact_ids) {
      return array();
    }

    if ($mailing_list_meta_ids) {
      foreach (ContactMetaTable::getInstance()->createQuery('cm')
        ->select('cm.contact_id, cm.mailing_list_meta_id, cm.value, cm.mailing_list_meta_choice_id, c.choice')
        ->whereIn('cm.contact_id', $contact_ids)
        ->andWhereIn('cm.mailing_list_meta_id', $mailing_list_meta_ids)
        ->leftJoin('cm.MailingListMetaChoice c')
        ->execute(array(), Doctrine_Core::HYDRATE_ARRAY_SHALLOW) as $row) {

        if ($row['mailing_list_meta_choice_id']) {
          $infos[$row['contact_id']][$pos[$row['mailing_list_meta_id']]] = $row['choice'];
        } else {
          $infos[$row['contact_id']][$pos[$row['mailing_list_meta_id']]] = $row['value'];
        }
      }
    }

    return array_map(array(__CLASS__, 'implodeComma'), array_map('array_filter', $infos));
  }

  public static function implodeComma($pieces) {
    return implode(', ', $pieces);
  }

  public function queryFullData(MailingList $ml, $pledges_by_petition = null) {
    $query = $this->queryByMailingList($ml);

    if ($pledges_by_petition) {
      $query->leftJoin('c.PetitionContacts pc INDEXBY pc.petition_id');
      $query->addSelect('pc.petition_id, pc.contact_id, pc.comment');
    }

    $query->leftJoin('c.ContactMeta cm');
    $query->leftJoin('cm.MailingListMetaChoice mlmc');
    $query->addSelect('cm.*')->addSelect('mlmc.*');

    return $query;
  }
}

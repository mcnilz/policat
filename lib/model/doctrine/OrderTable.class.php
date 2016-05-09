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
 * OrderTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class OrderTable extends Doctrine_Table {

  const STATUS_ORDER = 1;
  const STATUS_CANCELATION = 8;
  const STATUS_PAID = 10;

  static $STATUS_SHOW = array(
      self::STATUS_ORDER => 'ordering',
      self::STATUS_CANCELATION => 'cancelation',
      self::STATUS_PAID => 'paid'
  );

  const PAYPAL_STATUS_NONE = 1;
  const PAYPAL_STATUS_PAYMENT_EXECUTED = 10;

  /**
   * Returns an instance of this class.
   *
   * @return OrderTable The table instance
   */
  public static function getInstance() {
    return Doctrine_Core::getTable('Order');
  }

  public function queryAll() {
    return $this->createQuery('o')->orderBy('id DESC');
  }

  /**
   * 
   * @param Campaign $campign
   * @param sfGuardUser $user
   * @return Order
   */
  public function fetchLastOrder(Campaign $campign, sfGuardUser $user) {
    return $this
        ->createQuery('o')
        ->leftJoin('o.Quotas q')
        ->where('q.campaign_id = ?', $campign->getId())
        ->andWhere('o.user_id = ?', $user->getId())
        ->orderBy('o.id DESC')
        ->limit(1)
        ->fetchOne();
  }

  public function paid(Order $order, $connection = null) {
    $con = $connection ? : $this->getConnection();
    try {
      $time = time();
      $con->beginTransaction();
      foreach ($order->getQuotas() as $quota) {
        /* @var $quota Quota */
        $quota->setStatus(QuotaTable::STATUS_ACTIVE);
        if (!$quota->getStartAt()) {
          $quota->setStartAt(gmdate('Y-m-d H:i:s', $time));
        }
        if (!$quota->getEndAt()) {
          $quota->setEndAt(gmdate('Y-m-d H:i:s', $time + $quota->getDays() * 24 * 60 * 60));
        }
        $quota->setPaidAt(gmdate('Y-m-d H:i:s', $time));
        $quota->save();
        if ($quota->getCampaignId()) {
          QuotaTable::getInstance()->activateQuota($quota->getCampaign());
          $quota->getCampaign()->setOrder(null);
          $quota->getCampaign()->save();
        }
      }
      $order->setStatus(OrderTable::STATUS_PAID);
      $order->setPaidAt(gmdate('Y-m-d H:i:s', $time));
      $order->save();

      $ticket = TicketTable::getInstance()->generate(array(
          TicketTable::CREATE_TO => $order->getUser(),
          TicketTable::CREATE_CAMPAIGN => $quota->getCampaign(),
          TicketTable::CREATE_KIND => TicketTable::KIND_PAID,
          TicketTable::CREATE_TEXT => 'Payment for order #' . $order->getId() . ' received. Package can be used now.',
      ));
      if ($ticket) {
        $ticket->save();
      }

      $con->commit();
      $ticket->notifyAdmin();
      return true;
    } catch (\Exception $e) {
      $con->rollback();
      return false;
    }
  }

}

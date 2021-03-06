<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class frontendConfiguration extends sfApplicationConfiguration {

  public function configure() {
    $dispatcher = $this->getEventDispatcher();
    $dispatcher->connect('doctrine.configure_connection', array($this, 'configureDoctrineConnection'));
    $dispatcher->connect('task.cache.clear', array($this, 'clearQueryCache'));
  }

  public function configureDoctrineConnection(sfEvent $event) {
    $parameters = $event->getParameters();
    $con = $parameters['connection'];
    /* @var $con Doctrine_Connection */

    $cache = new Doctrine_Cache_Db(array('connection' => $con, 'tableName' => 'query_cache'));
    $con->setAttribute(Doctrine_Core::ATTR_QUERY_CACHE, $cache);
    $con->setAttribute(Doctrine_Core::ATTR_QUERY_CACHE_LIFESPAN, 3600);
  }

  public function clearQueryCache(sfEvent $event) {
    $databaseManager = new sfDatabaseManager($this);
    $con = $databaseManager->getDatabase('doctrine')->getConnection();
    /* @var $con PDO */
    $con->exec('TRUNCATE query_cache');
  }

  public function initialize() {
    parent::initialize();

    if (!sfConfig::get('sf_cli') && false !== sfConfig::get('app_frontend_csrf_secret')) {
      sfForm::enableCSRFProtection(sfConfig::get('app_frontend_csrf_secret'));
    }
  }

}

<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version29 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->createTable('facebook_tab', array(
             'id' => 
             array(
              'type' => 'integer',
              'primary' => '1',
              'autoincrement' => '1',
              'length' => '4',
             ),
             'page_id' => 
             array(
              'type' => 'string',
              'length' => '40',
             ),
             'language_id' => 
             array(
              'type' => 'string',
              'notnull' => '',
              'length' => '5',
             ),
             'widget_id' => 
             array(
              'type' => 'integer',
              'notnull' => '',
              'length' => '4',
             ),
             ), array(
             'indexes' => 
             array(
              'ft_page' => 
              array(
              'fields' => 
              array(
               0 => 'page_id',
              ),
              ),
              'ft_page_lang' => 
              array(
              'fields' => 
              array(
               0 => 'page_id',
               1 => 'language_id',
              ),
              'type' => 'unique',
              ),
             ),
             'primary' => 
             array(
              0 => 'id',
             ),
             'collate' => 'utf8_general_ci',
             'charset' => 'utf8',
             ));
    }

    public function down()
    {
        $this->dropTable('facebook_tab');
    }
}
<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version145 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addIndex('widget', 'widget_origin', array(
             'fields' => 
             array(
              0 => 'petition_id',
              1 => 'origin_widget_id',
             ),
             ));
    }

    public function down()
    {
        $this->removeIndex('widget', 'widget_origin', array(
             'fields' => 
             array(
              0 => 'petition_id',
              1 => 'origin_widget_id',
             ),
             ));
    }
}
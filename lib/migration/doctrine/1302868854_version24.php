<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version24 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('petition', 'target_num', 'integer', '4', array(
             'notnull' => '1',
             'default' => '0',
             ));
    }

    public function down()
    {
        $this->removeColumn('petition', 'target_num');
    }
}
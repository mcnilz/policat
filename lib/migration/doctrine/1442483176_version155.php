<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version155 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('petition_signing', 'delete_code', 'string', '16', array(
             ));
        $this->changeColumn('petition_signing', 'validation_data', 'string', '16', array(
             ));
    }

    public function down()
    {
        $this->removeColumn('petition_signing', 'delete_code');
    }
}
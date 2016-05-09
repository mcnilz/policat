<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version6 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('petition', 'homepage', 'integer', '1', array(
             'notnull' => '1',
             'default' => '0',
             ));
        $this->addColumn('petition', 'twitter_tags', 'string', '200', array(
             ));
        $this->addColumn('petition', 'language_id', 'string', '5', array(
             'notnull' => '',
             ));
        $this->addColumn('petition_text', 'widget_id', 'integer', '4', array(
             'notnull' => '',
             ));
    }

    public function down()
    {
        $this->removeColumn('petition', 'homepage');
        $this->removeColumn('petition', 'twitter_tags');
        $this->removeColumn('petition', 'language_id');
        $this->removeColumn('petition_text', 'widget_id');
    }
}
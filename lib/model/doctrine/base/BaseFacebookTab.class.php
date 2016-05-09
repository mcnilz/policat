<?php

/**
 * BaseFacebookTab
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $page_id
 * @property string $language_id
 * @property integer $widget_id
 * @property Widget $Widget
 * @property Language $Language
 * 
 * @method integer     getId()          Returns the current record's "id" value
 * @method string      getPageId()      Returns the current record's "page_id" value
 * @method string      getLanguageId()  Returns the current record's "language_id" value
 * @method integer     getWidgetId()    Returns the current record's "widget_id" value
 * @method Widget      getWidget()      Returns the current record's "Widget" value
 * @method Language    getLanguage()    Returns the current record's "Language" value
 * @method FacebookTab setId()          Sets the current record's "id" value
 * @method FacebookTab setPageId()      Sets the current record's "page_id" value
 * @method FacebookTab setLanguageId()  Sets the current record's "language_id" value
 * @method FacebookTab setWidgetId()    Sets the current record's "widget_id" value
 * @method FacebookTab setWidget()      Sets the current record's "Widget" value
 * @method FacebookTab setLanguage()    Sets the current record's "Language" value
 * 
 * @package    policat
 * @subpackage model
 * @author     Martin
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseFacebookTab extends myDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('facebook_tab');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             'length' => 4,
             ));
        $this->hasColumn('page_id', 'string', 40, array(
             'type' => 'string',
             'length' => 40,
             ));
        $this->hasColumn('language_id', 'string', 5, array(
             'type' => 'string',
             'notnull' => false,
             'length' => 5,
             ));
        $this->hasColumn('widget_id', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => false,
             'length' => 4,
             ));


        $this->index('ft_page', array(
             'fields' => 
             array(
              0 => 'page_id',
             ),
             ));
        $this->index('ft_page_lang', array(
             'fields' => 
             array(
              0 => 'page_id',
              1 => 'language_id',
             ),
             'type' => 'unique',
             ));
        $this->option('symfony', array(
             'filter' => false,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Widget', array(
             'local' => 'widget_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));

        $this->hasOne('Language', array(
             'local' => 'language_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));
    }
}
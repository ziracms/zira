<?php
/**
 * Zira project.
 * recordtext.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Windows;

use Dash\Dash;
use Zira;
use Zira\Permission;

class Recordtext extends Editor {
    const DRAFT_INTERVAL = 10000;

    protected $_help_url = 'zira/help/notepad';

    public $item;

    public function create() {
        parent::create();

        $this->setOnCloseJSCallback(
            $this->createJSCallback(
                'desk_call(dash_recordtext_close, this);'
            )
        );

        $this->setOnSaveJSCallback(
            $this->createJSCallback(
                'desk_call(dash_recordtext_save, this);'
            )
        );

        $this->addStrings(array(
            'saved to drafts',
            'Load saved draft ?'
        ));

        $this->addVariables(array(
            'dash_record_draft_interval' => self::DRAFT_INTERVAL,
            'dash_recordtext_records_wnd' => Dash::getInstance()->getWindowJSName(Records::getClass())
        ), true);

        $this->includeJS('dash/recordtext');
    }

    public function getTextOnLoadJs() {
        return parent::getTextOnLoadJs().
                'desk_call(dash_recordtext_load, this);'
            ;
    }

    public function load() {
        if (empty($this->item) || !is_numeric($this->item)) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }
        if (!Permission::check(Permission::TO_CREATE_RECORDS) && !Permission::check(Permission::TO_EDIT_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $record = new Zira\Models\Record($this->item);
        if (!$record->loaded()) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }

        if (!$record->content && !Permission::check(Permission::TO_CREATE_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        } else if ($record->content && !Permission::check(Permission::TO_EDIT_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $this->setBodyFullContent(
            $this->getBodyContent($record->content, 'item', $this->item, (string)Zira\Request::post('id'))
        );

        $this->setTitle(Zira\Locale::t(self::$_title).' - '.$record->title);

        $draft = Zira\Models\Draft::getCollection()
                            ->where('record_id','=',$record->id)
                            ->and_where('author_id','=',Zira\User::getCurrent()->id)
                            ->and_where('published','=',Zira\Models\Draft::STATUS_NOT_PUBLISHED)
                            ->get(0);

        if ($draft && strtotime($draft->modified_date)>strtotime($record->modified_date)) {
            $draftExists = true;
        } else {
            $draftExists = false;
        }

        $this->setData(array(
            'content' => null,
            'items' => array($this->item),
            'draft' => $draftExists ? $draft->id : 0
        ));
    }
}
<?php
/**
 * Zira project.
 * index.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Eform\Controllers;

use Zira;
use Eform;

class Index extends Zira\Controller {
    public function index() {
        $request = Zira\Router::getRequest();
        $request = preg_replace('/^'.preg_quote(Eform\Eform::ROUTE).'\/([a-z]+)$/', '$1', $request);
        if (empty($request)) Zira\Response::notFound();

        $eform = Eform\Models\Eform::getCollection()
                                    ->where('name','=', $request)
                                    ->and_where('active','=',1)
                                    ->get(0);

        if (!$eform) Zira\Response::notFound();

        $fields = Eform\Models\Eformfield::getCollection()
                                            ->where('eform_id','=',$eform->id)
                                            ->order_by('sort_order', 'asc')
                                            ->get();

        $labels = array();
        $has_required = false;
        $has_file = false;
        foreach($fields as $field) {
            $labels []= Zira\Locale::t($field->label);
            if ($field->required) $has_required = true;
            if ($field->field_type == 'file') $has_file = true;
        }

        $form = new Eform\Forms\Submit($eform, $fields, $has_required, $has_file);
        if (Zira\Request::isPost() && $form->isValid()) {
            try {
                Eform\Models\Eform::sendEmail($eform, $fields, $form);
                $form->setMessage(Zira\Locale::tm('Successfully sent. Thank you!', 'eform'));
                $form->setFill(false);
            } catch (\Exception $e) {
                $form->setError(Zira\Locale::tm('Sorry, something went wrong. Try later', 'eform'));
            }
        }

        $title = Zira\Locale::t($eform->title);
        $description = Zira\Locale::t($eform->description);
        $meta_description = $description;
        $meta_description = str_replace("\r\n", "\n", $meta_description);
        $dlimit = 1024;
        $p = strpos($meta_description, "\n");
        if ($p>0) {
            $meta_description = substr($meta_description, 0, $p);
        } else if (mb_strlen($meta_description, CHARSET)>$dlimit) {
            $p = strrpos($meta_description, ' ');
            if ($p>0) $meta_description = substr($meta_description, 0, $p);
            else $meta_description = mb_substr($meta_description, 0, $dlimit, CHARSET).'...';
        }
        $keywords = implode(', ', $labels);

        Zira\Page::addTitle($title);
        Zira\Page::setKeywords($keywords);
        Zira\Page::setDescription($meta_description);
        //Zira\Page::addOpenGraphTags($title, $meta_description, Eform\Eform::ROUTE.'/'.$eform->name);

        Zira\Page::setView('eform/page');

        Zira\Page::render(array(
            Zira\Page::VIEW_PLACEHOLDER_TITLE => $title,
            Zira\Page::VIEW_PLACEHOLDER_DESCRIPTION => $description,
            Zira\Page::VIEW_PLACEHOLDER_CONTENT => $form
        ));
    }
}
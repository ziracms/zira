<?php
/**
 * Zira project.
 * index.php
 * (c)2016 http://dro1d.ru
 */

namespace Forum\Controllers;

use Zira;
use Forum;

class Index extends Zira\Controller {
    protected $_has_category = false;

    public function _before() {
        parent::_before();

        Zira\Page::setRedirectUrl(Forum\Forum::ROUTE);

        $category = Zira\Category::current();
        if ($category) {
            $category_parts = explode('/',$category->name);
            if ((count($category_parts)==1 && $category_parts[0]==Zira\Router::getModule() && Zira\Router::getAction()==DEFAULT_ACTION) ||
                (count($category_parts)==2 && $category_parts[0]==Zira\Router::getModule() && $category_parts[1]==Zira\Router::getAction())
            ) {
                $this->_has_category = true;
            }
        }
    }

    protected function _renderPlaceholderCategory() {
        if ($this->_has_category) {
            Zira\Content\Category::placeholderContent();
        }
    }

    protected function _renderCategoryPage() {
        if ($this->_has_category) {
            Zira\View::addPlaceholderView(Zira\View::VAR_CONTENT_BOTTOM, array(), 'forum/bottom');
            Zira\Content\Category::content();
        }
    }

    public function index() {
        $this->_renderPlaceholderCategory();

        $categories = Forum\Models\Category::getCollection()
                                ->order_by('sort_order', 'asc')
                                ->get();

        foreach($categories as $category) {
            $category->forums = Forum\Models\Forum::getCollection()
                                    ->select(Forum\Models\Forum::getFields())
                                    ->left_join(Zira\Models\User::getClass(), array('user_firstname' => 'firstname', 'user_secondname' => 'secondname', 'user_username' => 'username'))
                                    ->where('category_id', '=', $category->id)
                                    ->and_where('active', '=', 1)
                                    ->order_by('sort_order', 'asc')
                                    ->get();
        }

        $title = Zira\Config::get('forum_title') ? Zira\Locale::t(Zira\Config::get('forum_title')) : Zira\Locale::tm('Forum','forum');
        $meta_title = Zira\Config::get('forum_meta_title') ? Zira\Locale::t(Zira\Config::get('forum_meta_title')) : $title;
        $description = Zira\Config::get('forum_description') ? Zira\Locale::t(Zira\Config::get('forum_description')) : '';
        $meta_description = Zira\Config::get('forum_meta_description') ? Zira\Locale::t(Zira\Config::get('forum_meta_description')) : $description;
        $keywords = Zira\Config::get('forum_meta_keywords') ? Zira\Locale::t(Zira\Config::get('forum_meta_keywords')) : '';

        Zira\Page::addTitle($meta_title);
        Zira\Page::setKeywords($keywords);
        Zira\Page::setDescription($meta_description);

        Zira\Page::putBreadcrumb(Forum\Forum::ROUTE, Zira\Locale::tm('Forum', 'forum'));

        if (Zira\Config::get('forum_layout')) {
            Zira\Page::setLayout(Zira\Config::get('forum_layout'));
        }
        Zira\Page::setView('forum/page');

        $searchForm = new Forum\Forms\Search();
        $searchForm->setAlignRight(true);

        Zira\View::addPlaceholderView(Zira\View::VAR_CONTENT, array(
                                                                    'categories'=>$categories,
                                                                    'searchForm'=>$searchForm
                                                                ), 'forum/index');

        Zira\Page::render(array(
            Zira\Page::VIEW_PLACEHOLDER_TITLE => $title,
            Zira\Page::VIEW_PLACEHOLDER_DESCRIPTION => $description,
            Zira\Page::VIEW_PLACEHOLDER_CONTENT => ''
        ));
    }

    public function group($category_id) {
        if (empty($category_id)) {
            if (!$this->_has_category) Zira\Response::notFound();
            else $this->_renderCategoryPage();
            return;
        }

        $this->_renderPlaceholderCategory();

        $category = Forum\Models\Category::getCollection()
                                ->where('id','=',$category_id)
                                ->get(0);

        if (!$category) Zira\Response::notFound();

        // checking permission
        if ($category->access_check && !Zira\Permission::check(Zira\Permission::TO_VIEW_RECORDS)) {
            if (!Zira\User::isAuthorized()) {
                Zira\Response::redirect('user/login?redirect='.Forum\Models\Category::generateUrl($category), true);
            } else {
                Zira\Response::forbidden();
            }
        }

        $rows = Forum\Models\Forum::getCollection()
                            ->select(Forum\Models\Forum::getFields())
                            ->left_join(Zira\Models\User::getClass(), array('user_firstname'=>'firstname', 'user_secondname'=>'secondname', 'user_username'=>'username'))
                            ->where('category_id','=',$category->id)
                            ->and_where('active','=',1)
                            ->order_by('sort_order','asc')
                            ->get();

        $title = Zira\Locale::t($category->title);
        $meta_title = $category->meta_title ? Zira\Locale::t($category->meta_title) : $title;
        $description = $category->description ? Zira\Locale::t($category->description) : '';
        $meta_description = $category->meta_description ? Zira\Locale::t($category->meta_description) : $description;
        $keywords = $category->meta_keywords ? Zira\Locale::t($category->meta_keywords) : '';

        Zira\Page::addTitle($meta_title);
        Zira\Page::setKeywords($keywords);
        Zira\Page::setDescription($meta_description);

        Zira\Page::putBreadcrumb(Forum\Forum::ROUTE, Zira\Locale::tm('Forum', 'forum'));
        Zira\Page::removeBreadcrumb(Forum\Forum::ROUTE . '/'. Zira\Router::getAction());
        Zira\Page::addBreadcrumb(Forum\Models\Category::generateUrl($category), Zira\Locale::t($category->title));

        if ($category->layout) {
            Zira\Page::setLayout($category->layout);
        } else if (Zira\Config::get('forum_layout')) {
            Zira\Page::setLayout(Zira\Config::get('forum_layout'));
        }
        Zira\Page::setView('forum/page');

        $searchForm = new Forum\Forms\Search();
        $searchForm->setAlignRight(true);

        Zira\View::addPlaceholderView(Zira\View::VAR_CONTENT, array(
                                                                    'items'=>$rows,
                                                                    'searchForm'=>$searchForm
                                                                ), 'forum/group');

        Zira\Page::render(array(
            Zira\Page::VIEW_PLACEHOLDER_TITLE => $title,
            Zira\Page::VIEW_PLACEHOLDER_DESCRIPTION => $description,
            Zira\Page::VIEW_PLACEHOLDER_CONTENT => ''
        ));
    }

    public function threads($forum_id) {
        if (empty($forum_id)) {
            if (!$this->_has_category) Zira\Response::notFound();
            else $this->_renderCategoryPage();
            return;
        }

        $this->_renderPlaceholderCategory();

        $category_fields = Forum\Models\Category::getFields();
        $_category_fields = array();
        foreach($category_fields as $field) {
            $_category_fields['category_'.$field] = $field;
        }

        $forum = Forum\Models\Forum::getCollection()
                                ->select(Forum\Models\Forum::getFields())
                                ->join(Forum\Models\Category::getClass(), $_category_fields)
                                ->where('id','=',$forum_id)
                                ->get(0);

        if (!$forum || !$forum->active) Zira\Response::notFound();

        // checking permission
        if (($forum->access_check || $forum->category_access_check) && !Zira\Permission::check(Zira\Permission::TO_VIEW_RECORDS)) {
            if (!Zira\User::isAuthorized()) {
                Zira\Response::redirect('user/login?redirect='.Forum\Models\Forum::generateUrl($forum), true);
            } else {
                Zira\Response::forbidden();
            }
        }

        $sticky = Forum\Models\Topic::getCollection()
                            ->select(Forum\Models\Topic::getFields())
                            ->left_join(Zira\Models\User::getClass(), array('user_firstname'=>'firstname', 'user_secondname'=>'secondname', 'user_username'=>'username'))
                            ->where('category_id','=',$forum->category_id)
                            ->and_where('forum_id','=',$forum->id)
                            ->and_where('sticky','=',1)
                            ->and_where('published','=',Forum\Models\Topic::STATUS_PUBLISHED)
                            ->order_by('id','desc')
                            ->get();

        $total = Forum\Models\Topic::getCollection()
                            ->count()
                            ->where('category_id','=',$forum->category_id)
                            ->and_where('forum_id','=',$forum->id)
                            ->and_where('sticky','=',0)
                            ->and_where('published','=',Forum\Models\Topic::STATUS_PUBLISHED)
                            ->get('co');

        $limit = Zira\Config::get('forum_limit') ? intval(Zira\Config::get('forum_limit')) : 10;
        $page = (int)Zira\Request::get('page');
        $pages = ceil($total / $limit);
        if ($page>$pages) $page = $pages;
        if ($page<1) $page = 1;

        $topics = Forum\Models\Topic::getCollection()
                            ->select(Forum\Models\Topic::getFields())
                            ->left_join(Zira\Models\User::getClass(), array('user_firstname'=>'firstname', 'user_secondname'=>'secondname', 'user_username'=>'username'))
                            ->where('category_id','=',$forum->category_id)
                            ->and_where('forum_id','=',$forum->id)
                            ->and_where('sticky','=',0)
                            ->and_where('published','=',Forum\Models\Topic::STATUS_PUBLISHED)
                            ->order_by('id','desc')
                            ->limit($limit, ($page-1)*$limit)
                            ->get();

        $title = Zira\Locale::t($forum->title);
        $meta_title = $forum->meta_title ? Zira\Locale::t($forum->meta_title) : $title;
        $description = $forum->description ? Zira\Locale::t($forum->description) : '';
        $meta_description = $forum->meta_description ? Zira\Locale::t($forum->meta_description) : $description;
        $keywords = $forum->meta_keywords ? Zira\Locale::t($forum->meta_keywords) : '';

        Zira\Page::addTitle($meta_title);
        Zira\Page::setKeywords($keywords);
        Zira\Page::setDescription($meta_description);

        Zira\Page::putBreadcrumb(Forum\Forum::ROUTE, Zira\Locale::tm('Forum', 'forum'));
        Zira\Page::removeBreadcrumb(Forum\Forum::ROUTE . '/'. Zira\Router::getAction());
        Zira\Page::addBreadcrumb(Forum\Models\Category::generateUrl($forum->category_id), Zira\Locale::t($forum->category_title));
        Zira\Page::addBreadcrumb(Forum\Models\Forum::generateUrl($forum->id), Zira\Locale::t($forum->title));

        if ($forum->category_layout) {
            Zira\Page::setLayout($forum->category_layout);
        } else if (Zira\Config::get('forum_layout')) {
            Zira\Page::setLayout(Zira\Config::get('forum_layout'));
        }
        Zira\Page::setView('forum/page');

        $pagination = new Zira\Pagination();
        $pagination->setLimit($limit);
        $pagination->setTotal($total);
        $pagination->setPages($pages);
        $pagination->setPage($page);

        $searchForm = new Forum\Forms\Search();
        $searchForm->setValue('forum_id', $forum->id);

        Zira\View::addPlaceholderView(Zira\View::VAR_CONTENT, array(
                                                                'top_items'=>$sticky,
                                                                'items'=>$topics,
                                                                'pagination' => $pagination,
                                                                'compose_url' => Forum\Forum::ROUTE.'/compose/'.$forum->id,
                                                                'category_title' => $forum->category_title,
                                                                'category_url' => Forum\Models\Category::generateUrl($forum->category_id),
                                                                'info' => $forum->info,
                                                                'searchForm' => $searchForm
                                                            ), 'forum/threads');

        Zira\Page::render(array(
            Zira\Page::VIEW_PLACEHOLDER_TITLE => $title,
            Zira\Page::VIEW_PLACEHOLDER_DESCRIPTION => $description,
            Zira\Page::VIEW_PLACEHOLDER_CONTENT => ''
        ));
    }

    public function thread($topic_id) {
        if (empty($topic_id)) {
            if (!$this->_has_category) Zira\Response::notFound();
            else $this->_renderCategoryPage();
            return;
        }

        $this->_renderPlaceholderCategory();

        $category_fields = Forum\Models\Category::getFields();
        $_category_fields = array();
        foreach($category_fields as $field) {
            $_category_fields['category_'.$field] = $field;
        }

        $forum_fields = Forum\Models\Forum::getFields();
        $_forum_fields = array();
        foreach($forum_fields as $field) {
            $_forum_fields['forum_'.$field] = $field;
        }

        $topic = Forum\Models\Topic::getCollection()
                                ->select(Forum\Models\Topic::getFields())
                                ->join(Forum\Models\Category::getClass(), $_category_fields)
                                ->join(Forum\Models\Forum::getClass(), $_forum_fields)
                                ->where('id','=',$topic_id)
                                ->get(0);

        if (!$topic || !$topic->forum_active || $topic->published != Forum\Models\Topic::STATUS_PUBLISHED) Zira\Response::notFound();

        // checking permission
        if (($topic->forum_access_check || $topic->category_access_check) && !Zira\Permission::check(Zira\Permission::TO_VIEW_RECORDS)) {
            if (!Zira\User::isAuthorized()) {
                Zira\Response::redirect('user/login?redirect='.Forum\Models\Topic::generateUrl($topic), true);
            } else {
                Zira\Response::forbidden();
            }
        }

        $total = null;
        $limit = Zira\Config::get('forum_limit') ? intval(Zira\Config::get('forum_limit')) : 10;
        $page = (int)Zira\Request::get('page');

        $form = new Forum\Forms\Reply();
        if ($topic->active && Zira\Request::isPost() && $form->isValid()) {
            $content = $form->getValue('message');
            // storing files
            if (Zira\Config::get('forum_file_uploads')) {
                $file_refs = array();
                $files = Forum\Models\File::storeFiles($form->getValue('attaches'), $file_refs);
                if (!empty($files)) {
                    Forum\Models\File::parseContentFiles($file_refs, $content);
                }
            }
            // creating new message
            if (!($message=Forum\Models\Message::createNewMessage($topic->forum_id, $topic->id, $content, ++$topic->messages, $topic->forum_topics))) {
                $form->setError(Zira\Locale::t('An error occurred'));
            } else {
                // saving files
                if (Zira\Config::get('forum_file_uploads') && !empty($files)) {
                    Forum\Models\File::saveFiles($files, $message->id);
                }
                // sending notification message
                try {
                    Forum\Models\Message::notify($topic, $message);
                } catch (\Exception $e) {
                    Zira\Log::exception($e);
                }

                if ($message->published != Forum\Models\Message::STATUS_PUBLISHED) {
                    $form->setMessage(Zira\Locale::t('Thank you. Your message is awaiting moderation'));
                    if (Zira\View::isAjax()) {
                        Zira\Page::render(array('message' => $form->getMessage()));
                        return;
                    }
                } else {
                    // redirect to last page
                    $total = Forum\Models\Message::getCollection()
                        ->count()
                        ->where('topic_id', '=', $topic->id)
                        ->and_where('published', '=', \Forum\Models\Message::STATUS_PUBLISHED)
                        ->get('co');

                    $pages = ceil($total / $limit);
                    if ($page > $pages) $page = $pages;
                    if ($page < 1) $page = 1;

                    if (!Zira\View::isAjax() && $page < $pages) {
                        Zira\Response::redirect(Forum\Models\Topic::generateUrl($topic) . '?page=' . $pages);
                        return;
                    } else if (Zira\View::isAjax()) {
                        $hash = '#forum-message-' . $message->id;
                        if ($pages > 1) {
                            $url = Forum\Models\Topic::generateUrl($topic) . '?page=' . $pages . '&t=' . time() . $hash;
                        } else {
                            $url = Forum\Models\Topic::generateUrl($topic) . '?t=' . time() . $hash;
                        }
                        Zira\Page::render(array('redirect' => Zira\Helper::url($url)));
                        return;
                    }
                }
            }
        }

        if (Zira\Request::isPost() && Zira\View::isAjax() && $form->getError()) {
            Zira\Page::render(array('error'=>$form->getError()));
            return;
        }

        if ($total===null) {
            $total = Forum\Models\Message::getCollection()
                ->count()
                ->where('topic_id', '=', $topic->id)
                ->and_where('published', '=', \Forum\Models\Message::STATUS_PUBLISHED)
                ->get('co');

            $pages = ceil($total / $limit);
            if ($page>$pages) $page = $pages;
            if ($page<1) $page = 1;
        }

        $file_fields = Forum\Models\File::getFields();
        $_file_fields = array();
        foreach($file_fields as $field) {
            $_file_fields['file_'.$field] = $field;
        }

        $rows = Forum\Models\Message::getCollection()
                            ->select(Forum\Models\Message::getFields())
                            ->left_join(Zira\Models\User::getClass(), array('user_group_id'=>'group_id', 'user_firstname'=>'firstname', 'user_secondname'=>'secondname', 'user_username'=>'username', 'user_image'=>'image', 'user_posts'=>'posts'))
                            ->left_join(Forum\Models\File::getClass(), $_file_fields)
                            ->where('topic_id','=',$topic->id)
                            ->and_where('published', '=', \Forum\Models\Message::STATUS_PUBLISHED)
                            ->order_by('id','asc')
                            ->limit($limit, ($page-1)*$limit)
                            ->get();

        $status = $topic->status ? Forum\Models\Topic::getStatus($topic->status) : '';
        $_status = !empty($status) ? '['.$status.'] ' : '';

        $title = Zira\Locale::t($topic->title);
        $meta_title = $topic->meta_title ? Zira\Locale::t($topic->meta_title) : $title;
        $description = $topic->description ? Zira\Locale::t($topic->description) : '';
        $meta_description = $topic->meta_description ? Zira\Locale::t($topic->meta_description) : $description;
        $keywords = $topic->meta_keywords ? Zira\Locale::t($topic->meta_keywords) : '';

        Zira\Page::addTitle($_status.$meta_title);
        Zira\Page::setKeywords($keywords);
        Zira\Page::setDescription($meta_description);

        Zira\Page::putBreadcrumb(Forum\Forum::ROUTE, Zira\Locale::tm('Forum', 'forum'));
        Zira\Page::removeBreadcrumb(Forum\Forum::ROUTE . '/'. Zira\Router::getAction());
        Zira\Page::addBreadcrumb(Forum\Models\Category::generateUrl($topic->category_id), Zira\Locale::t($topic->category_title));
        Zira\Page::addBreadcrumb(Forum\Models\Forum::generateUrl($topic->forum_id), Zira\Locale::t($topic->forum_title));

        if ($topic->category_layout) {
            Zira\Page::setLayout($topic->category_layout);
        } else if (Zira\Config::get('forum_layout')) {
            Zira\Page::setLayout(Zira\Config::get('forum_layout'));
        }
        Zira\Page::setView('forum/page');

        Zira\View::addLightbox();
        Zira\View::addParser();

        $pagination = new Zira\Pagination();
        $pagination->setLimit($limit);
        $pagination->setTotal($total);
        $pagination->setPages($pages);
        $pagination->setPage($page);

        $searchForm = new Forum\Forms\Search();
        $searchForm->setValue('forum_id', $topic->forum_id);

        Zira\View::addPlaceholderView(Zira\View::VAR_CONTENT, array(
                                                                'items'=>$rows,
                                                                'pagination' => $pagination,
                                                                'forum_title' => $topic->forum_title,
                                                                'forum_url' => Forum\Models\Forum::generateUrl($topic->forum_id),
                                                                'info' => $topic->info,
                                                                'user_groups' => Zira\Models\Group::getArray(true),
                                                                'form' => Zira\User::isAuthorized() ? $form : null,
                                                                'topic_active' => $topic->active,
                                                                'topic_url' => Forum\Models\Topic::generateUrl($topic),
                                                                'topic_page' => $page,
                                                                'searchForm' => $searchForm
                                                            ), 'forum/thread');

        Zira\Page::render(array(
            Forum\Forum::VIEW_PLACEHOLDER_LABEL => $status,
            Zira\Page::VIEW_PLACEHOLDER_TITLE => $title,
            Zira\Page::VIEW_PLACEHOLDER_DESCRIPTION => $description,
            Zira\Page::VIEW_PLACEHOLDER_CONTENT => ''
        ));
    }

    public function compose($forum_id) {
        if (empty($forum_id)) Zira\Response::notFound();

        if (!Zira\User::isAuthorized()) {
            Zira\Response::redirect('user/login?redirect='.Forum\Forum::ROUTE.'/compose/'.intval($forum_id), true);
        }

        $category_fields = Forum\Models\Category::getFields();
        $_category_fields = array();
        foreach($category_fields as $field) {
            $_category_fields['category_'.$field] = $field;
        }

        $forum = Forum\Models\Forum::getCollection()
                                ->select(Forum\Models\Forum::getFields())
                                ->join(Forum\Models\Category::getClass(), $_category_fields)
                                ->where('id','=',$forum_id)
                                ->get(0);

        if (!$forum || !$forum->active) Zira\Response::notFound();

        // checking permission
        if (($forum->access_check || $forum->category_access_check) && !Zira\Permission::check(Zira\Permission::TO_VIEW_RECORDS)) {
            if (!Zira\User::isAuthorized()) {
                Zira\Response::redirect('user/login?redirect='.Forum\Forum::ROUTE.'/compose/'.$forum->id, true);
            } else {
                Zira\Response::forbidden();
            }
        }

        $form = new Forum\Forms\Compose();
        if (Zira\Request::isPost() && $form->isValid()) {
            $message = null;
            $content = $form->getValue('message');
            // storing files
            if (Zira\Config::get('forum_file_uploads')) {
                $file_refs = array();
                $files = Forum\Models\File::storeFiles($form->getValue('attaches'), $file_refs);
                if (!empty($files)) {
                    Forum\Models\File::parseContentFiles($file_refs, $content);
                }
            }
            if (!($topic=Forum\Models\Topic::createNewTopic($forum->category_id, $forum->id, $form->getValue('title'), $content, ++$forum->topics, $message)) || !$message) {
                $form->setError(Zira\Locale::t('An error occurred'));
            } else {
                // saving files
                if (Zira\Config::get('forum_file_uploads') && !empty($files)) {
                    Forum\Models\File::saveFiles($files, $message->id);
                }
                // sending notification message
                try {
                    Forum\Models\Message::notify($topic, $message, $forum);
                } catch (\Exception $e) {
                    Zira\Log::exception($e);
                }
                if ($topic->published != Forum\Models\Topic::STATUS_PUBLISHED) {
                    $form->setMessage(Zira\Locale::t('Thank you. Your message is awaiting moderation'));
                    if (Zira\View::isAjax()) {
                        Zira\Page::render(array('message' => $form->getMessage()));
                        return;
                    }
                } else {
                    if (!Zira\View::isAjax()) {
                        Zira\Response::redirect(Forum\Models\Topic::generateUrl($topic));
                    } else {
                        Zira\Page::render(array('redirect' => Zira\Helper::url(Forum\Models\Topic::generateUrl($topic))));
                    }
                    return;
                }
            }
        }

        $title = Zira\Locale::tm('New thread', 'forum');
        $meta_title = $title .' - '.Zira\Locale::t($forum->title);
        $description = $forum->description ? Zira\Locale::t($forum->description) : '';

        Zira\Page::addTitle($meta_title);

        Zira\Page::putBreadcrumb(Forum\Forum::ROUTE, Zira\Locale::tm('Forum', 'forum'));
        Zira\Page::addBreadcrumb(Forum\Models\Category::generateUrl($forum->category_id), Zira\Locale::t($forum->category_title));

        if ($forum->category_layout) {
            Zira\Page::setLayout($forum->category_layout);
        } else if (Zira\Config::get('forum_layout')) {
            Zira\Page::setLayout(Zira\Config::get('forum_layout'));
        }

        Zira\Page::setView('forum/page');

        Zira\Page::render(array(
            Zira\Page::VIEW_PLACEHOLDER_TITLE => $title .' - '.Zira\Locale::t($forum->title),
            Zira\Page::VIEW_PLACEHOLDER_DESCRIPTION => $description,
            Zira\Page::VIEW_PLACEHOLDER_CONTENT => $form
        ));
    }

    public function poll() {
        Zira\View::setAjax(true);

        if (!Zira\Request::isPost()) return;
        $value = Zira\Request::post('value');
        $id = Zira\Request::post('id');
        $type = Zira\Request::post('type');
        $token = Zira\Request::post('token');

        if (!isset($value) || empty($id) || empty($type) || empty($token)) return;
        if (!Zira\User::checkToken($token)) return;

        $user_id = Zira\User::isAuthorized() ? Zira\User::getCurrent()->id : 0;
        $anonymous_id = Zira\User::getAnonymousUserId();
        if (empty($user_id) && empty($anonymous_id)) return;

        $message = new Forum\Models\Message($id);
        if (!$message->loaded()) return;

        $query = Forum\Models\Forumlike::getCollection()
                        ->where('message_id','=',$message->id);

        if (!empty($user_id)) {
            $query->and_where();
            $query->open_where();
            $query->where('user_id','=',$user_id);
            $query->or_where('anonymous_id','=',$anonymous_id);
            $query->close_where();
        } else if (!empty($anonymous_id)) {
            $query->and_where('anonymous_id','=',$anonymous_id);
        }

        $exists = $query->get(0, true);

        if (!$exists || $exists['rate'] != $value) {
            $like = new Forum\Models\Forumlike();
            if (!$exists) {
                $like->message_id = $message->id;
                $like->user_id = $user_id;
                $like->anonymous_id = $anonymous_id;
                $like->creation_date = date('Y-m-d H:i:s');
            } else {
                $like->loadFromArray($exists);
                if ($exists['rate']>0) $message->rating--;
                else $message->rating++;
            }
            $like->rate = $value;
            $like->save();

            if ($value>0) $message->rating++;
            else $message->rating--;
            $message->save();
        }

        Zira\Page::render(array('rating'=>$message->rating));
    }

    public function user($id) {
        $id = intval($id);
        if ($id>0) {
            if (!Zira\Config::get(Zira\User::CONFIG_ALLOW_VIEW_PROFILE, true) &&
                !Zira\User::isAuthorized()
            ) {
                Zira\Response::redirect('user/login' . '?redirect=forum/user/' . $id);
                return;
            }
            $user = Zira\Models\User::findUser($id);
            if (!$user || !$user->active) {
                Zira\Response::notFound();
                return;
            }

            $title = Zira\Locale::tm('Posted by: %s', 'forum', Zira\User::getProfileName($user));
        } else {
            if (!Zira\User::isAuthorized()) {
                Zira\Response::redirect('user/login');
                return;
            }
            $user = Zira\User::getCurrent();

            $title = Zira\Locale::tm('My discussions', 'forum');
        }

        $topic_fields = Forum\Models\Topic::getFields();
        $_topic_fields = array();
        foreach($topic_fields as $field) {
            if ($field == Forum\Models\Topic::getPk()) continue;
            $_topic_fields['topic_'.$field] = $field;
        }

        $total = null;
        $limit = Zira\Config::get('forum_limit') ? intval(Zira\Config::get('forum_limit')) : 10;
        $page = (int)Zira\Request::get('page');

        $total = Forum\Models\Message::getCollection()
                            ->countDistinctField('topic_id')
                            ->join(Forum\Models\Topic::getClass())
                            ->where('creator_id','=',$user->id)
                            ->and_where('published', '=', \Forum\Models\Message::STATUS_PUBLISHED)
                            ->get('co');

        $pages = ceil($total / $limit);
        if ($page>$pages) $page = $pages;
        if ($page<1) $page = 1;

        $file_fields = Forum\Models\File::getFields();
        $_file_fields = array();
        foreach($file_fields as $field) {
            $_file_fields['file_'.$field] = $field;
        }

        $rows = Forum\Models\Message::getCollection()
                            ->select('id')
                            ->join(Forum\Models\Topic::getClass())
                            ->where('creator_id','=',$user->id)
                            ->and_where('published', '=', \Forum\Models\Message::STATUS_PUBLISHED)
                            ->group_by('topic_id')
                            ->limit($limit, ($page-1)*$limit)
                            ->get();

        $ids = array();
        foreach($rows as $row) {
            $ids []= $row->id;
        }

        if (!empty($ids)) {
            $rows = Forum\Models\Message::getCollection()
                ->select(Forum\Models\Message::getFields())
                ->left_join(Forum\Models\Topic::getClass(), $_topic_fields)
                ->left_join(Zira\Models\User::getClass(), array('user_group_id' => 'group_id', 'user_firstname' => 'firstname', 'user_secondname' => 'secondname', 'user_username' => 'username', 'user_image' => 'image', 'user_posts' => 'posts'))
                ->left_join(Forum\Models\File::getClass(), $_file_fields)
                ->where('id', 'in', $ids)
                ->get();
        } else {
            $rows = array();
        }

        Zira\Page::addTitle($title);

        Zira\Page::putBreadcrumb(Forum\Forum::ROUTE, Zira\Locale::tm('Forum', 'forum'));

        if (Zira\Config::get('forum_layout')) {
            Zira\Page::setLayout(Zira\Config::get('forum_layout'));
        }
        Zira\Page::setView('forum/page');

        Zira\View::addLightbox();
        Zira\View::addParser();

        $pagination = new Zira\Pagination();
        $pagination->setLimit($limit);
        $pagination->setTotal($total);
        $pagination->setPages($pages);
        $pagination->setPage($page);

        Zira\View::addPlaceholderView(Zira\View::VAR_CONTENT, array(
                                                                'items'=>$rows,
                                                                'user_groups' => Zira\Models\Group::getArray(true),
                                                                'pagination' => $pagination
                                                            ), 'forum/user');

        Zira\Page::render(array(
            Zira\Page::VIEW_PLACEHOLDER_TITLE => $title,
            Zira\Page::VIEW_PLACEHOLDER_DESCRIPTION => '',
            Zira\Page::VIEW_PLACEHOLDER_CONTENT => ''
        ));
    }

    public function search() {
        $offset = (int)Zira\Request::get('offset');
        $is_ajax = (int)Zira\Request::get('ajax');
        $forum_id = (int)Zira\Request::get('forum_id');

        $limit = 10;
        $form = new Forum\Forms\Search();
        $form->setExtended(true);

        $forum = null;
        $category_id = 0;
        if ($forum_id>0) {
            $forum = new Forum\Models\Forum($forum_id);
            if ($forum->loaded()) {
                $category_id = $forum->category_id;
            } else {
                $forum = null;
            }
        }

        $data = array();
        $found = false;
        if ($form->getValue('text') && $offset>=0 && $form->isValid()) {
            $topics = Forum\Models\Search::getTopics($form->getValue('text'), $limit + 1, $offset, $category_id, $forum_id);
            if (!empty($topics)) {
                $found = true;
                $_data = array(
                                'class' => 'search-list'.($is_ajax ? ' xhr-list' : ''),
                                'items'=>$topics,
                                'settings' => array(
                                        'limit' => $limit,
                                        'text' => $form->getValue('text'),
                                        'offset' => $offset,
                                        'forum_id' => $forum_id
                                    )
                            );

                if (!$is_ajax) {
                    Zira\View::addPlaceholderView(Zira\View::VAR_CONTENT, $_data, 'forum/search-results');
                    Zira\View::preloadThemeLoader();
                    $data[Zira\Page::VIEW_PLACEHOLDER_TITLE] = ($forum ? $forum->title.' - ' : '').Zira\Locale::tm('Search results','forum');
                    $data[Zira\Page::VIEW_PLACEHOLDER_CONTENT] = '';
                } else {
                    Zira\View::renderView($_data, 'forum/search-results');
                }
            } else {
                $form->setValue('text','');
                $form->setError(Zira\Locale::t('Your search did not match any documents'));
            }
        } else {
            $form->setValue('text','');
        }

        if (!$is_ajax) {
            Zira\Page::addTitle(Zira\Locale::t('Search'));

            Zira\Page::putBreadcrumb(Forum\Forum::ROUTE, Zira\Locale::tm('Forum', 'forum'));
            Zira\Page::addBreadcrumb(Forum\Forum::ROUTE.'/search', Zira\Locale::t('Search'));

            if (Zira\Config::get('forum_layout')) {
                Zira\Page::setLayout(Zira\Config::get('forum_layout'));
            }
            Zira\Page::setView('forum/page');

            Zira\View::addPlaceholderView(Zira\View::VAR_CONTENT_TOP, array('form' => $form, 'found' => $found), 'forum/search');
            Zira\Page::render($data);
        }
    }
}
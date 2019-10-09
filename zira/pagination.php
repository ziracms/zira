<?php
/**
 * Zira project
 * pagination.php
 * (c)2015 https://github.com/ziracms/zira
 */

namespace Zira;

class Pagination {
    const PAGINATION_CLASS = 'pagination';

    protected $_param = 'page';
    protected $_total;
    protected $_limit;
    protected $_page;
    protected $_offset;
    protected $_url;
    protected $_url_params = array();
    protected $_pages = 10;
    protected $_class;
    protected $_id;

    public function setParam($param) {
        if (!is_string($param)) {
            throw new \Exception('Only string should be passed as parameter');
        }
        $this->_param = $param;
    }

    public function setTotal($total) {
        $this->_total = intval($total);
    }

    public function setLimit($limit) {
        $this->_limit = intval($limit);
    }

    public function setPage($page) {
        $this->_page = intval($page);
        if ($this->_page<1) $this->_page = 1;
    }

    public function setOffset($offset) {
        $this->_offset = intval($offset);
        if ($this->_offset<0) $this->_offset = 0;
    }

    public function setPages($pages) {
        $this->_pages = intval($pages);
    }

    public function setUrl($url) {
        $this->_url = $url;
    }

    public function setUrlParams(array $url_params) {
        $this->_url_params = $url_params;
    }

    public function setClass($class) {
        $this->_class = $class;
    }

    public function setId($id) {
        $this->_id = $id;
    }

    public function getParam() {
        return $this->_param;
    }

    public function getTotal() {
        return $this->_total;
    }

    public function getLimit() {
        return $this->_limit;
    }

    public function getPage() {
        if ($this->_page === null) {
            $this->setPage(Request::get($this->getParam()));
        }
        return $this->_page;
    }

    public function getOffset() {
        if ($this->_offset === null) {
            $limit = $this->getLimit();
            if ($limit === null) {
                throw new \Exception('Limit is not set');
            }
            $page = $this->getPage();
            $this->_offset = ($page-1)*$limit;
        }
        return $this->_offset;
    }

    public function getPages() {
        return $this->_pages;
    }

    public function getUrl() {
        return $this->_url;
    }

    public function getUrlParams() {
        return $this->_url_params;
    }

    public function getClass() {
        if ($this->_class === null) return '';
        return $this->_class;
    }

    public function getId() {
        if ($this->_id === null) return '';
        return $this->_id;
    }

    protected function url($page) {
        $url = $this->getUrl();
        $params = $this->getUrlParams();
        $url_params = '';
        if (!empty($params)) {
            foreach($params as $k=>$v) {
                if (empty($url_params)) $url_params .= '?';
                else $url_params .= '&';
                $url_params .= $k.'='.$v;
            }
        }
        if ($url === null) {
            if (empty($url_params)) {
                $param = $this->getParam();
                foreach(Request::get() as $k=>$v) {
                    if ($k==$param) continue;
                    if (empty($url_params)) $url_params .= '?';
                    else $url_params .= '&';
                    $url_params .= $k.'='.$v;
                }
            }
            $sign = '?';
            if (!empty($url_params)) $sign = '&';
            $url = Helper::url(Router::getRequest()).$url_params.$sign.$this->getParam().'='.$page;
        } else {
            $sign = '?';
            if (strpos($url, '?')!==false || !empty($url_params)) $sign = '&';
            $url = Helper::url($url).$url_params.$sign.$this->getParam().'='.$page;
        }
        return $url;
    }

    public function __toString() {
        $limit = $this->getLimit();
        $page = $this->getPage();
        $total = $this->getTotal();
        $pages = $this->getPages();

        if ($total === null || $total<=0 || $limit === null || $limit<=0 || $total<=$limit) return '';

        $total = ceil($total / $limit);
        $pages_half = floor($pages/2);
        $start = 1;
        $end = $total>$pages ? $pages : $total;
        if ($total>$pages && $page>$pages_half && $total-$page>$pages_half) {
            $start = $page - $pages_half + 1;
            $end = $start + $pages - 1;
        } else if ($total>$pages && $page>$pages_half && $total-$page<=$pages_half) {
            $end = $total;
            $start = $end - $pages + 1;
        }

        $class = $this->getClass();
        $id = $this->getId();
        if (!empty($class)) $class = ' '.$class;

        $html = Helper::tag_open('nav');
        if (!empty($id)) {
            $html .= Helper::tag_open('ul',array('class'=>self::PAGINATION_CLASS.$class,'id'=>$id));
        } else {
            $html .= Helper::tag_open('ul',array('class'=>self::PAGINATION_CLASS.$class));
        }
        if ($page>1 && $page<=$total) {
            $html .= Helper::tag_open('li');
            //$html .= Helper::tag_open('a', array('href'=>$this->url($page-1)));
            $html .= Helper::tag_open('a', array('href'=>$this->url(1), 'title'=>Locale::t('First page')));
            $html .= Helper::tag_open('span');
            $html .= '&laquo;';
            $html .= Helper::tag_close('span');
            $html .= Helper::tag_close('a');
            $html .= Helper::tag_close('li');
        } else {
            $html .= Helper::tag_open('li',array('class'=>'disabled'));
            $html .= Helper::tag_open('span');
            $html .= '&laquo;';
            $html .= Helper::tag_close('span');
            $html .= Helper::tag_close('li');
        }
        for ($i=$start; $i<=$end; $i++) {
            if ($i==$page) {
                $html .= Helper::tag_open('li',array('class'=>'active'));
                $html .= Helper::tag('span', $i);
            } else {
                $html .= Helper::tag_open('li');
                $html .= Helper::tag('a', $i, array('href'=>$this->url($i)));
            }
            $html .= Helper::tag_close('li');
        }
        if ($page<$total) {
            $html .= Helper::tag_open('li');
            //$html .= Helper::tag_open('a', array('href'=>$this->url($page+1)));
            $html .= Helper::tag_open('a', array('href'=>$this->url($total), 'title'=>Locale::t('Last page')));
            $html .= Helper::tag_open('span');
            $html .= '&raquo;';
            $html .= Helper::tag_close('span');
            $html .= Helper::tag_close('a');
            $html .= Helper::tag_close('li');
        } else {
            $html .= Helper::tag_open('li',array('class'=>'disabled'));
            $html .= Helper::tag_open('span');
            $html .= '&raquo;';
            $html .= Helper::tag_close('span');
            $html .= Helper::tag_close('li');
        }
        $html .= Helper::tag_close('ul');
        $html .= Helper::tag_close('nav');
        return $html;
    }
}
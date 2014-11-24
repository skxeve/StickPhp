<?php
namespace Stick\controller;

use Stick\view\TextView;
use Stick\view\HeaderView;

abstract class AbstractHtmlController extends AbstractController
{
    protected $site_title;
    protected $sub_title;

    protected $local_css_files;
    protected $local_js_files;

    protected function mainExecute()
    {
        parent::mainExecute();

        // get title
        list($site_title, $sub_title) = $this->getTitle();

        // title view
        $title_view = new TextView;
        $title_text = $site_title;
        if (!empty($sub_title)) {
            $title_text .= ' - ' . $sub_title;
        }
        $title_view->setText($site_title);
        $this->getView()->setView('title', $title_view);

        // header view
        $header_view = new HeaderView;
        $header_view->init();
        $header_view->setCss($this->getCss());
        $header_view->setJs($this->getJs());
        $this->getView()->setView('header', $header_view);
    }

    protected function setTitle($site_title, $sub_title = null)
    {
        $this->site_title = $site_title;
        $this->sub_title = $sub_title;
    }

    protected function getTitle()
    {
        if (!empty($this->site_title)) {
            $site_title = $this->site_title;
        } elseif (defined('static::DEFAULT_SITE_TITLE')) {
            $sub_title = static::DEFAULT_SITE_TITLE;
        } else {
            $site_title = '';
        }
        if (!empty($this->sub_title)) {
            $sub_title = $this->sub_title;
        } elseif (defined('static::DEFAULT_SUB_TITLE')) {
            $sub_title = static::DEFAULT_SUB_TITLE;
        } else {
            $sub_title = '';
        }
        return array($site_title, $sub_title);
    }

    protected function setCss($path)
    {
        return $this->setFiles('css', $path);
    }
    protected function setJs($path)
    {
        return $this->setFiles('js', $path);
    }

    protected function getCss()
    {
        return $this->getFiles('css');
    }
    protected function getJs()
    {
        return $this->getFiles('js');
    }

    protected function getGlobalValueName($type)
    {
        return 'global_' . $type . '_files';
    }

    protected function getLocalValueName($type)
    {
        return 'local_' . $type . '_files';
    }

    protected function getFiles($type)
    {
        $global_value_name = $this->getGlobalValueName($type);
        $local_value_name = $this->getLocalValueName($type);
        if (isset($this->$global_value_name) && is_array($this->$global_value_name)) {
            $global_files = $this->$global_value_name;
        } else {
            $global_files = array();
        }
        if (isset($this->$local_value_name) && is_array($this->$local_value_name)) {
            $local_files = $this->$local_value_name;
        } else {
            $local_files = array();
        }
        $this->getLogger()->debug($type . ' -> ' . var_export($global_files, true));
        return array_merge($global_files, $local_files);
    }

    protected function setFiles($type, $path)
    {
        if (!is_array($path)) {
            $path = array($path);
        }
        $local_value_name = $this->getLocalValueName($type);
        if (isset($this->$local_value_name)) {
            if (is_array($this->$local_value_name)) {
                array_merge($this->$local_value_name, $path);
            } else {
                $this->$local_value_name = $path;
            }
            return true;
        } else {
            return false;
        }
    }
}

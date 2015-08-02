<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Twig database template loader
 *
 * @package     Application
 * @category    Twig
 * @author      Todd Wirth
 * @copyright   (c) 2013
 */
class Twig_DatabaseLoader implements Twig_LoaderInterface, Twig_ExistsLoaderInterface {

    public function exists($path)
    {
        $template = $this->getTemplate($path);

        return $template->loaded();
    }

    public function getCacheKey($path)
    {
        return $path;
    }

    public function getSource($path)
    {
        $template = $this->getTemplate($path);

        if ( ! $template->loaded())
        {
            throw new Kohana_Exception('Template ":path" does not exist.', array(
                    ':path' => $path
                ) );
        }

        return $template->get($this->_column($path));
    }

    public function getTemplate($path)
    {
        $entity = $this->_entity($path);

        $slug = $this->_slug($path);

        if (empty($entity) OR empty($slug))
        {
            throw new Kohana_Exception('Template ":path" does not exist.', array(
                    ':path' => $path
                ) );
        }

        $template = ORM::factory($entity);

        return $template->where('slug', '=', $slug)->find();
    }

    public function isFresh($path, $time)
    {
        $template = $this->getTemplate($path);

        if ( ! $template->loaded())
        {
            return FALSE;
        }

        return strtotime($template->updated_at) <= $time;
    }

    protected function _column($path)
    {
        $path = explode('/', $path, 3);

        return Arr::get($path, 2);
    }

    protected function _entity($path)
    {
        $path = explode('/', $path, 3);

        return Arr::get($path, 0);
    }

    protected function _slug($path)
    {
        $path = explode('/', $path, 3);

        return Arr::get($path, 1);
    }
}


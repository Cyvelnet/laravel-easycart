<?php

namespace Cyvelnet\EasyCart\Contracts;


/**
 * Interface Renderable
 *
 * @package Cyvelnet\EasyCart\Contracts
 */
interface Renderable
{
    /**
     * render cart into view
     *
     * @param null $view
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function render($view = null);
}

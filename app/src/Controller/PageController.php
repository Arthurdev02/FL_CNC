<?php

namespace App\Controller;

class PageController
{
    public function home()
    {
        require __DIR__ . '/../../views/page/home.phtml';
    }
}

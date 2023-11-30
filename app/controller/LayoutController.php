<?php

namespace controller;

class LayoutController
{
    public function header(): void {
        require_once 'app/template/header.php';
    }

    public function footer(): void {
        require_once 'app/template/footer.php';
    }
}
<?php
namespace app\qdapi\controller;

class Error
{
    public function index(Request $request)
    {
        abort(404,'error');
    }
}
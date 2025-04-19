<?php

namespace src\Controller\Routes;

interface Route
{
public function __construct();

public function action();

public function get();

public function post();

public function put();

public function delete();

public function patch();

public function options();
}
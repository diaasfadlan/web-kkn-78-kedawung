<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Firebase extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'firebase';
    }
}

class Firestore extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'firebase.firestore';
    }
}

class Storage extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'firebase.storage';
    }
}

class Auth extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'firebase.auth';
    }
}

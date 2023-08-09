<?php

namespace App\Actions;

use Illuminate\Support\Str;    //aliaseを使う書き方の時は、このuse文は不要です。

class StrRandom
{
    public function get($length)
    {
        // return \Str::random($length);   //aliaseを使う書き方
        return Str::random($length);       //aliaseを使わない書き方
    }
}
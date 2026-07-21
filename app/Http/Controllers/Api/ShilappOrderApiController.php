<?php

namespace App\Http\Controllers;


class ShilappOrderApiController extends Controller
{
    public function index()
    {
        dd('index');
    }

    public function show($orderId)
    {
        dd($orderId);
    }
}
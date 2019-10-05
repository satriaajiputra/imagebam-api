<?php

namespace ImageBamAPI\Interfaces;

interface RequestTokenInterface
{
    /**
     * Execute oAuthRequest method
     * 
     * @return array
     */
    public function runRequestToken(): array;
}
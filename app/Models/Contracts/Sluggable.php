<?php

namespace App\Models\Contracts;

interface Sluggable
{
    /**
     * Get the URL slug
     *
     * @return string
     */
    public function slug();
}

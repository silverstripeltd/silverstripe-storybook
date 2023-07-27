<?php

namespace Wilr\SilverStripe\Storybook;

interface Storybook
{
    /**
     * @return array
     */
    public function getStories(?array $vars);
}

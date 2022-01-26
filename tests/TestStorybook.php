<?php

namespace Wilr\SilverStripe\Storybook\Tests;

use SilverStripe\Tests\TestOnly;
use Wilr\SilverStripe\Storybook\Storybook;

class TestStoryBook implements Storybook, TestOnly
{
    public function getStories()
    {
        return [
            'button' => function() {
                return '<button name="test">Test</button>'
            }
        ];
    }
}
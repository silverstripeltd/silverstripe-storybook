<?php

namespace Wilr\SilverStripe\Storybook\Tests;

use SilverStripe\Tests\FunctionalTest;

class StorybookControllerTest extends FunctionalTest
{
    public function testIndex()
    {
        $this->assertEquals(400, $this->get('storybook')->getStatusCode());
        $this->assertEquals(404, $this->get('storybook/fake')->getStatusCode());

        $btn = $this->get('storybook/button');

        $this->assertEquals(200, $btn->getStatusCode());
        $this->assertContains('<button', $btn->getBody());
    }
}
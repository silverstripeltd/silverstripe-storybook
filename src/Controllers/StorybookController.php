<?php

namespace Wilr\SilverStripe\Storybook\Controllers;

use Wilr\SilverStripe\Storybook;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Control\Controller;

class StorybookController extends Controller
{
    private static $register = [];

    public function index()
    {
        $key = $this->request->param('id');

        if (!$key) {
            return $this->httpError(400);
        }

        $sources = ClassInfo::implementorsOf(Storybook::class);
        $stories = [];

        foreach ($sources as $source) {
            $stories = array_merge($stories, $source->getStories());
        }

        if (!isset($stories[$key])) {
            return $this->httpError(404);
        }

        $template = $stores[$key]();

        return $template;
    }
}

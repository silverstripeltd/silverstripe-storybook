<?php

namespace Wilr\SilverStripe\Storybook\Controllers;

use Wilr\SilverStripe\Storybook\Storybook;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Control\Controller;

class StorybookController extends Controller
{
    private static $url_handlers = [
        '$ID' => 'index',
    ];

    public function init()
    {
        parent::init();

        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            $response = $this->getResponse()
                ->addHeader('Access-Control-Allow-Origin', '*');

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
                $response = $response->addHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            }

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                $response = $response->addHeader(
                    'Access-Control-Allow-Headers',
                    $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']
                );
            }

            $response->output();
            exit;
        }
    }

    public function allowedActions($limitToClass = null)
    {
        return true;
    }


    public function index()
    {
        $response = $this->getResponse()
            ->addHeader('Access-Control-Allow-Origin', '*');

        $key = $this->request->param('ID');
        if (!$key) {
            return $response->setStatusCode(404);
        }

        $sources = ClassInfo::implementorsOf(Storybook::class);
        $stories = [];

        foreach ($sources as $source) {
            $stories = array_merge($stories, singleton($source)->getStories());
        }

        if (!isset($stories[$key])) {
            return $response->setStatusCode(404);
        }

        $template = $stories[$key]();
        $response->setBody($template);

        return $response;
    }
}

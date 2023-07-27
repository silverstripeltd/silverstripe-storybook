<?php

namespace Wilr\SilverStripe\Storybook\Controllers;

use Exception;
use Wilr\SilverStripe\Storybook\Storybook;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\FieldType\DBString;
use SilverStripe\View\ArrayData;
use SilverStripe\View\Requirements;

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

        $request = $this->getRequest();
        $vars = $request->getVars();

        foreach ($sources as $source) {
            $stories = array_merge($stories, singleton($source)->getStories($vars));
        }

        if (!isset($stories[$key])) {
            return $response->setStatusCode(404);
        }

        $template = $stories[$key]();

        if ($template instanceof HTTPResponse) {
            $templateCopy = $template->getBody();
        } elseif ($template instanceof DBString) {
            $templateCopy = (string) $template;
        } elseif (is_string($template)) {
            $templateCopy = $template;
        } else {
            throw new Exception(
                'Accessing storybook story '. $key .' is invalid. It must return a DBString or HTTPResponse class'
            );
        }

        if (strpos($templateCopy, '<body') === false) {
            // the template is a partial one, such as an include or otherwise
            // so we should 'wrap' it in a base page template
            $template = $this->customise(ArrayData::create([
                'Content' => DBField::create_field('HTMLText', $templateCopy)
            ]))->renderWith('Storybook');
        }

        $response->setBody($template);

        Requirements::include_in_response($response);

        return $response;
    }
}

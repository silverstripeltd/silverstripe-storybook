# Silverstripe Storybook

[![Version](http://img.shields.io/packagist/v/wilr/silverstripe-storybook.svg?style=flat-square)](https://packagist.org/packages/wilr/silverstripe-storybook)
[![License](http://img.shields.io/packagist/l/wilr/silverstripe-storybook.svg?style=flat-square)](LICENSE)

Helpers for rendering Silverstripe templates to a [Storybook
Server](https://github.com/storybookjs/storybook/tree/next/app/server). Can be
used in conjuction with
[silverstripe-populate](https://github.com/silverstripe/silverstripe-populate)
to build example components and for testing purposes.

## Setup

First step is to install this module within your existing Silverstripe project.

```
composer require wilr/silverstripe-storybook --dev
```

Storybook runs outside of your app. So you can develop UI components in
isolation without worrying about app specific dependencies and requirements.

```
npx sb init -t server
```

To configure the Silverstripe host that Storybook will connect to, export a
global parameter `parameters.server.url` in `.storybook/preview.js`.

```js
export const parameters = {
  server: {
    url: `http://localhost:${port}/storybook`,
  },
};
```

## Writing Stories

See the [Storybook
documentation](https://github.com/storybookjs/storybook/tree/next/app/server#server-rendering)
for more information.

In your `.storybook/main.js` provide a glob specifying the location of YAML /
JSON story files, e.g.

```js
module.exports = {
  stories: ['../app/stories/**/*.stories.json'],
};
```

### Example story

**app/stories/carousel.stories.json**
```js
{
  "title": "Carousel",
  "parameters": {
    "options": {
        "component": "carousel"
    }
  },
  "stories": [
    {
      "name": "Default",
      "parameters": {
        "server": {
            "id": "carousel_default"
        }
      }
    }
  ]
}
```

The only important line in this story is the `server.id` value. This will call
the following Silverstripe endpoint to receive the template
`storybook/carousel_default`. Next step is for your project code to tell this
module how to render `carousel_default`.

Rendering a story is managed entirely by your application code. Implement the
`Storybook` interface on any class and define the `getStories` method.
`getStories` should return a named array of ids and callable functions which
render the data for Storybook. The function can either be defined inline, or
link to another class or method.

```php
<?php

use Wilr\SilverStripe\Storybook\Storybook;

class AppStorybook implements Storybook
{
    public function getStories()
    {
        return [
            'carousel_default' => function() {
                // $data could fetch from your database, or use `silverstripe-populate`
                // or just manually create the data structure.
                $data = Page::create();
                $data->Title = 'Test';

                return $data->renderWith('Carousel');
            }
        ];
    }
}
```

Storybook supports `args` and `argTypes` for stories. These come through to the
backend as GET parameters.

# Arrange by key
## Twig extension
Giving a collection or multilevel array, get a new dataset having as keys the childrens' value associated to that key.

Compatible with Doctrine and Eloquent collections.

For example, if you have this array and you provide the 'age' key to the filter
````
[
   ['name' => 'Mark', 'surname' => 'Lenders', 'age' => '17'],
   ['name' => 'Oliver', 'surname' => 'Hutton', 'age' => '15'],
   ['name' => 'Benjamin', 'surname' => 'Price', 'age' => '17'],
   ['name' => 'Bruce', 'surname' => 'Harper', 'age' => '15'],
]
````

the filter returns:
````
[
   '15' => [
       ['name' => 'Oliver', 'surname' => 'Hutton', 'age' => '15'],
       ['name' => 'Bruce', 'surname' => 'Harper', 'age' => '15']
   ],
   '17' => [
       ['name' => 'Mark', 'surname' => 'Lenders', 'age' => '17'],
       ['name' => 'Benjamin', 'surname' => 'Price', 'age' => '17']
   ]
]
````

### When to use
Generally, you shouldn't do that from the View. When provided to the View, data should be ready to be consumed.
So, before considering the use of this extension, you should ask yourself:
- can I manipulate data from a service or controller before providing it to the view (functions or ViewModels)?
- can my need be better fulfilled from the frontend with JavaScript (for example, if you need to manipulate data multiple times on the fly)?

If not (that could happen when you "can't" touch the backend code) you may use this extension.

### Instructions
````
composer require lucagentile\arrange-by-key-twig
````
Symfony: https://symfony.com/doc/current/templating/twig_extension.html#register-an-extension-as-a-service
````
#services.yml
services:
    twig.extension.arrangebykey:
        class: Gautile\Twig\ArrangeByKeyExtension
        tags:
            - { name: twig.extension }
````

Laravel: https://github.com/rcrowe/TwigBridge#extensions

### License
MIT
# PRESTissimo

PRESTissimo is a minimal framework for building APIs utilizing PHP, MySQL and JSON in a RESTful manner.

## Quick Start

PRESTissimo is made for getting up and started as quickly as possible. Because of this, there are very few configuration options. Here's how to get started:
Test
1. Upload everything to your server that already has PHP and MySQL installed.
2. Open `utils/db_utils.php` and modify `username`, `password`, and `database_name` to match your MySQL username/password and database.
3. Open `router.php` and modify the `base_url` to the local URL where your API will reside (e.g., for http://example.com/api/, it would be "/api/").
4. Open `controllers/example_controller.php` to see an example controller.

Note that the name of the controller (both the file name and the class name inside the file) is what defines the URL of the resource. For example, the example controller is named `example_controller.php`, and has a matching class name of `ExampleController`, so the URL of the resource will be `http://your_domain.com/example`.

A `get` request will invoke the `get($params, $data)` method on the controller, a `post` request will invoke the `post($params, $data)` method, and so on for `put` and `delete`.

Take the following example request: `GET http://your_domain.com/something/1/2/3?a=1&b=2`

The framework will attempt to include `controllers/something_controller.php` and create an instance of `SomethingController`, invoking the `get()` method with the following arguments:

- `$params`: `array(1, 2, 3)`
- `$data`: `array("a" => 1, "b" => 2)`

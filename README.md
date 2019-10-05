# ImageBam-API
API Implementation for ImageBam [https://imagebam.com](https://imagebam.com)

Register API Client from [https//www.imagebam.com/sys/API/clients](http://www.imagebam.com/sys/API/clients)

## Features
- List Galleries
- List Images From a Gallery
- Upload Image

## Installation
Start using the package with executing this command
```bash
composer require satmaxt/imagebam
```

After executing that command, next to implement it

## Implementation
The complete example implementation of this package can be found at ``implementation`` folder

### Configuration
Create a config file, for example ``imagebam.php``

Fill the config like this
```php
<?php

$constant['API_KEY'] = 'yourkey';
$constant['API_SECRET'] = 'yoursecret';
$constant['IMAGEBAM_CACHE_PATH'] = __DIR__ . 'cache/imagebam.dat';

foreach($constant as $key => $val) {
    if( !defined($key) ) {
        define($key, $val);
    }
}
```

## Example Output
```php
// Get all galleries
stdClass Object
(
  [rsp] => stdClass Object
  (
    [status] => ok
    [galleries] => Array
    (
      [0] => stdClass Object
      (
        [GID] => p2jg3ul6lb4uclvxkc14tcxlgkoxjy69
        [URL] => http://www.imagebam.com/gallery/p2jg3ulxxxxx69
        [title] => 
        [description] => 
      )
      ...
    )
  )
)
```
```php
// Get all images from a gallery
stdClass Object
(
  [rsp] => stdClass Object
  (
    [status] => ok
    [images] => Array
    (
      [0] => stdClass Object
      (
        [ID] => a436fe1024587234
        [URL] => http://www.imagebam.com/image/a4safwxxxxx4
        [thumbnail] => https://thumbs2.imagebam.com/8d/18/21/a4safwxxxxx4
        [GID] => gxojib0f25ashg56cdf8xxxxxxxxx
        [filename] => my.jpg
      )
    )
  )
)
```
```php
// Upload Image
stdClass Object
(
  [rsp] => stdClass Object
  (
    [status] => ok
    [image] => stdClass Object
    (
      [ID] => 5b93511xsdssssss
      [URL] => http://www.imagebam.com/image/5b93511xsdssssss
      [thumbnail] => https://thumbs2.imagebam.com/4c/94/5b/5b93511xsdssssss.jpg
      [GID] => 
      [filename] => bg.jpg
    )
  )
)
```
Copyright &copy; 2019. [Satmaxt Developer](https://satmaxt.xyz). Coded with :heart: & :coffee: at Bandung

# PHP build a zip 

This library create a zip from a .yml configuration: 

```yml
folder:
  include:
    - app
  ignore:
    - assets

file:
  include:
    - index.php
  ignore:
    - "*.zip"
    - "*.scss"
```